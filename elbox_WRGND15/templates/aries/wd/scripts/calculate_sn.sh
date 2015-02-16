#!/bin/sh
echo [$0] $1 $2 $3 $4 $5 ... > /dev/console
if [ "$3" = "3" ]; then
	xmldbc -P /etc/scripts/calculate_sn.php -V WD_DEVICE=$1 -V PID=$2 -V CAL_SN=$3 -V VOLUMENAME=$4 > /var/run/calculate_sn_$2.sh
	sh /var/run/calculate_sn_$2.sh  > /dev/console
else
	xmldbc -P /etc/scripts/calculate_sn.php -V SER=$1 -V WD_DEVICE=$2 -V CAL_SN=$3 -V TMP=$4 -V PID=$5 > /var/run/calculate_sn_$5.sh
	sh /var/run/calculate_sn_$5.sh  > /dev/console
fi 

exit 0
