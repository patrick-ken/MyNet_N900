#!/bin/sh
echo 1 > /var/stop_SE_RATE_ESTIMATION
sleep 5
if [ -f "/var/stop_SE_RATE_ESTIMATION" ]; then
	rm /var/stop_SE_RATE_ESTIMATION
fi

