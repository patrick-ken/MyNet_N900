#!/bin/sh
echo "Inserting gpio.ko ..." > /dev/console
insmod /lib/modules/gpio.ko
[ "$?" = "0" ] && mknod /dev/gpio c 101 0 && echo "done."

echo "Inserting athrs_gmac.ko ..." > /dev/console
insmod /lib/modules/athrs_gmac.ko

echo "Inserting rebootm.ko ..." > /dev/console
insmod /lib/modules/rebootm.ko
# UNIX 98 pty
mknod -m666 /dev/pts/0 c 136 0
mknod -m666 /dev/pts/1 c 136 1