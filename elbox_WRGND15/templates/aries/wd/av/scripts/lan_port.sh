#!/bin/sh

dev_type=`xmldbc -w /device/layout`

if [ "$1" = "start" ]; then
	ethreg -i eth0 -p 0 0x0=0x1000 > /dev/null
	ethreg -i eth0 -p 1 0x0=0x1000 > /dev/null
	ethreg -i eth0 -p 2 0x0=0x1000 > /dev/null
	ethreg -i eth0 -p 3 0x0=0x1000 > /dev/null

	if [ "$dev_type" != "router" ]; then 
		ethreg -i eth0 -p 4 0x0=0x1000 > /dev/null
	fi
	
	#QCA patch for WOL lost issue(workaround)
	ethreg -i eth0 -p 0  0xd=0x3 > /dev/null
	ethreg -i eth0 -p 0  0xe=0x800d > /dev/null
	ethreg -i eth0 -p 0  0xd=0x4003 > /dev/null
	ethreg -i eth0 -p 0  0xe=0x803f > /dev/null
	ethreg -i eth0 -p 0  0x1d=0x3d > /dev/null
	ethreg -i eth0 -p 0  0x1e=0x6860 > /dev/null
	ethreg -i eth0 -p 0  0x1d=0x5 > /dev/null
	ethreg -i eth0 -p 0  0x1e=0x2c46 > /dev/null
	ethreg -i eth0 -p 0  0x1d=0x37 > /dev/null
	ethreg -i eth0 -p 0  0x1e=0x6000 > /dev/null
	ethreg -i eth0 -p 1  0xd=0x3 > /dev/null
	ethreg -i eth0 -p 1  0xe=0x800d > /dev/null
	ethreg -i eth0 -p 1  0xd=0x4003 > /dev/null
	ethreg -i eth0 -p 1  0xe=0x803f > /dev/null
	ethreg -i eth0 -p 1  0x1d=0x3d > /dev/null
	ethreg -i eth0 -p 1  0x1e=0x6860 > /dev/null
	ethreg -i eth0 -p 1  0x1d=0x5 > /dev/null
	ethreg -i eth0 -p 1  0x1e=0x2c46 > /dev/null
	ethreg -i eth0 -p 1  0x1d=0x37 > /dev/null
	ethreg -i eth0 -p 1  0x1e=0x6000 > /dev/null
	ethreg -i eth0 -p 2  0xd=0x3 > /dev/null
	ethreg -i eth0 -p 2  0xe=0x800d > /dev/null
	ethreg -i eth0 -p 2  0xd=0x4003 > /dev/null
	ethreg -i eth0 -p 2  0xe=0x803f > /dev/null
	ethreg -i eth0 -p 2  0x1d=0x3d > /dev/null
	ethreg -i eth0 -p 2  0x1e=0x6860 > /dev/null
	ethreg -i eth0 -p 2  0x1d=0x5 > /dev/null
	ethreg -i eth0 -p 2  0x1e=0x2c46 > /dev/null
	ethreg -i eth0 -p 2  0x1d=0x37 > /dev/null
	ethreg -i eth0 -p 2  0x1e=0x6000 > /dev/null
	ethreg -i eth0 -p 3  0xd=0x3 > /dev/null
	ethreg -i eth0 -p 3  0xe=0x800d > /dev/null
	ethreg -i eth0 -p 3  0xd=0x4003 > /dev/null
	ethreg -i eth0 -p 3  0xe=0x803f > /dev/null
	ethreg -i eth0 -p 3  0x1d=0x3d > /dev/null
	ethreg -i eth0 -p 3  0x1e=0x6860 > /dev/null
	ethreg -i eth0 -p 3  0x1d=0x5 > /dev/null
	ethreg -i eth0 -p 3  0x1e=0x2c46 > /dev/null
	ethreg -i eth0 -p 3  0x1d=0x37 > /dev/null
	ethreg -i eth0 -p 3  0x1e=0x6000 > /dev/null
	ethreg -i eth0 -p 4  0xd=0x3 > /dev/null
	ethreg -i eth0 -p 4  0xe=0x800d > /dev/null
	ethreg -i eth0 -p 4  0xd=0x4003 > /dev/null
	ethreg -i eth0 -p 4  0xe=0x803f > /dev/null
	ethreg -i eth0 -p 4  0x1d=0x3d > /dev/null
	ethreg -i eth0 -p 4  0x1e=0x6860 > /dev/null
	ethreg -i eth0 -p 4  0x1d=0x5 > /dev/null
	ethreg -i eth0 -p 4  0x1e=0x2c46 > /dev/null
	ethreg -i eth0 -p 4  0x1d=0x37 > /dev/null
	ethreg -i eth0 -p 4  0x1e=0x6000 > /dev/null

else
	ethreg -i eth0 -p 0 0x0=0x800 > /dev/null
	ethreg -i eth0 -p 1 0x0=0x800 > /dev/null
	ethreg -i eth0 -p 2 0x0=0x800 > /dev/null
	ethreg -i eth0 -p 3 0x0=0x800 > /dev/null
	
	if [ "$dev_type" != "router" ]; then 
		ethreg -i eth0 -p 4 0x0=0x800 > /dev/null
	fi
fi



