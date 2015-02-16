<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("a",$START, "service PHYINF.WIFI restart\n");
fwrite("a",$STOP, "service PHYINF.WIFI stop\n");
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>

