<?
include "/htdocs/phplib/trace.php";

echo '#!/bin/sh\n';
echo 'echo Stop Parental Control Blocking > /dev/console\n';
echo 'echo Initialize event settings > /dev/console\n';
echo 'event BLOCK.ADD add true\n';
echo 'event BLOCK.DEL add true\n';
echo 'event BLOCK.CHANGE add true\n';
echo 'echo Stop access time control daemon > /dev/console\n';
echo 'killall accesstimectl\n';
echo 'rm /var/run/accesstimectl.pid\n';
echo 'rmmod lib/modules/accesstime_monitor.ko\n';
echo 'echo Flush iptables > /dev/console\n';
echo 'iptables -F PCTL.GUEST\n';
echo 'iptables -F PCTL.SCH\n';
echo 'iptables -F PCTL.DAILY\n';
echo 'ip6tables -F PCTL.SCH\n';
echo 'ip6tables -F PCTL.DAILY\n';
echo 'echo Start Parental Control Blocking > /dev/console\n';
echo 'echo Execute accesstimectl > /dev/console\n';
echo 'accesstimectl -d &\n';
echo 'insmod lib/modules/accesstime_monitor.ko\n';
echo 'sleep 1\n';
if(query("/security/parental/guest/type")== "1")
{
	echo 'event PCTL.START add "usockc /var/accesstimectl PCTL.START_GUEST"\n';
	echo 'event PCTL.START\n';
}
foreach("/security/parental/DeviceList/entry")
{
	$BlockType = query("type");
	$mac       = query("mac");
	if($BlockType=="1")
	{
		echo 'event PCTL.START add "usockc /var/accesstimectl PCTL.START_'.$mac.'_sdule"\n';
		echo 'event PCTL.START\n';
	}
	else if($BlockType=="2")
	{
		echo 'event PCTL.START add "usockc /var/accesstimectl PCTL.START_'.$mac.'_daily"\n';
		echo 'event PCTL.START\n';
	}
	else
	{
		echo 'echo error > /dev/console\n';
	}
}
echo 'exit 0\n';
?>