#!/bin/sh
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
event WAN-1.UP      add "service INFSVCS.WAN-1 restart"
event LAN-1.UP      add "service INFSVCS.LAN-1 restart"
fi
