#!/bin/sh
usockc /var/gpio_ctrl ALL_BLINK
mfc fan highspeed
#shut down ethernet interface in software layer.
ifconfig br0 down
ifconfig eth0 down
ifconfig eth1 down
sleep 1
#shut down wifi interface and remove dirver
service PHYINF.WIFI stop
# Stop USB services
service FTP stop
service ITUNES stop
service NETATALK stop
service DLNA stop
service SAMBA stop
#umount all disk
xmldbc -P /etc/events/SAFE_REMOVE.php -V REMOVE_ALL="1" > /var/run/SAFE_REMOVE_ALL.sh
sh /var/run/SAFE_REMOVE_ALL.sh
sleep 1
#power off ethernet switch
event ETH.PWROFF
sleep 1
usockc /var/gpio_ctrl ALL_BLINK
#power off USB port
event USB.PWROFF
# Stop services again to prevent some process are still looping run.
sleep 40
service FTP stop
service ITUNES stop
service NETATALK stop
service DLNA stop
service SAMBA stop
service PHYINF.WIFI stop
service INTERNET_PING stop
usockc /var/gpio_ctrl ALL_BLINK
