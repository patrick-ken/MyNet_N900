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
service NETATALK stop
service DLNA stop
service SAMBA stop
service ORION stop

ps
## For enough time to stop service
sleep 10
echo "Check PS after"
ps

echo 3 > /proc/sys/vm/drop_caches
echo 0 > /proc/sys/vm/panic_on_oom
killall watchdog

event HTTP.DOWN add /etc/events/FWUPDATER.sh
service HTTP stop
#Stop LAN
sleep 1
ifconfig br0 down
exit 0
