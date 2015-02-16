<module>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>	
	<ACTIVATE>ignore</ACTIVATE>	
	<service><?=$GETCFG_SVC?></service><?
			include "/htdocs/phplib/xnode.php";
			include "/htdocs/webinc/config.php";
			include "/htdocs/phplib/wifi.php";
			include "/htdocs/phplib/phyinf.php";
			include "/htdocs/phplib/inet.php";
			include "/htdocs/webinc/feature.php";//For QoS notification settings
			$device_mode = query("/device/layout");
			$notification_n	= 0;
			$wlan1_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN1, 0)."/active");
			$wlan2_enable = query(XNODE_getpathbytarget("", "phyinf", "uid", $WLAN2, 0)."/active");
					
			//Check firmware to update.
			if(query("/runtime/alertmsg/firmware_new/ignore")=="1") $firmware_new = 0;
			else if(query("/runtime/device/upgrades/available")=="true") $firmware_new = 1;
			else $firmware_new = 0;
			if($firmware_new==1) $notification_n++;
			
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
				if($wireless_unconfig==1) $notification_n++;
				$wireless_unconfig_desc = $wlan_desc." ".I18N("h", "wireless security is not configured.");			
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
				if($wireless_open==1) $notification_n++;
				$wireless_open_desc = $wlan_desc." ".I18N("h", "wireless security has been set to Open.");			
			}
			
			//Check the password of admin is default or not.
			if(query("/runtime/alertmsg/admin_password/ignore")=="1") $admin_password = 0;
			else
			{			
				if(query(XNODE_getpathbytarget("/device/account", "entry", "name", "admin", 0)."/password")=="password") $admin_password = 1;
				else $admin_password = 0;
				if($admin_password==1) $notification_n++;
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
			if($storage_unconfig==1) $notification_n++;
			
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
			if($WD_drive_locked==1) $notification_n++;
			
			//Check the temperature of internal drive is above normal level or not.			
			if(query("/runtime/alertmsg/temperature_high/ignore")=="1") $temperature_high = 0;
			else $temperature_high = 0; //not complete Joseph
			if($device_mode=="bridge") $temperature_high = 0;
			if($temperature_high==1) $notification_n++;
			
			//Check the internet network status.
			/*
			$wan1_inet = INET_getpathbyinf("WAN-1");
			$wan1_rinf = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", "0");
			$wan1_rphy = PHYINF_getphypath("WAN-1");
			$wan1_cable_status = 0;
			if(query($wan1_rphy."/linkstatus")!="0" && query($wan1_rphy."/linkstatus")!="")
			{
				$wan1_cable_status=1;
			}
			$wan1_network_status = 0;
			if (query($wan1_inet."/addrtype") == "ipv4")
			{
				if(query($wan1_inet."/ipv4/static")== "1") //Static IP
				{
					if($wan1_cable_status==1) $wan1_network_status = 1;
				}
				else //DHCP Client
				{
					if (query($wan1_rinf."/inet/ipv4/valid")=="1" && $wan1_cable_status==1)
					{
						$wan1_network_status = 1;
					}
				}
			}
			else if(query($wan1_inet."/addrtype")=="ppp4" || query($wan1_inet."/addrtype")=="ppp10")
			{
				if(query($wan1_rinf."/inet/ppp4/valid")=="1" && $wan1_cable_status==1)
				{
					$wan1_network_status = 1;
				} 
			}
			*/
			/* New way to check the internet network status.
				It would ping to the internet after WAN get IP. If the ping action is successful, the runtime node would be set up. 
			*/
			if(query("/runtime/device/wan_status")=="1") $wan1_network_status=1;
			else $wan1_network_status=0;
			if($wan1_network_status==1)	set("/runtime/alertmsg/WAN_detect_fail/connect", 1);
			else	set("/runtime/alertmsg/WAN_detect_fail/connect", 0);		
			
			//Check the internet connection.
			if(query("/runtime/alertmsg/WAN_detect_fail/ignore")=="1") $WAN_detect_fail = 0;
			else if(query("/runtime/alertmsg/WAN_detect_fail/connect")=="1") $WAN_detect_fail = 0;
			else $WAN_detect_fail = 1; 			
			if($WAN_detect_fail==1) $notification_n++;

			//Check the user is registered or not.
			if(query("/runtime/alertmsg/unregister/ignore")=="1") $unregister = 0;
			else if(query("/runtime/devdata/register")!="1") $unregister = 1;
			else $unregister = 0;
			if($unregister==1) $notification_n++;
			
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
			if($device_mode=="bridge") $wireless_guest_disable = 0;
			if($wireless_guest_disable==1) $notification_n++;
			
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
				if($device_mode=="bridge") $wireless_guest_pwd_same=0;
				if($wireless_guest_pwd_same==1) $notification_n++;
			}
			//Show the uplink speed by auto detection of QoS function.
			$wan = query("/runtime/device/activewan");
			if($wan == "") $wan = "WAN-1";
			$run_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $wan, 0);
			
			$qos_enable = query("/bwc/entry:1/enable");
			$qos_autodetect = query("/bwc/entry:1/autobandwidth");
			$qos_autobandwidth = query($run_infp."/auto_detect_bw");
			$qos_BandWidthField = query($run_infp."/bwc/BandWidthField");
			
			if($FEATURE_MODEL_NAME=="storage"||$FEATURE_MODEL_NAME=="pro")
			{//pro and storage only support one message
				if(query("/runtime/alertmsg/QoS_auto_detect/ignore")=="1") $QoS_auto_detect = 0;
				else if($qos_enable=="1" && $qos_autodetect=="1" && $qos_autobandwidth!="0" && $qos_autobandwidth!="" && $qos_BandWidthField!="RUSSIA") $QoS_auto_detect = 1;
				else $QoS_auto_detect = 0;
				if($device_mode == "bridge") $QoS_auto_detect = 0;
				if($QoS_auto_detect==1) $notification_n++;

				$QoS_auto_detect_fail = 0;//never support this message.
			}
			else
			{//av and db600 support 2 messages
				if(query("/runtime/alertmsg/QoS_auto_detect/ignore")=="1") $QoS_auto_detect = 0;
				else if($qos_enable=="1" && $qos_autodetect=="1" && $qos_autobandwidth!="0" && $qos_autobandwidth!="" && $qos_BandWidthField!="FAKE" && $qos_BandWidthField!="RUSSIA") $QoS_auto_detect = 1;
				else $QoS_auto_detect = 0;
				if($device_mode == "bridge") $QoS_auto_detect = 0;
				if($QoS_auto_detect==1) $notification_n++;

				//Check auto detection is failed or not of QoS function.
				if(query("/runtime/alertmsg/QoS_auto_detect_fail/ignore")=="1") $QoS_auto_detect_fail = 0;
				else if($qos_BandWidthField=="FAKE") $QoS_auto_detect_fail = 1;
				else $QoS_auto_detect_fail = 0;
				if($device_mode == "bridge") $QoS_auto_detect_fail=0;
				if($QoS_auto_detect_fail==1) $notification_n++;
			}
			
			//Check Brute force WPS attack.
			if(query("/runtime/alertmsg/WPS_PIN_disabled/ignore")=="1") $WPS_PIN_disabled = 0;
			else if(query("/runtime/wps/setting/aplocked")=="1" && query("/runtime/wps/setting/aplocked_byuser")!="1") $WPS_PIN_disabled = 1;
			else $WPS_PIN_disabled = 0;
			if($WPS_PIN_disabled==1) $notification_n++;
			
			if($notification_n == 0) $notification_n_desc = I18N("h", "You have no notification");
			else $notification_n_desc = I18N("h", "You have $1 notification(s)", $notification_n);			
		?>
	<alertmsg>
		<firmware_new>
			<alert><?=$firmware_new?></alert>
		</firmware_new>
		<wireless_unconfig>
			<alert><?=$wireless_unconfig?></alert>
			<descript><?=$wireless_unconfig_desc?></descript>
		</wireless_unconfig>
		<wireless_open>
			<alert><?=$wireless_open?></alert>
			<descript><?=$wireless_open_desc?></descript>
		</wireless_open>
		<admin_password>
			<alert><?=$admin_password?></alert>
		</admin_password>
		<storage_unconfig>
			<alert><?=$storage_unconfig?></alert>
		</storage_unconfig>
		<WD_drive_locked>
			<alert><?=$WD_drive_locked?></alert>
		</WD_drive_locked>
		<temperature_high>
			<alert><?=$temperature_high?></alert>
		</temperature_high>
		<WAN_network_status>
			<alert><?=$wan1_network_status?></alert>
		</WAN_network_status>		
		<WAN_detect_fail>
			<alert><?=$WAN_detect_fail?></alert>
		</WAN_detect_fail>
		<unregister>
			<alert><?=$unregister?></alert>
		</unregister>
		<wireless_guest_disable>
			<alert><?=$wireless_guest_disable?></alert>
		</wireless_guest_disable>
		<wireless_guest_pwd_same>
			<alert><?=$wireless_guest_pwd_same?></alert>
		</wireless_guest_pwd_same>
		<QoS_auto_detect>
			<alert><?=$QoS_auto_detect?></alert>			
		</QoS_auto_detect>
		<QoS_auto_detect_fail>
			<alert><?=$QoS_auto_detect_fail?></alert>
		</QoS_auto_detect_fail>
		<WPS_PIN_disabled>
			<alert><?=$WPS_PIN_disabled?></alert>
		</WPS_PIN_disabled>
		<notification_n>
			<descript><?=$notification_n_desc?></descript>
		</notification_n>		
	</alertmsg>
</module>
