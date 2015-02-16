<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
 
/*
This page's flow algorithm:
			     save settings            pop no saving message
(have  configed)-------------->(not dirty)--------------------->(return to setting page)
					|
					|------->(is dirty)
								|
								|
								|
								| pop warning message                 Click OK
(have not configed)------------------------------------>(show result)--------->(return to main dashboard page)


What is result's table structure??  It may looks like below:
		<table>
		<tr>
			<th></th>
			<th><?echo I18N('h','Network Name (SSID)');?></th>
			<th><?echo I18N('h','Password');?></th>
		</tr>
		<tr>
			<td>2.4 GHz</td>
			<td>'+OBJ('ssid').value+'</td>
			<td>'+OBJ('wpapsk').value+'</td>
		</tr>
		<tr>
			<td>5 GHz</td>
			<td>'+OBJ('ssid_Aband').value+'</td>
			<td>'+OBJ('wpapsk_Aband').value+'</td>
		</tr>
		</table>
*/
p.wireless_title
{
	color: white;
	font-size:20px;
	font-weight: bold;
	text-align: left;
	padding:0px;
	border:0px;
	margin:0px;
	margin-top: 8px; 
	margin-bottom: 8px;
}
span.name
{
	color: white;
	font-size:14px;
	text-align: left;
	padding:0px;
	border:0px;
	margin:0px;
}
span.little_msg
{
	color: white;
	font-size:12px;
	text-align: left;
	padding:0px;
	border:0px;
	margin:0px;
	margin-top: 0px; 
	margin-left: 120px;
	margin-bottom: 20px;
}
div.wireless_input td.text_line
{
	clear: both;
	position: relative;
	height: 35px;
	line-height: 35px;
	*line-height: 17px;/*For IE 7*/
	width: 380px;
}
div.wireless_input span.name_c
{    
	color: white;
	font-weight: bold;
	text-align: left;
	font-size: 16px;
  margin-left: 120px;
}
div.wireless_input span.value_c
{
	color: white;
}
</style>

