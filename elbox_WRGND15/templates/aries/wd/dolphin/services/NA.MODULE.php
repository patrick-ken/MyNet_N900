<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: na_restart */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$SYSFS="/sys/devices/system";
$UBI32NA="ubi32_na";
$UBI32NA_SYSFS_PATH=$SYSFS.'/'.$UBI32NA.'/'.$UBI32NA.'0';
$UBI32NA_CACHE_NODE="/dev/na0";
$IPV4_MODULE="ubicom_na_connection_manager_ipv4";
$IPV6_MODULE="ubicom_na_connection_manager_ipv6";
$IPV4_SYSFS_PATH=$SYSFS.'/'.$IPV4_MODULE.'/'.$IPV4_MODULE.'0';
$IPV6_SYSFS_PATH=$SYSFS.'/'.$IPV6_MODULE.'/'.$IPV6_MODULE.'0';

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

	startcmd(
		/* Mapping to SDK: na_modules_load */
		'echo "Starting NA"\n'.
		//'insmod /lib/modules/ubicom_na_connection_manager_ipv4.ko\n'.
		'insmod /lib/modules/ubicom_na_connection_manager_ipv6.ko\n'.
		/* Create char device nodes for debug */
		'if [ -e '.$UBI32NA_SYSFS_PATH.'/cache_dev_major ]; then\n'.
		'	if [ ! -e '.$UBI32NA_CACHE_NODE.' ]; then\n'.
		'		mknod '.$UBI32NA_CACHE_NODE.' c $(cat '.$UBI32NA_SYSFS_PATH.'/cache_dev_major) 0\n'.
		'	fi\n'.
		'fi\n'.
	);

	stopcmd(
		/* Mapping to SDK: na_modules_unload */
		/* threaded_module_unload(module_name, terminate_file)
		 * Terminates the module specified.  The module is threaded meaning
		 * it has one or more kernel threads to be stopped.
		 * Unloading a threaded module is more tricky - we have to signal 
		 * to terminate the threads, wait for the thread to end, then unload
		 */
		'threaded_module_unload()\n'.
		'{\n'.
		'	MODULE_NAME=$1\n'.
		'	TERMINATE_FILE=$2\n'.
		'	echo "Unloading module $MODULE_NAME"\n'.
		/* Signal to the module to begin shutdown */
		'	echo 1 > $TERMINATE_FILE\n'.
		/* Wait for the $TERMINATE_FILE to be removed - 
		 * this means the threads have stopped and unloaded sysfs files.
		 */
		'	echo "Waiting for $MODULE_NAME to unload"\n'.
		'	REFS=$(cat /proc/modules | grep -w ^$MODULE_NAME | cut -s -d \' \' -f3)\n'.
		'	if [ -z $REFS ]; then\n'.
		'	REFS=0\n'.
		'	fi\n'.
		'	until [ $REFS -eq 0 ]; do\n'.
		'		if [ -f \"/var/stop_SE_unload\" ]; then'.
		'			break\n'.	
		'		fi\n'.	
		'		echo -n .\n'.
		'		sleep 1\n'.
		'		REFS=$(cat /proc/modules | grep -w ^$MODULE_NAME | cut -s -d \' \' -f3)\n'.
		'		if [ -z $REFS ]; then\n'.
		'			REFS=0\n'.
		'		fi\n'.
		'	done\n'.
		'	echo Module $MODULE_NAME unloaded\n'.
		/* Now unload the module */
		'	rmmod $MODULE_NAME\n'.
		'}\n'.
		'stop_and_unload()\n'.
		'{\n'.
		'	MODULE_NAME=$1\n'.
		'	SYSFS_PATH=$2\n'.
		/* Stop the module */
		'	echo 1 > $SYSFS_PATH/stop\n'.
		'	threaded_module_unload $MODULE_NAME $SYSFS_PATH/terminate\n'.
		'}\n'.
		/* Mapping to SDK: na_stop */
		'if [ -e '.$IPV4_SYSFS_PATH.' ] ; then\n'.
		'	echo "Stopping Network Accelerator IPv4 module"\n'.
		'	stop_and_unload '.$IPV4_MODULE.' '.$IPV4_SYSFS_PATH.'\n'.
		'fi\n'.
		'if [ -e '.$IPV6_SYSFS_PATH.' ] ; then\n'.
		'	echo "Stopping Network Accelerator IPv6 module"\n'.
		'stop_and_unload '.$IPV6_MODULE.' '.$IPV6_SYSFS_PATH.'\n'.
		'fi\n'.
		/* Remove char device nodes */
		'if [ -e '.$UBI32NA_CACHE_NODE.' ]; then\n'.
		'	rm '.$UBI32NA_CACHE_NODE.'\n'.
		'fi\n'.
	);

error($ret);
?>
