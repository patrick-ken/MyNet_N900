#
# Board dependent Makefile for WRG-ND15
#

MYNAME	:= WRG-ND15
MKSQFS	:= ./tools/squashfs-tools-4.0/mksquashfs

SEAMA	:= ./tools/seama/seama
LZMA	:= ./tools/lzma/lzma
MYMAKE	:= $(Q)make V=$(V) DEBUG=$(DEBUG)
FWDEV	:= /dev/mtdblock/1
UBOOTDEV:= /dev/mtdblock/6
SVNREV	:= $(shell svn info $(TOPDIR) | grep Revision: | cut -f2 -d' ')

ifeq ($(strip $(ELBOX_USE_IPV6)),y)
KERNELCONFIG := kernel.aries.ipv6.config
else
KERNELCONFIG := kernel.aries.config
endif

ifdef PREDEFINE_RELIMAGE
RELIMAGE:=$(PREDEFINE_RELIMAGE)
UBOOTIMAGE:=$(PREDEFINE_UBOOTIMAGE)
else
BUILDNO :=$(shell cat buildno)
RELIMAGE:=$(shell echo My_Net_N900_$(ELBOX_FIRMWARE_VERSION)_$(BUILDNO))
UBOOTIMAGE:=$(shell echo $(ELBOX_MODEL_NAME)_uboot_v$(SVNREV)_$(BUILDNO))
endif

#############################################################################
# This one will be make in fakeroot.
fakeroot_rootfs_image:
	@rm -f fakeroot.rootfs.img
	@./progs.board/makedevnodes rootfs
	$(Q)$(MKSQFS) rootfs fakeroot.rootfs.img $(MKSQFS_BLOCK)

.PHONY: rootfs_image

#############################################################################
# The real image files

$(ROOTFS_IMG): $(MKSQFS) $(LZMA)
	@echo -e "\033[32m$(MYNAME): building squashfs (LZMA)!\033[0m"
	$(Q)make clean_CVS
	$(Q)fakeroot make -f progs.board/config.mk fakeroot_rootfs_image
	$(Q)mv fakeroot.rootfs.img $(ROOTFS_IMG)
	$(Q)chmod 664 $(ROOTFS_IMG)
	$(Q)cp $(ROOTFS_IMG) progs.board/ubicom/bin/.

$(MKSQFS) $(SEAMA) $(LZMA):
	$(Q)make -C $(dir $@)

##########################################################################

rootfs_image:
	@echo -e "\033[32m$(MYNAME): creating rootfs image ...\033[0m"
	$(Q)rm -f $(ROOTFS_IMG)
	$(MYMAKE) $(ROOTFS_IMG)

.PHONY: rootfs_image

##########################################################################
#
#   Major targets: kernel, kernel_clean, release & tftpimage
#
##########################################################################

kernel_clean:
	@echo -e "\033[32m$(MYNAME): cleaning kernel ...\033[0m"
	$(Q)make -C progs.board/ubicom kernel_clean V=$(V) DEBUG=$(DEBUG)

kernel:
	@echo -e "\033[32m$(MYNAME) Building kernel ...\033[0m"
	$(Q)cp progs.board/$(KERNELCONFIG) kernel/.config
	#$(Q)make -C progs.board/ubicom -f sources.mk
	#$(Q)make -C progs.board/ubicom ultra V=$(V) DEBUG=$(DEBUG)
	$(Q)make -C progs.board/ubicom kernel_image V=$(V) DEBUG=$(DEBUG)

ifeq (buildno, $(wildcard buildno))
BUILDNO := $(shell cat buildno)

uboot_release: $(SEAMA)
	@echo -e "\033[32m"; \
	echo "=====================================";   \
	echo "You are going to build release uboot.";   \
	echo "=====================================";   \
	echo -e "\033[32m$(MYNAME) make release uboot... \033[0m"
	$(Q)[ -d images ] || mkdir -p images
	@echo -e "\033[32m$(MYNAME) prepare uboot...\033[0m"
	$(Q)make -C progs.board/ubicom uboot
	$(Q)cp progs.board/ubicom/bin/bootexec_bd.bin+u-boot.ub raw.img
	$(Q)cp raw.img $(UBOOTIMAGE).bin 
