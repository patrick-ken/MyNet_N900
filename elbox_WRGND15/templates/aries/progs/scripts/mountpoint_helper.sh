#!/bin/sh 
echo "[$0] $1" > /dev/console
echo `xmldbc -P /etc/scripts/mountpoint_helper.php -V uid=$1`
exit 0
