<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
.value2
{
    color: #FFFFFF;
    font-family: Arial;
    font-size: 14px;
    text-align: right;
    width: 148px;
}
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "WIFI.PHYINF,DEVICE.ACCOUNT,INET.WAN-1,RUNTIME.INF.WAN-1,RUNTIME.PHYINF,DHCPS4.LAN-1,DHCPS4.LAN-2,RUNTIME.INF.LAN-1,RUNTIME.INF.LAN-2,PARENTCTRL,STORAGE,AUFOURA83",
	
	OnLoad: function() 
	{
        /*
        var device_mode = '<? echo $layout;?>';
        if(device_mode == "router")
		{
			if(!this.InitRemoteAccess()) return false;
		}
        */
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		
		this.dual_band = COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>');
		if(!this.InitWLAN("BAND24G-1.1","WIFI.PHYINF")) return false;
		if(this.dual_band)
			if(!this.InitWLAN("BAND5G-1.1","WIFI.PHYINF")) return false; 				
		if(!this.InitAccount("DEVICE.ACCOUNT")) return false;
		if(!this.InitWAN()) return false;
		if(!this.InitDeviceNumber()) return false;
		if(!this.InitParentalControl()) return false;
		if(!this.InitAttachedStorage()) return false;
		if(!this.InitDeviceMode()) return false;
		
		var device_mode = '<? echo $layout;?>';
		if(device_mode == "router")
		{
			if(!this.InitRemoteAccess()) return false;
		}
		
		return true;
	},
	PreSubmit: function()
	{
		return PXML.doc;
	},
	IsDirty: null,
	
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	SaveXML: function(wlan_phyinf , wifi_phyinf)
	{
		//XS(this.phyinf+"/dot11n/bw2040coexist",		SetBNode(OBJ("coexist_enable"+str_Aband).checked));
		return true;
	},

	GetSecKey: function(wifip)
	{
		var auth 		= XG(wifip+"/authtype");
		var cipher 		= XG(wifip+"/encrtype");
		var sec_type 	= null;

		switch (auth)
		{
			case "WPA":
			case "WPA2":
			case "WPA+2":
			case "WPAEAP":
			case "WPA+2EAP":			
			case "WPA2EAP":
				sec_type = "wpa_enterprise";
				wpa_mode = auth;
				break;
			case "WPAPSK":
				sec_type = "wpa_personal";
				wpa_mode = "WPA";
				break;
			case "WPA2PSK":
				sec_type = "wpa_personal";
				wpa_mode = "WPA2";				
				break;
			case "WPA+2PSK":
				sec_type = "wpa_personal";
				wpa_mode = "WPA+2";
				break;
			default:
				sec_type = "";
				wpa_mode = "WPA+2";
		}
		
		if (cipher=="WEP")
			return	XG(wifip+"/nwkey/wep/key:1");
		else if(sec_type != "" )
		{
			if(sec_type == "wpa_enterprise")
			{
				return	XG(wifip+"/nwkey/eap/secret");
			}
			else
			{
				return	XG(wifip+"/nwkey/psk/key");
			}
		}
		else
			return	"(<? echo I18N("h", "None");?>)";
		return true;
	},	
	InitAccount: function ( acc_module )
	{
		this.actp 	= PXML.FindModule(acc_module);
		if (!this.actp)
		{
			BODY.ShowAlert("Initial(InitAccount) ERROR!!!");
			return false;
		}
		OBJ("gw_name").innerHTML = XG(this.actp + "/device/hostname");
		return true;
	},
	no_usb2 : <? if (query("/runtime/wd/USB1/entry#") == "") {echo "0";} else {echo query("/runtime/wd/USB1/entry#");} ?>,
	InitDeviceMode: function ()/* For bridge mode 20120127 Daniel Chen */
	{
		var device_mode = '<? echo $layout;?>';
		if(device_mode != "router")
		{
            OBJ("u_status_1_bt").style.display = "none";
            OBJ("u_status_2_bt").style.display = "none";
			OBJ("u_status_3_bt").style.display = "none";
			OBJ("u_status_4_bt").style.display = "none";
			OBJ("u_status_1").innerHTML="";
			OBJ("u_st_networkstatus").innerHTML="";
			OBJ("u_status_2").innerHTML=""; 
			OBJ("u_device_number").innerHTML="";
			OBJ("u_status_3").innerHTML = "";
			OBJ("u_status_4").innerHTML = "";
			OBJ("u_gz_ssid").innerHTML  = "";
			OBJ("u_gz_key").innerHTML   = "";
			OBJ("hr_1").innerHTML   = "";
			OBJ("u_hr_2").innerHTML   = "";
			OBJ("u_hr_3").innerHTML   = "";
			OBJ("u_ra_0").innerHTML   = "";
			OBJ("u_ra_1").innerHTML   = "";
			OBJ("u_ra_2").innerHTML   = "";
			OBJ("u_ra_3").innerHTML   = "";
			OBJ("u_ra_4").innerHTML   = "";
			OBJ("u_ra_5").innerHTML   = "";
			OBJ("u_ra_6").innerHTML   = "";
		}
		return true;
	},
	InitWAN: function ()
	{
		var wan		= PXML.FindModule("INET.WAN-1");
		var rwan 	= PXML.FindModule("RUNTIME.INF.WAN-1");
		var rphy 	= PXML.FindModule("RUNTIME.PHYINF");
		var waninetuid 	= XG  (wan+"/inf/inet");
		var wanphyuid 	= XG  (wan+"/inf/phyinf");
		this.waninetp 	= GPBT(wan+"/inet", "entry", "uid", waninetuid, false);
		this.rwaninetp 	= GPBT(rwan+"/runtime/inf", "inet", "uid", waninetuid, false);      
		this.rwanphyp 	= GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);     
		var str_Connected = "<?echo I18N("h", "Connected");?>";
		var str_networkstatus = str_Disconnected = "<?echo I18N("h", "Disconnected");?>";

        var wancable_status=0;
		var wan_network_status=0;

		if ((!this.waninetp))
		{
			BODY.ShowAlert("InitWAN() ERROR!!!");
			return false;
		}

        if((XG(this.rwanphyp+"/linkstatus")!="0") && (XG  (this.rwanphyp+"/linkstatus")!=""))
		   wancable_status=1;

		if (XG  (this.waninetp+"/addrtype") == "ipv4")
		{
			if(XG  ( this.waninetp+"/ipv4/static")== "1")
			{
		    		str_networkstatus  = wancable_status== 1 ? str_Connected:str_Disconnected;
		    		wan_network_status = wancable_status;
			}
			else
			{
		   		//DHCP Client
				if ((XG  (this.rwaninetp+"/ipv4/valid")== "1")&& (wancable_status==1))
				{
					wan_network_status=1;
					str_networkstatus = str_Connected;
				}
			}
		}
		else if (XG  (this.waninetp+"/addrtype") == "ppp4")
		{
			var connStat = XG(rwan+"/runtime/inf/pppd/status");    
			    
			if ((XG  (this.rwaninetp+"/ppp4/valid")== "1")&& (wancable_status==1))
				wan_network_status=1;

		    switch (connStat)
            {
                case "connected":
	        		if (wan_network_status == 1)	str_networkstatus=str_Connected;
		            else			                str_networkstatus=str_Disconnected;
		            break;
	            case "":
                case "disconnected":
                	str_networkstatus=str_Disconnected;
	            	break;
                case "on demand":
	                str_networkstatus="<?echo I18N("h", "Idle");?>";
	            	break;
                default:
	                str_networkstatus = "<?echo I18N("h", "Busy ...");?>";
	                break;
			}
		}
		if (!PAGE.no_usb2)
		{
			OBJ("d_st_networkstatus").innerHTML = str_networkstatus ;
		}
		else
		{
			OBJ("u_st_networkstatus").innerHTML = str_networkstatus ;
		}
		return true;
	},
	InitDeviceNumber: function ()
	{
		var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
		var inf2p = PXML.FindModule("RUNTIME.INF.LAN-2");
		
		if (!inf1p || !inf2p)
		{
			BODY.ShowAlert("Initial(InitDeviceNumber) ERROR!!!");
			return false;
		}
		
		this.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);
		this.leasep2 = GPBT(inf2p+"/runtime", "inf", "uid", "LAN-2", false);
		
		this.leasep += "/dhcps4/leases";
		this.leasep2 += "/dhcps4/leases";
		
		if (!this.leasep)	return true;	// in bridge mode, the value of this.leasep is null.
		
		var entry = this.leasep+"/entry";
		var cnt = XG(entry+"#");
		
		var entry_gz = this.leasep2+"/entry";
		var cnt_gz = XG(entry_gz+"#");
        var total_cnt=cnt+cnt_gz;
		
		if (!PAGE.no_usb2)
		{	
			OBJ("d_device_number").innerHTML = total_cnt;
		}
		else
		{
			OBJ("u_device_number").innerHTML = total_cnt;
		}
		return true;
	},
	InitAttachedStorage: function ()
	{
		var p = PXML.FindModule("STORAGE");
		if (!p)
		{
			BODY.ShowAlert("Initial(InitAttachedStorage) ERROR!!!");
			return false;
		}
		var s1 = p + "/runtime/wd/USB1";
		var s2 = p + "/runtime/wd/USB2";
		var cnt_1 = S2I(XG(s1+"/entry#"));
		var cnt_2 = S2I(XG(s2+"/entry#"));

		var temp_port="usb_port_1";
		var temp_status="usb_status_1";
		var temp_link="usb_link_1";
		
		if(cnt_1+cnt_2 == 0)
		{
			OBJ("usb_port_1").innerHTML = "<? echo I18N("h", "Status"); ?>";
			OBJ("usb_status_1").innerHTML = "<? echo I18N("h", "No Drive Attached"); ?>";
			OBJ("usb_port_2").innerHTML = '';
			OBJ("usb_status_2").innerHTML = '';
			OBJ("usb_link_2").innerHTML = '';
			return true;
		}

		if(cnt_1)
		{
			OBJ(temp_port).innerHTML = "<?echo I18N("h", "USB Port1");?>";
			OBJ(temp_status).innerHTML = "<?echo I18N("h", "Attached");?>";
			temp_port="usb_port_2";
			temp_status="usb_status_2";
			temp_link="usb_link_2";
		}
		if(cnt_2)
		{
			if("<? echo $FEATURE_MODEL_NAME;?>" == "storage")
			{
				OBJ(temp_port).innerHTML = "<?echo I18N("h", "Internal Hard Drive");?>";
			}
			else
			{
				OBJ(temp_port).innerHTML = "<?echo I18N("h", "USB Port2");?>";
			}
			OBJ(temp_status).innerHTML = "<?echo I18N("h", "Attached");?>";
		}
		else
		{
			OBJ(temp_link).innerHTML = '';
		}
		return true;
	},
	InitParentalControl: function ()
	{
		this.parent_ctrl = PXML.FindModule("PARENTCTRL");
		if (!this.parent_ctrl)
		{
			BODY.ShowAlert("Initial(InitParentalControl) ERROR!!!");
			return false;
		}
		OBJ("parental_control_value").innerHTML = XG(this.parent_ctrl+"/security/active")=="1" ? "<? echo I18N("h", "<Enabled>"); ?>" : "<? echo I18N("h", "<Disabled>"); ?>";
		return true;
	},
	OnClickAdvPage: function(str)
	{
		self.location.href='/'+str+'.php';
	},
	guest_zone: 0,
	InitWLAN: function ( wlan_uid, wifi_module )
	{
		this.wifi_module = PXML.FindModule(wifi_module);
		if (!this.wifi_module)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.phyinf = GPBT(this.wifi_module, "phyinf", "uid",wlan_uid, false);

		var wifi_profile = XG(this.phyinf+"/wifi");
		var freq = XG(this.phyinf+"/media/freq");
		this.wifip = GPBT(this.wifi_module+"/wifi", "entry", "uid", wifi_profile, false);
		
		if(freq == "5") 	str_Aband = "_Aband";
		else				str_Aband = "";
	
        if(COMM_ToBOOL(XG(this.phyinf+"/active"))===true)
        {
            OBJ("ssid"+str_Aband).innerHTML = control_str_len(XG(this.wifip+"/ssid"));
            OBJ("key"+str_Aband).innerHTML = control_str_len(this.GetSecKey(this.wifip));
        }
        else
        {
            OBJ("ssid"+str_Aband).innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
            OBJ("key"+str_Aband).innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
			if (!PAGE.no_usb2)
			{
				OBJ("d_gz_ssid").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
				OBJ("d_gz_key").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
			}
			else
			{
                OBJ("u_gz_ssid").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
                OBJ("u_gz_key").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
			}
			return true;
        }
		//guestzone info
		if(wlan_uid=="BAND24G-1.1") 		gz_uid = "BAND24G-1.2"
		else 								gz_uid = "BAND5G-1.2"
			
		this.phyinf 	= GPBT(this.wifi_module, "phyinf", "uid", gz_uid, false);
		wifi_profile 	= XG(this.phyinf+"/wifi");
		wifi_path 		= GPBT(this.wifi_module+"/wifi", "entry", "uid", wifi_profile, false);
		if (COMM_ToBOOL(XG(this.phyinf+"/active")))
		{
			if (!this.guest_zone)
			{
				this.guest_zone = 1;
				if (!PAGE.no_usb2)
				{
					OBJ("d_gz_ssid").innerHTML = control_str_len(XG(wifi_path+"/ssid")); 
					OBJ("d_gz_key").innerHTML = control_str_len(this.GetSecKey(wifi_path)); 
				}
				else
				{
                    OBJ("u_gz_ssid").innerHTML = control_str_len(XG(wifi_path+"/ssid"));
                    OBJ("u_gz_key").innerHTML = control_str_len(this.GetSecKey(wifi_path));
				}
			}
		}
		else
		{
			if (!this.guest_zone)
			{
				if (!PAGE.no_usb2)
				{
					OBJ("d_gz_ssid").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
					OBJ("d_gz_key").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
				}
				else
				{
                    OBJ("u_gz_ssid").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
                    OBJ("u_gz_key").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
				}
			}
		}

		return true;
	},
    InitRemoteAccess: function()
    {
		var aufoura = PXML.FindModule("AUFOURA83");
		var aufourauid = XG(aufoura+"/text");
		get_remote_access_status(aufourauid);
    },
    OnClickLogout: function()
    {
        if (confirm("<?echo I18N("j", "Are you sure you want to logout?");?>"))
        {
            BODY.Logout();
        }
    }
}

