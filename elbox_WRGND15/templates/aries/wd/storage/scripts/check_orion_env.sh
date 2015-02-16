#!/bin/sh 
CONSOLE=/dev/console;
killall -9 apache2 2> /dev/null;
echo "Apache config directory: $1" > $CONSOLE;
echo "PHP config directory: $2" > $CONSOLE;
mkdir -p $1;
mkdir -p $2/conf.d;
mkdir -p /var/www;
chmod 777 /var/www;
ROOT=/var/www/Admin;
INTROOT=/internalhd/root;
mkdir -p $INTROOT$ROOT/webapp/htdocs 2> /dev/null;
test -d $INTROOT$ROOT && ln -s $INTROOT$ROOT $ROOT;
chmod 777 /var/tmp;
INTETC=/internalhd/etc;
mkdir -p $INTETC/orion 2> /dev/null;
if [ -f $INTETC/hosts ]; then
	echo "Self-defined hosts instead of default." > $CONSOLE;
	cat $INTETC/hosts > /var/hosts;
	if [ "$?" != "0" ]; then echo "[Failed!!!]" > $CONSOLE; fi
fi
ORIONCONFS="orion.db oriondb_version.txt dynamicconfig.ini dynamicconfig.ini_safe";
for i in $ORIONCONFS; do
	if [ ! -f $INTETC/orion/$i ]; then
   		cp /etc/orion/$i $INTETC/orion/.;
		echo "[Orion]Recover $i" > $CONSOLE;
	fi
done
mkdir -p /var/tmp/storage/Public/.uploads
if [ -f $INTROOT/orion.local ]; then
. $INTROOT/orion.local;
fi
