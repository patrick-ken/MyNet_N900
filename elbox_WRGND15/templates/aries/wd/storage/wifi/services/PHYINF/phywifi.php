<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($err)	{startcmd("exit ".$err); stopcmd("exit ".$err); return $err;}

/**********************************************************************/
function devname($uid)
{
	if ($uid=="BAND24G-1.1")	return "ath0";
	else if ($uid=="BAND24G-1.2") return "ath1";
	else if ($uid=="BAND5G-1.1") return "ath2";
	else if ($uid=="BAND5G-1.2") return "ath3";
	else if ($uid=="STATION24G-1.1") 	return "ath0";
	else if ($uid=="STATION5G-1.1") 	return "ath2";
	return "ath0";
}

/* what we check ?
1. if host is disabled, then our guest must also be disabled !!
*/
function host_guest_dependency_check($prefix)
{
	$host_uid=$prefix."-1.1";
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $host_uid, 0);
	if (query($p."/active")!=1) return 0;
	else 						return 1;
}

function isguestzone($uid)
{
	$postfix = cut($uid, 1,"-");
	$minor = cut($postfix, 1,".");
	if($minor=="2")	return 1;
	else			return 0;
}

function find_brdev($phyinf)
{
	foreach ("/runtime/phyinf")
	{
		if (query("type")!="eth") continue;
		foreach ("bridge/port") if ($VaLuE==$phyinf) {$find = "yes"; break;}
		if ($find=="yes") return query("name");
	}
	return "";
}

function a_channel_is_plus($ch)
{
	if($ch==36||$ch==44||$ch==52||$ch==60||$ch==100||$ch==108||$ch==116||$ch==124||$ch==132||$ch==149||$ch==157){
        return "1";
    }
    else if($ch==40||$ch==48||$ch==56||$ch==64||$ch==104||$ch==112||$ch==120||$ch==128||$ch==136||$ch==153||$ch==161){
    	return "0";
	}
	else if($ch==165||$ch==140){
		return "2";
	}
	else return "-1";
}

function get_vap_activate_file_path()
{
	$file_path = "/var/run/activateVAP.sh";
	return $file_path;
}

