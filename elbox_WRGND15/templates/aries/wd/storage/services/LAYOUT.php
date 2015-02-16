<?
/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)	{startcmd("exit ".$errno); stopcmd("exit ".$errno);}

function setup_switch($mode)
{
	if ($mode == "bridge") {
		SHELL_info($START, "LAYOUT: bridge mode ...");

		/* software reset AR8327s */
		startcmd("echo \"write 0x0 0x80001202\" > /proc/ar8327/mii/ctrl1");

		startcmd("echo \"write 0x0010 0x40000000\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 0x007c 0x0000007e\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 0x0004 0x07680000\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 0x000c 0x03c00000\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 0x0624 0x007f7f7f\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 0x0094 0x0000007e\" > /proc/ar8327/mii/ctrl1");
		startcmd("echo \"write 4 0x1d 0x0012\" > /proc/ar8327/phy/ctrl1");
		startcmd("echo \"write 4 0x1e 0x4c04\" > /proc/ar8327/phy/ctrl1");

		/* pro/storage enable hw snoop */
		$use_hw_snoop=1;
		if ( $use_hw_snoop == 1 )
		{
			/* for hw igmp snoop */
			startcmd("echo \"write 0x0210 0x07070707\" > /proc/ar8327/mii/ctrl1");
			//HW snooping handle IPMP v3 
			startcmd("echo \"write 0x0214 0x01000707\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0620 0x000004f0\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x03c 0x00400000\" > /proc/ar8327/mii/ctrl1");
			//HW snooping for uknown multicast will only flood to CPU port
			startcmd("echo \"write 0x0624 0x217f017f\" > /proc/ar8327/mii/ctrl1");
			//ARL_CTRL Register, the group address is never aged-out before receiving the leave packet
			startcmd("echo \"write 0x0618 0x50f8002b\" > /proc/ar8327/mii/ctrl1");
		}
	}
	else {
		/* pro/storage enable hw snoop */
		$use_hw_snoop=1;
		if ( $use_hw_snoop == 1 )
		{
			/* let multicast packet 239.255.255.250/ff02::c will flood all port in ARL table. */
			startcmd("echo \"write 0x0600 0x5e7ffffa\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0604 0x001f0100\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0608 0x0000010f\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x060c 0x80000002\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0600 0x0000000c\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0604 0x801f3333\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0608 0x0000010f\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x060c 0x80000002\" > /proc/ar8327/mii/ctrl1");
			/* let multicast packet 224.0.0.252/ff02::1:3 will flood all port in ARL table. */
			startcmd("echo \"write 0x0600 0x5e0000fc\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0604 0x001f0100\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0608 0x0000010f\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x060c 0x80000002\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0600 0x00010003\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0604 0x801f3333\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x0608 0x0000010f\" > /proc/ar8327/mii/ctrl1");
			startcmd("echo \"write 0x060c 0x80000002\" > /proc/ar8327/mii/ctrl1");
		}
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
	/* Config AR8327 as bridge mode layout. */
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

	/* calculate auto-ip - skip 169.254.0.0 & 169.254.255.255 */
	$mactmp = cut($mac, 4, ":");
	$mac4 = strtoul($mactmp, 16);
	$mactmp = cut($mac, 5, ":");
	$mac5 = strtoul($mactmp, 16);
	if ($mac4 == "0" && $mac5 == "0") {
		$aip = "169.254.0.1";
	}
	else if ($mac4 == "255" && $mac5 == "255") {
		$aip = "169.254.0.1";
	}
	else {
		$aip = "169.254.".$mac4.".".$mac5;
	}

	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", "BRIDGE-1", 1);
	set($p."/devnam", "br0");
	set($p."/ipalias/ipv4/autoip", $aip);

	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 1);
	add($p."/bridge/port", "BAND24G-1.1");
	add($p."/bridge/port", "BAND5G-1.1");

	add($p."/bridge/port", "BAND24G-1.2");
	add($p."/bridge/port", "BAND5G-1.2");

	/* Stop ........................................................................... */
	SHELL_info($STOP, "LAYOUT: Stop bridge layout ...");
	stopcmd("service PHYINF.ETH-1 stop");
	stopcmd("service PHYINF.BRIDGE-1 delete");
	stopcmd('xmldbc -s /runtime/device/layout ""');
	stopcmd("/etc/scripts/delpathbytarget.sh /runtime phyinf uid ETH-1");

	stopcmd("brctl delif br0 ath0");
	stopcmd("brctl delif br0 ath2");

	stopcmd("brctl delif br0 eth0");
	stopcmd("ip link set eth0 down");
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

	startcmd("ip link set eth1 addr ".$wanmac." up");   
	startcmd("ip link set eth0 addr ".$lanmac." up"); 

	/* set smaller tx queue len */
	startcmd("ifconfig eth1 txqueuelen 200");

	/* Create bridge interface. */
	startcmd("brctl addbr br0; brctl stp br0 off; brctl setfd br0 0");
	startcmd("brctl addif br0 eth0");
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
		PHYINF_setup("ETH-2", "eth", "eth1");
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
		PHYINF_setup("ETH-3", "eth", "eth1");
		/* set Service Alias */
		startcmd('service PHYINF.ETH-1 alias PHYINF.LAN-1');
		startcmd('service PHYINF.ETH-2 alias PHYINF.LAN-2');
		startcmd('service PHYINF.ETH-3 alias PHYINF.WAN-1');
		/* WAN: set extension nodes for linkstatus */
		$path = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-3", 0);
		$wanindex = query("/device/router/wanindex");	if($wanindex == "") { $wanindex = "0"; }
		startcmd('xmldbc -x '.$path.'/linkstatus "get:psts -i '.$wanindex.'"');		
		
		$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
		add($p."/bridge/port",	"BAND24G-1.1");	
		add($p."/bridge/port",	"BAND5G-1.1");	
		$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-2", 0);
		add($p."/bridge/port",	"BAND24G-1.2");	
		add($p."/bridge/port",	"BAND5G-1.2");	
	}
	
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
	stopcmd('brctl delif br0 eth0');
	stopcmd('ip link set eth1 down');
	stopcmd('ip link set eth0 down');
	stopcmd('brctl delbr br0; brctl delbr br1');
	return 0;
}

/* everything starts from here !! */
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$ret = 9;
$layout	= query("/device/layout");

startcmd("ifconfig lo up");
stopcmd("ifconfig lo down");


if ($layout == "router") {

	startcmd("/etc/scripts/lan_port.sh stop");

	/* only 1W1L & 1W2L supported for router mode. */
	$mode = query("/device/router/mode"); if ($mode != "1W1L") $mode = "1W2L";
	$ret = layout_router($mode);

	/* Start Hw_nat here */
	//startcmd("service HW_NAT start");
}
else if ($layout == "bridge") {
	$ret = layout_bridge();
	startcmd("service BRIDGE start");
	stopcmd("service BRIDGE stop");
}


error($ret);
?>
