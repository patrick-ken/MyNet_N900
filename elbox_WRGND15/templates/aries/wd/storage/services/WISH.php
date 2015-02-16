<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$bwc_profile_name = "BWC-2";
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$WISH_ENABLED = query($bwcp."/enable");
$WMM_24_ENABLED = query("/phyinf:4/media/wmm/enable");
$WIFI_24_ENABLED = query("/phyinf:4/active");
$WMM_5_ENABLED = query("/phyinf:6/media/wmm/enable");
$WIFI_5_ENABLED = query("/phyinf:6/active");


fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");


/* Start WISH if required */
startcmd(
	'if [ '.$WMM_24_ENABLED.' -ne 1 -o '.$WIFI_24_ENABLED.' -ne 1 ]; then\n'.
	'	if [ '.$WMM_5_ENABLED.' -ne 1 -o '.$WIFI_5_ENABLED.' -ne 1 ]; then\n'.		
	'		echo "WISH: WiFI 2.4G is disabled or WMM 2.4G is disabled ..." > /dev/console\n'.
	'		echo "WISH: WiFI 5G is disabled or WMM 5G is disabled ..." > /dev/console\n'.
	'		exit 0\n'.
	'	fi\n'.
	'fi\n'.
	'if [ '.$WISH_ENABLED.' -ne 1 ]; then\n'.
	'	echo "WISH: WISH disabled ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	'service WISH.MODULE start\n'.
	'service WISH.CLASSIFICATION start\n'.
	'service WISH.USER.RULES start\n'.
	'service WISH.MOUNT.STATE start\n'.
	'service WISH.STATE.GET start\n'.
);

stopcmd(
	/* Is WISH currently running?  If so then stop it. */
	'if [ '.$WMM_24_ENABLED.' -ne 1 -o '.$WIFI_24_ENABLED.' -ne 1 ]; then\n'.
	'	if [ '.$WMM_5_ENABLED.' -ne 1 -o '.$WIFI_5_ENABLED.' -ne 1 ]; then\n'.		
	'		echo "WISH: WiFI 2.4G is disabled or WMM 2.4G is disabled ..." > /dev/console\n'.
	'		echo "WISH: WiFI 5G is disabled or WMM 5G is disabled ..." > /dev/console\n'.
	'		exit 0\n'.
	'	fi\n'.
	'fi\n'.
	'if [ '.$WISH_ENABLED.' -ne 1 ]; then\n'.
	'	echo "WISH: WISH disabled ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	'service WISH.MODULE stop\n'.
	'service WISH.CLASSIFICATION stop\n'.
	'service WISH.USER.RULES stop\n'.
	'service WISH.MOUNT.STATE stop\n'.
	'service WISH.STATE.GET stop\n'.
);

error($ret);
?>
