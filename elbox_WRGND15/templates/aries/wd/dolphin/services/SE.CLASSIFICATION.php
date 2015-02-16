<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: se_restart */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}


$name = "WAN-1";
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
$phy = query($stsp."/phyinf");
$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy, 0);
$WAN_MODE_INTERFACE = query($phyp."/name");

$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$bwc_profile_name = query($infp."/bwc");
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$AUTO_CLASSIFICATION_ENABLED = query($bwcp."/autoclassification");
$BITTORRENT_CLASSIFICATION_ENABLED = query($bwcp."/autoclassification");

$inet   = query($infp."/inet");
$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$addrtype = query($inetp."/addrtype");
$static = query($inetp."/ipv4/static");

if ($addrtype == "ppp4")
{
	$ppp4over =  query($inetp."/ppp4/over");
	if ($ppp4over == "eth")
	{		
		$WANCONNECTIONTYPE = "pppoe";
	}
	else if ($ppp4over == "pptp")	
	{
		$WANCONNECTIONTYPE = "pptp";	
	}
	else if ($ppp4over == "l2tp")	
	{
		$WANCONNECTIONTYPE = "l2tp";	
	}	
}
else if ($addrtype == "ipv4")
{
	if ($static == "1") { $WANCONNECTIONTYPE = "static"; }
	else                { $WANCONNECTIONTYPE = "dhcp"; }
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

startcmd(
	/* Set WAN interface, default eth0.1
	 * Update interface info. Following source required, do not remove.
	 */
	'SE_SYSFS_WAN_IF=/sys/devices/system/streamengine_classifier/streamengine_classifier0/streamengine_classifier_if_name\n'.
	//'if [ ! -w ${SE_SYSFS_WAN_IF} ]; then\n'.
	//'	sleep 1\n'.
	//'fi\n'.
	/* Update interface info. Following source required, do not remove. */
	'if [ -e ${SE_SYSFS_WAN_IF} ]; then\n'.
	'	echo "Set WAN Interface to '.$WAN_MODE_INTERFACE.'" > /dev/console\n'.
	'	echo -n '.$WAN_MODE_INTERFACE.' > ${SE_SYSFS_WAN_IF}\n'.
	'else\n'.
	'	echo "NO WAN Interface exists." > /dev/console\n'.
	'	exit 1\n'.
	'fi\n'.
	/* Tell the classifier the loaded state of the shaper */
	'STREAMENGINE_SHAPER_LOADED=`xmldbc -g /runtime/bwc/shaperload`\n'.
	'echo $STREAMENGINE_SHAPER_LOADED > /sys/devices/system/streamengine_classifier/streamengine_classifier0/shaper_loaded\n'.
	/* Inform the classifier if we have a PPP wan link */
	'case '.$WANCONNECTIONTYPE.' in\n'.
	'	pppoe )\n'.
	'		echo "WAN type: PPPoE" > /dev/console\n'.
	'		echo 1 > /sys/devices/system/streamengine_classifier/streamengine_classifier0/ppp_transport\n'.
	'		;;\n'.
    '   pptp )\n'.
    '       echo "WAN type: PPTP" > /dev/console\n'.
    '       echo 1 > /sys/devices/system/streamengine_classifier/streamengine_classifier0/ppp_transport\n'.
    '       ;;\n'.
    '   l2tp )\n'.
    '       echo "WAN type: L2TP" > /dev/console\n'.
    '       echo 1 > /sys/devices/system/streamengine_classifier/streamengine_classifier0/ppp_transport\n'.
    '       ;;\n'.
	'	* )\n'.
	'		echo "WAN type: '.$WANCONNECTIONTYPE.'" > /dev/console\n'.
	'		;;\n'.
	'esac\n'.
	/* Enable or disable auto classification */
	'echo '.$AUTO_CLASSIFICATION_ENABLED.' > /sys/devices/system/streamengine_classifier_default/streamengine_classifier_default0/enabled\n'.
	/* Enable or disable bit torrent classification */
	'echo '.$BITTORRENT_CLASSIFICATION_ENABLED.' > /sys/devices/system/streamengine_classifier_bittorrent/streamengine_classifier_bittorrent0/enabled\n'.
	/* Set the default 16000 for BT test */
	'echo 16000 > /sys/devices/system/streamengine_db/streamengine_db0/connections_max\n'.
	/* Set the default 9437184 for Ixia test */
	'echo 9437184 > /sys/devices/system/streamengine_db/streamengine_db0/tracked_data_limit\n'.
);

error($ret);
?>
