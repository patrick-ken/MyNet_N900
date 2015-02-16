<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

echo "#!/bin/sh\n";

$uid1 = $UID;
if($uid1=="")
{
	TRACE_error("wpsevents can't get UID. Need UID argument !!");
	TRACE_error("wpsevents can't get UID. Need UID argument !!");
}

$p1 = XNODE_getpathbytarget("", "phyinf", "uid", $uid1, 0);

if ($p1=="") echo "exit 0\n";

$wifi1 = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p1."/wifi"),0);

$wps = 0;
if (query($p1."/active")==1 && query($wifi1."/wps/enable")==1) $wps++;

if ($ACTION == "ADD")
{
	/* Someone uses wps, so add the events for WPS. */
	if ($wps > 0)
	{
		echo 'event WPSPIN insert "'.$uid1.':/etc/scripts/wps.sh pin '.$uid1.'"\n';
		if ($uid1 == "STATION24G-1.1" || $uid1 == "STATION5G-1.1")
		{
			echo 'event WPSPBC.PUSH insert "'.$uid1.':/etc/scripts/wps_sta.sh pbc '.$uid1.'"\n';
		}
		else
		{
			echo 'event WPSPBC.PUSH insert "'.$uid1.':/etc/scripts/wps.sh pbc '.$uid1.'"\n';
		}
		//hendry, WPS led must light if enabled (WD spec)
		echo 'event WPS.SUCCESS\n';
	}
}

else if ($ACTION == "FLUSH")
{
	/* ONLY clear the UID */
	echo "event WPSPIN remove ".$uid1."\n";
	echo "event WPSPBC.PUSH remove ".$uid1."\n";
	
	/* IF No body uses wps, so we can flush it. */
	if ($wps == 0)
	{
		echo "event WPSPIN flush \n";
		echo "event WPSPBC.PUSH flush \n";
	}
}

echo "exit 0\n";

?>
