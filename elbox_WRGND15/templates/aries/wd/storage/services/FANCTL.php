<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}

$proc_fanctl	= "/proc/fanctl";
$fanctl_start	= "/var/servd/FANCTL_start.sh";
$fanctl_debug_path	= "/runtime/fanctl/debug";
$fanctl_timer	= query("/runtime/fanctl/timer");
$fanctl_max	= query("/runtime/fanctl/max");
$fanctl_mid	= query("/runtime/fanctl/mid");
$fanctl_min	= query("/runtime/fanctl/min");
$fanctl_debug	= query("/runtime/fanctl/debug");

if ($fanctl_timer == 0 || $fanctl_timer == "") { $fanctl_timer = 10; }
if ($fanctl_max == 0 || $fanctl_max == "") { $fanctl_max = 60; }
if ($fanctl_mid == 0 || $fanctl_mid == "") { $fanctl_mid = 50; }
if ($fanctl_min == 0 || $fanctl_min == "") { $fanctl_min = 40; }
if ($fanctl_debug == 0 || $fanctl_debug == "") { set($fanctl_debug_path, "0"); }

/*fwrite(a, $START, "LIST=`cd /sys/block; ls -d sd*`\n");
fwrite(a, $START, "for DEV in $LIST; do\n");
fwrite(a, $START, "	ls -ld /sys/block/${DEV}/device | grep -q SATA\n");
fwrite(a, $START, "	if [ $? == 0 ]; then\n");
fwrite(a, $START, "		FOUND=1\n");
fwrite(a, $START, "		break;\n");
fwrite(a, $START, "	fi\n");
fwrite(a, $START, "done\n");
fwrite(a, $START, "if [ $FOUND == 0 ]; then\n");
fwrite(a, $START, "	exit 0;\n");
fwrite(a, $START, "fi\n");
 */
fwrite("w", $START, "#!/bin/sh\n");
startcmd(
	'SIZE=`cat /sys/block/sda/device/block/sda/size`\n'.
	'if [ $SIZE == 0 ]; then\n'.
	'	echo "FANCTL: No hard disk" > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	'if [ ! -f '.$proc_fanctl.' ]; then\n'.
	'	echo "FANCTL: No proc entry '.$proc_fanctl.'" > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	'LIST=`smartctl -d sat -A /dev/sda | grep Temperature_Celsius`\n'.
	'CNT=0;\n'.
	'for X in $LIST; do \n'.
	'	CNT=`expr $CNT + 1`;\n'.
	'	if [ $CNT == 10 ]; then\n'.
	'		break\n'.
	'	fi\n'.
	'done\n'.
	'DEBUG=`xmldbc -g '.$fanctl_debug_path.'`\n'.
	'if [ $DEBUG == 1 ]; then\n'.
	'	echo "FANCT: HD temperature $X" > /dev/console\n'.
	'fi\n'.
	'if [ $X -gt '.$fanctl_min.' -a $X -le '.$fanctl_mid.' ]; then\n'.
	'	echo "60 58" > '.$proc_fanctl.'\n'.
	'	echo "FANCT: Rotating FAN on minimum mode" > /dev/console\n'.
	'elif [ $X -gt '.$fanctl_mid.' -a $X -le '.$fanctl_max.' ]; then\n'.
	'	echo "60 50" > '.$proc_fanctl.'\n'.
	'	echo "FANCT: Rotating FAN on maximum mode" /dev/console\n'.
	'else\n'.
	'	break\n'.
	'fi\n'.
	'chmod +x '.$fanctl_start.'\n'.
	'xmldbc -t fanctl:'.$fanctl_timer.':.'.$fanctl_start.'\n'.
	'exit 0\n'
	);

fwrite("w", $STOP, "#!/bin/sh\n");
stopcmd(
	'xmldbc -k fanctl\n'.
	'exit 0\n'
	);
?>