function SetBNode(value)
{
	if (COMM_ToBOOL(value))
		return "1";
	else
		return "0";
}

function control_str_len(str)
{
	if(str.length >28)
	{
		str = str.substr(0,25)+"...";
	}
	str = COMM_EscapeHTMLSC(str);
	return str;
}

function GetRadioValue(name)
{
	var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		if (obj[i].checked)	return obj[i].value;
	}
}
function SetRadioValue(name, value)
{
	var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		if (obj[i].value==value)
		{
			obj[i].checked = true;
			break;
		}
	}
}

function get_remote_access_status(text)
{
    var ajaxObj = GetAjaxObj("raccess");
    ajaxObj.createRequest();
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
		if (!PAGE.no_usb2)
		{
			OBJ("d_remote_access_status").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
			OBJ("d_mobile_device_number").innerHTML = "0";
			OBJ("d_web_account_number").innerHTML = "0";
		}
		else
		{
            OBJ("u_remote_access_status").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
            OBJ("u_mobile_device_number").innerHTML = "0";
            OBJ("u_web_account_number").innerHTML = "0";
		}
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();
        if (xml.Get("/device/remote_access") == "true")
        {
			if (!PAGE.no_usb2)
			{
            	OBJ("d_remote_access_status").innerHTML = "<? echo I18N("h", "Ready"); ?>";
			}
			else
			{
				OBJ("u_remote_access_status").innerHTML = "<? echo I18N("h", "Ready"); ?>";
			}
        }
        else
        {
			if (!PAGE.no_usb2)
			{
            	OBJ("d_remote_access_status").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
        }
        else
        {
				OBJ("u_remote_access_status").innerHTML = "<? echo I18N("h", "<Disabled>"); ?>";
			}
        }
		checkDevicesNumber(text);
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+text);
}

