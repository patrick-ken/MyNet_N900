#!/bin/sh
echo 1 >   proc/sys/net/netfilter/nf_conntrack_acct
echo [$0]: $1 ... > /dev/console
if [ "$1" = "start" ]; then
	event "STATUS.READY"		add "usockc /var/gpio_ctrl STATUS_GREEN"
	event "STATUS.CRITICAL"		add "usockc /var/gpio_ctrl STATUS_AMBER_BLINK"
	event "STATUS.NOTREADY"		add "usockc /var/gpio_ctrl STATUS_AMBER"
	event "STATUS.GREEN"		add "usockc /var/gpio_ctrl STATUS_GREEN"
	event "STATUS.GREEBBLINK"	add "usockc /var/gpio_ctrl STATUS_GREEN_BLINK"
	event "STATUS.AMBER"		add "usockc /var/gpio_ctrl STATUS_AMBER"
	event "STATUS.AMBERBLINK"	add "usockc /var/gpio_ctrl STATUS_AMBER_BLINK"
	event "INET.CONNECTED"		add "usockc /var/gpio_ctrl INET_ON"
	event "INET.DISCONNECTED"	add "usockc /var/gpio_ctrl INET_OFF"
	
	event "WAN-1.CONNECTED"		add "usockc /var/gpio_ctrl INET_ON"
	event "WAN-1.PPP.ONDEMAND"	add "usockc /var/gpio_ctrl INET_BLINK_SLOW"
	event "WAN-2.CONNECTED"		add "null"
	event "WAN-1.DISCONNECTED"	add "usockc /var/gpio_ctrl INET_OFF"
	event "WAN-2.DISCONNECTED"	add "null"
	
	event "WLAN.CONNECTED"		add "usockc /var/gpio_ctrl WLAN_LED_ON"
	event "WLAN.DISCONNECTED"	add "usockc /var/gpio_ctrl WLAN_LED_OFF"
	event "WPS.INPROGRESS"		add "usockc /var/gpio_ctrl WPS_IN_PROGRESS"
	event "WPS.SUCCESS"			add "usockc /var/gpio_ctrl WPS_SUCCESS"
	event "WPS.OVERLAP"			add "usockc /var/gpio_ctrl WPS_OVERLAP"
	event "WPS.ERROR"			add "usockc /var/gpio_ctrl WPS_ERROR"
	event "WPS.NONE"			add "usockc /var/gpio_ctrl WPS_NONE"
	event "ETH.PWROFF"              add "usockc /var/gpio_ctrl ETH_PWROFF"
	event "WIFI.PWROFF"             add "usockc /var/gpio_ctrl WIFI_PWROFF"
	event "SATA.PWROFF"             add "usockc /var/gpio_ctrl SATA_PWROFF"
	event "USB.PWROFF"              add "usockc /var/gpio_ctrl USB_PWROFF"
	event "OVERHEAT"		add "/etc/events/overheat.sh"
fi
