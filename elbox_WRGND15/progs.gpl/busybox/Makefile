#
# Makefile for busybox in elbox
#
-include ../../path.mk
-include $(TOPDIR)/arch.mk
-include $(TOPDIR)/.config
####################################################################################

define MSG
echo -e "\033[$(if $(2),$(2),32)m$(1)\033[0m"
endef

####################################################################################
# Version
ifeq ($(ELBOX_PROGS_GPL_BUSYBOX_1_18_1),y)
VERSION	:= 1.18.1
endif

MYNAME	:= busybox ($(VERSION))
CURDIR	:= $(shell pwd)
BUILD	:= build
UNTAR	:= tar jxf

####################################################################################
#
BUILDDIR	= $(CURDIR)/$(BUILD)
SRCDIR		= $(CURDIR)/sources
SRCTARBALL	= busybox-$(VERSION).tar.bz2
DIFF		= $(SRCTARBALL).alpha.diff
PROGDIR		= $(CURDIR)/$(BUILD)/busybox-$(VERSION)

####################################################################################
all: prepare_source build_all
install: build_install
clean:
	@$(call MSG,Cleaning $(MYNAME) ...)
	$(Q)rm -rf $(BUILDDIR)

.PHONY: all install clean
####################################################################################
build_%:
	@$(call MSG,Making $(MYNAME) $(patsubst build_%,%,$@))
	$(Q)make -C $(PROGDIR) $(patsubst build_%,%,$@) $(if $(Q),> /dev/null,)

####################################################################################
# Clean for DIFF files list
C4D_LIST_1.18.1 := $(PROGDIR)/docs/busybox.1 $(PROGDIR)/docs/BusyBox.html \
	$(PROGDIR)/docs/busybox.net/BusyBox.html $(PROGDIR)/docs/busybox.pod \
	$(PROGDIR)/docs/BusyBox.txt $(PROGDIR)/include/applets.h \
	$(PROGDIR)/include/bbconfigopts_bz2.h $(PROGDIR)/include/NUM_APPLETS.h \
	$(PROGDIR)/include/usage.h 

clean4diff_%:
	@$(call MSG,Clean for diff - $(MYNAME) $(patsubst clean4diff_%,%,$@))
	@rm -rf $($(patsubst clean4diff_%,C4D_LIST_%,$@))

####################################################################################
prepare_source:
ifeq ($(BUILD),$(wildcard $(BUILD)))
	@$(call MSG,The source tree for $(MYNAME) is already exist !!)
else
	@$(call MSG,Preparing the source for $(MYNAME) ...)
	$(Q)mkdir -p $(BUILDDIR)
	$(Q)cd $(BUILDDIR); $(UNTAR) $(SRCDIR)/$(SRCTARBALL)
	$(Q)chmod +w -R $(BUILDDIR)
	$(Q)patch -p0 < $(SRCDIR)/$(DIFF)
endif

mkdiff: build_distclean clean4diff_$(VERSION)
	@$(call MSG,Generating the diff file for $(MYNAME) ...)
	$(Q)rm -rf original; mkdir -p original
	$(Q)cd original; $(UNTAR) $(SRCDIR)/$(SRCTARBALL)
	$(Q)$(SRCDIR)/mkdiff original $(BUILD) $(SRCDIR)/$(DIFF).tmp
	$(Q)$(SRCDIR)/stripdate.pl < $(SRCDIR)/$(DIFF).tmp > $(SRCDIR)/$(DIFF)
	$(Q)rm -rf original $(SRCDIR)/$(DIFF).tmp

.PHONY: prepare_source mkdiff
####################################################################################
