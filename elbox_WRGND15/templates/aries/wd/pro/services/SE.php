<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$Q_COUNT = 256;
$name = "WAN-1";
$infp   = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$phyinf = query($infp."/phyinf");
$devnam = PHYINF_getifname($phyinf);

/* Mapping to SDK: se_load */
function se_load($WANINTERFACE, $Q_COUNT)
{
	/* Default to 256 qos queues unless a different amount is given */
	startcmd(
		/* STREAMENGINE_SHAPER_LOADED=0 */
		'xmldbc -s /runtime/bwc/shaperload 0\n'.
		'echo "Starting StreamEngine Schduler on '.$WANINTERFACE.' with '.$Q_COUNT.' queues" > /dev/console\n'.
		'insmod /lib/modules/sch_ubicom_streamengine.ko 2> /dev/null\n'.
		'tc qdisc add dev '.$WANINTERFACE.' handle 1:0 root streamengine bands '.$Q_COUNT.'\n'.
		'tc qdisc show dev '.$WANINTERFACE.'\n'.
		/* STREAMENGINE_SHAPER_LOADED=1 */
		'xmldbc -s /runtime/bwc/shaperload 1\n'
		);
}

/* Mapping to SDK: se_unload */
function se_unload($WANINTERFACE)
{
	stopcmd(
		/* Is StreamEngine shaper currently running?  If so then stop it. */
		'if [ -e /sys/devices/system/ubicom_streamengine/ubicom_streamengine0 ]; then\n'.
		'	echo "Stopping StreamEngine shaper ..."\n'.
		'	echo "Terminating rate estimation ..."\n'.
		'	echo 1 > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_terminate\n'.
		'	echo "Stopping StreamEngine Schduler on '.$WANINTERFACE.' ..." > /dev/console\n'.
		'	tc qdisc del dev '.$WANINTERFACE.' root\n'.
		'	tc qdisc show dev '.$WANINTERFACE.'\n'.
		/* Wait for the module to become idle */
		'	REFS=$(cat /proc/modules | grep -w ^sch_ubicom_streamengine | cut -s -d \' \' -f3)\n'.
		'	if [ -z $REFS ]; then\n'.
		'	REFS=0\n'.
		'	fi\n'.
		'	until [ $REFS -eq 0 ]; do\n'.
		'		if [ -f \"/var/stop_SE_unload\" ]; then'.
		'			break\n'.	
		'		fi\n'.	
		'		echo -n .\n'.
		'		sleep 1\n'.
		'		REFS=$(cat /proc/modules | grep -w ^sch_ubicom_streamengine | cut -s -d \' \' -f3)\n'.
		'		if [ -z $REFS ]; then\n'.
		'			REFS=0\n'.
		'		fi\n'.
		'	done\n'.
		'	rmmod /lib/modules/sch_ubicom_streamengine.ko\n'.
		'fi\n'.
		);
	return 0;
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$ret = se_load($devnam, $Q_COUNT);
$ret = se_unload($devnam, $Q_COUNT);

error($ret);
?>
