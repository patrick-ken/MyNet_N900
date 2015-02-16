#!/bin/sh
echo "[$0] ...." > /dev/console
echo 1 > /var/stop_SE_RATE_ESTIMATION
kill -SIGUSR2 `cat /var/servd/WAN-1-udhcpc.pid`

exit 0
