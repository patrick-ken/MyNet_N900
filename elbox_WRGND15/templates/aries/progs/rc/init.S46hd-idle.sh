#!/bin/sh
	
	SPIN_DOWN=`xmldbc -g /wd/spindown`
	
	if [ "$SPIN_DOWN" = "" ]; then
		$SPIN_DOWN = "600"
	fi
	
	echo spin-down [$SPIN_DOWN] ... > /dev/console
	hd-idle -a sda -i "$SPIN_DOWN" &
	
