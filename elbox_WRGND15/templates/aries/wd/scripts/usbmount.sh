#!/bin/sh
echo [$0] $1 $2 $3 $4... > /dev/console

event DLNA.REBUILD      add "/etc/scripts/dlna_rebuild.sh"
service NETATALK stop
case "$1" in
add)

	if [ "$3" = "" ] || [ "$3" = "null" ]; then
		INFO=`/usr/sbin/dev_info /dev/$2`
		SER=`echo $INFO | cut -d, -f3`
		echo "$SER" > /var/tmp/get_suffix
#		echo "SER = $SER..." > /dev/console

		if [ "$SER" = " " ];then
			#SER=`xmldbc -g /runtime/device/time`
			SER="$4"
			echo "$SER" > /var/tmp/get_suffix
#			echo "SER = $SER..." > /dev/console
		fi

		xmldbc -P /etc/scripts/calculate_sn.php -V SER=$SER -V WD_DEVICE=$2 -V CAL_SN="0" > /var/run/calculate_sn.sh
		sh /var/run/calculate_sn.sh  > /dev/console

		/usr/sbin/usbmount "$1" "$2";
	else
		/usr/sbin/usbmount "$1" "$2" "$3";
	fi

	if [ -f "/var/restore_status3" ]; then
		if [ "$3" = "/var/tmp/storage/Public" ]; then
			if [ ! -d "/var/tmp/storage/Public/Shared\ Music" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Music
			fi

			if [ ! -d "/var/tmp/storage/Public/Shared\ Pictures" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Pictures
			fi

			if [ ! -d "/var/tmp/storage/Public/Shared\ Videos" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Videos
			fi

			if [ ! -d "/var/tmp/storage/Public/Software" ]; then
				mkdir /var/tmp/storage/Public/Software
			fi
		fi
	fi

	if [ -f "/var/format_status" ]; then
		if [ "$3" = "/var/tmp/storage/Public" ]; then
			if [ ! -d "/var/tmp/storage/Public/Shared\ Music" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Music
			fi

			if [ ! -d "/var/tmp/storage/Public/Shared\ Pictures" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Pictures
			fi

			if [ ! -d "/var/tmp/storage/Public/Shared\ Videos" ]; then
				mkdir /var/tmp/storage/Public/Shared\ Videos
			fi

			if [ ! -d "/var/tmp/storage/Public/Software" ]; then
				mkdir /var/tmp/storage/Public/Software
			fi
		fi
	fi
	service NETATALK restart

#Because USB delay_use change to 8 seconds so init.S30symbolic.sh can not create symbolic link in boot up.
#Change symbolic link to create in part of mount action.
	echo "Start symbolic link checking..." > /dev/console
	modelname=`xmldbc -g /runtime/device/modelname`
	if [ "$modelname" == "MyNetN900C" ]; then
		# wd storage
		if [ "$3" = "/var/tmp/storage/Public" ]; then
			echo "Start symbolic link checking..." > /dev/console
			link=/shares/Public
			if [ ! -d $link ]; then
				ln -s /var/tmp/storage/Public $link;
				echo "Create $link -> /var/tmp/storage/Public" > /dev/console
			fi
			
			base=/internalhd/tmp/var/log
			if [ ! -d $base ]; then
				mkdir -p $base
			fi
			rm -rf /var/log
			cd /var; ln -s $base
			
			tmp=/internalhd/tmp/var/tmp
			if [ ! -d $tmp ]; then
				mkdir -p $tmp
			fi
		fi
	fi
    ;;
remove)
	/etc/scripts/service_checker.sh "$1" "$2";
	/etc/scripts/dlna_dbscanner.sh "$1" "$2";
	/usr/sbin/usbmount $1 $2
	service NETATALK start
	;;
*)
	echo "not support [$1] ..."
    ;;
esac

exit 0


