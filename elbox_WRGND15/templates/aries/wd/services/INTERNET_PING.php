<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

fwrite("a",$START, "/etc/scripts/internet_ping.sh &\n");
fwrite("a",$START, "echo $! > /var/tmp/internet_ping.pid\n");
fwrite("a",$START, "cat /var/tmp/internet_ping.pid\n");
fwrite("a",$START, "exit 0\n");

fwrite("a",$STOP,  "/etc/scripts/killpid.sh /var/tmp/internet_ping.pid\n");
fwrite("a",$STOP,  "/etc/scripts/killpid.sh /var/tmp/internet_ping_sleep.pid\n");
fwrite("a",$STOP,  "exit 0\n");
?>
