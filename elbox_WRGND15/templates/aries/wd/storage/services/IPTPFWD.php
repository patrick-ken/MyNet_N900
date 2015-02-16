<?
/* VSVR & PFWD are depends on LAN services.
 * Be sure to start LAN services first. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/inf.php";
include "/etc/services/IPTABLES/iptlib.php";
include "/htdocs/phplib/igdentry.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
if ($ME!="virtualserver") $ME="portforward";
/* Get all the LAN interface IP address. */
IPT_scan_lan();
/* Build the useful inbound filter rules in iptables. */
IPT_build_inbound_filter($START);


$orion = query("/orion/enable");
$port_list = "/var/pfw_use_port";
fwrite("w", $port_list, "");

function toipt_style($port_list)
{
	$port_result = "";
	$cnt = cut_count($port_list, ",");
	$idx = 0;
	while ($idx < $cnt)
	{
		if($idx > 0) $port_result = $port_result.",";
		$port = cut($port_list,$idx,",");
		if (cut_count($port, "-") > 1)
			$port_result = $port_result.cut($port,0,"-").":".cut($port,1,"-");
		else
			$port_result = $port_result.$port;
		$idx++;
	}
	return $port_result;
}

$cnt = query("/nat/count"); if ($cnt=="") $cnt = 0;
foreach ("/nat/entry")
{
	/* beyond the count are garbage */
	if ($InDeX>$cnt) break;

	/* Get the CHAIN */
	$UID = query("uid");
	if ($ME=="portforward")	$CHAIN="DNAT.PFWD.".$UID;
	else					$CHAIN="DNAT.VSVR.".$UID;
	/* Mark that there is no rules in the CHAIN. */
	XNODE_set_var($CHAIN.".USED", "0");
	/* Flush the CHAIN */
	fwrite("a",$START, "iptables -t nat -F ".$CHAIN."\n");
	fwrite("a",$START, "iptables -t nat -F PFWD.".$UID."\n");
	fwrite("a",$STOP,  "iptables -t nat -F ".$CHAIN."\n");
	fwrite("a",$STOP,  "iptables -t nat -F PFWD.".$UID."\n");

	/* Walk through the rules. */
	$ecnt = query($ME."/count"); if ($ecnt=="") $ecnt=0;
	foreach ($ME."/entry")
	{
		/* beyond the count are garbage */
		if ($InDeX>$ecnt) break;
		/* enable ? */
		if (query("enable")!=1 || query("inbfilter")=="denyall") continue;

		/* check the destination host */
		$inf	= query("internal/inf");
		$hostid = query("internal/hostid");
		$ipaddr = XNODE_get_var($inf.".IPADDR");
		$mask	= XNODE_get_var($inf.".MASK");
		if ($ipaddr=="" || $mask=="" || $hostid=="" || $inf=="") continue;
		$ipaddr = ipv4ip($ipaddr, $mask, $hostid);
		if ($ipaddr=="") continue;

		if (query("tport_str")!="" || query("uport_str")!="")
		{
			$tcpport	= toipt_style(query("tport_str"));
			$udpport	= toipt_style(query("uport_str"));
			$tcp_portcmd = ""; $udp_portcmd = "";
			$TCP = " -m mport --destination-ports ".$tcpport;
			$UDP = " -m mport --destination-ports ".$udpport;
			$DNAT_TARGET = " --to-destination ".$ipaddr;
			$TARGET_NAT = " -j DNAT".$DNAT_TARGET;
			/* time */
			$sch = query("schedule");
			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			/* Inbound Filter*/
			if (query("inbfilter") != "")	$inbfn = cut(query("inbfilter"), 1, "-");

			$iptcmd = "iptables -t nat -A ".$CHAIN." ".$timecmd;
			if($tcpport!="")
			{
				if (query("inbfilter")!="") fwrite("a",$START, $iptcmd." -p tcp ".$TCP." "."-j CK_INBOUND".$inbfn."\n");
				fwrite("a",$START, $iptcmd." -p tcp ".$TCP." ".$TARGET_NAT."\n");
			}
			if($udpport!="")
			{
				if (query("inbfilter")!="") fwrite("a",$START, $iptcmd." -p udp ".$UDP." "."-j CK_INBOUND".$inbfn."\n");
				fwrite("a",$START, $iptcmd." -p udp ".$UDP." ".$TARGET_NAT."\n");
			}
			XNODE_set_var($CHAIN.".USED", "1");
		}
		else
		{
			/* check the protocol */
			$prot_tcp = 0; $prot_udp = 0; $prot_other = 0; $offset = 0;
			$prot = query("protocol");
			if ($prot=="TCP+UDP") {	$prot_tcp++; $prot_udp++; }
			else if	($prot=="TCP")	$prot_tcp++;
			else if	($prot=="UDP")	$prot_udp++;
			else if	($prot=="Other")$prot_other++;
			else continue;

			if($prot_other==0)
			{
				/* check port setting */
				$ext_end	= query("external/end");
				$ext_start	= query("external/start");	if ($ext_start=="") continue;
				$int_start	= query("internal/start");	if ($int_start=="") $int_start = $ext_start;
				if		($int_start > $ext_start) $offset = $int_start - $ext_start;
				else if ($int_start < $ext_start) $offset = 65536 - $ext_start + $int_start;
				else							  $offset = 0;

				/* port */
				if ($ext_end=="" || $ext_end==$ext_start)
				{
					$portcmd = "--dport ".$ext_start;	/* Single port forwarding */
					$external_port = $ext_start;
					$internal_port = $int_start;
				}
				else
				{
					$portcmd = "-m mport --ports ".$ext_start.":".$ext_end; /* Multi port forwarding */
					$external_port = $ext_start."-".$ext_end;
					$tmp = $ext_end - $ext_start + $int_start;
					$internal_port = $int_start."-".$tmp;
				}
			}
			/* DNAT */
			if ($offset=="0") $dnatcmd = "-j DNAT --to-destination ".$ipaddr;
			else $dnatcmd = "-j DNAT --to-shift ".$ipaddr.":".$offset;
			/* time */
			$sch = query("schedule");
			if ($sch=="") $timecmd = "";
			else $timecmd = IPT_build_time_command($sch);
			/* Inbound Filter*/
			if (query("inbfilter") != "")	$inbfn = cut(query("inbfilter"), 1, "-");

			$iptcmd = "iptables -t nat -A ".$CHAIN." ".$timecmd;
			$app_name = query("description");
			if ($prot_tcp>0)
			{
				if (query("inbfilter") != "") fwrite("a",$START, $iptcmd." -p tcp ".$portcmd." "."-j CK_INBOUND".$inbfn."\n");
				fwrite("a",$START, $iptcmd." -p tcp ".$portcmd." ".$dnatcmd."\n");
				fwrite("a", $port_list, "TCP:".$external_port.",".$ipaddr.",".$internal_port.",".$app_name."\n");
				$enable = CheckIGDEntry($external_port, "TCP", $internal_port, $ipaddr, "PFW");
				if($enable != 1) InsertIGDEntry($external_port, "TCP", $internal_port, $ipaddr, "PFW");
				fwrite("a", $STOP, "xmldbc -P /etc/scripts/RemoveIGDEntry.php -V EXTERNALPORT=".$external_port." -V DEST=PFW\n");
			}
			if ($prot_udp>0)
			{
				if (query("inbfilter") != "") fwrite("a",$START, $iptcmd." -p udp ".$portcmd." "."-j CK_INBOUND".$inbfn."\n");
				fwrite("a",$START, $iptcmd." -p udp ".$portcmd." ".$dnatcmd."\n");
				fwrite("a", $port_list, "UDP:".$external_port.",".$ipaddr.",".$internal_port.",".$app_name."\n");
				$enable = CheckIGDEntry($external_port, "UDP", $internal_port, $ipaddr, "PFW");
				if($enable != 1)InsertIGDEntry($external_port, "UDP", $internal_port, $ipaddr, "PFW");
				fwrite("a", $STOP, "xmldbc -P /etc/scripts/RemoveIGDEntry.php -V EXTERNALPORT=".$external_port." -V DEST=PFW\n");
			}
			if ($prot_other>0)
			{
				if (query("inbfilter") != "") fwrite("a",$START, $iptcmd." -p ".query("protocolnum")." "."-j CK_INBOUND".$inbfn."\n");
				fwrite("a",$START, $iptcmd." -p ".query("protocolnum")." ".$dnatcmd."\n");
			}
			XNODE_set_var($CHAIN.".USED", "1");

			/* Wake-On-Lan */
			if ($ME=="virtualserver" && query("description")=="Wake-On-Lan" && query("wakeonlan_mac")!="")
			{
				fwrite("a",$START, "arp -s ".$ipaddr." ".query("wakeonlan_mac")."\n");
				fwrite("a",$STOP,  "arp -d ".$ipaddr."\n");
			}
		}
	}

	/* Add VSVR and PFWD chain */
	$CHAIN="DNAT.VSVR.".$UID;
	if (XNODE_get_var($CHAIN.".USED")>0)
		fwrite("a", $START, "iptables -t nat -A PFWD.".$UID." -j ".$CHAIN."\n");
	$CHAIN="DNAT.PFWD.".$UID;
	if (XNODE_get_var($CHAIN.".USED")>0)
		fwrite("a", $START, "iptables -t nat -A PFWD.".$UID." -j ".$CHAIN."\n");
}

