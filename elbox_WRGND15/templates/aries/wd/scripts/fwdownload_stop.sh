#!/bin/sh
echo "$0 ..." > /dev/console
# Re-start USB services
service FTP restart
service ITUNES restart
service DLNA restart
service SAMBA restart
exit 0


