#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/igdentry.php";

/*
<runtime>
	<orion>
		<entry>
			<application_name>example</application_name>
			<portocol>TCP</portocol>
			<external_port>80</external_port>
			<internal_port>80</internal_port>
			<lan_ip>192.168.1.101</lan_ip>
		</entry>
	</orion>
</runtime>
*/
//TRACE_debug("ACTION=".$ACTION);
//TRACE_debug("APPNAME=".$APPNAME);
//TRACE_debug("PROTOCOL=".$PROTOCOL);
//TRACE_debug("EXTERNALPORT=".$EXTERNALPORT);
//TRACE_debug("INTERNALPORT=".$INTERNALPORT);
//TRACE_debug("LANIP=".$LANIP);

$name = "LAN-1";
$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$upnp = query($infp."/upnp/count");

$dirty=0;
if($ACTION == "ADD")
{
	$stsp = XNODE_getpathbytarget("/runtime/orion", "entry", "external_port", $EXTERNALPORT, 0);
	if ($stsp=="")
	{
		$dirty++;
		$stsp = XNODE_getpathbytarget("/runtime/orion", "entry", "external_port", $EXTERNALPORT, 1);
		set($stsp."/application_name",$APPNAME);
		set($stsp."/portocol",	$PROTOCOL);
		set($stsp."/external_port",	$EXTERNALPORT);
		set($stsp."/internal_port",	$INTERNALPORT);
		set($stsp."/lan_ip",	$LANIP);
	}
	else
	{
		if ($APPNAME!="")
		{
			if (query($stsp."/application_name")!=$APPNAME)		{ $dirty++; set($stsp."/application_name", $APPNAME); }
			if (query($stsp."/portocol")!=$PROTOCOL)			{ $dirty++; set($stsp."/portocol", $PROTOCOL); }
			if (query($stsp."/external_port")!=$EXTERNALPORT)	{ $dirty++; set($stsp."/external_port", $EXTERNALPORT); }
			if (query($stsp."/internal_port")!=$INTERNALPORT)	{ $dirty++; set($stsp."/internal_port", $INTERNALPORT); }
			if (query($stsp."/lan_ip")!=$LANIP)					{ $dirty++; set($stsp."/lan_ip", $LANIP); }
		}
	}
	if ($dirty > 0 && $upnp > 0)
	{
		$base = "/runtime/upnpigd/portmapping";
		$igd_stsp = XNODE_getpathbytarget($base, "entry", "externalport", $EXTERNALPORT, 0);
		if ($igd_stsp == "")
		{
			$enable = CheckIGDEntry($EXTERNALPORT, $PROTOCOL, $INTERNALPORT, $LANIP, $APPNAME);
			if($enable != 1)
			InsertIGDEntry($EXTERNALPORT, $PROTOCOL, $INTERNALPORT, $LANIP, $APPNAME);
		}
		else
		{
			echo "echo 'CM and IGD open the same port' > /dev/console\n";
		}
	}
}
else if($ACTION == "REMOVE")
{
	$stsp = XNODE_getpathbytarget("/runtime/orion", "entry", "external_port", $EXTERNALPORT, 0);
	if ($stsp!="") { $dirty++; del($stsp); }
	if ($dirty > 0 && $upnp > 0)
	{
		$base = "/runtime/upnpigd/portmapping";
	    foreach($base."/entry")
	    {
			if(query("externalport") == $EXTERNALPORT && query("internalclient") == $LANIP)
			{
				$igd_stsp = $base."/entry:".$InDeX;
				$index    = $InDeX;
				break;
			}
		}
		if ($igd_stsp != "")
		{
			del($igd_stsp);
			$cnt = query($base."/count");
			$cnt--;
			set($base."/count", $cnt);
			$seqno = query($base."/seqno");
			$seqno++;
			set($base."/seqno", $seqno);

			foreach($base."/entry")
	        {
  	        	if($InDeX >= $index)
	        	set($base."/entry:".$InDeX."/uid", "PORTMAP-".$InDeX);
			}
	  	 }
	}
}
else if($ACTION == "LIST")
{
	
	if( $upnp <= 0 || $upnp == "")
	{
		/* Remote Access port */
		$path = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
		if ($path!="")
		{
			$https_rport = query($path."/https_rport");
			$web = query($path."/web");
	
			if ($https_rport!="")
				echo "echo 'TCP:".$https_rport.",127.0.0.1,443,WebMgt'\n";
			else if ($web!="")
				echo "echo 'TCP:".$web.",127.0.0.1,80,WebMgt'\n";
		}
		foreach("/nat/entry/portforward/entry")
		{
			if (query("enable") == "0") continue;
			$proto = query("protocol");
			$ext_p_start = query("external/start");
			$ext_p_end = query("external/end");
			$int_p_start = query("internal/start");
			$int_hostid = query("internal/hostid");
			$int_inf = query("internal/inf");
			$apnam = query("description");
			$ipaddr = XNODE_get_var($int_inf.".IPADDR");
			$mask   = XNODE_get_var($int_inf.".MASK");
			$lanip = ipv4ip($ipaddr, $mask, $int_hostid);
			$tmp = $ext_p_end - $ext_p_start;
			if ($tmp==0)
			{
				$eport = $ext_p_start;
				$iport = $int_p_start;
			}	
			else
			{
				$eport = $ext_p_start."-".$ext_p_end;
				$int_p_end = $int_p_start + $tmp;
				$iport = $int_p_start."-".$int_p_end;
			}
			if ($proto=="TCP+UDP")
			{
				echo "echo '"."TCP".":".$eport.",".$lanip.",".$iport.",".$apnam."'\n";
				echo "echo '"."UDP".":".$eport.",".$lanip.",".$iport.",".$apnam."'\n";
			}	
			else
			{
				echo "echo '".$proto.":".$eport.",".$lanip.",".$iport.",".$apnam."'\n";
			}
		}	
		foreach("/runtime/orion/entry")
		{
			$proto = query("portocol");
			$eport = query("external_port");
			$iport = query("internal_port");
			$lanip = query("lan_ip");
			$apnam = query("application_name");
			if ($eport=="") continue;
				echo "echo '".$proto.":".$eport.",".$lanip.",".$iport.",".$apnam."'\n";
		}	
	}
	else
	{
		foreach("/runtime/upnpigd/portmapping/entry")
		{
			$proto = query("protocol");
			$eport = query("externalport");
			$iport = query("internalport");
			$lanip = query("internalclient");
			$apnam = query("description");
			if ($eport=="") continue;
				echo "echo '".$proto.":".$eport.",".$lanip.",".$iport.",".$apnam."'\n";
		}
	}	
}

if ($dirty>0)
{
	echo "service IPTPFWD restart\n";
}

echo "exit 1\n";
?>
