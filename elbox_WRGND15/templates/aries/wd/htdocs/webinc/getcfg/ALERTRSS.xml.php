<?
			include "/htdocs/phplib/xnode.php";
			include "/htdocs/webinc/config.php";
			include "/htdocs/phplib/wifi.php";	
						
			$notification_n	= 0;
			$wlan1_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0)."/active");
			$wlan2_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0)."/active");
			$rss_date = query("/runtime/device/rss_date");
				
			//Check firmware to update.
			if(query("/runtime/alertmsg/firmware_new/ignore")=="1") $firmware_new = 0;
			else if(query("/runtime/device/upgrades/available")=="true")	$firmware_new = 1;
			else 								$firmware_new = 0;
		
			if($firmware_new==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/firmware_new/alert_time") == "")
					set("/runtime/alertmsg/firmware_new/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/firmware_new/alert_time") != "")
					set("/runtime/alertmsg/firmware_new/alert_time", "");
			}

			//Check the wireless configuration.
			if(query("/runtime/alertmsg/wireless_unconfig/ignore")=="1") $wireless_unconfig = 0;
			else
			{
				$wireless_unconfig = 1;
				if(query(WIFI_getpathbyphyinf($WLAN1)."/wps/configured")=="0" && query(WIFI_getpathbyphyinf($WLAN2)."/wps/configured")=="0") $wlan_desc = I18N("h", "2.4GHz and 5GHz");
				else if(query(WIFI_getpathbyphyinf($WLAN1)."/wps/configured")=="0") $wlan_desc = "2.4GHz";
				else if(query(WIFI_getpathbyphyinf($WLAN2)."/wps/configured")=="0") $wlan_desc = "5GHz";
				else $wireless_unconfig = 0;
				if($wlan1_enable!=1 && $wlan2_enable!=1)
				{
					$wireless_unconfig = 0;
				}
				$wireless_unconfig_desc = $wlan_desc." ".I18N("h", "wireless security is not configured.");			
			}
			if($wireless_unconfig==1)
			{					
				$notification_n++;
				if(query("/runtime/alertmsg/wireless_unconfig/alert_time") == "")
					set("/runtime/alertmsg/wireless_unconfig/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/wireless_unconfig/alert_time") != "")
					set("/runtime/alertmsg/wireless_unconfig/alert_time", "");
			}
			//Check the wireless security.
			if(query("/runtime/alertmsg/wireless_open/ignore")=="1") $wireless_open = 0;
			else
			{			
				$wlan1_open=0;
				$wlan2_open=0;
				if(query(WIFI_getpathbyphyinf($WLAN1)."/authtype")=="OPEN") $wlan1_open=1;
				if(query(WIFI_getpathbyphyinf($WLAN2)."/authtype")=="OPEN") $wlan2_open=1;			
						
				$wireless_open=1;		
				if($wlan1_enable==1 && $wlan1_open==1 && $wlan2_enable==1 && $wlan2_open==1) $wlan_desc = I18N("h", "2.4GHz and 5GHz");
				else if($wlan1_enable==1 && $wlan1_open==1) $wlan_desc = "2.4GHz";
				else if($wlan2_enable==1 && $wlan2_open==1) $wlan_desc = "5GHz";
				else $wireless_open = 0;
				$wireless_open_desc = $wlan_desc." ".I18N("h", "wireless security has been set to Open.");			
			}
			if($wireless_open==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/wireless_open/alert_time") == "")
					set("/runtime/alertmsg/wireless_open/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/wireless_open/alert_time") != "")
					set("/runtime/alertmsg/wireless_open/alert_time", "");
			}
			//Check the password of admin is default or not.
			if(query("/runtime/alertmsg/admin_password/ignore")=="1") $admin_password = 0;
			else
			{			
				if(query(XNODE_getpathbytarget("/device/account", "entry", "name", "admin", 0)."/password")=="password") $admin_password = 1;
				else $admin_password = 0;
			}
			if($admin_password==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/admin_password/alert_time") == "")
					set("/runtime/alertmsg/admin_password/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/admin_password/alert_time") != "")
					set("/runtime/alertmsg/admin_password/alert_time", "");
			}
			//Check the storage configuration.			
			if(query("/runtime/alertmsg/storage_unconfig/ignore")=="1") $storage_unconfig = 0;
			else
			{
				$storage_username = query("/wd/storage/username");
				$storage_password = query("/wd/storage/password");
				$public_share = query("/wd/storage/public_share");
				$USB1_n = query("/runtime/wd/USB1/entry#");
				$USB2_n = query("/runtime/wd/USB2/entry#");
				if($USB1_n > 0 || $USB2_n > 0)
				{
					if ($public_share == "1")
					{
						$storage_unconfig = 0;
					}
					else if($storage_username=="wd_user" && $storage_password=="")
					{ $storage_unconfig = 1;}
				}
				else $storage_unconfig = 0;
			}
			if($storage_unconfig==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/storage_unconfig/alert_time") == "")
					set("/runtime/alertmsg/storage_unconfig/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/storage_unconfig/alert_time") != "")
					set("/runtime/alertmsg/storage_unconfig/alert_time", "");
			}
			
			//Check the WD drive locked or not.			
			if(query("/runtime/alertmsg/WD_drive_locked/ignore")=="1") $WD_drive_locked = 0;
			else
			{
				$USB1_n = query("/runtime/wd/USB1/entry#");
				$USB2_n = query("/runtime/wd/USB2/entry#");
				$WD_drive_locked = 0;
				if($USB1_n!=0 || $USB2_n!=0)
				{
					if($USB1_n!=0)
					{
						foreach("/runtime/wd/USB1/entry")
						{
							if(query("lock_status")=="LOCK")
							{
								$WD_drive_locked = 1;
								break;
							}
						}
					}
					if($USB2_n!=0 && $WD_drive_locked==0)
					{
						foreach("/runtime/wd/USB2/entry")
						{
							if(query("lock_status")=="LOCK")
							{
								$WD_drive_locked = 1;
								break;
							}
						}
					}					
				}
			}
			if($WD_drive_locked==1) 
			{
				$notification_n++;
				if(query("/runtime/alertmsg/WD_drive_locked/alert_time") == "")
					set("/runtime/alertmsg/WD_drive_locked/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/WD_drive_locked/alert_time") != "")
					set("/runtime/alertmsg/WD_drive_locked/alert_time", "");
			}
			
			//Check the temperature of internal drive is above normal level or not.			
			if(query("/runtime/alertmsg/temperature_high/ignore")=="1") $temperature_high = 0;
			else $temperature_high = 0; //not complete Joseph
			if($temperature_high==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/temperature_high/alert_time") == "")
					set("/runtime/alertmsg/temperature_high/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/temperature_high/alert_time") != "")
					set("/runtime/alertmsg/temperature_high/alert_time", "");			 
			}
			
			//Check the internet connection.
			if(query("/runtime/alertmsg/WAN_detect_fail/ignore")=="1") $WAN_detect_fail = 0;
			else if(query("/runtime/alertmsg/WAN_detect_fail/connect")=="1") $WAN_detect_fail = 0;
			else $WAN_detect_fail = 1; 			
			if($WAN_detect_fail==1)
			{	
				$notification_n++;
				if(query("/runtime/alertmsg/WAN_detect_fail/alert_time") == "")
					set("/runtime/alertmsg/WAN_detect_fail/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/WAN_detect_fail/alert_time") != "")
					set("/runtime/alertmsg/WAN_detect_fail/alert_time", "");				
			}

			//Check the user is registered or not.
			if(query("/runtime/alertmsg/unregister/ignore")=="1") $unregister = 0;
			else if(query("/runtime/devdata/register")!="1") $unregister = 1;
			else $unregister = 0;
			if($unregister==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/unregister/alert_time") == "")
					set("/runtime/alertmsg/unregister/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/unregister/alert_time") != "")
					set("/runtime/alertmsg/unregister/alert_time", "");
			}
			
			//Check the wirless guest zone is enable or not.
			if(query("/runtime/alertmsg/wireless_guest_disable/ignore")=="1") $wireless_guest_disable = 0;
			else
			{
				if(query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1_GZ, 0)."/active")=="0") $wireless24g_guest_disable = 1;
				if(query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2_GZ, 0)."/active")=="0") $wireless5g_guest_disable = 1;
			}
			if($wlan1_enable==1 && $wireless24g_guest_disable==1) $wireless_guest_disable = 1;
			else if($wlan2_enable==1 && $wireless5g_guest_disable==1) $wireless_guest_disable = 1;
			else $wireless_guest_disable = 0;

			if($wireless_guest_disable==1) 
			{
				$notification_n++;		
				if(query("/runtime/alertmsg/wireless_guest_disable/alert_time") == "")
					set("/runtime/alertmsg/wireless_guest_disable/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/wireless_guest_disable/alert_time") != "")
					set("/runtime/alertmsg/wireless_guest_disable/alert_time", "");
			}

			//Check the password of wireless is the same as that of wireless guest zone or not.
			if(query("/runtime/alertmsg/wireless_guest_pwd_same/ignore")=="1") $wireless_guest_pwd_same = 0;
			else
			{			
				$wireless1_guest_pwd = query(WIFI_getpathbyphyinf($WLAN1_GZ)."/nwkey/psk/key");
				if(query(WIFI_getpathbyphyinf($WLAN1)."/encrtype")=="WEP") $wlan1_pwd = query(WIFI_getpathbyphyinf($WLAN1)."/nwkey/wep/key:1");
				else if(cut(query(WIFI_getpathbyphyinf($WLAN1)."/authtype"), 0, "PSK")!="") $wlan1_pwd = query(WIFI_getpathbyphyinf($WLAN1)."/nwkey/psk/key");
				else $wlan1_pwd = query(WIFI_getpathbyphyinf($WLAN1)."/nwkey/eap/secret");
				
				$wireless2_guest_pwd = query(WIFI_getpathbyphyinf($WLAN2_GZ)."/nwkey/psk/key");
				if(query(WIFI_getpathbyphyinf($WLAN2)."/encrtype")=="WEP") $wlan2_pwd = query(WIFI_getpathbyphyinf($WLAN2)."/nwkey/wep/key:1");
				else if(cut(query(WIFI_getpathbyphyinf($WLAN2)."/authtype"), 0, "PSK")!="") $wlan2_pwd = query(WIFI_getpathbyphyinf($WLAN2)."/nwkey/psk/key");
				else $wlan2_pwd = query(WIFI_getpathbyphyinf($WLAN2)."/nwkey/eap/secret");			
				
				if(query(WIFI_getpathbyphyinf($WLAN2_GZ)."/authtype")=="OPEN" && query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN") $wireless_guest_pwd_same = 0;
				else
				{
					//$wlan1_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0)."/active");
					//$wlan2_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0)."/active");
					//$wlan1gz_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1_GZ, 0)."/active");
					//$wlan2gz_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2_GZ, 0)."/active");
					$wireless_guest_pwd_same = 0;	
					if($wlan1_enable=="1")
					{
						if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN"||query(WIFI_getpathbyphyinf($WLAN2_GZ)."/authtype")=="OPEN")
						{
							if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN")
							{
								if($wireless2_guest_pwd==$wlan1_pwd )
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
							if(query(WIFI_getpathbyphyinf($WLAN2_GZ)."/authtype")=="OPEN")
							{
								if($wireless1_guest_pwd==$wlan1_pwd )
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
						}
						else
						{
							if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN")
							{
								if($wireless2_guest_pwd==$wlan1_pwd ||$wireless1_guest_pwd==$wlan1_pwd)
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
						}	 	
					}
					if($wlan2_enable=="1")
					{
						if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN"||query(WIFI_getpathbyphyinf($WLAN2_GZ)."/authtype")=="OPEN")
						{
							if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN")
							{
								if($wireless2_guest_pwd==$wlan2_pwd )
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
							if(query(WIFI_getpathbyphyinf($WLAN2_GZ)."/authtype")=="OPEN")
							{
								if($wireless1_guest_pwd==$wlan2_pwd )
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
						}
				else
				{
							if(query(WIFI_getpathbyphyinf($WLAN1_GZ)."/authtype")=="OPEN")
							{
								if($wireless2_guest_pwd==$wlan2_pwd ||$wireless1_guest_pwd==$wlan2_pwd)
							  {
								  $wireless_guest_pwd_same = 1;
								}
							}
						}	 	
					}	
				}
			}
			if($wireless_guest_pwd_same==1)
			{
				$notification_n++;
				if(query("/runtime/alertmsg/wireless_guest_pwd_same/alert_time") == "")
					set("/runtime/alertmsg/wireless_guest_pwd_same/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/wireless_guest_pwd_same/alert_time") != "")
					set("/runtime/alertmsg/wireless_guest_pwd_same/alert_time", "");
			}	
			//Show the uplink speed by auto detection of QoS function.
			$wan = query("/runtime/device/activewan");
			if($wan == "") $wan = "WAN-1";
			$run_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $wan, 0);
			
			$qos_enable = query("/bwc/entry:1/enable");
			$qos_autodetect = query("/bwc/entry:1/autobandwidth");
			$qos_autobandwidth = query($run_infp."/auto_detect_bw");
			
			if(query("/runtime/alertmsg/QoS_auto_detect/ignore")=="1") $QoS_auto_detect = 0;
			else if($qos_enable=="1" && $qos_autodetect=="1" && $qos_autobandwidth!="0" && $qos_autobandwidth!="") $QoS_auto_detect = 1;
			else $QoS_auto_detect = 0;
			if($QoS_auto_detect==1) 
			{
				$notification_n++;
				if(query("/runtime/alertmsg/QoS_auto_detect/alert_time") == "")
					set("/runtime/alertmsg/QoS_auto_detect/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/QoS_auto_detect/alert_time") != "")
					set("/runtime/alertmsg/QoS_auto_detect/alert_time", "");			
			}
			
			//Check auto detection is failed or not of QoS function.
			if(query("/runtime/alertmsg/QoS_auto_detect_fail/ignore")=="1") $QoS_auto_detect_fail = 0;
			else if($qos_enable=="1" && $qos_autodetect=="1" && $qos_autobandwidth=="0") $QoS_auto_detect_fail = 1;
			else $QoS_auto_detect_fail = 0;
			if($QoS_auto_detect_fail==1) 
			{
				$notification_n++;
				if(query("/runtime/alertmsg/QoS_auto_detect_fail/alert_time") == "")
					set("/runtime/alertmsg/QoS_auto_detect_fail/alert_time", $rss_date);
			}
			else
			{
				if(query("/runtime/alertmsg/QoS_auto_detect_fail/alert_time") != "")
					set("/runtime/alertmsg/QoS_auto_detect_fail/alert_time", "");
			}
			
			
			if($notification_n == 0) $notification_n_desc = I18N("h", "You have no notification");
			else $notification_n_desc = I18N("h", "You have $1 notification(s)", $notification_n);			
			
			$host_name = query("/device/hostname");
			$ipaddr = query("/runtime/services/http/server:1/ipaddr");
?>
<?echo '<?xml version="1.0" encoding="utf-8"?>';?>
<rss version="2.0">
<channel>
<title><?=$host_name?> Alerts</title>
<link>http://<?=$ipaddr?></link>
<description>The last alerts</description>
<lastBuildDate><?=$rss_date?></lastBuildDate>
<cloud></cloud>
<?
	if ($firmware_new == "1")
	{
		echo "<item>\n";   
		echo "<title>".I18N("h", "Firmware update available")."</title>\n";                                              
		echo "<description>".I18N("h", "New firmware is available.")."</description>\n";       
		$firmware_new_time = query("/runtime/alertmsg/firmware_new/alert_time");
  		echo "<pubDate>".$firmware_new_time."</pubDate>\n"; 
  		echo "</item>\n";            
	}
	if($wireless_unconfig == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Wireless security is not configured.")."</title>\n";
		echo "<description>".I18N("h", "The router's wireless settings are set to default.")."</description>\n";
		$wireless_unconfig_time = query("/runtime/alertmsg/wireless_unconfig/alert_time");
		echo "<pubDate>".$wireless_unconfig_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($wireless_open == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Wireless security has been set to open.")."</title>\n";
		echo "<description>".I18N("h", "Wireless security has not been configured on router.")."</description>\n";
		$wireless_open_time = query("/runtime/alertmsg/wireless_open/alert_time");
		echo "<pubDate>".$wireless_open_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($admin_password == "1")
	{	
		echo "<item>\n";
		echo "<title>".I18N("h", "Admin password is default value.")."</title>\n";
		echo "<description>".I18N("h", "The router's administrator password is set to default.")."</description>\n";
		$admin_password_time = query("/runtime/alertmsg/admin_password/alert_time");
		echo "<pubDate>".$admin_password_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($storage_unconfig == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Storage is not configured")."</title>\n";
		echo "<description>".I18N("h", "Storage attached to the router has not been configured.")."</description>\n";
		$storage_unconfig_time = query("/runtime/alertmsg/storage_unconfig/alert_time");
		echo "<pubDate>".$storage_unconfig_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($WD_drive_locked == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Western Digital drive is locked.")."</title>\n";
		echo "<description>".I18N("h", "A password encrypted Western Digital drive is connected to the router.")."</description>\n";
		$WD_drive_locked_time = query("/runtime/alertmsg/WD_drive_locked/alert_time");
		echo "<pubDate>".$WD_drive_locked_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($temperature_high == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "The internal drive's temperature is above normal level")."</title>\n";
		echo "<description>".I18N("h", "The internal drive's temperature above normal level. Make sure it has proper ventilation, and move it to a cooler location if necessary.")."</description>\n";
		$temperature_high_time = query("/runtime/alertmsg/temperature_high/alert_time");
		echo "<pubDate>".$temperature_high_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($WAN_detect_fail == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Unable to connect to Internet")."</title>\n";
		echo "<description>".I18N("h", "Internet connection is not detected.")."</description>\n";
		$WAN_detect_fail_time = query("/runtime/alertmsg/WAN_detect_fail/alert_time");
		echo "<pubDate>".$WAN_detect_fail_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($unregister == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "The product has not been registered")."</title>\n";
		echo "<description>".I18N("h", "Please register your product to make sure you get warranty service.")."</description>\n";
		$unregister_time = query("/runtime/alertmsg/unregister/alert_time");
		echo "<pubDate>".$unregister_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($wireless_guest_disable == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "Guest wireless access has not been set up")."</title>\n";
		echo "<description>".I18N("h", "Guest wireless access has not been set up.")."</description>\n";
		$wireless_guest_disable_time = query("/runtime/alertmsg/wireless_guest_disable/alert_time");
		echo "<pubDate>".$wireless_guest_disable_time."</pubDate>\n";
		echo "</item>\n";
	}
	if($wireless_guest_pwd_same == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "The wireless passwords are the same")."</title>\n";
		echo "<description>".I18N("h", "The password for Guest wireless network is same as that of your regular wireless network. It is strongly advised that you set different password for security.")."</description>\n";
		$wireless_guest_pwd_same_time = query("/runtime/alertmsg/wireless_guest_pwd_same/alert_time");
		echo "<pubDate>".$wireless_guest_pwd_same_time."</pubDate>\n";
		echo "</item>\n";
	}
        if($QoS_auto_detect == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "The uplink WAN speed is detected. ")."</title>\n";
		echo "<description>".I18N("h", "The uplink WAN speed detection is for QoS optimization and it is currently detected to be $1 Kbps. If you know your uplink speed to be different, then click OK to change the speed manually.", $qos_autobandwidth)."</description>\n";
		$QoS_auto_detect_time = query("/runtime/alertmsg/QoS_auto_detect/alert_time");
		echo "<pubDate>".$QoS_auto_detect_time."</pubDate>\n";
		echo "</item>\n";
	}
        if($QoS_auto_detect_fail == "1")
	{
		echo "<item>\n";
		echo "<title>".I18N("h", "The QoS auto detection failed")."</title>\n";
		echo "<description>".I18N("h", "The WAN uplink speed by auto detection is 0 Kbps.")."</description>\n";
		$QoS_auto_detect_fail_time = query("/runtime/alertmsg/QoS_auto_detect_fail/alert_time");
		echo "<pubDate>".$QoS_auto_detect_fail_time."</pubDate>\n";
		echo "</item>\n";
	} 
?>	
</channel>
</rss>
