<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";

$hostname = query($SETCFG_prefix."/device/hostname");
$LocalMaster = query($SETCFG_prefix."/wd/storage/master");
TRACE_debug("SETCFG/DEVICE.HOSTNAME: /device/hostname = ".$hostname);
TRACE_debug("SETCFG/DEVICE.HOSTNAME: /wd/storage/master = ".$LocalMaster);
set("/device/hostname", $hostname);
set("/wd/storage/master", $LocalMaster);
?>
