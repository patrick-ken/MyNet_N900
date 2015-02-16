#!/bin/sh
echo [$0] $1 $2 $3 $4... > /dev/console

case "$1" in
add)
#	echo "USB is = $3 ..." > /dev/console
	cap=`/usr/sbin/sg_readcap $2 | grep "Device size" | cut -d, -f3`
	TBCAP=``
#	echo "cap = $cap ..." > /dev/console

	Apollo_INFO=`/usr/sbin/apollo $2`

	query_apollo=`echo $Apollo_INFO |  grep "Not Apollo device"`
#	echo "apollo = $query_apollo ..."  > /dev/console

	INFO=`/usr/sbin/dev_info $2`
	MODEL=`echo $INFO | cut -d, -f1`
	DEVICE=`echo $INFO | cut -d, -f2`
	SER=`echo $INFO | cut -d, -f3`
	REVISION=`echo $INFO | cut -d, -f4`
	if [ "$SER" = " " ];then
		SER="$4"
#		echo "SER = $SER..." > /dev/console
	fi
	echo "$MODEL $DEVICE $SER $REVISION..." > /dev/console

	#sg_map
	SG_MAP=`/usr/sbin/sg_map -sd | grep $2 | cut -d "/" -f3` #sg0
#	echo "sg_map = $SG_MAP ..." > /dev/console
	SD_MAP=`/usr/sbin/sg_map -sd | grep $2 | cut -d "/" -f5` #sda
#	echo "sd_map = $SD_MAP ..." > /dev/console

	if [ "$SD_MAP" = "sde" ]; then
		exit 1
	fi

	if [ "$SD_MAP" == "" ]; then
		for i in 0 1 2
		do
			sleep 1
		#   echo "wait time $i..." > /dev/console
			SD_MAP=`/usr/sbin/sg_map -sd | grep $2 | cut -d "/" -f5` #sda
#			echo "sd_map = $SD_MAP ..." > /dev/console
			if [ "$SD_MAP" != "" ]; then
				if [ "$SD_MAP" = "sde" ]; then
					echo "SD_MAP = $SD_MAP ..." > /dev/consol
					exit 1
				fi
				break
			fi
		done
	fi

	if [ "$query_apollo" = "Not Apollo device" ]; then
		echo Not Apollo device ... > /dev/console

	else
		query_nolock=`echo $Apollo_INFO |  grep "WD_ENCRYPTION_STATUS_OFF"`
#		echo "nolock = $query_nolock" > /dev/console

		query_lock=`echo $Apollo_INFO |  grep "WD_ENCRYPTION_STATUS_LOCKED"`
#		echo "lock = $query_lock" > /dev/console

		query_unlock=`echo $Apollo_INFO |  grep "WD_ENCRYPTION_STATUS_UNLOCKED"`
#		echo "unlock = $query_unlock" > /dev/console

		xmldbc -P /etc/scripts/unlock_wd.php -V USB_ADDR=$3 -V WD_DEVICE=$2 -V UNLOCK=$query_unlock -V NOLOCK=$query_nolock -V LOCK=$query_lock -V MODEL_VAL="$MODEL" -V DEVICE_VAL="$DEVICE" -V SER_VAL="$SER" -V REVISION_VAL="$REVISION" > /var/run/unlock_wd.sh
		sh /var/run/unlock_wd.sh  > /dev/console
	fi

	#inner USB
	if [ "$MODEL" = "JMicron" ]; then
		INNER_INFO=`/usr/sbin/smartctl -d sat -T permissive -i $2`
#		echo "INNER_INFO = $INNER_INFO..." > /dev/console
		DEVICE=`echo "$INNER_INFO" | grep "Device Model:" | cut -d: -f2 | sed 's/^[ \t]*//g'`
		MODEL=`echo "$INNER_INFO" | grep "Serial Number:" | cut -d: -f2 | cut -d "-" -f1 | sed 's/^[ \t]*//g'`
		SER=`echo "$INNER_INFO" | grep "Serial Number:" | cut -d: -f2 | cut -d "-" -f2 | sed 's/^[ \t]*//g'`
		REVISION=`echo "$INNER_INFO" | grep "Firmware Version:" | cut -d: -f2 | sed 's/^[ \t]*//g'`
		TBCAP=`echo "$INNER_INFO" | grep "User Capacity:" | cut -d: -f2 | sed 's/^[ \t]*//g'`
		echo "$MODEL $DEVICE $SER $REVISION..." > /dev/console
		Check_WD_DEVICE=`/usr/sbin/sg_map -sd | grep $2 | cut -d "/" -f5`
		if [ "$MODEL" != "WD" ]; then
			xmldbc -P /etc/scripts/UMOUNT_ALL_PARTITION.php -V Check_WD_HD=$Check_WD_DEVICE > /var/run/UMOUNT_ALL_PARTITION.sh
			sh /var/run/UMOUNT_ALL_PARTITION.sh
			exit 1
		else
			/etc/scripts/partition_checker.sh /dev/$Check_WD_DEVICE;
		fi
	fi

	#capacity
		xmldbc -P /etc/scripts/capacity_wd.php -V USB_ADDR=$3 -V WD_DEVICE=$2 -V APOLLO_DEVICE="$query_apollo" -V TBCAP="$TBCAP" -V CAPACITY="$cap" -V MODEL_VAL="$MODEL" -V DEVICE_VAL="$DEVICE" -V SER_VAL="$SER" -V REVISION_VAL="$REVISION" > /var/run/capacity_wd.sh
	sh /var/run/capacity_wd.sh > /dev/console

	xmldbc -P /etc/scripts/sg_map_wd.php -V SG_MAP_VAL=$SG_MAP -V SD_MAP_VAL=$SD_MAP -V USB_ADDR=$3 -V SER_VAL="$SER" -V REVISION_VAL="$REVISION" > /var/run/sg_map_wd.sh

	sh /var/run/sg_map_wd.sh > /dev/console

	sh /etc/events/unlock.sh $2	$SG_MAP

	;;
remove)

	xmldbc -P /etc/scripts/remove_wd.php -V USB_ADDR=$3 -V ACTION=$1 -V REMOVE_WD=$2 > /var/run/remove_wd.sh
	sh /var/run/remove_wd.sh > /dev/console

	echo WD-remove-ok [$2] ... > /dev/console
	;;

*)	echo not support [$1] ... > /dev/console
	;;
esac

exit 0
