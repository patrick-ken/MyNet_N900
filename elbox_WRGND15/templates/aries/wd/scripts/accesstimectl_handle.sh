#!/bin/sh
echo [$0] [$1] [$2] [$3] [$4]....

# Called from accesstimectl daemon.

case "$1" in
ADD)
	echo "ADD Device handle - [$2] ..." > /dev/console
	rm -rf /var/etc/parentalctl.sh
	day=`date | cut -d " " -f1`
	xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V DAY=$day -V MARK=$3 -V MARK2=$4 
	sh /var/etc/parentalctl.sh
	;;
DEL)
	echo "DEL Device handle - [$2] ..." > /dev/console
	rm -rf /var/etc/parentalctl.sh
	xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V MARK=$3 -V MARK2=$4
	sh /var/etc/parentalctl.sh
	;;
CHANGE)
	echo "CHANGE Device handle - [$2] ..." > /dev/console
	rm -rf /var/etc/parentalctl.sh
	day=`date | cut -d " " -f1`
	xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V DAY=$day -V MARK=$3 -V MARK2=$4
	sh /var/etc/parentalctl.sh
	;;
#++++ for daily_detect
#DETECT)
#	echo "DETECT Device handle - [$2] ..." > /dev/console
#	rm -rf /var/etc/parentalctl.sh
#	day=`date | cut -d " " -f1`
#	xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V DAY=$day -V MARK=$3 -V MARK2=$4
#	sh /var/etc/parentalctl.sh
#	;;
#MIDNIGHT)
#	echo "MIDNIGHT Device handle -  ..." > /dev/console
#	rm -rf /var/etc/parentalctl.sh
#	xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1
#	sh /var/etc/parentalctl.sh
#	;;
#++++ for daily_detect
START)
    echo "START Device handle - [$2] ..." > /dev/console
	    rm -rf /var/etc/parentalctl.sh
		day=`date | cut -d " " -f1`
	    xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V DAY=$day -V MARK=$3 -V MARK2=$4
	    sh /var/etc/parentalctl.sh
	;;
#++++ for daily_accumulate_data
TIMESOUT)
	echo "TIMESOUT Device handle - [$2] ..." > /dev/console
		rm -rf /var/etc/parentalctl.sh
		day=`date | cut -d " " -f1`
        xmldbc -P /etc/scripts/ParentalCTL.php -V ACTION=$1 -V MAC=$2 -V DAY=$day -V MARK2=$4
        sh /var/etc/parentalctl.sh
    ;;
#++++ for daily_accumulate_data
esac
exit 0
