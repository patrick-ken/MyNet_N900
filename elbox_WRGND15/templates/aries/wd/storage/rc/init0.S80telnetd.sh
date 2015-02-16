#!/bin/sh
echo [$0]: $1 ... > /dev/console
TELNET_DISABLE=`devdata get -e telnet_disable`
[ "$TELNET_DISABLE" = "1" ] && exit 0

if [ "$1" = "start" ]; then
	if [ -f "/usr/sbin/login" ]; then
		image_sign=`cat /etc/config/image_sign`
		telnetd -l /usr/sbin/login -u Alphanetworks:$image_sign -i br0 &
	else
		telnetd &
	fi
else
	killall telnetd
fi
