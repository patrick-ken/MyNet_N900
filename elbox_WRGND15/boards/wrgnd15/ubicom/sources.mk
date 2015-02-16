#
# This makefile is used to create the elbox bsp for ubicom32 platform.
#

SOURCES	:= $(shell pwd)/sources

BUILDDIR:= build
KERNEL	?= $(BUILDDIR)/kernel
UBOOT	:= $(BUILDDIR)/u-boot
UBICOM	:= ubicom-private

###############################################################################
define MSG
	@echo -e "\033[$(1)m$(2)\033[0m"
endef

define PrepareTarget
	$(call MSG,32,Preparing the source tree for $(3))
	tar zxf $(SOURCES)/$(1).tgz
	mv $(1) $(2)
	$(if $(wildcard $(SOURCES)/$(1).diff),patch -p0 < $(SOURCES)/$(1).diff,)
endef

define MakeDiff
	$(call MSG,32,Generating diff file for $(3))
	tar zxf $(SOURCES)/$(1).tgz
	$(SOURCES)/mkdiff $(1) $(2) $(SOURCES)/$(1).tmp.diff
	$(SOURCES)/stripdate.pl < $(SOURCES)/$(1).tmp.diff > $(SOURCES)/$(1).diff
	rm -rf $(1) $(SOURCES)/$(1).tmp.diff
endef

###############################################################################

all:
	$(if $(wildcard $(UBOOT)),,@make -f sources.mk prepare_uboot)
	$(if $(wildcard $(UBICOM)),,@make -f sources.mk prepare_ubicom)

clean:
	@echo -e "\033[32mCleaning the BSP directory.\033[0m"
	rm -rf $(BUILDDIR) $(UBICOM)

.PHONY: all clean

###############################################################################
# untar the linux kernel, this directory will be in elbox/kernels/xxxx
prepare_kernel:
	$(if $(wildcard $(BUILDDIR)),,mkdir -p $(BUILDDIR))
	$(call PrepareTarget,linux-2.6.x,$(KERNEL),Linux kernel)
	chmod +x $(KERNEL)/scripts/kconfig/lxdialog/check-lxdialog.sh
	chmod +x $(KERNEL)/scripts/mksysmap

mkdiff_kernel: clean_kernel
	$(call MakeDiff,linux-2.6.x,$(KERNEL),Linux kernel)

clean_kernel:
	$(call MSG,32,Cleaning source build of Linux kernel.)
	make -C $(KERNEL) mrproper
	rm -f $(KERNEL)/arch/ubicom32/kernel/vmlinux.lds
	rm -f $(KERNEL)/arch/ubicom32/include/asm/ocm_size.h
	rm -f $(KERNEL)/arch.mk $(KERNEL)/path.mk

# untar the u-boot, this car be in progs.board, I think.
prepare_uboot:
	$(if $(wildcard $(BUILDDIR)),,mkdir -p $(BUILDDIR))
	$(call PrepareTarget,u-boot,$(UBOOT),U-Boot)

mkdiff_uboot: clean_uboot
	$(call MakeDiff,u-boot,$(UBOOT),U-Boot)

clean_uboot:
	$(call MSG,32,Cleaning the u-boot.)
	make -f Makefile uboot_distclean
	rm -rf build/u-boot/board/ubicom/IP8100_RGW_BOARD/u-boot.lds build/u-boot/include/configs/ultra_uboot_config.h

# untar the private part of the SDK.
prepare_ubicom:
	$(call MSG,32,Preparing the source tree for UBICOM ultra)
	$(if $(wildcard $(UBICOM)),,mkdir -p $(UBICOM))
	(cd $(UBICOM); tar zxf $(SOURCES)/ultra.tgz; tar zxf $(SOURCES)/gdbloader.tgz)
	$(if $(wildcard $(SOURCES)/$(UBICOM).diff),patch -p0 < $(SOURCES)/$(UBICOM).diff,)

mkdiff_ubicom: clean_ubicom
	$(call MSG,32,Generating diff file for UBICOM ultra)
	rm -rf original; mkdir -p original
	(cd original; tar zxf $(SOURCES)/ultra.tgz; tar zxf $(SOURCES)/gdbloader.tgz)
	$(call MSG,32,Remove links in mainexec)
	(cd $(UBICOM)/ultra/projects/mainexec; rm -f app Makefile)
	(cd original/ultra/projects/mainexec; rm -f app Makefile)
	$(call MSG,32,Generating diff ...)
	$(SOURCES)/mkdiff original $(UBICOM) $(SOURCES)/$(UBICOM).tmp.diff
	$(SOURCES)/stripdate.pl < $(SOURCES)/$(UBICOM).tmp.diff > $(SOURCES)/$(UBICOM).diff
	rm -rf original $(SOURCES)/$(UBICOM).tmp.diff
	$(call MSG,32,Restore the links in mainexec)
	(cd $(UBICOM)/ultra/projects/mainexec; \
		ln -s ../bootexec/app app; \
		ln -s ../bootexec/Makefile Makefile)

clean_ubicom:
	$(call MSG,32,CLeaning the UBICOM)
	@make -C $(UBICOM) clean

.PHONY: prepare_kernel prepare_uboot prepare_ubicom \
		mkdiff_kernel mkdiff_uboot mkdiff_ubicom \
		clean_kernel clean_uboot clean_ubicom

