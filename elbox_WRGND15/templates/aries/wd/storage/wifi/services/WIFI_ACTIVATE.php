<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/etc/services/PHYINF/phywifi.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)	{startcmd("exit ".$errno); stopcmd("exit ".$errno);}

fwrite(w,$_GLOBALS["START"], "#!/bin/sh\n");
fwrite(w,$_GLOBALS["STOP"],  "#!/bin/sh\n");

$wifi_activateVAP = get_vap_activate_file_path();
if(isfile($wifi_activateVAP) == 1) {startcmd("sh ".$wifi_activateVAP);}

/*
Win7 logo patch. Hostapd must be restarted ONLY once..!
*/
if(query("/runtime/hostapd/enable")=="1")
{
	/* define WFA related info for hostapd */
	$dtype	= "urn:schemas-wifialliance-org:device:WFADevice:1";
	setattr("/runtime/hostapd/mac",  "get", "devdata get -e lanmac");
	setattr("/runtime/hostapd/guid", "get", "genuuid -s \"".$dtype."\" -m \"".query("/runtime/hostapd/mac")."\"");
	if(query("/runtime/hostapd_restartap")=="1")
	{
		startcmd('xmldbc -k "HOSTAPD_RESTARTAP"');
		startcmd('xmldbc -t "HOSTAPD_RESTARTAP:5:sh /etc/scripts/restartap_hostapd.sh"');
	}
	else
	{
		startcmd("xmldbc -P /etc/services/WIFI/hostapdcfg.php > /var/topology.conf");
		startcmd("/etc/scripts/hostapd_loop.sh &");
	}
	stopcmd("ps | grep hostapd_loop.sh | awk '{print $1}' | xargs kill -SIGTERM\n");
	stopcmd("killall hostapd > /dev/null 2>&1; sleep 1");
	set("/runtime/hostapd/enable","0");
}

if(query("/runtime/wpa_supplicant/enable")=="1")
{
	startcmd("xmldbc -P /etc/services/WIFI/wpa_supplicant.php > /var/topology_sta.conf");
	startcmd("wpa_supplicant /var/topology_sta.conf &");
	stopcmd("killall wpa_supplicant");
	set("/runtime/wpa_supplicant/enable","0");
}

startcmd("sleep 1");
startcmd("service MULTICAST restart");

stopcmd("service MULTICAST restart");
if(isfile($wifi_activateVAP) == 1) {stopcmd("rm -f ".$wifi_activateVAP);}

error(0);
?>
