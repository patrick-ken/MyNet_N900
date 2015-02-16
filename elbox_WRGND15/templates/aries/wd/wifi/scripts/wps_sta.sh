#!/bin/sh
echo [$0] $1 $2 ... > /dev/console
phpsh /etc/scripts/wps/wps.php PARAM1=$1 PARAM2=$2 MODE=station
exit 0
