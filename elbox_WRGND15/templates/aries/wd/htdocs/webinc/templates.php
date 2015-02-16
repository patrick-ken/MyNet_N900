<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/feature.php";
include "/htdocs/webinc/config.php";
include "/htdocs/phplib/wifi.php";
include "/htdocs/phplib/langpack.php";
$layout	= query("/device/layout");

LANGPACK_setsealpac();
include "/htdocs/webinc/menu.php";		/* The menu definitions */
$lang = query("/device/features/language");
if($lang=="auto")
{
	$lang = query("/runtime/device/auto_language");
	if($lang=="")	$lang="en";
}
function alert_msg_each($title, $content, $id, $setup_page)
{
	echo '\t\t<div id="'.$id.'" style="width:300px;margin-top:10px;margin-left:23px;margin-right:22px;background-color:#3C3C3C;">\n'.
	 	 '\t\t\t<div style="position:relative;height:47px;width:300px;background:url(/pic/alert_descript.png);">\n'.
		 '\t\t\t\t<span style="position:absolute;top:18px;left:45px;">'.$title.'</span>\n'.
		 '\t\t\t</div>\n'.
		 '\t\t\t<div style="height:6px;"></div>\n'.
		 '\t\t\t<div style="margin-left:16px;margin-right:16px;">\n'.
		 '\t\t\t\t<span id="'.$id.'_content">'.$content.'</span>\n'.
		 '\t\t\t</div>\n'.
		 '\t\t\t<div style="height:6px;"></div>\n'.
		 '\t\t\t<div style="height:52px;line-height:52px;text-align:right;background-color:#262626;">\n'.
		 '\t\t\t\t<span>\n'.	
		 '\t\t\t\t\t<input type="button" class="button_black" onclick="BODY.AlertAct(\'ignore\', \'\', \''.$id.'\')" value="'.I18N("h", "Ignore").'" />&nbsp;&nbsp;\n'.
		 '\t\t\t\t\t<input type="button" class="button_blue" onclick="self.location.href=\''.$setup_page.'\';" value="'.I18N("h", "OK").'" />\n'.
		 '\t\t\t\t</span>\n'.
		 '\t\t\t</div>\n'.
		 '\t\t</div>\n';	
}

function alert_msg()
{	
	include "/htdocs/webinc/feature.php";
	$wan = query("/runtime/device/activewan");
	if($wan == "") $wan = "WAN-1";
	$run_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $wan, 0);
	$qos_autobandwidth = query($run_infp."/bwc/bandwidth");
		alert_msg_each(I18N("h", "Firmware update available"), 
			I18N("h", "New firmware is available. Click OK to upgrade."), 
			"firmware_new", "tools_fwup.php" );
		alert_msg_each(I18N("h", "Wireless security is not configured."), 
			I18N("h", "The router's wireless settings are set to default. Click OK to change the settings and secure your network."), 
			"wireless_unconfig", "main_wireless.php" );
		alert_msg_each(I18N("h", "Wireless security has been set to open."), 
			I18N("h", "Wireless security has not been configured on router. Click OK to set up wireless security."), 
			"wireless_open", "main_wireless.php" );
		alert_msg_each(I18N("h", "Admin password is default value."), 
			I18N("h", "The router's administrator password is set to default. Click OK to change."), 
			"admin_password", "tools_admin.php" );
		alert_msg_each(I18N("h", "Storage is not configured"), 
			I18N("h", "Storage attached to the router has not been configured. Click OK to configure."), 
			"storage_unconfig", "main_storage.php" );
		alert_msg_each(I18N("h", "Western Digital drive is locked."), 
			I18N("h", "A password encrypted Western Digital drive is connected to the router. Click OK to unlock."), 
			"WD_drive_locked", "main_storage.php" );
		alert_msg_each(I18N("h", "The internal drive's temperature is above normal level"), 
			I18N("h", "The internal drive's temperature above normal level. Make sure it has proper ventilation, and move it to a cooler location if necessary."), 
			"temperature_high", $_GLOBALS["TEMP_MYNAME"].".php" );//Should only support router mode. Will be modified in getcfg.
		$layout	= query("/device/layout");
		if($layout=="router")
		{//support router mode and AP mode. But OK button behavior is different. Router mode's OK button can go to Internet settings page. AP mode's button do nothing.
		alert_msg_each(I18N("h", "Unable to connect to Internet"), 
			I18N("h", "Internet connection is not detected. Click OK to set up Internet connection."), 
			"WAN_detect_fail", "main_internet.php" );
		}
		else
		{//This is for WD ITR 54581
			alert_msg_each(I18N("h", "Unable to connect to Internet"), 
			I18N("h", "Internet connection is not detected."), "WAN_detect_fail", "ap_status.php" );
		}
		alert_msg_each(I18N("h", "The product has not been registered"), 
			I18N("h", "Please register your product to make sure you get warranty service.  Click OK to register the product."), 
			"unregister", "register.php" );
		alert_msg_each(I18N("h", "Guest wireless access has not been set up"), 
			I18N("h", "Guest wireless access has not been set up. Click OK to set it up."), 
			"wireless_guest_disable", "wlan_gz.php" );//Should only support router mode. Will be modified in getcfg.
		alert_msg_each(I18N("h", "The wireless passwords are the same"), 
			I18N("h", "The password for Guest wireless network is same as that of your regular wireless network. It is strongly advised that you set different password for security. Click OK to change password."), 
			"wireless_guest_pwd_same", "wlan_gz.php" );	//Should only support router mode. Will be modified in getcfg.
		alert_msg_each(I18N("h", "Check your uplink WAN speed"),
			I18N("h", "The uplink WAN speed detection is for QoS optimization and it is currently detected to be $1 Kbps. If you know your uplink speed to be different, then click OK to change the speed manually.", $qos_autobandwidth), 
			"QoS_auto_detect", "adv_qos.php" );//Should only support router mode. Will be modified in getcfg.
		alert_msg_each(I18N("h", "FasTrack is turned off automatically"), 
			I18N("h", "FasTrack QoS is turned off automatically because the uplink WAN speed cannot be detected. If you know your uplink speed, click OK to set the speed manually."), 
			"QoS_auto_detect_fail", "adv_qos.php" );//Should only support router mode. Will be modified in getcfg.
		alert_msg_each(I18N("h", "WPS PIN has been disabled"), 
			I18N("h", "The router's WPS PIN has been disabled due to 10 consecutive unsuccessful PIN input attempts.  Click OK to enable WPS PIN."), 
			"WPS_PIN_disabled", "wlan_wps.php" );						
}

function alert_bar_display()
{
	$router_name = query("/runtime/device/modelname");
	$firmware_version = query("/runtime/device/firmwareversion");
	$lang = query("/device/features/language");
	if($lang=="auto")
	{
		$lang = query("/runtime/device/auto_language");
		if($lang=="")	$lang="en";
	}

	echo '<div class="alert_bar">\n'.
		 '\t<span style="position:absolute;top:12px;left:25px;"><img src="/pic/mark.png"></span>\n'.		 
		 '\t<span style="position:absolute;top:13px;left:100px;"><img id="internet_alert" src="/pic/internet_green.png"></span>\n';
	if($_GLOBALS["FEATURE_MODEL_NAME"]=="db600")
	{
		echo '\t<span style="position:absolute;top:18px;left:140px;color:white;font-size:10pt;">'.I18N("h", "Welcome to My Net N600 Router").'</span>\n';
	}
	else if($_GLOBALS["FEATURE_MODEL_NAME"]=="av")
	{	 
		echo '\t<span style="position:absolute;top:18px;left:140px;color:white;font-size:10pt;">'.I18N("h", "Welcome to My Net N750 Router").'</span>\n';
	}
	else if($_GLOBALS["FEATURE_MODEL_NAME"]=="storage")
	{	 
		if($lang=="ru") $model_Left_Position = "125px";
		else $model_Left_Position = "140px";
		echo '\t<span style="position:absolute;top:18px;left:'.$model_Left_Position.';color:white;font-size:9pt;">'.I18N("h", "Welcome to My Net N900 Central Router").'</span>\n';
	}
	else if($_GLOBALS["FEATURE_MODEL_NAME"]=="pro")
	{	 
		echo '\t<span style="position:absolute;top:18px;left:140px;color:white;font-size:10pt;">'.I18N("h", "Welcome to My Net N900 Router").'</span>\n';
	}
    else if($_GLOBALS["FEATURE_MODEL_NAME"]=="dolphin")
	{
		echo '\t<span style="position:absolute;top:18px;left:140px;color:white;font-size:10pt;">'.I18N("h", "Welcome to My Net AC1800 Router").'</span>\n';
	}

	if($lang=="ru") $FWVER_Left_Position = "440px";
	else $FWVER_Left_Position = "400px";
	echo '\t<span id="fw_version" style="display:none;position:absolute;top:18px;left:'.$FWVER_Left_Position.';color:white;font-size:9pt;">'.I18N("h", "Firmware Version")."  ".$firmware_version.'</span>\n'.
		 '\t<div id="alert_msg_button" style="display:none;position:absolute;top:8px;left:670px;height:33px;width:301px;background-image:url(\'/pic/alert_button.png\');" onclick="OBJ(\'alert_msg\').style.display=\'block\';" onmouseout="this.style.backgroundImage=\'url(/pic/alert_button.png)\';" onmouseover="this.style.backgroundImage=\'url(/pic/alert_button_hover.png)\';this.style.cursor = \'pointer\';">\n'.
		 '\t\t<span id="notification_n_out" style="position:absolute;top:11px;left:40px;color:white;font-size:8pt;">'.I18N("h", "You have some notifications.").'</span>\n'.
		 '\t</div>\n'.
		 '\t<div id="alert_msg" style="display:none;position:absolute;left:660px;width:345px;color:white;font-size:9pt;background:url(/pic/alert_background.png);background-repeat: repeat-y;">\n'.
		 '\t\t<div style="position:relative;height:49px;width:300px;margin-left:20px;background:url(/pic/alert_button_top.png);" onclick="OBJ(\'alert_msg\').style.display=\'none\';" onmouseover="this.style.cursor = \'pointer\';">\n'.
		 '\t\t\t<span id="notification_n_in" style="position:absolute;top:26px;left:40px;color:white;font-size:8pt;">'.I18N("h", "You have some notifications.").'</span>\n'.
		 '\t\t</div>\n';
	alert_msg("alert_msg");	 
	echo '\t\t<div style="height:20px;width:345px;background:url(/pic/alert_background_bottom.png)";></div>\n'.
		 '\t</div>\n'.
		 '</div>\n';
}	

