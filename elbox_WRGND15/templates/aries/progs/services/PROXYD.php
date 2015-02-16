<?
include "/htdocs/phplib/trace.php";

$proxyd_pid="/var/run/proxyd.pid";
if($config_file=="") { $config_file="/var/etc/proxyd.conf"; }

if($config_guest_file=="") { $config_guest_file="/var/etc/proxyd_guest.conf"; }

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$netstar_enable=query("/security/netstar/enable");

if($netstar_enable == 1)
{
	// create start script file
	fwrite("a",$START, "echo Starting PROXYD ... > /dev/console\n");
	fwrite("a",$START, "xmldbc -P /etc/services/PROXYD/proxyd_conf.php > ".$config_file."\n");
	fwrite("a",$START, "proxyd -f ".$config_file." & > /dev/console\n");
	
	fwrite("a",$START, "echo Starting PROXYD Guest ... > /dev/console\n");
	fwrite("a",$START, "xmldbc -P /etc/services/PROXYD/proxyd_guest_conf.php > ".$config_guest_file."\n");
	fwrite("a",$START, "proxyd -f ".$config_guest_file." & > /dev/console\n");
}

// create stop script file
fwrite("a", $STOP, "#!/bin/sh\n");
fwrite("a", $STOP, "echo Stoping PROXYD ... > /dev/console\n");
fwrite("a", $STOP, "if [ -f ".$proxyd_pid." ]; then\n");
fwrite("a", $STOP, "	pid=`cat ".$proxyd_pid."`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		killall proxyd > /dev/null 2>&1\n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f ".$proxyd_pid."\n");
fwrite("a", $STOP, "fi\n");
?>
