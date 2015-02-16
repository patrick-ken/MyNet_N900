<style>
span.value
{
    color: white;
}

</style>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "MACCTRL,WIFI.PHYINF,PHYINF.WIFI,RUNTIME.WPS,WPSPIN",
	OnLoad:    function() {lanugage_StyleSet('<? echo $lang;?>' , "<?echo $TEMP_MYNAME; ?>");},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },

	OnSubmit: function()
	{
		var ajaxObj = GetAjaxObj("WPS");
		var action = this.action;
		if(action=="") action = "PBC";
		
		var uid = "BAND24G-1.1";
		var value = OBJ("pincode").value;
		ajaxObj.createRequest();
		ajaxObj.onCallback = function (xml)
		{
			ajaxObj.release();
			PAGE.WPSOnSubmitCallback(xml.Get("/wpsreport/result"), xml.Get("/wpsreport/reason"));
		}
		
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("wpsacts.php", "action="+action+"&uid="+uid+"&pin="+value);
		AUTH.UpdateTimeout();
	},
	WPSOnSubmitCallback: function(code, result)
	{
		switch (code)
		{
			case "OK":
				this.WPSInProgress();
				break;
			case "":
				BODY.ShowAlert("<?echo I18N('h', 'Wireless is disabled. Please enable wireless first to use WPS.');?>");
				break;
			default:
				BODY.ShowAlert(result);
				break;
		}
		return true;
	},
	OnClickConnectPIN: function()
	{
		this.action = "PIN";
		this.method = 1;
		PAGE.OnSubmit();
	},
	OnClickConnectPBC: function()
	{
		if (OBJ("en_wps").checked )
		{
			this.action = "PBC";
			this.method = 0;
			PAGE.OnSubmit();		
		}
		
	},
	
	wifip: null,
	method: 0,
	defpin: null,
	curpin: null,
	dual_band: COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>'),	
	wifi_module: null,
	start_count_down: false,
	runtime_wps: null,
	wps_configured: null,
	wps_result: 0,
	MSGArr:"",
	wifi_g_active:false,
	wifi_a_active:false,
	SavingMessageTitle: "<?echo I18N('h', 'Saving new wireless setting');?> ...",
	SavingMessageArray: [
	"<?echo I18N('h', 'The new wireless setting will take effect shortly.');?>",
	"<?echo I18N('h', 'Please wait');?> ..."
	],
	SavingWatingMessage:"<?echo I18N('h', 'Time until completion');?>",
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.wifi_module 	= PXML.FindModule("WIFI.PHYINF");
		this.phyinf 		= GPBT(this.wifi_module, "phyinf", "uid","BAND24G-1.1", false);
		this.wifip 			= XG(this.phyinf+"/wifi");
		this.wifip 			= GPBT(this.wifi_module+"/wifi", "entry", "uid", this.wifip, false);
		PAGE.wifi_g_active = XG(this.phyinf+"/active");
		this.runtime_wps	= PXML.FindModule("RUNTIME.WPS");
		var wpspin = PXML.FindModule("WPSPIN");
		var wpspinid = XG(wpspin+"/text");
		PAGE.defpin = wpspinid;

		if (!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		
		var wps_enable 		= XG(this.wifip+"/wps/enable");
		PAGE.wps_configured  = XG(this.wifip+"/wps/configured");
		var lock_wifi_sec	= XG(this.wifip+"/wps/locksecurity");
		var str_info = "";
		
		OBJ("en_wps").checked = COMM_ToBOOL(wps_enable);
		//OBJ("lock_wifi_security").checked = COMM_ToBOOL(lock_wifi_sec);
		if (XG(this.wifip+"/wps/pin")=="")
			this.curpin = OBJ("pin").innerHTML = this.defpin;
		else
			this.curpin = OBJ("pin").innerHTML = XG(this.wifip+"/wps/pin");

		if(this.dual_band)
		{
			this.phyinf2 	= GPBT(this.wifi_module, "phyinf", "uid","BAND5G-1.1", false);
			this.wifip2 	= XG(this.phyinf2+"/wifi");
			this.wifip2 	= GPBT(this.wifi_module+"/wifi", "entry", "uid", this.wifip2, false);
			PAGE.wifi_a_active = XG(this.phyinf2+"/active");
			if(OBJ("en_wps").checked==false && XG(this.wifip2+"/wps/enable")=="1")
			{
				OBJ("en_wps").checked = true;
			}
		}
		
		//this.OnClickEnWPS();
		if(PAGE.wifi_g_active == 0 && PAGE.wifi_a_active == 0)
		{
			OBJ("en_wps").checked		= false;
			OBJ("en_wps").disabled		= true;
		}
		
		if(OBJ("en_wps").checked)
		{
			if(COMM_ToBOOL(XG(this.runtime_wps+"/runtime/wps/setting/aplocked"))) OBJ("en_wps_pin").checked = false;
			else OBJ("en_wps_pin").checked = true;
			OBJ("en_wps_pin").disabled = false;
			OBJ("div_wps_options").style.display ="";
			OBJ("reset_pin").disabled	= false;
			OBJ("gen_pin").disabled		= false;
			button_disabled(OBJ("reset_pin"),false);
			button_disabled(OBJ("gen_pin"),false);
			OBJ("pincode").disabled		= false;
		}	
		else
		{
			OBJ("en_wps_pin").checked = false;
			OBJ("en_wps_pin").disabled = true;
			OBJ("div_wps_options").style.display = "none";
			OBJ("reset_pin").disabled	= true;
			OBJ("gen_pin").disabled		= true;
			button_disabled(OBJ("reset_pin"),true);
			button_disabled(OBJ("gen_pin"),true);
			OBJ("pincode").disabled		= true;
		}
		OBJ("connect_pin").disabled		= !(OBJ("en_wps").checked);
		OBJ("connect_pbc").disabled		= !(OBJ("en_wps").checked);

		//this.OnClickLockSettingWPS();
		
		/* TODO 
		if(wps_enable == "1") 		str_info = "<?echo I18N("j","Enable");?>"; else str_info ="<?echo I18N("j","Disable");?>";
		if(wps_configured == "1") 	str_info +=  "/<?echo I18N("j","Configured");?>"; else str_info += "/<?echo I18N("j","Not Configured");?>";
		OBJ("wifi_info_str").innerHTML = str_info;
		*/
		return true;
	},
	PreSubmit: function()
	{
		//var lock_wps_security = OBJ("lock_wifi_security").checked ? "1":"0";
		if(OBJ("en_wps").checked==true && PAGE.wifi_g_active=="1")
			XS(this.wifip+"/wps/enable", "1");
		else
			XS(this.wifip+"/wps/enable", "0");
		//XS(this.wifip+"/wps/locksecurity", lock_wps_security);
		
		if(this.dual_band)
		{
			if(OBJ("en_wps").checked==true && PAGE.wifi_a_active=="1")
				XS(this.wifip2+"/wps/enable", "1");
			else
				XS(this.wifip2+"/wps/enable", "0");
			//XS(this.wifip2+"/wps/locksecurity", lock_wps_security);
		}
		
		//check authtype, if we use radius server, then wps can't be enabled.
		//check authtype, if we use WEP security, then wps can't be enabled.
		if(OBJ("en_wps").checked)
		{
            if ((!this.Is_SecuritySupportedByWps(this.wifip) || (this.dual_band && !this.Is_SecuritySupportedByWps(this.wifip2))) &&
				(this.Is_HiddenSsid(this.wifip) || (this.dual_band && this.Is_HiddenSsid(this.wifip2)))
				)
            {
                OBJ("en_wps").checked = false;
                OBJ("en_wps_pin").checked = false;
				BODY.NewWDStyle_refresh();
                OBJ("en_wps").setAttribute("modified", false); 
                OBJ("en_wps_pin").setAttribute("modified", false); 
                OBJ("mainform").setAttribute("modified", false); 
                BODY.ShowAlert("<?echo I18N('j', 'WPS cannot be enabled with WPA only or WEP security mode. Change the Security Mode to WPA/WPA2 or WPA2 in Wireless Setup menu before enabling WPS.');?>");
                return null;
            }

            if(this.Is_HiddenSsid(this.wifip) || (this.dual_band && this.Is_HiddenSsid(this.wifip2)) )
            {
                OBJ("en_wps").checked = false;
				BODY.NewWDStyle_refresh();
                OBJ("en_wps").setAttribute("modified", false); 
                OBJ("mainform").setAttribute("modified", false); 
				BODY.ShowAlert("<?echo I18N('j', 'WPS cannot be enabled when SSID broadcast is off. Turn on the SSID broadcast in Wireless Setup menu before enabling WPS.');?>");
                return null;
            }

			if((!this.Is_SecuritySupportedByWps(this.wifip) && PAGE.wifi_g_active=="1") || (this.dual_band && !this.Is_SecuritySupportedByWps(this.wifip2) && PAGE.wifi_a_active=="1"))
			{
				OBJ("en_wps").checked = false;
				OBJ("en_wps_pin").checked = false;
				BODY.NewWDStyle_refresh();
				OBJ("mainform").setAttribute("modified", false); 
				OBJ("en_wps").setAttribute("modified", false); 
                OBJ("en_wps_pin").setAttribute("modified", false);
				BODY.ShowAlert("<?echo I18N('j', 'WPS cannot be enabled with WPA only or WEP security mode. Change the Security Mode to WPA/WPA2 or WPA2 in Wireless Setup menu before enabling WPS.');?>");
				return null;
			}
			
			/* Enable MAC filter doesn't need to disable WPS. They are different function. */
			/* Because MAC filter use iptable rule to control the packet accept or drop */
			/*
			if(this.Is_MacFilterEnabled())
			{
				OBJ("en_wps").checked		= false;
				BODY.NewWDStyle_refresh();
				OBJ("mainform").setAttribute("modified", false); 
				OBJ("en_wps").setAttribute("modified", false); 
				BODY.ShowAlert("<?echo I18N('j', 'WPS can not be enabled when network filter is enabled.').'\\n'.I18N('j','Please select disable network filter in Advanced settings => Security => MAC Filter to enable WPS.');?>");
				return null;
			}
			*/
		}
		
		if(OBJ("en_wps_pin").checked)
		{
			XS(this.runtime_wps+"/runtime/wps/setting/aplocked", 0);
			XS(this.runtime_wps+"/runtime/wps/setting/aplocked_byuser", 0);
		}
		else
		{
			XS(this.runtime_wps+"/runtime/wps/setting/aplocked", 1);
			if (COMM_Equal(OBJ("en_wps_pin").getAttribute("modified"), "true"))
				XS(this.runtime_wps+"/runtime/wps/setting/aplocked_byuser", 1);
		}
		
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function()
	{
		if (OBJ("pin").innerHTML!=this.curpin)
		{
			OBJ("mainform").setAttribute("modified", "true");
			XS(this.wifip+"/wps/pin", OBJ("pin").innerHTML);
			if(this.dual_band)
				XS(this.wifip2+"/wps/pin", OBJ("pin").innerHTML);
		}
	},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	//OnCheckWPAEnterprise:function()
	Is_SecuritySupportedByWps:function(wifipath)
	{
		var auth = XG(wifipath+"/authtype");
		var cipher = XG(wifipath+"/encrtype");
		var issupported = true;
		
		//wpa-enterprise all not supported
		switch(auth)
		{
			case "WPA":
			case "WPAEAP":
				issupported = false;
			default : 
				issupported = true;
		}
		
		//wep all not supported
		if (cipher=="WEP")
			issupported = false;
		
		//wpa-personal, "wpa only" or "tkip only" not supported
		if(auth=="WPAPSK" || cipher=="TKIP")
			issupported = false;
		return issupported;
	}, 
	Is_MacFilterEnabled:function()
	{
		this.macfp = PXML.FindModule("MACCTRL");
		if (!this.macfp) { return false; }
		this.macfp += "/acl/macctrl";
		var policy = "";
		
		if ((policy = XG(this.macfp+"/policy")) !== "")
		{	
			if(policy == "DISABLE")
				return false;
			else 
				return true;
		}
		
		return false;
	},
	
	
	Is_HiddenSsid:function(wifipath)
	{
		if(XG(wifipath+"/ssidhidden") == "1")
			return true;
		else 
			return false;
	}, 

	OnClickEnWPS: function()
	{
		/*
		if (XG(this.wifip+"/wps/configured")=="0")
			OBJ("reset_cfg").disabled = true;
		else
			OBJ("reset_cfg").disabled = false;
		*/
				
		if (OBJ("en_wps").checked )
		{
			//OBJ("go_wps").disabled		= false;
			//OBJ("lock_wifi_security").disabled	= false;
			if(XG(this.runtime_wps+"/runtime/wps/setting/aplocked")!="1") OBJ("en_wps_pin").checked = true;
			else OBJ("en_wps_pin").checked = false;
			OBJ("en_wps_pin").disabled = false;
			OBJ("div_wps_options").style.display ="";
		}
		else
		{
			//OBJ("go_wps").disabled		= true;
			//OBJ("lock_wifi_security").disabled	= true;
			OBJ("en_wps_pin").checked = false;
			OBJ("en_wps_pin").setAttribute("modified","true");
			OBJ("en_wps_pin").disabled = true;
			OBJ("div_wps_options").style.display = "none";
		}
	},
	/*
	OnClickLockSettingWPS: function()
	{
		var configured = COMM_ToBOOL(XG(this.wifip+"/wps/configured"));
		var en_wlan = XG(this.phyinf+"/active");
		if (!configured)
		{
			OBJ("lock_wifi_security").checked = false; 
			OBJ("lock_wifi_security").disabled = true; 
		}
		else
		{
			if(OBJ("lock_wifi_security").checked || en_wlan!="1")	
				OBJ("reset_cfg").disabled 			= true;
			else 									
				OBJ("reset_cfg").disabled 			= false;
		}
	},
	*/	
	OnClickResetPIN: function()
	{
		OBJ("pin").innerHTML = this.defpin;
	},
	OnClickGenPIN: function()
	{
		var pin = "";
		var sum = 0;
		var check_sum = 0;
		var r = 0;
		for(var i=0; i<7; i++)
		{
			r = (Math.floor(Math.random()*9));
			pin += r;
			sum += parseInt(r, [10]) * (((i%2)==0) ? 3:1);
		}
		check_sum = (10-(sum%10))%10;
		pin += check_sum;
		OBJ("pin").innerHTML = pin;
	}
	,
	WPSInProgress: function()
	{	
		if (!this.start_count_down)
		{
			this.start_count_down = true;
			if (this.method)
			{
				var str = '<div style="height:1px;"></div>';
				str += '<h1>'+'<?echo I18N("h", "Connecting your wireless device");?>...' +'</h1>';
				str += '<div class="emptyline"></div>';
				str += '<div class="centerline">';
				str += '<?echo I18N("h", "Please wait a few moments while the wireless device is being connected to your wireless network.");?>';
				str += '</div>';
			}
			else
			{
				var str = '<div style="height:1px;"></div>';
				str += '<h1>'+'<?echo I18N("h", "Connect to Wireless Device");?>'+'...'+'</h1>';
				str += '<div class="emptyline"></div>';
				str += '<div class="centerline">';
				str += '<?echo I18N("h", "Please press down the WPS button (physical or virtual) on the wireless device you are adding to your wireless network.");?>';
				str += '</div>';
			}
			str += '<div class="emptyline"></div>';
			str += '<div class="centerline">';
			str += '<?echo I18N("h", "Remaining time in seconds");?>:  <span id="ct" style="color:red;">120</span>';
			str += '</div>';
			str += '<div class="emptyline"></div>';
			str += '<div class="centerline">';
			str += this.m_prefix;
			str += '<?echo I18N("h", "Started");?>.';
			str += '</div>';
			str += '<div class="emptyline"></div>';
			OBJ("msg").innerHTML = str;
			BODY.NewWDStyle_init();
			this.ShowWPSMessage("WPS_IN_PROGRESS");
			setTimeout('PAGE.WPSCountDown()',1000);
		}

		var ajaxObj = GetAjaxObj("WPS");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function (xml)
		{
			ajaxObj.release();
			PAGE.WPSInProgressCallBack(xml);
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("wpsstate.php", "dummy=dummy");
	},
	WPSInProgressCallBack: function(xml)
	{
		var self = this;
		var cnt = xml.Get("/wpsstate/count");
		
		for (var i=1; i<=cnt; i++)
		{
			var state=xml.Get("/wpsstate/phyinf:"+i+"/state");
			if (state==="WPS_SUCCESS")
				break;
		}
		if (state=="WPS_IN_PROGRESS" || state=="")
			this.wps_timer = setTimeout('PAGE.WPSInProgress()',1000);
		else
			this.ShowWPSMessage(state);
	},
	WPSCountDown: function()
	{
		var time = parseInt(OBJ("ct").innerHTML, 10);
		if (time > 0)
		{
			time--;
			this.cd_timer = setTimeout('PAGE.WPSCountDown()',1000);
			OBJ("ct").innerHTML = time;
		}
		else
		{
			clearTimeout(this.cd_timer);
			this.ShowWPSMessage("WPS_NONE");
		}
	},
	m_prefix: "<?echo I18N("h", "Adding wireless device").": ";?>",
	ShowWPSMessage: function(state)
	{
		switch (state)
		{
			case "WPS_NONE":
				var msgArray =
					[
						this.m_prefix + '<?echo I18N("j","Session timed out. ");?>'
					];
				break;
			case "WPS_ERROR":
				var msgArray =
					[
						this.m_prefix +  '<?echo I18N("j","WPS error.");?>'
					];
				break;
			case "WPS_OVERLAP":
				var msgArray =
					[
						this.m_prefix +  '<?echo I18N("j","Authentication of multiple devices not allowed.");?>'
					];
				break;
			case "WPS_IN_PROGRESS":
				var msgArray = '';
				break;
			case "WPS_SUCCESS":
				var msgArray =
					[
						this.m_prefix + '<?echo I18N("j","Succeeded. ");?>'
					];
				break;
		}
		if(state=="WPS_SUCCESS")
		{
			PAGE.wps_result = 1;
			PAGE.MSGArr = msgArray;
			this.ShowMessageCountdown("<?echo I18N('j','Your wireless device is connected.');?>", msgArray);//For WD ITR 50589
		}
		else
		{
			PAGE.wps_result = 0;
			this.ShowMessageCountdown("<?echo I18N('j','Your wireless device is not connected.');?>", msgArray);
		}
		
		if (state=="WPS_IN_PROGRESS")	return;
		PAGE.start_count_down = false;
		if (this.cd_timer)	clearTimeout(this.cd_timer);
		if (this.wps_timer)	clearTimeout(this.wps_timer);
	},
	ShowMessageCountdown: function(banner, msgArray)
	{
		if(PAGE.wps_result==1 && PAGE.wps_configured!="1")
		{
			PAGE.wps_result=0;
			PAGE.wps_configured="1";
			BODY.ShowCountdown_ex(PAGE.SavingMessageTitle,PAGE.SavingMessageArray,30,"PAGE.ShowMessageCountdown('<?echo I18N('j','Your wireless device is connected.');?>', PAGE.MSGArr);",PAGE.SavingWatingMessage);
			return;
		}
		OBJ("login").style.display		= "none";
		OBJ("menu").style.display		= "none";
		OBJ("content").style.display	= "none";
		OBJ("mbox").style.display	= "none";
		OBJ("mbox2").style.display	= "block";
		//hendry
		if(msgArray != '')
		{
			var str = '<div style="height:1px;"></div>'+'<h1>'+banner+'</h1>';
			for (var i=0; i<msgArray.length; i++)
			{
				str += '<div class="emptyline"></div>';
				str += '<div class="centerline">'+msgArray[i]+'</div>';
			}
			str += '<div class="emptyline"></div>';
			str += '<div class="centerline">';
			str += "<input class='button_blue' type='button' value='<?echo I18N('h', 'Return');?>' onClick='BODY.ShowContent();' />";	
			str += '</div>';
			str += '<div class="emptyline"></div>';
			OBJ("msg").innerHTML = str;
			BODY.NewWDStyle_init();
		}
	}
}

function Service(svc)
{
	//var sec = 10;
	//var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK")
			BODY.ShowAlert("Internal ERROR!\nEVENT "+svc+": "+xml.Get("/report/message"));
		//else
		//	BODY.ShowCountdown(banner, msgArray, sec, url);
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
}

function SetButtonDisabled(name, isDisable)
{
	var button = document.getElementsByName(name);
	for (i=0; i<button.length; i++)
		button[i].disabled = isDisable;
}
function SetButtonDisplayStyle(name, style)
{
	var button = document.getElementsByName(name);
	for (i=0; i<button.length; i++)
		button[i].style.display = style;
}
</script>
