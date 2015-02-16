<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<style>
/* The CSS is only for this page.
 * Notice:
 *  If the items are few, we put them here,
 *  If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	<?
		if($layout=="router")
		{
			echo 'services: "DHCPS4.LAN-1,RUNTIME.INF.LAN-1,DEVICE.HOSTNAME,INET.LAN-1",';
		}
		else
		{
			echo 'services: "RUNTIME.INF.LAN-1,DEVICE.HOSTNAME,INET.BRIDGE-1",';
		}
	?>
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		BODY.ShowContent();
		switch (code)
		{
		case "OK":
			var model_name = "<? echo $_GLOBALS["FEATURE_MODEL_NAME"];?>";
			/* added by alex shi for WD stroage model, we need to write the hostname to orion when hostname changed */
			if (model_name == "storage" && this.orion_hostname.length)
			{
				if(this.layout=="router")
				{
					do_setting_hostname_to_orion(this.orion_hostname,this.ipdirty, OBJ("ipaddr").value);
				}
				else
				{
					do_setting_hostname_to_orion(this.orion_hostname,this.ipdirty, OBJ("ipaddr_v4").value);
				}
			}
			else
			{
				if (this.ipdirty)
				{
					if(this.layout=="router")
					{
						var ipaddr = OBJ("ipaddr").value;
					}
					else
					{
						if(OBJ("lan_type_v4").value === "lan_static")
						{
							var ipaddr = OBJ("ipaddr_v4").value;
						}
						else
						{
							var ipaddr = OBJ("device").value;
						}
					}
					Service("REBOOT", ipaddr);
				}
				else
				{
					BODY.OnReload();
				}
			}
			break;
		case "BUSY":
			BODY.ShowAlert("<?echo I18N("j", "Someone is configuring the device; please try again later.");?>");
			break;
		case "HEDWIG":
			if (result.Get("/hedwig/result")=="FAILED")
			{
				if(this.layout=="router") FocusObj(result);
				BODY.ShowAlert(result.Get("/hedwig/message"));
			}
			break;
		case "PIGWIDGEON":
			BODY.ShowAlert(result.Get("/pigwidgeon/message"));
			break;
		}
		return true;
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.InitHostname())
		{
			return false;
		}
		if(this.layout=="router")
		{
			var svc = PXML.FindModule("DHCPS4.LAN-1");
			var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
			if (!svc || !inf1p)
			{
				BODY.ShowAlert("InitDHCPS() ERROR !");
				return false;
			}
			this.dhcps4 = GPBT(svc+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
			this.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);		
			if (!this.dhcps4)
			{
				BODY.ShowAlert("InitDHCPS() ERROR !");
				return false;
			}
			if (!this.InitLAN()) return false;
		}
		else if(this.layout=="bridge")
		{
			if (!this.InitLANBr()) return false;
		}
		return true;
	},
	PreSubmit: function()
	{
		if (!this.PreHostname()) return null;
		if(this.layout=="router")
		{
			if(COMM_Equal(OBJ("ipaddr").getAttribute("modified"), "true") || COMM_Equal(OBJ("netmask").getAttribute("modified"), "true") || COMM_Equal(OBJ("dnsr").getAttribute("modified"), "true"))
			{
				PXML.IgnoreModule("INET.BRIDGE-1");
				if (!this.PreLAN()) return null;
			}	
		}
		else if(this.layout=="bridge")
		{
			if(COMM_Equal(OBJ("ipaddr_v4").getAttribute("modified"), "true") || 
				COMM_Equal(OBJ("netmask_v4").getAttribute("modified"), "true") ||
				COMM_Equal(OBJ("gateway_v4").getAttribute("modified"), "true") ||
				COMM_Equal(OBJ("dns1_v4").getAttribute("modified"), "true") ||
				COMM_Equal(OBJ("dns2_v4").getAttribute("modified"), "true") ||
				COMM_Equal(OBJ("lan_type_v4").getAttribute("modified"), "true")
				)
			{				
				PXML.IgnoreModule("INET.LAN-1");
				if (!this.PreLANBr()) return null;
			}
		}
		//PXML.CheckModule("DHCPS4.LAN-1","ignore",null,null);
		PXML.CheckModule("RUNTIME.INF.LAN-1","ignore","ignore","ignore");
		return PXML.doc;
	},	
	IsDirty: function() {},
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	lanip: null,
	inetp: null,
	dhcps4: null,
	leasep: null,
	mask: null,
	ipdirty: false,
	layout: "<? echo $layout;?>",
	orion_hostname: new String(), /* added by alex shi for WD stroage model, we need to write the hostname to orion when hostname changed */	
	InitHostname: function()
	{
		var b = PXML.FindModule("DEVICE.HOSTNAME");
		if (!b)
		{
			BODY.ShowAlert("<?echo I18N("j", "InitHostname() ERROR!!!");?>");
			return false;
		}

		OBJ("device").value = XG(b+"/device/hostname");
		if(XG(b+"/wd/storage/master")=="1")
			OBJ("locm").checked = true;
		else
			OBJ("locm").checked = false;
		return true;
	},
	PreHostname: function()
	{
		var b = PXML.FindModule("DEVICE.HOSTNAME");
		XS(b+"/wd/storage/master", OBJ("locm").checked==true ? "1" : "0");
		if (COMM_Equal(OBJ("device").getAttribute("modified"), "true"))
		{
			if(this.IsNumber(OBJ("device").value))
			{
				BODY.ShowAlert('<?echo I18N("j", "Host name format error; must have alpha characters.");?>');
				OBJ("device").focus();
				return false;
			}
			if(this.IsInValidChar(OBJ("device").value))
			{
				BODY.ShowAlert('<?echo I18N("j", "Invalid host name format. Host name must be alphanumeric (a-z, A~Z, 0~9) and can not contain special characters or space.");?>');
				OBJ("device").focus();
				return false;
			}
			if(OBJ("device").value.match(" ") !== null)
			{
				BODY.ShowAlert('<?echo I18N("j", "Host name format error; can not contain space.");?>');
				OBJ("device").focus();
				return false;
			}			
			/* added by alex shi for WD stroage model, we need to write the hostname to orion when hostname changed */
			this.orion_hostname = OBJ("device").value;	
			XS(b+"/device/hostname", OBJ("device").value);
			PXML.ActiveModule("DEVICE.HOSTNAME");
		}
		else
		{
			PXML.CheckModule("DEVICE.HOSTNAME", null, null, "ignore");
		}
		return true;
	},
	InitLAN: function()
	{
		var lan	= PXML.FindModule("INET.LAN-1");
		var inetuid = XG(lan+"/inf/inet");
		this.inetp = GPBT(lan+"/inet", "entry", "uid", inetuid, false);
		if (!this.inetp)
		{
			BODY.ShowAlert("InitLAN() ERROR!!!");
			return false;
		}

		if (XG(this.inetp+"/addrtype") == "ipv4")
		{
			var b = this.inetp+"/ipv4";
			this.lanip = XG(b+"/ipaddr");
			this.mask = XG(b+"/mask");
			OBJ("ipaddr").value	= this.lanip;
			OBJ("netmask").value= COMM_IPv4INT2MASK(this.mask);
			OBJ("dnsr").checked	= XG(lan+"/inf/dns4")!="" ? true : false;
		}
		
		return true;
	},
	PreLAN: function()
	{
		var dhcp_networkid;
		var dhcp_max_count,dhcp_star,dhcp_end,dhcp_host;
		var lan = PXML.FindModule("INET.LAN-1");
		var b = this.inetp+"/ipv4";

		var vals = OBJ("ipaddr").value.split(".");
		if (vals.length!=4)
		{
			BODY.ShowAlert("<?echo I18N("j", "Invalid IP address");?>");
			OBJ("ipaddr").focus();
			return false;
		}
		for (var i=0; i<4; i++)
		{
			if (!TEMP_IsDigit(vals[i]) || vals[i]>255)
			{
				BODY.ShowAlert("<?echo I18N("j", "Invalid IP address");?>");
				OBJ("ipaddr").focus();
				return false;
			}
		}
		if (confirm("<?echo I18N("j", "After applying updates, the router will reboot. Are you sure?");?>"))
		{
			//if(this.mask!=COMM_IPv4MASK2INT(OBJ("netmask").value) && COMM_IPv4MASK2INT(OBJ("netmask").value)>=24)
			//{
				dhcp_networkid=COMM_IPv4NETWORK(OBJ("ipaddr").value,COMM_IPv4MASK2INT(OBJ("netmask").value));
				dhcp_max_count=COMM_IPv4MAXHOST(COMM_IPv4MASK2INT(OBJ("netmask").value));
				dhcp_host=COMM_IPv4HOST(OBJ("ipaddr").value,COMM_IPv4MASK2INT(OBJ("netmask").value));
				dhcp_star=COMM_IPv4HOST(dhcp_networkid,COMM_IPv4MASK2INT(OBJ("netmask").value))+1;
				dhcp_end=COMM_IPv4HOST(dhcp_networkid,COMM_IPv4MASK2INT(OBJ("netmask").value))+dhcp_max_count-1;
				var svc = PXML.FindModule("DHCPS4.LAN-1");
	
				XS(svc+"/jump",1);
				//if(dhcp_host-dhcp_star > dhcp_end-dhcp_host)
				//{
				//		XS(this.dhcps4+"/start",dhcp_star);
				//		XS(this.dhcps4+"/end",dhcp_host-1);	
				//}
				//else
				//{
				//		XS(this.dhcps4+"/start",dhcp_host+1);
				//		XS(this.dhcps4+"/end",dhcp_end);	
				//}
				
				if(dhcp_host == dhcp_star)
				{
					XS(this.dhcps4+"/start",dhcp_star+1);
					XS(this.dhcps4+"/end",dhcp_end);	
				}
				else if(dhcp_host == dhcp_end)
				{	
					XS(this.dhcps4+"/start",dhcp_star);
					XS(this.dhcps4+"/end",dhcp_end-1);
				}
				else
				{
					XS(this.dhcps4+"/start",dhcp_star);
					XS(this.dhcps4+"/end",dhcp_end);	
				}
			//}
			//else
			//{
			//	var lan_host_dot4=COMM_IPv4HOST(OBJ("ipaddr").value,24);
			//	
			//	if(lan_host_dot4 < 100 || 149 < lan_host_dot4)
			//	{
			//		XS(this.dhcps4+"/start",100);
			//		XS(this.dhcps4+"/end",149);
			//	}
			//	else
			//	{
			//		XS(this.dhcps4+"/start",200);
			//		XS(this.dhcps4+"/end",249);					
			//	}							
			//}		
			this.mask = COMM_IPv4MASK2INT(OBJ("netmask").value);
			XS(b+"/ipaddr", OBJ("ipaddr").value);
			XS(b+"/mask", this.mask);
			if (OBJ("dnsr").checked)	XS(lan+"/inf/dns4", "DNS4-1");
			else						XS(lan+"/inf/dns4", "");

			if (COMM_EqBOOL(OBJ("ipaddr").getAttribute("modified"), true)||
					COMM_EqBOOL(OBJ("netmask").getAttribute("modified"), true)||
					COMM_Equal(OBJ("dnsr").getAttribute("modified"), true))
			{
				this.ipdirty = true;
			}		
			
			if (this.ipdirty)
			{
				PXML.DelayActiveModule("INET.LAN-1", "3");
			}
			else
			{
				PXML.IgnoreModule("INET.LAN-1");
			}
			return true;
		}
		else
		{
			return false;
		}
	},
	InitLANBr: function()
	{		
		var p = PXML.FindModule("INET.BRIDGE-1");
		var inf_ipv4 = GPBT(p, "inf", "uid", "BRIDGE-1", false);
		var inet_ipv4 = XG(inf_ipv4+"/inet");
		this.inetp_v4 = GPBT(p+"/inet", "entry", "uid", inet_ipv4, false);
		if(XG(this.inetp_v4+"/ipv4/static") === "1")
		{
			OBJ("lan_type_v4").value	= "lan_static";
			OBJ("ipaddr_v4").value 		= XG(this.inetp_v4+"/ipv4/ipaddr");
			OBJ("netmask_v4").value 	= COMM_IPv4INT2MASK(XG(this.inetp_v4+"/ipv4/mask"));
			OBJ("gateway_v4").value 	= XG(this.inetp_v4+"/ipv4/gateway");
			var cnt = XG(this.inetp_v4+"/ipv4/dns/count");
			OBJ("dns1_v4").value		= cnt > 0 ? XG(this.inetp_v4+"/ipv4/dns/entry:1") : "";
			OBJ("dns2_v4").value		= cnt > 1 ? XG(this.inetp_v4+"/ipv4/dns/entry:2") : "";
			OBJ("ipv4_conn_type").style.display	= "block";
		}
		else
		{
			OBJ("lan_type_v4").value = "lan_dynamic";
			OBJ("ipv4_conn_type").style.display	= "none"; 
		}			 
		
		return true;
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
				PXML.DelayActiveModule("INET.BRIDGE-1", "1");//Run LAN service after the result of Pigwidgeon is come back. 
			}
		}
		else
		{
			XD(this.inetp_v4+"/ipv4");
			XS(this.inetp_v4+"/ipv4/static", "0");
			XS(this.inetp_v4+"/ipv4/mtu", 	"1500");
			this.ipdirty = true;
			PXML.DelayActiveModule("INET.BRIDGE-1", "1");//Run LAN service after the result of Pigwidgeon is come back. 
		}
		
		return true;
	},	
	OnChangeLANType: function(lantype)
	{
		if(lantype === "lan_static")	OBJ("ipv4_conn_type").style.display	= "block";
		else	OBJ("ipv4_conn_type").style.display	= "none";		
	},	
	IsInValidChar: function(str)
	{
		var character_set = "`~!@#$%^&*()=+_[]{}\|;:.¡¦¡¨,<>/?";
		var c;
		for (i = 0; i < str.length; i++)
		{
			c= str.charAt(i);
			if(character_set.indexOf(c) == -1)
			{
				return false;
			}
		}
		return true;
	},
	IsNumber: function(str)
	{
	   var num = "0123456789";
	   var isnum=true;
	   var c;
		 
	   for (i = 0; i < str.length; i++) 
	   { 	   
	      c = str.charAt(i); 
	      if (num.indexOf(c) == -1) 
	      {	         	
	         	isnum = false;
	         	return isnum;
	      }
	   }
	   return isnum;
   }
}

