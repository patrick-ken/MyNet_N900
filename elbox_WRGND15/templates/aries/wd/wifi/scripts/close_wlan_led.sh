#!/bin/sh
#check whether all wifi interfaces is down
ALLIF=`ifconfig | grep -i ath | cut -f1 -d' '`

#echo $ALLIF > /dev/console


if [ "$ALLIF" = "" ]; then
	event WLAN.DISCONNECTED
	echo "Close WLAN LED ... ." > /dev/console
	event WPS.NONE
	echo "Close WPS LED ... ." > /dev/console
fi