function checkDevicesNumber(text)
{
    var ajaxObj = GetAjaxObj("raccess");
    ajaxObj.createRequest();
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
		if (!PAGE.no_usb2)
		{
			OBJ("d_mobile_device_number").innerHTML = "0";
			OBJ("d_web_account_number").innerHTML = "0";
		}
		else
		{
            OBJ("u_mobile_device_number").innerHTML = "0";
            OBJ("u_web_account_number").innerHTML = "0";			
		}
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();
        var cnt = xml.Get("/device_users/device_user#");
        var mal = new String();
		var mobile_devices = 0;
		var web_accounts = 0;
        for (var i = 1; i <= cnt; i++)
        {
            var dac = new String(xml.Get("/device_users/device_user:"+i+"/dac"));
            if (dac.length != 0)
            {
				mobile_devices++;
			}
			var email = new String(xml.Get("/device_users/device_user:"+i+"/email"));
			if (dac.length == 0 && email.length !=0)
			{
				web_accounts++;
			}
        }
		if (mobile_devices)
		{
			if (!PAGE.no_usb2)
			{
				OBJ("d_mobile_device_number").innerHTML = mobile_devices;
			}
			else
			{
				OBJ("u_mobile_device_number").innerHTML = mobile_devices;
			}
		}
		else
		{
			if (!PAGE.no_usb2)
			{
				OBJ("d_mobile_device_number").innerHTML = "0";
			}
			else
			{
				OBJ("u_mobile_device_number").innerHTML = "0";
			}
		}
		
		if (web_accounts)
		{
			if (!PAGE.no_usb2)
			{
				OBJ("d_web_account_number").innerHTML = web_accounts;
			}
			else
			{
				OBJ("u_web_account_number").innerHTML = web_accounts;
			}
		}
		else
		{
			if (!PAGE.no_usb2)
			{
				OBJ("d_web_account_number").innerHTML = "0";
			}
			else
			{
				OBJ("u_web_account_number").innerHTML = "0";
			}
		}
    }
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device_user", "owner=admin&pw="+text);
}

</script>
