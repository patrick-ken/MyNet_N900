<?
$stunnel_client_pid = "/var/run/stunnel_client_temp.pid";
$stunnel_client_conf = "/var/etc/stunnel_client_temp.conf";

$PORT = query("/runtime/https/port");
if($PORT == "")
{
	$PORT = "9000";	
}		

$HOST = query("/runtime/https/host");
if($HOST == "")
{
	$HOST = "websupport.wdc.com";	
}		

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

fwrite("a", $START, "#!/bin/sh\n");
fwrite("a", $START, "echo Start Stunnel client service .. \n");
fwrite("a", $START, "xmldbc -P /etc/services/HTTPS/stunnel_client_conf.php -V host=".$HOST." -V port=".$PORT." > ".$stunnel_client_conf." \n");
fwrite("a", $START, "stunnel ".$stunnel_client_conf." & \n");
fwrite("a", $START, "echo $! > ".$stunnel_client_pid."\n");

fwrite("a", $STOP, "#!/bin/sh\n");
fwrite("a", $STOP, "echo \"Stop Stunnel client service ..\"  > /dev/console\n");
fwrite("a", $STOP, "if [ -f \"".$stunnel_client_pid."\" ]; then\n");
fwrite("a", $STOP, "	pid=`cat \"".$stunnel_client_pid."\"`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		kill $pid \n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f \"".$stunnel_client_pid."\"\n");
fwrite("a", $STOP, "	rm -f \"".$stunnel_client_conf."\"\n");
fwrite("a", $STOP, "fi\n");	

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");

?>

