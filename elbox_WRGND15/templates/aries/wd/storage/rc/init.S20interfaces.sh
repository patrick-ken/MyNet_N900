#!/bin/sh

insmod /lib/modules/ubi32_wdt.ko

watchdog -t 15 /dev/watchdog

if test -f "/proc/net/if_inet6" ;
then
	echo 2 > /proc/sys/net/ipv6/conf/eth0/accept_dad
	echo 1 > /proc/sys/net/ipv6/conf/eth0/disable_ipv6
	echo 2 > /proc/sys/net/ipv6/conf/eth1/accept_dad
	echo 1 > /proc/sys/net/ipv6/conf/eth1/disable_ipv6
fi
