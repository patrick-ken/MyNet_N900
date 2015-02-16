<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
if (query("/ezcfg/qos")=="1")
{
	fwrite("a",$START, "service STREAMENGINE restart\n");
}
else
{
	fwrite("a",$START, "sh /etc/scripts/stop_SE_RATE_ESTIMATION.sh\n");
	fwrite("a",$START, "service STREAMENGINE stop\n");
}
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>

