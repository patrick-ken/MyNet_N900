###########################################################################
-include ../../../path.mk
-include ../../path.mk
-include ../../arch.mk
-include $(TOPDIR)/.config
###########################################################################

MYNAME	:= "ARIES/WD/STORAGE/SERVICES/SSH"
SERVICES:= sshdconf4 sshserver

DIRS	:=

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating services for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/services/SSH ] || mkdir -p $(TARGET)/etc/services/SSH

install:
	@echo -e "\033[32mInstalling services for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SERVICES); do cp $$i.php $(TARGET)/etc/services/SSH/.; done
	$(Q)for i in $(DIRS); do make -C $$i install; done

clean:

.PHONY: all rootfs install clean