function wifi_AP($uid)
{
	$prefix = cut($uid, 0,"-");

	if($prefix=="BAND5G") 
	{
		$bandmode	= "5G";
		$is5G	= 1;
	}
	else
	{
		$bandmode   = "2G";
		$is5G   = 0;
	}

	$phy 	= XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	$wifi 	= XNODE_getpathbytarget("/wifi", "entry", "uid", query($phy."/wifi"), 0);
	$active = query($phy."/active");
	$dev = devname($uid);
		
	startcmd("# ".$uid.", dev=".$dev);
	PHYINF_setup($uid, "wifi", $dev);
	$brdev = find_brdev($uid);
			
	if(isguestzone($uid)=="1")
	{
		/* bring up guestzone bridge */
		startcmd("ip link set ".$brdev." up");

		/*Use the same configuration of hostzone to bring up guestzone*/
		if($is5G == 1)
		{
			$phy	= XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
		}
		else
		{
			$phy   = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
		}
	}
			
	anchor($phy."/media");
			
	$channel		= query("channel");				if ($channel=="")			{$channel="0";}
	$beaconinterval	= query("beacon");				if ($beaconinterval=="")	{$beaconinterval="100";}
	$bandwidth		= query("dot11n/bandwidth");
	if ($bandwidth=="20" )			{$bandwidth="0";}
	else 							{$bandwidth="1";}
	$ssidhidden		= query($wifi."/ssidhidden");	if ($ssidhidden!="1")		{$ssidhidden="0";}
	/* In order to support special character, we use the get() instead of query() to get SSID from xmldb. */
	//$ssid 		= query($wifi."/ssid");			if ($ssid=="")				{$ssid="wd";} 			
	$ssid 			= get("s", $wifi."/ssid");		if ($ssid=="")				{$ssid="wd";} 			
	$wmm			= query("wmm/enable");			if ($wmm!="1")				{$wmm="0";}
	$sgi			= query("dot11n/guardinterval");			
	if ($sgi=="400" )			{$sgi="1";}
	else						{$sgi="0";}
	$wlanmode		= query("wlmode");
	$puren			= "0";
	$pureg			= "0";
			
			
	if($is5G == 1)
	{
		//an, n only, a only
		if($wlanmode == "an")
		{
			$puren="0";
			$pureg="0";
			$chmode = "11NAHT20";
			if($bandwidth==1)	{$chmode = "11NAHT40";}
			else 				{$chmode = "11NAHT20";}
		}
		else if($wlanmode == "n")
		{
			$puren="1";
			$pureg="0";
			$chmode = "11NAHT20";
			if($bandwidth==1)	{$chmode = "11NAHT40";}
			else 				{$chmode = "11NAHT20";}
		}		
		else if($wlanmode == "a")
		{
			$puren="0";
			$pureg="0";
			$chmode = "11A";
		}
		else
		{
			$puren="0";
			$pureg="0";
			$chmode = "11NAHT20";
		}
				
		//for fixed channel, need to determine plus and minus 
		if($chmode == "11NAHT40" && $channel != "0")
		{
			$PLUS = a_channel_is_plus($channel); 
			if($PLUS=="-1") { TRACE_error("wrong A band channel: ".$channel); }
					
			if($PLUS=="1")  	{$chmode = "11NAHT40PLUS";}
			else if($PLUS=="0")	{$chmode = "11NAHT40MINUS";}
			else				{$chmode = "11NAHT20";}
		}
	}
	else
	{
		//1:11g only, 2.b/g mode 3:11b only, 4:only n  5:b/g/n mix, 6:g/n mix
		if($wlanmode == "g")
		{
			$puren="0";
			$pureg="1";
			$chmode = "11G";
		}
		else if($wlanmode == "bg")
		{
			$puren="0";
			$pureg="0";
			$chmode = "11G";
		}		
		else if($wlanmode == "n")
		{
			$puren="1";
			$pureg="0";
			if($bandwidth==1)	{$chmode = "11NGHT40";}
			else 				{$chmode = "11NGHT20";}
		}
		else if($wlanmode == "bgn")
		{
			$puren="0";
			$pureg="0";				
			if($bandwidth==1)	{$chmode = "11NGHT40";}
			else 				{$chmode = "11NGHT20";}
		}
		else if($wlanmode == "gn")
		{
			$puren="0";
			$pureg="1";				
			if($bandwidth==1)	{$chmode = "11NGHT40";}
			else 				{$chmode = "11NGHT20";}
		}
		else if($wlanmode == "b")
		{
			$puren="0";
			$pureg="0";
			$chmode = "11B";
		}
		else //for DEFAULT
		{
			$puren="0";
			$pureg="1";
			$chmode = "11NGHT20";
		}
				
		//for fixed channel, need to determine plus and minus 
		if($chmode == "11NGHT40" && $channel != "0")
		{
			if($channel<5)
			{
				//channel 1~4
				$chmode = "11NGHT40PLUS";
			}
			else if($channel<=11 && $channel>=5)
			{
				//channel 5~11
				$chmode = "11NGHT40MINUS";
			}
			else
			{ 
				//channel 12~14
				$chmod = "11NGHT20";
			}
		}
	}
				
	/* bring up the interface and bridge */
	//create the VAP athX
	$params = "";
	$params = $params."BANDMODE=".$bandmode.";";
	$params = $params."CH_MODE=".$chmode.";";
	$params = $params."PUREN=".$puren.";PUREG=".$pureg.";";
	//$params = $params."AP_CHANBW=".$bandwidth.";";
	//we don't know when to use iwpriv ath0 chanbw 1, let CH_MODE to be 11NGHT40 ??
	$params = $params."AP_HIDESSID=".$ssidhidden.";";
	$params = $params."AP_WMM=".$wmm.";";
	$params = $params."RF=RF;";
	$params = $params."PRI_CH=".$channel.";";
	$params = $params."BEACONINT=".$beaconinterval.";";
	$params = $params."ATH_NAME=".$dev.";";
	$params = $params."R_SHORTGI=".$sgi.";";
			
			
	$makeVAPcmd = "/etc/ath/makeVAP ap \"".$ssid."\" \"".$params."\"";
	TRACE_error("makeVAPcmd=".$makeVAPcmd);
			
	startcmd($makeVAPcmd);
	//activate the VAP athX (add to bridge, etc)
	$wifi_activateVAP = get_vap_activate_file_path();
	startcmd("echo /etc/ath/activateVAP ".$dev." ".$brdev." >> ".$wifi_activateVAP);
	startcmd("phpsh /etc/scripts/wifirnodes.php UID=".$uid);

	/* +++ upwifistats */
	startcmd("xmldbc -P /etc/services/WIFI/updatewifistats.php -V PHY_UID=".$uid." > /var/run/restart_upwifistats.sh;");
	startcmd("sh /var/run/restart_upwifistats.sh");
			
	if(isguestzone($uid)=="1")
	{
		startcmd("iwpriv ".$dev." w_partition 1");
	}

	/*Check if guestzone enable*/
	$active_24G_guest = query("/phyinf:5/active");
	$active_5G_guest = query("/phyinf:7/active");

	/* If guestzone is enabled, sleep more to prevent system crash on SMP(Ubicom) CPU.*/
	/* "ifconfig down" and "wlanconfig destroy" would enter the event_state machine in wifi driver several times.
	 * Input these two commands too frequently on SMP CPU system would disturb the sequence to enter the state machine.
	 * The wrong entering sequence would lead the crash. 
	 * To work around the issue, sleep more between each command. */

	stopcmd("phpsh /etc/scripts/delpathbytarget.php BASE=/runtime NODE=phyinf TARGET=uid VALUE=".$uid);
	stopcmd("ifconfig ".$dev." down");
	if($active_24G_guest==1 || $active_5G_guest==1)
	{
		stopcmd("sleep 10");
	}
	else
	{
		stopcmd("sleep 1");
	}
	stopcmd("wlanconfig ".$dev." destroy");
	if($active_24G_guest==1 || $active_5G_guest==1)
	{
		stopcmd("sleep 3");
	}
	else
	{
		stopcmd("sleep 1");
	}
	stopcmd("sleep 1");
	stopcmd("echo 1 > /var/run/".$uid.".DOWN");
	stopcmd("rm -f /var/run/".$uid.".UP");
	stopcmd("sh /etc/scripts/close_wlan_led.sh"); 
	
	if(isguestzone($uid)=="0") //hostzone
	{
		startcmd("phpsh /etc/scripts/wpsevents.php ACTION=ADD UID=".$uid);
		startcmd("event WLAN.CONNECTED");
		stopcmd("phpsh /etc/scripts/wpsevents.php ACTION=FLUSH UID=".$uid);
		stopcmd("event WPS.NONE\n");
	}
		
	set("/runtime/hostapd/enable","1");
}

