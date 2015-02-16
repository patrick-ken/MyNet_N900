<?
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}

/**************************************************************************/

function ipaddr_hex($v4addr)
{
	$a = dec2strf("%02x", cut($v4addr,0,'.'));
	$b = dec2strf("%02x", cut($v4addr,1,'.'));
	$c = dec2strf("%02x", cut($v4addr,2,'.'));
	$d = dec2strf("%02x", cut($v4addr,3,'.'));
	return $a.$b.$c.$d;
}

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");
/* In router mode, restart ralink hw_nat */
if (query("/device/layout")=="router")
{
    /* $active_hw_nat=1;*/
	$active_hw_nat=query("/device/hw_nat");
	/* If have WFQ or SPQ enable, then doesn't run hw_nat */
	foreach ("/bwc/entry")
	{
		if (query("enable") == 1 && query("uid") != "" )
		{
			if (query("flag") == "TC_WFQ" || query("flag") == "TC_SPQ" )
			{
				$active_hw_nat =0;
			}
		}
	}

	/* only enable hw_nat at wan static or dhcp mode */
	$wan1 = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
	$wan1_inet_uid = query($wan1."/inet");
	$wan1_inet = XNODE_getpathbytarget("/inet", "entry", "uid", $wan1_inet_uid, 0);
	$wan1_addrtype = query($wan1_inet."/addrtype");

	/* wan ip */
	$rt_wan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
	$rt_wan1_ip = query($rt_wan1."/inet/ipv4/ipaddr");
	if ($rt_wan1_ip!="")
	{
		$rt_wan1_ip_hex = ipaddr_hex($rt_wan1_ip);
	}

	/* lan ip */
	$rt_lan1 = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0);
	$rt_lan1_ip = query($rt_lan1."/inet/ipv4/ipaddr");
	if ($rt_lan1_ip!="")
	{
		$rt_lan1_ip_hex = ipaddr_hex($rt_lan1_ip);
	}

	$hw_nat_mask = 20;
	$wan1_net_id = ipv4networkid($rt_wan1_ip, $hw_nat_mask);
	$lan1_net_id = ipv4networkid($rt_lan1_ip, $hw_nat_mask);
	if ($wan1_net_id == $lan1_net_id)
	{
		startcmd("echo \"HW_NAT wan and lan ip conflict!!!\"");
		$hw_nat_ip_conflict = 1;
	}
	else
	{
		$hw_nat_ip_conflict = 0;
	}

	if ($active_hw_nat == 1 && $wan1_addrtype == "ipv4" && $hw_nat_ip_conflict == 0)
	{
	    $active_hw_nat = 1;
	}
	else
	{
	    $active_hw_nat = 0;
	}
	
	/* Enable hw_nat */
	if ($active_hw_nat == 1 )
	{
		/* disable software nat */
		startcmd("echo \"stop fastnat\"");
		startcmd("echo 0 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat");
		/* enable HNAT */
		startcmd("echo \"start HNAT\"");
		startcmd("echo 1 > /proc/sys/net/netfilter/nf_hnat");
		startcmd("echo 1 > /proc/sys/net/netfilter/nf_athrs17_hnat");
		startcmd("ath_switch_cli nat natstatus set enable 1>/dev/null");
		startcmd("ath_switch_cli nat naptstatus set enable 1>/dev/null");
		startcmd("echo ".$rt_lan1_ip_hex." > /proc/sys/net/netfilter/nf_athrs17_hnat_lan_ip");
		startcmd("echo ".$rt_wan1_ip_hex." > /proc/sys/net/netfilter/nf_athrs17_hnat_wan_ip");

		/* enable software nat */
    	stopcmd("echo \"start fastnat\"");
		stopcmd("echo 1 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat");
		/* disable HNAT */
		stopcmd("echo \"stop HNAT\"");
		stopcmd("echo 0 > /proc/sys/net/netfilter/nf_hnat");
		stopcmd("echo 0 > /proc/sys/net/netfilter/nf_athrs17_hnat");
		stopcmd("ath_switch_cli nat natstatus set disable 1>/dev/null");
		stopcmd("ath_switch_cli nat naptstatus set disable 1>/dev/null");
    }
	else 
	{
//		if (query("/runtime/qos_run") != "0")
//		{
			/* enable software nat */
			startcmd("echo \"start fastnat\"");
			startcmd("echo 1 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat");
			/* disable HNAT */
			startcmd("echo \"stop HNAT\"");
			startcmd("echo 0 > /proc/sys/net/netfilter/nf_hnat");
			startcmd("echo 0 > /proc/sys/net/netfilter/nf_athrs17_hnat");
			startcmd("ath_switch_cli nat natstatus set disable 1>/dev/null");
			startcmd("ath_switch_cli nat naptstatus set disable 1>/dev/null");
//		}
//		else
//		{
//			/* disable software nat */
//			startcmd("echo \"stop fastnat\"");
//			startcmd("echo 0 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat");
//			/* enable HNAT */
//			startcmd("echo \"start HNAT\"");
//			startcmd("echo 1 > /proc/sys/net/netfilter/nf_hnat");
//			startcmd("echo 1 > /proc/sys/net/netfilter/nf_athrs17_hnat");
//			startcmd("ath_switch_cli nat natstatus set enable 1>/dev/null");
//			startcmd("ath_switch_cli nat naptstatus set enable 1>/dev/null");
//			startcmd("echo ".$rt_lan1_ip_hex." > /proc/sys/net/netfilter/nf_athrs17_hnat_lan_ip");
//			startcmd("echo ".$rt_wan1_ip_hex." > /proc/sys/net/netfilter/nf_athrs17_hnat_wan_ip");
//
//			/* enable software nat */
//			stopcmd("echo \"start fastnat\"");
//			stopcmd("echo 1 > /proc/sys/net/ipv4/netfilter/ip_conntrack_fastnat");
//			/* disable HNAT */
//			stopcmd("echo \"stop HNAT\"");
//			stopcmd("echo 0 > /proc/sys/net/netfilter/nf_hnat");
//			stopcmd("echo 0 > /proc/sys/net/netfilter/nf_athrs17_hnat");
//			stopcmd("ath_switch_cli nat natstatus set disable 1>/dev/null");
//			stopcmd("ath_switch_cli nat naptstatus set disable 1>/dev/null");
//		}
   	}
}
?>
