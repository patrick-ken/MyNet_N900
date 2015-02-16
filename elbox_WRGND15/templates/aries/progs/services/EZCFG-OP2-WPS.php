<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
if (query("/ezcfg/wps/enable_24g")=="1")
{
	del("runtime/wps/setting");
	TRACE_error("uid STATION24G-1.1" );

	//update wifi station with bridge
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
	del($p."/bridge");
	add($p."/bridge/port",  "STATION24G-1.1");
	$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-2", 0);
	del($p."/bridge");

	//close all AP wifi
	foreach ("/phyinf")
	{
    	if (query("type") == "wifi")
    	{
        	set("active", "0");
    	}
	}
	//set STATION active
	$p      = XNODE_getpathbytarget("", "phyinf", "uid", "STATION24G-1.1", 0);
	set($p."/active", "1");
	fwrite("a",$START, "service PHYINF.WIFI restart\n");
	fwrite("a",$START, "xmldbc -t \"wps2:20:event WPSPBC.PUSH\"\n");
}
else if (query("/ezcfg/wps/enable_5g")=="1")
{
	del("runtime/wps/setting");
    TRACE_error("uid STATION5G-1.1" );

    //update wifi station with bridge
    $p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-1", 0);
    del($p."/bridge");
    add($p."/bridge/port", "STATION5G-1.1");
    $p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "ETH-2", 0);
    del($p."/bridge");

    //close all AP wifi
    foreach ("/phyinf")
    {
        if (query("type") == "wifi")
        {
            set("active", "0");
        }
    }
    //set STATION active
    $p      = XNODE_getpathbytarget("", "phyinf", "uid", "STATION5G-1.1", 0);
    set($p."/active", "1");
    fwrite("a",$START, "service PHYINF.WIFI restart\n");
	fwrite("a",$START, "xmldbc -t \"wps2:20:event WPSPBC.PUSH\"\n");
}
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
