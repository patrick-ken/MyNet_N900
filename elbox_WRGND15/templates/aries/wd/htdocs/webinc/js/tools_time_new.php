<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
.tzselect
{
	font-family: Tahoma, Helvetica, Geneva, Arial, sans-serif;
	font-size: 10px;
}
.timebox
{
	padding: 0 10px 10px 10px;
	width:   525px;
    color: white;
	font: small-caption;
}
.timebox_item
{
  font-family: Arial, Helvetica, sans-serif;
}
td.timebox_item select
{
  font-size: 10px;
  width:     55px;
}
</style>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DEVICE.TIME, RUNTIME.TIME,RUNTIME.SERVICES.TIMEZONE,PARENTCTRLSCHL",
	OnLoad:    function() {lanugage_StyleSet('<? echo $lang;?>' , "<?echo $TEMP_MYNAME; ?>");},
	OnUnload:  function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		PXML.IgnoreModule("PARENTCTRLSCHL");
		
		this.devtime_p = PXML.FindModule("DEVICE.TIME");
		this.runtime_p = PXML.FindModule("RUNTIME.TIME");
		this.timezone_p = PXML.FindModule("RUNTIME.SERVICES.TIMEZONE");
		this.parent_schedule = PXML.FindModule("PARENTCTRLSCHL");
		
		if (!this.devtime_p || !this.runtime_p || !this.parent_schedule) { BODY.ShowAlert("<?echo I18N("j", "InitValue ERROR!");?>"); return false; }

		OBJ("st_time").innerHTML = XG(this.runtime_p+"/runtime/device/uptime");			
		var tz = XG(this.devtime_p+"/device/time/timezone");
		COMM_SetSelectValue(OBJ("timezone"),COMM_ToNUMBER(tz));
		
		this.InitTimeZone();				
		OBJ("ntp_enable").checked = (XG(this.devtime_p+"/device/time/ntp/enable")=="1");
		this.NtpEnDiSomething();
		OBJ("sync_msg").innerHTML = "";
		OBJ("sync_pc_msg").innerHTML = "";
		this.IsSynchronized = "";

		if(OBJ("ntp_enable").checked) 
		{
			this.UpdateCurrentTime(xml);
		}	
		else 
		{	
			this.UpdateSyncTime(xml);						
		}

		OBJ("ntp_server").value = XG(this.devtime_p+"/device/time/ntp/server");
		this.InitNTP_list();
		
		this.InitManualBox();
		
		return true;
	},
		
	PreSubmit: function()
	{		
		clearTimeout(PAGE.tid1);
		clearTimeout(PAGE.tid2);
		if(XG(this.parent_schedule+"/security/active")=="1" && XG(this.parent_schedule+"/security/parental/enable")=="1")
		{
			if(PAGE.isModified()==true)
			{
				XA(this.devtime_p+"/runtime/device/ParentControlRestart", "1");
			}
		}
		else
		{
			XA(this.devtime_p+"/runtime/device/ParentControlRestart", "0");
		}
		
		XS(this.devtime_p+"/device/time/timezone",	OBJ("timezone").value);				
		XS(this.devtime_p+"/device/time/dst",		OBJ("daylight").checked ? "1":"0");
		if(OBJ("daylight").checked)
		{															
			this.GenDstManual();
			XS(this.devtime_p+"/device/time/dstmanual", this.dst_manual);
			XS(this.devtime_p+"/device/time/dstoffset", OBJ("daylight_offset").value);			
		}	
						
		if(OBJ("ntp_enable").checked)
		{
			XS(this.devtime_p+"/device/time/ntp/enable", "1");
			XS(this.devtime_p+"/device/time/ntp/server", OBJ("ntp_server").value);			
			XS(this.devtime_p+"/device/time/ntp6/enable", "0"); /* Temporarily closed ntp6, 20120126 Daniel Chen */

			PXML.IgnoreModule("RUNTIME.TIME");
			this.PreNTP_list();
		}
		else
		{
			XS(this.devtime_p+"/device/time/ntp/enable", "0");
			var date = OBJ("month").value+"/"+OBJ("day").value+"/"+OBJ("year").value;
			var time = OBJ("hour").value+":"+OBJ("minute").value+":"+OBJ("second").value;			
			XS(this.runtime_p+"/runtime/device/date", date);
			XS(this.runtime_p+"/runtime/device/time", time);
			XS(this.devtime_p+"/device/time/ntp6/enable", "0");						
			XS(this.devtime_p+"/device/time/date", date);
			XS(this.devtime_p+"/device/time/time", time);
			
			PXML.ActiveModule("RUNTIME.TIME");
		}

		return PXML.doc;
	},
	InitNTP_list: function()
	{
		while(OBJ("ntp_server").length != 1 )
		{
			OBJ("ntp_server").options.remove(OBJ("ntp_server").length-1);    
		}
	    var cnt = XG(this.devtime_p+"/device/time/ntp_list/entry#");
		for (var i=1; i<= cnt; i++)
		{
			var varItem = new Option(XG(this.devtime_p+"/device/time/ntp_list/entry:"+i), XG(this.devtime_p+"/device/time/ntp_list/entry:"+i));
			OBJ("ntp_server").options.add(varItem);	
		}
		COMM_SetSelectValue(OBJ("ntp_server"), XG(this.devtime_p+"/device/time/ntp/server"));
		return true;
	},
	PreNTP_list: function()
	{
		/* clear ntp_list entry */
		XD(this.devtime_p+"/device/time/ntp_list/");

		for (var i=1; i < OBJ("ntp_server").length; i++)
		{
			XS(this.devtime_p+"/device/time/ntp_list/entry:"+i, OBJ("ntp_server").options[i].value );
		}
		return true;
	},
	isModified: function()
	{
		var flag = 0;
		var parent_schdule = 0;

		flag = OBJ("daylight").checked?"1":"0";
		if(XG(this.devtime_p+"/device/time/dst")!=flag || XG(this.devtime_p+"/device/time/timezone")!=OBJ("timezone").value)
		{
			parent_schdule++;
		}
		
		if(OBJ("daylight").checked)
		{				
			this.GenDstManual();
			if(this.dst_manual != XG(this.devtime_p+"/device/time/dstmanual")||OBJ("daylight_offset").value != XG(this.devtime_p+"/device/time/dstoffset"))
			{
				parent_schdule++;
			}
		}			
		return parent_schdule==0 ? false : true;
	},
	
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	parent_schedule: null,
	devtime_p : null,
	runtime_p : null,
	timezone_p: null,
	dst_manual: "",
	tid1: null,
	tid2: null,
	IsSynchronized: null,
	dsFlag    : [''<?
				foreach ("/runtime/services/timezone/zone")
				{
					echo ",'";
					echo map("dst","","0","*","1");
					echo "'";
				}
				?>],

	GetDaysInMonth :function(year, mon)
	{
		var days;
		if (mon==1 || mon==3 || mon==5 || mon==7 || mon==8 || mon==10 || mon==12) days=31;
		else if (mon==4 || mon==6 || mon==9 || mon==11) days=30;
		else if (mon==2)
		{
			if (((year % 4)==0) && ((year % 100)!=0) || ((year % 400)==0)) { days=29; }
			else { days=28; }
		}
		return (days);
	},

	InitManualBox: function()
	{
		var datev, timev, marr, i, days_InMonth;
		datev = XG(this.runtime_p+"/runtime/device/date");
		timev = XG(this.runtime_p+"/runtime/device/time");
		
		/*init date */
		marr = datev.split("/");																				
		for(i = 0; i < 3; i++) 
		{			
			marr[i] = parseInt(marr[i], 10); 			
		}
						
		days_InMonth = this.GetDaysInMonth(marr[2], marr[0]);	
				
		for(var i=0;i<days_InMonth;i++) 
		{ 
			OBJ("day").options[i] = new Option(i+1, i+1);
		}
		OBJ("day").length = days_InMonth;		
		COMM_SetSelectValue(OBJ("year"), parseInt(marr[2], 10));	
		COMM_SetSelectValue(OBJ("month"), parseInt(marr[0], 10)); 
		COMM_SetSelectValue(OBJ("day"),	parseInt(marr[1], 10));  	
		
		/*init time */
		marr = timev.split(":");																				
		for(i = 0; i < 3; i++) { marr[i] = parseInt(marr[i], 10); }			
		COMM_SetSelectValue(OBJ("hour"), parseInt(marr[0], 10));	
		COMM_SetSelectValue(OBJ("minute"), parseInt(marr[1], 10)); 
		COMM_SetSelectValue(OBJ("second"),	parseInt(marr[2], 10));  			
	},

	NtpEnDiSomething: function()
	{
		var dis = OBJ("ntp_enable").checked ? false : true;
		/* ntp part */
		OBJ("ntp_server").disabled  = OBJ("ntp_sync").disabled = dis;
		OBJ("manual_sync").disabled = !(dis);
		OBJ("add_ntp_server").disabled  = OBJ("ntpadd").disabled = OBJ("ntpdel").disabled = dis;
		/* manual part */
		OBJ("year").disabled = OBJ("month").disabled  = OBJ("day").disabled = !(dis);
		OBJ("hour").disabled = OBJ("minute").disabled = OBJ("second").disabled = !(dis);
	},
	
	GetCurrentStatus: function()
	{
		COMM_GetCFG(false, "RUNTIME.TIME", PAGE.UpdateCurrentTime);
	},

	UpdateCurrentTime: function(xml)
	{		
		var rt = xml.GetPathByTarget("/postxml", "module", "service", "RUNTIME.TIME", false);
				
		if (rt != "")
		{
			usev6 = false;
			OBJ("st_time").innerHTML = xml.Get(rt+"/runtime/device/uptime");							
			var ntpstate = xml.Get(rt+"/runtime/device/ntp/state");
			var v6enable = xml.Get(rt+"/device/time/ntp6/enable");
			if((ntpstate!="SUCCESS") && (v6enable=="1"))
			{
				ntpstate = xml.Get(rt+"/runtime/device/ntp6/state");
				usev6 = true;
			}

			switch (ntpstate)
			{
			case "SUCCESS":				
				if(usev6)
				{
					var st_server = xml.Get(rt+"/runtime/device/ntp6/server");
					var st_uptime = xml.Get(rt+"/runtime/device/ntp6/uptime");
					var st_nexttime = xml.Get(rt+"/runtime/device/ntp6/nexttime");
				}
				else
				{
					var st_server = xml.Get(rt+"/runtime/device/ntp/server");
					var st_uptime = xml.Get(rt+"/runtime/device/ntp/uptime");
					var st_nexttime = xml.Get(rt+"/runtime/device/ntp/nexttime");
				}
				var msg_str = "<?echo I18N("h", "The time has been successfully synchronized.");?>";
				msg_str += "<br>(<?echo I18N("h", "NTP Server Used: ");?>" + st_server + "<?echo I18N("h", ", Time: ");?>"+st_uptime+")";
				msg_str += "<br><?echo I18N("h", "Next synchronization time: ");?>"+st_nexttime;
				OBJ("st_time").innerHTML = xml.Get(rt+"/runtime/device/uptime");
				if(this.IsSynchronized==="") OBJ("sync_msg").innerHTML = msg_str;
				this.IsSynchronized = "yes";
				clearTimeout(PAGE.tid2);
				this.tid1 = setTimeout("PAGE.GetCurrentStatus();", 1000);
				break;
			case "RUNNING":
				OBJ("sync_msg").innerHTML = "<?echo I18N("h", "Synchronizing ...");?>";
				this.IsSynchronized = "";
				setTimeout("PAGE.GetCurrentStatus();", 1000);
				break;
			case "FAILED":
				OBJ("sync_msg").innerHTML = "<?echo I18N("h", "Synchronizing Failed.");?>";
				this.IsSynchronized = "";
				setTimeout("PAGE.GetCurrentStatus();", 1000);
				break;
			default:
				break;
			}
		}
	},
	
	PreClickSync: function()
	{
		
		/* We don't need synchronous other object status. 20120127 Daniel Chen */
			//	XS(this.devtime_p+"/device/time/timezone",	OBJ("timezone").value);
			//	XS(this.devtime_p+"/device/time/dst",		OBJ("daylight").checked ? "1":"0");

		if(OBJ("ntp_enable").checked)
		{
			XS(this.devtime_p+"/device/time/ntp/enable", "1");
			XS(this.devtime_p+"/device/time/ntp/server", OBJ("ntp_server").value);
			XS(this.devtime_p+"/device/time/ntp6/enable", "0");/* Temporarily closed ntp6, 20120126 Daniel Chen */
		}
		else
		{
			XS(this.devtime_p+"/device/time/ntp/enable", "0");
			var dateObj = new Date();
			var date = (dateObj.getMonth()+1)+"/"+dateObj.getDate()+"/"+dateObj.getFullYear();
			var time = dateObj.getHours()+":"+dateObj.getMinutes()+":"+dateObj.getMinutes();			
			XS(this.runtime_p+"/runtime/device/date", date);
			XS(this.runtime_p+"/runtime/device/time", time);
			XS(this.devtime_p+"/device/time/ntp6/enable", "0");
			XS(this.devtime_p+"/device/time/date", date);
			XS(this.devtime_p+"/device/time/time", time);
		}
		
		this.Synchronize();
		var xml = PXML.doc;
		PXML.UpdatePostXML(xml);
		PXML.Post(function(code, result){BODY.SubmitCallback(code,result);});
	},

	InitTimeZone: function()
	{				
		if(XG(this.devtime_p+"/device/time/dst")=="1")		
		{		
			OBJ("daylight").checked = true;					
		}	
		else				
		{
			OBJ("daylight").checked = false;						
		}	
							
		this.SelectTimeZone(false);
	},
		
	OnClickNTPSync: function()
	{
		if(OBJ("ntp_server").value==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Invalid NTP server !");?>");
			return false;
		}
		OBJ("sync_msg").innerHTML = "<?echo I18N("h", "Synchronizing ...");?>";
		clearTimeout(PAGE.tid1);
		this.IsSynchronized = "";

		this.PreClickSync();		
		var ajaxObj = GetAjaxObj("NTPUpdate");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function (xml) { ajaxObj.release(); PAGE.GetCurrentStatus(); }
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("service.cgi", "SERVICE=DEVICE.TIME&ACTION=RESTART");
	},
	
	GetSyncTimeStatus: function()
	{												
		COMM_GetCFG(false, "RUNTIME.TIME", PAGE.UpdateSyncTime);
	},

	UpdateSyncTime: function(xml)
	{		
		var rt = xml.GetPathByTarget("/postxml", "module", "service", "RUNTIME.TIME", false);
				
		if (rt != "")
		{												
			var syncState = xml.Get(rt+"/runtime/device/timestate");

			switch (syncState)
			{
			case "SUCCESS":				
				OBJ("st_time").innerHTML = xml.Get(rt+"/runtime/device/uptime");				
				OBJ("sync_pc_msg").innerHTML = "";
				clearTimeout(PAGE.tid1);
				this.tid2 = setTimeout('PAGE.GetSyncTimeStatus()', 1000);
				break;
			case "RUNNING":
				OBJ("sync_pc_msg").innerHTML = "<?echo I18N("h", "Synchronizing ...");?>";
				setTimeout('PAGE.GetSyncTimeStatus()', 1000);				
				break;
			default:
				break;
			}
		}
	},
	
	onClickManualSync: function()
	{
		OBJ("sync_pc_msg").innerHTML = "<?echo I18N("h", "Synchronizing ...");?>";
		this.PreClickSync();		
		var ajaxObj = GetAjaxObj("SyncPC");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function (xml) { ajaxObj.release(); PAGE.GetSyncTimeStatus(); }
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("service.cgi", "SERVICE=DEVICE.TIME&ACTION=RESTART");
	},
	
	OnClickNtpEnb: function()
	{
		this.NtpEnDiSomething();
	},

	DrawDayMenu: function()
	{
		var old_day_value = S2I(OBJ("day").value);

		var year = S2I(OBJ("year").value);
		var mon  = S2I(OBJ("month").value);
		var days = this.GetDaysInMonth(year, mon);
				
		for (var i=0;i<days;i++)
		{
			OBJ("day").options[i]=new Option(i+1, i+1);			
		}
		
		OBJ("day").length=days;

		if( days>=old_day_value ) OBJ("day").value=old_day_value;
	},
	OnChangeMonth: function()	{ this.DrawDayMenu(); },
	OnChangeYear:  function()	{ this.DrawDayMenu(); },
	DayLightTimeObj: function()
	{		
		var i, t;
		for(i=0; i<24; i++)
		{										
			if(i<12) 														
				mark="AM";			
			else if(i>=12)				
				mark="PM";			
			
			if(i<10)			
				t = "0"+i+":00:00";	
			else
				t = i+":00:00";	
			
			if(i==12 || i==0)			
				document.write("<option value=\""+t+"\">12:00 "+mark+"</option>");			
			else if(i>12)
				document.write("<option value=\""+t+"\">"+(i-12)+":00 "+mark+"</option>");
			else			
				document.write("<option value=\""+t+"\">"+i+":00 "+mark+"</option>");			
		}																		
	},
	DaylightSetEnable: function()
	{				
		if(OBJ("daylight").checked)
		{
			OBJ("daylight_offset").disabled = false;
			OBJ("daylight_sm").disabled = false;	
			OBJ("daylight_sw").disabled = false;
			OBJ("daylight_sd").disabled = false;
			OBJ("daylight_st").disabled = false;
			OBJ("daylight_em").disabled = false;	
			OBJ("daylight_ew").disabled = false;
			OBJ("daylight_ed").disabled = false;
			OBJ("daylight_et").disabled = false;							
		}
		else
		{
			OBJ("daylight_offset").disabled = true;
			OBJ("daylight_sm").disabled = true;	
			OBJ("daylight_sw").disabled = true;
			OBJ("daylight_sd").disabled = true;
			OBJ("daylight_st").disabled = true;
			OBJ("daylight_em").disabled = true;	
			OBJ("daylight_ew").disabled = true;
			OBJ("daylight_ed").disabled = true;
			OBJ("daylight_et").disabled = true;	
		}				
	},
	SelectTimeZone: function(dst_auto_flag)
	{				
		var timezonep, dst;
										
		if(dst_auto_flag)
		{
			timezonep = "/runtime/services/timezone/zone:"+OBJ("timezone").value+"/dst";
			dst = XG(this.timezone_p+timezonep);
			if(dst!="")
				{ OBJ("daylight").checked =true; }				
			else 
				{ OBJ("daylight").checked =false; }
			offsetv = "+01:00";			
		}
		else
		{			
			if(OBJ("daylight").checked == true)
			{
				timezonep = "/device/time/dstmanual";
				dst = XG(this.devtime_p+timezonep);	
				offsetp = "/device/time/dstoffset";
				offsetv = XG(this.devtime_p+offsetp);
			}
			else
			{
				dst = "";
				offsetv = "+01:00";
			}		
		}
				
		if(dst !="")
		{						
			var mystr = dst.split(",");
			var mystr2 = "";
			var i, j, k;
									
			this.SelectOption("daylight_offset", offsetv);
				
			for(i=1;i <mystr.length; i++) 
			{											
				mystr2 = mystr[i].split(".");
				for(j=0;j <mystr2.length; j++) 
				{					
					switch(j)
					{
						case 0:															
							this.SelectOption((i==1?"daylight_sm":"daylight_em"), mystr2[j].substring(1));															
							break;
						case 1:
							this.SelectOption((i==1?"daylight_sw":"daylight_ew"), mystr2[j]);
							break;			
						case 2:
							var mystr3 = mystr2[j].split("/");							
							for(k=0;k <mystr3.length; k++) 
							{																
								if(k==0)
									this.SelectOption((i==1?"daylight_sd":"daylight_ed"), mystr3[k]);
								else	
									this.SelectOption((i==1?"daylight_st":"daylight_et"), mystr3[k]);	
							}	
							break;												
					}					
				}																						
			}				
		}
		else
		{																				
			this.SelectOption("daylight_offset", offsetv);
			for(i=1; i<3; i++)
			{
				this.SelectOption((i==1?"daylight_sm":"daylight_em"), 1);	
				this.SelectOption((i==1?"daylight_sw":"daylight_ew"), 1);
				this.SelectOption((i==1?"daylight_sd":"daylight_ed"), 0);
				this.SelectOption((i==1?"daylight_st":"daylight_et"), "00:00:00");	
			}
		}
		
		this.DaylightSetEnable();				
	},
	SelectOption: function(itemid, value)
	{
		for(var i=0; i<OBJ(itemid).length; i++)
		{
			if(OBJ(itemid).options[i].value == value)
			{
				OBJ(itemid).options[i].selected=true;
				break;
			}				
		}	
	},
	OnClickAddServer: function()
	{
		var new_index=OBJ("ntp_server").length;
		var max = parseInt(XG(this.devtime_p+"/device/time/ntp_list_max"),10);

		if (!OBJ("add_ntp_server").value.length)
		{
			BODY.ShowAlert("<?echo I18N("j", "The input domain name is empty.");?>");
			OBJ("add_ntp_server").focus();
			return false;
		}
	
		if(new_index >= (max+1) )
		{
			BODY.ShowAlert("<?echo I18N("j", "The maximum number of NTP server is ");?>"+XG(this.devtime_p+"/device/time/ntp_list_max")+".");
			return;
		}
		
		if (!isDomain(OBJ("add_ntp_server").value))
		{
			BODY.ShowAlert("<?echo I18N("j", "The domain name is invalid.");?>");
			OBJ("add_ntp_server").focus();
			return false;
		}

		for(var i=1; i< OBJ("ntp_server").length; i++)
		{
			if(OBJ("ntp_server").options[i].value == OBJ("add_ntp_server").value)
			{
				BODY.ShowAlert("<?echo I18N("j", "The input domain name already exists.");?>");
				OBJ("add_ntp_server").focus();
				return false;
			}				
		}	

		var varItem = new Option(OBJ("add_ntp_server").value, OBJ("add_ntp_server").value);
		
        OBJ("ntp_server").options.add(varItem);	
		COMM_SetSelectValue(OBJ("ntp_server"), OBJ("add_ntp_server").value);
		OBJ("add_ntp_server").value="";
		OBJ("add_ntp_server").focus();
	},
	OnClickDelServer: function()
	{
		var new_index=OBJ("ntp_server").length;
		
		if( OBJ("ntp_server").selectedIndex == 0 )
		{
			BODY.ShowAlert("Select a NTP server to remove.");
			return;
		}
		
		if( OBJ("ntp_server").selectedIndex == 1 || OBJ("ntp_server").selectedIndex == 2)
		{	/* pool.ntp.org and time.nist.gov are default value */
			BODY.ShowAlert("<?echo I18N("j", "This NTP Server Cannot be removed.");?>");
			return;
		}
		OBJ("ntp_server").options.remove(OBJ("ntp_server").selectedIndex);        
		OBJ("ntp_server").selectedIndex=(OBJ("ntp_server").length-1);
		NewSelect.refresh();// Refresh the WD style select.		
	},
	GenDstManual: function()
	{				
		this.dst_manual = ",M"+OBJ("daylight_sm").value+"."+OBJ("daylight_sw").value+"."+OBJ("daylight_sd").value+"/"+OBJ("daylight_st").value;
		this.dst_manual += ",M"+OBJ("daylight_em").value+"."+OBJ("daylight_ew").value+"."+OBJ("daylight_ed").value+"/"+OBJ("daylight_et").value;
	}				
}

function isDomain(domain)
{
	var rlt = true;
	var dArray = new Array();
	if (domain.length==0)	return rlt;
	else					dArray = domain.split(".");

	/* the total length of a domain name is restricted to 255 octets or less. */
	if (domain.length > 255)
	{
		rlt = false;
	}
	for (var i=0; i<dArray.length; i++)
	{
		var reg = new RegExp("[A-Za-z0-9\-]{"+dArray[i].length+"}");
		/* the label must start with a letter */
		if (!dArray[i].match(/^[A-Za-z]/))
		{
			rlt = false;
			break;
		}
		/* the label must end with a letter or digit. */
		else if (!dArray[i].match(/[A-Za-z0-9]$/))
		{
			rlt = false;
			break;
		}
		/* the label must be 63 characters or less. */
		else if (dArray[i].length>63)
		{
			rlt = false;
			break;
		}
		/* the label has interior characters that only letters, digits and hyphen */
		else if (!reg.exec(dArray[i]))
		{
			rlt = false;
			break;
		}
	}

	return rlt;
}
</script>
