# vi: set sw=4 ts=4:

include path.mk
include arch.mk
include $(TOPDIR)/.config
include $(TOPDIR)/Vars.mk

#####################################################################
# targets for main Makefile

MYNAME	:= "Aries/WD"
MYMAKE	:= make V=$(V) DEBUG=$(DEBUG)
SUBDIRS	:= defnodes htdocs scripts services events init0.d wifi init.d

ifneq ($(strip $(ELBOX_CREATE_LANGUAGE_PACK)),y)
SUBDIRS += locale
endif

ifneq ($(strip $(ELBOX_PROGS_GPL_DNSMASQ_ADVANCE_DNS_SERVICE_ALPHA)),y)
NO_ADV_DNS :=y
endif

ifneq ($(strip $(ELBOX_PROGS_PRIV_UPNP_AV)),y)
ifneq ($(strip $(ELBOX_PROGS_GPL_ITUNES_SERVER)),y)
NO_UPNP_AV_ITUNES_SERVER :=y
endif
endif

#####################################################################
SUBDIRS	+= $(ELBOX_MODEL_NAME)

all:
	@echo -e "\033[32mBuilding applets for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i; done

prepare:
	@echo -e "\033[32mPreparing configuration for $(MYNAME) ...\033[0m"

rootfs:
	@echo -e "\033[32mCreate rootfs for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i rootfs; done

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i install; done
	$(Q)$(MYMAKE) remove_useless_files

clean:
	@echo -e "\033[32mCleaning board related programs for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i clean; done

.PHONY: all install clean prepare rootfs

#####################################################################

remove_useless_files: no_parentalcontrols no_upnpav_itunesserver

no_parentalcontrols:
ifeq ($(strip $(NO_ADV_DNS)),y)
	@echo -e "\033[32mRemove useless function of parentalcontrols ...\033[0m"
	rm $(TARGET)/htdocs/web/adv_parent_ctrl.php
	rm $(TARGET)/htdocs/web/adv_parent_ctrl_map.php
	rm $(TARGET)/htdocs/web/open_dns.php
	rm $(TARGET)/htdocs/webinc/body/adv_parent_ctrl.php
	rm $(TARGET)/htdocs/webinc/body/adv_parent_ctrl_map.php
	rm $(TARGET)/htdocs/webinc/js/adv_parent_ctrl.php
	rm $(TARGET)/htdocs/webinc/js/adv_parent_ctrl_map.php
	rm $(TARGET)/htdocs/webinc/help/adv_parent_ctrl.php	
	rm $(TARGET)/htdocs/webinc/getcfg/OPENDNS4.xml.php
	rm $(TARGET)/htdocs/webinc/getcfg/OPENDNS4.MAP.xml.php
	rm $(TARGET)/htdocs/phplib/fatlady/OPENDNS4.php
	rm $(TARGET)/htdocs/phplib/fatlady/OPENDNS4.MAP.php
	rm $(TARGET)/htdocs/phplib/setcfg/OPENDNS4.php
	rm $(TARGET)/htdocs/phplib/setcfg/OPENDNS4.MAP.php
	rm $(TARGET)/etc/services/OPENDNS4.php
	rm $(TARGET)/etc/services/OPENDNS4.MAP.php
	rm -rf $(TARGET)/htdocs/parentalcontrols
	rm $(TARGET)/htdocs/web/pic/wd_utility.jpg
endif

no_upnpav_itunesserver:
ifeq ($(strip $(NO_UPNP_AV_ITUNES_SERVER)),y)
	@echo -e "\033[32mRemove useless folder of web/portal ...\033[0m"
	rm -rf $(TARGET)/htdocs/web/portal
endif

.PHONY: remove_useless_files no_parentalcontrols no_upnpav_itunesserver 
