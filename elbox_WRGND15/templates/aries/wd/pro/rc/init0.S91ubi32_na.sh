#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	insmod /lib/modules/ubicom_na_connection_manager_ipv4.ko
	if [ -f "/proc/net/if_inet6" ]; then
		insmod /lib/modules/ubicom_na_connection_manager_ipv6.ko
	fi
else
	rmmod /lib/modules/ubicom_na_connection_manager_ipv4.ko
	if [ -f "/proc/net/if_inet6" ]; then
		rmmod /lib/modules/ubicom_na_connection_manager_ipv6.ko
	fi
fi
