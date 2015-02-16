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
		if($lang=="ru" || $lang=="es" || $lang=="hu") echo "font-size: 16px;";
		else if($lang=="pl") echo "font-size: 10px;";
		else if($lang=="cs") echo "font-size: 15px;";
		else echo "font-size: 18px;";
    ?>
    font-weight: bold;
    margin: 8px 0;
    padding: 0;
    text-align: left;
}

div.textinput span.name_c
{
    color: white;
    text-align: left;
    margin-top: 4px;
    <?
        if($lang=="cs") echo "font-size: 12px;";
        else echo "font-size: 14px;";
    ?>
    margin-left: 10px;
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
	services: "WIFI.PHYINF, PHYINF.WIFI",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	feature_nosch: null,
	dual_band: 0, 
	radius_adv_flag: 0,	
	sec_type_Aband:null,
	sec_type: null,
	
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.wifi_module = PXML.FindModule("WIFI.PHYINF");		
		this.feature_nosch=1;
		
		if(!this.Initial("BAND24G-1.2")) return false;  
		if(!this.Initial("BAND5G-1.2")) return false;  
	},
	
	Initial: function(uid_wlan)
	{
		var phyinf 	= GPBT(this.wifi_module			,"phyinf", "uid",uid_wlan, false);
		var wifip 	= GPBT(this.wifi_module+"/wifi"	,"entry", "uid" ,XG(phyinf+"/wifi"), false); 
		if (!wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		
		//to get the wlan mode, we must access to host zone phyinf
		var wlanmode = this.GetWlanMode(uid_wlan);
		var str_Aband = (GetBand(uid_wlan)=="a") ? "_Aband" : "" ;
		
		OBJ("en_gzone"+str_Aband).checked 	= COMM_ToBOOL(XG(phyinf+"/active"));		
		OBJ("ssid"+str_Aband).value 				= XG(wifip+"/ssid");

		DrawSecurityList(wlanmode, str_Aband);
		
    var sec_type = "";
    if (!OBJ("en_gzone"+str_Aband).checked)
        sec_type = "";
    else if (XG(wifip+"/encrtype")=="WEP")
        sec_type = "wep";
    else if (/WPA/.test(XG(wifip+"/authtype")))
        sec_type = XG(wifip+"/authtype");
    else
        sec_type = "";
    
    if(str_Aband == "_Aband") //a band	
    	this.sec_type_Aband = sec_type;    
    else 											//g band
    	this.sec_type = sec_type;            	
        	
		COMM_SetSelectValue(OBJ("security_type"+str_Aband), sec_type);	
		
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
		if(sec_type == "wep")
			BODY.ShowAlert("Guest Access doesn't support WEP .Please check ..!");	

		
		COMM_SetSelectValue(OBJ("cipher_type"+str_Aband), cipher);

		OBJ("wpapsk"+str_Aband).value	= XG(wifip+"/nwkey/psk/key");

		this.OnClickEnGzone(str_Aband);
		this.OnChangeSecurityType(str_Aband);
		this.OnChangeWEPKey(str_Aband);
		this.OnChangeWPAAuth(str_Aband);
		this.OnHostEnableCheck(str_Aband, uid_wlan);

		return true;
	},
	OnHostEnableCheck: function (str_Aband, uid_wlan)
	{
		var host_uid = "";
		var host_active = "";
		var guest_active = "";
		var p="";
		
		/* if host is disabled, then our guestzone must also disabled	*/
		if(uid_wlan=="BAND24G-1.2") host_uid = "BAND24G-1.1";	
		else 												host_uid = "BAND5G-1.1";	
		
		p = GPBT(this.wifi_module	,"phyinf", "uid",host_uid, false);
		host_active = XG(p +"/active");
	
		if (host_active == "0")
		{
			OBJ("en_gzone"+str_Aband).checked 		= false;			
			OBJ("en_gzone"+str_Aband).disabled		= true;
			OBJ("ssid"+str_Aband).disabled			= true;
			OBJ("security_type"+str_Aband).disabled	= true;
			//OBJ("sch_gz"+str_Aband).disabled		= true;
			//OBJ("go2sch_gz"+str_Aband).disabled	= true;
			
			COMM_SetSelectValue(OBJ("security_type"+str_Aband), "");		
			this.OnChangeSecurityType(str_Aband);
			OBJ("security_type"+str_Aband).disabled	= true;
		}
	},
	
	PreSubmit: function(uid_wlan)
	{
		if(!this.ValidityCheck("BAND24G-1.1","")) return null; 
		if(!this.ValidityCheck("BAND5G-1.1","")) return null;	
		if(!this.ValidityCheck("BAND24G-1.1","5")) return null; 	
		if(!this.ValidityCheck("BAND5G-1.1","5")) return null;
		if(!this.SaveXML("BAND24G-1.2")) return null; 
		if(!this.SaveXML("BAND5G-1.2")) return null; 
		
		PXML.CheckModule("WIFI.PHYINF", null,null, "ignore");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function()
	{
	},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
		
	SaveXML: function(uid_wlan)
	{
			var phyinf = GPBT(this.wifi_module,"phyinf","uid",uid_wlan,false);
			var wifip = GPBT(this.wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);
			var freq = XG(phyinf+"/media/freq");
			
			var str_Aband = (GetBand(uid_wlan)=="a") ? "_Aband" : "" ;
			
			if (OBJ("en_gzone"+str_Aband).checked)
			{
				XS(phyinf+"/active", "1");
			}
			else
			{
				XS(phyinf+"/active", "0");
			}
			
			XS(wifip+"/ssid",		OBJ("ssid"+str_Aband).value);
						
			if (OBJ("security_type"+str_Aband).value=="wep")
			{
				if (OBJ("auth_type"+str_Aband).value=="SHARED")
					XS(wifip+"/authtype", "SHARED");
				else
					XS(wifip+"/authtype", "WEPAUTO");
				XS(wifip+"/encrtype",			"WEP");
				XS(wifip+"/nwkey/wep/size",	"");
				XS(wifip+"/nwkey/wep/ascii",	"");
				XS(wifip+"/nwkey/wep/defkey",	OBJ("wep_def_key"+str_Aband).value);
				for (var i=1, len=OBJ("wep_key_len"+str_Aband).value; i<5; i++)
				{
					if (i==OBJ("wep_def_key"+str_Aband).value)
						XS(wifip+"/nwkey/wep/key:"+i, OBJ("wep_"+len+"_"+i+str_Aband).value);
					else
						XS(wifip+"/nwkey/wep/key:"+i, "");
				}
			}
			else if (/WPA/.test(OBJ("security_type"+str_Aband).value))
			{
       /* XS(wifip+"/encrtype", OBJ("cipher_type"+str_Aband).value);*/
		switch (OBJ("security_type"+str_Aband).value)
       	{
           case "WPA2":
           	XS(wifip+"/encrtype", "AES");
           	OBJ("cipher_type"+str_Aband).value="AES";
            break;
           case "WPA2PSK":
           	XS(wifip+"/encrtype", "AES");
           	OBJ("cipher_type"+str_Aband).value="AES";
            break;
           case "WPA":
            XS(wifip+"/encrtype", "TKIP");
            OBJ("cipher_type"+str_Aband).value="TKIP";
           	break;
           case "WPAPSK":
           	XS(wifip+"/encrtype", "TKIP");
           	OBJ("cipher_type"+str_Aband).value="TKIP";
            break;
           default:
            	XS(wifip+"/encrtype", "TKIP+AES");
            	OBJ("cipher_type"+str_Aband).value="TKIP+AES";
        }	
        if (/PSK/.test(OBJ("security_type"+str_Aband).value))
        {
					XS(wifip+"/authtype", OBJ("security_type"+str_Aband).value);
	                XS(wifip+"/nwkey/psk/passphrase",  "");
	                XS(wifip+"/nwkey/psk/key",         OBJ("wpapsk"+str_Aband).value);
				}
        else
        {
        	BODY.ShowAlert("Guest Access doesn't support EAP enterprise. Please check ..!!");
        	XS(wifip+"/authtype", OBJ("security_type"+str_Aband).value);
            XS(wifip+"/nwkey/eap/radius",  OBJ("srv_ip"+str_Aband).value);
            XS(wifip+"/nwkey/eap/port",    OBJ("srv_port"+str_Aband).value);
            XS(wifip+"/nwkey/eap/secret",  OBJ("srv_sec"+str_Aband).value);
        }
			}
			else
			{
				XS(wifip+"/authtype", "OPEN");
				XS(wifip+"/encrtype", "NONE");
			}
			
			if(this.feature_nosch==0)
				XS(phyinf+"/schedule",	OBJ("sch_gz"+str_Aband).value);
				
		return true;
	},
	OnClickEnGzone: function(str_Aband)
	{
		if (OBJ("en_gzone"+str_Aband).checked)
		{
			OBJ("ssid"+str_Aband).disabled			= false;
			OBJ("security_type"+str_Aband).disabled	= false;
			if(this.feature_nosch==0)
			{
				OBJ("sch_gz"+str_Aband).disabled	= false;
				OBJ("go2sch_gz"+str_Aband).disabled	= false;
			}
			
			OBJ("security_type"+str_Aband).disabled= false;

			if(str_Aband == "")
				COMM_SetSelectValue(OBJ("security_type"+str_Aband), this.sec_type);
			else
				COMM_SetSelectValue(OBJ("security_type"+str_Aband), this.sec_type_Aband);
		}
		else
		{
			OBJ("ssid"+str_Aband).disabled			= true;
			OBJ("security_type"+str_Aband).disabled	= true;
			if(this.feature_nosch==0)
			{
				OBJ("sch_gz"+str_Aband).disabled	= true;
				OBJ("go2sch_gz"+str_Aband).disabled	= true;
			}

			OBJ("security_type"+str_Aband).disabled= true;
			
			if(str_Aband == "")
				this.sec_type = OBJ("security_type"+str_Aband).value;
			else 
				this.sec_type_Aband = OBJ("security_type"+str_Aband).value;

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
                break;
            case "WPAPSK":
            case "WPA2PSK":
            case "WPA+2PSK":
                OBJ("wep"+str_Aband).style.display = "none";
                OBJ("wpa"+str_Aband).style.display = "block";
				SetDisplayStyle("div", "psk"+str_Aband, "block");
				SetDisplayStyle("div", "eap"+str_Aband, "none");                
            	break;
            case "WPA":
            case "WPA2":
            case "WPA+2":            
                OBJ("wep"+str_Aband).style.display = "none";
                OBJ("wpa"+str_Aband).style.display = "block";
            	SetDisplayStyle("div", "psk"+str_Aband, "none");
				SetDisplayStyle("div", "eap"+str_Aband, "block");                
                break;
        }
		
	},
	OnChangeWEPAuth: function(str_Aband)
	{
		if(OBJ("auth_type"+str_Aband).value == "SHARED" && this.wps==true)
		{
			BODY.ShowAlert("Can't choose shared key when wps is enable !!");
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
				OBJ("wep_128"+str_Aband).style.display = "none";
				SetDisplayStyle(null, "wepkey_64"+str_Aband, "none");
				document.getElementsByName("wepkey_64"+str_Aband)[no].style.display = "inline";
				break;
			case "128":
				OBJ("wep_64"+str_Aband).style.display = "none";
				OBJ("wep_128"+str_Aband).style.display = "block";
				SetDisplayStyle(null, "wepkey_128"+str_Aband, "none");
				document.getElementsByName("wepkey_128"+str_Aband)[no].style.display = "inline";
		}
	},
	OnChangeWPAAuth: function(str_Aband)
	{
		switch (OBJ("psk_eap"+str_Aband).value)
		{
			case "psk":
				SetDisplayStyle("div", "psk"+str_Aband, "block");
				SetDisplayStyle("div", "eap"+str_Aband, "none");
				break;
			case "eap":
				SetDisplayStyle("div", "psk"+str_Aband, "none");
				SetDisplayStyle("div", "eap"+str_Aband, "block");
		}
	},	
	/*
    For ssid, WEP key, WPA key, we don't allow whitespace in front OR behind !!!
    */
    ValidityCheck: function(wlan_uid,wlan_p_uid)
	{	
		//hendry todo :
		//return true;
		var wifi_module 	= this.wifi_module;
		var phyinf 			= GPBT(wifi_module,"phyinf","uid",wlan_uid,false);
		var wifip 			= GPBT(wifi_module+"/wifi", "entry", "uid", XG(phyinf+"/wifi"), false);
		if(wlan_p_uid == "5")		str_Aband = "_Aband";
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
				alert("The prefix or postfix of the 'WEP Key' can not be blank.");
				return false;
			}
		}
		else if(OBJ("security_type"+str_Aband).value==="wpa_personal")
		{ 
			if (obj_wpa_key.charAt(0)===" " || obj_wpa_key.charAt(obj_wpa_key.length-1)===" ")
			{
				alert("<?echo I18N("h", "The prefix or postfix of the 'Pre-Shared Key' can not be blank.");?>");
				return false;
			}
		}
		else if(OBJ("security_type"+str_Aband).value==="wpa_enterprise")
		{	
			var radius_key 			= OBJ("radius_srv_sec"+str_Aband).value;
			var radius_key_second	= OBJ("radius_srv_sec_second"+str_Aband).value;
			
			if (radius_key.charAt(0)===" " || radius_key.charAt(radius_key.length-1)===" ")
			{
				alert("<?echo I18N("h", "The prefix or postfix of the 'RADIUS server Shared Secret' can not be blank.");?>");
				return false;
			}
			
			if(radius_key_second!=="")
			{
				if (radius_key_second.charAt(0)===" " || radius_key_second.charAt(radius_key_second.length-1)===" ")
				{
					alert("<?echo I18N("h", "The prefix or postfix of the 'Second RADIUS server Shared Secret' can not be blank.");?>");
					return false;
				}
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
	},
	GetWlanMode: function(guestzone_uidwlan)
	{
		//if guest uid=BAND24G-1.2, then we need to get BAND24G-1.1 wlan mode !!
		var index 	= guestzone_uidwlan.indexOf(".")+1;
		var tmp 	= guestzone_uidwlan.slice(0,index);
		var minor = guestzone_uidwlan.slice(-1);
		var wlanmode = "bgn";
		if(minor == 2)
		{
			var host_uid 	= tmp.concat("1");
			var host_phyinf	= GPBT(this.wifi_module	,"phyinf", "uid",host_uid, false);
			var phyinf 	= GPBT(this.wifi_module			,"phyinf", "uid",guestzone_uidwlan, false);		
			var wlanmode 	= XG(host_phyinf+"/media/wlmode");		
			return wlanmode;
		}
		else 
			return "bgn";	//just default
	}
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

	/*security_list = ['WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>",
						'WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>"];*/
	if(wlan_mode === "n")
	{
		security_list = ['WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>",
					'WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>"];
	}
	else
	{
		security_list = ['WPA2PSK', "<?echo I18N("h", "WPA2 - Personal");?>",
					'WPAPSK', "<?echo I18N("h", "WPA - Personal");?>",
					'WPA+2PSK', "<?echo I18N("h", "WPA/WPA2 - Personal");?>"];
	}
/*
					'WPA', "<?echo I18N("h", "WPA - Enterprise");?>",
					'WPA2', "<?echo I18N("h", "WPA2 - Enterprise");?>",
					'WPA+2', "<?echo I18N("h", "WPA/WPA2 - Enterprise");?>"
*/
	/*cipher_list = ['AES'];*/
	cipher_list = ['TKIP+AES','TKIP','AES'];
	
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

function GetBand(uid_wlan)
{
	var index 	= 0;
	index 		= uid_wlan.indexOf("-");
	var prefix 	= uid_wlan.substring(0,index);

	if(prefix == "BAND5G")
		return "a";	
	else
		return "g";
}
</script>
