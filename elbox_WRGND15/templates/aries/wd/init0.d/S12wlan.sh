#!/bin/sh
#we only insert wifi modules in init. 

####insmod /lib/modules/asf.ko
####insmod /lib/modules/adf.ko
####insmod /lib/modules/ath_hal.ko
####insmod /lib/modules/ath_rate_atheros.ko
####insmod /lib/modules/ath_dfs.ko
####insmod /lib/modules/ath_dev.ko
####insmod /lib/modules/umac.ko 
####
#####2.4 Ghz mac
####ifconfig wifi0 hw ether `xmldbc -g /runtime/devdata/wlanmac` 
#####5 Ghz mac
####ifconfig wifi1 hw ether `xmldbc -g /runtime/devdata/wlanmac2` 

