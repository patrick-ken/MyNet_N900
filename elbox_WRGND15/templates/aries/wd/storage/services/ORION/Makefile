###########################################################################
-include ../../../path.mk
-include ../../path.mk
-include ../../arch.mk
-include $(TOPDIR)/.config
###########################################################################

MYNAME	:= "ARIES/WD/STORAGE/SERVICES/ORION"
SERVICES:= orioncfg apache2_conf mime_types webdav_conf autoindex_conf
ifeq ($(ELBOX_PHP),y)
SERVICES+= php_ini
endif
ifeq ($(ELBOX_OPENSSL),y)
SERVICES+= server_crt server_key apache2_https_conf
endif
DIRS	:=

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating services for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/etc/services/ORION ] || mkdir -p $(TARGET)/etc/services/ORION

install:
	@echo -e "\033[32mInstalling services for $(MYNAME) ...\033[0m"
	$(Q)for i in $(SERVICES); do cp $$i.php $(TARGET)/etc/services/ORION/.; done
	$(Q)for i in $(DIRS); do make -C $$i install; done

clean:

.PHONY: all rootfs install clean
