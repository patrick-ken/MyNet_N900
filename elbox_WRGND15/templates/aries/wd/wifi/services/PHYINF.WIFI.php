<?
include "/htdocs/phplib/xnode.php";
include "/etc/services/PHYINF/phywifi.php";

function schcmd($uid)
{
	/* Get schedule setting */
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
	$sch = XNODE_getschedule($p);
	if ($sch=="") $cmd = "start";
	else
	{
		$days = XNODE_getscheduledays($sch);
		$start = query($sch."/start");
		$end = query($sch."/end");
		if (query($sch."/exclude")=="1") $cmd = 'schedule!';
		else $cmd = 'schedule';
		$cmd = $cmd.' "'.$days.'" "'.$start.'" "'.$end.'"';
	}
	return $cmd;
}

/********************************************************************/
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");


fwrite("a",$START,"service WIFI_MODS start\n");
$wifi_activateVAP = get_vap_activate_file_path();
if(isfile($wifi_activateVAP) == 1) { unlink($wifi_activateVAP); }
fwrite("a",$START,"service PHYINF.BAND24G-1.1 ".schcmd("BAND24G-1.1")."\n");
fwrite("a",$START,"service PHYINF.BAND24G-1.2 ".schcmd("BAND24G-1.2")."\n");
fwrite("a",$START,"service PHYINF.BAND5G-1.1 ".schcmd("BAND5G-1.1")."\n");
fwrite("a",$START,"service PHYINF.BAND5G-1.2 ".schcmd("BAND5G-1.2")."\n");
//fwrite("a",$START,"service PHYINF.STATION24G-1.1 ".schcmd("STATION24G-1.1")."\n");
//fwrite("a",$START,"service PHYINF.STATION5G-1.1 ".schcmd("STATION5G-1.1")."\n");
fwrite("a",$START,"service WIFI_ACTIVATE start\n");

fwrite("a",$STOP,"service WIFI_ACTIVATE stop\n");
//fwrite("a",$STOP,"service PHYINF.STATION5G-1.1 stop\n");
//fwrite("a",$STOP,"service PHYINF.STATION24G-1.1 stop\n");
fwrite("a",$STOP,"service PHYINF.BAND5G-1.2 stop\n");
fwrite("a",$STOP,"service PHYINF.BAND5G-1.1 stop\n");
fwrite("a",$STOP,"service PHYINF.BAND24G-1.2 stop\n");
fwrite("a",$STOP,"service PHYINF.BAND24G-1.1 stop\n");
fwrite("a",$STOP,"service WIFI_MODS stop\n");

fwrite("a",$START,	"exit 0\n");
fwrite("a",$STOP,	"exit 0\n");
?>
