<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP,  "#!/bin/sh\n");

$path = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
$stunnel = query($path."/stunnel");
$stunnel_pid = "/var/run/stunnel.pid";

if ($stunnel==1)
{
	fwrite("a", $START, "echo \"Start Stunnel service ..\"  > /dev/console\n");
	fwrite("a", $START, "stunnel &\n");
	fwrite("a", $STOP, "echo \"Stop Stunnel service ..\"  > /dev/console\n");
	fwrite("a", $STOP, "if [ -f \"".$stunnel_pid."\" ]; then\n");
	fwrite("a", $STOP, "	pid=`cat \"".$stunnel_pid."\"`\n");
	fwrite("a", $STOP, "	if [ $pid != 0 ]; then\n");
	fwrite("a", $STOP, "		kill $pid \n");
	fwrite("a", $STOP, "	fi\n");
	fwrite("a", $STOP, "	rm -f \"".$stunnel_pid."\"\n");
	fwrite("a", $STOP, "fi\n");
	
	

	/* Prepare data for http to listen ssl data from stunel. (127.0.0.1:80) */
	$stsp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "STUNNEL", 0);
	$dirty=0;

	if ($stsp=="")
	{
		$dirty++;
		$stsp = XNODE_getpathbytarget("/runtime/services/http", "server", "uid", "STUNNEL", 1);
		set($stsp."/mode",	"STUNNEL");
		set($stsp."/ifname", "dummy");
		set($stsp."/ipaddr","127.0.0.1");
		set($stsp."/port",	80);
		set($stsp."/af",	"inet");
	}
	else
	{
		if (query($stsp."/mode")!="STUNNEL")		{ $dirty++; set($stsp."/mode", "STUNNEL"); }
		if (query($stsp."/ifname")!="dummy")		{ $dirty++; set($stsp."/ifname", "dummy"); }
		if (query($stsp."/ipaddr")!="127.0.0.1")		{ $dirty++; set($stsp."/ipaddr", "127.0.0.1"); }
		if (query($stsp."/port")!= 80)		{ $dirty++; set($stsp."/port", 80); }
		if (query($stsp."/af")!="inet")		{ $dirty++; set($stsp."/af", "inet"); }
	}

	if ($dirty>0) $action="restart"; else $action="start";
	fwrite("a", $START, "service HTTP ".$action);
}
?>
