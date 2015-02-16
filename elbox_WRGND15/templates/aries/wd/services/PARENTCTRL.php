<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$lns_process_pid = "/var/run/lns_process.pid";
$ps_process_pid = "/var/run/ps_process.pid";
$at_process_pid = "/var/run/at_process.pid";

$netstar_pid="/var/run/netstar.pid";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

if(query("/security/active")!="0" && query("/security/netstar/enable")=="1")
	$netstar_enable=1;
else
	$netstar_enable=0;
$location=query("/security/netstar/location");
$email=query("/security/netstar/email");

if($netstar_enable == 1 && $location != "")
{
	$mac=query("/runtime/devdata/wanmac");
	$newMac="";
	$num=0;
	while ($num < 6)
	{
		$tmpMac = cut($mac, $num, ":");
     		$newMac = $newMac.$tmpMac;
     		$num++;
	}
	set("/runtime/netstar/wanmac", toupper($newMac));
	set("/runtime/netstar/status", "1");
	
	fwrite("a", $START, "#!/bin/sh\n");
///
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
	fwrite("a", $START, "echo \"Disable Parental control block\" > /dev/console\n");
	fwrite("a", $START, "killall accesstimectl\n");
	fwrite("a", $START, "rm /var/run/accesstimectl.pid\n");
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
	fwrite("a", $START, "echo \"Flush relative iptables\" > /dev/console\n");
	fwrite("a", $START, "iptables -F PCTL.GUEST\n");
	fwrite("a", $START, "iptables -F PCTL.SCH\n");
	fwrite("a", $START, "iptables -F PCTL.DAILY\n");
    fwrite("a", $START, "ip6tables -F PCTL.SCH\n");
    fwrite("a", $START, "ip6tables -F PCTL.DAILY\n");
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
///
	fwrite("a", $START, "echo Start NETSTAR LNS process .. \n");
	fwrite("a", $START, "lns_process &\n");
	
	if($email != "")
	{
		fwrite("a", $START, "echo Starting NETSTAR AT process ..\n");
		fwrite("a", $START, "at_process &\n");
	
		fwrite("a", $START, "echo Starting NETSTAR ... \n");
		fwrite("a", $START, "netstar & \n");
	
		fwrite("a", $START, "echo Start NETSTAR PS process .. \n");
		fwrite("a", $START, "ps_process &\n");
	
		fwrite("a", $START, "service PROXYD start\n");
		fwrite("a", $START, "service IPTPROXYD start\n");
	}
}

fwrite("a", $STOP, "#!/bin/sh\n");

set("/runtime/netstar/status", "0");
//fwrite("a", $STOP, "xmldbc -X /runtime/netstar/user\n");

fwrite("a", $STOP, "if [ -f \"".$ps_process_pid."\" ]; then\n");
fwrite("a", $STOP, "echo \"Stop PS process ..\"  > /dev/console\n");
fwrite("a", $STOP, "	pid=`cat \"".$ps_process_pid."\"`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		kill $pid \n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f \"".$ps_process_pid."\"\n");
fwrite("a", $STOP, "fi\n");

fwrite("a", $STOP, "echo Stoping NETSTAR ... > /dev/console\n");
fwrite("a", $STOP, "killall  netstar \n");
fwrite("a", $STOP, "rm -f ".$netstar_pid."\n");

fwrite("a", $STOP, "if [ -f \"".$at_process_pid."\" ]; then\n");
fwrite("a", $STOP, "echo \"Stop AT process ..\"  > /dev/console\n");
fwrite("a", $STOP, "	pid=`cat \"".$at_process_pid."\"`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		kill $pid \n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f \"".$at_process_pid."\"\n");
fwrite("a", $STOP, "fi\n");

fwrite("a", $STOP, "if [ -f \"".$lns_process_pid."\" ]; then\n");
fwrite("a", $STOP, "echo \"Stop  Start LNS process ..\"  > /dev/console\n");
fwrite("a", $STOP, "	pid=`cat \"".$lns_process_pid."\"`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		kill $pid \n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f \"".$lns_process_pid."\"\n");
fwrite("a", $STOP, "fi\n");	

fwrite("a", $STOP, "service PROXYD stop\n");
fwrite("a", $STOP, "service IPTPROXYD stop\n");

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");

?>

