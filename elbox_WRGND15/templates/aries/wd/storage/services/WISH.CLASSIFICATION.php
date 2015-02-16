<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$bwc_profile_name = "BWC-2";
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$HTTP_CLASSIFICATION_ENABLED = query($bwcp."/wishhttp");
$AUTO_CLASSIFICATION_ENABLED = query($bwcp."/wishauto");
$WIFI_IF_NAME = "ath";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

/* Start WISH if required */
startcmd(
	/* Do we need to enable dynamic classification? */
	/*
	'if [ '.$AUTO_CLASSIFICATION_ENABLED.' -eq 1 ] ; then\n'.
	'	echo 1 > /sys/devices/system/ubicom_wish/ubicom_wish0/auto_classifier\n'.
	'else\n'.
	'	echo 0 > /sys/devices/system/ubicom_wish/ubicom_wish0/auto_classifier\n'.
	'fi\n'.
	 */
	/* Do we need to enable http classification? */
	'if [ '.$HTTP_CLASSIFICATION_ENABLED.' -eq 1 ] ; then\n'.
	'	echo 1 > /sys/devices/system/ubicom_wish/ubicom_wish0/http_classifier\n'.
	'else\n'.
	'	echo 0 > /sys/devices/system/ubicom_wish/ubicom_wish0/http_classifier\n'.
	'fi\n'.
	'/usr/sbin/wish_tool write2file /sys/devices/system/ubicom_wish/ubicom_wish0/if_name '.$WIFI_IF_NAME.'\n'.
);

error($ret);
?>
