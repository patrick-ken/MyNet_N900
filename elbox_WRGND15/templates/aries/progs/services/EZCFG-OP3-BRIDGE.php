<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
//if (query("/ezcfg/ipaddr")!="")
//{
	//fwrite("a",$START, "xmldbc -t \"ezhttp:2:service INET.LAN-1 restart\"\n");
	$wanp = XNODE_getpathbytarget("/runtime", "inf", "uid", "WAN-1", 0);
	if ($wanp != "")
	{
		$devp = XNODE_getpathbytarget("/runtime", "phyinf", "uid", query($wanp."/phyinf"), 0);
		if ($devp != "")
		{
			fwrite("a",$START, "brctl addif br0 ".query($devp."/name")."\n");
		}
		else
		{
        	fwrite("a",$START, "brctl addif br0 eth0.2\n");
        	fwrite("a",$START, "brctl addif br0 eth0\n");
		}
	}
	else
	{
		fwrite("a",$START, "brctl addif br0 eth0.2\n");
		fwrite("a",$START, "brctl addif br0 eth0\n");
	}
	fwrite("a",$START, "service DHCPS4.LAN-1 stop\n");
	fwrite("a",$START, "/etc/scripts/killpid.sh /var/run/nameresolv-br0.pid\n");
	fwrite("a",$START, "nameresolv  -n  -l  -i br0 -r 'MyNetN600' -r 'WDAP' &\n");
	fwrite("a",$START, "echo $! >  /var/run/nameresolv-br0.pid\n");
//}
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>

