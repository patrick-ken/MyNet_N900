<?
include "/etc/services/INET/interface.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$layout = query("/device/layout");
if ( $layout == "bridge" )	
{	
	fwrite("a",$START, "service INET.LAN-1 stop\n");
	ifsetup("BRIDGE-1");
}

?>
