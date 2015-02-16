#!/bin/sh
echo "[$0] [$1] $2 $3 $4" > /dev/console

# echo ACTION=$ACTION > /dev/console
# echo KERNELS=$KERNELS > /dev/console
# echo DEVPATH=$DEVPATH > /dev/console

SG_NAME=`echo $1 | grep "/dev/" | cut -d '/' -f3 | grep "sg"`
# echo SG_NAME=$SG_NAME > /dev/console


if [ "${ACTION}" = "remove" -a "${DEVPATH}" != "" -a "${SG_NAME}" != "" ]; then
    PORT1=`echo $DEVPATH | grep '1-1'`

	# echo PORT1=$PORT1 > /dev/console
   
	if [ "${PORT1}" != "" ]; then
		/etc/scripts/unlock_wd.sh remove /dev/${SG_NAME} USB1	    
	fi
fi

