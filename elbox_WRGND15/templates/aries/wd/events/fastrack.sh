#!/bin/sh

# delete bwc default rules, move user rules to runtime temp node
/usr/sbin/phpsh /etc/events/fastrack.php ACTION=CLEAN

# load default rules to db
/usr/sbin/xmldbc -r /etc/defnodes/fastrack.xml

# load user urles to db
/usr/sbin/phpsh /etc/events/fastrack.php ACTION=RESTORE
