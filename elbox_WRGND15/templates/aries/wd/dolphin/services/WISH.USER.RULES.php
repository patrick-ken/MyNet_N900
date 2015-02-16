<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$bwc_profile_name = "BWC-2";
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$WISH_ENABLED = query($bwcp."/enable");
$MEDIA_CLASSIFICATION_ENABLED = query($bwcp."/wishwmc");
$HTTP_CLASSIFICATION_ENABLED = query($bwcp."/wishhttp");
$AUTO_CLASSIFICATION_ENABLED = query($bwcp."/wishauto");

$BRINTERFACE = "br0";
$wish_config = "/var/wish_rules";

fwrite("w", $wish_config, "config 'qos' 'wish'\n");
fwrite("a", $wish_config, "   option 'wish_engine_enabled' ' ".$WISH_ENABLED."'\n");
fwrite("a", $wish_config, "   option 'wish_http_enabled' '".$HTTP_CLASSIFICATION_ENABLED."'\n");
fwrite("a", $wish_config, "   option 'wish_media_enabled' '".$MEDIA_CLASSIFICATION_ENABLED."'\n");
fwrite("a", $wish_config, "   option 'wish_auto_enabled' '".$AUTO_CLASSIFICATION_ENABLED."'\n");

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

/* Add rules into the table
 * wish_rule_00 = <en dis>/<name>/<prio>/<unused>/<unused>/<proto>/<local ip from>/<local ip to>/<local port from>/<local port to>/<remote ip from>/<remote ip to>/<remote port from>/<remote port to>
 */
	startcmd(
		/* Set up the given qos rule */
		'qos_rule_set()\n'.
		'{\n'.
		'	RULE_SPEC=$1\n'.
		'	QOS_RULE_ENABLED=$(echo $RULE_SPEC | cut -d/ -f1 -)\n'.
		'	if [ $QOS_RULE_ENABLED -ne "1" ] ; then\n'.
		'		return 0\n'.
		'	fi\n'.
		'	echo "wish_rule_$2 = $RULE_SPEC" > /dev/console\n'.
		'	echo $RULE_SPEC > /sys/devices/system/ubicom_wish/ubicom_wish0/add_manual_rule\n'.
		'}\n'.
		/* Clear all existing rules */
		'echo 1 > /sys/devices/system/ubicom_wish/ubicom_wish0/clear_manual_rules\n'.
	);

/* Add rules into the table
 * wish_rule_00 = <en dis>/<name>/<prio>/<unused>/<unused>/<proto>/<local ip from>/<local ip to>/<local port from>/<local port to>/<remote ip from>/<remote ip to>/<remote port from>/<remote port to>
 */
	$i = 0;
	foreach($bwcp."/rules/entry")
	{
		$enable = query("enable");
		$description = query("description");

		$bwcf_name = query("bwcf");
		$bwcfp = XNODE_getpathbytarget("/bwc/bwcf2", "entry", "uid", $bwcf_name, 0);
		if($bwcfp == "" ) { continue; }

		$protocol = query($bwcfp."/protocol");
		/* TCP: 6, UDP: 17 */
		if ($protocol == "TCP") { $protocol = 6; }
		else                    { $protocol = 17; }
		$src_startip = query($bwcfp."/ipv4/start");
		$src_endip = query($bwcfp."/ipv4/end");
		$src_startport = query($bwcfp."/port/start");
		$src_endport = query($bwcfp."/port/end");

		$dst_startip = query($bwcfp."/dst/ipv4/start");
		$dst_endip = query($bwcfp."/dst/ipv4/end");
		$dst_startport = query($bwcfp."/dst/port/start");
		$dst_endport = query($bwcfp."/dst/port/end");

		$bwcqd_name = query("bwcqd");
		$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd2", "entry", "uid", $bwcqd_name, 0);
		if($bwcqdp == "" ) { continue; }

		$priority = query($bwcqdp."/priority");
		/* VO: 6, VI: 4, BG: 1, BE: 0 */
		if ($priority == "VO")		{ $priority = "6"; }
		else if ($priority == "VI")	{ $priority = "5"; }
		else if ($priority == "BG")	{ $priority = "1"; }
		else 						{ $priority = "0"; }

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

		startcmd(
			'qos_rule_set '.$enable.'/'.$description.'/'.$priority.'/0/0/'.$protocol.'/'.$src_startip.'/'.$src_endip.'/'.$src_startport.'/'.$src_endport.'/'.$dst_startip.'/'.$dst_endip.'/'.$dst_startport.'/'.$dst_endport.' '.$i.'\n'.
		);
		fwrite("a", $wish_config, "   option 'wish_rule_".$i."' '".$enable."/".$description."/".$priority."/0/0/".$protocol."/".$src_startip."/".$src_endip."/".$src_startport."/".$src_endport."/".$dst_startip."/".$dst_endip."/".$dst_startport."/".$dst_endport."'\n");
		$i++;
	}
	/* Do we need to enable media classification?
	 * If media centre support is enabled then we create rules for that.
	 * Rules are formed as:
	 * <unused>/<unused>/<prio>/<unused>/<unused>/<proto>/<h1 ip from>/<h1 ip to>/<h1 port from>/<h1 port to>/<h2 ip from>/<h2 ip to>/<h2 port from>/<h2 port to>
	 */
	startcmd('if [ '.$MEDIA_CLASSIFICATION_ENABLED.' -eq 1 ] ; then\n');
	startcmd('	qos_rule_set 1/0/4/0/0/17/0.0.0.0/255.255.255.255/5555/5555/0.0.0.0/255.255.255.255/0/65535 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/17/0.0.0.0/255.255.255.255/3390/3390/0.0.0.0/255.255.255.255/0/65535 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/17/0.0.0.0/255.255.255.255/0/65535/0.0.0.0/255.255.255.255/5555/5555 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/17/0.0.0.0/255.255.255.255/0/65535/0.0.0.0/255.255.255.255/3390/3390 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/6/0.0.0.0/255.255.255.255/5555/5555/0.0.0.0/255.255.255.255/0/65535 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/6/0.0.0.0/255.255.255.255/3390/3390/0.0.0.0/255.255.255.255/0/65535 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/6/0.0.0.0/255.255.255.255/0/65535/0.0.0.0/255.255.255.255/5555/5555 '.$i.'\n');
	$i++;
	startcmd('	qos_rule_set 1/0/4/0/0/6/0.0.0.0/255.255.255.255/0/65535/0.0.0.0/255.255.255.255/3390/3390 '.$i.'\n');
	startcmd('fi\n');


error($ret);
?>
