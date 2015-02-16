#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inet.php";

function add_each($list, $path, $node)
{
	//echo "# add_each(".$list.",".$path.",".$node.")\n";
	$i = 0;
	$cnt = scut_count($list, "");
	while ($i < $cnt)
	{
		$val = scut($list, $i, "");
		if ($val!="") add($path."/".$node, $val);
		$i++;
	}
	return $cnt;
}

function dev_detach($hasevent)
{
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($sts=="") return $_GLOBALS["INF"]." has no runtime nodes.";
	if (query($sts."/inet/addrtype")!="ipv4") return $_GLOBALS["INF"]." is not ipv4.";
	if (query($sts."/inet/ipv4/valid")!=1) return $_GLOBALS["INF"]." is not active.";
	$devnam = query($sts."/devnam");
	if ($devnam=="") return $_GLOBALS["INF"]." has no device name.";

	anchor($sts."/inet/ipv4");
	$ipaddr	= query("ipaddr");
	$mask	= query("mask");
	$srt	= query("sstrout#");
	$crt	= query("clsstrout#");
	echo "ip addr del ".$ipaddr."/".$mask." dev ".$devnam."\n";
	echo "ip route flush table ".$_GLOBALS["INF"]."\n";
	if ($srt>0 || $crt>0) echo "ip rule del table ".$_GLOBALS["INF"]."\n";
	if ($hasevent>0)
	{
		echo "rm -f /var/run/".$_GLOBALS["INF"].".UP\n";
		echo "event ".$_GLOBALS["INF"].".DOWN\n";
	}

	del($sts."/inet");
	del($sts."/udhcpc");
	del($sts."/devnam");
		/*marco*/
	
	if($_GLOBALS["INF"]=="LAN-1")
	{
		echo "echo -n \"0\" > /proc/nf_accelerate_to_local \n";
	}
	
}

