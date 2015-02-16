#!/bin/sh
echo "Reboot in 3 seconds ..."
sleep 1
echo "Reboot in 2 seconds ..."
sleep 1
echo "Reboot in 1 seconds ..."
sleep 1
echo "Rebooting ..."

if [ "`xmldbc -g /runtime/device/layout`" != "router" ]; then
	sh /var/servd/ENLAN_stop.sh
	echo "Disable Lan ethernet power ..."
	reboot	
else
	xmldbc -P /etc/events/SAFE_REMOVE.php -V REMOVE_ALL="1" > /var/run/SAFE_REMOVE_ALL.sh
	sh /var/run/SAFE_REMOVE_ALL.sh  > /dev/console
	event WAN-1.DOWN add "sh /var/servd/ENLAN_stop.sh;reboot"
	event STATUS.CRITICAL
	killall radvd
	service INET.WAN-2 stop
	service INET.WAN-1 stop
	xmldbc -t "reboot:10:reboot"
fi
