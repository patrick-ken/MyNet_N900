<?
if ($AUTHORIZED_GROUP!="-1" && $_POST["act"]=="clear")
{
	/* Clear the logs */
	$count = query("/runtime/log/".$_POST["logtype"]."/entry#");
	while($count >0) { del("/runtime/log/".$_POST["logtype"]."/entry"); $count--; }

	/* Log in sysact. */
	$TIME = query("/runtime/device/uptime");
	$TEXT = "Log (".$_POST["logtype"].") cleared by user !";
	$FACILITY = "";
	dophp("load", "/etc/services/LOG/logd_helper.php");
}
include "/htdocs/web/getcfg.php";
?>
