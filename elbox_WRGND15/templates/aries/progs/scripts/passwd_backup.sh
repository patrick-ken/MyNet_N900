#!/bin/sh
#backup password
passwd=`xmldbc -g /device/account/entry:1/password`
#load default value
xmldbc -L /etc/defnodes/defaultvalue.xml
[ -f /etc/defnodes/defaultvalue.php ] && xmldbc -P /etc/defnodes/defaultvalue.php
#read restore config value
gunzip /var/config.xml.gz
xmldbc -r /var/config.xml
rm /var/config.xml
#update defnode
for i in /etc/defnodes/S??* ;do
	[ ! -f "$i" ] && continue
	echo "  DEFNODE[$i]" > /dev/console
	case "$i" in
	*.sh)
		sh $i
		;;
	*.php)
		xmldbc -P $i
		;;
	*.xml)
		xmldbc -R $i
		;;
	esac
done
#set password
xmldbc -s /device/account/entry:1/password $passwd
