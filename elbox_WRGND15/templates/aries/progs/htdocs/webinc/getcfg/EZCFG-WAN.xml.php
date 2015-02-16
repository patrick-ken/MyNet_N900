<?include "/htdocs/phplib/xnode.php";
echo "<module>\n";
echo "<service>".$GETCFG_SVC."</service>\n";
echo "<wan>\n";
$mode = "";
$russia = "0";
$ipaddr = "";
$subnet = "";
$gateway = "";
$dns1 = "";
$dns2 = "";
$ppp_user = "";
$ppp_pass = "";
$pptp_user = "";
$pptp_pass = "";
$pptp_ipaddr = "";
$l2tp_user = "";
$l2tp_pass = "";
$l2tp_ipaddr = "";
$inf1 = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
$inf2 = XNODE_getpathbytarget("", "inf", "uid", "WAN-2", 0);
$inf4 = XNODE_getpathbytarget("", "inf", "uid", "WAN-4", 0);
$lan4 = XNODE_getpathbytarget("", "inf", "uid", "LAN-4", 0);
$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf1."/inet"), 0);
$inet2 = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf2."/inet"), 0);
if (query($inet."/addrtype") == "ipv4")
{
	if (query($inet."/ipv4/static") == "1")
	{
		$mode = "STATIC";
		$ipaddr = query($inet."/ipv4/ipaddr");
		$subnet = query($inet."/ipv4/mask");
		$gateway = query($inet."/ipv4/gateway");
		$dns1 = query($inet."/ipv4/dns/entry:1");
		$dns2 = query($inet."/ipv4/dns/entry:2");		
	}
	else
	{
		$mode = "DHCP";	
	}
}
else if (query($inet."/addrtype") == "ppp4")
{
	if (query($inet."/ppp4/over") == "eth")
	{
		$mode = "PPPOE";
		$ppp_user = query($inet."/ppp4/username");
		$ppp_pass = query($inet."/ppp4/password");

		if (query($inf2."/nat") == "NAT-1" && query($inf2."/active") == "1")
		{
			$russia = "1";
		}
	}
	else if (query($inet."/ppp4/over") == "pptp")
	{
		$pptp_user = query($inet."/ppp4/username");
		$pptp_pass = query($inet."/ppp4/password");
		$pptp_ipaddr = query($inet."/ppp4/pptp/server");
		$mode = "PPTP";
		if (query($inf2."/nat") == "NAT-1" && query($inf2."/active") == "1")
		{
			$russia = "1";
		}
	}
	else if (query($inet."/ppp4/over") == "l2tp")
	{
		$l2tp_user = query($inet."/ppp4/username");
		$l2tp_pass = query($inet."/ppp4/password");
		$l2tp_ipaddr = query($inet."/ppp4/l2tp/server");
		$mode = "L2TP";
        if (query($inf2."/nat") == "NAT-1" && query($inf2."/active") == "1")
        {
			$russia = "1";
        }
	}
}
else if (query($inet."/addrtype") == "ppp10")
{
	$ppp_user = query($inet."/ppp6/username");
	$ppp_pass = query($inet."/ppp6/password");
	if (query($inet."/ppp4/over") == "eth")
	{
		$mode = "PPPOE";
	}
}
echo "<mode>".$mode."</mode>\n";
echo "<russia>".$russia."</russia>\n";
echo "<static>\n";
echo "<ipaddr>".$ipaddr."</ipaddr>\n";
echo "<subnet>".$subnet."</subnet>\n";
echo "<gateway>".$gateway."</gateway>\n";
echo "<dns1>".$dns1."</dns1>\n";
echo "<dns2>".$dns2."</dns2>\n";
echo "</static>\n";
echo "<pppoe>\n";
echo "<username>".$ppp_user."</username>\n";
echo "<password>".$ppp_pass."</password>\n";
echo "</pppoe>\n";
echo "<pptp>\n";
echo "<username>".$pptp_user."</username>\n";
echo "<password>".$pptp_pass."</password>\n";
echo "<ipaddr>".$pptp_ipaddr."</ipaddr>\n";
echo "</pptp>\n";
echo "<l2tp>\n";
echo "<username>".$l2tp_user."</username>\n";
echo "<password>".$l2tp_pass."</password>\n";
echo "<ipaddr>".$l2tp_ipaddr."</ipaddr>\n";
echo "</l2tp>\n";
?></wan>
</module>