<?include "/htdocs/phplib/phyinf.php";?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{	
	services: "WIFI.PHYINF,PHYINF.WIFI",
	OnLoad: function() 
	{
		if(PAGE.STAGE==1)
		{
			PAGE.STAGE = 0;
			var msgArrayResult=[
				"<?echo I18N('h','To connect to your new wireless network, open your computer/device\'s wireless/Wi-Fi settings and connect to the new network name with the new password.');?>",
				"<table class='general'><tr><th width='80px'></th><th width='230px'><?echo I18N('h','Network Name (SSID)');?></th><th  width='200px'><?echo I18N('h','Password');?></th></tr><tr><td>2.4 GHz</td><td>"+OBJ('ssid').value+"</td><td>"+OBJ('wpapsk').value+"</td></tr><tr><td>5 GHz</td><td>"+OBJ('ssid_Aband').value+"</td><td>"+OBJ('wpapsk_Aband').value+"</td></tr></table>",
				"<input type='button' class='button_blue' id='reload' onclick='BODY.OnReload();' value='<?echo I18N('h', 'Ok');?>'>"
			];
			BODY.ShowMessage("<?echo I18N('h', 'Wireless Setting Changed'); ?>",msgArrayResult);
			PAGE.back_to_maindashboard = true;			
		}
		else
		{
			if(PAGE.back_to_maindashboard) self.location.href='/main_dashboard.php';
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result){ return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		
		if(!this.Initial("BAND24G-1.1","WIFI.PHYINF")) return false;
		
		this.dual_band = COMM_ToBOOL('<?=$FEATURE_DUAL_BAND?>');
		
		if(this.dual_band)
		{
			OBJ("div_5G").style.display = "block";
			if(!this.Initial("BAND5G-1.1","WIFI.PHYINF")) return false;
			if(this.wizard_config!="1") OBJ("wpapsk_Aband").value = OBJ("wpapsk").value;//For WD ITR 55281 and WD ITR 55294
		}
		else
		{
			OBJ("div_5G").style.display = "none";
		}
		return true;
	},
	PreSubmit: function()
	{	
		if(this.wizard_config=="1" && PAGE.STAGE==0)	
		{/* if have been configed and data also dirty, then show warning message */			
			BODY.ShowMessage("<?echo I18N('h', 'Warning'); ?>",PAGE.msgArray);
			return null;/* return null because we want to save settings after clicked warning message's save button. */
		}
		if(!this.ValidityCheck("BAND24G-1.2","")) return null; 
        if(this.dual_band)
        {
            if(!this.ValidityCheck("BAND5G-1.2","")) return null;
            if(!this.ValidityCheck("BAND24G-1.2","5")) return null;
            if(!this.ValidityCheck("BAND5G-1.2","5")) return null;
		}				
		
		if(!this.SaveXML("BAND24G-1.1")) return null; 				
		if(!this.WPSCHK("BAND24G-1.1")) return null; 
		
		if(this.dual_band)
		{
			if(!this.SaveXML("BAND5G-1.1")) return null;
			if(!this.WPSCHK("BAND5G-1.1")) return null;
			this.WPS_CONFIGURED_CHK("BAND24G-1.1","BAND5G-1.1");
		}
		if(this.wizard_config!="1") this.back_to_maindashboard = true;
		return PXML.doc;
	},
	GoNext: function(step)
	{
		PAGE.STAGE = step;
		if(step==0)//check config or not config
		{
			if(this.wizard_config=="1")//config
			{
				BODY.OnSubmit();/* run BODY.OnSubmit to check is dirty or not */
			}
			else//not config, don't need check dirty. Directly show warning message.
			{
				BODY.ShowMessage("<?echo I18N('h', 'Warning'); ?>",PAGE.msgArray);
			}
		}
		else if(step==1)
		{
			BODY.OnSubmit();
		}
	},
	<?  
	if(query('/device/wizardconfig')!="1")
	{/* if not config, we set data to always dirty so that we can save settings */
		echo 'IsDirty: function()
		{
			return true;
		},';
	}
	else
	{
		echo 'IsDirty: null,';
	}
	?>
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	STAGE: 0,
	msgArray: [
		"<?echo I18N('h', 'Your wireless devices in the network will be disconnected. Please make changes to your devices\' wireless/Wi-Fi settings to reconnect after saving the new settings.');?>",
		"<input type='button' class='button_black' id='reload' onclick='BODY.ShowContent(); PAGE.STAGE=0;' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='PAGE.GoNext(1);' value='<?echo I18N('h', 'Save');?>'>"
	],
	Colors: [
			"Maroon","Great","Blue","Beige","Pink","Light",
			"Green","Fast","Purple","Swith","Yellow","Jumpy",
			"White","Fresh","Gray","Large","Orange","Ivory",
			"Violet","Amber","Brown","Neon","Tiny","Crimson",
			"Small","Silver","Golden","Rainbow"
	],
	Animals: [
			"Grouper","Shark","Squid","Starfish","Tuna",
			"Salmon","Whale","Stingray","Jellyfish","Clam",
			"Shrimp","Lobster","Crab","Marlin","Bass",
			"Trout","Catfish","Carp","Halibut","Croaker",
			"Perch","Pike","Dolphin","Sardine","Wahoo",
			"Turtle","Orca","Seal","Penguin","Sealion",
			"Abalone","Scallop"
	],
	wifip: null,
	phyinf: null,
	wizard_config: "<? echo query('/device/wizardconfig');?>",
	g_mac:"<? echo query('/runtime/devdata/wlanmac');?>",
	back_to_maindashboard: false,
	str_Aband: null,
	dual_band: null,
	Initial: function(wlan_uid,wifi_module)
	{
		this.wifi_module = PXML.FindModule(wifi_module);
		if (!this.wifi_module)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		if(this.wizard_config=="1")
		{//if have been configed, get data from XML.
			this.phyinf = GPBT(this.wifi_module, "phyinf", "uid",wlan_uid, false);
	
			var wifi_profile 	= XG(this.phyinf+"/wifi");
			var freq 			= XG(this.phyinf+"/media/freq");
			var wifip 			= GPBT(this.wifi_module+"/wifi", "entry", "uid", wifi_profile, false);
			
			if(freq == "5") 	str_Aband = "_Aband";
			else				str_Aband = "";
			
			OBJ("ssid"+str_Aband).value = XG(wifip+"/ssid");
			OBJ("wpapsk"+str_Aband).value	= XG(wifip+"/nwkey/psk/key");
		}
		else
		{//not config yet,use rules to generated random data.
			if(wlan_uid.search("5")>=0) str_Aband = "_Aband";
			else						str_Aband = "";
			
			OBJ("wpapsk"+str_Aband).value = this.RandomInt()+this.RandomColors()+this.RandomAnimals();
			
			if(this.g_mac.lastIndexOf(":")>=0)
			{
				OBJ("ssid"+str_Aband).value = "WesternDigital"+"-"+this.g_mac.substring(this.g_mac.lastIndexOf(":")+1, this.g_mac.lastIndexOf(":")+3);
			}
			else
			{
				OBJ("ssid"+str_Aband).value = "WesternDigital";
			}
			if(str_Aband!="")
			{
				OBJ("ssid"+str_Aband).value = OBJ("ssid"+str_Aband).value + "_5G";
			}
		}
		return true;
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
			COMM_EqBOOL(OBJ("wpapsk"+str_Aband).getAttribute("modified"),true))
		{
			XS(wifip+"/wps/configured", "1");
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

		if(freq == "5")		str_Aband = "_Aband";
		else				str_Aband = "";
		
		XS(phyinf+"/active", "1");//wireless Enable
		XS(wifip+"/ssid", OBJ("ssid"+str_Aband).value);//set SSID
		XS(phyinf+"/media/channel", "0");//set channel to AUTO
		//Set Network Mode to Mixed 802.11a+n or 802.11 b+g+n
		if(str_Aband != "")	XS(phyinf+"/media/wlmode", "an");
		else	XS(phyinf+"/media/wlmode", "bgn");
		
		XS(phyinf+"/media/dot11n/bandwidth", "20+40");//Channel width to 20/40MHz(AUTO)

		XS(phyinf+"/media/wmm/enable",	"1");//WMM QOS always enable
		XS(wifip+"/ssidhidden",			"0");//Visible SSID
		XS(wifip+"/encrtype", "TKIP+AES");//referenced by WPA/WPA2 personal
		XS(wifip+"/authtype", "WPA+2PSK");//set to WPA/WPA2 personal
		XS(wifip+"/nwkey/psk/passphrase",  "");
		XS(wifip+"/nwkey/psk/key",OBJ("wpapsk"+str_Aband).value);//set password

		return true;
	},
	RandomInt: function()
	{
		var c = "0123456789";
		var rand_char = Math.floor(Math.random() * c.length);
		var str = c.substring(rand_char, rand_char + 1);
		return str;
	},
	RandomColors: function()
	{
		var rand_char = Math.floor(Math.random() * this.Colors.length);
		return this.Colors[rand_char];
	},
	RandomAnimals: function()
	{
		var rand_char = Math.floor(Math.random() * this.Animals.length);
		return this.Animals[rand_char];
	},
    OnSubmit: function() {},    
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
		if (obj_wpa_key.charAt(0)===" " || obj_wpa_key.charAt(obj_wpa_key.length-1)===" ")
		{
			alert("<?echo I18N("h", "The prefix or postfix of the 'Network Key' can not be blank.");?>");
			return false;
		}
		return true;
	}
}
</script>