#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function update_state($state)
{
	//since AP, set to both BAND24G-1.1 and BAN5G-1.1
	$uid = "BAND24G-1.1";
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
	if ($p != "") set($p."/media/wps/enrollee/state", $state);
	$uid = "BAND5G-1.1";
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
	if ($p != "") set($p."/media/wps/enrollee/state", $state);
}

function kill_wpatalk($uid)
{
	$pidfile = "/var/run/wpatalk.".$uid.".pid";
	$pid = fread("s", $pidfile);
	if ($pid != "")
	{
		echo "kill ".$pid."\n";
		echo "rm ".$pidfile."\n";
	}
}

function do_wps_sta($uid, $method)
{
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
	if ($p == "") return;
	$dev = query($p."/name");
	if		($method == "pbc") { $wsc_mode = "2"; }
	else if ($method == "pin") { TRACE_error("do_wps_sta : for bridge mode, we only support PBC for now..!!\n"); return; }
	else return;

	//init 
	echo "iwpriv ".$dev." wps 1 \n";			//in case if forgotten
	
	kill_wpatalk($uid);
	$pidfile = "/var/run/wpatalk.".$uid.".pid";
	echo "wpatalk ".$dev." configme &\n";
	echo "echo $! > ".$pidfile."\n";
}

function do_wps_ap($uid, $method)
{
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	if ($p == "") return;
	if (query($p."/active")!="1") return;
	$p = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
	if ($p == "") return;
	$enable = query($p."/wps/enable");
	if ($enable!="1") return;

	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $uid, 0);
	if ($p == "") return;
	
	update_state("WPS_IN_PROGRESS");
	event("WPS.INPROGRESS");
	
	$dev = query($p."/name");
	$pin = query($p."/media/wps/enrollee/pin");

	if		($method == "pbc") $cmd = "configthem";
	else if ($method == "pin") $cmd = "\"configthem pin=".$pin."\"";
	else return;

	kill_wpatalk($uid);
	$pidfile = "/var/run/wpatalk.".$uid.".pid";
	echo "wpatalk ".$dev." ".$cmd." &\n";
	echo "echo $! > ".$pidfile."\n";
}

function do_wps($uid, $method)
{
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	if ($p == "") return;
	if (query($p."/active")!="1") return;
	
	$p = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
	if ($p == "") return;
	$enable = query($p."/wps/enable");
	
	if ($enable!="1") return;
	$opmode = query($p."/opmode");
	
	if($opmode == "STA")	{ TRACE_error("do_wps station"); do_wps_sta($uid, $method); 	}
	else									{ TRACE_error("do_wps ap"); 		 do_wps_ap($uid, $method); 	}
}

