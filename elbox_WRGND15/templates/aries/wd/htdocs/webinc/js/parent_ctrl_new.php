
<?
	$mac=query("/runtime/devdata/wanmac");
	$newMac="";
	$num=0;
	while ($num < 6)
	{
	    $tmpMac = cut($mac, $num, ":");
	    $newMac = $newMac.$tmpMac;
	    $num++;
	}
	$newMac = toupper($newMac);
?>
<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "PARENTCTRL,PARENTCTRLSCHL,DEVICE.TIME,DHCPS4.LAN-1,DHCPS4.LAN-2,RUNTIME.INF.LAN-1,RUNTIME.INF.LAN-2,RUNTIME.DEVICE-LIST",
	OnLoad: function() 
	{
		BODY.CleanTable("DHCP_info");
		this.devicecount = 0;
	},
	OnUnload: function() {},
	EnDelay: 0,
	OnSubmitCallback: function (code,result)
	{
		switch (code)
		{
		case "OK":
			if(PAGE.EnDelay!=0)
			{
	            var msgArray =
	            [
	            	"<div style='text-align:center;'><img src='pic/process_bar.gif' /></div>",
	                "<?echo I18N('h', 'The settings are being saved and are taking effect.');?>",
	                "<?echo I18N('h', 'Please wait');?> ..."
	            ];
				BODY.ShowCountdown("<?echo I18N('h', 'Saving');?>", msgArray, PAGE.rule_save_time, "http://<?echo $_SERVER['HTTP_HOST'];?>/parent_ctrl.php");
				return true;
			}
			else
			{
				BODY.ShowContent();
				BODY.OnReload();
			}
			break;
		case "BUSY":
			BODY.ShowContent();
			BODY.ShowAlert("<?echo I18N("j","Someone is configuring the device; please try again later.");?>");
			break;
		case "HEDWIG":
			BODY.ShowContent();
			BODY.ShowAlert(result.Get("/hedwig/message"));
			if (PAGE.CursorFocus) PAGE.CursorFocus(result.Get("/hedwig/node"));  
			break;
		case "PIGWIDGEON":
			BODY.ShowContent();
			if (result.Get("/pigwidgeon/message")=="no power")
			{
				BODY.NoPower();
			}
			else
			{
				BODY.ShowAlert(result.Get("/pigwidgeon/message"));
			}
			break;
		}
	},
	parent_ctrl: null,/* content filtering XML data */
	devtime_p: null,/* /device/time  XML data */
	enable: null,/* indicate parental control is enable or not */
	registed: null,/* router have been registared or not */
	located: null,
	xml_location: null,/* XML node router's location */
	email: null,
	pw: null,
	mac: null,
	tid: null,
	tid2: null,
	device_limit: null,/* XML node device limit */
	rule_limit: 25,/* Schedule block device rules limit */
	rule_save_time: 8,/* Schedule block device rule setting time */
	honame: null,
	IsSynchronized: null,
	devicecount: null,/* to remember dhcp list table device number */
	dhcps4: null,
	leasep: null,
	leasep2: null,
	valid_mail: null,	
	now_using: null, /* indicate now using mode 0 or 1 or 2 */
	now_list_select_value: null, /* indicate MultiList select item value not index */
	now_list_select_index: null, /* indicate MultiList select item index not value */
	scheduling: null,/* scheduling XML data */
	SelectNewDHCPIndex: -1,/* indicate new DHCP client's index in the selection bar */
	InitValue: function(xml)
	{
		PXML.doc = xml;
		PXML.IgnoreModule("DEVICE.TIME");
		PXML.IgnoreModule("DHCPS4.LAN-1");
		PXML.IgnoreModule("DHCPS4.LAN-2");
		PXML.IgnoreModule("RUNTIME.INF.LAN-1");
		PXML.IgnoreModule("RUNTIME.INF.LAN-2");
		PXML.IgnoreModule("PARENTCTRL");
		
		PAGE.IsSynchronized = "";
		PAGE.parent_ctrl = PXML.FindModule("PARENTCTRL");
		PAGE.scheduling = PXML.FindModule("PARENTCTRLSCHL");
		PAGE.devtime_p = PXML.FindModule("DEVICE.TIME");

		if (!PAGE.parent_ctrl || !PAGE.devtime_p)
		{
			BODY.ShowAlert("InitValue ERROR!"); 
			return false; 
		}
		PAGE.registed	= XG(PAGE.parent_ctrl+"/security/netstar/registed");
		PAGE.xml_location = XG(PAGE.parent_ctrl+"/security/netstar/location");
		PAGE.device_limit = S2I(XG(PAGE.parent_ctrl+"/security/netstar/device_limit"));

		PAGE.scheduling = PAGE.scheduling + "/security/parental";
		if(XG(PAGE.parent_ctrl+"/security/active")=="0")
		{
			PAGE.enable = "0";
			PAGE.now_using=0;
			OBJ("en_parent_ctrl").checked= false;
			OBJ("control_option").style.display="none";
			OBJ("last_save").style.display="none";
			PAGE.CloseOption2();
			PAGE.CloseUI();
			if(XG(PAGE.parent_ctrl+"/security/netstar/enable")=="1")
			{
				OBJ("PCoption1").checked= true;
			}
			else if(XG(PAGE.scheduling+"/enable")=="1")
			{
				OBJ("PCoption2").checked= true;
			}
		}
		else
		{
			if(XG(PAGE.parent_ctrl+"/security/netstar/enable")=="1")
			{
				PAGE.enable = "1";
				PAGE.now_using=1;
				PAGE.CloseOption2();
				OBJ("last_save").style.display="none";
				OBJ("control_option").style.display="block";
				OBJ("en_parent_ctrl").checked= true; 
				OBJ("PCoption1").checked= true;
				if(PAGE.xml_location!="")
				{
					OBJ("device_location").value=PAGE.xml_location;
					PAGE.OpenUI();
					PAGE.ShowTable();
				}
				else
				{
					PAGE.CloseUI();
					OBJ("device_loc").style.display="block";
					PAGE.SetLocZone("");
				}
			}
			else if(XG(PAGE.scheduling+"/enable")=="1")
			{
				PAGE.enable = "2";
				PAGE.now_using=2;
				OBJ("en_parent_ctrl").checked= true;
				OBJ("PCoption2").checked= true;
				OBJ("control_option").style.display="block";
	
				PAGE.ShowMultiList();
				PAGE.Option2();
				///Set to default cause by last save settings below///
				PAGE.ScheduleSettingClear();
				OBJ("device_list").selectedIndex=-1;
				PAGE.now_list_select_value = null;
				PAGE.now_list_select_index = null;
				///Set to default cause by last save settings beyond//
				PAGE.CloseUI();
			}
			else
			{
				PAGE.CloseUI();
				PAGE.CloseOption2();
				OBJ("en_parent_ctrl").checked = false;
				OBJ("control_option").style.display="none";
				OBJ("first_save").style.display="none";
				OBJ("last_save").style.display="none";
			}
		}
		return true;
	},
	ShowTable: function()
	{
		BODY.CleanTable("DHCP_info");
		var svc = PXML.FindModule("DHCPS4.LAN-1");
		var svc_gz = PXML.FindModule("DHCPS4.LAN-2");
		var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
		var inf2p = PXML.FindModule("RUNTIME.INF.LAN-2");
		if (!svc || !inf1p || !svc_gz || !inf2p)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.dhcps4 = GPBT(svc+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
		this.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);
		var router_ip = XG(this.leasep+"/dhcps4/pool/router");
		this.leasep2 = GPBT(inf2p+"/runtime", "inf", "uid", "LAN-2", false);
		if (!this.dhcps4)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.leasep += "/dhcps4/leases";
		this.leasep2 += "/dhcps4/leases";

		if (!this.leasep)	return true;	// in bridge mode, the value of this.leasep is null.

		var lease_i = 0;
		entry = this.leasep+"/entry";
		cnt = XG(entry+"#");
		if (XG(svc+"/inf/dhcps4")!="")		// when the dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt; i++)
			{
				var host	= XG(entry+":"+i+"/hostname");
				var mac		= XG(entry+":"+i+"/macaddr");
				var ipaddr	= XG(entry+":"+i+"/ipaddr");//For WD ITR 43414.same with lan_client.php
				mac = this.getMAC(mac);
				lease_i++;

				var data = [	'<div align="center"><input type="checkbox" id="'+lease_i+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+lease_i+'_check_0\');" ></div>',
						'<div align="center" id="name_'+lease_i+'">'+host+'</div>',
						'<div align="center" id="mac_'+lease_i+'">'+mac+'</div>'
						];
				var type = ["","",""];
												
				if(router_ip!=ipaddr && mac!="00:00:00:00:00:00")//For WD ITR 43414.same with lan_client.php
				{
					BODY.InjectTable("DHCP_info", lease_i, data, type);
	
					OBJ(lease_i+"_check_0").checked = false;
					OBJ(lease_i+"_check_0").disabled = false;
					OBJ("mac_"+lease_i).value = mac;
					OBJ("name_"+lease_i).value = host;
				}
			}
		}

	/*	var entry_gz = this.leasep2+"/entry";
		var cnt_gz = XG(entry_gz+"#");
		if (XG(svc_gz+"/inf/dhcps4")!="")		// when the guest zone dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt_gz; i++)
			{
				var uid		= "DUMMY_GZ_"+i;
				var host	= XG(entry_gz+":"+i+"/hostname");
				var mac		= XG(entry_gz+":"+i+"/macaddr");
				mac = this.getMAC(mac);
				lease_i++;
				
				var data = [	'<div align="center"><input type="checkbox" id="'+lease_i+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+lease_i+'_check_0\');" ></div>',
						'<div align="center" id="name_'+lease_i+'">'+host+'</div>',
						'<div align="center" id="mac_'+lease_i+'">'+mac+'</div>'
						];
				var type = ["","",""];
				
				BODY.InjectTable("DHCP_info", lease_i, data, type);
				
				OBJ(lease_i+"_check_0").checked = false;
				OBJ(lease_i+"_check_0").disabled = false;
				OBJ("mac_"+lease_i).value = mac;
				OBJ("name_"+lease_i).value = host;
			}
		}*/
		this.devicecount = lease_i;	
		this.AddRegisteredData();

		NewCheckRadio.init(); //Generate the new styles of checkbox and radio for WD.
		
	},
	CleanMultiList: function(objID)
	{
		/*
			Only delete DHCP clients information.
			Reserved <Guest devices>
		*/
		var count = OBJ(objID).options.length;
		//alert(count);
		while (count > 0)
		{
			//alert(OBJ(objID).options[count-1].value);
			if(OBJ(objID).options[count-1].value!="0")
			{
				OBJ(objID).remove(count-1);
			}
			count=count-1;
		}
	},
	ReadingDays: function()
	{
		var daylist="";
		if(OBJ("Mon").checked==true)	daylist=daylist+",1";
		if(OBJ("Tue").checked==true)	daylist=daylist+",2";
		if(OBJ("Wen").checked==true)	daylist=daylist+",3";
		if(OBJ("Thu").checked==true)	daylist=daylist+",4";
		if(OBJ("Fri").checked==true)	daylist=daylist+",5";
		if(OBJ("Sat").checked==true)	daylist=daylist+",6";
		if(OBJ("Sun").checked==true)	daylist=daylist+",7";
		if(daylist.substr(0,1)==',')	daylist = daylist.substr(1,daylist.length-1);
		return daylist;
	},
	DaysToOption: function(daylist)
	{//Use daylist string to set WebGUI option
		var f=-1;
		f = daylist.search("1");
		if(f!=-1)	OBJ("Mon").checked = true;
		else	OBJ("Mon").checked = false;
		f=-1;
		f = daylist.search("2");
		if(f!=-1)	OBJ("Tue").checked = true;
		else	OBJ("Tue").checked = false;
		f=-1;
		f = daylist.search("3");
		if(f!=-1)	OBJ("Wen").checked = true;
		else	OBJ("Wen").checked = false;
		f=-1;
		f = daylist.search("4");
		if(f!=-1)	OBJ("Thu").checked = true;
		else	OBJ("Thu").checked = false;
		f=-1;
		f = daylist.search("5");
		if(f!=-1)	OBJ("Fri").checked = true;
		else	OBJ("Fri").checked = false;
		f=-1;
		f = daylist.search("6");
		if(f!=-1)	OBJ("Sat").checked = true;
		else	OBJ("Sat").checked = false;
		f=-1;
		f = daylist.search("7");
		if(f!=-1)	OBJ("Sun").checked = true;
		else	OBJ("Sun").checked = false;
	},
	RestoreXMLdata: function()
	{
		if(PAGE.now_list_select_index < PAGE.SelectNewDHCPIndex)
		{//For XML device data
			if(PAGE.now_list_select_index==0)
			{//GUEST devices settings
				var XMLtype = XG(PAGE.scheduling+"/guest/type");
				switch(XMLtype)
				{
					case "1":
					if(OBJ("blocking2").checked==true)
					{
						PAGE.DaysToOption(XG(PAGE.scheduling+"/guest/days"));
						OBJ("limit2_time_start").value = XG(PAGE.scheduling+"/guest/start");
						OBJ("limit2_time_end").value = XG(PAGE.scheduling+"/guest/end");
					}
					break;
				}
			}
			else
			{//Schedule and daily devices settings
				var XMLtype = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type");
				switch(XMLtype)
				{
					case "1":
					if(OBJ("blocking2").checked==true)
					{
						PAGE.DaysToOption(XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days"));
						OBJ("limit2_time_start").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start");
						OBJ("limit2_time_end").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end");
					}
					break;
					case "2":
					if(OBJ("blocking3").checked==true)
					{
						OBJ("limit3_time1").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday");
						OBJ("limit3_time2").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend");
					}
					break;
				}
			}
		}		
	},
	ClearBlocking1: function()
	{
		OBJ("blocking1").checked = false;
	},
	ClearBlocking2: function()
	{
		OBJ("blocking2").checked = false;
		OBJ("Mon").checked = false;
		OBJ("Tue").checked = false;
		OBJ("Wen").checked = false;
		OBJ("Thu").checked = false;
		OBJ("Fri").checked = false;
		OBJ("Sat").checked = false;
		OBJ("Sun").checked = false;
		OBJ("limit2_time_start").value = "00:00";
		OBJ("limit2_time_end").value = "00:00";
	},
	ClearBlocking3: function()
	{
		OBJ("blocking3").checked = false;
		OBJ("limit3_time1").value = "1";
		OBJ("limit3_time2").value = "1";
	},
	ScheduleSettingClear: function()
	{
		PAGE.ClearBlocking1();
		PAGE.ClearBlocking2();
		PAGE.ClearBlocking3();
	},
	ListChange: function()
	{
		var dirty = 0;
		//////check if is dirty or not below///////
		if(PAGE.now_list_select_value== null)
		{//user have't choose one multilist item
			PAGE.now_list_select_value = OBJ("device_list").value;
		}
		else
		{//user already choose one multilist item
			if(PAGE.now_list_select_value=="0")
			{
				var XMLtype = XG(PAGE.scheduling+"/guest/type");
				switch(XMLtype)
				{
					case "0":
						if(OBJ("blocking1").checked!=true)
							dirty=1;
						break;
					case "1":
						if(OBJ("blocking2").checked!=true || XG(PAGE.scheduling+"/guest/days")!=PAGE.ReadingDays() || XG(PAGE.scheduling+"/guest/start")!=OBJ("limit2_time_start").value || XG(PAGE.scheduling+"/guest/end")!=OBJ("limit2_time_end").value)
							dirty=1;
						break;
				}
			}
			else if(PAGE.now_list_select_value.search("!")!=-1)
			{
				if(OBJ("blocking3").checked==true||OBJ("blocking2").checked==true)
					dirty=1;
			}
			else
			{
				var XMLtype = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type");
				switch(XMLtype)
				{
					case "0":
						if(OBJ("blocking1").checked!=true)
							dirty=1;
						break;
					case "1":
						if(
						OBJ("blocking2").checked!=true
						|| XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days")!=PAGE.ReadingDays() 
						|| XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start")!=OBJ("limit2_time_start").value 
						|| XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end")!=OBJ("limit2_time_end").value
						)
							dirty=1;
						break;
					case "2":
						if(
						OBJ("blocking3").checked!=true
						|| XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday")!=OBJ("limit3_time1").value 
						|| XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend")!=OBJ("limit3_time2").value
						)
							dirty=1;
						break;
				}
			}
		}
		//////check if is dirty or not beyond//////
		if(dirty==0)
		{// No modify
			PAGE.ScheduleSettingClear();
			PAGE.now_list_select_index = OBJ("device_list").selectedIndex;//store now using selection
			PAGE.now_list_select_value = OBJ("device_list").value;// or use: OBJ("device_list").options[select_idx].value;
			if(PAGE.now_list_select_index < PAGE.SelectNewDHCPIndex)
			{//For XML device data
				if(PAGE.now_list_select_index==0)
				{//GUEST devices settings
					var XMLtype = XG(PAGE.scheduling+"/guest/type");
					switch(XMLtype)
					{
						case "0":
							OBJ("blocking1").checked=true;
							break;
						case "1":
							OBJ("blocking2").checked=true;
							PAGE.DaysToOption(XG(PAGE.scheduling+"/guest/days"));
							OBJ("limit2_time_start").value = XG(PAGE.scheduling+"/guest/start");
							OBJ("limit2_time_end").value = XG(PAGE.scheduling+"/guest/end");
							break;
					}
				}
				else
				{//Schedule and daily devices settings
					var XMLtype = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type");
					switch(XMLtype)
					{
						case "1":
							OBJ("blocking2").checked=true;
							PAGE.DaysToOption(XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days"));
							OBJ("limit2_time_start").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start");
							OBJ("limit2_time_end").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end");
							break;
						case "2":
							OBJ("blocking3").checked=true;
							OBJ("limit3_time1").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday");
							OBJ("limit3_time2").value = XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend");
							break;
						default:
							OBJ("blocking1").checked=true;
							break;
					}
				}
			}
			else
			{// Default is NONE
				OBJ("blocking1").checked=true;
			}
		}
		else
		{	// Modify
			BODY.ShowAlert('<?echo I18N("j", "Please click Save button to save settings before changed to another device.");?>');
			OBJ("device_list").value=PAGE.now_list_select_value;// or use: OBJ("device_list").selectedIndex = PAGE.now_list_select_index;
		}
		BODY.NewWDStyle_refresh();
		if(PAGE.now_list_select_value=="0")	OBJ("DailySetting").style.display="none";
		else	OBJ("DailySetting").style.display="block";
	},
	AddSettingsToList: function()
	{//Add XML data to multiselect list
		var cnt = XG(PAGE.scheduling+"/DeviceList/entry#");
		for(var i=1; i<=cnt; i++)
		{
			var objNAME = XG(PAGE.scheduling+"/DeviceList/entry:"+i+"/hostname");
			var objVALUE = i;
			if(objNAME=="")
				XG(PAGE.scheduling+"/DeviceList/entry:"+i+"/mac");
			
			COMM_AddSelectOption("device_list",objNAME,objVALUE);
		}
		PAGE.SelectNewDHCPIndex = OBJ("device_list").options.length;
	},
	CheckExistSettings: function(checkmac)
	{//0:checkmac not exist in our XML data. 1:already existed.
		var cnt = XG(PAGE.scheduling+"/DeviceList/entry#");
		for(var i=1; i<=cnt; i++)
		{
			var XMLmac = XG(PAGE.scheduling+"/DeviceList/entry:"+i+"/mac");
			if(checkmac==XMLmac)
				return 1;
		}
		return 0;
	},
	CheckExistHTMLlist: function(checkmac)
	{//0:checkmac not exist in our HTML list option data. 1:already existed.
		var obj = document.getElementById("device_list");
		for (var i = PAGE.SelectNewDHCPIndex ; i < obj.options.length; i++)
		{
			var mac = PAGE.GetOptionData(obj[i].value,2);
			if ( mac == checkmac)
			{
				return 1;
			}
		}
		return 0;
	},
	ShowMultiList: function()
	{
		PAGE.CleanMultiList("device_list");
		PAGE.AddSettingsToList();
		var svc = PXML.FindModule("DHCPS4.LAN-1");
		var svc_gz = PXML.FindModule("DHCPS4.LAN-2");
		var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
		var inf2p = PXML.FindModule("RUNTIME.INF.LAN-2");
		var userlist = PXML.FindModule("RUNTIME.DEVICE-LIST");
		if (!svc || !inf1p || !svc_gz || !inf2p || !userlist)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		PAGE.dhcps4 = GPBT(svc+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
		PAGE.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);
		var router_ip = XG(PAGE.leasep+"/dhcps4/pool/router");
		PAGE.leasep2 = GPBT(inf2p+"/runtime", "inf", "uid", "LAN-2", false);
		if (!PAGE.dhcps4)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		PAGE.leasep += "/dhcps4/leases";
		PAGE.leasep2 += "/dhcps4/leases";

		if (!PAGE.leasep)	return true;	// in bridge mode, the value of this.leasep is null.

		entry = PAGE.leasep+"/entry";
		var cnt = XG(entry+"#");
		if (XG(svc+"/inf/dhcps4")!="")		// when the dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt; i++)
			{
				var devicename = XG(entry+":"+i+"/hostname");
				var mac		= XG(entry+":"+i+"/macaddr");
				var ipaddr	= XG(entry+":"+i+"/ipaddr");
				var OPTIONvalue;
				var OPTIONname = devicename;
				if(OPTIONname=="")	OPTIONname = mac;
				OPTIONvalue = devicename+'!'+mac+'!'+ipaddr;

				if(router_ip!=ipaddr && mac!="00:00:00:00:00:00" && PAGE.CheckExistSettings(mac)==0)
				{
					COMM_AddSelectOption("device_list",OPTIONname,OPTIONvalue);
				}
			}
		}
		cnt = 0;
		cnt = XG(userlist+"/runtime/devlist/userlist/entry#");
		userlist += "/runtime/devlist/userlist/entry";
		for (var i=1; i<=cnt; i++)
		{
			var devicename = XG(userlist+":"+i+"/hostname");
			var mac		= XG(userlist+":"+i+"/macaddr");
			var ipaddr	= XG(userlist+":"+i+"/ipv4addr");
			if(ipaddr=="0.0.0.0" || ipaddr=="")
			{
				ipaddr = XG(entry+":"+i+"/ipv6addr");
			}
			var OPTIONvalue;
			var OPTIONname = devicename;
			if(OPTIONname=="")	OPTIONname = mac;
			OPTIONvalue = devicename+'!'+mac+'!'+ipaddr;

			if(router_ip!=ipaddr && mac!="00:00:00:00:00:00" && PAGE.CheckExistSettings(mac)==0 && PAGE.CheckExistHTMLlist(mac)==0)
			{
				COMM_AddSelectOption("device_list",OPTIONname,OPTIONvalue);
			}
		}
	},
	OpenUI: function()
	{
		OBJ("policy_management").style.display="";
		OBJ("policy_management").disabled	= false;
		OBJ("hr1").style.display="";
		OBJ("register_r").style.display="block";
		OBJ("register_result").innerHTML = "";
		OBJ("DHCP_text").style.display="";
		OBJ("DHCP_text").disabled = false;
		OBJ("DHCPtable").style.display="block";
		OBJ("Dscription").style.display="";
		OBJ("Router_text").style.display="block";
		OBJ("device_loc").style.display="block";
		OBJ("reg_device").style.display="block";
		OBJ("register_result2").innerHTML = "";
		OBJ("limit1_title").style.display="block";
		OBJ("limit1_Dscript").style.display="block";
		/*Disable button to save or cancel*/
		this.SetFieldData();
		this.SetPolicyLink();
	},
	CloseUI: function()
	{
		OBJ("policy_management").style.display="none";
		OBJ("policy_management").disabled	= true;	
		OBJ("hr1").style.display="none";
		OBJ("register_r").style.display="none";		
		OBJ("register_result").innerHTML = "";
		OBJ("DHCP_text").style.display="none";
		OBJ("DHCP_text").disabled = true;
		OBJ("DHCPtable").style.display="none";
		OBJ("Dscription").style.display="none";
		OBJ("Router_text").style.display="none";
		OBJ("device_loc").style.display="none";
		OBJ("reg_device").style.display="none";
		OBJ("register_result2").innerHTML = "";
		OBJ("div_restore").style.display="none";
		OBJ("limit1_title").style.display="none";
		OBJ("limit1_Dscript").style.display="none";		
		/*Enable button to save or cancel*/
	},
	checkPW: function(str)
	{
		var i=0;
		var result=0;
		if(str.length<6 || str.length>20)
		{
			result=-1;
		}
		else
		{
			for(i=0;i<str.length;i++) 
			{ 
				if(str.charAt(i)<'0'||(str.charAt(i)>'9' && str.charAt(i)<'A')||(str.charAt(i)>'Z' && str.charAt(i)<'a')||str.charAt(i)>'z')
				{ 
					result=-1; 
					break;
				}
			}
		}
		return result;
	},
	OnClickButtonRegister: function(mode)
	{
		var email = OBJ("email_count").value;
		var locate = OBJ("device_location").value;
		var argumentMAC="";
		var str;
		var chPW=0;
		if(mode=="RegisterRouter")
		{	
			var pass = OBJ("private_password").value;
			pass=this.RmSpace(pass);
			chPW=this.checkPW(pass);
			
			if(this.xml_location!=locate)
			{
					BODY.ShowAlert("<?echo I18N("j", "Please click on Apply button to save the new router location before registering the router.");?>");
			}
			else if(email == "" || email.match("@")==null || (email.lastIndexOf("@")!=email.indexOf("@")) || pass == "" || chPW != 0 )
			{
				if(email=="")
				{
					BODY.ShowAlert("<?echo I18N("j", "The email address is empty.");?>");
					OBJ("email_count").focus();
				}
				else if(pass == "")
				{
					BODY.ShowAlert("<?echo I18N("j", "The password is empty.");?>");
					OBJ("private_password").focus();
				}
				else if(chPW!=0)
				{
					BODY.ShowAlert("<?echo I18N("j", "Invalid password. Please enter a password between 6 and 20 alphanumeric characters.");?>");
					OBJ("private_password").focus();
				}
				else
				{
					BODY.ShowAlert("<?echo I18N("j", "The email address is invalid.");?>");
					OBJ("email_count").focus();
				}
			}
			else
			{
				this.mac = XG(this.parent_ctrl+"/runtime/devdata/wanmac");
				this.mac = this.getMAC(this.mac);
				//argumentMAC = this.ChangeMAC(this.mac);
				argumentMAC = this.mac;
				var ajaxObj = GetAjaxObj("RegisterRouter");
				ajaxObj.release();
				ajaxObj.createRequest();
				this.bindUI();
				OBJ("register_result").innerHTML = "<? echo I18N("h", "Registering router");?>";
				ajaxObj.onCallback = function(xml)
				{
					var register_result = xml.Get("/result/result1");
					switch(register_result)
					{
						case "Correct":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Registration Successful! You will receive a registration confirmation email.");echo "<br>";echo I18N("h", "Please follow the URL link in the email to confirm your registration of Parental Controls.");echo "<br>";echo I18N("h", "Parental Controls will be activated 5 minutes after you confirm your registration.");?>";
							break;
						case "Request fail":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Request fail");?>";
							break;
						case "Invalid HEADER part":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Invalid header part");?>";
							break;				
						case "Invalid BODY part":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Invalid body part");?>";
							break;
						case "Duplicated E-Mail and Password":
							OBJ("register_result").innerHTML = "<img src='pic/warning_mark_small.png' style='float:left;margin-right:10px'>"+"<? echo I18N("h", "Your selected email address/password combination is already registered for another router product. Please select another email address or password to register.");?>";
							break;				
						default:
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Registration failed");?>";
							break;
					}
					PAGE.showUI();
					if(register_result=="Correct"||register_result=="Duplicated E-Mail and Password")
					{
						OBJ("regist_router").style.display="none";
						OBJ("regist_router").disabled = true;
						OBJ("email_count").disabled = true;
						OBJ("password_text").style.display="none";
						OBJ("private_password").style.display="none";
						OBJ("div_restore").style.display="";
						OBJ("regist_device").style.display="";
						if(register_result=="Duplicated E-Mail and Password")
							PAGE.UpdateDeviceData();
					}
				}
				ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
				ajaxObj.sendRequest("parent_ctrl_register.php", "act=RegisterRouter&email="+email+"&password="+pass+"&location="+locate+"&mac="+argumentMAC);
			}
		}
		else if(mode=="RegisterDevice")
		{
			var i=1;
			var a1="{\"MAC\":\"";
			var a2="\"}";
			var del="";
			var add="";	
			var alreadyregister=0;
	
			for(i=1; i<=this.devicecount; i++)
			{
				argumentMAC="";
				argumentMAC=OBJ("mac_"+i).value;
				//argumentMAC=this.ChangeMAC(OBJ("mac_"+i).value);
				if(OBJ(i+"_check_0").checked==false)//del
				{
					if(COMM_EqBOOL(OBJ(i+"_check_0").getAttribute("modified"),true))
					{
						if(del=="")
							del = "'" + a1 + argumentMAC + a2;
						else
							del = del +"," + a1 + argumentMAC + a2;
					}
				}
				else//add
				{
					alreadyregister++;
					if(COMM_EqBOOL(OBJ(i+"_check_0").getAttribute("modified"),true))
					{
						if(add=="")
							add="'"+a1+argumentMAC+"\",\"Name\":\""+OBJ("name_"+i).value+a2;
						else
							add=add+","+a1+argumentMAC+"\",\"Name\":\""+OBJ("name_"+i).value+a2;
					}
				}
				OBJ(i+"_check_0").setAttribute("modified", "false");//set to default
			}
			if((add!="" || del!="")&& alreadyregister<=PAGE.device_limit)
			{
				if(add!="")
					add=add+"'";
				if(del!="")
					del=del+"'";

				var ajaxObj = GetAjaxObj("RegisterDevice");
				ajaxObj.release();
				ajaxObj.createRequest();
				this.bindUI();
				OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registering device");?>";
				ajaxObj.onCallback = function(xml)
				{
					var register_result = xml.Get("/result/result1");
					switch(register_result)
					{
						case "Correct":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registration success");?>";
							break;
						case "Request fail":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Request fail");?>";
							break;
						case "Invalid HEADER part":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid header part");?>";
							break;				
						case "Invalid BODY part":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid body part");?>";
							break;
						case "Duplicated E-Mail and Password":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Duplicated email and password");?>";
							break;				
						case "Invalid authentication":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid authentication");?>";
							break;				
						default:
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registration failed");?>";
							break;
					}
					PAGE.showUI();
				}
				ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
				ajaxObj.sendRequest("parent_ctrl_register.php", "act=RegisterDevice&add="+add+"&del="+del+"&email="+email+"&location="+locate);	
			}
			else if(alreadyregister > PAGE.device_limit)
			{
				BODY.ShowAlert("<? echo I18N("j", "The max number of devices registered is ");?>" + PAGE.device_limit);
				PAGE.MantainDeviceData();
			}
			else
			{
				BODY.ShowAlert("<? echo I18N("h", "Device settings have not changed.");?>");
			}
		}
		else if(mode=="RestoreUI")
		{
			if(this.xml_location!=locate)
			{
					BODY.ShowAlert("<?echo I18N("j", "Please click on Apply button to save the new router location before registering the router.");?>");
			}
			else
			{
				OBJ("email_count").disabled = false;
				OBJ("password_text").style.display="";
				OBJ("private_password").style.display="";
				OBJ("div_restore").style.display="none";
				OBJ("register_result").innerHTML = '';
				OBJ("regist_router").style.display="";
				OBJ("regist_router").disabled = false;
				OBJ("regist_router").style.color="white";
			}
		}
	},
	SetPolicyLink: function()
	{
		var ajaxObj = GetAjaxObj("GetLink");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			var cp_server = xml.Get("/result/result1");
			var email;
			var locate;//20120419 add
			locate = OBJ("device_location").value;//20120419 add
			if(OBJ("email_count").value=="")
			{	
				email = xml.Get("/result/result2");
			}
			else
			{
				email = OBJ("email_count").value;
			}
			if(cp_server==="undefined" ||cp_server==="" || cp_server===null)
			{
				OBJ("policy_management").style.display="none";
				OBJ("policy_management").disabled	= true;
				PAGE.tid = setTimeout("PAGE.SetPolicyLink();", 3000);
			}
			else
			{
				cp_server = "www.wdinternetsecurity.com";//this is new link!!!! In old link method,no need to do tis line.
				if(email==="" || email===null || email==="undefined")
				{
					//document.getElementById("linking").href="https://"+cp_server+"/wdpcs/login.php"; this is old link,at 20120419 WD want us use new link
					document.getElementById("linking").href="https://"+cp_server+"/?location="+locate;//this is new link! In old link method,no need to do tis line.
					//document.getElementById("linking").style.color = "#1E90FF";no need to do this. template.php has been modify.

				}
				else
				{
					//document.getElementById("linking").href="https://"+cp_server+"/wdpcs/login.php?email="+email; this is old link,at 20120419 WD want us use new link
					document.getElementById("linking").href="https://"+cp_server+"/?email="+email+"&location="+locate;//this is new link! In old link method,no need to do tis line.
					//document.getElementById("linking").style.color = "#1E90FF";no need to do this. template.php has been modify.
				}
				clearTimeout(PAGE.tid);
				OBJ("policy_management").style.display="";
				OBJ("policy_management").disabled	= false;
				OBJ("linking").innerHTML = "<?echo I18N("h","WD Internet parental controls policy management");?>";
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("parent_ctrl_register.php", "act=GetLink");
	},
	AddRegisteredData: function()
	{
		var list;
		var listname;
		var honame;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		
		list = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/name");//i start at 1
		while( list != null && list != "" )
		{
			//list=list.toLowerCase();
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+this.devicecount+'_check_0\');" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
						];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/name");
		}
	},
	bindUI: function()
	{	
		OBJ("en_parent_ctrl").disabled = true;	
		OBJ("device_span").disabled = true;	
		OBJ("email_count").disabled = true;
		OBJ("private_password").disabled = true;
		OBJ("regist_router").disabled = true;
		OBJ("regist_device").disabled = true;
		OBJ("policy_management").style.display="none";
		OBJ("policy_management").disabled	= true;
	},
	showUI: function()
	{
		OBJ("en_parent_ctrl").disabled = false;	
		OBJ("device_span").disabled = false;	
		if(OBJ("div_restore").style.display=="none")
			OBJ("email_count").disabled = false;
		OBJ("private_password").disabled = false;
		OBJ("regist_router").disabled = false;
		OBJ("regist_device").disabled = false;
		OBJ("policy_management").style.display="";
		OBJ("policy_management").disabled	= false;
		this.SetPolicyLink();
	},
	getMAC: function(m)
	{
		var myMAC="";
		if (m.search(":") != -1)	var tmp=m.split(":");
		else				var tmp=m.split("-");
		if (m == "" || tmp.length != 6)
			return "";
	
		for (var i=0; i<tmp.length; i++)
		{
			if (tmp[i].length==1)
				tmp[i]="0"+tmp[i];
			else if (tmp[i].length==0||tmp[i].length>2)
				return "";
		}
		myMAC = tmp[0];
		for (var i=1; i<tmp.length; i++)
		{
			//myMAC = myMAC + ':' + tmp[i];
			myMAC = myMAC + tmp[i];
		}
		return myMAC.toUpperCase();
	},
	RmPunc: function(str)
	{
		var i,j;
		var result="";
		var before="";
		var after="";
		while(str.length>0)
		{
			j=-1;
			j=str.search(":");
			if(j>=0)
			{
				before="";
				after="";
				before=str.substr(0,j);
				after=str.substr(j+1,str.length-j-1);
				str=after;
				result = result.concat(before);
			}
			else
			{
				result = result.concat(str);
			}
		}
		return result;
	},
	UpdateDeviceData: function()
	{
		OBJ("DHCP_updating_text").style.display="block";
		OBJ("regist_device").disabled	= true;
		COMM_GetCFG(false, "PARENTCTRL", function(xml) {PAGE.AddData(xml);});
	},
	AddData: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("PARENTCTRL");
		var list;
		var listname;
		var honame;
		var routername;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		var alreadyrun=0;
		routername = XG(p+"/runtime/netstar/name");
		list = XG(p+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(p+"/runtime/netstar/user:"+i+"/name");//i start at 1
		while( list != null && list != "" )
		{
			//list=list.toLowerCase();
			alreadyrun=1;
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
							];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(p+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(p+"/runtime/netstar/user:"+i+"/name");
		}
		if(alreadyrun==0 && routername=="")
		{
			PAGE.tid2 = setTimeout("PAGE.UpdateDeviceData();", 2000);
		}
		else
		{
			OBJ("DHCP_updating_text").style.display="none";
			OBJ("regist_device").disabled	= false;
			clearTimeout(PAGE.tid2);
			NewCheckRadio.init();
		}
	},
	MantainDeviceData: function()
	{
		OBJ("DHCP_updating_text").style.display="block";
		OBJ("regist_device").disabled	= true;
		COMM_GetCFG(false, "PARENTCTRL", function(xml) {PAGE.Mantaindata(xml);});
	},
	Mantaindata: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("PARENTCTRL");
		var list;
		var listname;
		var honame;
		var routername;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		var alreadyrun=0;
		routername = XG(p+"/runtime/netstar/name");
		list = XG(p+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(p+"/runtime/netstar/user:"+i+"/name");//i start at 1
		
		for(j=1;j<=k;j++)
			OBJ(j+"_check_0").checked = false;
			
		while( list != null && list != "" )
		{
			alreadyrun=1;
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
							];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(p+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(p+"/runtime/netstar/user:"+i+"/name");
		}
		if(alreadyrun==0 && routername=="")
		{
			PAGE.tid2 = setTimeout("PAGE.UpdateDeviceData();", 2000);
		}
		else
		{
			OBJ("DHCP_updating_text").style.display="none";
			OBJ("regist_device").disabled	= false;
			clearTimeout(PAGE.tid2);
			NewCheckRadio.init();
		}
	},
	SetLocZone: function(val)
	{
		var zone=0;
		if(val==null|| val=="")
		{
			val = XG(this.devtime_p+"/device/time/timezone");
			zone = parseInt(val,10);
			if( zone>0 && zone<25 )
			{
				OBJ("device_location").value = "USA";
				this.located = "USA";
			}
			else if( zone>24 && zone<49 )
			{
				OBJ("device_location").value = "EMEA";
				this.located = "EMEA";
			}
			else if( zone>48 && zone<76 )
			{
				OBJ("device_location").value = "APAC";
				this.located = "APAC";
			}
			else
			{
				OBJ("device_location").value = "USA";//default is USA
				this.located = "USA";//default is USA
			}
		}
		else
		{
			OBJ("device_location").value = val;
			this.located = val;
		}
	},
	SetFieldData: function()
	{
		var ajaxObj = GetAjaxObj("GetFieldData");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			PAGE.located = xml.Get("/result/result1");//location
			PAGE.SetLocZone(PAGE.located);
			if(PAGE.email=="" ||PAGE.email==null)
			{
				PAGE.email = xml.Get("/result/result2");//email
				PAGE.valid_mail = PAGE.email;
				OBJ("email_count").value = PAGE.email;
				OBJ("private_password").value = xml.Get("/result/result3");//password
			}			
			if(OBJ("email_count").value!="" && OBJ("email_count").value!=null)
			{
				OBJ("regist_router").style.display="none";
				OBJ("regist_router").disabled = true;
				OBJ("email_count").disabled = true;
				OBJ("password_text").style.display="none";
				OBJ("private_password").style.display="none";
				OBJ("div_restore").style.display="";
				OBJ("regist_device").style.display="";
			}
			else
			{
				OBJ("div_restore").style.display="none";
				OBJ("private_password").value = "";
				OBJ("regist_device").style.display="none";
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("parent_ctrl_register.php", "act=GetFieldData");
	},
	RmSpace: function(str)
	{
		ptntrim = /(^\s*)|(\s*$)/g;
		return str.replace(ptntrim,"");
	},
	PreSubmit: function()
	{	
		if(OBJ("PCoption1").checked== true)
		{
			PAGE.EnDelay = 0;
			PXML.IgnoreModule("PARENTCTRLSCHL");
			this.located = OBJ("device_location").value;
			var ajaxObj = GetAjaxObj("Apply");
			ajaxObj.release();
			ajaxObj.createRequest();
			
			ajaxObj.onCallback = function(xml)
			{/*do nothing*/}
			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			ajaxObj.sendRequest("parent_ctrl_register.php", "act=Apply&location="+PAGE.located+"&en="+PAGE.enable);
			return PXML.doc;
		}
		else if(OBJ("PCoption2").checked== true)
		{
			PAGE.EnDelay = 1;
			var result = PAGE.SaveXMLOption2();
			if(result=="SaveFail")
			{//Check failed. Don't save.
				return null;
			}
			else if(result=="SaveXML")
			{//Check Success. Do save.
				return  PXML.doc;
			}
			else if(result=="NoChange")
			{//Don't change. Reload webpage.
				PAGE.ScheduleSettingClear();
				var msgArray =
				[
					"<?echo I18N('h', 'Settings have not changed.');?>",
					"<input id='nochg' type='button' class='button_blueX2' value='<?echo I18N('h', 'Continue');?>' onClick='window.location.reload();' />"
				];
				BODY.ShowMessage('', msgArray);
				OBJ("menu").style.display	= "none";
				OBJ("content").style.display= "none";
				OBJ("mbox").style.display	= "block";
				OBJ("mbox2").style.display	= "none";
				OBJ("nochg").focus();
				return null;
			}
		}
	},
	GetOptionData: function(str,pos)
	{//Get option's name, mac, and IP. pos: 1 or 2 or 3
		var f=-1;
		var i=0;
		var result= new Array(5);
		
		f = str.search("!");
		while(f!=-1)
		{
			result[i] = str.substr(0,f);
			i++;
			str = str.substr(f+1,str.length-f-1);
			f=-1;
			f = str.search("!");
		}
		result[i]=str;
		
		return result[pos-1];
	},
	scheduleCheck: function()
	{
		if(OBJ("blocking2").checked==true)
		{
			if(OBJ("Mon").checked == false && OBJ("Tue").checked == false && OBJ("Wen").checked == false
			&& OBJ("Thu").checked == false && OBJ("Fri").checked == false && OBJ("Sat").checked == false
			&& OBJ("Sun").checked == false)
			{
				BODY.ShowAlert("<? echo I18N('j', 'You must select at least one days if you want to use Block access control.');?>");
				return "FAIL";
			}
		}
	},
	DirtyCheck: function()
	{
		var p = PXML.FindModule("PARENTCTRLSCHL");
		p = p + "/security/parental";
		var pre = p.replace("/parental","");
		if( XG(p+"/enable") != "1" || XG(pre+"/active") != "1" )
		{
			return "DIRTY";
		}
		else
		{
			if(PAGE.now_list_select_index==null)
			{
				return "NOT_DIRTY";
			}
			else
			{
				if(PAGE.now_list_select_index < PAGE.SelectNewDHCPIndex)
				{
					if(PAGE.now_list_select_index==0)
					{//for guest settings
						var original_type = XG(p+"/guest/type");
						switch(original_type)
						{
							case "0":
								if(OBJ("blocking1").checked!=true)	return "DIRTY";
								else	return "NOT_DIRTY";
							case "1":
								if(OBJ("blocking2").checked!=true)
								{
									return "DIRTY";
								}
								else
								{
									if(PAGE.ReadingDays() == XG(p+"/guest/days") &&
									OBJ("limit2_time_start").value == XG(p+"/guest/start") &&
									OBJ("limit2_time_end").value == XG(p+"/guest/end"))
									{
										return "NOT_DIRTY";
									}
									else
									{
										return "DIRTY";
									}
								}
						}
					}
					else
					{//for schedule and daily settings
						var original_type = XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type");
						switch(original_type)
						{
							case "0":
								if(OBJ("blocking1").checked!=true)	return "DIRTY";
								else	return "NOT_DIRTY";
							case "1":
								if(OBJ("blocking2").checked!=true)
								{
									return "DIRTY";
								}
								else
								{
									if(PAGE.ReadingDays() == XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days") &&
									OBJ("limit2_time_start").value == XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start") &&
									OBJ("limit2_time_end").value == XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end"))
									{
										return "NOT_DIRTY";
									}
									else
									{
										return "DIRTY";
									}
								}
							case "2":
								if(OBJ("blocking3").checked!=true)
								{
									return "DIRTY";
								}
								else
								{
									if(
									OBJ("limit3_time1").value == XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday") &&
									OBJ("limit3_time2").value == XG(p+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend")
									)
									{
										return "NOT_DIRTY";
									}
									else
									{
										return "DIRTY";
									}
								}
						}
					}
				}
				else
				{
					if(OBJ("blocking1").checked==true)	return "NOT_DIRTY";
					else	return "DIRTY";
				}
			}
		}
	},
	SaveXMLOption2: function()
	{
		if(PAGE.enable=="2" && OBJ("PCoption2").checked== true)
		{
			if(PAGE.DirtyCheck() != "DIRTY")
			{
				return "NoChange";
			}
			if(PAGE.now_list_select_index > PAGE.rule_limit)
			{
				BODY.ShowAlert("<? echo I18N('j', 'You have already used maximum of 25 devices allowed for scheduled Internet access block. In order to set up this device for scheduling, change one of devices to None option.');?>");
				return "SaveFail";
			}
			if(PAGE.scheduleCheck()=="FAIL")
			{
				return "SaveFail"; 
			}
			var p = PAGE.scheduling.replace("/parental","");
			var chg = 0;
			if(XG(p+"/active")!="1" || XG(PAGE.scheduling+"/enable")!="1")
				chg = 1;
			XS(p+"/active","1");
			XS(PAGE.scheduling+"/enable","1");
			
			if(PAGE.now_list_select_index != null)
			{//user already choose one item in multilist
				if(PAGE.now_list_select_index < PAGE.SelectNewDHCPIndex)
				{//modify setting to XML
					if(PAGE.now_list_select_index==0)
					{//for guest settings
						var old_type=XG(PAGE.scheduling+"/guest/type");
						XS(PAGE.scheduling+"/action/target","GUEST");
						XS(PAGE.scheduling+"/action/LastState",old_type);
						XS(PAGE.scheduling+"/action/acceptMark","");
						XS(PAGE.scheduling+"/action/dropMark","");
						XS(PAGE.scheduling+"/action/trigger","");
						if(old_type=="0")
						{
							if(OBJ("blocking1").checked==true)
							{//NONE-->NONE
								XS(PAGE.scheduling+"/action/NowState","0");
								if(chg!=1)	return "NoChange";
							}
							else if(OBJ("blocking2").checked==true)
							{//NONE-->SCHEDULE
								XS(PAGE.scheduling+"/action/NowState","1");
								XS(PAGE.scheduling+"/action/days",PAGE.ReadingDays());
								XS(PAGE.scheduling+"/action/start",OBJ("limit2_time_start").value);
								XS(PAGE.scheduling+"/action/end",OBJ("limit2_time_end").value);
								XS(PAGE.scheduling+"/guest/type","1");
								XS(PAGE.scheduling+"/guest/days",PAGE.ReadingDays());
								XS(PAGE.scheduling+"/guest/start",OBJ("limit2_time_start").value);
								XS(PAGE.scheduling+"/guest/end",OBJ("limit2_time_end").value);
								XS(PAGE.scheduling+"/guest/weekday","");
								XS(PAGE.scheduling+"/guest/weekend","");
							}
							else if(OBJ("blocking3").checked==true)
							{//NONE-->DAILY
								BODY.ShowAlert("<?echo I18N('j', 'Guest devices can not use \'Limit daily usage\' function.');?>");
								return "SaveFail";
							}
						}
						else if(old_type=="1")
						{
							XS(PAGE.scheduling+"/action/days",XG(PAGE.scheduling+"/guest/days"));
							XS(PAGE.scheduling+"/action/start",XG(PAGE.scheduling+"/guest/start"));
							XS(PAGE.scheduling+"/action/end",XG(PAGE.scheduling+"/guest/end"));
							if(OBJ("blocking1").checked==true)
							{//SCHEDULE-->NONE
								XS(PAGE.scheduling+"/action/NowState","0");
								XS(PAGE.scheduling+"/guest/type","0");
								XS(PAGE.scheduling+"/guest/days","");
								XS(PAGE.scheduling+"/guest/start","");
								XS(PAGE.scheduling+"/guest/end","");
								XS(PAGE.scheduling+"/guest/weekday","");
								XS(PAGE.scheduling+"/guest/weekend","");
							}
							else if(OBJ("blocking2").checked==true)
							{//SCHEDULE-->SCHEDULE
								XS(PAGE.scheduling+"/action/NowState","1");
								XS(PAGE.scheduling+"/guest/type","1");
								XS(PAGE.scheduling+"/guest/days",PAGE.ReadingDays());
								XS(PAGE.scheduling+"/guest/start",OBJ("limit2_time_start").value);
								XS(PAGE.scheduling+"/guest/end",OBJ("limit2_time_end").value);
								XS(PAGE.scheduling+"/guest/weekday","");
								XS(PAGE.scheduling+"/guest/weekend","");
							}
							else if(OBJ("blocking3").checked==true)
							{//SCHEDULE-->DAILY
								BODY.ShowAlert("<?echo I18N('j', 'Guest devices can not use \'Limit daily usage\' function.');?>");
								return "SaveFail";
							}
						}
						else
						{
							BODY.ShowAlert("<?echo I18N('j', 'Guest devices can not use \'Limit daily usage\' function.');?>");
							return "SaveFail";
						}
					}
					else
					{//for schedule and daily settings
						XS(PAGE.scheduling+"/action/LastState",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type"));
						XS(PAGE.scheduling+"/action/target",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/mac"));
						XS(PAGE.scheduling+"/action/acceptMark",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/acceptMark"));
						XS(PAGE.scheduling+"/action/dropMark",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/dropMark"));
						XS(PAGE.scheduling+"/action/trigger",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/trigger"));
						XS(PAGE.scheduling+"/action/days",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days"));
						XS(PAGE.scheduling+"/action/start",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start"));
						XS(PAGE.scheduling+"/action/end",XG(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end"));

						if(OBJ("blocking1").checked==true)
						{
							XS(PAGE.scheduling+"/action/NowState","0");
							//XD(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value);//we delete node in the setcfg, not here.
						}
						else if(OBJ("blocking2").checked==true)
						{
							XS(PAGE.scheduling+"/action/NowState","1");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type","1");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days",PAGE.ReadingDays());
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start",OBJ("limit2_time_start").value);
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end",OBJ("limit2_time_end").value);
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday","");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend","");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/trigger","");
						}
						else if(OBJ("blocking3").checked==true)
						{
							XS(PAGE.scheduling+"/action/NowState","2");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/type","2");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/days","");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/start","");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/end","");
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekday",OBJ("limit3_time1").value);
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/weekend",OBJ("limit3_time2").value);
							XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.now_list_select_value+"/trigger","");
						}
					}
				}
				else
				{//new setting to XML
					XS(PAGE.scheduling+"/action/LastState","0");
					XS(PAGE.scheduling+"/action/days",PAGE.ReadingDays());
					XS(PAGE.scheduling+"/action/start",OBJ("limit2_time_start").value);
					XS(PAGE.scheduling+"/action/end",OBJ("limit2_time_end").value);
					XS(PAGE.scheduling+"/action/acceptMark","");
					XS(PAGE.scheduling+"/action/dropMark","");
					XS(PAGE.scheduling+"/action/trigger","");
					XS(PAGE.scheduling+"/action/target",PAGE.GetOptionData(OBJ("device_list").value,2));
					if(OBJ("blocking1").checked==true)
					{
						XS(PAGE.scheduling+"/action/NowState","0");
						/*BODY.ShowAlert("<?echo I18N('j', 'This device is set to no scheduled Internet access restriction by default.');?>");*/
						if(chg!=1)	return "NoChange";
					}
					else if(OBJ("blocking2").checked==true)
					{
						XS(PAGE.scheduling+"/action/NowState","1");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/type","1");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/ip",PAGE.GetOptionData(OBJ("device_list").value,3));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/mac",PAGE.GetOptionData(OBJ("device_list").value,2));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/hostname",PAGE.GetOptionData(OBJ("device_list").value,1));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/days",PAGE.ReadingDays());
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/start",OBJ("limit2_time_start").value);
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/end",OBJ("limit2_time_end").value);
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/weekday","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/weekend","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/trigger","0");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/acceptMark","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/dropMark","");
					}
					else if(OBJ("blocking3").checked==true)
					{
						XS(PAGE.scheduling+"/action/NowState","2");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/type","2");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/ip",PAGE.GetOptionData(OBJ("device_list").value,3));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/mac",PAGE.GetOptionData(OBJ("device_list").value,2));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/hostname",PAGE.GetOptionData(OBJ("device_list").value,1));
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/days","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/start","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/end","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/weekday",OBJ("limit3_time1").value);
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/weekend",OBJ("limit3_time2").value);
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/trigger","0");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/acceptMark","");
						XS(PAGE.scheduling+"/DeviceList/entry:"+PAGE.SelectNewDHCPIndex+"/dropMark","");
					}
				}
			}
		}
		else
		{
			XS(PAGE.scheduling+"/enable","0");
		}
		return "SaveXML";
	},
	OnClickRadioMethod: function()
	{
		if(OBJ("PCoption1").checked==true)
		{
			PAGE.enable = "1";
			PAGE.CloseOption2();
			OBJ("last_save").style.display="none";
			if(PAGE.now_using != 1)
			{
				PAGE.CloseUI();
				OBJ("hr1").style.display="block";
				OBJ("device_loc").style.display="block";
				PAGE.SetLocZone("");
			}
			else
			{
				PAGE.Option1();
			}
		}
		else
		{
			PAGE.enable = "2";
			PAGE.ShowMultiList();
			PAGE.Option2();
			PAGE.CloseUI();
			OBJ("last_save").style.display="block";
		}
		OBJ("first_save").style.display="block";
		BODY.NewWDStyle_refresh();
	},
	OnClickChange: function()
	{
		if(OBJ("en_parent_ctrl").checked)
		{
			OBJ("control_option").style.display="block";
			if(PAGE.now_using==1)
			{
				OBJ("PCoption1").checked= true;
				PAGE.Option1();
				OBJ("first_save").style.display="block";
				OBJ("last_save").style.display="none";
				PAGE.enable = "1";
			}
			else if(PAGE.now_using==2)
			{
				OBJ("PCoption2").checked= true;
				PAGE.Option2();
				OBJ("first_save").style.display="block";
				OBJ("last_save").style.display="block";
				PAGE.enable = "2";
			}
			else if(OBJ("PCoption1").checked== true)
			{
				PAGE.CloseOption2();
				OBJ("last_save").style.display="none";
				PAGE.CloseUI();
				OBJ("hr1").style.display="block";
				OBJ("device_loc").style.display="block";
				OBJ("first_save").style.display="block";
				PAGE.SetLocZone("");
				PAGE.enable = "1";
			}
			else if(OBJ("PCoption2").checked== true)
			{
				PAGE.Option2();
				PAGE.CloseUI();
				OBJ("first_save").style.display="block";
				OBJ("last_save").style.display="block";
				PAGE.enable = "2";
				PAGE.ShowMultiList();
			}
			else
			{
				OBJ("first_save").style.display="none";
			}
		}
		else
		{
			OBJ("control_option").style.display="none";
			OBJ("last_save").style.display="none";
			PAGE.CloseUI();
			PAGE.CloseOption2();
			PAGE.enable = "0";
		}
	},
	Option1: function()
	{
		if(OBJ("en_parent_ctrl").checked && this.xml_location!="")
		{
			OBJ("device_location").value=this.xml_location;
			this.OpenUI();
			this.ShowTable();
			this.enable = "1";
		}
		else if(OBJ("en_parent_ctrl").checked)
		{
			this.CloseUI();
			OBJ("hr1").style.display="block";
			OBJ("device_loc").style.display="block";
			this.enable = "1";
			this.SetLocZone("");
		}
		else
		{
			this.CloseUI();
			this.enable = "0";
		}
	},
	Option2: function()
	{

		OBJ("hr2").style.display="block";
		OBJ("option2-1").style.display="block";
		OBJ("option2-2").style.display="block";	
	},
	CloseOption2: function()
	{
		OBJ("hr2").style.display="none";
		OBJ("option2-1").style.display="none";
		OBJ("option2-2").style.display="none";
	},	
	OnClickDHCP: function(check)
	{
		if(COMM_EqBOOL(OBJ(check).getAttribute("modified"),true))
		{
			OBJ(check).setAttribute("modified", "false");
		}
		else
		{
			OBJ(check).setAttribute("modified", "true");
		}
	},
	isDirty: null,
	Synchronize: function() {}
}
</script>
