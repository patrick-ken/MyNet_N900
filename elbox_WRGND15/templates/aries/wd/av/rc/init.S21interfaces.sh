#!/bin/sh

echo "Inserting gpio.ko ..." > /dev/console
insmod /lib/modules/gpio.ko
[ "$?" = "0" ] && mknod /dev/gpio c 101 0 && echo "done."

echo "Inserting athrs_gmac.ko ..." > /dev/console
insmod /lib/modules/athrs_gmac.ko

echo "Inserting rebootm.ko ..." > /dev/console
insmod /lib/modules/rebootm.ko
# UNIX 98 pty
mknod -m666 /dev/pts/0 c 136 0
mknod -m666 /dev/pts/1 c 136 1

if test -f "/proc/net/if_inet6" ;
then
	echo 2 > /proc/sys/net/ipv6/conf/eth0/accept_dad
	echo 1 > /proc/sys/net/ipv6/conf/eth0/disable_ipv6
fi

if [ "$dev_type" == "router" ]; then 
	MACADDR=`devdata get -e wanmac`
else
	MACADDR=`devdata get -e lanmac`
fi

[ "$MACADDR" != "" ] && ip link set eth0 addr $MACADDR
#ip link set eth0 up
#vconfig set_name_type DEV_PLUS_VID_NO_PAD
