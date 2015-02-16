#!/bin/sh
# check is mfc mode or normal mode, Sammy
mfcmode=`devdata get -e mfcmode`
if [ "$mfcmode" = "1" ]; then
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	echo "!! Enable mfc mode, loading mfc_config.xml !!"
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	[ -f /etc/defnodes/mfc_config.xml ] && xmldbc -L /etc/defnodes/mfc_config.xml
else
# load default value
xmldbc -L /etc/defnodes/defaultvalue.xml
fi
# set coutnrycode, telnet_disable , timezone from devdata to node
[ -f /etc/defnodes/defaultvalue.php ] && xmldbc -P /etc/defnodes/defaultvalue.php

# WD 4 models have no "isfreset"
[ -f /usr/sbin/isfreset ] && RESET=`/usr/sbin/isfreset`

if [ "$RESET" = "PRESSED" ]; then
	# do factory reset
	devconf del
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
	echo "!! Reset button is pressed, reset to factory default. !!"
	echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
elif [ "$mfcmode" != "1" ]; then
	# read config value
	devconf get -f /var/config.xml.gz
	if [ "$?" = "0" ]; then
		gunzip /var/config.xml.gz
		xmldbc -r /var/config.xml
		rm /var/config.xml
	else
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
		echo "!!     Uable to read device config.     !!"
		echo "!! Setting is reset to factory default. !!"
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
		# load qos default value
		xmldbc -r /etc/defnodes/fastrack.xml
	fi
fi
# update defnode
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
