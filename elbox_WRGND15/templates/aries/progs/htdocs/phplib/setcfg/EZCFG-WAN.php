<?
include "/htdocs/phplib/xnode.php";
$mode = query($SETCFG_prefix."/wan/mode");
$inf1 = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
$inf2 = XNODE_getpathbytarget("", "inf", "uid", "WAN-2", 0);
$inf4 = XNODE_getpathbytarget("", "inf", "uid", "WAN-4", 0);
$lan4 = XNODE_getpathbytarget("", "inf", "uid", "LAN-4", 0);
$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf1."/inet"), 0);
$inet2 = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf2."/inet"), 0);
if ($inet!="")
{
	TRACE_debug("EZCFG-WAN mode: (".query($SETCFG_prefix."/wan/mode").")");
	TRACE_debug("EZCFG-WAN russia: (".query($SETCFG_prefix."/wan/russia").")");
	//TRACE_debug("EZCFG-WAN pptp server: (".query($SETCFG_prefix."/wan/pptp/ipaddr").")");
	//TRACE_debug("EZCFG-WAN l2tp server: (".query($SETCFG_prefix."/wan/l2tp/ipaddr").")");	
	set("runtime/ezcfg/wan_config/status", "1");
	set($inf1."/schedule", "");
	set($inf1."/lowerlayer","");
	set($inf1."/upperlayer","");
	set($inf1."/infprevious", "");
	set($inf2."/schedule", "");
	set($inf2."/lowerlayer","");
	set($inf2."/upperlayer","");
	set($inf2."/active", "0");
	set($inf2."/nat","");
	set($inet."/ipv4/ipv4in6/mode","");
	if (strstr(query($inf4."/infnext"), "WAN") != "")
	{
		set($inf4."/infnext", "");
	}
	if (strstr(query($inf4."/infnext:2"), "WAN") != "")
	{
		set($inf4."/infnext:2", "");
	}
	set($lan4."/dns6", "");
	set($lan4."dnsrelay", "0");
	
	if ($mode == "STATIC")
	{

		$cnt = 0;
		set($inet."/addrtype", "ipv4");
		set($inet."/ipv4/static",  "1");
		set($inet."/ipv4/ipaddr", query($SETCFG_prefix."/wan/static/ipaddr"));
		set($inet."/ipv4/mask", query($SETCFG_prefix."/wan/static/subnet"));
		set($inet."/ipv4/gateway", query($SETCFG_prefix."/wan/static/gateway"));

		if (query($SETCFG_prefix."/wan/static/dns1") != "")
		{
			set($inet."/ipv4/dns/entry", query($SETCFG_prefix."/wan/static/dns1"));
			$cnt = 1;
		}
	
		if ($cnt==1)
		{
			if (query($SETCFG_prefix."/wan/static/dns2") != "")
			{
				set($inet."/ipv4/dns/entry:2", query($SETCFG_prefix."/wan/static/dns2"));
				$cnt = 2;
			}
		}
		else
		{
			if (query($SETCFG_prefix."/wan/static/dns2") != "")
			{
				set($inet."/ipv4/dns/entry", query($SETCFG_prefix."/wan/static/dns2"));
				$cnt = 1;
			}
		}
		set($inet."/ipv4/dns/count", $cnt);
	}
	else if ($mode == "DHCP")
	{
		set($inet."/addrtype", "ipv4");
		set($inet."/ipv4/static",  "0");
		set($inet."/ipv4/dns/entry", "");
		set($inet."/ipv4/dns/entry:2", "");
		set($inet."/ipv4/dns/count", 0);
		set($inet."/ipv4/dhcpplus/enable", "0");	
	}
	else if ($mode == "PPPOE")
	{
		if (query($inet."/addrtype") == "ppp10" && query($inet."/ppp4/over") == "eth")
		{
			set($inet."/addrtype", "ppp10");
			set($inet."/ppp6/username", query($SETCFG_prefix."/wan/pppoe/username"));
			set($inet."/ppp6/password", query($SETCFG_prefix."/wan/pppoe/password"));
			set($inet."/ppp6/over", "eth");
		}
		else
		{
			set($inet."/addrtype", "ppp4");
		}
		set($inet."/ppp4/over", "eth");
		set($inet."/ppp4/username", query($SETCFG_prefix."/wan/pppoe/username"));
		set($inet."/ppp4/password", query($SETCFG_prefix."/wan/pppoe/password"));
		set($inet."/ppp4/static", "0");
		set($inet."/ppp4/ipaddr", "");
		set($inet."/ppp4/mru", "");
		set($inet."/ppp4/dns/entry:1","");
		set($inet."/ppp4/dns/entry:2","");
		set($inet."/ppp4/dns/count", 0);
		set($inet."/ppp4/dns/entry:1", "");
		set($inet."/ppp4/dns/entry:2", "");
		set($inet."/ppp4/dialup/mode", "auto");
		set($inet."/ppp4/dialup/idletimeout", "0");
		set($inet."/ppp4/pppoe/acname", "");
		set($inet."/ppp4/pppoe/servicename", "");
		set($inet."/ppp4/mppe/enable", 0);
	}
	else if ($mode == "PPTP")
	{
		if (query($SETCFG_prefix."/wan/russia") == "1")
		{
			set($inf1."/defaultroute", "100");
			set($inf2."/defaultroute", "200");
			set($inf1."/lowerlayer", "WAN-2");//JERRY
			set($inf2."/nat", "NAT-1");
		}
		else
		{
			set($inf1."/defaultroute", "100");
			set($inf2."/defaultroute", "");
			set($inf1."/lowerlayer", "WAN-2");
			set($inf2."/upperlayer", "WAN-1");
			set($inf2."/nat", "");
		}	
		set($inf2."/active", "1");
		set($inet."/addrtype", "ppp4");	
		set($inet."/ppp4/over", "pptp");
		set($inet."/ppp4/static", "0");
		set($inet."/ppp4/mtu", "1400");
		set($inet."/ppp4/username", query($SETCFG_prefix."/wan/pptp/username"));
		set($inet."/ppp4/password", query($SETCFG_prefix."/wan/pptp/password"));
		set($inet."/ppp4/pptp/server", query($SETCFG_prefix."/wan/pptp/ipaddr"));
		set($inet2."/ipv4/static", "0");	
		set($inet."/ppp4/ipaddr", "");
		set($inet."/ppp4/mru", "");
		set($inet."/ppp4/dns/count", "0");
		del($inet2."/ipv4/dns");
		set($inet2."/ipv4/dns/count", "0");
		set($inet."/ppp4/dialup/mode", "auto");
		set($inet."/ppp4/dialup/idletimeout", "0");
		set($inet."/ppp4/mppe/enable", 0);
	}
	else if ($mode == "L2TP")
	{
        if (query($SETCFG_prefix."/wan/russia") == "1")
        {
            set($inf1."/defaultroute", "100");
            set($inf2."/defaultroute", "200");
            set($inf1."/lowerlayer", "WAN-2");//JERRY
            set($inf2."/nat", "NAT-1");
        }
        else
        {
            set($inf1."/defaultroute", "100");
            set($inf2."/defaultroute", "");
            set($inf1."/lowerlayer", "WAN-2");
            set($inf2."/upperlayer", "WAN-1");
            set($inf2."/nat", "");
        }
        set($inf2."/active", "1");
        set($inet."/addrtype", "ppp4");
        set($inet."/ppp4/over", "l2tp");
        set($inet."/ppp4/static", "0");
        set($inet."/ppp4/mtu", "1400");
        set($inet."/ppp4/username", query($SETCFG_prefix."/wan/l2tp/username"));
        set($inet."/ppp4/password", query($SETCFG_prefix."/wan/l2tp/password"));
        set($inet."/ppp4/l2tp/server", query($SETCFG_prefix."/wan/l2tp/ipaddr"));
        set($inet2."/ipv4/static", "0");
		set($inet."/ppp4/ipaddr", "");
		set($inet."/ppp4/mru", "");
        set($inet."/ppp4/dns/count", "0");
        del($inet2."/ipv4/dns");
        set($inet2."/ipv4/dns/count", "0");
        set($inet."/ppp4/dialup/mode", "auto");
		set($inet."/ppp4/dialup/idletimeout", "0");
		set($inet."/ppp4/mppe/enable", 0);
	}
}
?>
