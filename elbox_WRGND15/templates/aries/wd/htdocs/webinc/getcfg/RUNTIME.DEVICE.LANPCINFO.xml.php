<module>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<lanpcinfo>
<?
include "/htdocs/phplib/xnode.php";

function find_hotstname($mac, $inf)
{
	$path = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	$entry_path = $path."/dhcps4/leases/entry";
	$cnt = query($entry_path."#");
	while ($cnt > 0) 
	{						
		$mac2 = query($entry_path.":".$cnt."/macaddr" );
		$hostname = query($entry_path.":".$cnt."/hostname" );		
		if($mac2 == $mac) return $hostname;		
		$cnt--; 
	}
	
	return "";	
}	
	
$arptable   = fread("", "/proc/net/arp");
$tailindex  = strstr($arptable, "\n")+1;
$macindex   = strstr($arptable, "HW address");
$devindex   = strstr($arptable, "Device");
$tablelen   = strlen($arptable);
$iplen      = strlen("255.255.255.255");
$macnull    = "00:00:00:00:00:00";
$maclen     = strlen($macnull);
$line       = substr($arptable, $tailindex, $tablelen-$tailindex);

$cnt        = 1;
$LAN1       = "LAN-1";
$LAN2       = "LAN-2";

while($line != "")
{				
	$tailindex  = strstr($line, "\n")+1;
	$tmp       = substr($line, 0, $tailindex);
				
	if($line != "")
	{		
		if(strstr($tmp, "br0")!="" || strstr($tmp, "br1")!="") 
		{
			$ip     = strip(substr($tmp, 0, $iplen+1));
			$mac    = strip(substr($tmp, $macindex, $maclen));
			if($mac != $macnull)							
			{
												
				$hostname = find_hotstname($mac, $LAN1);								
				if($hostname == "") $hostname = find_hotstname($mac, $LAN2);
																						
				echo "\t\t\t<entry>\n";
				echo "\t\t\t\t<ipaddr>".$ip."</ipaddr>\n";
				echo "\t\t\t\t<macaddr>".$mac."</macaddr>\n";
				echo "\t\t\t\t<hostname>".$hostname."</hostname>\n";
				echo "\t\t\t</entry>\n";
				
				$cnt++;
					
			}	
		}
		$tablelen = strlen($line);
		$line     = substr($line, $tailindex, $tablelen-$tailindex);			
	}		
}

?>
		</lanpcinfo>
	</runtime>
	<SETCFG>ignore</SETCFG>
	<ACTIVATE>ignore</ACTIVATE>
</module>