<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$at_process_pid = "/var/run/at_process.pid";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

fwrite("a", $START, "echo Starting NETSTAR AT process ..\n");
fwrite("a", $START, "at_process &\n");


fwrite("a", $STOP, "#!/bin/sh\n");

fwrite("a", $STOP, "if [ -f \"".$at_process_pid."\" ]; then\n");
fwrite("a", $STOP, "echo \"Stop AT process ..\"  > /dev/console\n");
fwrite("a", $STOP, "	pid=`cat \"".$at_process_pid."\"`\n");
fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
fwrite("a", $STOP, "		kill $pid \n");
fwrite("a", $STOP, "	fi\n");
fwrite("a", $STOP, "	rm -f \"".$at_process_pid."\"\n");
fwrite("a", $STOP, "fi\n");

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");

?>