function top_bar_display($group)
{	
	$layout	= query("/device/layout");
	if($group == $_GLOBALS["TEMP_MYGROUP"])
	{	
		if($group == "adv_admin") 		{return 'style="background-image:url(pic/adv_top_bar_admin_select.png);color:#2AA4FF;"';}
		else if($group == "adv_wan")	{return 'style="background-image:url(pic/adv_top_bar_wan_select.png);color:#2AA4FF;"';}
		else if($group == "adv_wlan")	{return 'style="background-image:url(pic/adv_top_bar_wlan_select.png);color:#2AA4FF;"';}
		else if($group == "adv_lan")	{return 'style="background-image:url(pic/adv_top_bar_lan_select.png);color:#2AA4FF;"';}
		else if($group == "adv_storage"){return 'style="background-image:url(pic/adv_top_bar_storage_select.png);color:#2AA4FF;"';}
		else if($group == "adv_secure")	{return 'style="background-image:url(pic/adv_top_bar_secure_select.png);color:#2AA4FF;"';}
		else if($group == "adv_remote")	{return 'style="background-image:url(pic/adv_top_bar_remote_select.png);color:#2AA4FF;"';}		
		else if($group == "adv_add")	{return 'style="background-image:url(pic/adv_top_bar_add_select.png);color:#2AA4FF;"';}
		else if($group == "main_dashboard")	{return 'style="background-image:url(pic/main_dashboard_select.png);color:#2AA4FF;"';}
		else if($group == "main_internet")	{return 'style="background-image:url(pic/main_internet_select.png);color:#2AA4FF;"';}
		else if($group == "main_wireless")	{return 'style="background-image:url(pic/main_wireless_select.png);color:#2AA4FF;"';}
		else if($group == "main_storage")	{return 'style="background-image:url(pic/main_storage_select.png);color:#2AA4FF;"';}
		else if($group == "main_wps")		{return 'style="background-image:url(pic/main_wps_select.png);color:#2AA4FF;"';}
		else if($group == "main_remote")	{return 'style="background-image:url(pic/main_remote_select.png);color:#2AA4FF;"';}
		else if($group == "main_adv")		{return 'style="background-image:url(pic/main_adv_select.png);color:#2AA4FF;"';}
	}
	else
	{
		if($group == "adv_admin")		{return ' style="background-image:url(pic/adv_top_bar_admin.png);"	onclick="self.location.href=\'tools_admin.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_admin.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_admin_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_wan")	{return ' style="background-image:url(pic/adv_top_bar_wan.png);"	onclick="self.location.href=\'wan_status.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_wan.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_wan_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_wlan")	{return ' style="background-image:url(pic/adv_top_bar_wlan.png);"	onclick="self.location.href=\'wlan.php\'"			onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_wlan.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_wlan_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_lan")	{return ' style="background-image:url(pic/adv_top_bar_lan.png);"	onclick="self.location.href=\'lan.php\'"			onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_lan.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_lan_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_storage"){return ' style="background-image:url(pic/adv_top_bar_storage.png);"onclick="self.location.href=\'storage.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_storage.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_storage_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_secure")	{return ' style="background-image:url(pic/adv_top_bar_secure.png);"	onclick="self.location.href=\'adv_firewall.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_secure.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_secure_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_remote")	{return ' style="background-image:url(pic/adv_top_bar_remote.png);"	onclick="self.location.href=\'adv_remote.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_remote.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_remote_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "adv_add")	{return ' style="background-image:url(pic/adv_top_bar_add.png);"	onclick="self.location.href=\'adv_pfwd.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/adv_top_bar_add.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/adv_top_bar_add_hover.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_dashboard")	{return ' style="background-image:url(pic/main_dashboard.png);"	onclick="self.location.href=\'main_dashboard.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/main_dashboard.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/main_dashboard_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_internet")	{return ' style="background-image:url(pic/main_internet.png);"	onclick="self.location.href=\'main_internet.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/main_internet.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/main_internet_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_wireless")	{return ' style="background-image:url(pic/main_wireless.png);"	onclick="self.location.href=\'main_wireless.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/main_wireless.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/main_wireless_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_storage")	{return ' style="background-image:url(pic/main_storage.png);"	onclick="self.location.href=\'main_storage.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/main_storage.png)\';this.style.color = \'#898989\';"	onmouseover="this.style.backgroundImage = \'url(/pic/main_storage_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_wps")		{return ' style="background-image:url(pic/main_wps.png);"		onclick="self.location.href=\'main_wps.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/main_wps.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/main_wps_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_remote")	{return ' style="background-image:url(pic/main_remote.png);"	onclick="self.location.href=\'main_remote.php\'"	onmouseout="this.style.backgroundImage = \'url(/pic/main_remote.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/main_remote_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_adv" && $layout == "router")	{return ' style="background-image:url(pic/main_adv.png);"		onclick="self.location.href=\'wan_status.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/main_adv.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/main_adv_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
		else if($group == "main_adv")		{return ' style="background-image:url(pic/main_adv.png);"		onclick="self.location.href=\'lan.php\'"		onmouseout="this.style.backgroundImage = \'url(/pic/main_adv.png)\';this.style.color = \'#898989\';"		onmouseover="this.style.backgroundImage = \'url(/pic/main_adv_over.png)\';this.style.color = \'#D6D6D6\';this.style.cursor = \'pointer\';"';}
	}		
}

function draw_menu($menuString, $menuLink, $delimiter, $echo)
{
	$lang = query("/device/features/language");
	if($lang=="auto")
	{
		$lang = query("/runtime/device/auto_language");
		if($lang=="")	$lang="en";
	}
	$function_desc = "";
	if($menuString != "")
	{
		$menuItems = cut_count($menuString,$delimiter);
		if($menuItems == 0) $menuItems = 1;
		$i = 0;
		while( $i < $menuItems )
		{
			if ($menuItems == 1)
			{
				$item = $menuString;
				$link = $menuLink;
			}
			else
			{
				$item = cut($menuString, $i, $delimiter);
				$link = cut($menuLink,   $i, $delimiter);
			}
			
			//Modify the word style in the left menu to fit the multi-language.
			$shrink=0;
			$shrink_linefeed=0;
			if($lang=="ru" && strlen($item)>=60){$shrink=1;$shrink_linefeed=1;}
			else if($lang=="ru" && strlen($item)>=38){$shrink=1;}
			else if($lang=="pl" && strlen($item)>=33){$shrink=1;$shrink_linefeed=1;}
			else if($lang=="pl" && strlen($item)>=23){$shrink=1;}
			else if($lang=="de" && strlen($item)>=58){$shrink=1;$shrink_linefeed=1;}
			else if($lang=="de" && strlen($item)>=25){$shrink=1;}
			else if($lang=="cs"){$shrink=1;}
			else if($lang=="ptbr" && strlen($item)>=22){$shrink=1;}					
			else if($lang=="pt" || $lang=="tr" || $lang=="no" || $lang=="it" || $lang=="nl" || $lang=="es"){ if(strlen($item)>=22) $shrink=1;}
			else if($lang=="hu" || $lang=="fr" || $lang=="ja" || $lang=="sv"){ if(strlen($item)>=27) $shrink=1;}
			else if($lang=="ko"){ if(strlen($item)>=36) $shrink=1;}
			if ($link==$_GLOBALS["TEMP_MYNAME"].".php")
			{
				if($echo==1 && $shrink_linefeed==1 && $shrink==1)
				{
					if($lang=="pl" &&$link=="wlan.php") echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p style="font-size:13px;line-height:18px;">'.$item.'</p></a></li>\n';
					else echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p style="font-size:11px;line-height:18px;">'.$item.'</p></a></li>\n';
				}
				else if($echo==1 && $shrink==1)
				{
					if($lang=="cs" &&$link=="wlan.php") echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p style="font-size:13px;line-height:18px;">'.$item.'</p></a></li>\n';
					else if($lang=="es" &&$link=="lan_server.php") echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p style="font-size:11px;">'.$item.'</p></a></li>\n';
					else echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p style="font-size:12px;">'.$item.'</p></a></li>\n';
				}
				else if($echo==1) echo '\t\t\t\t<li><a class="label" href="'.$link.'"><p>'.$item.'</p></a></li>\n';
				$function_desc = $item;
			}
			else
			{
				if($echo==1 && $shrink_linefeed==1) echo '\t\t\t\t<li><a href="'.$link.'"><p style="line-height:18px;">'.$item.'</p></a></li>\n';
				else if($echo==1) echo '\t\t\t\t<li><a href="'.$link.'"><p>'.$item.'</p></a></li>\n';
			}
			$i++;
		}
	}
	return $function_desc;
}

function draw_main_top_info($group)
{
	if($group=="main_dashboard")
	{
		$pic_name = "main_dashboard_small.png";
		$info = I18N("h", "My Dashboard"); 
	}
	else if($group=="main_internet")
	{
		$pic_name = "main_internet_small.png";
		$info = I18N("h", "Connect to Internet"); 
	}
	else if($group=="main_wireless")
	{
		$pic_name = "main_wireless_small.png";
		$info = I18N("h", "Set up Wireless"); 
	}
	else if($group=="main_storage")
	{
		$pic_name = "main_storage_small.png";
		$info = I18N("h", "Set up Storage"); 
	}
	else if($group=="main_wps")
	{
		$pic_name = "main_wps_small.png";
		$info = I18N("h", "Add a Wi-Fi Device");
	}					
    else if($group=="main_remote")
    {
        $pic_name = "adv_remote_small.png";
        $info = I18N("h", "Set up WD 2go Remote Access");
    }
	/* if($_GLOBALS["FEATURE_MODEL_NAME"]=="storage" && $group=="main_dashboard") */
	if($group=="main_dashboard") /* apply to all model */
	{
		echo "<table style=\"width: 800px\">\n";
		echo "<tr>\n";
		echo "<td  style=\"width: 24px\"><img src=\"/pic/".$pic_name."\"></td>\n";
		echo "<td align=left><span style=\"margin-left: 15px;\" class=\"title1\">".$info."</span></td>\n";
		echo "<td align=right><input type=button class=\"button_blackX2\" value=\"".I18N("h", "Log Out")."\" onClick=\"PAGE.OnClickLogout();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
	else
	echo	'\t\t<img src="/pic/'.$pic_name.'" />\n'.
		 	'\t\t<span style="margin-left: 15px;" class="title1">'.$info.'</span>\n';
	if ($group=="main_remote")
	{
		echo    '<span id=wb_storage_remote_access class="title1"></span>\n';
	}
}	

function draw_adv_top_info($func_desc)
{
	if($_GLOBALS["TEMP_MYGROUP"]=="adv_admin")
	{
		$pic_src = "adv_admin_small.png";
		$func_group = I18N("h", "Admin");
	}
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_wan")
	{
		$pic_src = "adv_wan_small.png";
		$func_group = I18N("h", "WAN");
	}	
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_wlan")
	{
		$pic_src = "adv_wlan_small.png";
		$func_group = I18N("h", "Wireless");
	}	
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_lan")
	{
		$pic_src = "adv_lan_small.png";
		$func_group = I18N("h", "LAN");
	}
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_storage")
	{
		$pic_src = "adv_storage_small.png";
		$func_group = I18N("h", "Storage");
	}
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_secure")
	{
		$pic_src = "adv_secure_small.png";
		$func_group = I18N("h", "Security");
	}		
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_remote")
	{
		$pic_src = "adv_remote_small.png";
		$func_group = I18N("h", "Set up Remote Access");
	}
	else if($_GLOBALS["TEMP_MYGROUP"]=="adv_add")
	{
		$pic_src = "adv_add_small.png";
		$func_group = I18N("h", "Additional Features");
	}	
	echo '\t\t\t<span><img src="/pic/'.$pic_src.'"></span>\n'.
		 '\t\t\t<span>&nbsp;&nbsp;</span>\n'.
		 '\t\t\t<span style="color:white;font-size:24px;font-weight:bold;">'.$func_group.' / '.$func_desc.'</span>\n';	
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="shortcut icon" href="/favicon.ico" >	
<?  
	if ($TEMP_STYLE!="progress") echo '\t<link rel="stylesheet" href="/css/general.css" type="text/css">\n';			
	if ($TEMP_STYLE=="support") echo '\t<link rel="stylesheet" href="/css/support.css" type="text/css">\n';
?>
	<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
	<meta http-equiv="Content-Type" content="no-cache">
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
	<title>WESTERN DIGITAL, INC. | WIRELESS ROUTER | HOME</title>		
	<script type="text/javascript" charset="utf-8" src="./js/comm.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/libajax.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/postxml.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_checkbox_radio.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_select.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_button.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_help.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/hmac_md5.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/multi_language.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/browser_detection.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_text.js"></script>
	<script type="text/javascript" charset="utf-8" src="./js/new_arrow.js"></script>
<?
	if (isfile("/htdocs/webinc/js/".$TEMP_MYNAME.".php")==1)
		dophp("load", "/htdocs/webinc/js/".$TEMP_MYNAME.".php");
?>
	<script type="text/javascript">
	var OBJ	= COMM_GetObj;
	var XG	= function(n){return PXML.doc.Get(n);};
	var XS	= function(n,v){return PXML.doc.Set(n,v);};
	var XD	= function(n){return PXML.doc.Del(n);};
	var XA	= function(n,v){return PXML.doc.Add(n,v);};
	var GPBT= function(r,e,t,v,c){return PXML.doc.GetPathByTarget(r,e,t,v,c);};
	var S2I	= function(str) {return isNaN(str)?0:parseInt(str, 10);}

	function TEMP_IsDigit(no)
	{
		if (no==""||no==null)
			return false;
		if (no.toString()!=parseInt(no, 10).toString())
			return false;

	    return true;
	}
	function TEMP_CheckNetworkAddr(ipaddr, lanip, lanmask)
	{
		if (lanip)
		{
			var network = lanip;
			var mask = lanmask;
		}
		else
		{
			var network = "<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0); echo query($inf."/inet/ipv4/ipaddr");?>";
			var mask = "<?echo query($inf."/inet/ipv4/mask");?>";
		}
		var vals = ipaddr.split(".");

		if (vals.length!=4)
			return false;

		for (var i=0; i<4; i++)
			if (!TEMP_IsDigit(vals[i]) || vals[i]>255)	return false;

		if (COMM_IPv4NETWORK(ipaddr, mask)!=COMM_IPv4NETWORK(network, mask))
			return false;

		return true;
	}
	function TEMP_RulesCount(path, id)
	{
		var max = parseInt(XG(path+"/max"), 10);
		var cnt = parseInt(XG(path+"/count"), 10);
		var rmd = max - cnt;
		OBJ(id).innerHTML = rmd;
	}
	function TEMP_SetCookie(c_name,value,exdays)
	{
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}
	function TEMP_GetCookie(c_name)
	{
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++)
		{
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name)
			{
				return unescape(y);
			}
		}
	}
	function TEMP_CheckCookie(c_name)
	{
		var a = TEMP_GetCookie(c_name);
		if(a!="" && a!=null)
		{
			return a;
		}
		else
		{
			return null;
		}
	}
		
	function Body() {}
	Body.prototype =
	{
		ShowLogin: function()
		{
			var a = TEMP_CheckCookie("QPYVIXOAZD");
			if(a!=null)
			{
				OBJ("loginusr").value = a;
				OBJ("remember").checked = true;
			}
			else
			{
				OBJ("loginusr").value = "";
			}
			OBJ("loginpwd").value	= "";
			
			OBJ("menu").style.display	= "none";
			OBJ("content").style.display= "none";
			OBJ("mbox").style.display	= "none";
			OBJ("mbox2").style.display	= "none";
			OBJ("mbox_ex").style.display  = "none";
			OBJ("login").style.display	= "block";
			
			if (OBJ("loginusr").tagName.toLowerCase()=="input")
			{
				//OBJ("loginusr").value = "admin";
				OBJ("loginusr").focus();
			}
			else
			{
				OBJ("loginpwd").focus();
			}
			if(OBJ("loginusr").value!="") OBJ("loginpwd").focus();
			this.NewWDStyle_init();
			this.NewWDStyle_refresh();
		},
		ShowAutoLogin: function()
		{
			var usr1="ad", usr2="min";
			var pwd1="pa", pwd2="sswo", pwd3="rd";
			OBJ("loginusr").value = usr1 + usr2;
			OBJ("loginpwd").value = pwd1 + pwd2 + pwd3;
			
			var title = "<? echo I18N('h', 'Welcome to the WD Setup Wizard');?>";
			var msgArray = ["<? echo I18N('h', 'This wizard will guide you through a step-by-step process to configure your new WD router and connect to the Internet.');?>",
							"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='BODY.LoginSubmit();'>"];
			this.ShowMessage(title, msgArray);
		},
		ShowContent: function()
		{
			clearTimeout(this.timerId);
			OBJ("login").style.display	= "none";
			OBJ("mbox").style.display	= "none";
			OBJ("mbox2").style.display	= "none";
			OBJ("mbox_ex").style.display  = "none";
			<?if ($TEMP_STYLE!="simple") echo 'OBJ("menu").style.display	= "block";';?>
			OBJ("content").style.display= "block";
		},
		ShowMessage: function(banner, msgArray)
		{
			clearTimeout(this.timerId);
			var str = '<div style="height:1px;"></div>';
			str += '<h1>'+banner+'</h1>';
			for (var i=0; i<msgArray.length; i++)
			{
				str += '<div class="emptyline"></div>';
				if(msgArray[i].search("<div") > -1)
					str += msgArray[i];
				else
				str += '<div class="centerline">'+msgArray[i]+'</div>';
			}
			str += '<div class="emptyline"></div>';
			OBJ("message").innerHTML = str;
			OBJ("login").style.display	= "none";
			OBJ("menu").style.display	= "none";
			OBJ("content").style.display= "none";
			OBJ("mbox").style.display	= "block";
			OBJ("mbox2").style.display	= "none";
			OBJ("mbox_ex").style.display  = "none";
			this.NewWDStyle_init();
		},
		rtnURL: null,
		seconds: null,
		timerId: null,
		AutoLogin: "<? echo $_GLOBALS["AUTO_LOGIN"];?>",
		Countdown: function()
		{
			this.seconds--;
			OBJ("timer").innerHTML = this.seconds;
			if (this.seconds < 1) this.GotResult();
			else this.timerId = setTimeout('BODY.Countdown()',1000);
		},
		GotResult: function()
		{
			clearTimeout(this.timerId);
			if (this.rtnURL)	self.location.href = this.rtnURL;
			else				this.ShowContent();
		},
		ShowCountdown: function(banner, msgArray, sec, url, wait_descript)
		{
			this.rtnURL = url;
			this.seconds = sec;
			var str = '<div style="height:1px;"></div>';
			str += '<h1>'+banner+'</h1>';
			for (var i=0; i<msgArray.length; i++)
			{
				str += '<div class="emptyline"></div>';
				if(msgArray[i].search("<div") > -1)
					str += msgArray[i];
				else
				str += '<div class="centerline">'+msgArray[i]+'</div>';
			}
			str += '<div class="emptyline"></div>';
			if(typeof(wait_descript)==="undefined" || wait_descript===null || wait_descript==="")
				str += "<div class='centerline'><?echo I18N('h', 'Waiting time');?> : ";
			else str += '<div class="centerline">'+wait_descript+' : ';	
			str += '<span id="timer" style="color:red;"></span>';
			str += "&nbsp; <?echo I18N('h', 'second(s)');?></div>";				
			str += '<div class="emptyline"></div>';
			OBJ("message").innerHTML	= str;
			OBJ("login").style.display	= "none";
			OBJ("menu").style.display	= "none";
			OBJ("content").style.display= "none";
			OBJ("mbox").style.display	= "block";
			OBJ("mbox2").style.display	= "none";
			OBJ("mbox_ex").style.display  = "none";
			this.Countdown();
			this.NewWDStyle_init();
		},
		interval_ex: 1,
		status_ex: 1,
        ShowProcessBar: function(status)
        {
            if (status > 20) status = 20;

            for (var i = 1; i <= 20 ; i++)
            {
                if (i <= status)
                {
                    OBJ("pb_"+i).style.display  = "block";
                }
                else
                {
                    OBJ("pb_"+i).style.display  = "none";
                }
            }
            OBJ("timer_ex").innerHTML = status*5 + "%";
        },
        GotResult_ex: function()
        {
            clearTimeout(this.timerId);
            if (this.rtnURL)   
			{
				if (this.rtnURL != "1")
				{
					if(this.rtnURL.search("PAGE.") >= 0)
					{
						if(this.rtnURL.lastIndexOf(';')!=this.rtnURL.length-1)
						{//If designer forgot add ';' at the end of function, this check help to add ';'.
							this.rtnURL = this.rtnURL + ";";
						}
						OBJ("mbox_ex").style.display  = "none";//hide countdown message box
						eval(this.rtnURL);//Running PAGE. function.
					}
					else
					{
						self.location.href = this.rtnURL;
					}
				}
			}
            else 
			{
	               this.ShowContent();
			}
        },
        Countdown_ex: function()
        {
            this.seconds-=this.interval_ex;
			if (this.status_ex <= 20)
			{
				OBJ("pb_"+this.status_ex).style.display  = "block";
				OBJ("timer_ex").innerHTML = this.status_ex*5 + "%";
				this.status_ex++;
			}
            if (this.seconds < 1) this.GotResult_ex();
            else this.timerId = setTimeout('BODY.Countdown_ex()',this.interval_ex*1000);
        },
        ShowCountdown_ex: function(banner, msgArray, sec, url, wait_descript)
        {
            this.rtnURL = url;
            this.seconds = sec;
			this.status_ex = 1;
			this.interval_ex = sec / 20;
			for (var i = 1; i <= 20 ; i++)
			{
				OBJ("pb_"+i).style.display  = "none";
			}
            var str = '<div style="height:1px;"></div>';
            str += '<h1>'+banner+'</h1>';
            for (var i=0; i<msgArray.length; i++)
            {
                str += '<div class="emptyline"></div>';
                str += '<div class="centerline">'+msgArray[i]+'</div>';
            }
            str += '<div class="emptyline"></div>';
            OBJ("message_ex").innerHTML    = str;
            OBJ("login").style.display  = "none";
            OBJ("menu").style.display   = "none";
            OBJ("content").style.display= "none";
			OBJ("mbox").style.display  = "none";
			OBJ("mbox_ex").style.display  = "block";
            OBJ("mbox2").style.display  = "none";
            this.Countdown_ex();
            this.NewWDStyle_init();
        },
		ShowAlert: function(msg)
		{
			alert(msg);
		},
		DisableCfgElements: function(type)
		{
			for (var i = 0; i < document.forms.length; i+=1)
		    {
				var frmObj = document.forms[i];
				for (var idx = 0; idx < frmObj.elements.length; idx+=1)
				{
					if (frmObj.elements[idx].getAttribute("usrmode")=="enable") continue;
					frmObj.elements[idx].disabled = type;
				}
			}
		},
		//////////////////////////////////////////////////
		LoginCallback: null,
		//////////////////////////////////////////////////
		LoginSubmit: function()
		{
			var self = this;
			if (OBJ("loginusr").value=="")
			{
				this.ShowAlert("<?echo I18N("j", "Please enter the User Name.");?>")
				OBJ("loginusr").focus();
				return false;
			}
			AUTH.Login_Hash(
				function(json)
				{
					var JsonData = eval('(' + json + ')');
					if(JsonData.RESULT=="OK")
					{
						if(typeof(JsonData.AUTHORIZED_GROUP)=="undefined") AUTH.AuthorizedGroup = 0;
						else AUTH.AuthorizedGroup = parseInt(JsonData.AUTHORIZED_GROUP, 10);
						AUTH.UpdateTimeout();
						BODY.AlertAct("fw_check");
						if (self.LoginCallback) self.LoginCallback();
						//BODY.OnReload();
						//self.ShowContent();
						if(OBJ("remember").checked== true)
						{
							//if(TEMP_CheckCookie("QPYVIXOAZD")==null)
								TEMP_SetCookie("QPYVIXOAZD",OBJ("loginusr").value,365);
						}
						else
						{
							TEMP_SetCookie("QPYVIXOAZD","",-1);
						}
						window.location.reload();						
					}
					else if(JsonData.RESULT=="FAIL")
					{
						switch (JsonData.REASON)
						{
						case "AUTH_FAIL":
							var msgArray =
							[
								"<?echo I18N('h', 'User name or password is incorrect.');?>",
								"<input id='relogin' type='button' class='button_blueX3' value='<?echo I18N('h', 'Login Again');?>' onClick='BODY.ShowLogin();' />"
							];
							self.ShowMessage("<?echo I18N('h', 'Login failed');?>", msgArray);
							OBJ("relogin").focus();
							break;
						case "ERR_CREATE_SESSDATA":
							var msgArray =
							[
								"<?echo I18N('h', 'Too many sessions connected, please try again later.');?>",
								"<input id='relogin' type='button' class='button_blueX3' value='<?echo I18N('h', 'Login Again');?>' onClick='BODY.ShowLogin();' />"
							];
							self.ShowMessage("<?echo I18N('h', 'Login failed');?>", msgArray);
							OBJ("relogin").focus();
							break;						
						default:
							//self.ShowAlert("Internal error, "+JsonData.REASON);
							OnunloadAJAX();
							window.location.reload(true);
							break;
						}
					}
					else
					{
						self.ShowAlert('<?echo I18N("j", "Unexpected response. Please login again.");?>');
						OBJ("relogin").focus();
					}		
				},
				OBJ("loginusr").value,
				OBJ("loginpwd").value
			);			
		},
		Login: function(callback)
		{
			if (callback)	this.LoginCallback = callback;
			if (AUTH.AuthorizedGroup >= 0) { AUTH.UpdateTimeout(); return true; }
			return false;
		},
		Logout: function()
		{
			AUTH.Logout(function(){AUTH.TimeoutCallback();});
		},
		Reboot: function()
		{
			if (!confirm("<?echo I18N("j", "Reboot Router ?");?>"))	return;
			self.location.href = "./reboot.php";
		},
		//////////////////////////////////////////////////
		GetCFG: function()
		{
			var self = this;
			if (!this.Login(function(){self.GetCFG();})) return;
			if (AUTH.AuthorizedGroup >= 100) this.DisableCfgElements(true);
			if (PAGE)
			{
				COMM_GetCFG(
					false,
					PAGE.services+",ALERTMSG",
					function(xml) {
						PAGE.InitValue(xml);
						PAGE.Synchronize();
						COMM_DirtyCheckSetup();
						if (AUTH.AuthorizedGroup >= 100) BODY.DisableCfgElements(true);
						BODY.AlertMSG(xml);
						}
					);
			}			
			return;
		},
		OnSubmit: function()
		{
			if (PAGE === null) return;
			PAGE.Synchronize();
			var dirty = COMM_IsDirty(false);
			if (!dirty && PAGE.IsDirty) dirty = PAGE.IsDirty();
			if (!dirty)
			{
				var msgArray =
				[
					"<?echo I18N('h', 'Settings have not changed.');?>",
					"<input id='nochg' type='button' class='button_blueX2' value='<?echo I18N('h', 'Continue');?>' onClick='BODY.ShowContent();' />"
				];
				this.ShowMessage('', msgArray);//According to WD ITR 41414, remove string "No Change".
				/*this.ShowMessage("<?echo I18N('h', 'No Change');?>", msgArray);*/
				OBJ("menu").style.display	= "none";
				OBJ("content").style.display= "none";
				OBJ("mbox").style.display	= "block";
				OBJ("mbox2").style.display	= "none";
				OBJ("nochg").focus();
				return;
			}

			var xml = PAGE.PreSubmit();
			if (xml === null) return;

			if('<?echo $_GLOBALS["TEMP_MYNAME"];?>' != 'bsc_sms_send')
            {
            var msgArray =
            [
                "<?echo I18N('h', 'The settings are being saved and are taking effect.');?>",
                "<?echo I18N('h', 'Please wait');?> ..."
            ];
            }
            else
            {
                var msgArray = ["<?echo I18N('h', 'Sending message, please wait...');?>"];
            }
			
			if(PAGE.SavingMessageTitle!=null && PAGE.SavingMessageArray!=null)
			{
				this.ShowMessage(PAGE.SavingMessageTitle , PAGE.SavingMessageArray);
			}
			else
			{
				this.ShowMessage("<?echo I18N('h', 'Saving');?>", msgArray);
			}
			AUTH.UpdateTimeout();

			var self = this;
			PXML.UpdatePostXML(xml);
			PXML.Post(function(code, result){self.SubmitCallback(code,result);});
		},
		ComplexSubmit: function(DC , PreFL , BEF_en , ING_en)
		{
			/*
			ComplexSubmit will do same actions like OnSubmit.
			But this function has more display features.
			Above describes parameters effects.
			
			DC:	    boolean	 Check data modified or not.
			PreFL:  boolean  JS. check data is valid or not.
			BEF_en: boolean  Before saving message enable or not.
			ING_en: boolean  Doing saving message enable or not.
			PAGE.PreFatLady()     must have  function   JS. check
			PAGE.SetXML()         must have  function   Data saving
			PAGE.IsDirty()        optional   funciton   Check data modified
			PAGE.BeforeSaveTitle  optional   string     Before saving message title
			PAGE.BeforeSaveMsg    optional   array      Before saving message
			PAGE.SavingTitle      optional   string     Doing saving message title
			PAGE.SavingMsg        optional   array      Doing saving message
			PAGE.AFT_en           optional   boolean    After saving message enable or not.
			PAGE.AfterSaveTitle   optional   string     After saving message title.
			PAGE.AfterSaveMsg     optional   array      After saving message.
			PAGE.DecideToGo()     optional   function   Decide where webpage should go.
			*/
			if(PAGE.PreFatLady===null||PAGE.SetXML===null)
			{
				BODY.ShowAlert("BODY.ComplexSubmit() internal initial error.");
				return;
			}
			if(DC===true)
			{
				var dirty = COMM_IsDirty(false);
				if (!dirty && PAGE.IsDirty) dirty = PAGE.IsDirty();
				if (!dirty)
				{
					var msgArray =
					[
						"<?echo I18N('h', 'Settings have not changed.');?>",
						"<input id='nochg' type='button' class='button_blueX2' value='<?echo I18N('h', 'Continue');?>' onClick='BODY.ShowContent();' />"
					];
					this.ShowMessage('', msgArray);
					OBJ("nochg").focus();
					return;
				}
			}
			if(PAGE.BeforeSaveTitle!=null && PAGE.BeforeSaveMsg!=null &&BEF_en===true)
			{
				PAGE.BeforeSaveMsg.push("<input type='button' class='button_black' id='reload' onclick='BODY.ShowContent();' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='BODY.ComplexSubmit(false,true,false,true);' value='<?echo I18N('h', 'Save');?>'>");
				BODY.ShowMessage(PAGE.BeforeSaveTitle , PAGE.BeforeSaveMsg);
				PAGE.BeforeSaveMsg.pop();
				return;
			}
			if(PreFL===true)
			{
				if(PAGE.PreFatLady()===null)
				{
					BODY.ShowContent();
					return;
				}
			}
			var xml = PAGE.SetXML();
			if (xml === null) return;
			
			if(PAGE.SavingTitle!=null && PAGE.SavingMsg!=null && ING_en===true)
			{
				BODY.ShowMessage(PAGE.SavingTitle , PAGE.SavingMsg);
			}
			else
			{
	            var msgArray =
	            [
	                "<?echo I18N('h', 'The settings are being saved and are taking effect.');?>",
	                "<?echo I18N('h', 'Please wait');?> ..."
	            ];
				this.ShowMessage("<?echo I18N('h', 'Saving');?>", msgArray);
			}
			AUTH.UpdateTimeout();

			var self = this;
			PXML.UpdatePostXML(xml);
			PXML.Post(function(code, result){self.ComplexBack(code,result);});
		},
		ComplexBack: function(code, result)
		{
			BODY.ShowContent();
			switch (code)
			{
			case "OK":
				if(PAGE.AFT_en===true && PAGE.AfterSaveTitle!=null && PAGE.AfterSaveMsg!=null)
				{
					var Append_button ="";
					if(PAGE.DecideToGo!=null)
						Append_button ="<input type='button' class='button_blue' id='reload' onclick='PAGE.DecideToGo();BODY.ShowContent();' value='<?echo I18N('h', 'Ok');?>'>";
					else
						Append_button ="<input type='button' class='button_blue' id='reload' onclick='BODY.OnReload();BODY.ShowContent();' value='<?echo I18N('h', 'Ok');?>'>";

					PAGE.AfterSaveMsg.push(Append_button);
					BODY.ShowMessage(PAGE.AfterSaveTitle , PAGE.AfterSaveMsg);
					OBJ("reload").focus();
					PAGE.AfterSaveMsg.pop();
				}
				else
				{
					BODY.OnReload();
				}
				break;
			case "BUSY":
				BODY.ShowAlert("<?echo I18N("j","Someone is configuring the device; please try again later.");?>");
				break;
			case "HEDWIG":
				BODY.ShowAlert(result.Get("/hedwig/message"));
				if (PAGE.CursorFocus) PAGE.CursorFocus(result.Get("/hedwig/node"));  
				break;
			case "PIGWIDGEON":
				if (result.Get("/pigwidgeon/message")=="no power")
				{
					BODY.NoPower();
				}
				else
				{
					BODY.ShowAlert(result.Get("/pigwidgeon/message"));
				}
				break;
			}
		},
		SubmitCallback: function(code, result)
		{
			if (PAGE.OnSubmitCallback(code, result)) return;
			this.ShowContent();
			switch (code)
			{
			case "OK":
				this.OnReload();
				break;
			case "BUSY":
				this.ShowAlert("<?echo I18N("j","Someone is configuring the device; please try again later.");?>");
				break;
			case "HEDWIG":
				this.ShowAlert(result.Get("/hedwig/message"));
				if (PAGE.CursorFocus) PAGE.CursorFocus(result.Get("/hedwig/node"));  
				break;
			case "PIGWIDGEON":
				if (result.Get("/pigwidgeon/message")=="no power")
				{
					BODY.NoPower();
				}
				else
				{
					this.ShowAlert(result.Get("/pigwidgeon/message"));
				}
				break;
			}
		},
		NoPower: function()
		{
			BODY.ShowAlert("<?echo I18N("j","Your connection session is invalid; please login again.");?>");
			AUTH.Logout();
			BODY.ShowLogin();
		},
		OnReload: function()
		{
			if (PAGE) PAGE.OnLoad();
			this.GetCFG();
			this.NewWDStyle_init();
		},
		//////////////////////////////////////////////////
		OnLoad: function()
		{
			var self = this;
			if (AUTH.AuthorizedGroup < 0)	
			{
				if(this.AutoLogin=="1") this.ShowAutoLogin();
				else this.ShowLogin();
				return; 
			}
			else	this.ShowContent();
			AUTH.TimeoutCallback = function()
			{
				var msgArray =
				[
					'<?echo I18N("h", "You have successfully logged out.");?>',
					'<input id="tologin" class="button_blueX4" type="button" value="<?echo I18N("h", "Return to login page");?>" onClick="BODY.ShowLogin();" />'
				];
				self.ShowMessage('<?echo I18N("h", "Logout");?>', msgArray);
				self.DisableCfgElements(false);
				//if (PAGE) PAGE.OnLoad();
				OBJ("tologin").focus();
			};

			if (PAGE) PAGE.OnLoad();
			this.GetCFG();
			this.NewWDStyle_init();
		},
		OnUnload: function() { if (PAGE) PAGE.OnUnload(); OnunloadAJAX(); },
		OnKeydown: function(e)
		{
			switch (COMM_Event2Key(e))
			{
			case 13: this.LoginSubmit();
			default: return;
			}
		},
		AlertMSG: function(xml)
		{
			PXML.doc = xml;
			var path = PXML.FindModule("ALERTMSG");
			path = path + "/alertmsg";
			if(XG(path+"/notification_n/descript")!="<?echo I18N('h', 'You have no notification');?>")
			{	
				if(XG(path+"/firmware_new/alert")!="1") OBJ("firmware_new").style.display = "none";
				else OBJ("firmware_new").style.display = "block";
				if(XG(path+"/wireless_unconfig/alert")!="1") OBJ("wireless_unconfig").style.display = "none";
				else
				{
					OBJ("wireless_unconfig").style.display = "block";
					OBJ("wireless_unconfig_content").innerHTML = XG(path+"/wireless_unconfig/descript");
				}			
				if(XG(path+"/wireless_open/alert")!="1") OBJ("wireless_open").style.display = "none";
				else
				{
					OBJ("wireless_open").style.display = "block";
					OBJ("wireless_open_content").innerHTML = XG(path+"/wireless_open/descript");
				}
				if(XG(path+"/admin_password/alert")!="1") OBJ("admin_password").style.display = "none";
				else OBJ("admin_password").style.display = "block";
				if(XG(path+"/storage_unconfig/alert")!="1") OBJ("storage_unconfig").style.display = "none";
				else OBJ("storage_unconfig").style.display = "block";
				if(XG(path+"/WD_drive_locked/alert")!="1") OBJ("WD_drive_locked").style.display = "none";
				else OBJ("WD_drive_locked").style.display = "block";
				if(XG(path+"/temperature_high/alert")!="1") OBJ("temperature_high").style.display = "none";
				else OBJ("temperature_high").style.display = "block";
				if(XG(path+"/WAN_network_status/alert")=="1") OBJ("internet_alert").src = "pic/internet_green.png";
				else OBJ("internet_alert").src = "pic/internet_gray.png";
				if(XG(path+"/WAN_detect_fail/alert")!="1") OBJ("WAN_detect_fail").style.display = "none";
				else OBJ("WAN_detect_fail").style.display = "block";																
				if(XG(path+"/unregister/alert")!="1") OBJ("unregister").style.display = "none";
				else OBJ("unregister").style.display = "block";
				if(XG(path+"/wireless_guest_disable/alert")!="1") OBJ("wireless_guest_disable").style.display = "none";
				else OBJ("wireless_guest_disable").style.display = "block";
				if(XG(path+"/wireless_guest_pwd_same/alert")!="1") OBJ("wireless_guest_pwd_same").style.display = "none";
				else OBJ("wireless_guest_pwd_same").style.display = "block";
				if(XG(path+"/QoS_auto_detect/alert")!="1") OBJ("QoS_auto_detect").style.display = "none";
				else OBJ("QoS_auto_detect").style.display = "block";
				if(XG(path+"/QoS_auto_detect_fail/alert")!="1") OBJ("QoS_auto_detect_fail").style.display = "none";
				else OBJ("QoS_auto_detect_fail").style.display = "block";
				if(XG(path+"/WPS_PIN_disabled/alert")!="1") OBJ("WPS_PIN_disabled").style.display = "none";
				else OBJ("WPS_PIN_disabled").style.display = "block";															
				OBJ("notification_n_in").innerHTML = XG(path+"/notification_n/descript");
				OBJ("notification_n_out").innerHTML = XG(path+"/notification_n/descript");			
				OBJ("fw_version").style.display = "block";
				OBJ("alert_msg_button").style.display = "block";
			}
			else
			{
				OBJ("alert_msg_button").style.display = "none";
				OBJ("alert_msg").style.display = "none";
							
				OBJ("fw_version").style.left = "750px";
				OBJ("fw_version").style.display = "block";
			}	
		},
		AlertActReport_n: 0,
		AlertAct: function(act, ping_dst, alert_item)
		{
			var self = this;
			var ajaxObj = GetAjaxObj("alert_act");
	
			ajaxObj.createRequest();
			ajaxObj.onCallback = function(xml)
			{
				ajaxObj.release();
				if(act=="ping") setTimeout('BODY.AlertAct("report")',1000);	
				else if(act=="report")
				{
					if(xml.Get("/alertmsg/report")==="")
					{
						self.AlertActReport_n++;
						if(self.AlertActReport_n===6) setTimeout('BODY.AlertAct("ping", "www.wdc.com")',1000);
						else if(self.AlertActReport_n < 12) setTimeout('BODY.AlertAct("report")',1000);
					}
					else self.AlertActReport_n = 0;
				}
				else if(act=="ignore") COMM_GetCFG(false,"ALERTMSG",BODY.AlertMSG);
			}
			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			ajaxObj.sendRequest("alertmsg.php", "act="+act+"&ping_dst="+ping_dst+"&alert_item="+alert_item);		
		},	
		InjectTable: function(tblID, uid, data, type)
		{
			var rows = OBJ(tblID).getElementsByTagName("tr");
			var tagTR = null;
			var tagTD = null;
			var i;
			var str;
			var found = false;
			
			/* Search the rule by UID. */
			for (i=0; !found && i<rows.length; i++) if (rows[i].id == uid) found = true;
			if (found)
			{
				for (i=0; i<data.length; i++)
				{
					tagTD = OBJ(uid+"_"+i);
					switch (type[i])
					{
					case "checkbox":
						str = "<input type='checkbox'";
						str += " id="+uid+"_check_"+i;
						if (COMM_ToBOOL(data[i])) str += " checked";
						str += " disabled>";
						tagTD.innerHTML = str;
						break;
					case "text":
						str = data[i];
						if(typeof(tagTD.innerText) !== "undefined")	tagTD.innerText = str;
						else if(typeof(tagTD.textContent) !== "undefined")	tagTD.textContent = str;
						else	tagTD.innerHTML = str;
						break;	
					default:
						str = data[i];
						tagTD.innerHTML = str;
						break;
					}
				}
				return;
			}

			/* Add a new row for this entry */
			tagTR = OBJ(tblID).insertRow(rows.length);
			tagTR.id = uid;
			/* save the rule in the table */
			for (i=0; i<data.length; i++)
			{
				tagTD = tagTR.insertCell(i);
				tagTD.id = uid+"_"+i;
				tagTD.className = "content";
				switch (type[i])
				{
				case "checkbox":
					str = "<input type='checkbox'";
					str += " id="+uid+"_check_"+i;
					if (COMM_ToBOOL(data[i])) str += " checked";
					str += " disabled>";
					tagTD.innerHTML = str;
					break;
				case "text":
					str = data[i];
					if(typeof(tagTD.innerText) !== "undefined")	tagTD.innerText = str;
					else if(typeof(tagTD.textContent) !== "undefined")	tagTD.textContent = str;
					else	tagTD.innerHTML = str;
					break;
				default:
					str = data[i];
					tagTD.innerHTML = str; 
					break;
				}
			}
		},
		CleanTable: function(tblID)
		{
			table = OBJ(tblID);
			var rows = table.getElementsByTagName("tr");
			while (rows.length > 1) table.deleteRow(rows.length - 1);
		},
		NewWDStyle_init: function()
		{
			NewCheckRadio.init(); //Generate the new styles of checkbox and radio for WD.
			NewSelect.init();//Generate the new style of select for WD.
			NewButton.init();//Generate the new style of button for WD.
			NewHelp.init();//Generate the new style of help box for WD.			
			NewArrow.init();//Generate the new style of button for WD.
		},
		NewWDStyle_refresh: function()
		{
			NewCheckRadio.refresh(); //Refresh the new styles of checkbox and radio for WD.
			NewSelect.refresh();//Refresh the new style of select for WD.
			NewButton.refresh();//Refresh the new style of button for WD.
			NewText.refresh();//Refresh the new style of input text for WD.
		}	
	};
	/**************************************************************************/

	var AUTH = new Authenticate(<?=$AUTHORIZED_GROUP?>, <?echo query("/device/session/timeout");?>);
	var PXML = new PostXML();
	var BODY = new Body();
	var PAGE = <? if (isfile("/htdocs/webinc/js/".$TEMP_MYNAME.".php")==1) echo "new Page();"; else echo "null;"; ?>
	var NewCheckRadio = new New_checkbox_radio();
	var NewSelect = new New_select();
	var NewButton = new New_button();
	var NewHelp = new New_help();
	var NewText = new New_Text();
	var NewArrow = new New_arrow();
<?
	/* generate cookie */
	if ($_SERVER["HTTP_COOKIE"] == "")
		echo 'if (navigator.cookieEnabled) document.cookie = "uid="+COMM_RandomStr(10)+"; path=/";\n';
?>	</script>
</head>

<body class="mainbg" onload="BODY.OnLoad();" onunload="BODY.OnUnload();" link="#1E90FF">	
<?
if($TEMP_STYLE=="adv")
{
	if($layout == "router")	$uid = "LAN-1";
	else 					$uid = "BRIDGE-1";
		
	$inf	= XNODE_getpathbytarget("/runtime", "inf", "uid", $uid , 0); 
	$LanIP = query($inf."/inet/ipv4/ipaddr");
		
	echo '<div id="content" class="advcontainer" style="display:none;">\n';
	alert_bar_display();	
	//Modify the space in the close button to fit different language.
	if($lang=="de") $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	else if($lang=="ru" || $lang=="hu"  || $lang=="pl") $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	else $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";//For WD ITR 53439
	echo '	<div>\n'.
		 '		<div class="top_bar_up"></div>\n'.
		 '		<div class="top_bar">\n'.
		 '			<h1>'.I18N("h", "Advanced Settings").'</h1>\n'.
		 '			<span class="top_bar_close" onclick="self.location.href=\'main_dashboard.php\';"  onmouseover="this.style.cursor = \'pointer\';">'.$space.I18N("h", "Close").'</span>\n'.
		 '		</div>\n'.
		 '	</div>\n'.
		 '	<div class="main_box">\n'.
		 '		<div id="menu" style="height:140px;">\n'.
		 '			<div style="height:127px;">\n';
	//if($layout=="bridge" && $FEATURE_MODEL_NAME=="storage")
	//{echo '				<table class="top_bar_adv_table_ap_remote"> \n';}
	if($layout=="bridge")
	{echo '				<table class="top_bar_adv_table_ap"> \n';}
	else if($FEATURE_MODEL_NAME=="storage")
	{echo '				<table class="top_bar_adv_table_remote"> \n';}
	else
	{echo '				<table class="top_bar_adv_table"> \n';}
	echo '					<tr>';
	if($layout=="router")
	{echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_wan")	.'>'.I18N("h", "WAN").'</td> \n';}
	echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_wlan")	.'>'.I18N("h", "Wireless").'</td> \n'.
		 '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_lan")	.'>'.I18N("h", "LAN").'</td> \n'.	 
		 '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_storage")	.'>'.I18N("h", "Storage").'</td> \n';
	if($layout=="router")
	{echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_secure")	.'>'.I18N("h", "Security").'</td> \n';}		 	 
	if($layout=="router" && $FEATURE_MODEL_NAME=="storage")
	{echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_remote")	.'>'.I18N("h", "Remote Access").'</td> \n';}
	if($layout=="router")
	{echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_add")	.'>'.I18N("h", "Additional Features").'</td> \n';}		 
	echo '						<td class="top_bar_adv_table_content" '.top_bar_display("adv_admin")	.'>'.I18N("h", "Admin").'</td></tr> \n'.
		 '				</table> 																														\n'.	 
		 '			</div>                                                                                                                            \n'.
		 '		</div>\n'.
		 '		<div>\n';
	$function_description = draw_menu($menu, $link, "|", 0);	 
	draw_adv_top_info($function_description);
	echo '		</div>\n'.
		 '		<div>\n'.
		 '			<img src="/pic/u919_line.png" style="width: 100%;">\n'.
		 '		</div>\n'.
		 '		<div style="height:15px;"></div>\n'.
		 '		<div class="leftmenu">\n'.
		 '			<ul>\n';
	draw_menu($menu, $link, "|", 1);
	echo '			</ul>\n'.
		 '		</div>\n'.
		 '		<div id="mainbody" class="mainbody">\n';
	if (isfile("/htdocs/webinc/body/".$_GLOBALS["TEMP_MYNAME"].".php")==1)
	{	dophp("load", "/htdocs/webinc/body/".$_GLOBALS["TEMP_MYNAME"].".php");}
	echo '		</div>\n'.
		 '	</div>\n'.		 
		 '	<div class="copyright">&copy; 2012 Western Digital Technologies, Inc.</div>\n'.		 
		 '</div>\n';		
}
else if($TEMP_STYLE=="main")
{
	echo '<div id="content" class="maincontainer" style="display:none;">																									\n';
	alert_bar_display();
	echo '<div id="menu" style="height: 125px; margin-left: 85px; margin-right: 100px;">                                                \n';
	if($FEATURE_MODEL_NAME!="storage")
	{
		echo '	<div style="height:10px;"></div>	                                                                                            \n';
	}
	echo '	<div style="height:127px;">\n';

	if($layout=="bridge")
	{
		echo '		<table class="top_bar_main_table_ap"> \n';
	}
	else if($FEATURE_MODEL_NAME=="storage")
	{
		echo '		<table class="top_bar_main_table_remote"> \n';
	}
	else
	{
		echo '		<table class="top_bar_main_table"> \n';
	}
	echo '			<tr><td '.top_bar_display("main_dashboard")	.'>'.I18N("h", "My Dashboard").'</td> \n';
	
	if($layout=="router")
	{
		echo '				<td '.top_bar_display("main_internet")	.'>'.I18N("h", "Connect to Internet").'</td> \n';
	}
	
	if($lang=="ru" && $FEATURE_MODEL_NAME=="storage")
	{
		echo '				<td '.top_bar_display("main_wireless")	.'><div style="height:30px; font-size:12px;">'.I18N("h", "Set up Wireless").'</div></td> \n'.
			 '				<td '.top_bar_display("main_storage")	.'>'.I18N("h", "Set up Storage").'</td> \n'.
			 '				<td '.top_bar_display("main_wps")		.'><div style="height:30px; font-size:12px;">'.I18N("h", "Add a Wi-Fi Device").'</div></td> \n';
	}
	else if($lang=="de" && $FEATURE_MODEL_NAME=="storage")
	{
		echo '				<td '.top_bar_display("main_wireless")	.'>'.I18N("h", "Set up Wireless").'</td> \n'.
			 '				<td '.top_bar_display("main_storage")	.'>'.I18N("h", "Set up Storage").'</td> \n'.
			 '				<td '.top_bar_display("main_wps")		.'><div style="height:30px; font-size:12px;">'.I18N("h", "Add a Wi-Fi Device").'</div></td> \n';
	}
	else if($lang=="cs" && $FEATURE_MODEL_NAME=="storage")
	{
		echo '				<td '.top_bar_display("main_wireless")	.'><div style="height:33px; font-size:11px;">'.I18N("h", "Set up Wireless").'</div></td> \n'.
			 '				<td '.top_bar_display("main_storage")	.'>'.I18N("h", "Set up Storage").'</td> \n'.
			 '				<td '.top_bar_display("main_wps")		.'>'.I18N("h", "Add a Wi-Fi Device").'</div></td> \n';
	}
	else
	{
		echo '				<td '.top_bar_display("main_wireless")	.'>'.I18N("h", "Set up Wireless").'</div></td> \n'.
			 '				<td '.top_bar_display("main_storage")	.'>'.I18N("h", "Set up Storage").'</td> \n'.
			 '				<td '.top_bar_display("main_wps")		.'>'.I18N("h", "Add a Wi-Fi Device").'</div></td> \n';
	}
	
	if($layout=="router" && $FEATURE_MODEL_NAME=="storage")
	{
		if($lang=="ru")
		{
			echo '				<td '.top_bar_display("main_remote")		.'><div style="height:39px; font-size:11px;">'.I18N("h", "Set up Remote Access").'</div></td> \n';
		}
		else
		{
			echo '				<td '.top_bar_display("main_remote")		.'>'.I18N("h", "Set up Remote Access").'</td> \n';
		}
	}
	echo '				<td '.top_bar_display("main_adv")		.'>'.I18N("h", "Advanced Settings").'</td></tr> \n'.
		 '		</table> 																														\n'.	 
		 '	</div>                                                                                                                            \n'.
		 '</div>                                                                                                                       		\n'.
		 '<div style="height:45px;width:800px;margin-left: 85px;"><!--Main top bar information-->                                          \n'.
		 '	<div style="height:35px;line-height:35px;width: 800px">		                                                    				\n';
	draw_main_top_info($_GLOBALS["TEMP_MYGROUP"]);
	echo '	</div>\n';
	if ($_GLOBALS["TEMP_MYGROUP"] == "main_dashboard")
    {
        echo "<div style=\"height: 6px;width: 800px;\"></div>\n";
    }
	echo '	<div><img src="pic/line.png" style="height: 4px;width: 800px;"></div>                     \n'.
		 '</div>                                                     																		\n'.
		 '<div id="page" style="height: 355px;margin-left: 85px; margin-right: 100px;">                                                 	\n';
			if (isfile("/htdocs/webinc/body/".$TEMP_MYNAME.".php")==1) dophp("load", "/htdocs/webinc/body/".$TEMP_MYNAME.".php");	
	echo '</div>																															\n'.
		 '<div>                                                                                                        						\n';
	if($layout=="router")
	{
		if($TEMP_MYGROUP=="main_dashboard")
			echo '	<a href="wan_status.php"><img src="/pic/main_bottom_adv_set.png" border="0" style="width: 1005px;"></a> \n';
		//else
			//echo '	<img src="/pic/main_bottom_adv_set.png" border="0" style="width: 1005px;"> \n';
	}
	else
	{
		if($TEMP_MYGROUP=="main_dashboard")
			echo '	<a href="lan.php"><img src="/pic/main_bottom_adv_set.png" border="0" style="width: 1005px;"></a> \n';
		//else
			//echo '	<img src="/pic/main_bottom_adv_set.png" border="0" style="width: 1005px;"> \n';
	}

	echo '</div>                                                                                                                            \n'.
		 '<div class="copyright">&copy; 2012 Western Digital Technologies, Inc.</div>													\n'.
		 '</div>	 <!--  content end here -->																								\n'; 
}
else if($TEMP_STYLE=="simple")
{
	echo '<div id="content" style="display:none;">\n'.
		 '	<div id="menu"></div>\n';
			if (isfile("/htdocs/webinc/body/".$TEMP_MYNAME.".php")==1) dophp("load", "/htdocs/webinc/body/".$TEMP_MYNAME.".php");
	echo '</div>\n';
}
?>
	<!-- Start of Login Body -->
	<div id="login" class="login_body" style="display:none;">	
		<div class="login_box">
			<div class="login_box_top"></div>
			<div class="login_box_middle">
				<div class="gap"></div>
				<div class="login_box_info"><img src="/pic/mark.png" style="float:left;"></div>
				<div class="gap"></div>
				<div class="gap"></div>
				<div class="login_box_info"><?echo I18N("h", "Welcome.");?></div>					
				<div class="login_box_info"><?echo I18N("h", "Please sign in.");?></div>
				<div class="gap"></div>
				<div class="gap"></div>
				<br>
				<div><?echo I18N("h", "User Name");?></div>
				<div><input type="text" style="height:25px;width:250px;" id="loginusr" maxlength="32" onkeydown="BODY.OnKeydown(event);" /></div>
				<div class="gap"></div>
				<div class="gap"></div>
				<div><?echo I18N("h", "Password");?></div>
				<div><input type="password" style="height:25px;width:250px;" id="loginpwd" maxlength="32" onkeydown="BODY.OnKeydown(event);"/></div>
				<div class="gap"></div>
				<div class="gap"></div>
				<div>
					<table>
						<tr>
							<td align="center"><span></span><input class="styled2" type="checkbox" id="remember" /></td>
							<td align="center">&nbsp;<? echo I18N("h","Remember me");?></td>
						</tr>					
					</table> 
				</div>
				<div class="gap"></div>
				<? 
					if($lang=='tr') echo '<div style="padding-left:80px;"><input class="button_blueX2" type="button" value="'.I18N("h", "Submit").'" onclick="BODY.LoginSubmit();" /></div>';
					else echo '<div style="padding-left:190px;"><input class="button_blue" type="button" value="'.I18N("h", "Submit").'" onclick="BODY.LoginSubmit();" /></div>';
				?>
				<div class="gap"></div>
			</div>
			<div class="login_box_bottom"></div>
		</div>
	</div>
	<!-- End of Login Body -->
	<!-- Start of Message Box -->
	<div id="mbox" class="msg_body" style="display:none;">
		<div class="msg_box">
			<div class="msg_box_top"></div>
			<div class="msg_box_middle">
				<div style="width: 620px;"><span id="message"></span></div>
			</div>
			<div class="msg_box_bottom"></div>
		</div>
	</div>
	<!-- End of Message Box -->

	<!-- Start of Message Box -->
	<div id="mbox2" class="msg_body" style="display:none;">
		<div class="msg_box">
			<div class="msg_box_top"></div>
			<div class="msg_box_middle">		
				<div style="width: 620px;"><span id="msg"></span></div>
			</div>
			<div class="msg_box_bottom"></div>
		</div>
	</div>
	<!-- End of Message Box -->

	<div id="mbox_ex" class="msg_body" style="display:none;">
        <div class="msg_box">
            <div class="msg_box_top"></div>
            <div class="msg_box_middle">
                <div style="width: 620px;"><span id="message_ex"></span></div>
				<div id="process_bar_ex" class="pb_item">
    				<div id="pb_1" class="pb_item item1" style="display:none;"></div>
    				<div id="pb_2" class="pb_item item2" style="display:none;"></div>
    				<div id="pb_3" class="pb_item item3" style="display:none;"></div>
    				<div id="pb_4" class="pb_item item4" style="display:none;"></div>
    				<div id="pb_5" class="pb_item item5" style="display:none;"></div>
    				<div id="pb_6" class="pb_item item6" style="display:none;"></div>
    				<div id="pb_7" class="pb_item item7" style="display:none;"></div>
    				<div id="pb_8" class="pb_item item8" style="display:none;"></div>
    				<div id="pb_9" class="pb_item item9" style="display:none;"></div>
    				<div id="pb_10" class="pb_item item10" style="display:none;"></div>
    				<div id="pb_11" class="pb_item item11" style="display:none;"></div>
    				<div id="pb_12" class="pb_item item12" style="display:none;"></div>
    				<div id="pb_13" class="pb_item item13" style="display:none;"></div>
    				<div id="pb_14" class="pb_item item14" style="display:none;"></div>
    				<div id="pb_15" class="pb_item item15" style="display:none;"></div>
    				<div id="pb_16" class="pb_item item16" style="display:none;"></div>
    				<div id="pb_17" class="pb_item item17" style="display:none;"></div>
    				<div id="pb_18" class="pb_item item18" style="display:none;"></div>
    				<div id="pb_19" class="pb_item item19" style="display:none;"></div>
    				<div id="pb_20" class="pb_item item20" style="display:none;"></div>
					<div id="timer_ex" class="pb_item item26"></div>
    				<div class="pb_item item27"></div>
				</div>
				<div class="emptyline"></div>
			</div>
			<div class="msg_box_bottom"></div>
		</div>
	</div>
</body>
</html>
