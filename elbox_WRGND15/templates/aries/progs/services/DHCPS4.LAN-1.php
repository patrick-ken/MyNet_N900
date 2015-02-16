<?
include "/etc/services/DHCPS/dhcpserver.php";
fwrite("w",$START,"#!/bin/sh\n");
fwrite("w", $STOP,"#!/bin/sh\n");

//bridge mode no support dhcp server
$layout = query("/device/layout");
if ( $layout == "bridge" )	{ return;	}

dhcps4setup("LAN-1");
?>
