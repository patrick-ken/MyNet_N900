#!/bin/sh

dev_type=`xmldbc -w /device/layout`

if [ "$dev_type" == "router" ]; then

	echo [$0]: $1 ... > /dev/console
	if [ "$1" = "start" ]; then
	event WAN.DETECT	add "/etc/events/WAN-DETECT.sh WAN-1"
	event PPP.DISCOVER	add "sh /etc/events/WAN_ppp_dis.sh WAN-1 START"
	fi

fi
