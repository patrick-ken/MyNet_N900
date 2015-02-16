#!/bin/sh
# reset wifi config to default

xmldbc -P /etc/events/DELETECFG.WIFI.php
/etc/scripts/dbsave.sh

xmldbc -d /var/all_nowifi.xml
xmldbc -r /etc/defnodes/defaultvalue.xml
xmldbc -r /var/all_nowifi.xml

rm -f /var/all_nowifi.xml

echo "Resetting wifi config success...." > /dev/console