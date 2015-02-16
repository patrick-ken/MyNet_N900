# vi: set sw=4 ts=4:

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME  := "Aries/WD/scripts"
#FILES	:= S91proclink.sh S12wlan.sh S21layout.sh S40event.sh S30disk_cache.sh S96access.sh S32hd-idle-time.sh S92fastroute.sh
FILES	:= S91proclink.sh S12wlan.sh S21layout.sh S40event.sh S30disk_cache.sh S92fastroute.sh S51wlan.sh

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/scripts ] || mkdir -p $(TARGET)/etc/scripts

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(FILES); do \
		cp --remove-destination $$i $(TARGET)/etc/init0.d/.; \
		chmod +x $(TARGET)/etc/init0.d/$$i; \
	done

clean:
.PHONY: all rootfs install clean