function set_wps($uid)
{
	TRACE_debug("SETWPS(".$uid."):\n");
	
	/* Validating the interface. */
	if ($uid=="")	{TRACE_debug("SETWPS: error - no UID!\n"); return;}
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	if ($p=="")		{TRACE_debug("SETWPS: error - no PHYINF!\n"); return;}
	//set($p."/active", 1); /* added by alex shi , we should enable wifi interface when we successfully get the wps profile. */
	$wifi = query($p."/wifi");
	if ($wifi=="")	{TRACE_debug("SETWPS: error - no wifi!\n"); return;}
	$p = XNODE_getpathbytarget("/wifi", "entry", "uid", $wifi, 0);
	if ($p=="")		{TRACE_debug("SETWPS: error - no wifi profile!\n"); return;}
	
	/* The WPS result. */
	anchor("/runtime/wps/setting");
	$scfg	= query("selfconfig");	TRACE_debug("selfconf	= ".$scfg);
	$ssid	= query("ssid");		TRACE_debug("ssid		= ".$ssid);
	$atype	= query("authtype");	TRACE_debug("authtype	= ".$atype);
	$etype	= query("encrtype");	TRACE_debug("encrtype	= ".$etype);
	$defkey	= query("defkey");		TRACE_debug("defkey		= ".$defkey);
	$maddr	= query("macaddr");		TRACE_debug("macaddr	= ".$maddr);
	$newpwd	= query("newpassword");	TRACE_debug("newpwd		= ".$newpwd);
	$devpid	= query("devpwdid");	TRACE_debug("devpwdid	= ".$devpid);
	
	/* If we started from Unconfigured AP (self configured),
	 * change the setting to auto. */
	if		($scfg == 1)	{ $atype = 7; $etype = 4; /* WPA/WPA2 PSK & TKIP+AES */ }
	
	if		($atype == 0)	$atype = "OPEN";
	else if ($atype == 1)	$atype = "SHARED";
	else if ($atype == 2)	$atype = "WPA";
	else if ($atype == 3)	$atype = "WPAPSK";
	else if ($atype == 4)	$atype = "WPA2";
	else if ($atype == 5)	$atype = "WPA2PSK";
	else if ($atype == 6)	$atype = "WPA+2";
	else if ($atype == 7)	$atype = "WPA+2PSK";
	
	if		($etype == 0)	$etype = "NONE";
	else if ($etype == 1)	$etype = "WEP";
	else if ($etype == 2)	$etype = "TKIP";
	else if ($etype == 3)	$etype = "AES";
	else if ($etype == 4)	$etype = "TKIP+AES";
	
	set($p."/ssid",		$ssid);
	set($p."/authtype",	$atype);
	set($p."/encrtype",	$etype);

	if ($etype=="WEP")
	{
		foreach ("key")
		{
			TRACE_debug("key[".$InDeX."]");
			$idx = query("index");	TRACE_debug("key index	= ".$idx);
			$key = query("key");	TRACE_debug("key		= ".$key);
			$fmt = query("format");	TRACE_debug("format		= ".$fmt);
			$len = query("len");	TRACE_debug("len		= ".$len);
			
			if ($idx<5 && $idx>0) set($p."/nwkey/wep/key:".$idx, $key);
		}
		
		if ($fmt!=1) $fmt=0;
		set($p."/nwkey/wep/defkey",	$idx);
		set($p."/nwkey/wep/ascii",	$fmt);
		/*
		 *	Ascii	 64 bits ->  5 bytes
		 *			128 bits -> 13 bytes
		 *	Hex		 64 bits -> 10 bytes
		 *			128 bits -> 26 bytes
		 *
		 * size should be filled with "64" and "128", so we derive it from above.
		 */
		if		($len==5  || $len==10)	set($p."/nwkey/wep/size", "64");
		else if ($len==13 || $len==26)	set($p."/nwkey/wep/size", "128");
		else set($p."/nwkey/wep/size", "64"); //just for default
	}
	else
	{
		/* The 2st key only. */
		$idx = query("key:1/index");	TRACE_debug("key index	= ".$idx);
		$key = query("key:1/key");		TRACE_debug("key		= ".$key);
		$fmt = query("key:1/format");	TRACE_debug("format		= ".$fmt);
		$len = query("key:1/len");		TRACE_debug("len		= ".$len);
		if ($fmt!=1) $fmt=0;
		set($p."/nwkey/psk/passphrase", $fmt);
		set($p."/nwkey/psk/key", $key);
	}
	
	set($p."/wps/configured", "1");
}

