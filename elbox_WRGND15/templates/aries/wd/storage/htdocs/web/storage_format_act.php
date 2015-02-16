HTTP/1.1 200 OK
Content-Type: text/xml

<?
function PHP_sleep($second) 
{ 
	setattr("/runtime/sleep",  "get", "sleep ".$second); 
	query("/runtime/sleep");
}

if ($_POST["act"] == "format")
{
	set("/runtime/storage/format_status", "");
	event("STORAGE.FORMAT");
	$format_result = "formatting";
}
else if ($_POST["act"] == "format_result")
{
	$format_result = get("x", "/runtime/storage/format_status");
	if($format_result == "1") $format_result="success";
}
else if ($_POST["act"] == "restore")
{
	set("/runtime/storage/restore_status", "");
	setattr("/runtime/storage/restore", "set", "sh /etc/scripts/restore_hard_drive.sh remove_pid"); 
	set("/runtime/storage/restore", "");
	PHP_sleep(10);
	setattr("/runtime/storage/restore", "set", "sh /etc/scripts/restore_hard_drive.sh partition_hd"); 
	set("/runtime/storage/restore", "");
	PHP_sleep(10);
	PHP_sleep(10);
	PHP_sleep(10);
	$restore_result = "restoring";
}
else if ($_POST["act"] == "restore_result")
{
	if(isfile("/var/restore_status2")==1 && isfile("/var/restore_status3")==1) $restore_result="success";
	else $restore_result="";
}
echo '<?xml version="1.0"?>\n';
?><format>
	<format_report><?=$format_result?></format_report>
	<restore_report><?=$restore_result?></restore_report>
</format>