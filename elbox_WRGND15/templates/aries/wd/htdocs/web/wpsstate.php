HTTP/1.1 200 OK
Content-Type: text/xml

<?echo '<?xml version="1.0" encoding="utf-8"?>';?>
<wpsstate>
<?
include "/htdocs/phplib/trace.php";
$i=0;
foreach ("/runtime/phyinf")
{
	if (query("type")=="wifi")
	{
		$i++;
		$uid = query("uid");
		echo "	<phyinf>\n".
			 "		<uid>".$uid."</uid>\n".
			 "		<state>".query("media/wps/enrollee/state")."</state>\n".
			 "		<configured>".query("media/wps/configured")."</configured>\n".
			 "	</phyinf>\n";
	}
}
echo "	<count>".$i."</count>\n";
?></wpsstate>
