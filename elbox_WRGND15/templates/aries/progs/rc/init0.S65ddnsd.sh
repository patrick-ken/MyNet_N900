#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	dev_type=`xmldbc -w /device/layout`
	
	if [ "$dev_type" == "router" ]; then
		ddnsd &
	fi
else
	killall ddnsd
fi
