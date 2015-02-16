<?
/* We use VID 2 for WAN port, VID 1 for LAN ports.
 * by David Hsieh <david_hsieh@alphanetworks.com> */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)	{startcmd("exit ".$errno); stopcmd("exit ".$errno);}
//function vlancmd($var, $val) {fwrite(a,$_GLOBALS["START"], "echo ".$val." > /proc/rt3052/vlan/".$var."\n");}

function setup_switch($mode)
{
	if ( $mode == "bridge" )
	{
		SHELL_info($START, "LAYOUT: bridge mode ...=====>>>> TODO...");
	}
	else
	{
		/* Start .......................................................................... */
		/* Config VLAN as router mode layout. ( 4 LAN + 1 WAN ) */
		
	}
}

function setup_vlaninf($dev,$VID,$macaddr)
{
	$devname = $dev.".".$VID;
	startcmd(
			"vconfig add ".$dev." ".$VID."; ".
			"ip link set ".$devname." addr ".$macaddr."; ".
			"ip link set ".$devname." up"
			);
	stopcmd("ip link set ".$devname." down; vconfig rem ".$devname);
}

function layout_bridge()
{
	SHELL_info($START, "LAYOUT: Start bridge layout ...");
	/* Start .......................................................................... */
	/* Config RTL8367 as bridge mode layout. */
	setup_switch("bridge");

	/* Using WAN MAC address during bridge mode. */
	$mac = PHYINF_getmacsetting("BRIDGE-1");
	startcmd("ip link set eth0 addr ".$mac." up");

	/* Create bridge interface. */
	startcmd("brctl addbr br0; brctl stp br0 off; brctl setfd br0 0");
	startcmd("brctl addif br0 eth0");
	startcmd("ip link set br0 up");

	/* Setup the runtime nodes. */
	PHYINF_setup("ETH-1", "eth", "br0");

	/* Done */
	startcmd("xmldbc -s /runtime/device/layout bridge");
	startcmd("usockc /var/gpio_ctrl BRIDGE");
	startcmd("service ENLAN start");
	startcmd("service PHYINF.ETH-1 alias PHYINF.BRIDGE-1");
	startcmd("service PHYINF.ETH-1 start");
	

	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
	add($p."/bridge/port",	"BAND24G-1.1");
	add($p."/bridge/port",	"BAND5G-1.1");
	
	add($p."/bridge/port",  "BAND24G-1.2");
	add($p."/bridge/port",  "BAND5G-1.2");
	/* ip alias */
	$mactmp = cut($mac, 4, ":");  $mac4 = strtoul($mactmp, 16);
	$mactmp = cut($mac, 5, ":");  $mac5 = strtoul($mactmp, 16);
	
	/* skip 169.254.0.0 & 169.254.255.255 */
	if($mac4 == "0" && $mac5 == "0") $aip = "169.254.0.1";
	else if($mac4 == "255" && $mac5 == "255") $aip = "169.254.0.1";
	else $aip = "169.254.".$mac4.".".$mac5;

	/* The ip alias is to against the case of br0 can not obtain an available ip.
	 * In this case, we still can access our device through the ip alias.
	 */
	//startcmd("ifconfig br0 ".$aip." up");
//	$p 			= XNODE_getpathbytarget("", "inf", "uid", "BRIDGE-1", 0);
//	$inetp 	= XNODE_getpathbytarget("/inet", "entry","uid", query($p."/inet") , 0);
//	$ip		= query($inetp."/ipv4/ipalias/ipaddr");
//	$mask	= query($inetp."/ipv4/ipalias/mask");
	
//	startcmd("ifconfig br0:1 ".$ip." up");
	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", "BRIDGE-1", 1);
//	set($p."/ipalias/cnt",          1);
//	set($p."/ipalias/ipv4/ipaddr:1",    $ip);
//	set($p."/ipalias/ipv4/netmask:1",	$mask);
	set($p."/ipalias/ipv4/autoip",      $aip);
	set($p."/devnam","br0");
	
	/* Stop ........................................................................... */
	SHELL_info($STOP, "LAYOUT: Stop bridge layout ...");
	stopcmd("service PHYINF.ETH-1 stop");
	stopcmd("service PHYINF.BRIDGE-1 delete");
	stopcmd('xmldbc -s /runtime/device/layout ""');
	stopcmd("/etc/scripts/delpathbytarget.sh /runtime phyinf uid ETH-1");
	
	/* bridge wifi dev to br0 in WIFI_AP2G.php/WIFI_AP5G.php 's activateVAP() 2011.12.20 Daniel Chen */	
	//stopcmd("brctl delif br0 rai0");
	//stopcmd("brctl delif br0 ra0");
	
	stopcmd("brctl delif br0 eth0");
	stopcmd("ip link set eth0 down");
	stopcmd("ip link set eth1 down");
	stopcmd("ip link set br0 down");
	stopcmd("brctl delbr br0");
	return 0;
}

