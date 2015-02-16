#!/bin/sh
echo [$0]: $1 $2 $3 ... > /dev/console

case "$1" in
add)
	PROTOCOL=`echo $2 | cut -d, -f1 | tr -d ' '`
	EXTERNALPORT=`echo $2 | cut -d, -f2 | tr -d ' '`
	LANIP=`echo $2 | cut -d, -f3 | tr -d ' '`
	INTERNALPORT=`echo $2 | cut -d, -f4 | tr -d ' '`
	APPNAME=`echo $2 | cut -d, -f5`
	echo "[PROTOCOL: $PROTOCOL],[EXTERNALPORT: $EXTERNALPORT],[LANIP: $LANIP],[INTERNALPORT: $INTERNALPORT],[APPNAME: $APPNAME]" > /dev/console

	if [ "$PROTOCOL" = "" -o "$EXTERNALPORT" = "" -o "$LANIP" = "" -o "$INTERNALPORT" = "" -o "$APPNAME" = "" ]; then
		exit 0
	fi

	DUPRULE="$PROTOCOL:$EXTERNALPORT,$LANIP,$INTERNALPORT"
	ret=`grep $DUPRULE /var/pfw_use_port`
	if [ "$ret" != "" ]; then
		echo "Duplicate Rule!!!" > /dev/console
		exit 0
	fi

	xmldbc -P /etc/scripts/orion_helper.php -V ACTION=ADD -V PROTOCOL=$PROTOCOL -V EXTERNALPORT=$EXTERNALPORT -V LANIP=$LANIP -V INTERNALPORT=$INTERNALPORT -V APPNAME=$APPNAME > /var/run/orion_helper.sh
	sh /var/run/orion_helper.sh > /dev/console
	exit
	;;

remove)
	PROTOCOL=`echo $2 | cut -d, -f1 | tr -d ' '`
	EXTERNALPORT=`echo $2 | cut -d, -f2 | tr -d ' '`
	LANIP=`echo $2 | cut -d, -f3 | tr -d ' '`
	INTERNALPORT=`echo $2 | cut -d, -f4 | tr -d ' '`
	echo "[PROTOCOL: $PROTOCOL],[EXTERNALPORT: $EXTERNALPORT],[LANIP: $LANIP],[INTERNALPORT: $INTERNALPORT]" > /dev/console

	if [ "$PROTOCOL" = "" -o "$EXTERNALPORT" = "" -o "$LANIP" = "" -o "$INTERNALPORT" = "" ]; then
		exit 0
	fi

	xmldbc -P /etc/scripts/orion_helper.php -V ACTION=REMOVE -V PROTOCOL=$PROTOCOL -V EXTERNALPORT=$EXTERNALPORT -V LANIP=$LANIP -V INTERNALPORT=$INTERNALPORT -V APPNAME=$APPNAME > /var/run/orion_helper.sh
	sh /var/run/orion_helper.sh > /dev/console
	exit
	;;

list)
	xmldbc -P /etc/scripts/orion_helper.php -V ACTION=LIST > /var/run/orion_helper.sh
	sh /var/run/orion_helper.sh
	exit
	;;

*)	echo not support [$1] ... > /dev/console
	;;
esac

exit 0
