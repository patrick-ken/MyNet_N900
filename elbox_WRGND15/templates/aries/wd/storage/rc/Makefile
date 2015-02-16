#############################################################################
-include ../../path.mk
-include ../../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk
#############################################################################

MYNAME  := "WRGND14/ARIES/RC"
INIT    := S10init.sh S19init.sh S20interfaces.sh 
INIT0   := S80telnetd.sh S40gpioevent.sh 
IDIR    := $(TARGET)/etc/init.d
I0DIR   := $(TARGET)/etc/init0.d

#############################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(IDIR) ] || mkdir -p $(IDIR)
	$(Q)[ -d $(I0DIR) ] || mkdir -p $(I0DIR)

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	for i in $(INIT); do cp init.$$i $(IDIR)/$$i; chmod +x $(IDIR)/$$i; done
	for i in $(INIT0); do cp init0.$$i $(I0DIR)/$$i; chmod +x $(I0DIR)/$$i; done
	$(Q)echo "export PATH=\$$PATH:/internalhd/root/sbin:/internalhd/root/bin:/internalhd/root/usr/sbin:/internalhd/root/usr/bin" >> $(TARGET)/etc/profile
#	$(Q)echo "export LD_LIBRARY_PATH=/internalhd/root/lib" >> $(TARGET)/etc/profile

clean:

.PHONY: all rootfs install clean
