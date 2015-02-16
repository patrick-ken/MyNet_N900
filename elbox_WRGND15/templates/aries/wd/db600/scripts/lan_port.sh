#!/bin/sh

dev_type=`xmldbc -w /device/layout`

if [ "$1" == "start" ]; then

	if [ "$dev_type" != "router" ]; then 
		ethreg -i eth0 -p 0 0x0=0x1000 > /dev/null
		ethreg -i eth0 -p 1 0x0=0x1000 > /dev/null
		ethreg -i eth0 -p 2 0x0=0x1000 > /dev/null
		ethreg -i eth0 -p 3 0x0=0x1000 > /dev/null
		ethreg -i eth0 -p 4 0x0=0x1000 > /dev/null
	else
	ethreg -i eth1 -p 1 0x0=0x1000 > /dev/null
	ethreg -i eth1 -p 2 0x0=0x1000 > /dev/null
	ethreg -i eth1 -p 3 0x0=0x1000 > /dev/null
	ethreg -i eth1 -p 4 0x0=0x1000 > /dev/null
	fi	
	
else

	if [ "$dev_type" != "router" ]; then 
		ethreg -i eth0 -p 0 0x0=0x800 > /dev/null
		ethreg -i eth0 -p 1 0x0=0x800 > /dev/null
		ethreg -i eth0 -p 2 0x0=0x800 > /dev/null
		ethreg -i eth0 -p 3 0x0=0x800 > /dev/null
		ethreg -i eth0 -p 4 0x0=0x800 > /dev/null
else
	ethreg -i eth1 -p 1 0x0=0x800 > /dev/null
	ethreg -i eth1 -p 2 0x0=0x800 > /dev/null
	ethreg -i eth1 -p 3 0x0=0x800 > /dev/null
	ethreg -i eth1 -p 4 0x0=0x800 > /dev/null
fi
fi
