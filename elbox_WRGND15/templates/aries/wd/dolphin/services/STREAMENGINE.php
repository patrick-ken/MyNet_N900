<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$name = "WAN-1";
$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$bwc_profile_name = query($infp."/bwc");
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$STREAMENGINE_ENABLED = query($bwcp."/enable");

$inet   = query($infp."/inet");
$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$addrtype = query($inetp."/addrtype");
$static = query($inetp."/ipv4/static");

if ($addrtype == "ppp4")
{
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 1);
	$ipaddr = query($stsp."/inet/ppp4/local");
	$ppp4over =  query($inetp."/ppp4/over");
	if ($ppp4over == "eth")
	{
		$WANCONNECTIONTYPE = "pppoe";
	}
	else if ($ppp4over == "pptp")
	{
		$WANCONNECTIONTYPE = "pptp";
	}
	else if ($ppp4over == "l2tp")
	{
		$WANCONNECTIONTYPE = "l2tp";
	}
}
else if ($addrtype == "ipv4")
{
	$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 1);
	$ipaddr = query($stsp."/inet/ipv4/ipaddr");
	if ($static == "1") { $WANCONNECTIONTYPE = "static"; }
	else				{ $WANCONNECTIONTYPE = "dhcp"; }
}

if ($ipaddr == "")	{ $ipaddr = "N/A"; }
$ezsetup = query("/runtime/ezcfg/wan_config/status");
if ($ezsetup == "")  { $ezsetup = "0"; }
startcmd(
	'if [ '.$ipaddr.' = "N/A" ]; then\n'.
	'	echo "StreamEngine: WAN interface is down ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	/* If StreamEngine are disabled then we have nothing to do. */
	'if [ '.$STREAMENGINE_ENABLED.' -ne 1 ]; then\n'.
	'	echo "StreamEngine: StreamEngine disabled ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	/* StreamEngine is enabled fully - do we need to perform a rate estimation? */
	'if [ '.$ezsetup.' -ne 1 ]; then\n'.
	'	service SE start\n'.
	'	service SE.RATE.ESTIMATION start\n'.
	'else\n'.
	'	echo "StreamEngine: ezsetup is running, the rate estimation stop..." > /dev/console\n'.
	'fi\n'.
);
		/* Do not run the v4 connection manager ??that function is now obsolete
		 * and is provided by SE.  Still load the v6 one though.
		 * If the v4 connection manager is loaded it causes connections to bypass SE
		 * and that would explain the lack of shaping.
		 */
startcmd(
	/* Start ipv6 connection manager */
	'service NA.MODULE restart\n'.
);

startcmd(
	/* Enable the QoS classifier subsystem */
	'service SE.MODULE start\n'.
	'service SE.CLASSIFICATION start\n'.
	'service SE.USER.RULES start\n'.
	'service SE.HTTP.RULES start\n'
);

stopcmd(
	'if [ '.$ipaddr.' = "N/A" ]; then\n'.
	'	echo "StreamEngine: WAN interface is down ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	/* If both the NA and StreamEngine are disabled then we have nothing to do. */
	'if [ '.$STREAMENGINE_ENABLED.' -ne 1 ]; then\n'.
	'	echo "StreamEngine StreamEngine disabled ..." > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	/* Is StreamEngine shaper currently running?  If so then stop it. */
	'service SE stop\n'.
	'service SE.RATE.ESTIMATION stop\n'.
	/* Stop connection manager modules */
	/* Is classifier currently running?  If so then stop it. */
	'service SE.MODULE stop\n'.
	'service SE.CLASSIFICATION stop\n'.
	'service SE.USER.RULES stop\n'.
	'service SE.HTTP.RULES stop\n'.
        'service NA.MODULE stop\n'
);

error($ret);
?>
