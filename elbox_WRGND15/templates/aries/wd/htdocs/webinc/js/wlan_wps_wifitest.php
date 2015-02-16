<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "MACCTRL,WIFI.PHYINF,PHYINF.WIFI",
	OnLoad: function() {},
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
			default:
				BODY.ShowAlert(result);
				break;
		}
		return true;
	},
	OnClickConnectPIN: function()
	{
		this.action = "PIN";
		PAGE.OnSubmit();
	},
	OnClickConnectPBC: function()
	{
		this.action = "PBC";
		PAGE.OnSubmit();		
	},
	
	wifip: null,
	defpin: '<?echo query("/runtime/devdata/pin");?>',
	curpin: null,
	dual_band: COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>'),	
	wifi_module: null,
	start_count_down: false,

	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.wifi_module 	= PXML.FindModule("WIFI.PHYINF");
		this.phyinf 		= GPBT(this.wifi_module, "phyinf", "uid","BAND24G-1.1", false);
		this.wifip 			= XG(this.phyinf+"/wifi");
		this.wifip 			= GPBT(this.wifi_module+"/wifi", "entry", "uid", this.wifip, false);

		if (!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		
		var wps_enable 		= XG(this.wifip+"/wps/enable");
		var wps_configured  = XG(this.wifip+"/wps/configured");
		var lock_wifi_sec	= XG(this.wifip+"/wps/locksecurity");
		var str_info = "";
		
		
		OBJ("en_wps").checked = COMM_ToBOOL(wps_enable);
		OBJ("connect_pin").disabled		= !(OBJ("en_wps").checked);
		OBJ("connect_pbc").disabled		= !(OBJ("en_wps").checked);	
	
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
		}
			
		this.OnClickEnWPS();
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
		
		XS(this.wifip+"/wps/enable", (OBJ("en_wps").checked)? "1":"0");
		//XS(this.wifip+"/wps/locksecurity", lock_wps_security);
		
		if(this.dual_band)
		{
			XS(this.wifip2+"/wps/enable", (OBJ("en_wps").checked)? "1":"0");
			//XS(this.wifip2+"/wps/locksecurity", lock_wps_security);
		}
		
		//check authtype, if we use radius server, then wps can't be enabled.
		//check authtype, if we use WEP security, then wps can't be enabled.
		if(OBJ("en_wps").checked)
		{
			if(!this.Is_SecuritySupportedByWps(this.wifip) || 
				(this.dual_band && !this.Is_SecuritySupportedByWps(this.wifip2)) )
			{
				OBJ("en_wps").checked		= false;
					/*BODY.ShowAlert("<?echo I18N("j", "WPS isn't supported for these securities : "). "\\n". 
						I18N("j","  - WPA-Personal (WPA Only or TKIP only)") . "\\n". 
						I18N("j","  - WPA-Enterprise") . "\\n". 
						I18N("j","  - WEP security") . "\\n". 
						I18N("j","Please select other security in SETUP => WIRELESS SETTINGS to enable WPS.");?>");*/
				BODY.ShowAlert("<?echo I18N("j", "WPS cannot be enabled when SSID broadcast is off. Turn on the SSID broadcast in Wireless Setup menu before enabling WPS.");?>");
				return null;
			}
			
			if(this.Is_HiddenSsid(this.wifip) || 
				(this.dual_band && this.Is_HiddenSsid(this.wifip2)) )
			{
				OBJ("en_wps").checked		= false;
				BODY.ShowAlert("<?echo I18N("j", "WPS can't be enabled when a hidden SSID (invisible) is selected."). "\\n".
								I18N("j","Please select use visible SSID in SETUP => WIRELESS SETTINGS to enable WPS.");?>");
				return null;
			}
			
			if(this.Is_MacFilterEnabled())
			{
				OBJ("en_wps").checked		= false;
				BODY.ShowAlert("<?echo I18N("j", "WPS can't be enabled when network filter is enabled."). "\\n".
					I18N("j","Please select disable network filter in Advanced settings => Security => MAC Filter to enable WPS.");?>");
				return null;
			}
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
			case "WPA2":
			case "WPA+2":
			case "WPAEAP":
			case "WPA+2EAP":			
			case "WPA2EAP":
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
		var en_wlan = XG(this.phyinf+"/active");
		var en_wlan2 = XG(this.phyinf2+"/active");
		
		if(en_wlan == 0 && en_wlan2 == 0)
		{
			OBJ("en_wps").checked 		= false;
			OBJ("en_wps").disabled		= true;
		}
		
		/*
		if (XG(this.wifip+"/wps/configured")=="0")
			OBJ("reset_cfg").disabled = true;
		else
			OBJ("reset_cfg").disabled = false;
		*/
				
		if (OBJ("en_wps").checked )
		{
			OBJ("reset_pin").disabled	= false;
			OBJ("gen_pin").disabled		= false;
			OBJ("pincode").disabled		= false;
			//OBJ("go_wps").disabled		= false;
			//OBJ("lock_wifi_security").disabled	= false;
		}
		else
		{
			OBJ("reset_pin").disabled	= true;
			OBJ("gen_pin").disabled		= true;
			OBJ("pincode").disabled		= true;
			//OBJ("go_wps").disabled		= true;
			//OBJ("lock_wifi_security").disabled	= true;
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
			var str = '<h3>'+ 'Connect to Wireless Device' +'</h3>';
			if (OBJ("pin").checked)
			{
				str += "<?echo I18N("h", "Please start WPS on the wireless device you are adding to your wireless network.");?><br />";
			}
			else
			{
				str += "<?echo I18N("h", "Please press down the WPS button (physical or virtual) on the wireless device you are adding to your wireless network.");?><br />";
			}
			str += "<?echo I18N('h', 'Remaining time in seconds');?>: <span id='ct'>120</span><br /><br />";
			str += this.m_prefix + "<?echo I18N("h", "Started");?>.";
			OBJ("msg").innerHTML 		= str;
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
			this.wps_timer = setTimeout('PAGE.WPSInProgress()',2000);
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
						this.m_prefix + '<?echo I18N("j","Session Time-Out. ");?>'
					];
				break;
			case "WPS_ERROR":
				var msgArray =
					[
						this.m_prefix +  '<?echo I18N("j","WPS_ERROR.");?>'
					];
				break;
			case "WPS_OVERLAP":
				var msgArray =
					[
						this.m_prefix +  '<?echo I18N("j","WPS_OVERLAP.");?>'
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
		this.ShowMessageCountdown("Connect your Wireless Device", msgArray);
		
		if (state=="WPS_IN_PROGRESS")	return;
		PAGE.start_count_down = false;
		if (this.cd_timer)	clearTimeout(this.cd_timer);
		if (this.wps_timer)	clearTimeout(this.wps_timer);
	},
	ShowMessageCountdown: function(banner, msgArray)
	{
		OBJ("login").style.display		= "none";
		OBJ("menu").style.display		= "none";
		OBJ("content").style.display	= "none";
		OBJ("mbox").style.display	= "none";
		OBJ("mbox2").style.display	= "block";

		//hendry
		if(msgArray != '')
		{
			var str = '<h3>'+banner+'</h3>';
			for (var i=0; i<msgArray.length; i++)
			{
				str += '<div class="emptyline"></div>';
				str += '<div class="centerline">'+msgArray[i]+'</div>';
			}
		
			str += '<div class="emptyline"></div>';
			str += '<div class="centerline">';
			str += "	<input class='button_blue' type='button' value='<?echo I18N('h', 'Return');?>' onClick='BODY.ShowContent();' />";
			str += '</div>';			
			OBJ("msg").innerHTML = str;
		}
	}
}

function Service(svc)
{
	//var banner = "<?echo I18N("h", "RESET WIFI CONFIG");?>...";
	//var msgArray = ["<?echo I18N("h", "Device is resetting wireless config. Please wait ... ");?>"];
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
