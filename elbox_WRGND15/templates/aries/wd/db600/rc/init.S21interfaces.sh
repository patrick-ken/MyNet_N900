#!/bin/sh

#atheros_driver_init
dev_type=`xmldbc -w /device/layout`

echo "Inserting gpio.ko ..." > /dev/console
insmod /lib/modules/gpio.ko
[ "$?" = "0" ] && mknod /dev/gpio c 101 0 && echo "done."

echo "Inserting athrs_gmac.ko ..." > /dev/console

if [ "$dev_type" == "router" ]; then 
	insmod /lib/modules/athrs_gmac.ko alpha_dev_type=1
else
	insmod /lib/modules/athrs_gmac.ko alpha_dev_type=0
fi


echo "Inserting rebootm.ko ..." > /dev/console
insmod /lib/modules/rebootm.ko
# UNIX 98 pty
mknod -m666 /dev/pts/0 c 136 0
mknod -m666 /dev/pts/1 c 136 1

if test -f "/proc/net/if_inet6" ;
then
	echo 2 > /proc/sys/net/ipv6/conf/eth0/accept_dad
	echo 1 > /proc/sys/net/ipv6/conf/eth0/disable_ipv6
	
	if [ "$dev_type" == "router" ]; then 
		echo 2 > /proc/sys/net/ipv6/conf/eth1/accept_dad
		echo 1 > /proc/sys/net/ipv6/conf/eth1/disable_ipv6
	fi

fi

if [ "$dev_type" == "router" ]; then 

	MACADDR=`devdata get -e wanmac`
	[ "$MACADDR" != "" ] && ip link set eth0 addr $MACADDR

	MACADDR=`devdata get -e lanmac`
	[ "$MACADDR" != "" ] && ip link set eth1 addr $MACADDR
else

	MACADDR=`devdata get -e lanmac`
	[ "$MACADDR" != "" ] && ip link set eth0 addr $MACADDR
fi
