#!/bin/sh
echo [$0] $1 $2 ... > /dev/console
HD=`mount | grep 'Public' | awk '{print $1}'| cut -d'/' -f3 | cut -c 1-3`
SD=`echo $1 | cut -c 1-3`
if [ "$HD" != "" ]; then
if [ "$HD" != "$SD" ]; then
echo "512" > /sys/block/$HD/queue/read_ahead_kb;
echo "64" > /sys/block/$HD/queue/nr_requests;
fi
fi
if [ "$SD" != "" ]; then
echo "512" > /sys/block/$SD/queue/read_ahead_kb;
echo "64" > /sys/block/$SD/queue/nr_requests;
exit 0;
fi
