#!/bin/sh

disk_count=`xmldbc -g /runtime/device/storage/count`;
model_name=`cat /etc/config/image_sign`;
count=$disk_count;

if [ "$disk_count" != "0" ];then
	service DLNA stop;
	sleep 5;
	while [ "$count" != "0" ]; do
		if [ "$count" == "1" ];then
			if [ "$model_name" == "wrgnd14_wd_storage" ];then
				rm -rf /var/tmp/storage/Public/.nflc_data;
				echo remove [/var/tmp/storage/Public/.nflc_data] > /dev/console;
			else
				disk1_name=`xmldbc -g /runtime/device/storage/disk:1/entry/mntp`;
				rm -rf $disk1_name/.nflc_data;
				echo remove [$disk1_name/.nflc_data] > /dev/console;
			fi
		else
			disk2_name=`xmldbc -g /runtime/device/storage/disk:$count/entry/mntp`;
			rm -rf $disk2_name/.nflc_data;
			sleep 1;
			echo remove [$disk2_name/.nflc_data] > /dev/console;
		fi
		count=`expr $count - 1`;
	done
	service DLNA restart;
fi

exit 0;