function FocusObj(result)
{
	var found = true;
	var node = result.Get("/hedwig/node");
	var nArray = node.split("/");
	var len = nArray.length;
	var name = nArray[len-1];
	if (node.match("inet"))
	{
		switch (name)
		{
		case "ipaddr":
			OBJ("ipaddr").focus();
			break;
		case "mask":
			OBJ("netmask").focus();
			break;
		default:
			found = false;
			break;
		}
	}
	else
	{
		found = false;
	}

	return found;
}

function SetDelayTime(millis)
{
	var date = new Date();
	var curDate = null;
	curDate = new Date();
	do { curDate = new Date(); }
	while(curDate-date < millis);
}
function GetMAC(m)
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
		myMAC = myMAC + ':' + tmp[i];
	}
	return myMAC;
}

function Service(svc, ipaddr)
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
	var msgArray = ["<?echo I18N("h", "If you changed the IP address of the router, you may need to renew the IP address of your device before accessing the router web page again.");?>"];
	var delay = 10;
	var sec = <?echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");
	if (svc=="FRESET")		url = "http://192.168.1.1/index.php";
	else if (svc=="REBOOT")	url = "http://"+ipaddr+"/index.php";
	else					return false;
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
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
}

/* added by alex shi for WD stroage model, we need to write the hostname to orion when hostname changed */
function do_setting_hostname_to_orion(hostname, ipdirty, ipaddr)
{
	var ajaxObj = GetAjaxObj("SERVICE");
	ajaxObj.requestMethod = "GET";
	ajaxObj.createRequest();
	ajaxObj.onError = function(msg)
	{
		ajaxObj.release();
		if (ipdirty)
		{
			Service("REBOOT", ipaddr);
		}
		else
		{
			BODY.OnReload();
		}
	}
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
        if (ipdirty)
        {
            Service("REBOOT", ipaddr);
        }
        else
        {
            BODY.OnReload();
        }
	}

	var option_context = new String();
	option_context += "/api/1.0/rest/device?owner=admin&pw=&name=" + hostname + "&rest_method=PUT";
	ajaxObj.sendRequest(option_context, "");
}

</script>
