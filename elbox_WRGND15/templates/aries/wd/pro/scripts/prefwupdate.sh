#!/bin/sh
echo "[$0] ...." > /dev/console
echo 1 > /var/stop_SE_RATE_ESTIMATION
echo 1 > /var/stop_SE_unload
service WAN stop
service INTERNET_PING stop
event STATUS.CRITICAL
# Stop USB services
service FTP stop
service ITUNES stop
service DLNA stop
service SAMBA stop
sleep 3

echo 0 > /proc/sys/vm/panic_on_oom
killall watchdog

event HTTP.DOWN add /etc/events/FWUPDATER.sh
service HTTP stop
#Stop LAN
sleep 1
ifconfig br0 down
exit 0
