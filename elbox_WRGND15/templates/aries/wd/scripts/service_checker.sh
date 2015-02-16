#!/bin/sh
# prog action partition service_name
# $0   add/remove  sdxx  samba
echo "[$0] $1 $2" > /dev/console
myname=`basename ${0} .sh`
phpscript="/etc/scripts/${myname}.php"
services="samba ftp itune afp"
for s in ${services}; do
phpsh ${phpscript} ACTION=${1} PARTITION=${2} SERVICE=${s}
done
exit 0
