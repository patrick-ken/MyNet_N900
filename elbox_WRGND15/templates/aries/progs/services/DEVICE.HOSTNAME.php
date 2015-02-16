<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/mdnsresponder.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");} 

fwrite(w,$_GLOBALS["START"], "#!/bin/sh\n");
fwrite(w,$_GLOBALS["STOP"], "#!/bin/sh\n"); 

function setup_nameresolv($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		TRACE_debug("SERVICES/DEVICE.HOSTNAME: ifname = ".$ifname);
		startcmd("service NAMERESOLV.".$ifname." restart");
		$i++;
	}
	$port	= "80";
	$srvname = query("/device/hostname");
	$srvcfg = "_http._tcp local.";
	$mdirty = setup_mdns("MDNSRESPONDER.HTTP",$port,$srvname,$srvcfg);
	if ($mdirty>0)
	{
		$uid = "MDNSRESPONDER.ITUNES";
		$stsp = XNODE_getpathbytarget("/runtime/services/mdnsresponder", "server", "uid", $uid, 0);
		if ($stsp!="")
		{
			if (query($stsp."/srvname")!=$srvname)
				set($stsp."/srvname", $srvname);
			stopcmd("service ITUNES restart");
			startcmd("service ITUNES restart");
		}
		else
		{
			stopcmd('sh /etc/scripts/delpathbytarget.sh /runtime/services/mdnsresponder server uid MDNSRESPONDER.HTTP');
			stopcmd("service MDNSRESPONDER restart");
			startcmd("service MDNSRESPONDER restart");
		}
	}
}

function setup_dhcpc($prefix)
{
	$i = 1;
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("/runtime", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		if (query($ifpath."/inet/addrtype")=="ipv4" && query($ifpath."/inet/ipv4/static")=="0" 
			&& query($ifpath."/inet/ipv4/valid")=="1")
		{
			TRACE_debug("Restart DHCP client: ifname = ".$ifname);
			startcmd("service INET.".$ifname." restart");
		}
		$i++;
	}
}

function setup_upnpname($prefix)
{
	$i = 1;
	$hostname = query("/device/hostname");

	// update upnp runtime info
	foreach ("/runtime/upnp/dev")
	{
		set("devdesc/device/friendlyName", $hostname);
	}
	
	// restart service
	while ($i>0)
	{
		$ifname = $prefix."-".$i;
		$ifpath = XNODE_getpathbytarget("", "inf", "uid", $ifname, 0);
		if ($ifpath == "") { $i=0; break; }
		$upnp = query($ifpath."/upnp/count");
		if ($upnp > 0)
			startcmd("service UPNP.".$ifname." restart");
		$i++;
	}
}

function setup_wifiname()
{
	foreach ("/runtime/phyinf")
	{
		if (query("type")!="wifi") continue;
		$uid = query("uid");
		$p      = XNODE_getpathbytarget("", "phyinf", "uid", $uid, 0);
		$wifi   = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
		anchor($wifi);
		$wps_conf   = query("wps/configured");
		if ($wps_conf != "1")
		{
			startcmd("service PHYINF.WIFI restart");
			break;
		}
	}

}

function setup_storagename()
{
	startcmd("service DLNA restart");
}

$layout = query("/device/layout");
if ( $layout == "bridge" )	{	setup_nameresolv("BRIDGE");	}
else						{	setup_nameresolv("LAN");	}

if ( $layout == "bridge" )	{	setup_upnpname("BRIDGE");	}
else						{	setup_upnpname("LAN");	}

setup_wifiname();
setup_storagename();
/* If WAN mode is DHCP, restart DHCP client */
if ( $layout != "bridge" )	{	setup_dhcpc("WAN");	}


?>
