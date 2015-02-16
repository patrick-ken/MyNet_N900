#!/bin/sh
# kernel > 2.6.22, it (may)will use nf_conntrack_max
CONNTRACK_MAX=`xmldbc -g /runtime/device/conntrack_max`
if [ "$CONNTRACK_MAX" = ""  ]; then
	CONNTRACK_MAX=8192
	echo "CONNTRACK_MAX=$CONNTRACK_MAX"
fi

if [ -f /proc/sys/net/netfilter/nf_conntrack_max ]; then
	echo $CONNTRACK_MAX > /proc/sys/net/netfilter/nf_conntrack_max
else
	echo $CONNTRACK_MAX > /proc/sys/net/ipv4/ip_conntrack_max
fi

# For non-NAPI dev(such as ralink wireless interface, may can smaller )
echo 200 > /proc/sys/net/core/netdev_max_backlog
# For NAPI dev(such as ralink ethernet interface, may can smaller )
echo 32 > /proc/sys/net/core/netdev_budget

#joel temp to disable fast net,that cause panic
#insmod /lib/modules/sw_tcpip.ko
insmod /lib/modules/ifresetcnt.ko

#bouble: smaller tcp established timeout
if [ -f /proc/sys/net/netfilter/nf_conntrack_tcp_timeout_established ]; then
	echo 3600 > /proc/sys/net/netfilter/nf_conntrack_tcp_timeout_established
fi
