HTTP/1.1 200 OK
Content-Type: text/xml

<?
$country = query("/runtime/devdata/countrycode");
$result = query("/device/hostname");
if ($result == "MyNetN600")
{
	$number = 1;
}
else if ($result == "MyNetN750")
{
	$number = 2;
}
else if ($result == "MyNetN900")
{
	$number = 3;
}
else if ($result == "MyNetN900C")	
{
	$number = 4;
}
else if ($result == "MyNetAC1800")
{
	        $number = 5;
}
else
{
	$number = 0;
}
echo '<?xml version="1.0"?>\n';
?><ezcfg>
    <wdrouter>1</wdrouter>
	<model><?=$number?></model>
	<countrycode><?=$country?></countrycode>
</ezcfg>
