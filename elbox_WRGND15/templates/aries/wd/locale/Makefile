#
# Makefile for installing locale files.
#

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME  := "Aries/wd/locale"
LANGUAGE_FILES := $(wildcard *.slp)
###########################################################################
all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/sealpac ] || mkdir -p $(TARGET)/etc/sealpac

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(LANGUAGE_FILES); do \
		cp --remove-destination $$i $(TARGET)/etc/sealpac/$$i; \
		done

clean:

.PHONY: all rootfs install clean