#	$(Q)$(SEAMA) -i raw.img\
		-m dev=$(UBOOTDEV) -m type=firmware -m signature=$(ELBOX_SIGNATURE) -m noheader=1
#	$(Q)mv raw.img.seama web.img; rm -f raw.img
#	$(Q)$(SEAMA) -d web.img
#	$(Q)./tools/release.sh web.img $(UBOOTIMAGE).bin
#	$(Q)make sealpac_template
#	$(Q)if [ -f sealpac.slt ]; then ./tools/release.sh sealpac.slt $(UBOOTIMAGE).slt; fi

uboot_release_with_seama: $(SEAMA)
	@echo -e "\033[32m"; \
	echo "=====================================";   \
	echo "You are going to build release uboot.";   \
	echo "=====================================";   \
	echo -e "\033[32m$(MYNAME) make release uboot... \033[0m"
	$(Q)[ -d images ] || mkdir -p images
	@echo -e "\033[32m$(MYNAME) prepare uboot...\033[0m"
	$(Q)make -C progs.board/ubicom uboot
	$(Q)cp progs.board/ubicom/bin/bootexec_bd.bin+u-boot.ub raw.img
	$(Q)$(SEAMA) -i raw.img\
		-m dev=$(UBOOTDEV) -m type=firmware -m signature=$(ELBOX_SIGNATURE) -m noheader=1
	$(Q)mv raw.img.seama web.img; rm -f raw.img
	$(Q)$(SEAMA) -d web.img
	$(Q)./tools/release.sh web.img $(UBOOTIMAGE).bin
	$(Q)make sealpac_template
	$(Q)if [ -f sealpac.slt ]; then ./tools/release.sh sealpac.slt $(UBOOTIMAGE).slt; fi

release: rootfs_image $(SEAMA)
	@echo -e "\033[32m"; \
	echo "=====================================";   \
	echo "You are going to build release image.";   \
	echo "=====================================";   \
	echo -e "\033[32m$(MYNAME) make release image... \033[0m"
	$(Q)[ -d images ] || mkdir -p images
	@echo -e "\033[32m$(MYNAME) prepare image...\033[0m"
	$(Q)make -C progs.board/ubicom image_distro
	$(Q)cp progs.board/ubicom/bin/upgrade.ub raw.img
	$(Q)$(SEAMA) -i raw.img\
		-m dev=$(FWDEV) -m type=firmware -m signature=$(ELBOX_SIGNATURE) -m noheader=0
	$(Q)mv raw.img.seama web.img; rm -f raw.img
	$(Q)$(SEAMA) -d web.img
	$(Q)./tools/release.sh web.img $(RELIMAGE).bin
	$(Q)make sealpac_template
	$(Q)if [ -f sealpac.slt ]; then ./tools/release.sh sealpac.slt $(RELIMAGE).slt; fi

tftpimage: rootfs_image $(SEAMA)
	@echo -e "\033[32mThe tftpimage of $(MYNAME) can be load by uboot via tftp!\033[0m"
	$(Q)make -C progs.board/ubicom image_distro
	$(Q)rm -f raw.img; cp progs.board/ubicom/bin/upgrade.ub raw.img
	$(Q)$(SEAMA) -i raw.img -m dev=$(FWDEV) -m type=firmware -m noheader=0
	$(Q)rm -rf raw.img; mv raw.img.seama raw.img
	$(Q)$(SEAMA) -d raw.img
	$(Q)./tools/tftpimage.sh $(TFTPIMG)

else
release tftpimage:
	@echo -e "\033[32m$(MYNAME): Can not build image, ROOTFS is not created yet !\033[0m"
endif
