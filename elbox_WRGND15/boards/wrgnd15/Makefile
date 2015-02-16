#############################################################################
# Makefile for WRG-ND14 of Aries
#############################################################################
###
## Note : I do not want put any template file in board.We remove to template/aries/wd directory.
##
-include path.mk
MYNAME	:= "WRGND14/Aries"
MYMAKE	:= make V=$(V) DEBUG=$(DEBUG)

all: apps 

prepare:
	@echo -e "\033[32mPreparing configuration for $(MYNAME) ...\033[0m"

apps:
	@echo -e "\033[32mBuilding apps for $(MYNAME) ...\033[0m"
	$(Q)$(MYMAKE) -C apps
#	$(Q)$(MYMAKE) -C template.aries

rootfs:
	@echo -e "\033[32mCreate rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/config ] || mkdir -p $(TARGET)/etc/config
	$(Q)[ -d $(TARGET)/usr/bin ] || mkdir -p $(TARGET)/usr/bin
	$(Q)[ -d $(TARGET)/usr/sbin ] || mkdir -p $(TARGET)/usr/sbin
	$(Q)echo "/dev/mtdblock/3" >> $(TARGET)/etc/config/devconf
	$(Q)echo "/dev/mtdblock/2" >> $(TARGET)/etc/config/devdata
ifeq ($(strip $(ELBOX_CREATE_LANGUAGE_PACK)),y)
	$(Q)echo "/dev/mtdblock/4" >> $(TARGET)/etc/config/langpack
endif
	$(Q)echo "#!/bin/sh" >> $(TARGET)/bin/reboot
	$(Q)echo "echo 1 > /proc/system_reset" >> $(TARGET)/bin/reboot
	$(Q)chmod +x $(TARGET)/bin/reboot
#	$(Q)$(MYMAKE) -C template.aries rootfs

# install board dependent programs
install:
	@echo -e "\033[32mInstalling applets for $(MYNAME) ...\033[0m"
	$(Q)$(MYMAKE) -C apps install
	$(Q)cp mfc $(TARGET)/usr/sbin/.
	$(Q)chmod +x $(TARGET)/usr/sbin/mfc
#	$(Q)$(MYMAKE) -C template.aries install

# clean board dependent programs
clean:
	@echo -e "\033[32mCleaning applets for $(MYNAME) ...\033[0m"
	$(Q)$(MYMAKE) -C apps clean
#	$(Q)$(MYMAKE) -C template.aries clean

.PHONY: apps install clean prepare rootfs

#####################################################################
