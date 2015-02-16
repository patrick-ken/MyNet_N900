#############################################################################
-include ../../path.mk
-include ../../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk
#############################################################################

MYNAME  := "WRGND14/ARIES/PRODUCT"
INIT    := not_run_mfc
INIT0   := not_run_mfc S40mfcevent.sh
IDIR    := $(TARGET)/etc/init.d
I0DIR   := $(TARGET)/etc/init0.d
XMLFILES:= mfc_config.xml

#############################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(IDIR) ] || mkdir -p $(IDIR)
	$(Q)[ -d $(I0DIR) ] || mkdir -p $(I0DIR)
	$(Q)[ -d $(TARGET)/etc/defnodes ] || mkdir -p $(TARGET)/etc/defnodes

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	for i in $(INIT); do cp init.$$i $(IDIR)/$$i; chmod +x $(IDIR)/$$i; done
	for i in $(INIT0); do cp init0.$$i $(I0DIR)/$$i; chmod +x $(I0DIR)/$$i; done
	$(Q)for i in $(XMLFILES); do \
		sed -e 's|SiGnAtUrE|$(ELBOX_SIGNATURE)|' $$i > $(TARGET)/etc/defnodes/$$i ; done


clean:

.PHONY: all rootfs install clean
