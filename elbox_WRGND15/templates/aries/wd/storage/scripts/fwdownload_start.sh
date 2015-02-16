#!/bin/sh
echo "$0 ..." > /dev/console
# Stop USB services
service FTP stop
service ITUNES stop
service DLNA stop
service SAMBA stop
service ORION stop
sleep 5
exit 0


