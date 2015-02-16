<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: se_do_rate_estimation */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$name = "WAN-1";
$infp   = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$phyinf = query($infp."/phyinf");
$WANINTERFACE = PHYINF_getifname($phyinf);

$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
$phy = query($stsp."/phyinf");
$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy, 0);
$WAN_MODE_INTERFACE = query($phyp."/name");

$inet   = query($infp."/inet");
$inetp = XNODE_getpathbytarget("/inet", "entry", "uid", $inet, 0);
$addrtype = query($inetp."/addrtype");
$static = query($inetp."/ipv4/static");

$bwc_profile_name = query($infp."/bwc");
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);

if ($addrtype == "ppp4")
{
	$ppp4over = query($inetp."/ppp4/over");
	if ($ppp4over == "eth") { $WANCONNECTIONTYPE = "pppoe"; }
	else if ($ppp4over == "l2tp") { $WANCONNECTIONTYPE = "l2tp"; }
	else if ($ppp4over == "pptp") { $WANCONNECTIONTYPE = "pptp"; }		
}
else if ($addrtype == "ipv4")
{
	if ($static == "1")	{ $WANCONNECTIONTYPE = "static"; }
	else				{ $WANCONNECTIONTYPE = "dhcp"; }
}

/* <SPEED_CMD>: Known speed in bps or 0 for Auto-Detect
 * <LINK_CMD>: 0 for No frame relay, 1 for Have frame relay, 2 for Auto-Detect
 */
$AUTO_SPEED = query($bwcp."/autobandwidth");
$SPEED = query($bwcp."/bandwidth");
$LINK_CMD = query($bwcp."/linktype");
$HOP_OFFSET = 0;

if ($SPEED == "")
{
	$SPEED = "512";
}

