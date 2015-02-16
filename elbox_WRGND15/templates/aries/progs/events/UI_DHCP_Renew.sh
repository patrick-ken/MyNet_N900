#!/bin/sh
echo "[$0] ...." > /dev/console
rm /var/stop_SE_RATE_ESTIMATION
kill -SIGUSR1 `cat /var/servd/WAN-1-udhcpc.pid`

exit 0
