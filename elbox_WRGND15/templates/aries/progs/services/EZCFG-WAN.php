<?
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("a",$START, "xmldbc -k ezwan\n");
fwrite("a",$START, "sleep 1\n");
fwrite("a",$START, "xmldbc -t \"ezwan:180:xmldbc -s /runtime/ezcfg/wan_config/status 0\"\n");
fwrite("a",$START, "xmldbc -t \"wand:3:service WAN restart\"\n");
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>

