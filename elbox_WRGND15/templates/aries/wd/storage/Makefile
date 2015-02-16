#
# Makefile for WD Storage
#

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

#####################################################################

MYNAME	:= "Aries/WD/Storage"
MYMAKE	:= make V=$(V) DEBUG=$(DEBUG)
SUBDIRS	:= htdocs defnodes services rc udev.rules wifi scripts events production

all:
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i; done

rootfs:
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i rootfs; done

install:
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i install; done

clean:
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i clean; done

.PHONY: all rootfs install clean
