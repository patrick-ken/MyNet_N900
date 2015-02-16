#!/bin/sh

echo [$0] $1 $2 ... > /dev/console

xmldbc -P /etc/events/WD-AUTO-UNLOCK.php -V WD_DEVICE=$1 > /var/run/WD-AUTO-UNLOCK_$2.sh
sh /var/run/WD-AUTO-UNLOCK_$2.sh

exit 0