/************************************************************************/
if($MODE == "station")
{
	if($PARAM1=="pbc")
	{
		TRACE_debug("WPS Station :".$PARAM1);
		$UID = $PARAM2;	
		do_wps($UID, $PARAM1);
	}
	else if ($PARAM1=="restartap")	//for easy setup. After sta get profile, set it to Ap profile. 
	{
		foreach("/runtime/phyinf")
		{
			if (query("type")=="wifi")
			{
				$UID = query("uid");	
				if(strstr($UID, "5G")!="")	//if uid==STATION5G-1.1 then set BAND5G-1.1
				{
					set_wps("BAND5G-1.1");
					/* added by alex shi , we should enable wifi interface when WPS is done. */
					$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0); 
					if ($p!="")
					{
						set($p."/active", 1);
						$p2 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
						if ($p2!="")
						{
							set($p2."/wps/configured", "1");
						}
					}
				}
				else 
				{
					set_wps("BAND24G-1.1");		//if uid==STATION24G-1.1 then set BAND24G-1.1 
					/* added by alex shi , we should enable wifi interface when WPS is done. */
					$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
					if ($p!="")
					{
						set($p."/active", 1);
						$p2 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
						if ($p2!="")
						{
							set($p2."/wps/configured", "1");
						}
					}
				}
			}
		}
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "STATION24G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 0);
		}
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "STATION5G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 0);
		}
		$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
		if ($p!="")
		{
			del($p."/bridge");
    		add($p."/bridge/port",	"BAND24G-1.1");
			add($p."/bridge/port",	"BAND5G-1.1");
			add($p."/bridge/port",  "BAND24G-1.2");
			add($p."/bridge/port",  "BAND5G-1.2");
		}
		
		event("DBSAVE");
		echo 'xmldbc -s /ezcfg/wps/status \"COMPLETED\"\n'; /* added by alex shi for WD ezsetup tools */
		echo 'xmldbc -t "WPS:2:service PHYINF.WIFI restart"\n';
	}
	else if ($PARAM1=="reactive")
	{
		TRACE_debug("WPS Station : reactive all wifi interface after WPS is timeout.");
		/*added by alex shi, we have to reactive all wifi interface after WPS is timeout */
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 1);
		}
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 1);
		}
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "STATION24G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 0);
		}
		$p = XNODE_getpathbytarget("", "phyinf", "uid", "STATION5G-1.1", 0);
		if ($p!="")
		{
			set($p."/active", 0);
		}
		$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
		if ($p!="")
		{
			del($p."/bridge");
    		add($p."/bridge/port",	"BAND24G-1.1");
			add($p."/bridge/port",	"BAND5G-1.1");
			add($p."/bridge/port",  "BAND24G-1.2");
			add($p."/bridge/port",  "BAND5G-1.2");
		}
					
		event("DBSAVE");
		echo 'xmldbc -t "WPS:2:service PHYINF.WIFI restart"\n';
	}
	else
	{
		$err = "usage: wps.sh [pbc] [station_uid]";
		echo 'echo "'.$err.'" > /dev/console\n';
	}
}
else //$MODE == "ap"
{
	if ($PARAM1=="pin" || $PARAM1=="pbc")
	{
		TRACE_debug("WPS AP :".$PARAM1);
		if($PARAM2 != "") { $UID = $PARAM2; }
		else 							{ $UID = "BAND24G-1.1"; TRACE_error("wps ap: uid is empty. use default BAND24G-1.1"); }
		do_wps($UID, $PARAM1);
	}
	else if ($PARAM1=="restartap")
	{
		TRACE_debug("WPS AP :".$PARAM1);
		set_wps("BAND24G-1.1");
		set_wps("BAND5G-1.1");
		event("DBSAVE");
		echo 'xmldbc -t "WPS:1:service PHYINF.WIFI restart"\n';
	}
	else if ($PARAM1=="WPS_NONE")				{update_state("WPS_NONE");				event("WPS.NONE");}
	else if ($PARAM1=="WPS_IN_PROGRESS"){update_state("WPS_IN_PROGRESS");	event("WPS.INPROGRESS");}
	else if ($PARAM1=="WPS_ERROR")			{update_state("WPS_ERROR");				event("WPS.ERROR");}
	else if ($PARAM1=="WPS_OVERLAP")		{update_state("WPS_OVERLAP");			event("WPS.OVERLAP");}
else if ($PARAM1=="WPS_SUCCESS")
{
		update_state("WPS_SUCCESS");
	event("WPS.SUCCESS");
		kill_wpatalk("BAND24G-1.1");
		kill_wpatalk("BAND5G-1.1");
}
else
{
	$err = "usage: wps.sh [pin|pbc|WPS_NONE|WPS_IN_PROGRESS|WPS_ERROR|WPS_OVERLAP|WPS_SUCCESS]";
	echo 'echo "'.$err.'" > /dev/console\n';
}
}

?>
