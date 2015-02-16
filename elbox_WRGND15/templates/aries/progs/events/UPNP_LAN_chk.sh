#!/bin/sh
filename1="/var/servd/UPNP.LAN-1_start.sh"
filename2="/var/servd/UPNP.LAN-1_stop.sh"
if [ -f $filename1 ] && [ -f $filename2 ]; then
	service UPNP.LAN-1 restart
fi
