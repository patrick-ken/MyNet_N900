<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: se_manual_rules_set */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$name = "WAN-1";
$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$bwc_profile_name = query($infp."/bwc");
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);

$traffic_shaping = query($bwcp."/trafficshaping");
$auto_uplink = query($bwcp."/autobandwidth");
$qos_uplink = query($bwcp."/bandwidth");
$qos_enable = query($bwcp."/enable");
$auto_classification = query($bwcp."/autoclassification");
$qos_dyn_fragmentation = query($bwcp."/dynamicfragmentation");
$bittorrent_classification = query($bwcp."/autoclassification");

$se_config = "/var/se_rules";

fwrite("w", $se_config, "config 'qos' 'streamengine'\n");
fwrite("a", $se_config, "	option 'traffic_shaping' '".$traffic_shaping."'\n");
fwrite("a", $se_config, "	option 'auto_uplink' '".$auto_uplink."'\n");
fwrite("a", $se_config, "	option 'qos_uplink' '".$qos_uplink."'\n");
fwrite("a", $se_config, "	option 'qos_enable' ' ".$qos_enable."'\n");
fwrite("a", $se_config, "	option 'link_type' '2'\n");
fwrite("a", $se_config, "	option 'auto_classification' '".$auto_classification."'\n");
fwrite("a", $se_config, "	option 'qos_dyn_fragmentation' '".$qos_dyn_fragmentation."'\n");
fwrite("a", $se_config, "	option 'bittorrent_classification' '".$bittorrent_classification."'\n");
fwrite("a", $se_config, "	option 'http_content_classification' '1'\n");
fwrite("a", $se_config, "	option 'user_rules_classification' '1'\n");

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

	/* qos_rule_00 = <en dis>/<name>/<prio>/<unused>/<unused>/<proto>/<local ip from>/<local ip to>/<local port from>/<local port to>/<remote ip from>/<remote ip to>/<remote port from>/<remote port to> */
startcmd(
		'qos_rule_set()\n'.
		'{\n'.
		'	RULE_SPEC=$1\n'.
		'	QOS_RULE_ENABLED=$(echo $RULE_SPEC | cut -d/ -f1 -)\n'.
		'	if [ $QOS_RULE_ENABLED -ne "1" ] ; then\n'.
		'		return 0\n'.
		'	fi\n'.
		'	echo "qos_rule_$2 = $RULE_SPEC" > /dev/console\n'.
		'	echo $RULE_SPEC > /sys/devices/system/streamengine_classifier_user_rules/streamengine_classifier_user_rules0/add_manual_rule\n'.
		'}\n'.
		/* Clear all existing rules */
		'echo 1 > /sys/devices/system/streamengine_classifier_user_rules/streamengine_classifier_user_rules0/clear_manual_rules\n'.
	);

	$i = 0;
	foreach($bwcp."/rules/entry")
	{
		$enable = query("enable");
		$description = query("description");
		
		$bwcf_name = query("bwcf");
		$bwcfp = XNODE_getpathbytarget("/bwc/bwcf1", "entry", "uid", $bwcf_name, 0);
		if($bwcfp == "" ) { continue; }

		$protocol = query($bwcfp."/protocol");
		if ($protocol == "TCP")	{ $protocol = 6; }
		else					{ $protocol = 17; }
		$src_startip = query($bwcfp."/ipv4/start");
		$src_endip = query($bwcfp."/ipv4/end");
		$src_startport = query($bwcfp."/port/start");
		$src_endport = query($bwcfp."/port/end");

		$dst_startip = query($bwcfp."/dst/ipv4/start");
		$dst_endip = query($bwcfp."/dst/ipv4/end");
		$dst_startport = query($bwcfp."/dst/port/start");
		$dst_endport = query($bwcfp."/dst/port/end");
		
		if($src_startip == "" && $src_endip == "")
		{
			$src_startip = "0.0.0.0";
			$src_endip ="255.255.255.255";
		}
		
		if($src_startip != "" && $src_endip == "")
		{
			$src_endip = $src_startip;
		}		
		
		if($src_startport == "" && $src_endport == "")
		{
			$src_startport = "0";
			$src_endport ="65535";
		}	
		
		if($src_startport != "" && $src_endport == "")
		{
			$src_endport = $src_startport;
		}	
		
		if($dst_startip == "" && $dst_endip == "")
		{
			$dst_startip = "0.0.0.0";
			$dst_endip ="255.255.255.255";
		}
		
		if($dst_startip != "" && $dst_endip == "")
		{
			$dst_endip = $dst_startip;
		}		
		
		if($dst_startport == "" && $dst_endport == "")
		{
			$dst_startport = "0";
			$dst_endport ="65535";
		}
		
		if($dst_startport != "" && $dst_endport == "")
		{
			$dst_endport = $dst_startport;
		}

		$bwcqd_name = query("bwcqd");
		$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd1", "entry", "uid", $bwcqd_name, 0);
		if($bwcqdp == "" ) { continue; }

		$priority = query($bwcqdp."/priority");

		startcmd(
			/* Add rules into the table */
			'qos_rule_set '.$enable.'/'.$description.'/'.$priority.'/0/0/'.$protocol.'/'.$src_startip.'/'.$src_endip.'/'.$src_startport.'/'.$src_endport.'/'.$dst_startip.'/'.$dst_endip.'/'.$dst_startport.'/'.$dst_endport.' '.$i.
		);
		fwrite("a", $se_config, "	option 'qos_rule_".$i."' '".$enable."/".$description."/".$priority."/0/0/".$protocol."/".$src_startip."/".$src_endip."/".$src_startport."/".$src_endport."/".$dst_startip."/".$dst_endip."/".$dst_startport."/".$dst_endport."'\n");
		$i++;
	}

error($ret);
?>
