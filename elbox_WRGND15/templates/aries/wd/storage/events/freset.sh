#!/bin/sh 
echo "START" > /tmp/SE_FRESET_START.pid ;
/internalhd/root/oreset.sh;
# If SE_RATE_ESTIMATION.sh is running,
# then oreset.sh will start after SE script done.
echo "Freset in 3 seconds ...";
sleep 1;
echo "Freset in 2 seconds ...";
sleep 1;
echo "Freset in 1 seconds ...";
sleep 1;
echo "Freseting ...";
event STATUS.CRITICAL;
devconf del;
event REBOOT;
