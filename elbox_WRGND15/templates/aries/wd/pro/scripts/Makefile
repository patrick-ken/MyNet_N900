
-include ../../path.mk
-include ../../arch.mk
include $(TOPDIR)/.config

MYNAME:="WRGND15/ARIES/SCRIPTS"
FILES=stop_SE_RATE_ESTIMATION.sh prefwupdate.sh 
FILES=lan_port.sh unlock_sg.sh
DIRS:=

rootfs:
	@echo -e "\033[32mCreating scripts for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/scripts ] || mkdir -p $(TARGET)/etc/scripts

install:
	@echo -e "\033[32mInstalling scripts for $(MYNAME) ...\033[0m"
	$(Q)for i in $(FILES); do \
		cp --remove-destination $$i $(TARGET)/etc/scripts/.; \
		chmod +x $(TARGET)/etc/scripts/$$i; \
	done
ifneq ($(strip $(ELBOX_SAMBA)),y)
	 $(Q)cp services_checker.php $(TARGET)/etc/scripts/services_checker.php
endif
	$(Q)for i in $(DIRS); do \
		if [ ! -d $(TARGET)/etc/scripts/$$i ]; then \
			mkdir -p $(TARGET)/etc/scripts/$$i; \
		fi ; \
		cp -r --remove-destination $$i $(TARGET)/etc/scripts/. ; \
	done
	chmod +x $(TARGET)/etc/scripts/*.sh;

clean:

.PHONY: rootfs install clean

