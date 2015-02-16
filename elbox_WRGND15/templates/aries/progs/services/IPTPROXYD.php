<?
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

$netstar_enable=query("/security/netstar/enable");

if($netstar_enable == 1)
{
	fwrite("a",$START, "iptables -t nat -A PRE.PROXYD -p tcp -j REDIRECT --to-ports 5449\n");
	fwrite("a",$START, "iptables -t nat -A PRE.PROXYD_GUEST -p tcp -j REDIRECT --to-ports 5450\n");
}

fwrite("a", $STOP, "iptables -t nat -F PRE.PROXYD\n");
fwrite("a", $STOP, "iptables -t nat -F PRE.PROXYD_GUEST\n");

fwrite("a", $START, "exit 0\n");
fwrite("a", $STOP,  "exit 0\n");
?>
