<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}


function wish_mount_state()
{
	startcmd(
		'STATE_MAJOR_NUMBER=$(cat /sys/devices/system/ubicom_wish/ubicom_wish0/wish_dev_major)\n'.
		'mknod /dev/ubicom_wish c $STATE_MAJOR_NUMBER 0\n'
	);
	return 0;
}

function wish_unmount_state()
{
	stopcmd(
		/* No longer need the special file */
		'rm /dev/ubicom_wish\n'
	);
	return 0;
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$ret = wish_mount_state();
$ret = wish_unmount_state();

error($ret);
?>
