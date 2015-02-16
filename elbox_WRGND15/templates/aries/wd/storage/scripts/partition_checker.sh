#!/bin/sh
CONSOLE=/dev/console;
STATUS=/var/partition_status;
echo "[$0] $1" > $CONSOLE;

umount_all_partitions () {
	sync;
	info="`mount | grep -m 1 $1`";
	while [ "$info" != "" ]; do
		mntp="`echo $info | cut -d' ' -f3`";
		echo "Umount $mntp" > $CONSOLE;
		umount $mntp;
		info="`mount | grep -m 1 $1`";
	done
	swapoff $1"3" 2> /dev/null;
	swapoff $1"5" 2> /dev/null;
	sleep 1;
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
	sleep 1;
}

formatdisk () {
	debug=/dev/null;
	blksize=4096;
	ls ${1}* > $CONSOLE;
	echo "Format ${1}1" > $CONSOLE;
	mkfs.ext3 -b $blksize -c ${1}1 > $debug; # root
	sleep 1;
	echo "Format ${1}2" > $CONSOLE;
	mkfs.ext3 -b $blksize -c ${1}2 > $debug; # etc
	sleep 1;
	echo "Format ${1}3" > $CONSOLE;
	mkfs.ext3 -b $blksize -c ${1}3 > $debug; # tmp
	sleep 1;
	echo "Format ${1}5" > $CONSOLE;
	mkswap ${1}5 > $debug; # swap
	sleep 1;
	echo "Format ${1}6" > $CONSOLE;
	echo -e "y\n" | mkntfs -b:$blksize ${1}6 > $debug; # public
	sleep 1;
}

exec_tasks () {
	echo "$1" > $CONSOLE;
	umount_all_partitions $2;
	partdisk $2;
}

test -z "$1" && echo -e "No options.\nUsage:\n\t$0 [device]" > $CONSOLE && exit 9;
disk=$1;
info=`fdisk -l $disk 2>&1 | grep "directory"`;
if [ "$info" != "" ]; then echo "No such device.($disk)" > $CONSOLE; exit 9; fi
echo "Start checking $disk" > $CONSOLE;
info=`fdisk -l $disk | grep -c "$disk[1-9]"`;
cnt=`/usr/sbin/xmldbc -g /runtime/device/storage/count`;
/usr/sbin/xmldbc -s /runtime/device/storage/disk:$cnt/partition_status VALID > $CONSOLE;
if [ "$info" -lt "6" ]; then
#	exec_tasks "Invalid partitions. Re-partition $disk." $disk;
	/usr/sbin/xmldbc -s /runtime/device/storage/disk:$cnt/partition_status INVALID > $CONSOLE;
	echo "[$0] done" > $CONSOLE;
	exit 0;
fi
invalid=0;
size=`fdisk -l ${disk} | grep ${disk}1 | awk '{print $3}'`;
if [ "$size" != 126 ]; then invalid=1; fi
size=`fdisk -l ${disk} | grep ${disk}2 | awk '{print $3}'`;
if [ "$invalid" = "1" -o "$size" != "189" ]; then invalid=1; fi
size=`fdisk -l ${disk} | grep ${disk}3 | awk '{print $3}'`;
if [ "$invalid" = "1" -o "$size" != "252" ]; then invalid=1; fi
size=`fdisk -l ${disk} | grep ${disk}5 | awk '{print $3}'`;
if [ "$invalid" = "1" -o "$size" != "284" ]; then invalid=1; fi

if [ "$invalid" = "1" ]; then
#	exec_tasks "Invalid size of partition. Re-partition $disk." $disk;
	/usr/sbin/xmldbc -s /runtime/device/storage/disk:$cnt/partition_status INVALID > $CONSOLE;
fi
echo "[$0] done" > $CONSOLE;
exit 0;
