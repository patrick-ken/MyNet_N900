#!/bin/sh
bandmode=$1
ssid=$2
service PHYINF.WIFI stop
sleep 10
service WIFI_MODS start
sleep 10
if [ "$bandmode" = "2G" ];then
	/etc/ath/makeVAP sta-ext "$ssid" "BANDMODE=2G;CH_MODE=11NGHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=ath0;"
	/etc/ath/activateVAP ath0 br0
fi

if [ "$bandmode" = "5G" ];then
	/etc/ath/makeVAP sta-ext "$ssid" "BANDMODE=5G;CH_MODE=11NAHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=ath0;"
	/etc/ath/activateVAP ath0 br0
fi

