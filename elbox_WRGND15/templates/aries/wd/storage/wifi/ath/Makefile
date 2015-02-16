# vi: set sw=4 ts=4:

-include ../../../path.mk
-include ../../../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME  := "Aries/wd/storage/ath"
FILES:= $(wildcard *)


all:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
rootfs:
	@echo -e "\033[32mCreating scripts for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/ath ] || mkdir -p $(TARGET)/etc/ath
install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(FILES); do cp $$i $(TARGET)/etc/ath/$$i; done
	chmod +x $(TARGET)/etc/ath/*;

clean:

.PHONY: all rootfs install clean
