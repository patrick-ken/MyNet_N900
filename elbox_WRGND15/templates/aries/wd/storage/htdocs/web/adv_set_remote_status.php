HTTP/1.1 200 OK
Content-Type: text/xml
<?
include "/htdocs/phplib/xnode.php";
if ($AUTHORIZED_GROUP < 0)
{
	$RESULT = "FAIL";
	$REASON = i18n("Permission denied. This user is unauthorized.");
}
else
{
	set("/runtime/remote_access/enable", $_POST["status"]);
}
?>
<?echo '<?xml version="1.0" encoding="utf-8"?>';?>
<remoteaccess>
	<action><? echo $_POST["status"]; ?></action>
	<result><?=$RESULT?></result>
	<reason><?=$REASON?></reason>
</remoteaccess>
