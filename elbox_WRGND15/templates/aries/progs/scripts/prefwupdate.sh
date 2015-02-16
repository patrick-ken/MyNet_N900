#!/bin/sh
echo "[$0] ...." > /dev/console
service WAN stop
service INTERNET_PING stop
event STATUS.CRITICAL
# Stop USB services
service FTP stop
service ITUNES stop
service DLNA stop
service SAMBA stop
sleep 3
event HTTP.DOWN add /etc/events/FWUPDATER.sh
service HTTP stop
# Stop LAN
sleep 1
ifconfig br0 down
exit 0
