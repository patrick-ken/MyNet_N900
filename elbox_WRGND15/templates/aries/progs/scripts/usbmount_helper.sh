#!/bin/sh
echo [$0] $1 $2 $3 > /dev/console
suffix="`echo $2|tr "[a-z]" "[A-Z]"`$3"
if [ "$3" = "0" ]; then
	dev=$2
else
	dev=$2$3
fi
if [ "$1" = "add" ]; then
	if [ "$3" == "1" ]; then
		xmldbc -s /runtime/usb/devname $2$3
	fi
	usbport="";
#if [ "$3" == "0" ]; then
	    modelname=`xmldbc -g /runtime/device/modelname`
		if [ "$modelname" == "MyNetN750" ]; then
		    # wd av
		    usb1=`ls -l /sys/block/$2 | grep "/ath-ehci.0/usb1/1-1/1-1.1/"`
		    usb2=`ls -l /sys/block/$2 | grep "/ath-ehci.0/usb1/1-1/1-1.2/"`
		fi
		if [ "$modelname" == "MyNetN600" ]; then
			# wd db600
		    usb1=`ls -l /sys/block/$2 | grep "/ath-ehci.0/usb1/1-1/"`
		fi
		if [ "$modelname" == "MyNetN900" ]; then
		    # wd pro 
		    usb2=`ls -l /sys/block/$2 | grep "/lm0/usb1/1-1/"`
		    usb1=`ls -l /sys/block/$2 | grep "/lm1/usb2/2-1/"`
		fi
		if [ "$modelname" == "MyNetN900C" ]; then
		    # wd storage	
		    usb1=`ls -l /sys/block/$2 | grep "/lm0/usb1/1-1/"`
		    usb2=`ls -l /sys/block/$2 | grep "/lm1/usb2/2-1/"`			    
		fi
		if [ "$modelname" == "MyNetAC1800" ]; then
		# wd dolphin
		usb2=`ls -l /sys/block/$2 | grep "/lm0/usb3/3-1/"`
		usb1=`ls -l /sys/block/$2 | grep "/ubi32_xhci_ctl.0/usb2/2-1/"`
		usb3=`ls -l /sys/block/$2 | grep "/lm1/usb4/4-1/"`
		fi

		if [ "$usb1" != "" -o "$usb3" != "" ]; then
			usbport="usb1";
		fi
		if [ "$usb2" != "" ]; then
			usbport="usb2";
		fi
#fi
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="add" -V prefix=$2 -V pid=$3 -V fs=$4 -V mntp=$5 -V usbport=$usbport
	# we run df then update extened node to avoid stuck by df while browser is required disk nodes
	df > /dev/null
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="update" -V prefix=$2 -V pid=$3 -V size=`df|scut -p$dev -f1`

	event MOUNT.$suffix add "usbmount mount $dev"
	event MOUNT.ALL add "phpsh /etc/events/MOUNT.ALL.php action=MOUNT"
	event UNMOUNT.$suffix add "usbmount unmount $dev"
	event UNMOUNT.ALL add "phpsh /etc/events/MOUNT.ALL.php action=UNMOUNT"
	event FDISK.`echo $2|tr [a-z] [A-Z]` add "sfdisk /dev/$2 < /var/run/`echo $2|tr [a-z] [A-Z]`.conf"
	event FORMAT.$suffix add "phpsh /etc/events/FORMAT.php dev=$dev action=try_unmount counter=30"
	event DISKUP $suffix
	/etc/scripts/dlna_dbscanner.sh "$1" "$dev"
	/etc/scripts/service_checker.sh "$1" "$dev";
elif [ "$1" = "remove" ]; then
	if [ "$3" == "1" ]; then
		xmldbc -s /runtime/usb/devname ""
	fi
	event MOUNT.$suffix add true
	event UNMOUNT.$suffix add true
	event FORMAT.$suffix add true
	event FDISK.`echo $2|tr "[a-z]" "[A-Z]"` add true
	event DISKDOWN $suffix
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="remove" -V prefix=$2 -V pid=$3
elif [ "$1" = "mount" ]; then
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="mount" -V prefix=$2 -V pid=$3 -V fs=$4
	# we run df then update extened node to avoid stuck by df while browser is required disk nodes
	df > /dev/null
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="update" -V prefix=$2 -V pid=$3 -V size=`df|scut -p$dev -f1`

	event DISKUP $suffix
elif [ "$1" = "unmount" ]; then
	event DISKDOWN $suffix
	phpsh /etc/scripts/usbmount_helper.php action="detach" prefix=$2 pid=$3
	xmldbc -P /etc/scripts/usbmount_helper.php -V action="unmount" -V prefix=$2 -V pid=$3
elif [ "$1" = "detach" ]; then
	phpsh /etc/scripts/usbmount_helper.php action="detach" prefix=$2 pid=$3 mntp=$4
fi
exit 0
