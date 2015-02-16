<? /* vi: set sw=4 ts=4: */
/* Mapping to SDK: se_shaper_set */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

$name = "WAN-1";
$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$bwc_profile_name = query($infp."/bwc");
$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", $bwc_profile_name, 0);
$DYNAMIC_FRAG = query($bwcp."/dynamicfragmentatoin");

$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
$phy = query($stsp."/phyinf");
$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy, 0);
$WAN_MODE_INTERFACE = query($phyp."/name");

$DYNAMIC_FRAG = 1;

$addrtype = query($stsp."/inet/addrtype");
$mtu = query($stsp."/inet/".$addrtype."/mtu");
if ($mtu >= 40)
{
	$mss = $mtu-40;
	$mss1 = $mss+1;
	$iptopt = "-p tcp --tcp-flags SYN,RST,FIN SYN -m tcpmss --mss ".$mss1.":1500 -j TCPMSS --set-mss ".$mss;
}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

/* <dynamic_frag>: 1 to enable dynamic fragmentation, 0 to disable dynanic fragmentation
 * With auto supplied the shaper will be configured from the last rate estimation run, when not supplied
 * <tx_rate_bps>: Rate in bits per seconds of the interface
 * <min_payload_bps>: Minimum size of any packet that we will send out, padding will be applied if necessary
 * <tx_overhead_bits>: Transmit overhead in bits imposed by the network
 * <tx_cell_bits>: On frame relay networks this is the cell overhead in bits
 * NOTE: When auto is specified, if the speed is > 2097152bps (2048Kbps) then shaping is DISABLED
 */
startcmd(
	'MIN_PAYLOAD_BITS=0\n'.
	/* Take configuration from rate estimation sysfs data points */
	'TX_RATE_BPS=$(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_calculated_rate)\n'.
	/* Set the per-packet transmit overhead.  This is the overhead of bit-times per
	 * packet that we don't see at the point of sending the data through the QoS queue
	 * Note that by the time a packet hits the QoS queue it already has an
	 * Ethernet header so we remove that from the calculation here.
	 */
	'TX_OVERHEAD_BITS=`expr $(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_protocol_overhead) - 112`\n'.
	/* If frame relay then switch on cell handling in the QoS queue. */
	'if [ $(cat /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_frame_relay) = 0 ] ; then\n'.
	'	TX_CELL_BITS=0\n'.
	'else\n'.
	'	 TX_CELL_BITS=384\n'.
	'fi\n'.
	'echo "Setting shaper on '.$WAN_MODE_INTERFACE.', Rate $TX_RATE_BPS, Min payload $MIN_PAYLOAD_BITS, Overhead $TX_OVERHEAD_BITS, Cell bits $TX_CELL_BITS" > /dev/console\n'.
	'echo $TX_RATE_BPS > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_tx_rate\n'.
	'echo $MIN_PAYLOAD_BITS > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_min_payload_bits\n'.
	'echo $TX_OVERHEAD_BITS > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_tx_overhead_bits\n'.
	'echo $TX_CELL_BITS > /sys/devices/system/ubicom_streamengine/ubicom_streamengine0/ubicom_streamengine_tx_cell_bits\n'.
	/* What is the default MTU for our internet connection type?
	 * Note that we use WAN_MODE_INTERFACE rather than WANINTERFACE
	 * as we want the MTU of any carrying interface e.g. ppp0.
	 */
	'DEFAULT_MTU=`ifconfig '.$WAN_MODE_INTERFACE.' | grep \'MTU\' | cut -f2 -d\':\' | cut -f1 -d\' \'`\n'.
	/* Prep by removing the DYNFRAG chain from the prerouting chain in the mangle table
	 * NOTE: Redirect output to null beause we may not have these rules and we don't want
	 * to generate errors
	 */
	'iptables -t mangle -D PREROUTING -g DYNFRAG > /dev/null 2> /dev/null\n'.
	/* Now flush and destroy the DYNFRAG table */
	'iptables -t mangle -F DYNFRAG > /dev/null 2> /dev/null\n'.
	'iptables -t mangle -X DYNFRAG > /dev/null 2> /dev/null\n'.
	/* Set up dynamic fragmentaion if required */
	'if [ '.$DYNAMIC_FRAG.' != 1 ]; then\n'.
	'	echo "Dynamic fragmentation: '.$WAN_MODE_INTERFACE.' DISABLED" > /dev/console\n'.
	'	exit 0\n'.
	'fi\n'.
	/* Set up dynamic fragmentation.
	 * Compute the MTU such that the largest datagram will consume a bandwidth
	 * of no more than 20ms for upstream TCP packets
	 */
	'FRAG_SIZE=`expr \\( \\( $TX_RATE_BPS \\* 20 \\) / 8 \\) / 1000`\n'.
	'if [ $TX_RATE_BPS = 0 ] ; then\n'.
	'	FRAG_SIZE=$DEFAULT_MTU\n'.
	'fi\n'.
	/* Fragment size should be a multiple of 8 bytes */
	'QOS_FRAG_SIZE=`expr \\( \\( $FRAG_SIZE + 7 \\) / 8 \\) \\* 8`\n'.
	/* Keep the fragment size within 576 to default mtu */
	'if [ $QOS_FRAG_SIZE -lt \"576\" ] ; then\n'.
	'	QOS_FRAG_SIZE=576\n'.
	'elif [ $QOS_FRAG_SIZE -gt \"$DEFAULT_MTU\" ] ; then\n'.
	'	QOS_FRAG_SIZE=$DEFAULT_MTU\n'.
	'fi\n'.
	/* Get our WAN IP Address for creating the MSS rule. */
	'WANIPADDRESS=`ifconfig '.$WAN_MODE_INTERFACE.' | grep \'inet addr\' | cut -f2 -d\':\' | cut -f1 -d\' \'`\n'.
	/* Create a chain in the mangle table that will allow us to clamp MSS for upstream TCP packets
	 * We create a seperate chain called DYNFRAG because this makes it easier to craft a rule
	 * to remove DYNFRAG from mangle PREROUTING without affecting other rules in there.
	 */
	/*
	'echo "Disable ORIGIN WAN MTU function." > /dev/console\n'.
	'iptables -t mangle -D PREROUTING -i '.$WAN_MODE_INTERFACE.' '.$iptopt.'\n'.
	'iptables -t mangle -D POSTROUTING -o '.$WAN_MODE_INTERFACE.' '.$iptopt.'\n'.
	*/

	'echo "Dynamic fragmentation: '.$WAN_MODE_INTERFACE.' ($WANIPADDRESS) ENABLED, Set to $QOS_FRAG_SIZE (MTU is $DEFAULT_MTU)" > /dev/console\n'.
	'iptables -t mangle -N DYNFRAG\n'.
	'iptables -t mangle -A DYNFRAG -d $WANIPADDRESS -p tcp --tcp-flags SYN,RST SYN -j TCPMSS --set-mss $QOS_FRAG_SIZE\n'.
	'iptables -t mangle -A PREROUTING -g DYNFRAG\n'.
	'iptables -t mangle -L DYNFRAG -n\n'.
	'iptables -t mangle -L PREROUTING -n\n'.
);

error($ret);
?>
