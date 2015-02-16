#!/bin/sh
xmldbc -P /etc/events/SAFE_REMOVE.php -V REMOVE_ALL="0" > /var/run/SAFE_REMOVE.sh
sh /var/run/SAFE_REMOVE.sh

exit 0