/* Remote Access port */
$name = "WAN-1";
$path = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$all_tcp_port = "";
$all_udp_port = "";
if ($path!="")
{
	$https_rport = query($path."/https_rport");
	$web = query($path."/web");

	if ($https_rport != "" && $orion > 0 )
		fwrite("a", $port_list, "TCP:".$https_rport.",127.0.0.1,443,WebMgt\n");
	else if ($web != "" && $orion > 0)
		fwrite("a", $port_list, "TCP:".$web.",127.0.0.1,80,WebMgt\n");
}

if ($orion>0)
{
	$inf	= "LAN-1";
	fwrite("a", $START, "iptables -t nat -N ORION.PFWD\n");
	fwrite("a", $START, "iptables -t nat -F ORION.PFWD\n");
	foreach ("/runtime/orion/entry")
	{
		$protocol = query("portocol");
		$app_name = query("application_name");
		if($protocol=="TCP")
		{
			$external_port = query("external_port");
			$internal_port = query("internal_port");
			$lan_ip = query("lan_ip");
			fwrite("a", $START, "iptables -t nat -A ORION.PFWD -p tcp --dport ".$external_port." -j DNAT --to-destination ".$lan_ip.":".$internal_port."\n");
		}
		else if($protocol=="UDP")
		{
			$external_port = query("external_port");
			$internal_port = query("internal_port");
			$lan_ip = query("lan_ip");
			fwrite("a", $START, "iptables -t nat -A ORION.PFWD -p udp --dport ".$external_port." -j DNAT --to-destination ".$lan_ip.":".$internal_port."\n");
		}
		fwrite("a", $port_list, $protocol.":".$external_port.",".$lan_ip.",".$internal_port.",".$app_name."\n");
	}
	fwrite("a", $START, "iptables -t nat -A DNAT.PFWD.NAT-1 -j ORION.PFWD\n");
	fwrite("a", $START, "iptables -t nat -A PFWD.NAT-1 -j DNAT.PFWD.NAT-1\n");

	fwrite("a", $STOP, "iptables -t nat -F DNAT.PFWD.NAT-1\n");
	fwrite("a", $STOP, "iptables -t nat -F ORION.PFWD\n");
	fwrite("a", $STOP, "iptables -t nat -X ORION.PFWD\n");
	XNODE_set_var("DNAT.PFWD.NAT-1.USED", "1");
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