function wifi_STA($is5G)
{
	if($is5G == 1) 
	{	
		$uid = "STATION5G-1.1";
		$bandmode 	= "5G";
	}
	else
	{
		$uid = "STATION24G-1.1";
		$bandmode 	= "2G";
	}
	
	$p 		= XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	if ($p != "")
	{
		$active = query($p."/active");
		$wifi 	= XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
		$dev = devname($uid);
		if ($active!=1)
		{
			startcmd("# ".$uid." is inactive!");
			return;
		}
		else
		{
			startcmd("# ".$uid.", dev=".$dev);
			PHYINF_setup($uid, "wifi", $dev);
			$brdev = find_brdev($uid);
			if ($bandmode == "2G")
			{
				$makeVAPcmd = "/etc/ath/makeVAP sta \"My Net N600 sta\" \"BANDMODE=".$bandmode.";CH_MODE=11NGHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=".$dev.";\"";
			}
			else if ($bandmode == "5G")
			{
				$makeVAPcmd = "/etc/ath/makeVAP sta \"My Net N600 sta\" \"BANDMODE=".$bandmode.";CH_MODE=11NAHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=".$dev.";\"";
			}
			TRACE_error("makeVAPcmd=".$makeVAPcmd);
			
			startcmd($makeVAPcmd);
			//activate the VAP athX (add to bridge, etc)
			$wifi_activateVAP = get_vap_activate_file_path();
			startcmd("echo /etc/ath/activateVAP ".$dev." ".$brdev." >> ".$wifi_activateVAP);
			startcmd("phpsh /etc/scripts/wifirnodes.php UID=".$uid);

			stopcmd("phpsh /etc/scripts/delpathbytarget.php BASE=/runtime NODE=phyinf TARGET=uid VALUE=".$uid);
			stopcmd("ifconfig ".$dev." down");
			stopcmd("wlanconfig ".$dev." destroy");
			stopcmd("echo 1 > /var/run/".$uid.".DOWN");
			stopcmd("rm -f /var/run/".$uid.".UP");
			stopcmd("sh /etc/scripts/close_wlan_led.sh"); 
		}
	}
	
	startcmd("phpsh /etc/scripts/wpsevents.php ACTION=ADD UID=".$uid);
	startcmd("event WLAN.CONNECTED");

	stopcmd("phpsh /etc/scripts/wpsevents.php ACTION=FLUSH UID=".$uid);
	stopcmd('xmldbc -t \"close_WPS_led:3:event WPS.NONE\"\n');
	set("/runtime/wpa_supplicant/enable","1");
}

