while [ 1 -eq 1 ]
do
	if [ "$1" = "-v" ]; then
		dms_smm
	else
		dms_smm 2>/dev/null
	fi
#	COUNT=`xmldbc -g /runtime/log/sysact/entry#`
#	if [ "$COUNT" != "" ]; then
#		COUNT=$(($COUNT + 1))
#	fi
#	UPTIME=`xmldbc -g /runtime/device/uptime`
#	xmldbc -s /runtime/log/sysact/entry:$COUNT/time $UPTIME
#	xmldbc -s /runtime/log/sysact/entry:$COUNT/message "DLNA is die !"
	echo "DLNA is die !"
	sleep 60
done
exit 0

