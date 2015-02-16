#!/bin/sh
#Usage: sh usbmount_fsid.sh sda1 
sfdisk -l $1 2>/dev/null|scut -p$2|grep "*" > /dev/null
if [ $? -eq 0 ]; then
	echo `sfdisk -l $1 2>/dev/null|scut -p$2 -f6`
else
	echo `sfdisk -l $1 2>/dev/null|scut -p$2 -f5`
fi
