#!/bin/sh
wanidx=`xmldbc -g /device/router/wanindex`
mknod /dev/gpio c 252 0

dev_type=`xmldbc -w /device/layout`

if [ "$dev_type" != "router" ]; then
		gpiod -b &
else
	if [ "$wanidx" != "" ]; then 
		gpiod -w $wanidx &
	else
		gpiod &
	fi
fi
echo $! > /var/run/gpiod.pid

