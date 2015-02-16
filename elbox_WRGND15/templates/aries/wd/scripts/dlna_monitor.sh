#!/bin/sh
#echo "[$0]" > /dev/console
# verbose level:
# 1: info
# 2: warning
# 3: debug
xmldbc -k DLNA_MONITOR
myname=`basename $0 .sh`
if [ -f "/var/run/$myname" ]; then
	sh "/var/run/$myname"
else
	dlna_base=/runtime/services/dlna
	timeout=`xmldbc -g $dlna_base/timeout`
	phpscript="/etc/scripts/$myname.php"
	xmldbc -P $phpscript
fi
if [ "$timeout" = "" ]; then timeout=60; fi
xmldbc -t DLNA_MONITOR:$timeout:"/etc/scripts/$myname.sh"
exit 0

