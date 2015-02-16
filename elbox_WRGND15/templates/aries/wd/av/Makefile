.EXPORT_ALL_VARIABLES:

# vi: set sw=4 ts=4:

-include ../path.mk
-include ../arch.mk
-include $(TOPDIR)/.config
-include $(TOPDIR)/Vars.mk

#####################################################################

MYNAME	:= "Aries/wd/av"
MYMAKE	:= make V=$(V) DEBUG=$(DEBUG)
SUBDIRS := defnodes rc services scripts udev.rules htdocs production

#wifi
ifneq ($(strip $(ELBOX_CREATE_LANGUAGE_PACK)),y)
#SUBDIRS += locale
endif

all:
	$(MYMAKE) -C ../db600/ 
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i; done

rootfs:
	$(MYMAKE) -C ../db600/ rootfs
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i rootfs; done

install:
	$(MYMAKE) -C ../db600/ install
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i install; done

clean:
	$(MYMAKE) -C ../db600/ clean
	$(Q)for i in $(SUBDIRS); do $(MYMAKE) -C $$i clean; done

.PHONY: all rootfs install clean
