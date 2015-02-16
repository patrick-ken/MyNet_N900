#!/bin/sh
remove_pid () 
{
	gpiod_pid=`cat /var/run/gpiod.pid`;
	xmldb_pid=`cat /var/run/xmldb_sock_wrgnd14_wd_storage.pid`;
	servd_pid=`cat /var/run/servd.pid`;
	remove_process1=`fuser -m /internalhd/root`;
	remove_process2=`fuser -m /internalhd/etc`;
	remove_process3=`fuser -m /internalhd/tmp`;
	remove_process=`$remove_process1 $remove_process2 $remove_process3`
	echo "disk = $disk"
	echo "remove_process = $remove_process"

	for i in $remove_process; do
		if [ "$i" != "$gpiod_pid" ]; then
			if [ "$i" != "$xmldb_pid" ]; then
				if [ "$i" != "$servd_pid" ]; then
					kill $i;
				fi
			fi
		fi	
	done
}	

if [ -e "/dev/sda1" ]; then
	removable_sda1=`cat /sys/class/block/sda/removable`
	if [ "$removable_sda1" = "0" ]; then
		disk=/dev/sda
	fi
fi
if [ -e "/dev/sdb1" ]; then
	removable_sdb1=`cat /sys/class/block/sdb/removable`
	if [ "$removable_sdb1" = "0" ]; then
		disk=/dev/sdb
	fi
fi	
if [ "$disk" = "" ]; then
	disk=/dev/sda;
fi	
usockc /var/gpio_ctrl ALL_BLINK
mfc fan highspeed
#shut down ethernet interface in software layer.
ifconfig br0 down
ifconfig eth0 down
ifconfig eth1 down
sleep 1
#shut down wifi interface in software layer
service PHYINF.WIFI stop
# Stop USB services
service FTP stop
service ITUNES stop
service NETATALK stop
service DLNA stop
service SAMBA stop
service ORION stop
#umount all disk
xmldbc -P /etc/events/SAFE_REMOVE.php -V REMOVE_ALL="1" > /var/run/SAFE_REMOVE_ALL.sh
sh /var/run/SAFE_REMOVE_ALL.sh
remove_pid
sleep 2
umount /internalhd/root
umount /internalhd/etc
umount /internalhd/tmp
sleep 1
#power off ethernet switch
event ETH.PWROFF
sleep 1
usockc /var/gpio_ctrl ALL_BLINK
#power off sata birdge controller
event SATA.PWROFF
sleep 1
#power off USB port
event USB.PWROFF
sleep 30
service FTP stop
service ITUNES stop
service NETATALK stop
service DLNA stop
service SAMBA stop
service ORION stop
service PHYINF.WIFI stop
service INTERNET_PING stop
usockc /var/gpio_ctrl ALL_BLINK
#
