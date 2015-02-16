# vi: set sw=4 ts=4:

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

###########################################################################

MYNAME	:= "Aries/wd/htdocs"
HTDOCS	:= web webinc web_GuestZone
SUBDIRS	:= phplib neap widget parentalcontrols

###########################################################################

all:

rootfs:
	@echo -e "\033[32mCreating rootfs for $(MYNAME) ...\033[0m"
	$(Q)[ -d $(TARGET)/htdocs ] || mkdir -p $(TARGET)/htdocs

install:
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)for i in $(HTDOCS); do cp -r --remove-destination $$i $(TARGET)/htdocs/. ; done
	$(Q)for i in $(SUBDIRS); do cp -r --remove-destination $$i $(TARGET)/htdocs/. ; done
	$(Q)find $(TARGET)/htdocs -name .svn -type d | xargs rm -rf
ifeq ($(strip $(CONFIG_RGBIN_USB3G_KIT)),y)
	$(Q)cp -r --remove-destination isplst.php $(TARGET)/htdocs/$(SUBDIRS)/.
endif

ifeq ($(strip $(ELBOX_NEW_PARENTAL_CONTROLS)),y)
	@echo -e "\033[32mInstalling New Parental Controls...\033[0m"
	$(Q)rm $(TARGET)/htdocs/webinc/body/parent_ctrl.php
	$(Q)rm $(TARGET)/htdocs/webinc/js/parent_ctrl.php
	$(Q)cp $(TARGET)/htdocs/webinc/body/parent_ctrl_new.php $(TARGET)/htdocs/webinc/body/parent_ctrl.php
	$(Q)cp $(TARGET)/htdocs/webinc/js/parent_ctrl_new.php $(TARGET)/htdocs/webinc/js/parent_ctrl.php
	$(Q)rm $(TARGET)/htdocs/webinc/body/parent_ctrl_new.php
	$(Q)rm $(TARGET)/htdocs/webinc/js/parent_ctrl_new.php
	$(Q)rm $(TARGET)/htdocs/webinc/js/tools_time.php
	$(Q)cp $(TARGET)/htdocs/webinc/js/tools_time_new.php $(TARGET)/htdocs/webinc/js/tools_time.php
	$(Q)rm $(TARGET)/htdocs/webinc/js/tools_time_new.php
	$(Q)cp $(TARGET)/htdocs/phplib/setcfg/DEVICE.TIME.new.php $(TARGET)/htdocs/phplib/setcfg/DEVICE.TIME.php
	$(Q)rm $(TARGET)/htdocs/phplib/setcfg/DEVICE.TIME.new.php
endif
	$(Q)cp lld2d.ico $(TARGET)/etc/config/.

clean:

.PHONY: all rootfs install clean
