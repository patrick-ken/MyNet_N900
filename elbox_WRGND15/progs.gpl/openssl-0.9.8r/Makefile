##############################################################################
# Makefile for OpenSSL
# Created by Yi-hung <yi_hung@alphanetworks.com>
##############################################################################
include ../arch.mk
include ../../path.mk
include ../../.config

VERBOSE := > /dev/null
APPLET := openssl
VERSION := 0.9.8r
TARBALL := $(APPLET)-$(VERSION).tar.gz
PATCHFILE := $(TARBALL).diff
SRCROOT := $(APPLET)-$(VERSION)
SOURCEDIR := $(CURDIR)
BUILDDIR := $(CURDIR)/build
ROOTFS := $(TARGET)
MYJOBS := -j$(shell grep -c processor /proc/cpuinfo)

TARGET_CONFS := linux-generic32
TARGET_CONFS += shared
TARGET_CONFS += zlib
TARGET_CONFS += zlib-dynamic
TARGET_CONFS += threads
TARGET_CONFS += no-sse2
TARGET_CONFS += --prefix=$(ROOTFS)
ifeq ($(strip $(HOST_CPU)),ubicom32)
TARGET_CONFS += --with-cryptodev
else
TARGET_CONFS += no-hw
endif
ifeq ($(ELBOX_PROGS_GPL_ZLIB_123),y)
ZLIBINC = $(TOPDIR)/progs.gpl/zlib-1.2.3
endif

# $0 <message> <echo>
define Msg
$(2)echo -e "\033[32m$(1)\033[0m";
endef

# $0 <tarball> <directory> <patchfile>
define PrepareSource
$(call Msg,Prepare source $(2) $(3),@)
$(Q)if [ ! -d "$(2)/$(SRCROOT)" ]; then \
		mkdir -p $(2); \
		tar xf $(1) -C $(2); \
		if [ -f "$(3)" ]; then cd "$(2)" && cat "$(3)" | patch -p1 -r /dev/null; fi \
		fi
endef

# $0 <directory> <options>
define RunConfigure
$(Q)if [ ! -f "$(1)/config.log" ]; then \
		$(call Msg,Enter $(1) and run configure $(2)) \
		cd "$(1)";export CFLAGS="$(CFLAGS)" LDFLAGS="$(LDFLAGS)";./Configure $(2) $(VERBOSE); \
		if [ "$$?" = "0" ]; then touch "$(1)/config.log"; fi \
	fi
endef

# $0 <to> <from>
define GenZlibHeaders
$(Q)cd "$(1)"; if test ! -f zlib.h; then ln -s $(2)/zlib.h; fi
$(Q)cd "$(1)"; if test ! -f zconf.h; then ln -s $(2)/zconf.h; fi
endef

# $0 <directory>
define RemoveZlibHeaders
$(Q)cd "$(1)";rm -f zlib.h zconf.h
endef

# $0 <directory>
define BuildApplet
$(call Msg,Enter $(1) and make,@)
$(Q)make -C "$(1)" CC="$(CC)" AR="$(AR) -r" RANLIB="$(RANLIB)" $(VERBOSE)
endef

# $0 <orig> <new> <patch file>
define Mkdiff
$(Q)if test -f $(3); then cp -f $(3) $(3).bak; fi
$(CURDIR)/mkdiff "$(1)" "$(2)" "$(3).tmp"
$(CURDIR)/stripdate.pl < "$(3).tmp" > "$(3)"
$(Q)if test -f $(3).tmp; then rm -f $(3).tmp; fi
endef

.PHONY: all
all:
	$(Q)make build

prepare:
	$(call PrepareSource,$(SOURCEDIR)/$(TARBALL),$(BUILDDIR),$(SOURCEDIR)/$(PATCHFILE))

config: prepare
	$(call RunConfigure,$(BUILDDIR)/$(SRCROOT),$(TARGET_CONFS))

build: config
	$(call Msg,Building target,@)
	$(call GenZlibHeaders,$(BUILDDIR)/$(SRCROOT),$(ZLIBINC))
	$(call BuildApplet,$(BUILDDIR)/$(SRCROOT))

.PHONY: prepare config build

.PHONY: cleantarget
cleantarget:
	$(Q)if [ -f "$(BUILDDIR)/$(SRCROOT)/Makefile" ]; then make -C "$(BUILDDIR)/$(SRCROOT)" distclean; fi

.PHONY: mkdiff
mkdiff:
	$(call Msg,Make the patch file,@)
	$(Q)mkdir -p $(CURDIR)/orig
	$(Q)tar xf $(SOURCEDIR)/$(TARBALL) -C $(CURDIR)/orig
	$(Q)if test -f "$(BUILDDIR)/$(SRCROOT)/Makefile"; then make -C "$(BUILDDIR)/$(SRCROOT)" dist; fi
	$(Q)if test -f "$(BUILDDIR)/$(SRCROOT)/config.log"; then rm -f "$(BUILDDIR)/$(SRCROOT)/config.log"; fi
	$(Q)if test -f "$(BUILDDIR)/$(SRCROOT)/Makefile.bak"; then rm -f "$(BUILDDIR)/$(SRCROOT)/Makefile.bak"; fi
	$(Q)if test -f "$(BUILDDIR)/$(TARBALL)"; then rm -f "$(BUILDDIR)/$(TARBALL)"; fi
	$(call RemoveZlibHeaders,$(BUILDDIR)/$(SRCROOT))
	$(call Mkdiff,orig,build,$(SOURCEDIR)/$(PATCHFILE))

ifeq ($(strip $(ELBOX_OPENSSL_IN_HD)),y)
MNTP=$(CONFIG_CGIBIN_ARIES_HD_MOUNT_POINT)
TARGETHD=$(TOPDIR)/$(CONFIG_CGIBIN_ARIES_IMAGE_INTERIM_DIR)
.PHONY: install
install:
	$(call Msg,Installing $(APPLET) in $(TARGETHD),@)
	$(Q)mkdir -p $(TARGETHD)/usr/sbin
	$(Q)mkdir -p $(TARGETHD)/lib
	$(Q)cd "$(BUILDDIR)/$(SRCROOT)"; cp ./apps/openssl $(TARGETHD)/usr/sbin
	$(Q)cd "$(BUILDDIR)/$(SRCROOT)"; cp -Rp libcrypto.so* libssl.so* $(TARGETHD)/lib
	$(Q)$(STRIP) $(TARGETHD)/usr/sbin/openssl
	$(Q)$(STRIP) $(TARGETHD)/lib/libcrypto.so*
	$(Q)$(STRIP) $(TARGETHD)/lib/libssl.so*
else
.PHONY: install
install:
	$(call Msg,Installing $(APPLET) in $(TARGET),@)
	$(Q)cd "$(BUILDDIR)/$(SRCROOT)"; cp -Rp libcrypto.so* libssl.so* $(TARGET)/lib
	$(Q)cd "$(BUILDDIR)/$(SRCROOT)"; cp ./apps/openssl	$(TARGET)/usr/sbin/.
	$(Q)$(STRIP) $(TARGET)/usr/sbin/openssl
	$(Q)$(STRIP) $(TARGET)/lib/libcrypto.so*
	$(Q)$(STRIP) $(TARGET)/lib/libssl.so*
endif

.PHONY: clean
clean:
	$(call Msg,Clean $(APPLET),@)
	$(Q)if [ -d "$(BUILDDIR)" ]; then rm -rf "$(BUILDDIR)"; fi
	$(Q)if [ -d "orig" ]; then rm -rf "orig"; fi