function wificonfig($uid)
{
	fwrite(w,$_GLOBALS["START"], "#!/bin/sh\n");
	fwrite(w,$_GLOBALS["STOP"],  "#!/bin/sh\n");
	
	$p 		= XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	$wifi 	= XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);

	$dev	= devname($uid);
	$prefix = cut($uid, 0,"-");
	
	if(query($wifi."/opmode")=="AP")
	{
		$is_APmode = 1;
	}
	else if(query($wifi."/opmode")=="STA")
	{
		$is_APmode = 0;
		if($prefix=="STATION5G") {$is_5G = 1;}
		else				  	 {$is_5G = 0;}
	}
	
	if ($p=="")					return error(9);
	if (query($p."/active")!=1) 
	{
		startcmd("# ".$uid." is inactive!");
		return error(8);
	}
	if(host_guest_dependency_check($prefix)==0)
	{
		startcmd("# The hostzone (".$uid.") is inactive. \nStop to continue the guestzone !");
		return error(8);
	}

	$layout=query("/device/layout");
	if(isguestzone($uid)=="1" && $layout=="bridge")
	{
		startcmd("# In bridge,we don't support guest zone.");
		$runtime_p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
		if($runtime_p!="")
		{
			set($runtime_p."/valid","0"); 				
		}
		return error(0);
	}
	
	startcmd("rm -f /var/run/".$uid.".DOWN");
	startcmd("echo 1 > /var/run/".$uid.".UP");
	if($is_APmode == 1) {wifi_AP($uid);}
	else                {wifi_STA($is_5G);}


	/* for closing guestzone bridge */
	/* do this only at both guestzone interfaces are down.*/
	if ($is_APmode == 1)
	{
		if($uid == "BAND5G-1.2")
		{
			$active_24G_guest = query("/phyinf:5/active");
			if($active_24G_guest == "0")
			{
				//get brname of guestzone
				$brname = find_brdev($uid);
				stopcmd("ip link set ".$brname." down");
			}
		}

		if($uid == "BAND24G-1.2")
		{
			$brname = find_brdev($uid);
			stopcmd("ip link set ".$brname." down");
		}
	}
	return error(0);
}

?>
