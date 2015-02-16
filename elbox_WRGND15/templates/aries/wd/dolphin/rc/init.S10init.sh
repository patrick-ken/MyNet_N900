#!/bin/sh
MODULE_PATH=/lib/modules

mount -t proc none /proc
mount -t ramfs ramfs /var
mount -t sysfs sysfs /sys
mount -t usbfs usbfs /proc/bus/usb

#echo 1 > /proc/irq/58/smp_affinity
#echo 1 > /proc/irq/83/smp_affinity
#echo 10 > /proc/irq/70/smp_affinity
#echo 1 > /proc/irq/71/smp_affinity
#echo 1 > /proc/irq/49/smp_affinity
echo 2 > /proc/irq/93/smp_affinity
echo 1 > /proc/irq/27/smp_affinity
echo 4 > /proc/irq/95/smp_affinity

## justin add for handling OOM
echo 2 > /proc/sys/vm/overcommit_memory
echo 90 > /proc/sys/vm/overcommit_ratio

insmod $MODULE_PATH/gpio.ko
insmod $MODULE_PATH/fanctl.ko
insmod $MODULE_PATH/fan_speed.ko
