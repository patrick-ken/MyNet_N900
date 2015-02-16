#!/bin/sh
usockc /var/gpio_ctrl ALL_BLINK
mfc fan highspeed
#shut down ethernet interface in software layer.
ifconfig br0 down
ifconfig eth0 down
ifconfig eth1 down
sleep 1
#shut down wifi interface in software layer
ifconfig ath0 down
ifconfig ath2 down
sleep 1
ifconfig wifi0 down
ifconfig wifi1 down
#remove wifi driver
rmmod /lib/modules/umac.ko
rmmod /lib/modules/ath_dev.ko
rmmod /lib/modules/ath_dfs.ko
rmmod /lib/modules/ath_rate_atheros.ko
rmmod /lib/modules/ath_hal.ko
rmmod /lib/modules/adf.ko
rmmod /lib/modules/asf.ko
#umount all disk
xmldbc -P /etc/events/SAFE_REMOVE.php -V REMOVE_ALL="1" > /var/run/SAFE_REMOVE_ALL.sh
sh /var/run/SAFE_REMOVE_ALL.sh
sleep 1
#power off ethernet switch
event ETH.PWROFF
sleep 1
#power off USB port
event USB.PWROFF
#
