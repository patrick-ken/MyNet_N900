# vi: set sw=4 ts=4:

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME  := "Aries/WD/scripts"
FILES	:= upwifistatshlper.sh ping.sh iptables_insmod.sh usbmount.sh shareport_setgw.sh \
			IPV4.INET.php dbload.sh SAMBA_speedup.sh fwdownload_start.sh fwdownload_stop.sh \
			calculate_sn.sh calculate_sn.php internet_ping.sh diskcnt_check.php

# FILES += chkdisk.ext3 chkdisk.ext2 chkdisk.ntfs chkdisk.hfs chkdisk.vfat
# now support chkdisk.hfs and chkdisk.ntfs form paragon.
# this script is call by usbmount by hard code.
FILES += chkdisk.ntfs chkdisk.hfs
FILES += dlna_dbscanner.sh dlna_dbscanner.php dlna_monitor.sh dlna_monitor.php dlna_loop.sh \
		 mpe_server_loop.sh dlna_rebuild.sh
FILES += service_checker.sh service_checker.php inotifywait.php smb_reloadcfg.php
FILES += RemoveIGDEntry.php wifi_sta.sh

DIRS	:= libs

ifeq ($(ELBOX_WD_UNLOCK_OPERATION),y)
FILES += unlock_wd.sh unlock_wd.php remove_wd.php capacity_wd.php sg_map_wd.php remove_db.php
endif

ifeq ($(ELBOX_PROGS_PRIV_ACCESSTIME),y)
FILES += accesstimectl.sh accesstimectl_handle.sh ParentalCTL.php PARENTCTRLSCHL_TIME.php
endif

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/scripts ] || mkdir -p $(TARGET)/etc/scripts

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(FILES); do \
		cp --remove-destination $$i $(TARGET)/etc/scripts/.; \
		chmod +x $(TARGET)/etc/scripts/$$i; \
	done
	$(Q)for i in $(DIRS); do \
		[ -d $(TARGET)/etc/scripts/$$i ] || mkdir -p $(TARGET)/etc/scripts/$$i ; \
		cp --remove-destination $$i/* $(TARGET)/etc/scripts/$$i/. ; \
	done

clean:
.PHONY: all rootfs install clean

