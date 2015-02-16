#!/bin/sh
echo "[$0] ..."


ROOTFS_HD_IMG=rootfs_hd.tar
HD_INSDIR=/internalhd/root/
HD_TMPDIR=/internalhd/tmp/


echo "kill servd and xmldb"
killall -9 servd
killall -9 xmldb
mkdir /var/bin
export PATH=/var/bin:$PATH

echo "copy upgrade needed file to ramfs"
if [ -f /bin/busybox ]; then
	cp -f /bin/busybox /var/bin
	ln -s ./busybox /var/bin/echo
	ln -s ./busybox /var/bin/sh
	ln -s ./busybox /var/bin/mount
	ln -s ./busybox /var/bin/umount
	ln -s ./busybox /var/bin/reboot
	ln -s ./busybox /var/bin/tar
	ln -s ./busybox /var/bin/sync
	ln -s ./busybox /var/bin/sleep
	ln -s ./busybox /var/bin/rm	
	ln -s ./busybox /var/bin/ls
	ln -s ./busybox /var/bin/cp
	ln -s ./busybox /var/bin/ifconfig
fi
#if [ -f /bin/busybox ]; then
#cp -f /usr/sbin/fwupdater /var/bin
#cp -f /usr/sbin/sdparm /var/bin
#fi

if [ -f /htdocs/cgibin ]; then
	cp -f /htdocs/cgibin /var/bin
	ln -s ./cgibin /var/bin/fwupdater
fi
cp -f /usr/sbin/sdparm /var/bin


echo "before of empty ramfs avoid someone access rootfs"


sda1_mount=`mount|grep sda1|wc -l`
if [ $sda1_mount -eq 0 ]; then
	/var/bin/busybox mount -O sync /dev/sda1 $HD_INSDIR 
	/var/bin/busybox sleep 3
fi
sda3_mount=`mount|grep sda3|wc -l`
if [ $sda3_mount -eq 0 ]; then
	/var/bin/busybox mount -O sync /dev/sda3 $HD_TMPDIR
	/var/bin/busybox sleep 3
fi	


if [ $? -eq 0 ]; then 
## remove any rootfs file
echo "remout /www /htdocs /etc /usr /bin /sbin to empty ramfs avoid someone access rootfs"
mount -t ramfs ramfs /etc
mount -t ramfs ramfs /htdocs
mount -t ramfs ramfs /www
mount -t ramfs ramfs /usr
mount -t ramfs ramfs /bin
mount -t ramfs ramfs /sbin
fi

echo "end of empty ramfs avoid someone access rootfs"

#/var/bin/busybox ls -al /htdocs
#/var/bin/busybox ls -al /usr


#/var/bin/busybox ls -al /var/bin
#/var/bin/busybox echo $PATH



/var/bin/busybox cp /var/firmware.seama /internalhd/tmp/
fwupdater -i /var/firmware.seama


/var/bin/busybox echo \"Preparing to install firmware in the internal HDD\" 
/var/bin/busybox rm -rf ${HD_INSDIR}*
/var/bin/busybox echo start > ${HD_INSDIR}HD_upgrade_start
/var/bin/busybox tar -xzf ${HD_TMPDIR}rootfs_hd.tar
/var/bin/busybox echo end > ${HD_INSDIR}HD_upgrade_end
cd ${HD_INSDIR} 
/var/bin/busybox sync
/var/bin/sdparm --command=sync /dev/sda1
/var/bin/busybox sleep 5

/var/bin/busybox ps
/var/bin/busybox sleep 2

/var/bin/busybox echo 1 > /proc/system_reset