function dev_attach($hasevent)
{
	$cfg = XNODE_getpathbytarget("", "inf", "uid", $_GLOBALS["INF"], 0);
	if ($cfg=="") return $_GLOBALS["INF"]."does not exist!";
	$sts = XNODE_getpathbytarget("/runtime", "inf", "uid", $_GLOBALS["INF"], 1);

	/* Just in case the device is still alive. */
	if (query($sts."/inet/ipv4/valid")==1||query($sts."/inet/ipv4/conflict")==1) dev_detach(0);

	/* Get the defaultroute metric from config. */
	$defrt = query($cfg."/defaultroute");
	/* Get the netmask */
	if ($_GLOBALS["SUBNET"]!="") $mask = ipv4mask2int($_GLOBALS["SUBNET"]);
	else $mask = $_GLOBALS["MASK"];
	
	/* Check if there is the same network id */
	if($_GLOBALS["INF"]=="WAN-1")
	{
		$netid	= ipv4networkid($_GLOBALS["IPADDR"], $mask);
		$lan = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
		$lan_inet = XNODE_getpathbytarget("/inet", "entry", "uid", query($lan."/inet"), 0);
		$lan_netid	= ipv4networkid(query($lan_inet."/ipv4/ipaddr"), query($lan_inet."/ipv4/mask"));

		$lan2 = XNODE_getpathbytarget("", "inf", "uid", "LAN-2", 0);
		$lan_inet2 = XNODE_getpathbytarget("/inet", "entry", "uid", query($lan2."/inet"), 0);
		$lan_netid2	= ipv4networkid(query($lan_inet2."/ipv4/ipaddr"), query($lan_inet2."/ipv4/mask"));
		echo "echo netid: ".$netid." > /dev/console\n";
		echo "echo lan_netid: ".$lan_netid." > /dev/console\n";
		echo "echo lan_netid2: ".$lan_netid2." > /dev/console\n";

		if($netid==$lan_netid)//Check WAN-1 and LAN-1
		{
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo WAN-1 and LAN-1 conflict > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			set($sts."/inet/uid", query($cfg."/inet"));
			set($sts."/inet/ipv4/conflict","1");

			$new_ip = "";//For LAN-1
			$new_netmask=24;//For LAN-1 and LAN-2
			$new_ip2 = "";//For LAN-2
			
			if (query($lan_inet."/ipv4/mask") >= 24)
			{
				if ($netid == "192.168.1.0") { $new_ip = "192.168.6.1"; $new_netmask=24; }
				else if ($netid == "192.168.6.0") { $new_ip = "192.168.66.1"; $new_netmask=24; }
				else if ($netid == "192.168.66.0") { $new_ip = "192.168.126.1"; $new_netmask=24; }
				else if ($netid == "192.168.126.0") { $new_ip = "192.168.166.1"; $new_netmask=24; }
				$new_ip2 = "192.168.252.1";//For LAN-2
			}
			else if (query($lan_inet."/ipv4/mask") >= 16 && query($lan_inet."/ipv4/mask") < 24 )
			{
				$new_ip = "172.17.0.1"; 
				$new_netmask=16;	
				$new_ip2 = "172.30.0.1";//For LAN-2 
			}
			else if (query($lan_inet."/ipv4/mask") >= 8 && query($lan_inet."/ipv4/mask") < 16 )
			{
				$new_ip = "10.1.0.1"; 
				$new_netmask=16;	
				$new_ip2 = "10.10.0.1";//For LAN-2 
			}
			else if (query($lan_inet."/ipv4/mask") < 8)
			{
				$new_ip = "192.168.1.1"; 
				$new_netmask=24;	
				$new_ip2 = "192.168.252.1";//For LAN-2
			}

			if ( $new_ip == "" )
			{
				$new_ip = "10.1.0.1"; 
				$new_netmask=16;	
				$new_ip2 = "10.10.0.1";//For LAN-2
			}
			set($lan_inet."/ipv4/ipaddr", $new_ip);//For LAN-1
			set($lan_inet."/ipv4/mask", $new_netmask);//For LAN-1
			set($lan_inet2."/ipv4/ipaddr", $new_ip2);//For LAN-2
			set($lan_inet2."/ipv4/mask", $new_netmask);//For LAN-2
			echo "echo new LAN-1 ip: ".$new_ip." > /dev/console\n";
			echo "echo new LAN-2 ip: ".$new_ip2." > /dev/console\n";
			echo "echo new netmask: ".$new_netmask." > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo ---save and reboot--- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "mfc save\n";
			echo "reboot\n";
			return "I.";
		}
		if($netid==$lan_netid2)//Check WAN-1 and LAN-2
		{
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo WAN-1 and LAN-2 conflict > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			set($sts."/inet/uid", query($cfg."/inet"));
			set($sts."/inet/ipv4/conflict","1");

			$new_ip = "";//For LAN-2
			$new_netmask=24;//For LAN-2
			
			if (query($lan_inet2."/ipv4/mask") >= 24)
			{
				if ($netid == "192.168.252.0") { $new_ip = "192.168.251.1"; $new_netmask=24; }
				else if ($netid == "192.168.251.0") { $new_ip = "192.168.250.1"; $new_netmask=24; }
				else if ($netid == "192.168.250.0") { $new_ip = "192.168.249.1"; $new_netmask=24; }
				else if ($netid == "192.168.249.0") { $new_ip = "192.168.248.1"; $new_netmask=24; }
				else if ($netid == "172.30.0.0") { $new_ip = "172.30.1.0"; $new_netmask=24; }
				else if ($netid == "172.30.1.0") { $new_ip = "172.30.2.0"; $new_netmask=24; }
				else if ($netid == "172.30.2.0") { $new_ip = "172.30.3.0"; $new_netmask=24; }
				else if ($netid == "172.30.3.0") { $new_ip = "172.30.4.0"; $new_netmask=24; }
			}
			else if (query($lan_inet2."/ipv4/mask") >= 16 && query($lan_inet2."/ipv4/mask") < 24 )
			{
				$new_ip = "172.29.0.1"; 
				$new_netmask=16;
			}
			else if (query($lan_inet2."/ipv4/mask") >= 8 && query($lan_inet2."/ipv4/mask") < 16 )
			{
				$new_ip = "10.9.0.1";
				$new_netmask=16;
			}
			else if (query($lan_inet2."/ipv4/mask") < 8)
			{
				$new_ip = "192.168.252.1";
				$new_netmask=24;
			}

			if ( $new_ip == "" )
			{
				$new_ip = "10.9.0.1";
				$new_netmask=16;
			}
		
			set($lan_inet2."/ipv4/ipaddr", $new_ip);
			set($lan_inet2."/ipv4/mask", $new_netmask);
			echo "echo new LAN-2 ip: ".$new_ip." > /dev/console\n";
			echo "echo new netmask: ".$new_netmask." > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo ---save and reboot--- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "mfc save\n";
			echo "reboot\n";
			return "I.";
		}
	}
	if($_GLOBALS["INF"]=="LAN-1")//Check LAN-1 and LAN-2
	{
		$lan = XNODE_getpathbytarget("", "inf", "uid", "LAN-2", 0);
		$lan_inet = XNODE_getpathbytarget("/inet", "entry", "uid", query($lan."/inet"), 0);

		if(query($lan_inet."/ipv4/mask") < $mask)	$mask=query($lan_inet."/ipv4/mask");//use smaller mask
		$lan_netid	= ipv4networkid(query($lan_inet."/ipv4/ipaddr"), $mask);
		$netid	= ipv4networkid($_GLOBALS["IPADDR"], $mask);
		echo "echo netid: ".$netid." > /dev/console\n";
		echo "echo lan_netid: ".$lan_netid." > /dev/console\n";

		if($netid==$lan_netid)
		{
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo LAN-1 and LAN-2 conflict > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			echo "echo +++++++++++++++++ > /dev/console\n";
			$addr1 = cut($_GLOBALS["IPADDR"], 0, ".");
			$addr2 = cut($_GLOBALS["IPADDR"], 1, ".");
			$addr3 = cut($_GLOBALS["IPADDR"], 2, ".");
			$addr4 = cut($_GLOBALS["IPADDR"], 3, ".");
			echo "echo addr1: ".$addr1." > /dev/console\n";
			echo "echo addr2: ".$addr2." > /dev/console\n";
			echo "echo addr3: ".$addr3." > /dev/console\n";
			echo "echo addr4: ".$addr4." > /dev/console\n";

			$new_ip = "";
			if($addr1 == "192")
			{
				$new_ip = "172.30.0.1";
			}
			else
			{
				$new_ip = "192.168.252.1";
			}
			//For LAN-2
			set($lan_inet."/ipv4/ipaddr", $new_ip);
			set($lan_inet."/ipv4/mask", "24");//Default mask is 24
			echo "echo new LAN-2 ip: ".$new_ip." > /dev/console\n";
			echo "echo new netmask: 24 > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo ---save and reboot--- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "echo --------------------- > /dev/console\n";
			echo "mfc save\n";
			echo "reboot\n";
			return "I.";
		}
	}
	set($sts."/inet/ipv4/conflict","0"); 
	
	/* Get the broadcast address */
	if ($_GLOBALS["BROADCAST"]!="") $brd = $_GLOBALS["BROADCAST"];
	else
	{
		$max = ipv4maxhost($mask);
		$brd = ipv4ip($_GLOBALS["IPADDR"], $mask, $max);
	}
	/* MTU */
	if ($_GLOBALS["MTU"]=="") $mtu = 1500;
	else $mtu = $_GLOBALS["MTU"]+0; // convert to integer, just in case.
	/* Record the domain in /runtime/device for our global domain name. */
	$domain=$_GLOBALS["DOMAIN"];/*marco*/
	if ($domain != query("/runtime/device/domain"))
	{
		set("/runtime/device/domain", $domain);
		$restartdhcpswer = 1;
	}

	/***********************************************/
	/* Update Status */
	set($sts."/defaultroute",	$defrt);
	set($sts."/devnam",			$_GLOBALS["DEVNAM"]);
	set($sts."/inet/uid",		query($cfg."/inet"));
	set($sts."/inet/addrtype",	"ipv4");
	set($sts."/inet/uptime",	query("/runtime/device/uptime"));
	set($sts."/inet/ipv4/valid","1");
	/* INET */
	anchor($sts."/inet/ipv4");
	set("static",	$_GLOBALS["STATIC"]);
	set("mtu",		$mtu);
	set("ipaddr",	$_GLOBALS["IPADDR"]);
	set("mask",		$mask);
	set("gateway",	$_GLOBALS["GATEWAY"]);
	set("domain",	$_GLOBALS["DOMAIN"]);
	/* DNS & Routing */
	add_each($_GLOBALS["DNS"],      $sts."/inet/ipv4", "dns");
	add_each($_GLOBALS["SSTROUT"],  $sts."/inet/ipv4", "sstrout");   /* static route - DHCP option 0x21 */
	add_each($_GLOBALS["CLSSTROUT"],$sts."/inet/ipv4", "clsstrout"); /* classes static route - DHCP option 0x79, RFC 3442 */

	/************************************************/
	/* attach */
//marco
	if ($_GLOBALS["GATEWAY"]!="")
	{
		echo "echo 0 > /proc/sys/net/ipv4/ip_forward\n";
	}
	echo "ip link set ".$_GLOBALS["DEVNAM"]." mtu ".$mtu."\n";
	echo "ip addr add ".$_GLOBALS["IPADDR"]."/".$mask." broadcast ".$brd." dev ".$_GLOBALS["DEVNAM"]."\n";
	/* gateway */
	if ($_GLOBALS["GATEWAY"]!="")
	{
		$netid = ipv4networkid($_GLOBALS["IPADDR"], $mask);
		if ($defrt!="" && $defrt>0)
		{	
			echo "ip route add default via ".$_GLOBALS["GATEWAY"]." metric ".$defrt." table default\n";
		}
		else
		{	
			echo "ip route add ".$netid."/".$mask." dev ".$_GLOBALS["DEVNAM"]." src ".$_GLOBALS["IPADDR"]." table ".$_GLOBALS["INF"]."\n";
	}
	}

/* Russia PPTP connection we need add the routing for server IP if the network is not the same*/
	$nat = query($cfg."/nat");
	$active = query($cfg."/active");
	if( $nat == "NAT-1" && $active == "1")
	{
//		TRACE_debug("[IPV4.INET] Russia PPTP Mode---------");
		$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
		if ($infp=="") return "WAN-1 does not exist!";

		$inet = query($infp."/inet");
		if ($inet=="") return "WAN-1 inet does not exit!";

		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);

		if (query($inetp."/ppp4/over")=="pptp" || query($inetp."/ppp4/over")=="l2tp")
		{
			if (query($inetp."/ppp4/over")=="pptp")
			{
				$server = query($inetp."/ppp4/pptp/server");
			}
			else if (query($inetp."/ppp4/over")=="l2tp")
			{
				$server = query($inetp."/ppp4/l2tp/server");
			}
			$dev = $_GLOBALS["DEVNAM"];
			$ip = $_GLOBALS["IPADDR"];
			$gw = $_GLOBALS["GATEWAY"];
			
			if (INET_validv4network($ip, $server, $mask) != 1) //if not the same network, we need add the route table
			{
//				TRACE_debug("[IPV4.INET]add route server=".$server." gw=".$gw." dev=".$dev);
				echo "ip route add ".$server." via ".$gw." dev ".$dev."\n";
			}
		}

	}

	/* PPTP/L2TP connection */
