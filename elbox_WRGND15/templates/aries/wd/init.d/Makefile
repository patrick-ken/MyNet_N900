# vi: set sw=4 ts=4:

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME  := "Aries/WD/init.d"
FILES	:= S14paragon.sh S14kcode.sh 

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/init.d ] || mkdir -p $(TARGET)/etc/init.d

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(FILES); do \
		cp --remove-destination $$i $(TARGET)/etc/init.d/.; \
		chmod +x $(TARGET)/etc/init.d/$$i; \
	done

clean:
.PHONY: all rootfs install clean

