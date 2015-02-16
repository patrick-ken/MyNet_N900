#!/bin/sh

xmldbc -s /runtime/device/wan_status 0

START_UPTIME=`xmldbc -g /runtime/device/uptime`
UPTIME=`xmldbc -g /runtime/device/uptime`

if [ $UPTIME -ge $START_UPTIME ]; then
        TEST_TIME=`expr $UPTIME - $START_UPTIME`
else
		START_UPTIME=$UPTIME
		TEST_TIME=0
fi

WAN_STATUS=`xmldbc -g /runtime/device/wan_status`
FINISH_PING=0
if [ -z $WAN_STATUS ]; then
   	WAN_STATUS=0
fi

#echo WAN_STATUS=$WAN_STATUS
#echo TEST_TIME=$TEST_TIME
#echo START_UPTIME=$START_UPTIME


while [ $TEST_TIME -le 180 -a $WAN_STATUS -ne 1 -a  $FINISH_PING -ne 1 ];do
#	echo WAN_STATUS=$WAN_STATUS
#	echo TEST_TIME=$TEST_TIME
#	echo START_UPTIME=$START_UPTIME
	sh /etc/events/Ping_Helper.sh
	WAN_STATUS=`xmldbc -g /runtime/device/wan_status`
	if [ -z $WAN_STATUS ]; then
   		WAN_STATUS=0
	fi
	if [ $WAN_STATUS -eq 1 ]; then
        FINISH_PING=1
	else
		sleep 5
		UPTIME=`xmldbc -g /runtime/device/uptime`
		if [ $UPTIME -ge $START_UPTIME ]; then
        	TEST_TIME=`expr $UPTIME - $START_UPTIME`
		else
			START_UPTIME=$UPTIME
			TEST_TIME=0
		fi
	fi
done

	

while [ 1 -eq 1 ];do
#	echo \"SLEEP 1800 seconds\"
#	remember sleep process pid so that when we don't need process, 
#	we can delete process by pid.
	if [ ! -f "/var/tmp/internet_ping_sleep.pid" ]; then
		sleep 1800
		echo $! > /var/tmp/internet_ping_sleep.pid
	fi
	WAN_STATUS=`xmldbc -g /runtime/device/wan_status`
	if [ -z $WAN_STATUS ]; then
   		WAN_STATUS=0
	fi
	if [ $WAN_STATUS -ne 1 ]; then
        sh /etc/events/Ping_Helper.sh
	fi
	sh etc/scripts/killpid.sh /var/tmp/internet_ping_sleep.pid
done

exit 0
