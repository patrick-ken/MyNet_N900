<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DEVICE.LAYOUT,INET.LAN-1,INET.BRIDGE-1",
	OnLoad: function()
	{
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result) 
	{
		switch (code)
		{
		case "OK":
			Reboot();
			break;
		case "BUSY":
			BODY.ShowAlert("<?echo I18N("j", "Someone is configuring the device; please try again later.");?>");
			break;
		case "HEDWIG":
			if (result.Get("/hedwig/result")=="FAILED")
			{
				BODY.ShowAlert(result.Get("/hedwig/message"));
				BODY.ShowContent();
			}
			break;
		case "PIGWIDGEON":
			BODY.ShowAlert(result.Get("/pigwidgeon/message"));
			break;
		}
		return true;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	InitValue: function(xml)
	{
		PXML.doc = xml;		
		this.layout_module 			= PXML.FindModule("DEVICE.LAYOUT");
		this.lan					= PXML.FindModule("INET.LAN-1");
		var layout_module 			= this.layout_module;
		if (!layout_module)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		var layout = XG(layout_module + "/device/layout");
		COMM_SetSelectValue(OBJ("dev_mode"), layout);
		if(OBJ("dev_mode").value=="bridge") OBJ("ap_lan_ipv4_settings").style.display = "";
		if (!this.InitLANBr()) return false;
		this.OnChangeLayout(OBJ("dev_mode").value);
		return true;
	},
	InitLANBr: function()
	{		
		var p = PXML.FindModule("INET.BRIDGE-1");
		var inf_ipv4 = GPBT(p, "inf", "uid", "BRIDGE-1", false);
		var inet_ipv4 = XG(inf_ipv4+"/inet");
		this.inetp_v4 = GPBT(p+"/inet", "entry", "uid", inet_ipv4, false);
		if(XG(this.inetp_v4+"/ipv4/static") === "1")
		{
			COMM_SetSelectValue(OBJ("lan_type_v4"), "lan_static");
			OBJ("ipaddr_v4").value 		= XG(this.inetp_v4+"/ipv4/ipaddr");
			OBJ("netmask_v4").value 	= COMM_IPv4INT2MASK(XG(this.inetp_v4+"/ipv4/mask"));
			OBJ("gateway_v4").value 	= XG(this.inetp_v4+"/ipv4/gateway");
			var cnt = XG(this.inetp_v4+"/ipv4/dns/count");
			OBJ("dns1_v4").value		= cnt > 0 ? XG(this.inetp_v4+"/ipv4/dns/entry:1") : "";
			OBJ("dns2_v4").value		= cnt > 1 ? XG(this.inetp_v4+"/ipv4/dns/entry:2") : "";
			OBJ("ipv4_conn_type").style.display	= "";
		}
		else
		{
			COMM_SetSelectValue(OBJ("lan_type_v4"), "lan_dynamic");
			OBJ("ipv4_conn_type").style.display	= "none"; 
		}			 
		
		return true;
	},
	msgArr:
	[
		"<?echo I18N('h', 'The router is being switched to AP (access point) mode. In order to work as an AP, connect this router\'s WAN port (yellow colored and labeled Internet) to existing network\'s live Ethernet LAN line.');?>",
		"<input type='button' class='button_black' id='reload' onclick='BODY.ShowContent();' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='BODY.OnSubmit();' value='<?echo I18N('h', 'Save');?>'>"
	],	
	BeforeOnSubmit: function()
	{
		if(OBJ("dev_mode").value=="bridge")
		{
			BODY.ShowMessage("<?echo I18N('h', 'Switching to AP mode'); ?>",PAGE.msgArr);
			return null;
		}
		else
		{
			BODY.OnSubmit();
		}
	},
	PreSubmit: function()
	{
		var layout_module 	= this.layout_module;
		XS(layout_module+"/device/layout", OBJ("dev_mode").value);
		if(OBJ("dev_mode").value=="bridge")
		{
			PXML.IgnoreModule("INET.LAN-1");
			PXML.CheckModule("DEVICE.LAYOUT", null, null, "ignore"); 
			PXML.CheckModule("INET.BRIDGE-1", null, null, "ignore"); 
			this.PreLANBr();
		}
		else
		{
			PXML.IgnoreModule("INET.BRIDGE-1");
			PXML.CheckModule("DEVICE.LAYOUT", null, null, "ignore"); 
			PXML.CheckModule("INET.LAN-1", null, null, "ignore");			
		}		
		//return null;
		return PXML.doc;
	},
	PreLANBr: function()
	{
		if(OBJ("lan_type_v4").value === "lan_static")	
		{
			XS(this.inetp_v4+"/ipv4/static", 		"1");
			XS(this.inetp_v4+"/ipv4/ipaddr", OBJ("ipaddr_v4").value);
			XS(this.inetp_v4+"/ipv4/mask", 	COMM_IPv4MASK2INT(OBJ("netmask_v4").value));
			XS(this.inetp_v4+"/ipv4/gateway",OBJ("gateway_v4").value);
								
			var cnt = 0;
			if (OBJ("dns1_v4").value !== "")
			{
				XS(this.inetp_v4+"/ipv4/dns/entry:1", OBJ("dns1_v4").value);
				cnt+=1;
				if (OBJ("dns2_v4").value !== "")
				{
					XS(this.inetp_v4+"/ipv4/dns/entry:2",OBJ("dns2_v4").value);
					cnt+=1;
				}
			}
			XS(this.inetp_v4+"/ipv4/dns/count", cnt);
			if(COMM_EqBOOL(OBJ("ipaddr_v4").getAttribute("modified"), true))	
			{
				this.ipdirty = true;
				//PXML.DelayActiveModule("INET.BRIDGE-1", "1");//Run LAN service after the result of Pigwidgeon is come back. 
			}
		}
		else
		{
			XD(this.inetp_v4+"/ipv4");
			XS(this.inetp_v4+"/ipv4/static", "0");
			XS(this.inetp_v4+"/ipv4/mtu", 	"1500");
		}
		
		return true;
	},	
	GetLanIP: function()
	{
		var lan			= this.lan;
		var inetuid = XG(lan+"/inf/inet");
		var inetp 	= GPBT(lan+"/inet", "entry", "uid", inetuid, false);	
		if (XG(inetp+"/addrtype") == "ipv4")
		{
			if(OBJ("dev_mode").value == "router")
			{
				var b = inetp+"/ipv4";
				var lanip = XG(b+"/ipaddr");
			}
			else
			{
				var p = PXML.FindModule("INET.BRIDGE-1");
				var inf_ipv4 = GPBT(p, "inf", "uid", "BRIDGE-1", false);
				var inet_ipv4 = XG(inf_ipv4+"/inet");
				inetp_v4 = GPBT(p+"/inet", "entry", "uid", inet_ipv4, false);
				var lanip=XG(inetp_v4+"/ipv4/ipalias/ipaddr");
			}

			return lanip;
		} else
			return null;
	},
	OnChangeLayout: function(layout)
	{
		if(OBJ("dev_mode").value === "router") OBJ("ap_lan_ipv4_settings").style.display = "none";
		else OBJ("ap_lan_ipv4_settings").style.display	= "";
	},
	OnChangeLANType: function(lantype)
	{
		if(lantype === "lan_static") OBJ("ipv4_conn_type").style.display = "";
		else OBJ("ipv4_conn_type").style.display = "none";		
	}		
};

function Reboot()
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
	var msgArray = ["<?echo I18N("h", "Device is switching ... please wait .");?>"];
	var delay = 20;
	var sec = <?echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ip = PAGE.GetLanIP();
	if( ip != null)
		url = "http://"+ip+"/index.php";
	if(OBJ("dev_mode").value == "bridge") 
		url = "http://wdap";//If the LAN type is DHCP, we may not know the new router IP and subnet mask.	
	var ajaxObj = GetAjaxObj("Reboot");
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK")
			BODY.ShowAlert("Internal ERROR!\nEVENT "+svc+": "+xml.Get("/report/message"));
		else
			BODY.ShowCountdown(banner, msgArray, sec, url);
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT=REBOOT");
}
</script>
