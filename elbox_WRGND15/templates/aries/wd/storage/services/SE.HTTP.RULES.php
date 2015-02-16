<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: se_http_content_rules_set */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$bwc_profile_name = "BWC-3";
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);

$se_config = "/var/se_rules";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

startcmd(
		/* http_content_rule_00 = <en dis>/<name>/<prio>/<major>/<minor> */
		'qos_rule_set()\n'.
		'{\n'.
		'	RULE_SPEC=$1\n'.
		'	QOS_RULE_ENABLED=$(echo $RULE_SPEC | cut -d/ -f1 -)\n'.
		'	if [ $QOS_RULE_ENABLED -ne "1" ] ; then\n'.
		'		return 0\n'.
		'	fi\n'.
		'	echo "http_content_rule_$2 = $RULE_SPEC" > /dev/console\n'.
		'	echo -n $RULE_SPEC > /sys/devices/system/streamengine_classifier_http_content/streamengine_classifier_http_content0/add_http_type\n'.
		'}\n'.
		/* Clear all existing rules */
	  'echo 1 > /sys/devices/system/streamengine_classifier_http_content/streamengine_classifier_http_content0/clear_http_types\n'.
	);

	$i = 0;
	foreach($bwcp."/rules/entry")
	{
		$enable = query("enable");
		$description = query("description");
		
		$bwcqd_name = query("bwcqd");
		$bwcqdp = XNODE_getpathbytarget("/bwc/bwcqd3", "entry", "uid", $bwcqd_name, 0);
		if($bwcqdp == "" ) { continue; }

		$priority = query($bwcqdp."/priority");
		$major = query($bwcqdp."/major");
		$minor = query($bwcqdp."/minor");

		startcmd(
			/* Add rules into the table */
			'qos_rule_set '.$enable.'/'.$description.'/'.$priority.'/'.$major.'/'.$minor.' '.$i.
			);
		
		fwrite("a", $se_config, "	option 'http_content_rule_".$i."' '".$enable."/".$description."/".$priority."/".$major."/".$minor."'\n");
		$i++;
	}
error($ret);
?>
