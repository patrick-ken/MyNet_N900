while [ 1 -eq 1 ]
do
    hostapd /var/topology.conf
	echo "hostapd server is die !"
	sleep 10
done
exit 0
