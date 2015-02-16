#!/bin/sh

dev_type=`xmldbc -w /device/layout`

if [ "$1" == "start" ]; then

	#remove original setting, because this setting is unwanted when kernel is Ubicom. 

	#if [ "$dev_type" != "router" ]; then
	#	echo "write 0 0x0 0x1000" > /proc/ar8327/phy/ctrl1
	#	echo "write 1 0x0 0x1000" > /proc/ar8327/phy/ctrl1
	#	echo "write 4 0x0 0x1000" > /proc/ar8327/phy/ctrl1
	#	echo "write 0 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 1 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 2 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 3 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 4 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#else
	#	echo "write 0 0x0 0x1000" > /proc/ar8327/phy/ctrl1
	#	echo "write 1 0x0 0x1000" > /proc/ar8327/phy/ctrl1
	#	echo "write 0 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 1 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 2 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 3 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#	echo "write 4 0x0 0x1000" > /proc/ar8327/phy/ctrl2
	#fi

	#QCA patch for WOL lost issue(workaround)

	echo "write 0 0xd 0x3" > /proc/ar8327/phy/ctrl2
	echo "write 0 0xe 0x800d" > /proc/ar8327/phy/ctrl2
	echo "write 0 0xd 0x4003" > /proc/ar8327/phy/ctrl2
	echo "write 0 0xe 0x803f" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1d 0x3d" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1e 0x6860" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1d 0x5" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1e 0x2c46" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1d 0x37" > /proc/ar8327/phy/ctrl2
	echo "write 0 0x1e 0x6000" > /proc/ar8327/phy/ctrl2

	echo "write 1 0xd 0x3" > /proc/ar8327/phy/ctrl2
	echo "write 1 0xe 0x800d" > /proc/ar8327/phy/ctrl2
	echo "write 1 0xd 0x4003" > /proc/ar8327/phy/ctrl2
	echo "write 1 0xe 0x803f" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1d 0x3d" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1e 0x6860" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1d 0x5" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1e 0x2c46" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1d 0x37" > /proc/ar8327/phy/ctrl2
	echo "write 1 0x1e 0x6000" > /proc/ar8327/phy/ctrl2

	echo "write 2 0xd 0x3" > /proc/ar8327/phy/ctrl2
	echo "write 2 0xe 0x800d" > /proc/ar8327/phy/ctrl2
	echo "write 2 0xd 0x4003" > /proc/ar8327/phy/ctrl2
	echo "write 2 0xe 0x803f" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1d 0x3d" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1e 0x6860" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1d 0x5" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1e 0x2c46" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1d 0x37" > /proc/ar8327/phy/ctrl2
	echo "write 2 0x1e 0x6000" > /proc/ar8327/phy/ctrl2

	echo "write 3 0xd 0x3" > /proc/ar8327/phy/ctrl2
	echo "write 3 0xe 0x800d" > /proc/ar8327/phy/ctrl2
	echo "write 3 0xd 0x4003" > /proc/ar8327/phy/ctrl2
	echo "write 3 0xe 0x803f" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1d 0x3d" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1e 0x6860" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1d 0x5" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1e 0x2c46" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1d 0x37" > /proc/ar8327/phy/ctrl2
	echo "write 3 0x1e 0x6000" > /proc/ar8327/phy/ctrl2

	echo "write 4 0xd 0x3" > /proc/ar8327/phy/ctrl2
	echo "write 4 0xe 0x800d" > /proc/ar8327/phy/ctrl2
	echo "write 4 0xd 0x4003" > /proc/ar8327/phy/ctrl2
	echo "write 4 0xe 0x803f" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1d 0x3d" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1e 0x6860" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1d 0x5" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1e 0x2c46" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1d 0x37" > /proc/ar8327/phy/ctrl2
	echo "write 4 0x1e 0x6000" > /proc/ar8327/phy/ctrl2

	echo "write 2 0xd 0x3" > /proc/ar8327/phy/ctrl1
	echo "write 2 0xe 0x800d" > /proc/ar8327/phy/ctrl1
	echo "write 2 0xd 0x4003" > /proc/ar8327/phy/ctrl1
	echo "write 2 0xe 0x803f" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1d 0x3d" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1e 0x6860" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1d 0x5" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1e 0x2c46" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1d 0x37" > /proc/ar8327/phy/ctrl1
	echo "write 2 0x1e 0x6000" > /proc/ar8327/phy/ctrl1

	echo "write 3 0xd 0x3" > /proc/ar8327/phy/ctrl1
	echo "write 3 0xe 0x800d" > /proc/ar8327/phy/ctrl1
	echo "write 3 0xd 0x4003" > /proc/ar8327/phy/ctrl1
	echo "write 3 0xe 0x803f" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1d 0x3d" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1e 0x6860" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1d 0x5" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1e 0x2c46" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1d 0x37" > /proc/ar8327/phy/ctrl1
	echo "write 3 0x1e 0x6000" > /proc/ar8327/phy/ctrl1

	if [ "$dev_type" != "router" ]; then
		echo "write 4 0xd 0x3" > /proc/ar8327/phy/ctrl1
		echo "write 4 0xe 0x800d" > /proc/ar8327/phy/ctrl1
		echo "write 4 0xd 0x4003" > /proc/ar8327/phy/ctrl1
		echo "write 4 0xe 0x803f" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1d 0x3d" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1e 0x6860" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1d 0x5" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1e 0x2c46" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1d 0x37" > /proc/ar8327/phy/ctrl1
		echo "write 4 0x1e 0x6000" > /proc/ar8327/phy/ctrl1
	fi
#else
	#if [ "$dev_type" != "router" ]; then
	#	echo "write 0 0x0 0x800" > /proc/ar8327/phy/ctrl1
	#	echo "write 1 0x0 0x800" > /proc/ar8327/phy/ctrl1
	#	echo "write 4 0x0 0x800" > /proc/ar8327/phy/ctrl1
	#	echo "write 0 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 1 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 2 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 3 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 4 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#else
	#	echo "write 0 0x0 0x800" > /proc/ar8327/phy/ctrl1
	#	echo "write 1 0x0 0x800" > /proc/ar8327/phy/ctrl1
	#	echo "write 0 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 1 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 2 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 3 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#	echo "write 4 0x0 0x800" > /proc/ar8327/phy/ctrl2
	#fi
fi

