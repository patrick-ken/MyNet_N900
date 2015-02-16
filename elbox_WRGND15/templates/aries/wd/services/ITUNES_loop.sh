while [ 1 -eq 1 ]
do
	sleep 30
	DAAP=`ps|grep daap|grep -v grep`
	INOTIFY=`ps|grep inotifywait|grep -v grep`
	if [ "$DAAP" = "" ] || [ "$INOTIFY" = "" ]; then
		echo "iTunes server is die !"
		service ITUNES restart
		exit 0
	#else
		#echo "iTunes server is alive !"
	fi
done
exit 0
