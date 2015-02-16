<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("a",$START, "/etc/scripts/lan_port.sh start\n");

fwrite("a",$START, "sleep 4\n");
fwrite("a",$START, "event IPV6ENABLE\n");

fwrite("w",$STOP, "#!/bin/sh\n");
fwrite("a",$STOP, "/etc/scripts/lan_port.sh stop\n");
?>
