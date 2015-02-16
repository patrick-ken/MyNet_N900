<?
include "/etc/services/HTTP/httpsvcs.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");
upnpsetup("LAN-3");
/*For boot up sequence is UPNP.LAN-1 then UPNP.LAN-3. When PC or notebook have ipv6 and ipv4 then 
  goto Windows Network Explorer under Network Infrastructure and Storage will show ipv6 address.
  In this situation, we will set a timer to know UPNP.LAN-1 is start or not, if start then restart UPNP.LAN-1 when UPNP.LAN-3 start.
  This is let Network Infrastructure and Storage will show ipv4 address.*/
//2013/02/07  UPNP.LAN-1 don't need to restart.
//fwrite("a",$START,'xmldbc -k UPNP_LAN_timer\n');
//fwrite("a",$START,'xmldbc -t UPNP_LAN_timer:20:"sh etc/events/UPNP_LAN_chk.sh"\n');
fwrite("a",$START,"exit 0\n");
fwrite("a", $STOP,"exit 0\n");
?>
