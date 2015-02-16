<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

/* Mapping to SDK: se_modules_load */
function se_module_load()
{
	startcmd(
		'insmod /lib/modules/ubicom_streamengine_tracker.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_tracker_tcp.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_tracker_udp.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_tracker_datagram.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_db.ko\n'.	
		'insmod /lib/modules/ubicom_streamengine_classifier_default.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier_user_rules.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier_bittorrent.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier_http.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier_http_content.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier_ftp.ko\n'.
		'insmod /lib/modules/ubicom_streamengine_classifier.ko\n'.
		/* If if_name sys node is not ready and writeble, wait one second to be sure */
		'SE_SYSFS_WAN_IF=/sys/devices/system/streamengine_classifier/streamengine_classifier0/streamengine_classifier_if_name\n'.
		'if [ ! -w ${SE_SYSFS_WAN_IF} ]; then'.
		'   sleep 1\n'.
		'fi\n'.
		);
}

/* Mapping to SDK: se_modules_unload */
function se_module_unload()
{
	stopcmd(
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
		'	echo "Unloading module $MODULE_NAME ..."\n'.
		/* Signal to the module to begin shutdown */
		'	echo 1 > $TERMINATE_FILE\n'.
		/* Wait for the $TERMINATE_FILE to be removed - 
		 * this means the threads have stopped and unloaded sysfs files.
		 */
		//'	echo Waiting for $MODULE_NAME to unload\n'.
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
		/* Is classifier currently running?  If so then stop it. */
		//'if [ ! -e /sys/devices/system/streamengine_classifier/streamengine_classifier0 ]; then\n'.
		//'	exit 0\n'.
		//'fi\n'.
		/* Signal to the classifier front end to stop processing further */
		'echo 1 > /sys/devices/system/streamengine_classifier/streamengine_classifier0/streamengine_classifier_stop\n'.
		/* Ask the database to expire all connections */
		'echo 1 > /sys/devices/system/streamengine_db/streamengine_db0/expire_all\n'.
		/* Wait for all connections to expire*/
		/*'UNEXPIRED=$(cat /sys/devices/system/streamengine_db/streamengine_db0/expire_all)\n'.
		'echo Waiting for $UNEXPIRED data objects to be destroyed\n'.
		'until [ $UNEXPIRED -eq 0 ]; do\n'.
		'	echo Waiting for $UNEXPIRED data objects to be destroyed\n'.
		'	sleep 1\n'.
		'	if [ -e /sys/devices/system/streamengine_db/streamengine_db0/expire_all]; then\n'.
		'		UNEXPIRED=$(cat /sys/devices/system/streamengine_db/streamengine_db0/expire_all)\n'.
		'	else\n'.
		'		UNEXPIRED=0\n'.
		'	fi\n'.
		'done\n'.
		'echo All Streamengine data objects are destroyed\n'.
		*/
		/* Now we can unload the modules - reverse order of loading
		 *  NOTE: Some modules don't have threads so can just be unloaded directly.
		 */
		'threaded_module_unload ubicom_streamengine_classifier /sys/devices/system/streamengine_classifier/streamengine_classifier0/terminate\n'.
		'threaded_module_unload ubicom_streamengine_classifier_ftp /sys/devices/system/streamengine_classifier_ftp/streamengine_classifier_ftp0/terminate\n'.
		'threaded_module_unload ubicom_streamengine_classifier_http_content /sys/devices/system/streamengine_classifier_http_content/streamengine_classifier_http_content0/terminate\n'.
		'rmmod ubicom_streamengine_classifier_http\n'.
		'threaded_module_unload ubicom_streamengine_classifier_bittorrent /sys/devices/system/streamengine_classifier_bittorrent/streamengine_classifier_bittorrent0/terminate\n'.
		'threaded_module_unload ubicom_streamengine_classifier_user_rules /sys/devices/system/streamengine_classifier_user_rules/streamengine_classifier_user_rules0/terminate\n'.
		'threaded_module_unload ubicom_streamengine_classifier_default /sys/devices/system/streamengine_classifier_default/streamengine_classifier_default0/terminate\n'.
		'threaded_module_unload ubicom_streamengine_db /sys/devices/system/streamengine_db/streamengine_db0/terminate\n'.		
		'rmmod ubicom_streamengine_tracker_datagram\n'.
		'rmmod ubicom_streamengine_tracker_udp\n'.
		'rmmod ubicom_streamengine_tracker_tcp\n'.
		'rmmod ubicom_streamengine_tracker\n'
		);
	return 0;
}
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$ret = se_module_load();
$ret = se_module_unload();

error($ret);
?>
