
<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div.main_wps_next_button
{
	margin-left:580px;
}

div.main_wps_nowireless
{
    margin-left:380px;
}

div.textinput span.valueb
{
	color: lightBlue;
    font-weight: bold;
	margin-top: 4px;
	position: absolute;
	left: 300px;
}

span.add_dev_desc
{
    color: #FFFFFF;
    font-family: Arial;
    font-size: 16px;
    font-style: normal;
    text-decoration: none;
}   

div.textinput span.wps_name {
    color: white;
    font-size: 13px;
    margin-top: 4px;
    text-align: left;
}

div.textinput span.wps_name_ex {
    color: white;
    font-size: 16px;
    margin-top: 4px;
    text-align: left;
}


.wps_item{margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;color:white;}
.wps_item .item1{position:relative;text-align:left;left:0px;}
.wps_item .item2{position:relative;text-align:left;left:0px;}
.wps_item .item3{position:relative;text-align:left;top:0px;}
.wps_item .item4{position:relative;text-align:left;left:0px;}
.wps_item .item5{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item6{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item7{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item8{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item9{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item10{position:relative;text-align:left;left:0px;top:0px;}
.wps_item .item11{position:relative;text-align:left;left:508px;top:0px;}
.wps_item .item12{position:relative;text-align:left;left:600px;top:0px;}


</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "WIFI.PHYINF,RUNTIME.WPS,PHYINF.WIFI,WPSPIN",
	OnLoad: function()
	{
		var db = "<?=$FEATURE_DUAL_BAND?>";
		var x24g= "<? echo query("/phyinf:4/active"); ?>";
		var x5g= "<? echo query("/phyinf:6/active"); ?>";
		if (db == "1")
		{
			if ((x24g == "0" && x5g == "0") || (x24g == "" && x5g == ""))
			{
				this.currentStage = 6;
			}
		}
		else
		{
			if (x24g == "0" || x24g == "")
			{
				this.currentStage = 6;
			}
		}
		this.ShowCurrentStage();
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		switch (code)
		{
			case "OK":
				if (PAGE.nowps)
				{
					COMM_DelayTime(7000);
					this.currentStage = 1;
					this.ShowCurrentStage();
					PAGE.nowps = 0;
					this.en_wps = true;
					BODY.ShowContent();
				}
				else
				{
					this.WPSInProgress();
				}
				break;
			default:
				BODY.ShowAlert(result);
				break;
		}
		return true;
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		PXML.CheckModule("PHYINF.WIFI","ignore","ignore",null);
		PAGE.dual_band = COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>');		
        PAGE.wifi_phyinf = PXML.FindModule("WIFI.PHYINF");
        PAGE.runtime_wps = PXML.FindModule("RUNTIME.WPS");

        if (!PAGE.wifi_phyinf )
        {
            BODY.ShowAlert("Initial() ERROR!!!");
            return false;
        }
		PAGE.en_24G="0";
		var WIFI_ID="";
		PAGE.security_type_24G="";
		var x = GPBT(PAGE.wifi_phyinf, "phyinf", "uid","BAND24G-1.1", false);
		WIFI_ID = XG(x+"/wifi");
		PAGE.en_24G = XG(x+"/active");
		PAGE.wifi_inf24g = GPBT(PAGE.wifi_phyinf+"/wifi", "entry", "uid", WIFI_ID, false);
		PAGE.security_type_24G = XG(PAGE.wifi_inf24g+"/authtype");
		if(PAGE.dual_band)
		{
			PAGE.en_5G="0";
			PAGE.security_type_5G="";
			x = GPBT(PAGE.wifi_phyinf, "phyinf", "uid","BAND5G-1.1", false);
			WIFI_ID = "";
			WIFI_ID = XG(x+"/wifi");
			PAGE.en_5G = XG(x+"/active");
			PAGE.wifi_inf5g = GPBT(PAGE.wifi_phyinf+"/wifi", "entry", "uid", WIFI_ID, false);
			PAGE.security_type_5G = XG(PAGE.wifi_inf5g+"/authtype");
		}
		this.ShowCurrentStage();
		if (!this.Initial("BAND24G-1.1", "WIFI.PHYINF")) return false;
		if (!this.Initial("BAND5G-1.1", "WIFI.PHYINF")) return false;

		return true;
	},
	PreSubmit: function() 
	{
		XS(this.runtime_wps+"/runtime/wps/setting/aplocked", 0);
		XS(this.runtime_wps+"/runtime/wps/setting/aplocked_byuser", 0);
        XS(this.wifi_inf24g+"/wps/enable", "1");
        if(this.dual_band)
        {
            XS(this.wifi_inf5g+"/wps/enable", "1");
        }
		return PXML.doc;
	},
	IsDirty: function() {return 1;},
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	m_prefix: "<?echo I18N("h", "Adding wireless device").": ";?>",
	m_success: "<?echo I18N("h", "Succeeded").". ".I18N("h", "To add another device click on the Cancel button below or click on the Wireless Status button to check wireless status.");?>",
	m_timeout: "<?echo I18N("h", "Session Time-Out").".";?>",
	wifip: null,
	wpsp: null,
	statep: null,
	en_wps: false,
	method: null,
	runtime_wps: null,
	start_count_down: false,
	wps_timer: null,
	phyinf:null,
	dual_band: 0,	
	wifi_phyinf:null,
	nowps: 0,
	wifi_inf24g: null,
	en_24G: null,
	security_type_24G: "",
	en_5G: null,
	security_type_5G: "",
	wifi_inf5g: null,
	wifi_configured: null,
	wifi_configured_24g: false,
	wifi_configured_5g: false,
	SavingMessageTitle: "<?echo I18N('h', 'Saving new wireless setting');?> ...",
	SavingMessageArray: [
	"<?echo I18N('h', 'The new wireless setting will take effect shortly.');?>",
	"<?echo I18N('h', 'Please wait');?> ..."
	],
	SavingWatingMessage:"<?echo I18N('h', 'Time until completion');?>",
	stages: new Array ("wiz_stage_1", "wiz_stage_2_auto", "wiz_stage_2_manu", "wiz_stage_2_msg", "wiz_wps_success","wiz_wps_failed","wiz_stage_nowireless", "wiz_stage_nowps","wiz_wireless"),
	currentStage: 0,	// 0 ~ this.stages.length
	
	Initial: function(wlan_phyinf, wifi_phyinf)
	{
		this.wifi_phyinf = PXML.FindModule(wifi_phyinf);
		
		if (!this.wifi_phyinf )
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		
		this.phyinf = GPBT(this.wifi_phyinf, "phyinf", "uid",wlan_phyinf, false);
		var wifi_profile = XG(this.phyinf+"/wifi");
		this.wifip = GPBT(this.wifi_phyinf+"/wifi", "entry", "uid", wifi_profile, false);
		this.wifi_configured = GPBT(this.wifi_phyinf, "extraInfo", "uid",wlan_phyinf, false);
		
		var configure = XG(this.wifi_configured+"/media/wps/configured");
		var freq = XG(this.phyinf+"/media/freq");
		if(freq == "5")
		{
			str_Aband = "_Aband";
			if(configure!="1") PAGE.wifi_configured_5g=false;
			else PAGE.wifi_configured_5g=true;
		}
		else
		{
			str_Aband = "";
			if(configure!="1") PAGE.wifi_configured_24g=false;
			else PAGE.wifi_configured_24g=true;
		}
		
		this.en_wps = XG(this.wifip+"/wps/enable")=="1" ? true : false ; 
/*
		if(!this.en_wps)
		{
			this.ShowWpsDisabled();
			return true;
		}
*/
		if (XG(this.wifip+"/wps/pin")=="")
		{
			var wpspin = PXML.FindModule("WPSPIN");
			var wpspinid = XG(wpspin+"/text");
			OBJ("pincode").innerHTML = wpspinid;
		}
		else
			OBJ("pincode").innerHTML = XG(this.wifip+"/wps/pin");
		
		if(str_Aband != "")
			OBJ("frequency"+str_Aband).innerHTML = "5 Ghz Frequency";
		else 
			OBJ("frequency"+str_Aband).innerHTML = "2.4 Ghz Frequency";		
		
		OBJ("ssid"+str_Aband).innerHTML = XG(this.wifip+"/ssid");
		OBJ("frequency"+str_Aband).innerHTML += " ( " + XG(this.wifip+"/ssid") + " ) ";		
		
		switch (XG(this.wifip+"/authtype"))
		{
			case "WPA":
	    		OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WPA-EAP");?>"; 
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				OBJ("st_pskkey"+str_Aband).style.display = "block";
				break;	
			case "WPA2":
	    		OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WPA2-EAP");?>"; 
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				OBJ("st_pskkey"+str_Aband).style.display = "block";		
				break;
		
			case "WPAPSK":
				OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WPA-PSK");?>";
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				OBJ("st_pskkey"+str_Aband).style.display = "block";
				break;
						
			case "WPA2PSK":
				OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WPA2-PSK");?>";
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				OBJ("st_pskkey"+str_Aband).style.display = "block";
				break;
				
			case "WPA+2PSK":
				OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "Auto")." (".I18N("h", "WPA or WPA2").") - ".I18N("h", "Personal");?>";
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				OBJ("st_pskkey"+str_Aband).style.display = "block";
				break;
			/* TODO : actually we don't need these, since wps will be closed in WEP and enterprise mode. (follow WPS 2.0 spec) */
			case "WPA+2":
				OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "Auto")." (".I18N("h", "WPA or WPA2").") - ".I18N("h", "Enterprise");?>";
				OBJ("cipher"+str_Aband).innerHTML = CipherTypeParse(XG(this.wifip+"/encrtype"));
				//OBJ("st_cipher"+str_Aband).style.display = "block";
				this.en_wps = false;
				DisableWPS();
				break;
			case "SHARED":
				var key_no = XG(this.wifip+"/nwkey/wep/defkey");
				OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WEP")." - ".I18N("h", "SHARED");?>";
				OBJ("wepkey"+str_Aband).innerHTML = key_no + ": " + XG(this.wifip+"/nwkey/wep/key:"+key_no);
				OBJ("pskkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/psk/key");
				OBJ("st_wep"+str_Aband).style.display = "block";
				this.en_wps = false;
				DisableWPS();
				break;
			case "OPEN":
			case "WEPAUTO":
				if (XG(this.wifip+"/encrtype")=="WEP")
				{
					var key_no = XG(this.wifip+"/nwkey/wep/defkey");
					OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "WEP")." - ".I18N("h", "AUTO");?>";
					//OBJ("wepkey"+str_Aband).innerHTML = key_no + ": " + XG(this.wifip+"/nwkey/wep/key:"+key_no);
					OBJ("wepkey"+str_Aband).innerHTML = XG(this.wifip+"/nwkey/wep/key:"+key_no);
					OBJ("st_wep"+str_Aband).style.display = "block";
				}
				else
				{
					OBJ("security"+str_Aband).innerHTML = "<?echo I18N("h", "None");?>";
				}
				break;
		}
		return true;
	},
	CheckSecurityType: function(ty,act)
	{
		if(act==24)
		{
			switch(ty)
			{
				case "WPAPSK":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WPA Personal security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WEP":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WEP security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WPA Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA2":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WPA2 Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA+2":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WPA/WPA2 Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WEPAUTO":
					OBJ("encrypt_reason_msg").innerHTML = '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 2.4GHz Frequency: WEP security mode"); ?></font></b>' + "<br><br>";
					return -1;
				default:
					OBJ("encrypt_reason_msg").innerHTML = "";
					return 1;
			}
		}
		else
		{
			switch(ty)
			{
				case "WPAPSK":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WPA Personal security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WEP":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WEP security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WPA Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA2":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WPA2 Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WPA+2":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WPA/WPA2 Enterprise security mode"); ?></font></b>' + "<br><br>";
					return -1;
				case "WEPAUTO":
					OBJ("encrypt_reason_msg").innerHTML = OBJ("encrypt_reason_msg").innerHTML + '<b><font color="deepskyblue" ><?echo I18N("h", "Wireless 5GHz Frequency: WEP security mode"); ?></font></b>' + "<br><br>";
					return -1;
				default:
					return 1;
			}
		}
	},
	ShowCurrentStage: function()
	{
		if(PAGE.currentStage==7)
		{
			if((PAGE.en_24G == "0" && PAGE.en_5G == "0") || (PAGE.en_24G == "" && PAGE.en_5G == ""))
			{
				PAGE.currentStage = 6;
				PAGE.ShowCurrentStage();
				return;
			}
			else
			{
				var result1=0;
				var result2=0;
				OBJ("encrypt_reason_msg").innerHTML = "";
				if(PAGE.en_24G=="1")
				{
					result1=PAGE.CheckSecurityType(PAGE.security_type_24G,24);
				}
				if(PAGE.en_5G=="1")
				{
					result2=PAGE.CheckSecurityType(PAGE.security_type_5G,5);
				}
				if(result1==-1||result2==-1)
				{
					OBJ("encrypt_reason_msg").innerHTML ='<?echo I18N("", "Before adding a wireless device to your network, you must change your wireless security mode instead of using the following security mode:");?>'+'<br><br>'+OBJ("encrypt_reason_msg").innerHTML+'<? echo I18N("", "Click OK to modify your wireless security mode.");?>';
					PAGE.currentStage = 8;
					PAGE.ShowCurrentStage();
					return;
				}
			}
		}
		if(this.currentStage==4 && PAGE.wifi_configured_5g==false && PAGE.wifi_configured_24g==false)
		{
			PAGE.wifi_configured_24g=true;
			PAGE.wifi_configured_5g=true;
			BODY.ShowCountdown_ex(PAGE.SavingMessageTitle,PAGE.SavingMessageArray,30,"PAGE.ShowReturnSuccessWPSMsg();",PAGE.SavingWatingMessage);
			return;
		}

		for (var i=0; i<this.stages.length; i++)
		{
			if (i==this.currentStage)
				OBJ(this.stages[i]).style.display = "block";
			else
				OBJ(this.stages[i]).style.display = "none";
		}
		BODY.NewWDStyle_refresh();
	},
	ShowReturnSuccessWPSMsg: function()
	{
		OBJ("content").style.display	= "block";
		OBJ("menu").style.display		= "block";

		for (var i=0; i<this.stages.length; i++)
		{
			if (i==this.currentStage)
				OBJ(this.stages[i]).style.display = "block";
			else
				OBJ(this.stages[i]).style.display = "none";
		}
		BODY.NewWDStyle_refresh();
	},
	SetStageStr: function (str)
	{	
		var found=0;
		var i=0;
		for (i=0; i<this.stages.length; i++)
		{
			if (this.stages[i]== str)
			{
				OBJ(this.stages[i]).style.display = "block";
				this.currentStage = i;
				found = 1;
			}
			else
			{
				OBJ(this.stages[i]).style.display = "none";
			}
		}
		if(found == 0 )
			BODY.ShowAlert("Can't find stage :" + str);
	}, 
	ShowWPSMessage: function(state)
	{
		switch (state)
		{
			case "WPS_NONE":
			case "WPS_ERROR":
			case "WPS_OVERLAP":
				//this.currentStage = 5;
				this.SetStageStr("wiz_wps_failed");
				break;
			case "WPS_IN_PROGRESS":
				this.SetStageStr("wiz_stage_2_msg");		
				//this.currentStage = 3;		
				break;
			case "WPS_SUCCESS":
				this.SetStageStr("wiz_wps_success");
				break;
		}
		this.ShowCurrentStage();
		if (state=="WPS_IN_PROGRESS")	return;
		PAGE.start_count_down = false;
		if (this.cd_timer)	clearTimeout(this.cd_timer);
		if (this.wps_timer)	clearTimeout(this.wps_timer);
	},
	
	OnClickChooseWPS: function()
	{	
		//for "back button"
		OBJ("pincode_ex").value = "";
		OBJ("btn_next").disabled = true;	
		NewButton.refresh();
		//
		if (this.en_wps)
		{
  		this.currentStage = 1;
		}
		else
		{
			this.currentStage = 7;
		}
		this.ShowCurrentStage();
	},

	OnClickChooseNonWPS: function()
	{
		this.currentStage = 2;
		this.ShowCurrentStage();
		OBJ("pincode_ex").value = "";
	},	
	OnClickAddAnother: function()
	{
		OBJ("pincode_ex").value = "";	
		this.currentStage = 0;
		this.ShowCurrentStage();
	},
	OnClickCancel: function()
	{
		OBJ("pincode_ex").value = "";	
		this.currentStage = 0;
		this.ShowCurrentStage();
	},
	OnClickClosed: function()
	{
		window.location="main_dashboard.php";
	},
	OnClickPBC: function()
	{
		var ajaxObj = GetAjaxObj("WPS");
		var action = "PBC";
		var uid = "BAND24G-1.1";
		ajaxObj.createRequest();
		ajaxObj.onError = function(msg) {}
		ajaxObj.onCallback = function (xml)
		{
			ajaxObj.release();
			PAGE.OnSubmitCallback(xml.Get("/wpsreport/result"), xml.Get("/wpsreport/reason"));
		}
		
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("wpsacts.php", "action="+action+"&uid="+uid);
		AUTH.UpdateTimeout();
	},
    WPSOnSubmitCallback: function(code, result)
    {
        switch (code)
        {
            case "OK":
                this.WPSInProgress_ex();
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
    OnSubmit_ex: function()
    {
        var ajaxObj = GetAjaxObj("WPS");
        var action = this.action;
        if(action=="") action = "PBC";

        var uid = "BAND24G-1.1";
        var value = OBJ("pincode_ex").value;
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
    WPSInProgress_ex: function()
    {
        if (!this.start_count_down)
        {
            this.start_count_down = true;
            var str = "";
            str = "<?echo I18N('h', 'Please wait a few moments while the wireless device is being connected to your wireless network.');?><br>";
            str += "<?echo I18N('h', 'Remaining time in seconds');?>: <span id='ct'>120</span><br>";
            str += this.m_prefix + "<?echo I18N('h', 'Started');?>.";
            OBJ("msg").innerHTML = str;
            this.ShowWPSMessage("WPS_IN_PROGRESS");
            setTimeout('PAGE.WPSCountDown()',1000);
        }

        var ajaxObj = GetAjaxObj("WPS");
        ajaxObj.createRequest();
        ajaxObj.onCallback = function (xml)
        {
            ajaxObj.release();
            PAGE.WPSInProgressCallBack_ex(xml);
        }
        ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest("wpsstate.php", "dummy=dummy");
    },
	WPSInProgress: function()
	{
		if (!this.start_count_down)
		{
			this.start_count_down = true;
			var str = "";
			str = "<?echo I18N('h', 'Please press down the WPS button (physical or virtual) on the wireless device you are adding to your wireless network.');?><br>";
			str += "<?echo I18N('h', 'Remaining time in seconds');?>: <span id='ct'>120</span><br>";
			str += this.m_prefix + "<?echo I18N('h', 'Started');?>.";
			OBJ("msg").innerHTML = str;
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
    WPSInProgressCallBack_ex: function(xml)
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
            this.wps_timer = setTimeout('PAGE.WPSInProgress_ex()',1000);
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
	SetStage: function(offset)
	{
		var length = this.stages.length;
		switch (offset)
		{
		case 2:
			if (this.currentStage < length-1)
				this.currentStage += 2;
			break;			
		case 1:
			if (this.currentStage < length-1)
				this.currentStage += 1;
			break;
		case -1:
			if (this.currentStage > 0)
				this.currentStage -= 1;
			break;
		case -2:
			if (this.currentStage > 1)
				this.currentStage -= 2;
			break;			
		}
	},
	ShowWpsDisabled: function()
	{
		for (var i=0; i<this.stages.length; i++)
		{
			OBJ(this.stages[i]).style.display = "none";
		}
		OBJ("wiz_stage_wps_disabled").style.display = "block";
	},
    OnClickConnectPIN: function()
    {
		if (OBJ("pincode_ex").value !="")
		{
        	this.action = "PIN";
        	this.method = 1;
        	PAGE.OnSubmit_ex();
		}
		else
		{
			PAGE.OnClickCancel();
		}
    }
}

function CipherTypeParse(cipher)
{
	switch (cipher)
	{
	case "TKIP+AES":
		return "<?echo I18N('h', 'TKIP and AES');?>";
	case "TKIP":
		return "<?echo I18N('h', 'TKIP');?>";
	case "AES":
		return "<?echo I18N('h', 'AES');?>";
	}
}
function DisableWPS()
{
	OBJ("pin").disabled = true;
	OBJ("pbc").disabled = true;
	OBJ("pincode").disabled = true;
	SetButtonDisabled("b_send", true);
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

//Let "next button" could change disable mode by entering PIN Code
function button_change(pincode_ex)
{
	if (pincode_ex!=""){
		OBJ("btn_next").disabled = false;
		NewButton.refresh();			
	}
	else {
		OBJ("btn_next").disabled = true;	
		NewButton.refresh();
	}
}

</script>
