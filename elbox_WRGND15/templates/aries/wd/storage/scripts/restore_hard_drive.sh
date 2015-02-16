#!/bin/sh
CONSOLE=/dev/console;
STATUS=/var/partition_status;
RESTORE_STATUS=/var/restore_status;
RESTORE_STATUS2=/var/restore_status2;
RESTORE_STATUS3=/var/restore_status3;
echo "[$0] $1" > $CONSOLE;

format_error=0;

umount_all_partitions () {
	sync;
	info="`mount | grep -m 1 $1`";
	counter=0;
	while [ "$info" != "" ]; do
		if [ "$counter" = "10" ]; then #try 10th times
			echo "remove pid again" > $CONSOLE;
			remove_pid;
			sleep 2;
		fi
		
		if [ "$counter" = "20" ]; then #try 20th times
			echo "Umount fail" > $CONSOLE;
			format_error=1;
			break;
		fi
		counter=$(( $counter + 1 ));
	
		mntp="`echo $info | cut -d' ' -f3`";
		echo "Umount $mntp" > $CONSOLE;
		umount $mntp;
		info="`mount | grep -m 1 $1`";
	done
	
	if [ "$format_error" = "0" ]; then
		swapoff $1"3" 2> /dev/null;
		swapoff $1"5" 2> /dev/null;
	fi
	echo 1 > $RESTORE_STATUS2
}

partdisk () {
	echo "Partition $1" > $CONSOLE;
	echo "FORMAT 12356" > $STATUS;
	P1="n\np\n1\n\n+1024M\n";
	P2="n\np\n2\n\n+512M\n";
	P3="n\np\n3\n\n+512M\n";
	P4="n\ne\n\n\n";
	P5="n\n\n+256M\nt\n5\n82\n";
	P6="n\n\n\nt\n6\n7\nw\n";
	dd if=/dev/zero of=$1 bs=1M count=1 > $CONSOLE;
	echo -e "$P1$P2$P3$P4$P5$P6" | fdisk $1 > /dev/null;
	sleep 30
	echo 1 > $RESTORE_STATUS3
}

exec_tasks () {
	echo "$1" > $CONSOLE;
	umount_all_partitions $2;
	sleep 5;
	partdisk $2;
}

remove_pid () 
{
	httpd_pid=`cat /var/run/httpd.pid`;
	xmldb_pid=`cat /var/run/xmldb_sock_wrgnd14_wd_storage.pid`;
	servd_pid=`cat /var/run/servd.pid`;
	udhcpc_pid=`cat /var/servd/BRIDGE-1-udhcpc.pid`;
	remove_process=`fuser -m $disk`;
	
	for i in $remove_process; do
		if [ "$i" != "$httpd_pid" ]; then
			if [ "$i" != "$xmldb_pid" ]; then
				if [ "$i" != "$servd_pid" ]; then
					if [ "$i" != "$udhcpc_pid" ]; then
						kill $i;
					fi
				fi
			fi
		fi
	done
}

if [ -e "/dev/sda1" ]; then
	removable_sda1=`cat /sys/class/block/sda/removable`
        if [ "$removable_sda1" = "0" ]; then
        	disk=/dev/sda
        fi
fi
if [ -e "/dev/sdb1" ]; then
       	removable_sdb1=`cat /sys/class/block/sdb/removable`
       	if [ "$removable_sdb1" = "0" ]; then
               	disk=/dev/sdb
       	fi
fi

if [ "$disk" = "" ]; then
	disk=/dev/sda;
fi

case "$1" in
format_hd)
	remove_pid
	sleep 5
	exec_tasks "Re-partition $disk." $disk;
	
	if [ "$format_error" = "0" ]; then
		echo 1 > $RESTORE_STATUS;
	fi
	
	sleep 5
	if [ -f "/var/CacheVolume/wdrouter" ]; then
		cp /var/CacheVolume/wdrouter /internalhd/tmp/
	fi
	;;
remove_pid)
	remove_pid
	echo 1 > /var/stop_SE_RATE_ESTIMATION
	echo 1 > /var/stop_SE_unload
	if [ -f "$RESTORE_STATUS2" ]; then
		rm $RESTORE_STATUS2
	fi	
	if [ -f "$RESTORE_STATUS3" ]; then
		rm $RESTORE_STATUS3
	fi	
	service ORION stop
	;;	
partition_hd)	
	umount_all_partitions $disk
	partdisk $disk
	;;	
download_fw)
	newest_image_path=`xmldbc -w /runtime/device/upgrades/upgrade/image`;
	newest_image=`echo $newest_image_path | cut -d'/' -f 5`;
	cd /var/CacheVolume/;
	wget $newest_image_path;
	mv $newest_image wdrouter;
	cd /
	xmldbc -s /runtime/storage/restore_download_fw 1
	;;	
upgrade_fw)
	sh /etc/events/FWUPDATER.sh;
	;;
*)	echo not support [$1] ... > /dev/console
	;;	
esac

echo "[$0] done" > $CONSOLE;
exit 0;
