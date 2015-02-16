<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$BRINTERFACE = "br0";

function wish_module_load($BRINTERFACE)
{
	startcmd(
		'echo "Starting WISH on '.$BRINTERFACE.'" > /dev/console\n'.
		'insmod /lib/modules/nf_ubicom_wish.ko\n'.
		);
	return 0;
}

function wish_module_unload($BRINTERFACE)
{
	stopcmd(
		'echo "Stopping WISH on '.$BRINTERFACE.'" > /dev/console\n'.
		'echo 1 > /sys/devices/system/ubicom_wish/ubicom_wish0/wish_terminate\n'.
		/* Wait for the module to stop */
		'until [  ! -e /sys/devices/system/ubicom_wish/ubicom_wish0/wish_terminate ]; do\n'.
		'	sleep 1\n'.
		'	echo -n .\n'.
		'done\n'.
		'rmmod nf_ubicom_wish\n'
	);
	return 0;
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");


/* Is WISH currently running?  If so then stop it. */
$ret = wish_module_load($BRINTERFACE);
$ret = wish_module_unload($BRINTERFACE);

error($ret);
?>
