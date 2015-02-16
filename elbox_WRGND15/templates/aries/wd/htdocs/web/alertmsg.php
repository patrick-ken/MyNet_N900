HTTP/1.1 200 OK
Content-Type: text/xml

<?
if ($AUTHORIZED_GROUP < 0)
{
	$result = "Authenication fail";
}
else if ($_POST["act"] == "ignore")
{
	set("/runtime/alertmsg/".$_POST["alert_item"]."/ignore", 1);
	$result = "OK";
}	
else if ($_POST["act"] == "ping")
{
	set("/runtime/diagnostic/ping", $_POST["ping_dst"]);
	$result = "OK";
}
else if ($_POST["act"] == "report")
{
	$result = get("x", "/runtime/diagnostic/ping");
	if($result=="www.google.com is alive!" || $result=="www.wdc.com is alive!")
	{
		set("/runtime/alertmsg/WAN_detect_fail/connect", 1);
		set("/runtime/alertmsg/WAN_detect_fail/ignore", 0);
	}	
	else
	{
		set("/runtime/alertmsg/WAN_detect_fail/connect", 0);
	}
}
else if ($_POST["act"] == "fw_check")
{
	event("FW.CHECK");
	$result = "OK";
}
echo '<?xml version="1.0"?>\n';
?><alertmsg>
	<report><?=$result?></report>
</alertmsg>