if ($LINK_CMD == "")
{
	$LINK_CMD = "2";
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

startcmd(
	/* Hop Offset */
	'if [ -n "'.$HOP_OFFSET.'" ] ; then\n'.
	'	echo $HOP_OFFSET > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_hop_offset\n'.
	'fi\n'.
	/* Manual or automatic speed? */
	'RET=0\n'.
	'SPEED=0\n'.
	'SPEED_CMD=0\n'.
	'if [ '.$AUTO_SPEED.' -eq 0 ]; then\n'.
	/* Manual speed */
	'	SPEED=`expr \\( '.$SPEED.' \\* 1000 \\)`\n'.
	'	echo "Manual speed = $SPEED" > /dev/console\n'.
	'fi\n'.
	/* Using the WANCONNECTIONTYPE identify the protocol overhead */
	'case '.$WANCONNECTIONTYPE.' in\n'.
	'	static )\n'.
	/* Ethernet IFG, Ethernet preamble, Ethernet header and Ethernet CRC. */
	'		WAN_PROTOCOL_OVERHEAD=`expr 96 + 64 + 112 + 32`\n'.
	'		;;\n'.
	'	dhcp )\n'.
	/* Ethernet IFG, Ethernet preamble, Ethernet header and Ethernet CRC. */
	'		WAN_PROTOCOL_OVERHEAD=`expr 96 + 64 + 112 + 32`\n'.
	'		;;\n'.
	'	pppoe )\n'.
	/* There are many different types of framing overhead that we may need to account
	 * for here so sadly we have to pick a worst-case in the absence of any better
	 * information.  The worst case is PPPoEoA with Ethernet header including SNAP/LLC,
	 * PPPoE header, Ethernet CRC and AAL5 framing.
	 */
	'		WAN_PROTOCOL_OVERHEAD=`expr 160 + 64 + 32 + 64`\n'.
	'		;;\n'.
    '   pptp )\n'.   
	/* Ethernet IFG, Ethernet preamble, Ethernet header, PPP header IP header,
	 * and Ethernet CRC.
	 */ 
	'		WAN_PROTOCOL_OVERHEAD=`expr 96 + 64 + 112 + 160 + 64 + 32`'.
	' 	    ;;\n'. 
	'   l2tp )\n'.
	/* Ethernet IFG, Ethernet preamble, Ethernet header, PPP header IP header,                                                       * and Ethernet CRC.
	 */ 
	'		WAN_PROTOCOL_OVERHEAD=`expr 96 + 64 + 112 + 160 + 64 + 32`'.
	'		;;\n'.
	'	* )\n'.
	'		echo "Unknow WAN connection type '.$WANCONNECTIONTYPE.'" > /dev/console\n'.
	'		RET=1\n'.
	'		;;\n'.
	'esac\n'.
	'if [ '.$AUTO_SPEED.' = \"1\" ]; then\n'.
	/* Automatic speed */
	'	SPEED_CMD=1\n'.
	'	SPEED=0\n'.
	'else\n'.
	/* Manual speed */
	'	SPEED_CMD=0\n'.
	'	SPEED=$SPEED\n'.
	'fi\n'.
	'FRAME_RELAY_CMD='.$LINK_CMD.'\n'.
	/* Be sure, we got in IP address */
	'MAX_TRY=120\n'.
	'TRY_COUNT=1\n'.
	'while ! ifconfig '.$WAN_MODE_INTERFACE.' | grep -v inet6 | grep inet > /dev/null; do\n'.
	'	sleep 1\n'.
	'	TRY_COUNT=$($TRY_COUNT + 1)\n'.
	'	if [ $TRY_COUNT -gt $MAX_TRY ]; then break; fi\n'.
	'done\n'.
	'if [ $TRY_COUNT -gt $MAX_TRY ]; then\n'.
	'	RET=1\n'.
	'fi\n'.
	'echo "Starting Rate Estimation and Line Type Detection on '.$WANINTERFACE.', Interface type '.$WANCONNECTIONTYPE.' \('.$WAN_MODE_INTERFACE.'\), Protocol overhead $WAN_PROTOCOL_OVERHEAD, Speed command $SPEED_CMD, Frame relay command $FRAME_RELAY_CMD" > /dev/console\n'.
	/* Configure the rate estimation engined*/
	'echo $SPEED > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_calculated_rate\n'.
	'echo $WAN_PROTOCOL_OVERHEAD > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_protocol_overhead\n'.
	'echo $SPEED_CMD > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_speed_cmd\n'.
	'echo $FRAME_RELAY_CMD > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_fr_cmd\n'.
	/* Stop packets routing so that the rate estimation is affected as little as possible */
	'iptables -t filter -I FORWARD 1 --out-interface '.$WAN_MODE_INTERFACE.' -j DROP\n'.
	'iptables -t filter -I FORWARD 1 --out-interface '.$WANINTERFACE.' -j DROP\n'.
	/* Begin estimation */
	'echo 1 > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_start_estimation\n'.
	/* Wait until the analysis is completed */
	'RUNNING=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_start_estimation)\n'.
	'until [ $RUNNING -lt 1 ]; do\n'.
	'	if [ -f \"/var/stop_SE_RATE_ESTIMATION\" ]; then'.
	'		break\n'.	
	'	fi\n'.	
	'	sleep 2\n'.
	'	echo -n .\n'.
	'	RUNNING=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_start_estimation)\n'.
	'done\n'.
	'rm /var/stop_SE_RATE_ESTIMATION\n'.
	/* Restore the routing of packets */
	'iptables -t filter -D FORWARD --out-interface '.$WAN_MODE_INTERFACE.' -j DROP\n'.
	'iptables -t filter -D FORWARD --out-interface '.$WANINTERFACE.' -j DROP\n'.
	/* Extract the results */
	
	'RATE=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_calculated_rate)\n'.
	'FRAME_RELAY=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_frame_relay)\n'.
	'PROTOCOL_OVERHEAD=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_protocol_overhead)\n'.
	'echo "DONE Result=$RUNNING Rate=$RATE Protocol Overhead=$PROTOCOL_OVERHEAD Frame Relay=$FRAME_RELAY" > /dev/console\n'.
	/* To save upstream rate to xmldb */
	'if [ '.$AUTO_SPEED.' = \"1\" -a $RATE -ge 0 ]; then\n'.
	'	RATE=`expr \\( $RATE \\/ 1000 \\)`\n'.
	'	xmldbc -s /bwc/entry:1/bandwidth $RATE\n'.
	'	xmldbc -s /runtime/inf:4/auto_detect_bw $RATE\n'.
	'fi\n'.
	/* If something went wrong with the rating then we unload the shaper */
	'if [ $RUNNING -lt 0 ] ; then\n'.
	'	RET=1\n'.
	'fi\n'.
	'if [ $RET -eq 1 ] ; then\n'.
	'	echo "Rate estimation failed - streamengine shaper will not start" > /dev/console\n'.
	'	service SE stop\n'.
	/* STREAMENGINE_SHAPER_LOADED=0 */
	'	xmldbc -s /runtime/bwc/shaperload 0\n'.
	'else\n'.
	/* If the detected speed is > 2000000 (2mbps) then we disable the shaper.
	 * - we assume there is enough upstream bandwidth so as not to need a shaper
	 */
	'	TX_RATE_BPS=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_calculated_rate)\n'.
	'	if [ $TX_RATE_BPS -gt \"2000000\" ] ; then\n'.
	'		echo Streamengine shaper disabled: WAN speed greater than 2mbps detected\n'.
	'		service SE stop\n'.
	/* STREAMENGINE_SHAPER_LOADED=0 */
	'		xmldbc -s /runtime/bwc/shaperload 0\n'.
	'	else\n'.
	/* Enable shaper with optional dynamic fragmentation */
	'		service SE.SHAPER.SET restart\n'.
	'	fi\n'.
	'fi\n'
);

error($ret);
?>
