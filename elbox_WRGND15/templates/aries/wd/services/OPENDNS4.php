<?
fwrite("w", $START, "#!/bin/sh\n");
fwrite("a", $START, "service DNS restart\n");
fwrite("a", $START, "/etc/events/UPDATERESOLV.sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");
fwrite("a", $STOP,  "service DNS restart\n");
?>
