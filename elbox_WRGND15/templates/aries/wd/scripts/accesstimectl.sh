#!/bin/sh
echo [$0] [$1] [$2] ....

# Called from web/klogd/.., notify accesstimectl daemon.

echo "$1 Device - [$2] ..." > /dev/console
usockc /var/accesstimectl "$1 $2" 

