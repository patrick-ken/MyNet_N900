<?include "/htdocs/phplib/phyinf.php";?>
<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div.textinput span.title {
    border: 0 none;
    color: white;
    <? 
		if($lang=="ru" || $lang=="es" || $lang=="hu" || $lang=="cs") echo "font-size: 16px;";
		else if($lang=="pl") echo "font-size: 10px;";
		else echo "font-size: 18px;";
    ?>
    font-weight: bold;
    margin: 8px 0;
    padding: 0;
    text-align: left;
}
div.textinput span.name_d
{
	color: white;
	text-align: left;
	margin-top: 4px;
    <? 
		if($lang=="es" || $lang=="de"||$lang=="ptbr") echo "font-size: 12px;";
		else if($lang=="fr")	echo "font-size: 13px;";
		else echo "font-size: 14px;";
    ?>
    margin-left: 10px;
}
div.textinput span.value_d
{
	color: white;
	margin-top: 4px;
	position: absolute;
    <? 
		if($lang=="es" || $lang=="de"||$lang=="fr"||$lang=="no"||$lang=="ptbr") echo "left: 200px;";
		else echo "left: 180px;";
    ?>
}
.same_width
{
	width: 174px;
} 
</style>
<script type="text/javascript">
function Page() {}
Page.prototype =
{	
	services: "WIFI.PHYINF,PHYINF.WIFI",
	OnLoad: function()
	{
		PAGE.DecideToGo_EN = PAGE.NowEnable;
		PAGE.DecideToGo_EN_Aband = PAGE.NowEnable_Aband;
		PAGE.DecideToGo_NowSecurityType = PAGE.NowSecurityType;
		PAGE.DecideToGo_NowSecurityType_Aband = PAGE.NowSecurityType_Aband;
		if(PAGE.STAGE==1 || PAGE.NeedResultMsg == 1)
		{
			COMM_DelayTime(7000);
			PAGE.STAGE = 0;
			var str="";
			var translate_HTML;
			var translate_HTML_key;
			if(PAGE.NowUsingPW==null) PAGE.NowUsingPW="  ";
			if(PAGE.NowUsingPW_Aband==null) PAGE.NowUsingPW_Aband="  ";
			if(PAGE.NowEnable==1)
			{
				translate_HTML="";
				translate_HTML_key="";
				translate_HTML = COMM_EscapeHTMLSC(OBJ('ssid').value);
				translate_HTML_key = COMM_EscapeHTMLSC(PAGE.NowUsingPW);
				str=str+"<tr><td>2.4 GHz</td><td style='color:deepskyblue; font-weight:bold;'>"+translate_HTML+"</td><td style='width:280px; display:inline-block; word-wrap: break-word; word-break: break-all; border-color:gray black black black;' >"+translate_HTML_key+"</td></tr>";
			}
			if(PAGE.NowEnable_Aband==1)
			{
				translate_HTML="";
				translate_HTML_key="";
				translate_HTML = COMM_EscapeHTMLSC(OBJ('ssid_Aband').value);
				translate_HTML_key = COMM_EscapeHTMLSC(PAGE.NowUsingPW_Aband);
				str=str+"<tr><td>5 GHz</td><td style='color:deepskyblue; font-weight:bold;'>"+translate_HTML+"</td><td style='width:280px; display:inline-block; word-wrap: break-word; word-break: break-all; border-color:gray black black black;' >"+translate_HTML_key+"</td></tr>";
			}
			var msgArrayResult=[
				"<?echo I18N('h','To connect to your new wireless network, open your computer/device\'s wireless/Wi-Fi settings and connect to the new network name with the new password.');?>",
				"<table class='general'><tr><th width='80px'></th><th width='230px'><?echo I18N('h','Network Name (SSID)');?></th><th width='280px'><?echo I18N('h','Password');?></th></tr>"+str+"</table>",
				"<input type='button' class='button_blue' id='reload' onclick='PAGE.DecideToGo();' value='<?echo I18N('h', 'Ok');?>'>"
			];
			BODY.ShowMessage("<?echo I18N('h', 'Wireless Setting Changed'); ?>",msgArrayResult);	
		}
		else
		{
			PAGE.DecideToGo();
		}
		//initial all variables to null
		PAGE.InitLocalVariables();
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result){ return false; },
	InitLocalVariables: function()
	{
		PAGE.NeedResultMsg = 0;
		PAGE.STAGE = 0;
		PAGE.NowUsingPW=null;
		PAGE.NowUsingPW_Aband=null;
		PAGE.NowEnable=null;
		PAGE.NowEnable_Aband=null;
		PAGE.NowSecurityType=null;
		PAGE.NowSecurityType_Aband=null;
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;		
		if(!this.Initial("BAND24G-1.1","WIFI.PHYINF")) return false;
		
		this.dual_band = COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>');
		
		if(this.dual_band)
		{
			OBJ("div_5G").style.display = "block";
			if(!this.Initial("BAND5G-1.1","WIFI.PHYINF")) return false; 				
		}
		else 				
			OBJ("div_5G").style.display = "none";
		return true;
	},
	STAGE:0,
	msgArray: [
		"<?echo I18N('h', 'Your wireless devices in the network will be disconnected. Please make changes to your devices\' wireless/Wi-Fi settings to reconnect after saving the new settings.');?>",
		"<input type='button' class='button_black' id='reload' onclick='PAGE.STAGE=0; BODY.ShowContent();' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='PAGE.STAGE=1; BODY.OnSubmit();' value='<?echo I18N('h', 'Save');?>'>"
	],
	msgArray2: [
		"<?echo I18N('h', 'Turning off WiFi will disconnect wireless devices from the router.');?>",
		"<input type='button' class='button_black' id='reload' onclick='PAGE.STAGE=0; BODY.ShowContent();' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='PAGE.STAGE=-1; BODY.OnSubmit();' value='<?echo I18N('h', 'Save');?>'>"
	],
	SavingMessageTitle: "<?echo I18N('h', 'Saving new wireless setting');?> ...",
	SavingMessageArray: [
	"<?echo I18N('h', 'The new wireless setting will take effect shortly.');?>",
	"<?echo I18N('h', 'Please wait');?> ..."
	],
	NowUsingPW: null,
	NowUsingPW_Aband: null,
	NowEnable: null,
	NowEnable_Aband: null,
	NowSecurityType: null,
	NowSecurityType_Aband: null,
	DecideToGo_EN: null,
	DecideToGo_EN_Aband: null,
	DecideToGo_NowSecurityType: null,
	DecideToGo_NowSecurityType_Aband: null,
	NeedResultMsg: 0,
	OLD_en_wifi: false,
	OLD_ssid:"",
	OLD_security_type:"",
	OLD_wep_64:"",
	OLD_wep_128:"",
	OLD_wpapsk:"",
	OLD_srv_ip:"",
	OLD_srv_port:"",
	OLD_srv_sec:"",
	OLD_en_wifi_Aband: false,
	OLD_ssid_Aband:"",
	OLD_security_type_Aband:"",
	OLD_wep_64_Aband:"",
	OLD_wep_128_Aband:"",
	OLD_wpapsk_Aband:"",
	OLD_srv_ip_Aband:"",
	OLD_srv_port_Aband:"",
	OLD_srv_sec_Aband:"",
	FromWPSwizard: '<? echo $_POST["FromWPS"];?>',
	CheckSecurityType: function(ty)
	{
		switch(ty)
		{
			case "WPAPSK":
				return -1;
			case "WEP":
				return -1;
			case "WEPAUTO":
				return -1;
			case "WPA":
				return -1;
			case "WPA2":
				return -1;
			case "WPA+2":
				return -1;
			default:
				return 1;
		}
	},
	DecideToGo: function()
	{
		if(PAGE.FromWPSwizard=="1")
		{
			if(PAGE.DecideToGo_EN=="1" && PAGE.DecideToGo_EN_Aband=="1")
			{
				if( PAGE.CheckSecurityType(PAGE.DecideToGo_NowSecurityType)==1 && PAGE.CheckSecurityType(PAGE.DecideToGo_NowSecurityType_Aband)==1)
					self.location.href='/main_wps.php';
			}
			else if(PAGE.DecideToGo_EN=="0" && PAGE.DecideToGo_EN_Aband=="1")
			{
				if(PAGE.CheckSecurityType(PAGE.DecideToGo_NowSecurityType_Aband)==1)
					self.location.href='/main_wps.php';
			}
			else if(PAGE.DecideToGo_EN=="1" && PAGE.DecideToGo_EN_Aband=="0")
			{
				if(PAGE.CheckSecurityType(PAGE.DecideToGo_NowSecurityType)==1)
					self.location.href='/main_wps.php';
			}
		}
		BODY.ShowContent();
	},
	ModifyCheck: function(band)
	{
		if( OBJ("en_wifi"+band).checked== false || OBJ("en_wifi"+band).checked!= eval('PAGE.OLD_en_wifi'+band) )
		{
			if(OBJ("en_wifi"+band).checked==true)
			{
				PAGE.NeedResultMsg = 1;
			}
			return false;
		}
		
		if(
		OBJ("ssid"+band).value != eval('PAGE.OLD_ssid'+band)||
		OBJ("security_type"+band).value != eval('PAGE.OLD_security_type'+band)||
		OBJ("wep_64_1"+band).value != eval('PAGE.OLD_wep_64'+band)||
		OBJ("wep_128_1"+band).value != eval('PAGE.OLD_wep_128'+band)||
		OBJ("wpapsk"+band).value != eval('PAGE.OLD_wpapsk'+band)||
		OBJ("srv_ip"+band).value != eval('PAGE.OLD_srv_ip'+band)||
		OBJ("srv_port"+band).value != eval('PAGE.OLD_srv_port'+band)||
		OBJ("srv_sec"+band).value != eval('PAGE.OLD_srv_sec'+band)
		)
		{
			return true;
		}
		else
		{
			return false;
		}
	},
	PreSubmit: function()
	{		
		if(PAGE.STAGE==0)
		{
			var FieldDirty = false;
			if(!this.ValidityCheck("BAND24G-1.2","")) return null;
        	if(this.dual_band)
        	{
	            if(!this.ValidityCheck("BAND5G-1.2","")) return null;
	            if(!this.ValidityCheck("BAND24G-1.2","5")) return null;
	            if(!this.ValidityCheck("BAND5G-1.2","5")) return null;
			}	
			FieldDirty = PAGE.ModifyCheck("");
			if(this.dual_band && FieldDirty==false)
			{
				FieldDirty = PAGE.ModifyCheck("_Aband");
			}
			if(FieldDirty==true)
			{
				BODY.ShowMessage("<?echo I18N('h', 'Warning'); ?>",PAGE.msgArray);
				return null;
			}
			if(OBJ("en_wifi").checked==false && OBJ("en_wifi_Aband").checked==false)
			{
				BODY.ShowMessage("<?echo I18N('h', 'Warning'); ?>",PAGE.msgArray2);
				return null;
			}
		}
		/////////////////
		if(!this.SaveXML("BAND24G-1.1")) return null; 				
		if(!this.WPSCHK("BAND24G-1.1")) return null; 
		
		if(this.dual_band)
		{
			if(!this.SaveXML("BAND5G-1.1")) return null;
			if(!this.WPSCHK("BAND5G-1.1")) return null;
			this.WPS_CONFIGURED_CHK("BAND24G-1.1","BAND5G-1.1");
		}
		return PXML.doc;
	},			
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	wifip: null,
	phyinf: null,
	sec_type: null,
	sec_type_Aband: null,
	bandWidth: null,
	wps: true,
	dual_band: 0,

	str_Aband: null,
	dual_band: null,
	feature_nosch: null,
	Initial: function(wlan_uid,wifi_module)
	{
		this.wifi_module 			= PXML.FindModule(wifi_module);
		if (!this.wifi_module)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.phyinf = GPBT(this.wifi_module, "phyinf", "uid",wlan_uid, false);

		var wifi_profile 	= XG(this.phyinf+"/wifi");
		var freq 			= XG(this.phyinf+"/media/freq");
		var wifip 			= GPBT(this.wifi_module+"/wifi", "entry", "uid", wifi_profile, false);
		var channel 		= XG(this.phyinf+"/media/channel");
		
		if(freq == "5") 	str_Aband = "_Aband";
		else				str_Aband = "";
		
		COMM_SetSelectValue(OBJ("wlan_mode"+str_Aband), XG(this.phyinf+"/media/wlmode"));
		OBJ("en_wifi"+str_Aband).checked = COMM_ToBOOL(XG(this.phyinf+"/active"));
		eval('PAGE.OLD_en_wifi'+str_Aband+'='+ OBJ("en_wifi"+str_Aband).checked);
		
		<? if($FEATURE_NOSCH!="1")echo 'this.feature_nosch=0;'; else echo 'this.feature_nosch=1;'; ?>
		
		if(this.feature_nosch==0)
			COMM_SetSelectValue(OBJ("sch"+str_Aband), XG(this.phyinf+"/schedule"));
		
		OBJ("ssid"+str_Aband).value = XG(wifip+"/ssid");
		eval('PAGE.OLD_ssid'+str_Aband+ '=' +'OBJ("ssid"+str_Aband).value');
		
		if(channel=="0")	COMM_SetSelectValue(OBJ("channel"+str_Aband), "auto");
		else 			COMM_SetSelectValue(OBJ("channel"+str_Aband), XG(this.phyinf+"/media/channel"));
			
		OBJ("en_wmm"+str_Aband).checked = COMM_ToBOOL(XG(this.phyinf+"/media/wmm/enable"));			
		
		if(COMM_ToBOOL(XG(wifip+"/ssidhidden"))== true) 	OBJ("ssid_visible"+str_Aband).checked = false;
		else 													OBJ("ssid_visible"+str_Aband).checked = true;
		this.OnChangeWLMode(str_Aband);	//move from last sequence, bc. need to create security list
		
        var sec_type = "";
        if (!OBJ("en_wifi"+str_Aband).checked)
            sec_type = "";
        else if (XG(wifip+"/encrtype")=="WEP")
            sec_type = "wep";
        else if (/WPA/.test(XG(wifip+"/authtype")))
            sec_type = XG(wifip+"/authtype");
        else
            sec_type = "";
            
        if(sec_type == "" && XG(wifip+"/authtype")!="OPEN")
        	sec_type = XG(wifip+"/authtype");
        
        if(freq == "5") //a band	
        	this.sec_type_Aband = sec_type;    
        else 			//g band
        	this.sec_type = sec_type;            	
        	
		COMM_SetSelectValue(OBJ("security_type"+str_Aband), sec_type);	
		eval('PAGE.OLD_security_type'+str_Aband + '=' +'sec_type');
		///////////////// initial WEP /////////////////
		var auth = XG(wifip+"/authtype");
		var len = (XG(wifip+"/nwkey/wep/size")=="")? "64" : XG(wifip+"/nwkey/wep/size");
		var defkey = (XG(wifip+"/nwkey/wep/defkey")=="")? "1" : XG(wifip+"/nwkey/wep/defkey");
		this.wps = COMM_ToBOOL(XG(wifip+"/wps/enable"));
		OBJ("auth_type"+str_Aband).disabled = this.wps;
		if (auth!="SHARED") auth = "OPEN";
		COMM_SetSelectValue(OBJ("auth_type"+str_Aband),	auth);
		COMM_SetSelectValue(OBJ("wep_key_len"+str_Aband),	len);
		COMM_SetSelectValue(OBJ("wep_def_key"+str_Aband),	defkey);
		OBJ("wep_"+len+"_1"+str_Aband).value = XG(wifip+"/nwkey/wep/key:1");
		eval('PAGE.OLD_wep_'+len+str_Aband+'='+ 'OBJ("wep_"+len+"_1"+str_Aband).value');
		///////////////// initial WPA /////////////////
		var cipher = XG(wifip+"/encrtype");
		switch (cipher)
		{
			case "TKIP":
			case "AES":
				break;
			default:
				cipher = "TKIP+AES";
		}
		COMM_SetSelectValue(OBJ("cipher_type"+str_Aband), cipher);

		OBJ("wpapsk"+str_Aband).value	= XG(wifip+"/nwkey/psk/key");
		eval('PAGE.OLD_wpapsk'+str_Aband+'='+'OBJ("wpapsk"+str_Aband).value');
		OBJ("srv_ip"+str_Aband).value	= XG(wifip+"/nwkey/eap/radius");
		eval('PAGE.OLD_srv_ip'+str_Aband+'='+'OBJ("srv_ip"+str_Aband).value');
		OBJ("srv_port"+str_Aband).value	= (XG(wifip+"/nwkey/eap/port")==""?"1812":XG(wifip+"/nwkey/eap/port"));
		eval('PAGE.OLD_srv_port'+str_Aband+'='+'OBJ("srv_port"+str_Aband).value');
		OBJ("srv_sec"+str_Aband).value	= XG(wifip+"/nwkey/eap/secret");
		eval('PAGE.OLD_srv_sec'+str_Aband+'='+'OBJ("srv_sec"+str_Aband).value');

		this.OnChangeSecurityType(str_Aband);
		this.OnChangeWEPKey(str_Aband);
		
		this.OnClickEnWLAN(str_Aband);
		/*BODY.ShowAlert
		(
		"active? :"+PAGE.OLD_en_wifi+"\n"+
		"ssid:" +PAGE.OLD_ssid+"\n"+
		"security type :" +PAGE.OLD_security_type+"\n"+
		"wep64: "+PAGE.OLD_wep_64+"\n"+
		"wep128: "+PAGE.OLD_wep_128+"\n"+
		
		"wpapsk: "+PAGE.OLD_wpapsk+"\n"+
		"srv_ip: "+PAGE.OLD_srv_ip+"\n"+
		"srv_port: "+PAGE.OLD_srv_port+"\n"+
		"srv_ip: "+PAGE.OLD_srv_sec+"\n"+
		
		"active_Aband? :"+PAGE.OLD_en_wifi_Aband+"\n"+
		"ssid_Aband:" +PAGE.OLD_ssid_Aband+"\n"+
		"security type_Aband :" +PAGE.OLD_security_type_Aband+"\n"+
		"wep64_Aband: "+PAGE.OLD_wep_64_Aband+"\n"+
		"wep128_Aband: "+PAGE.OLD_wep_128_Aband+"\n"+
		
		"wpapsk_Aband: "+PAGE.OLD_wpapsk_Aband+"\n"+
		"srv_ip_Aband: "+PAGE.OLD_srv_ip_Aband+"\n"+
		"srv_port_Aband: "+PAGE.OLD_srv_port_Aband+"\n"+
		"srv_ip_Aband: "+PAGE.OLD_srv_sec_Aband+"\n"
		);*/
		return true;
	},
	
	SetWps: function(string)
	{
		var phyinf 		= GPBT(this.wifi_module, "phyinf", "uid","BAND24G-1.1", false);
		var wifip 		= GPBT(this.wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);
		
		if(this.dual_band)
		{
			var phyinf2 	= GPBT(this.wifi_module, "phyinf", "uid","BAND5G-1.1", false);
			var wifip2 		= GPBT(this.wifi_module+"/wifi", "entry", "uid", XG(phyinf2+"/wifi"), false);	
		}
		
		if(string=="enable")
		{
			XS(wifip+"/wps/enable", "1");
			if(this.dual_band) XS(wifip2+"/wps/enable", "1");
		}
		else
		{
			XS(wifip+"/wps/enable", "0");
			if(this.dual_band) XS(wifip2+"/wps/enable", "0");			
		}
	},
	
	WPS_CONFIGURED_CHK: function(wlan_uid,wlan2_uid)
	{
		var wifi_module = this.wifi_module;
		var phyinf 		= GPBT(wifi_module,"phyinf","uid",wlan_uid,false);
		var wifip 		= GPBT(wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);			
		var phyinf2 	= GPBT(wifi_module,"phyinf","uid",wlan2_uid,false);
		var wifip2 		= GPBT(wifi_module+"/wifi", "entry", "uid", XG(phyinf2+"/wifi"), false);			

		var wps_configured  = XG(wifip+"/wps/configured");		
		var wps_configured2  = XG(wifip2+"/wps/configured");		
		if(wps_configured!=wps_configured2)
		{
			XS(wifip+"/wps/configured", "1");
			XS(wifip2+"/wps/configured", "1");
		}
	},
	
	WPSCHK: function(wlan_uid)
	{
		var wifi_module = this.wifi_module;
		var phyinf 		= GPBT(wifi_module,"phyinf","uid",wlan_uid,false);
		var freq 		= XG(phyinf+"/media/freq");
		var wifip 		= GPBT(wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);			
		
		if(freq == "5")	str_Aband = "_Aband";
		else			str_Aband = "";
			
		if (COMM_EqBOOL(OBJ("ssid"+str_Aband).getAttribute("modified"),true) ||
			COMM_EqBOOL(OBJ("security_type"+str_Aband).getAttribute("modified"),true))
		{
			XS(wifip+"/wps/configured", "1");
		}
		
		//check authtype, if radius server is used, then wps must be disabled.
		var wps_enable = COMM_ToBOOL(XG(wifip+"/wps/enable"));
		
		if(wps_enable)
		{
			var s = OBJ("security_type"+str_Aband).value;
			
			if(s =="WPA" || s =="WPA2" || s == "WPA+2" )
			{
				if(confirm('<?echo I18N("j", "To use WPA-enterprise security, WPS must be disabled. Proceed ? ");?>'))
					//XS(wifip+"/wps/enable", "0");
					this.SetWps("disable");
				else 
					return false;
			}
			else if(s =="wep")
			{
				if(confirm('<?echo I18N("j", "To use WEP security, WPS must be disabled. Proceed ? ");?>'))
					//XS(wifip+"/wps/enable", "0");
					this.SetWps("disable");
				else 
					return false;
			}
			
			if(s =="WPA2PSK" || s =="WPAPSK" || s == "WPA+2PSK")
			{
				if(OBJ("cipher_type"+str_Aband).value == "TKIP" )
				{
					if(confirm('<?echo I18N("j", "To use WPA only or TKIP only security, WPS must be disabled. Proceed ? ");?>'))
						this.SetWps("disable");
					else
						return false;
				}
			}
		
			if(OBJ("ssid_visible"+str_Aband).checked==false)
			{
				if(confirm('<?echo I18N("j", "To use a hidden SSID (invisible), WPS must be disabled. Proceed ? ");?>'))
					//XS(wifip+"/wps/enable", "0");
					this.SetWps("disable");
				else 
					return false;
			}
		}
		
		//for pass WPS 2.0 test, we add warning when security is disabled. 
		var wifi_enabled = OBJ("en_wifi"+str_Aband).checked;
		if(wifi_enabled && OBJ("security_type"+str_Aband).value=="")
		{
			if(str_Aband=="")
			{
				if(!confirm('<?echo I18N("j", "Warning ! Selecting None in Security Mode will make your 2.4 GHz wifi connection vulnerable. Continue ? ");?>'))
					return false;
			}
			else
			{
				if(!confirm('<?echo I18N("j", "Warning ! Selecting None in Security Mode will make your 5 GHz wifi connection vulnerable. Continue ? ");?>'))
					return false;	
			}
		}
		return true;
	},
	
	
	SaveXML: function(wlan_uid)
	{
		var wifi_module 	= this.wifi_module;
		var phyinf 			= GPBT(wifi_module,"phyinf","uid",wlan_uid,false);
		var wifi_profile 	= XG(phyinf+"/wifi");
		var wifip 			= GPBT(wifi_module+"/wifi", "entry", "uid", wifi_profile, false);
		var freq 			= XG(phyinf+"/media/freq");
		var WEPreg;

		if(freq == "5")		str_Aband = "_Aband";
		else				str_Aband = "";
		
		if (OBJ("en_wifi"+str_Aband).checked)
		{
			XS(phyinf+"/active", "1");
			eval('PAGE.NowEnable'+str_Aband+'=1');			
		}
		else
		{
			XS(phyinf+"/active", "0");
			eval('PAGE.NowEnable'+str_Aband+'=0');
			return true;
		}

		if(this.feature_nosch==0)
			XS(phyinf+"/schedule",	OBJ("sch"+str_Aband).value);
		
		XS(wifip+"/ssid",		OBJ("ssid"+str_Aband).value);
		
		if (OBJ("channel"+str_Aband).value == "auto" )
			XS(phyinf+"/media/channel", "0");
		else		
			XS(phyinf+"/media/channel", OBJ("channel"+str_Aband).value);			
		
		XS(phyinf+"/media/wlmode",		OBJ("wlan_mode"+str_Aband).value);
		if (/n/.test(OBJ("wlan_mode"+str_Aband).value) || /ac/.test(OBJ("wlan_mode"+str_Aband).value))
		{
			XS(phyinf+"/media/dot11n/bandwidth",		OBJ("bw"+str_Aband).value);
			this.bandWidth = OBJ("bw"+str_Aband).value;
		}
		XS(phyinf+"/media/wmm/enable",	SetBNode(OBJ("en_wmm"+str_Aband).checked));
		XS(wifip+"/ssidhidden",			SetBNode(!OBJ("ssid_visible"+str_Aband).checked));
		
		if (OBJ("security_type"+str_Aband).value=="wep")
		{
			switch(OBJ("wep_key_len"+str_Aband).value)
			{
				case "64":
					if(OBJ("wep_64_1"+str_Aband).value.length!=5 && OBJ("wep_64_1"+str_Aband).value.length!=10)
					{
						BODY.ShowAlert("<? echo I18N('j','The WEP key must be 5 characters or 10 HEX characters long.'); ?>");
						PAGE.STAGE=0;
						BODY.ShowContent();
						OBJ("wep_64_1"+str_Aband).focus();
						return null;
					}
					else
					{
						if(OBJ("wep_64_1"+str_Aband).value.length==5)
						{//ascii
							WEPreg = new RegExp("[\x20-\x7E]{5}");
						}
						else if(OBJ("wep_64_1"+str_Aband).value.length==10)
						{//hex
							WEPreg = new RegExp("[A-Fa-f0-9]{10}");
						}
						if(!WEPreg.exec(OBJ("wep_64_1"+str_Aband).value))
						{
							BODY.ShowAlert("<? echo I18N('j','The WEP key must be 5 characters or 10 HEX characters long.'); ?>");
							PAGE.STAGE=0;
							BODY.ShowContent();
							OBJ("wep_64_1"+str_Aband).focus();
							return null;
						}
					}
					break;
				case "128":
					if(OBJ("wep_128_1"+str_Aband).value.length!=13 && OBJ("wep_128_1"+str_Aband).value.length!=26)
					{
						BODY.ShowAlert("<? echo I18N('j','The WEP key must be 13 characters or 26 HEX characters long.'); ?>");
						PAGE.STAGE=0;
						BODY.ShowContent();
						OBJ("wep_128_1"+str_Aband).focus();
						return null;
					}
					else
					{
						if(OBJ("wep_128_1"+str_Aband).value.length==13)
						{//ascii
							WEPreg = new RegExp("[\x20-\x7E]{13}");
						}
						else if(OBJ("wep_128_1"+str_Aband).value.length==26)
						{//hex
							WEPreg = new RegExp("[A-Fa-f0-9]{26}");
						}
						if(!WEPreg.exec(OBJ("wep_128_1"+str_Aband).value))
						{
							BODY.ShowAlert("<? echo I18N('j','The WEP key must be 13 characters or 26 HEX characters long.'); ?>");
							PAGE.STAGE=0;
							BODY.ShowContent();
							OBJ("wep_128_1"+str_Aband).focus();
							return null;
						}
					}
					break;
				default:
					BODY.ShowAlert("<? echo I18N('j','WEP key length field set error.'); ?>");
			}
			if (OBJ("auth_type"+str_Aband).value=="SHARED")
				XS(wifip+"/authtype", "SHARED");
			else
				XS(wifip+"/authtype", "WEPAUTO");
			XS(wifip+"/encrtype",			"WEP");
			eval('PAGE.NowSecurityType'+str_Aband+'="WEP"');
			XS(wifip+"/nwkey/wep/size",	OBJ("wep_key_len"+str_Aband).value);
			XS(wifip+"/nwkey/wep/ascii",	"");
			XS(wifip+"/nwkey/wep/defkey",	OBJ("wep_def_key"+str_Aband).value);
			for (var i=1, len=OBJ("wep_key_len"+str_Aband).value; i<5; i++)
			{
				if (i==OBJ("wep_def_key"+str_Aband).value)
				{
					XS(wifip+"/nwkey/wep/key:"+i, OBJ("wep_"+len+"_"+i+str_Aband).value);
					eval('PAGE.NowUsingPW'+str_Aband+'=OBJ("wep_"+len+"_"+i+str_Aband).value');
				}
				else
				{
					XS(wifip+"/nwkey/wep/key:"+i, "");
				}
			}
		}
        else if (/WPA/.test(OBJ("security_type"+str_Aband).value))
        {
			/*XS(wifip+"/encrtype", OBJ("cipher_type"+str_Aband).value);*/
			switch (OBJ("security_type"+str_Aband).value)
       		{
            case "WPA2":
           		XS(wifip+"/encrtype", "AES");
           		eval('PAGE.NowSecurityType'+str_Aband+'="WPA2"');
           		OBJ("cipher_type"+str_Aband).value="AES";
            	break;
            case "WPA2PSK":
            	XS(wifip+"/encrtype", "AES");
            	eval('PAGE.NowSecurityType'+str_Aband+'="WPA2PSK"');
            	OBJ("cipher_type"+str_Aband).value="AES";
            	break;
            case "WPA":
            	XS(wifip+"/encrtype", "TKIP");
            	eval('PAGE.NowSecurityType'+str_Aband+'="WPA"');
            	OBJ("cipher_type"+str_Aband).value="TKIP";
           		break;
            case "WPAPSK":
           		XS(wifip+"/encrtype", "TKIP");
           		eval('PAGE.NowSecurityType'+str_Aband+'="WPAPSK"');
           		OBJ("cipher_type"+str_Aband).value="TKIP";
            	break;
            default:
            	XS(wifip+"/encrtype", "TKIP+AES");
            	eval('PAGE.NowSecurityType'+str_Aband+'="UNKNOWN"');
            	OBJ("cipher_type"+str_Aband).value="TKIP+AES";
            }		
            if (/PSK/.test(OBJ("security_type"+str_Aband).value))
			{
				XS(wifip+"/authtype", OBJ("security_type"+str_Aband).value);
                XS(wifip+"/nwkey/psk/passphrase",  "");
                XS(wifip+"/nwkey/psk/key",         OBJ("wpapsk"+str_Aband).value);                
                eval('PAGE.NowUsingPW'+str_Aband+'=OBJ("wpapsk"+str_Aband).value');
            }
            else
            {
            	XS(wifip+"/authtype", OBJ("security_type"+str_Aband).value);
                XS(wifip+"/nwkey/eap/radius",  OBJ("srv_ip"+str_Aband).value);
                XS(wifip+"/nwkey/eap/port",    OBJ("srv_port"+str_Aband).value);
                XS(wifip+"/nwkey/eap/secret",  OBJ("srv_sec"+str_Aband).value);
                eval('PAGE.NowUsingPW'+str_Aband+'=OBJ("srv_sec"+str_Aband).value');
            }
        }
		else
		{
			XS(wifip+"/authtype", "OPEN");
			XS(wifip+"/encrtype", "NONE");
			eval('PAGE.NowSecurityType'+str_Aband+'="OPEN"');
		}
		return true;
	},
	
	OnClickEnWLAN: function(str_Aband)
	{
		if (AUTH.AuthorizedGroup >= 100) return;
		if (OBJ("en_wifi"+str_Aband).checked)
		{
			if(this.feature_nosch==0)
			{
				OBJ("sch"+str_Aband).disabled		= false;
				OBJ("go2sch"+str_Aband).disabled	= false;
			}
			
			OBJ("ssid"+str_Aband).disabled	= false;
			OBJ("channel"+str_Aband).disabled = false;
			OBJ("wlan_mode"+str_Aband).disabled	= false;

			if (/n/.test(OBJ("wlan_mode"+str_Aband).value))
			{
				OBJ("bw"+str_Aband).disabled	= false;
				OBJ("en_wmm"+str_Aband).disabled = true;
			}
			else if( /ac/.test(OBJ("wlan_mode"+str_Aband).value))
			{
				OBJ("bw"+str_Aband).disabled	= true;
				OBJ("en_wmm"+str_Aband).disabled = true;
			}
			else
				OBJ("en_wmm"+str_Aband).disabled = false;			

			OBJ("ssid_visible"+str_Aband).disabled = false;
			OBJ("security_type"+str_Aband).disabled= false;
			
			if(str_Aband == "") COMM_SetSelectValue(OBJ("security_type"+str_Aband), this.sec_type);
			else				COMM_SetSelectValue(OBJ("security_type"+str_Aband), this.sec_type_Aband);
			PAGE.OnChangeChannel(str_Aband);
		}
		else
		{
			if(this.feature_nosch==0)
			{
				OBJ("sch"+str_Aband).disabled		= true;
				OBJ("go2sch"+str_Aband).disabled	= true;
			}
			
			OBJ("ssid"+str_Aband).disabled	= true;
			OBJ("channel"+str_Aband).disabled	= true;
			//OBJ("txrate"+str_Aband).disabled	= true;
			OBJ("wlan_mode"+str_Aband).disabled	= true;
			OBJ("bw"+str_Aband).disabled	= true;
			OBJ("en_wmm"+str_Aband).disabled = true;
			OBJ("ssid_visible"+str_Aband).disabled = true;
			
			OBJ("security_type"+str_Aband).disabled= true;
			
			if(str_Aband == "") this.sec_type 		= OBJ("security_type"+str_Aband).value;
			else 				this.sec_type_Aband = OBJ("security_type"+str_Aband).value;

			COMM_SetSelectValue(OBJ("security_type"+str_Aband), "");
		}
		this.OnChangeSecurityType(str_Aband);
	},
	OnChangeSecurityType: function(str_Aband)
	{
		switch (OBJ("security_type"+str_Aband).value)
        {
            case "":
                OBJ("wep"+str_Aband).style.display = "none";
                OBJ("wpa"+str_Aband).style.display = "none";
                break;
            case "wep":
                OBJ("wep"+str_Aband).style.display = "block";
                OBJ("wpa"+str_Aband).style.display = "none";
				OBJ("network_key"+str_Aband).style.display = "";
                break;
            case "WPAPSK":
            case "WPA2PSK":
            case "WPA+2PSK":
                OBJ("wep"+str_Aband).style.display = "none";
                OBJ("wpa"+str_Aband).style.display = "block";
				SetDisplayStyle("div", "psk"+str_Aband, "block");
				SetDisplayStyle("div", "eap"+str_Aband, "none");
				OBJ("network_key"+str_Aband).style.display = "";
            	break;
            case "WPA":
            case "WPA2":
            case "WPA+2":            
                OBJ("wep"+str_Aband).style.display = "none";
                OBJ("wpa"+str_Aband).style.display = "block";
            	SetDisplayStyle("div", "psk"+str_Aband, "none");
				SetDisplayStyle("div", "eap"+str_Aband, "block");
				OBJ("network_key"+str_Aband).style.display = "none";
               break;
		}
	},
	OnChangeWEPAuth: function(str_Aband)
	{
		if(OBJ("auth_type"+str_Aband).value == "SHARED" && this.wps==true)
		{
			BODY.ShowAlert("<? echo I18N('j','Can not choose shared key when WPS is enabled.'); ?>");
			OBJ("auth_type"+str_Aband).value = "WEPAUTO";
		}
	},
	OnChangeWEPKey: function(str_Aband)
	{
		
		var no = S2I(OBJ("wep_def_key"+str_Aband).value) - 1;
		
		switch (OBJ("wep_key_len"+str_Aband).value)
		{
			case "64":
				OBJ("wep_64"+str_Aband).style.display = "block";
				OBJ("wep_64"+str_Aband+"_text").style.display = "block";
				OBJ("wep_128"+str_Aband).style.display = "none";
				OBJ("wep_128"+str_Aband+"_text").style.display = "none";
				SetDisplayStyle(null, "wepkey_64"+str_Aband, "none");
				document.getElementsByName("wepkey_64"+str_Aband)[no].style.display = "inline";
				break;
			case "128":
				OBJ("wep_64"+str_Aband).style.display = "none";
				OBJ("wep_64"+str_Aband+"_text").style.display = "none";
				OBJ("wep_128"+str_Aband).style.display = "block";
				OBJ("wep_128"+str_Aband+"_text").style.display = "block";
				SetDisplayStyle(null, "wepkey_128"+str_Aband, "none");
				document.getElementsByName("wepkey_128"+str_Aband)[no].style.display = "inline";
				break;
		}
	},
	OnChangeWLMode: function(str_Aband)
	{
		var count = OBJ("bw"+str_Aband).options.length;
		while (count > 0)
		{
			if(OBJ("bw"+str_Aband).options[count-1].value=="80")
			{
				OBJ("bw"+str_Aband).remove(count-1);
				break;
			}
			count-=1;
		}
		var phywlan = "";
		if(str_Aband==="")	phywlan = GPBT(this.wifi_module, "phyinf", "uid","BAND24G-1.1", false);
		else				phywlan = GPBT(this.wifi_module, "phyinf", "uid","BAND5G-1.1", false);
		if (/n/.test(OBJ("wlan_mode"+str_Aband).value))
		{
			this.bandWidth	= XG(phywlan+"/media/dot11n/bandwidth");
			COMM_SetSelectValue(OBJ("bw"+str_Aband), this.bandWidth);
			OBJ("bw"+str_Aband).disabled	= false;
			OBJ("en_wmm"+str_Aband).checked = true;
			OBJ("en_wmm"+str_Aband).disabled = true;
		}
		else if(/ac/.test(OBJ("wlan_mode"+str_Aband).value))
		{
			COMM_AddSelectOption("bw_Aband","80 MHz","80");
			COMM_SetSelectValue(OBJ("bw"+str_Aband), "80");
			OBJ("bw"+str_Aband).disabled	= true;
			OBJ("en_wmm"+str_Aband).checked = true;
			OBJ("en_wmm"+str_Aband).disabled = true;			
		}
		else
		{
			OBJ("bw"+str_Aband).disabled	= true;
			OBJ("en_wmm"+str_Aband).disabled = false;
		}
		var st = OBJ("security_type"+str_Aband).value;
		DrawSecurityList(OBJ("wlan_mode"+str_Aband).value, str_Aband);
		for (var i=0; i < OBJ("security_type"+str_Aband).length; i++)
		{
			var v = OBJ("security_type"+str_Aband);
			if (v[i].value == st)
			{
				OBJ("security_type"+str_Aband).value = st;
				break;
			}
		}
		this.OnChangeSecurityType(str_Aband);
	},
	OnClickRadiusAdvanced: function(str_Aband)
    {
        if (this.radius_adv_flag) {
            OBJ("div_second_radius"+str_Aband).style.display = "none";
            OBJ("radius_adv"+str_Aband).value = "Advanced >>";
            this.radius_adv_flag = false;
        }
        else {
            OBJ("div_second_radius"+str_Aband).style.display = "block";
            OBJ("radius_adv"+str_Aband).value = "<< Advanced";
            this.radius_adv_flag = true;
		}
    },
	OnChangeChannel: function(str_Aband)
	{
		if( OBJ("channel"+str_Aband).value == "165" )
		{
			if(OBJ("bw"+str_Aband).value =="20+40")
				OBJ("bw"+str_Aband).value = "20";
			OBJ("bw"+str_Aband).disabled = true;
		}
		else
		{	if(OBJ("bw"+str_Aband).value=="80")
				OBJ("bw"+str_Aband).disabled = true;
			else
				OBJ("bw"+str_Aband).disabled = false;
		}
		NewSelect.refresh();
	},
    
    /*
    For ssid, WEP key, WPA key, we don't allow whitespace in front OR behind !!!
    */
    ValidityCheck: function(wlan_uid,wlan_g_uid)
	{
		var wifi_module 	= this.wifi_module;
		var phyinf 			= GPBT(wifi_module,"phyinf","uid",wlan_uid,false);
		var wifip 			= GPBT(wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);

		if(wlan_g_uid == "5")		str_Aband = "_Aband";
		else				str_Aband = "";
		
		var obj_ssid 	= OBJ("ssid"+str_Aband).value;
		var obj_wpa_key = OBJ("wpapsk"+str_Aband).value;
		

		var wep_key		= OBJ("wep_def_key"+str_Aband).value;
		var wep_key_len	= OBJ("wep_key_len"+str_Aband).value;			
		var obj_wep_key = OBJ("wep_"+wep_key_len+"_"+wep_key+str_Aband).value;		
		
		if(obj_ssid.charAt(0)===" "|| obj_ssid.charAt(obj_ssid.length-1)===" ")
		{
			alert("<?echo I18N("h", "The prefix or postfix of the 'Wireless Network Name' can not be blank.");?>");
			return false;
		}
		if(obj_ssid==XG(wifip+"/ssid") && XG(phyinf+"/active")=="1")
		{
			alert("<?echo I18N("j", "The guest network name cannot be same as the main network.");?>");
			return false;
		}
		if(OBJ("security_type"+str_Aband).value==="wep") //wep_64_1_Aband
		{
			if (obj_wep_key.charAt(0) === " "|| obj_wep_key.charAt(obj_wep_key.length-1)===" ")
			{
				alert("<?echo I18N('j','The prefix or postfix of the WEP Key can not be blank.');?>");
				return false;
			}
		}
        else if (/WPA/.test(OBJ("security_type"+str_Aband).value))
		{
            if (obj_wpa_key.charAt(0)===" " || obj_wpa_key.charAt(obj_wpa_key.length-1)===" ")
            {
                alert("<?echo I18N("h", "The prefix or postfix of the 'Network Key' can not be blank.");?>");
                return false;
            }
        }
		return true;
	}
}

