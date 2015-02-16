###########################################################################
-include ../../path.mk
-include ../../arch.mk
-include $(TOPDIR)/.config
###########################################################################

MYNAME	:= "ARIES/WD/STORAGE/SERVICES"
SERVICES:= LAYOUT ENLAN PHYINF.ETH-1 PHYINF.ETH-2 PHYINF.ETH-3 PHYINF.ETH-4 \
			ORION STREAMENGINE SE SE.MODULE SE.RATE.ESTIMATION \
			SE.SHAPER.SET SE.CLASSIFICATION SE.USER.RULES NA.MODULE \
			SE.HTTP.RULES WISH WISH.MODULE WISH.USER.RULES \
			WISH.CLASSIFICATION WISH.MOUNT.STATE  WISH.STATE.GET \
			MDNSRESPONDER
SERVICES += DEVICE.ACCOUNT IPTPFWD sshd LOGD
DIRS	:= ORION PHYINF SSH
ROOTFS_DIRS := root etc tmp

###########################################################################
all:

rootfs:
	@echo -e "\033[32mCreating services for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/services ] || mkdir -p $(TARGET)/etc/services
	$(Q)for i in $(DIRS); do make -C $$i rootfs; done
	$(Q)for i in $(ROOTFS_DIRS); do mkdir -p $(TARGET)/internalhd/$$i; done
	$(Q)ln -sf /internalhd/root/usr/local $(TARGET)/usr/local
	$(Q)ln -sf /internalhd/root/usr/modules $(TARGET)/usr/modules
	$(Q)ln -sf /internalhd/root/usr/lib $(TARGET)/usr/lib
	$(Q)ln -sf /internalhd/root $(TARGET)/shares

install:
	@echo -e "\033[32mInstalling services for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SERVICES); do cp $$i.php $(TARGET)/etc/services/.; done
ifneq ($(strip $(ELBOX_SAMBA)),y)
	$(Q)cp SAMBA.php $(TARGET)/etc/services/SAMBA.php
	$(Q)cp SAMBA_loop.sh $(TARGET)/etc/services/SAMBA_loop.sh
endif
	$(Q)for i in $(DIRS); do make -C $$i install; done

clean:

.PHONY: all rootfs install clean
