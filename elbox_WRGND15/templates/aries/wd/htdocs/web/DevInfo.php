HTTP/1.1 200 OK

<?
if ($AUTHORIZED_GROUP!="-1")
{
	include "/htdocs/phplib/inet.php";	
	echo "Firmware External Version: V".cut(fread("", "/etc/config/buildver"), "0", "\n")."\n";
	echo "Firmware Internal Version: ".cut(fread("", "/etc/config/buildno"), "0", "\n")."\n";
	echo "Bootcode Revision: ".query("/runtime/device/bootver")."\n";   
	echo "Model Name: ".query("/runtime/device/modelname")."\n";
	echo "Hardware Version: ".query("/runtime/devdata/hwrev")."\n";
	echo "WLAN Domain: ".query("/runtime/devdata/countrycode")."\n";
	/*
	$ver = cut(fread("", "/proc/version"), "0", "(")."\n";
	echo "Kernel: ".cut($ver, "2", " ")."\n";
	$lang = query("/runtime/device/langcode"); 
	if ($lang=="") $lang="en";
	echo "Language: ".$lang."\n";
	if (query("/device/session/captcha")=="1") $captcha="Enable"; 
	else $captcha="Disable";
	echo "Graphcal Authentication: ".$captcha."\n";
	*/
	echo "LAN MAC: ".query("/runtime/devdata/lanmac")."\n";
	echo "WAN MAC: ".query("/runtime/devdata/wanmac")."\n";
	echo "WLAN MAC: ".query("/runtime/devdata/wlanmac")."\n";
	echo "Serial Number: ".query("/runtime/devdata/sn")."\n";
	$wan1_inetp=INET_getpathbyinf("WAN-1");
	if(query($wan1_inetp."/addrtype")=="ipv4")
	{
		if(query($wan1_inetp."/ipv4/static")=="1") $wan_type = "Static IP";
		else $wan_type = "DHCP";
	}
	else if(query($wan1_inetp."/addrtype")=="ppp4")
	{
		if(query($wan1_inetp."/ppp4/over")=="eth") $wan_type = "PPPoE";
		else if(query($wan1_inetp."/ppp4/over")=="pptp") $wan_type = "PPTP";
		else if(query($wan1_inetp."/ppp4/over")=="l2tp") $wan_type = "L2TP";
	}	
	echo "WAN Type: ".$wan_type."\n";
	if(query("/runtime/device/modelname")=="MyNetN900" || query("/runtime/device/modelname")=="MyNetN900C" )
	{
		$command="i2cdev -g -r 0";
		setattr("/runtime/temperature/run", "get", $command);
		$temperature = get("x", "/runtime/temperature/run");
		del("/runtime/temperature/run");
//		event("DBSAVE");
		echo "T: ".substr($temperature, 27, 2)."\n";
		echo "RPM: ".cut(fread("", "/proc/fanspeed"), "1", "\n")."\n";
	}
}
else
{
	echo "Authentication Fail. Please Login First!";
}		
?>
