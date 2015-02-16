#!/bin/sh
# prog action partition scanner_name
# $0   add/remove  sdxx  dlna_sdxx_dbscanner
# verbose level:
# 1: info
# 2: warning
# 3: debug
npath=/wd/storage/dlna/USB
usb1on=`xmldbc -g ${npath}1`
usb2on=`xmldbc -g ${npath}2`
# No shared.
if [ $usb1on = 0 -a $usb2on = 0 ]; then exit 0; fi
phpscript="/etc/scripts/`basename $0 .sh`.php"
case "$1" in
	add)
		name="dlna_$2_dbscanner"
		xmldbc -k $name
		xmldbc -P $phpscript -V ACTION=$1 -V PARTITION=$2 -V NAME=$name
		if [ -p /tmp/dms_ipc ]; then
			xmldbc -t $name:1:"$0 start $name"
		else
			echo "[DLNA] Server is not ready."
		fi
		;;
	remove)
		name="dlna_$2_dbscanner"
		xmldbc -P $phpscript -V ACTION=$1 -V PARTITION=$2
		xmldbc -k $name
		;;
	stop)
		xmldbc -k $2
		xmldbc -P $phpscript -V ACTION=$1 -V NAME=$2
		;;
	start)
		xmldbc -P $phpscript -V ACTION=$1 -V NAME=$2 > /var/run/$2
		sh /var/run/$2
		xmldbc -t $2:1:"$0 stop $2"
		;;
esac
exit 0;