function SetBNode(value)
{
	if (COMM_ToBOOL(value))
		return "1";
	else
		return "0";
}

function SetDisplayStyle(tag, name, style)
{
	if (tag)	var obj = GetElementsByName_iefix(tag, name);
	else		var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		obj[i].style.display = style;
	}
}
function GetElementsByName_iefix(tag, name)
{
	var elem = document.getElementsByTagName(tag);
	var arr = new Array();
	for(i = 0,iarr = 0; i < elem.length; i++)
	{
		att = elem[i].getAttribute("name");
		if(att == name)
		{
			arr[iarr] = elem[i];
			iarr++;
		}
	}
	return arr;
}

function DrawSecurityList(wlan_mode, str_Aband)
{
	var security_list = null;
	var cipher_list = null;
	
	//if (wlan_mode === "n")
	//for atheros wifi solution, if mode have n, can't use WEP. 
	if (/n/.test(wlan_mode))
	{
		if(wlan_mode === "n")
		{
			security_list = ['WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>",
						'WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>",
						'WPA2', "<?echo I18N("h", "WPA2 - Enterprise");?>",
						'WPA+2', "<?echo I18N("h", "WPA/WPA2 - Enterprise");?>"];
		}
		else
		{
			security_list = ['WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>",
						'WPAPSK', "<?echo I18N("h", "WPA - Personal");?>",
						'WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>",
						'WPA', "<?echo I18N("h", "WPA - Enterprise");?>",
						'WPA2', "<?echo I18N("h", "WPA2 - Enterprise");?>",
						'WPA+2', "<?echo I18N("h", "WPA/WPA2 - Enterprise");?>"];
		}
		/*cipher_list = ['AES'];*/
		cipher_list = ['TKIP+AES','TKIP','AES'];
	}
	else
	{
		security_list = ['WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>",
						'WPAPSK', "<?echo I18N("h", "WPA - Personal");?>",
						'WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>",
						'WPA', "<?echo I18N("h", "WPA - Enterprise");?>",
						'WPA2', "<?echo I18N("h", "WPA2 - Enterprise");?>",
						'WPA+2', "<?echo I18N("h", "WPA/WPA2 - Enterprise");?>",
						'wep', "<?echo I18N("h", "WEP");?>"];
		cipher_list = ['TKIP+AES','TKIP','AES'];
	}
	
	//modify security_type
	var sec_length = OBJ("security_type"+str_Aband).length;
	for(var idx=1; idx<sec_length; idx++)
	{
		OBJ("security_type"+str_Aband).remove(1);
	}
	for(var idx=0; idx<security_list.length; idx++)
	{
		var item 	= document.createElement("option");
		item.value 	= security_list[idx++];
		item.text 	= security_list[idx];
		try		{ OBJ("security_type"+str_Aband).add(item, null); }
		catch(e){ OBJ("security_type"+str_Aband).add(item); }
	}
	// modify cipher_type
	var ci_length = OBJ("cipher_type"+str_Aband).length;
	for(var idx=0; idx<ci_length; idx++)
	{
		OBJ("cipher_type"+str_Aband).remove(0);
	}
	for(var idx=0; idx<cipher_list.length; idx++)
	{
		var item = document.createElement("option");
		item.value = cipher_list[idx];
		if (item.value=="TKIP+AES") item.text = "AUTO(TKIP/AES)";
		else						item.text = cipher_list[idx];
		try		{ OBJ("cipher_type"+str_Aband).add(item, null); }
		catch(e){ OBJ("cipher_type"+str_Aband).add(item); }
	}
}
</script>
