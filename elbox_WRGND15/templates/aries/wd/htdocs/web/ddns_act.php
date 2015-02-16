HTTP/1.1 200 OK
Content-Type: text/xml

<?
echo '<?xml version="1.0"?>\n';
echo '<ddns4>\n';

include "/htdocs/phplib/xnode.php";
$inf = "WAN-1";

if ($_POST["act"] == "update")
{
	event("DDNS4.".$inf.".UPDATE");
	echo "OK";
}
else if ($_POST["act"] == "getreport")
{
	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	$mode = "EnableDDNS";
	$now_provider = "";
	$now_status = "";
	$now_result = "";
	if ($p != "")
	{
		$now_provider =query($p."/ddns4/provider");
		$now_status =query($p."/ddns4/status");
		$now_result =query($p."/ddns4/result");
		if($now_status=="IDLE")
		{
			if($now_result=="SVRERR" || $now_result=="NOAUTH" || $now_result=="ERROR" || $now_result=="BADHOST")
			{
				$mode = "DisableDDNS";
			}
		}
		if($mode == "DisableDDNS")
		{
			echo "<valid>1</valid>\n";
			echo "<provider>".$now_provider."</provider>\n";
			echo "<status>IDLE</status>\n";
			echo "<result>".$now_result."</result>\n";
			set($p."/ddns4/valid","0");
		}
		else
		{
			echo dump(2, $p."/ddns4");
		}
	}
	else
	{
		echo "<valid>0</valid>\n";
	}
}

echo '</ddns4>\n';
?>