/* In order to support server address in FQDN, move this part to SETVPNSRRT.php, the server IP is determined just before pppd dialup */	
/*	
	$upperlayer = query($cfg."/upperlayer");
	if ($upperlayer!="")
	{
		$infp = XNODE_getpathbytarget("", "inf", "uid", $upperlayer, 0);
		if ($infp=="") return $upperlayer." does not exist!";
		
		$inet = query($infp."/inet");
		if ($inet=="") return $upperlayer." inet does not exit!";
		
		$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);

		if (query($inetp."/ppp4/over")=="pptp" || query($inetp."/ppp4/over")=="l2tp")
		{
			if (query($inetp."/ppp4/over")=="pptp")
			{
				$server = query($inetp."/ppp4/pptp/server");
			}
			else if (query($inetp."/ppp4/over")=="l2tp")
			{
				$server = query($inetp."/ppp4/l2tp/server");
			}
			$dev = $_GLOBALS["DEVNAM"];
			$ip = $_GLOBALS["IPADDR"];
			$gw = $_GLOBALS["GATEWAY"];
			
			if (INET_validv4network($ip, $server, $mask) == 1)
			{
				echo "ip route add ".$server." dev ".$dev."\n";
			}
			else
			{
				echo "ip route add ".$server." via ".$gw." dev ".$dev."\n";
			}
		}
	}
*/
	/* Routing */
	$hasroute=0;
	foreach("sstrout")
	{
		$ipaddr = cut($VaLuE, 0, ",");
		$mask	= cut($VaLuE, 2, ",");
		$gateway= cut($VaLuE, 3, ",");
		$netid	= ipv4networkid($ipaddr, $mask);
		echo "ip route add ".$netid."/".$mask." via ".$gateway." table ".$_GLOBALS["INF"]."\n";
		$hasroute++;
	}
	foreach("clsstrout")
	{
		$ipaddr = cut($VaLuE, 0, ",");
		$mask	= cut($VaLuE, 2, ",");
		$gateway= cut($VaLuE, 3, ",");
		$netid	= ipv4networkid($ipaddr, $mask);
		echo "ip route add ".$netid."/".$mask." via ".$gateway." table ".$_GLOBALS["INF"]."\n";
		$hasroute++;
	}
	if ($hasroute>0) echo "ip rule add table ".$_GLOBALS["INF"]." prio 30000\n";

	if ($hasevent>0)
	{
		echo "echo 1 > /var/run/".$_GLOBALS["INF"].".UP\n";
		echo "event ".$_GLOBALS["INF"].".UP\n";
	}
	/*marco*/
	if($_GLOBALS["INF"]=="LAN-1")
	{
		//echo "echo -n \"".$_GLOBALS["DEVNAM"]."\" > /proc/nf_accelerate_to_local \n";
		//hendry, close temp since can't work with loopback (port redirection)
		echo "echo -n \"0\" > /proc/nf_accelerate_to_local \n";
	}
}

function main_entry()
{
	if ($_GLOBALS["INF"]=="") return "No INF !!";
	if		($_GLOBALS["ACTION"]=="ATTACH") return dev_attach(1);
	else if	($_GLOBALS["ACTION"]=="DETACH") return dev_detach(1);
	return "Unknown action - ".$_GLOBALS["ACTION"];
}

/*****************************************/
$ret = main_entry();
if ($ret != "") echo "# ".$ret."\nexit 9\n";
else echo "exit 0\n";
/*****************************************/
?>
