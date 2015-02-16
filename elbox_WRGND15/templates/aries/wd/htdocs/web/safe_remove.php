HTTP/1.1 200 OK
Content-Type: text/xml

<?
if ($AUTHORIZED_GROUP < 0)
{
	$result = "Authenication fail";
}
else if ($_POST["act"] == "SET")
{
	set("/runtime/wd/remove/entry:1/Vendor", $_POST["vendor"]);
	set("/runtime/wd/remove/entry:1/Model", $_POST["model"]);
	set("/runtime/wd/remove/entry:1/serial_number", $_POST["serial_number"]);
	set("/runtime/wd/remove/entry:1/unmount", 0);	
	event("SAFE.REMOVE");
	$result = "OK";
}
else if ($_POST["act"] == "GET")
{
	$unmount = get("x", "/runtime/wd/remove/entry:1/unmount");
	$result = "OK";
}
echo '<?xml version="1.0"?>\n';
?><safe_remove>
	<report><?=$result?></report>
	<unmount><?=$unmount?></unmount>
</safe_remove>
