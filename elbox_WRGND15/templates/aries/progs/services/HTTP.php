<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";//use this because of GuestZone config

fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");

$httpd_conf = "/var/run/httpd.conf";
$httpd_guest_conf = "/var/run/httpd_guest.conf";//GuestZone config file
$name = "LAN-2";//assign GuestZone name
$port = "80";//assign GuestZone WebServer port number
$GZ_enable = 0;//0: Disable GuestZone WebServer. 1: Enable GuestZone WebServer

/* start script */
if ( isdir("/htdocs/widget") == 1) // For widget By Joseph
{
	foreach("/runtime/services/http/server")
	{
		if(query("mode")=="HTTP")
			set("widget",	1);
	}
	fwrite("a",$START, "xmldbc -x /runtime/widget/salt \"get:widget -s\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/widgetv2/logincheck  \"get:widget -a /var/run/password -v\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/time/date \"get:date +%m/%d/%Y\"\n");
	fwrite("a",$START, "xmldbc -x /runtime/time/time \"get:date +%T\"\n");
}
/* detect whether GuestZone WebServer sould be up or down */
$ifname="";
$af="";
$ipaddr="";
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);//xmldbc -w /runtime/inf:2
if ($stsp!="")
{
	$phy = query($stsp."/phyinf");//ETH-2
	if ($phy!="")
	{
		$phyp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", $phy, 0);//xmldbc -w /runtime/phyinf:2
		if ($phyp!="" && query($phyp."/valid")=="1")
		{
			$ifname = query($phyp."/name");//br1
		}
	}
	/* Get address family & IP address */
	$atype	= query($stsp."/inet/addrtype");//ipv4
	if		($atype=="ipv4") {$af="inet"; $ipaddr=query($stsp."/inet/ipv4/ipaddr"); $ipmask=query($stsp."/inet/ipv4/mask");}
	else if	($atype=="ppp4") {$af="inet"; $ipaddr=query($stsp."/inet/ppp4/local"); $ipmask=query($stsp."/inet/ppp4/mask");}
	else if	($atype=="ipv6") {$af="inet6";$ipaddr=query($stsp."/inet/ipv6/ipaddr");}
	else if	($atype=="ppp6") {$af="inet6";$ipaddr=query($stsp."/inet/ppp6/local");}
	
	if ($ifname!=""&& $af!="" && $ipaddr!="")
	{
		$GZ_enable =1;//1: Enable GuestZone WebServer
	}
}

fwrite("a",$START, "xmldbc -P /etc/services/HTTP/httpcfg.php > ".$httpd_conf."\n");
if($GZ_enable==1) {fwrite("a",$START, "xmldbc -P /etc/services/HTTP/httpcfg_GuestZone.php -V ifname=".$ifname." -V ipaddr=".$ipaddr." -V af=".$af." -V inf=".$name." -V port=".$port." > ".$httpd_guest_conf."\n");}
fwrite("a",$START, "event PREFWUPDATE add /etc/scripts/prefwupdate.sh\n");
fwrite("a",$START, "httpd -f ".$httpd_conf."\n");
if($GZ_enable==1) {fwrite("a",$START, "httpd -f ".$httpd_guest_conf."\n");}
fwrite("a",$START, "event HTTP.UP\n");
fwrite("a",$START, "exit 0\n");

/* stop script */
fwrite("a",$STOP, "killall httpd\n");
fwrite("a",$STOP, "rm -f ".$httpd_conf."\n");
if($GZ_enable==1) {fwrite("a",$STOP, "rm -f ".$httpd_guest_conf."\n");}
fwrite("a",$STOP, "event HTTP.DOWN\n");
fwrite("a",$STOP, "exit 0\n");
?>
