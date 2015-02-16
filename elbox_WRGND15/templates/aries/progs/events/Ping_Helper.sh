#!/bin/sh
echo [$0] ... > /dev/console
COUNTRYCODE=`xmldbc -g /runtime/devdata/countrycode`
if [ "$COUNTRYCODE" = "RU" ]; then
	echo "Check Internet Connection by HTTP request"
	res=`wget -q -O -  http://www.wdc.com/ru | grep "<title>Western"`
	echo $res | grep 'Western Digital' >> /dev/null;
else
	echo "Check Internet Connection by PING request"
	res=`ping www.wdc.com`
	echo $res | grep 'alive' >> /dev/null;
fi
w_status=$?
	
if [ $w_status -eq 0 ]; then
	xmldbc -s /runtime/device/wan_status 1
	xmldbc -s /runtime/device/wan_static_uptime `xmldbc -g /runtime/device/uptime`;
    event WAN-1.CONNECTED;
	echo [$0] success!! > /dev/console
else
	xmldbc -s /runtime/device/wan_status 0
	event INET.DISCONNECTED;
	echo [$0] error!! > /dev/console
fi