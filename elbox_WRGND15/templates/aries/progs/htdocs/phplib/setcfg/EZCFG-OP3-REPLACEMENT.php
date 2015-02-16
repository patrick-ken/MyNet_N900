<?
include "/htdocs/phplib/xnode.php";

$a = $SETCFG_prefix."/wan/entry";
foreach($a)
{
	$inf = XNODE_getpathbytarget("", "inf", "uid", query("uid"), 0);
	if ($inf!="")
	{
		set($inf."/active", query("active"));
		$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf."/inet"), 0);

		if ($inet!="")
		{
			set($inet."/addrtype", query("addrtype"));
			set($inet."/ipv4/static", query("ipv4/static"));
			set($inet."/ipv4/mtu", query("ipv4/mtu"));
			set($inet."/ipv4/dns/count", query("ipv4/dns/count"));
			set($inet."/ipv4/dns/entry:1", query("ipv4/dns/entry:1"));
			set($inet."/ipv4/dns/entry:2", query("ipv4/dns/entry:2"));	
			set($inet."/ipv4/ipaddr", query("ipv4/ipaddr"));
			set($inet."/ipv4/mask", query("ipv4/mask"));
			set($inet."/ipv4/gateway", query("ipv4/gateway"));
			set($inet."/ppp4/static", query("ppp4/static"));
			set($inet."/ppp4/mtu", query("ppp4/mtu"));
			set($inet."/ppp4/ipaddr", query("ppp4/ipaddr"));
			set($inet."/ppp4/username", query("ppp4/username"));
			set($inet."/ppp4/password", query("ppp4/password"));
			set($inet."/ppp4/over", query("ppp4/over"));
                	set($inet."/ppp4/dns/count", query("ppp4/dns/count"));
                	set($inet."/ppp4/dns/entry:1", query("ppp4/dns/entry:1"));
                	set($inet."/ppp4/dns/entry:2", query("ppp4/dns/entry:2"));
			set($inet."/ppp4/dialup/mode", query("ppp4/dialup/mode"));
			set($inet."/ppp4/dialup/idletimeout", query("ppp4/dialup/idletimeout"));
			set($inet."/ppp4/mppe/enable", query("ppp4/mppe/enable"));
			set($inet."/ppp4/pppoe/acname", query("ppp4/pppoe/acname"));
			set($inet."/ppp4/pppoe/servicename", query("ppp4/pppoe/servicename"));
			set($inet."/ppp4/pptp/server", query("ppp4/pptp/server"));
			set($inet."/ipv6/mode", query("ipv6/mode"));
                	set($inet."/ipv6/mtu", query("ipv6/mtu"));
			set($inet."/ipv6/pdhint/enable", query("ipv6/pdhint/enable"));
                	set($inet."/ipv6/dns/count", query("ipv6/dns/count"));
                	set($inet."/ipv6/dns/entry:1", query("ipv6/dns/entry:1"));
                	set($inet."/ipv6/dns/entry:2", query("ipv6/dns/entry:2"));
                	set($inet."/ppp6/over", query("ppp6/over"));
                	set($inet."/ppp6/static", query("ppp6/static"));
                	set($inet."/ppp6/ipaddr", query("ppp6/ipaddr"));
			set($inet."/ppp6/mtu", query("ppp6/mtu"));
			set($inet."/ppp6/mru", query("ppp6/mru"));
                	set($inet."/ppp6/username", query("ppp6/username"));
                	set($inet."/ppp6/password", query("ppp6/password"));
                	set($inet."/ppp6/dialup/mode", query("ppp6/dialup/mode"));
                	set($inet."/ppp6/dialup/idletimeout", query("ppp6/dialup/idletimeout"));
                	set($inet."/ppp6/dns/count", query("ppp6/dns/count"));
                	set($inet."/ppp6/dns/entry:1", query("ppp6/dns/entry:1"));
                	set($inet."/ppp6/dns/entry:2", query("ppp6/dns/entry:2"));
                	set($inet."/ppp6/pppoe/acname", query("ppp6/pppoe/acname"));
                	set($inet."/ppp6/pppoe/servicename", query("ppp6/pppoe/servicename"));
		}
	}
}

$a = $SETCFG_prefix."/lan/entry";
foreach($a)
{
	$inf = XNODE_getpathbytarget("", "inf", "uid", query("uid"), 0);
	if ($inf!="")
	{
        	set($inf."/active", query("active"));
        	$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf."/inet"), 0);
        	if ($inet!="")
        	{
                	set($inet."/addrtype", query("addrtype"));
                	set($inet."/ipv4/ipaddr", query("ipv4/ipaddr"));
                	set($inet."/ipv4/mask", query("ipv4/mask"));
		}

		$dhcps4 = XNODE_getpathbytarget("", "dhcps4/entry", "uid", query($inf."/dhcps4"), 0);
		if ($dhcps4!="")
		{
                	set($dhcps4."/start", query("dhcps4/start"));
                	set($dhcps4."/end", query("dhcps4/end"));
               		set($dhcps4."/domain", query("dhcps4/domain"));
                	set($dhcps4."/leasetime", query("dhcps4/leasetime"));
        	}
	}
}

$a = $SETCFG_prefix."/wifi/entry";
foreach($a)
{
	$inf = XNODE_getpathbytarget("", "phyinf", "uid", query("uid"), 0);
	if ($inf!="")
	{
		set($inf."/active", query("active"));
		set($inf."/media/wlmode", query("media/wlmode"));
		set($inf."/media/dot11n/bandwidth", query("media/dot11n/bandwidth"));
		set($inf."/media/dot11n/guardinterval", query("media/dot11n/guardinterval"));
		set($inf."/media/wmm/enable", query("media/wmm/enable"));

		$inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
		if ($inet!="")
		{
			set($inet."/opmode",  query("opmode"));
			set($inet."/ssid",  query("ssid"));
			set($inet."/ssidhidden",  query("ssidhidden"));
			set($inet."/authtype",  query("authtype"));
			set($inet."/encrtype",  query("encrtype"));
			set($inet."/wps/enable",  query("wps/enable"));
			set($inet."/nwkey/psk/passphrase",  query("nwkey/psk/passphrase"));
			set($inet."/nwkey/psk/key",  query("nwkey/psk/key"));
			set($inet."/wps/configured", "1");
		}
	}
}

$count = query($SETCFG_prefix."/account/entry#");
TRACE_debug("SETCFG: DEVICE.ACCOUNT got ".$count." accounts");
movc($SETCFG_prefix."/account", "/device/account");
set("/device/account/max", $count);
set("/device/account/count", $count);

del("acl/firewall");
$a = $SETCFG_prefix."/security/entry";
$count = 0;
foreach($a)
{
        $count = $InDeX;
        $e = "/acl/firewall/entry:".$InDeX;
        set($e."/uid", query("uid"));
        set($e."/enable", query("enable"));
        set($e."/description", query("description"));
        set($e."/src/inf", query("src/inf"));
        set($e."/src/host/start", query("src/host/start"));
        set($e."/src/host/end", query("src/host/end"));
        set($e."/protocol", query("protocol"));
        set($e."/policy", query("policy"));
        set($e."/dst/inf", query("dst/inf"));
        set($e."/dst/host/start", query("dst/host/start"));
        set($e."/dst/host/end", query("dst/host/end"));
        set($e."/schedule", query("schedule"));
}
TRACE_debug("SETCFG: FIREWALL.ACCOUNT got ".$count." accounts");
set("/acl/firewall/max", "32");
set("/acl/firewall/count", $count);
set("/acl/firewall/seqno", $count);
set("/acl/firewall/policy", "ACCEPT");
?>
