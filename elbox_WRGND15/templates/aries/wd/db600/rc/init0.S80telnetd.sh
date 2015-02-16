#!/bin/sh
echo [$0]: $1 ... > /dev/console
# Don't remove this, it will close telnet on some older h/w board(1A1-1.01.04) that doesn't close telnet.
TELNET_DISABLE=`devdata get -e telnet_disable`
if [ "$TELNET_DISABLE" = "" ]; then
	devdata set -e telnet_disable=1
fi

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
