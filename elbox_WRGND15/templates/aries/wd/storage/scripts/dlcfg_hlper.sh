#!/bin/sh
sign=`xmldbc -g /runtime/device/image_sign`
devn=`cat /etc/config/devconf`
xmldbc -P /etc/scripts/dlcfg_hlper.php -V ACTION=STARTTODOWNLOADFILE
xmldbc -d /var/config.xml
xmldbc -P /etc/scripts/dlcfg_hlper.php -V ACTION=ENDTODOWNLOADFILE
gzip /var/config.xml
tar -cvzf /var/config.xml.tgz /internalhd/etc/apache2/ /internalhd/etc/orion/ /var/config.xml.gz
seama -i /var/config.xml.tgz -m signature=$sign -m noheader=1 -m type=devconf -m dev=$devn 
mv /var/config.xml.tgz.seama /htdocs/web/docs/config.bin
rm -f /var/config.xml.gz /var/config.xml.tgz /var/config.xml.gz.seama
echo "[$0]: config.bin generated!" > /dev/console
