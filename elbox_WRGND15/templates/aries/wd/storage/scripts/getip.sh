#!/bin/sh
case $1 in
	wan) xmldbc -P /etc/scripts/getip.php -V TYPE=WAN ;;
	lan) xmldbc -P /etc/scripts/getip.php -V TYPE=LAN ;;
	*)	echo -e "Usage:\n  $0 [wan|lan]" > /dev/console;
esac

