# vi: set sw=4 ts=4:
#
# Makefile for services of Aries.
#

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME	:= "Aries/WD/services"
SERVICES:=


###########################################################################

ifeq ($(ELBOX_ARIES_FEATURE_ROUTER_GATEWAY),y)
SERVICES += SAMBA MULTICAST OPENDNS4 OPENDNS4.MAP IPTABLES IPTDEFCHAIN \
		   DEVICE.ACCOUNT DEVICE.TIME IPTPFWD DNS EMAIL LOG.EMAIL ACCESSCTRL \
		   LAN IPTOBF IP6TOBF REBOOT DEVICE DDNS4.WAN-1 DDNS4.WAN-2 DDNS4.WAN-3 DDNS6.WAN-1 \
		   MDNSRESPONDER HTTPS STORAGE IPTMACCTRL TELNET INTERNET_PING OPENTELNET
		   

# subdirectories.
DIRS	:= HTTP DNS BWC IPTABLES LOG DHCPS INET INFSVCS DDNS PHYINF NAMERESOLV HTTPS
endif

ifeq ($(ELBOX_ARIES_FEATURE_BRIDGE_AP),y)
#SERVICES +=
# subdirectories.
DIRS	:= HTTP
endif

ifeq ($(ELBOX_PROGS_PRIV_UPNP_AV),y)
SERVICES += UPNPAV UPNPAV.PRESCAN
endif

ifeq ($(ELBOX_PROGS_PRIV_ACCESS_DLNA),y)
SERVICES += DLNA
endif

ifeq ($(CONFIG_SHAREPORT_WITH_SAMBA),y)
SERVICES += SHAREPORT
endif

ifeq ($(ELBOX_PROGS_GPL_ITUNES_SERVER),y)
SERVICES += ITUNES
endif

ifeq ($(ELBOX_PROGS_GPL_PURE_FTPD),y)
SERVICES += FTP
endif

ifeq ($(ELBOX_PROGS_PRIV_ALPHA_STUNNEL),y)
SERVICES += STUNNEL
SERVICES += HTTP.WAN-1
endif

ifeq ($(ELBOX_PROGS_GPL_NETATALK),y)
SERVICES += NETATALK
endif

ifeq ($(ELBOX_PROGS_PRIV_NETSTAR),y)
SERVICES += PARENTCTRL PARENTCTRL_UPDATE
DIRS	 += NETSTAR
endif

ifeq ($(ELBOX_PROGS_PRIV_ACCESSTIME),y)
SERVICES += PARENTCTRLSCHL PARENTCTRLSCHL_UPDATE
endif
###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/services ] || mkdir -p $(TARGET)/etc/services
	$(Q)[ -d $(TARGET)/etc/config ] || mkdir -p $(TARGET)/etc/config

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(SERVICES); do cp $$i.php $(TARGET)/etc/services/.; done
	$(Q)cp ITUNES_loop.sh $(TARGET)/etc/services/.;
	$(Q)for i in $(DIRS); do \
		[ -d $(TARGET)/etc/services/$$i ] || mkdir -p $(TARGET)/etc/services/$$i ; \
		cp $$i/*.php $(TARGET)/etc/services/$$i/.; \
	done
clean:

.PHONY: all rootfs install clean