function layout_router($mode)
{
	SHELL_info($START, "LAYOUT: Start router layout ...");

	/* Start .......................................................................... */
	/* Config RTL8367 as router mode layout. (1 WAN + 4 LAN) */
	setup_switch("router");

	//+++ hendry, for wifi topology
	$p = XNODE_getpathbytarget("", "phyinf", "uid", "ETH-1", 0);
	set($p."/bridge/ports/entry:1/uid",		"MBR-1");
	set($p."/bridge/ports/entry:1/phyinf",	"BAND24G-1.1");	
	set($p."/bridge/ports/entry:2/uid",		"MBR-2");
	set($p."/bridge/ports/entry:2/phyinf",	"BAND5G-1.1");	
	$p = XNODE_getpathbytarget("", "phyinf", "uid", "ETH-2", 0);
	set($p."/bridge/ports/entry:1/uid",		"MBR-1");
	set($p."/bridge/ports/entry:1/phyinf",	"BAND24G-1.2");	
	set($p."/bridge/ports/entry:2/uid",		"MBR-2");
	set($p."/bridge/ports/entry:2/phyinf",	"BAND5G-1.2");	
	//--- hendry

	/* Setup MAC address */
	$wanmac = PHYINF_getmacsetting("WAN-1");
	$lanmac = PHYINF_getmacsetting("LAN-1");

	startcmd("ip link set eth0 addr ".$wanmac." up");
	startcmd("ip link set eth1 addr ".$lanmac." up");

	/* set smaller tx queue len */
	startcmd("ifconfig eth0 txqueuelen 200");

	/* Create bridge interface. */
	startcmd("brctl addbr br0; brctl stp br0 off; brctl setfd br0 0");
	startcmd("brctl addif br0 eth1");

	startcmd("ip link set br0 up");
	if ($mode=="1W2L")
	{
		startcmd("brctl addbr br1; brctl stp br1 off; brctl setfd br1 0");
		//hendry, we let guestzone to bring br1 up 
		//startcmd("ip link set br1 up");
	}

	/* Setup the runtime nodes. */
	if ($mode=="1W1L")
	{
		PHYINF_setup("ETH-1", "eth", "br0");
		PHYINF_setup("ETH-2", "eth", "eth0");
		/* set Service Alias */
		startcmd('service PHYINF.ETH-1 alias PHYINF.LAN-1');
		startcmd('service PHYINF.ETH-2 alias PHYINF.WAN-1');
		/* WAN: set extension nodes for linkstatus */
		$path 	= XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-2", 0);
		$wanindex = query("/device/router/wanindex");	if($wanindex == "") { $wanindex = "0"; }
		startcmd('xmldbc -x '.$path.'/linkstatus "get:psts -i '.$wanindex.'"');
	}
	else if ($mode=="1W2L")
	{
		PHYINF_setup("ETH-1", "eth", "br0");
		PHYINF_setup("ETH-2", "eth", "br1");
		PHYINF_setup("ETH-3", "eth", "eth0");
		/* set Service Alias */
		startcmd('service PHYINF.ETH-1 alias PHYINF.LAN-1');
		startcmd('service PHYINF.ETH-2 alias PHYINF.LAN-2');
		startcmd('service PHYINF.ETH-3 alias PHYINF.WAN-1');
		/* WAN: set extension nodes for linkstatus */
		$path = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-3", 0);
		$wanindex = query("/device/router/wanindex");	if($wanindex == "") { $wanindex = "0"; }
		startcmd('xmldbc -x '.$path.'/linkstatus "get:psts -i '.$wanindex.'"');		
	}
	//+++ hendry
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
	add($p."/bridge/port",	"BAND24G-1.1");	
	add($p."/bridge/port",	"BAND5G-1.1");	
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-2", 0);
	add($p."/bridge/port",	"BAND24G-1.2");	
	add($p."/bridge/port",	"BAND5G-1.2");	
	//--- hendry
	/* LAN: set extension nodes for linkstatus */
	$path = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
	if(query("/device/router/wanindex")=="4")
	{
		startcmd('xmldbc -x '.$path.'/linkstatus:1 "get:psts -i 0"');
		startcmd('xmldbc -x '.$path.'/linkstatus:2 "get:psts -i 1"');
		startcmd('xmldbc -x '.$path.'/linkstatus:3 "get:psts -i 2"');
		startcmd('xmldbc -x '.$path.'/linkstatus:4 "get:psts -i 3"');			
	}
	else
	{ 	//default wan index = 0 
		startcmd('xmldbc -x '.$path.'/linkstatus:1 "get:psts -i 1"');
		startcmd('xmldbc -x '.$path.'/linkstatus:2 "get:psts -i 2"');
		startcmd('xmldbc -x '.$path.'/linkstatus:3 "get:psts -i 3"');
		startcmd('xmldbc -x '.$path.'/linkstatus:4 "get:psts -i 4"');	
	}

	/* Done */
	startcmd("xmldbc -s /runtime/device/layout router");
	startcmd("xmldbc -s /runtime/device/router/mode ".$mode);
	startcmd("usockc /var/gpio_ctrl ROUTER");
	startcmd("service PHYINF.ETH-1 start");
	startcmd("service PHYINF.ETH-2 start");
	if ($mode=="1W2L") startcmd("service PHYINF.ETH-3 start");

	/* Stop ........................................................................... */
	SHELL_info($STOP, "LAYOUT: Stop router layout ...");
	if ($mode=="1W2L")
	{
		stopcmd("service PHYINF.ETH-3 stop");
		stopcmd('service PHYINF.LAN-2 delete');
	}
	stopcmd("service PHYINF.ETH-2 stop");
	stopcmd("service PHYINF.ETH-1 stop");
	stopcmd('service PHYINF.WAN-1 delete');
	stopcmd('service PHYINF.LAN-1 delete');
	stopcmd('xmldbc -s /runtime/device/layout ""');
	stopcmd('/etc/scripts/delpathbytarget.sh /runtime phyinf uid ETH-1');
	stopcmd('/etc/scripts/delpathbytarget.sh /runtime phyinf uid ETH-2');
	stopcmd('/etc/scripts/delpathbytarget.sh /runtime phyinf uid ETH-3');
	//stopcmd('brctl delif br0 ra0');
	stopcmd('brctl delif br0 eth1');
	//stopcmd('brctl delif br1 ra1');
	stopcmd('ip link set eth1 down');
	stopcmd('ip link set eth0 down');
	stopcmd('brctl delbr br0; brctl delbr br1');
	//stopcmd('vconfig rem eth0.1; vconfig rem eth0.2');
	return 0;
}

/* everything starts from here !! */
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$ret = 9;
$layout	= query("/device/layout");

startcmd("ifconfig lo up");
stopcmd("ifconfig lo down");

if ($layout=="router")
{
	/* disable LAN port */
	startcmd("/etc/scripts/lan_port.sh stop");
	
	/* only 1W1L & 1W2L supported for router mode. */
	$mode = query("/device/router/mode"); if ($mode!="1W1L") $mode = "1W2L";
	$ret = layout_router($mode);

	/* Start Hw_nat here */
	//startcmd("service HW_NAT start");
}
else if ($layout=="bridge")
{
	/* disable LAN port */
	//startcmd("/etc/scripts/lan_port.sh stop");

	$ret = layout_bridge();
	startcmd("service BRIDGE start");
	stopcmd("service BRIDGE stop");
}


error($ret);
?>
