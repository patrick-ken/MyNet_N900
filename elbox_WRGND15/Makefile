#
# Main make file for ELBOX
#
# Created by David Hsieh (david_hsieh@alphanetworks.com)
#
##########################################################################
# check login user group
UNAME:=$(shell id -g)
ifeq ($(strip $(UNAME)),0)
%:
	@echo -e "\033[32m"; \
	echo "You are building code in superuser mode."; \
	echo -e "Please login in normal user mode \033[0m"; \
	echo ""
	exit 9
else

##########################################################################
CONFIG:=configs/config
CONFIG_IN:=configs/Config.in
noconfig_targets:=model menuconfig oldconfig config_clean

.EXPORT_ALL_VARIABLES:
TOPDIR:=$(shell pwd)
Q?=@
ifeq ($(strip $(V)),y)
Q:=
endif

##########################################################################
# Pull in the user's configuration file
ifeq ($(filter $(noconfig_targets),$(MAKECMDGOALS)),)
-include .config
endif
# extended make files
PATH_MK			:= path.mk
ARCH_MK			:= arch.mk
CONF_MK			:= config.mk
DCFG_MK			:= .config
# links to create
ARCH_MK_LN		:= . progs.gpl progs.priv progs.template progs.brand kernel
CONF_MK_LN		:= .
PATH_MK_LN		:= progs.board progs.template progs.brand kernel
DCFG_MK_LN		:= progs.board progs.template progs.brand
# Directory path & image file name
TARGET			:= $(TOPDIR)/rootfs
KERNEL_IMG		:= kernel.img
ROOTFS_IMG		:= rootfs.img
# alias of the targets
EL_BOARD		:= $(shell echo $(TOPDIR)/boards/$(ELBOX_BOARD_NAME))
EL_ARCH_MK		:= $(shell echo $(TOPDIR)/boards/$(ELBOX_BOARD_NAME)/$(ARCH_MK))
EL_CONF_MK		:= $(shell echo $(TOPDIR)/boards/$(ELBOX_BOARD_NAME)/$(CONF_MK))
EL_PATH_MK		:= $(shell echo $(TOPDIR)/$(PATH_MK))
EL_DCFG_MK		:= $(shell echo $(TOPDIR)/$(DCFG_MK))
EL_BSP			:= $(shell echo $(TOPDIR)/kernels/$(ELBOX_BSP_NAME))
EL_PROGS_TEMP	:= $(shell echo $(TOPDIR)/templates/$(ELBOX_TEMPLATE_NAME)/progs)
EL_BRAND		:= $(shell echo $(TOPDIR)/templates/$(ELBOX_TEMPLATE_NAME)/$(ELBOX_BRAND_NAME))
EL_GENDEF		:= $(shell echo $(TOPDIR)/templates/$(ELBOX_TEMPLATE_NAME)/$(ELBOX_BRAND_NAME)/$(ELBOX_MODEL_NAME)/gendef.sh)

##########################################################################
# Global variables
-include Vars.mk
# include the define for PROGS_GPL_SUBDIRS & PROGS_PRIV_SUBDIRS
ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
-include $(TOPDIR)/configs/Makefile.in
endif
##########################################################################

# If we have everything, we are clear to go now.
ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
ifeq ($(PATH_MK), $(wildcard $(PATH_MK)))
-include $(PATH_MK)
ifeq ($(CONF_MK), $(wildcard $(CONF_MK)))
CLEAR_TO_GO:=y
endif
endif
endif

##########################################################################
ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
ifeq ($(strip $(CLEAR_TO_GO)),y)
all:
	@echo -e "\033[32m"; \
	echo "==================================================="; \
	echo "You are going to build the f/w images."; \
	echo "Both the release and tftp images will be generated."; \
	echo "==================================================="; \
	echo ""; \
	echo "Do you want to (re)build the linux kernel of the firmware now ?"; \
	echo "The linux kernel part of the firmware will be rebuild if you say yes."; \
	echo "You can skip building kernel to save some time if it is not modified"; \
	echo "since last build. Please say yes, if this is the first time building"; \
	echo "the firmware."; \
	echo ""; \
	echo -e -n "Do you want to build it now ? (yes/no) : \033[0m"; \
	read answer; \
	if [ "$$answer" == "yes" ]; then	\
		make V=$(V) all_with_kernel;	\
	else								\
		make V=$(V) all_without_kernel;	\
	fi

all_with_kernel:
	@echo -e "\033[32mStart building images (with kernel rebuild)...\033[0m"
	$(Q)rm -f buildno
	$(Q)make V=$(V) kernel
	$(Q)make V=$(V) progs_prepare
	$(Q)make V=$(V) progs
	$(Q)make V=$(V) progs_install
	$(Q)make V=$(V) tftpimage
	$(Q)make V=$(V) release

all_without_kernel ui:
	@echo -e "\033[32mStart building images (without kernel rebuild)...\033[0m"
	$(Q)rm -f buildno
	$(Q)make V=$(V) progs_prepare
	$(Q)make V=$(V) progs
	$(Q)make V=$(V) progs_install
	$(Q)make V=$(V) tftpimage
	$(Q)make V=$(V) release

.PHONY: all_with_kernel all_without_kernel

#=================================================================
# include the config.mk files
#MODEL_CONFIG := $(shell ls progs.brand/$(ELBOX_MODEL_NAME)/$(CONF_MK))
#BRAND_CONFIG := $(shell ls progs.brand/$(CONF_MK))
CUSTOMIZED_MODEL_CONFIG:=$(shell echo progs.brand/$(ELBOX_MODEL_NAME)/$(CONF_MK))
CUSTOMIZED_BRAND_CONFIG:=$(shell echo progs.brand/$(CONF_MK))
ifneq ($(wildcard $(CUSTOMIZED_MODEL_CONFIG)),)
HAVE_MODEL_CONFIG:=y
-include $(CUSTOMIZED_MODEL_CONFIG)
else
ifneq ($(wildcard $(CUSTOMIZED_BRAND_CONFIG)),)
HAVE_BRAND_CONFIG:=y
-include $(CUSTOMIZED_BRAND_CONFIG)
endif
endif
TEMPLATE_CONFIG:=$(shell echo progs.template/$(CONF_MK))
ifneq ($(wildcard $(TEMPLATE_CONFIG)),)
HAVE_TEMPLATE_CONFIG:=y
-include $(TEMPLATE_CONFIG)
endif
# - - - - - - - - - - - - - - - - - - -
-include $(CONF_MK)
#++ used by config.mk
clean_CVS:
	@echo -e "\033[32mRemove CVS/SVN from target ...\033[0m"
	$(Q)find $(TARGET) -name CVS -type d | xargs rm -rf
	$(Q)find $(TARGET) -name .svn -type d | xargs rm -rf

remove_fsimg:
	$(Q)rm -f $(ROOTFS_IMG)

.PHONY: clean_CVS remove_fsimg
#-- used by config.mk
#=================================================================
else
ifeq (boards, $(wildcard boards))
all: env_setup
kernel release tftpimage: env_setup
kernel_clean:
	@echo -e "\033[33mUnable to clean kernel while build environment is not setup !\033[0m"
else
all: update
kernel release tftpimage: update
kernel_clean:
	@echo -e "\033[33mUnable to clean kernel while source is not updated !\033[0m"
endif
endif
else
all: model
kernel release tftpimage showconfig: model
kernel_clean:
	@echo -e "\033[33mUnable to clean kernel while configuration is not setup !\033[0m"
endif

.PHONY: kernel_clean kernel release tftpimage

##########################################################################

ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
showconfig:
	@echo -e "===================================================="
	@echo -e "ELBOX_HAVE_DOT_CONFIG = \033[32m$(ELBOX_HAVE_DOT_CONFIG)\033[0m"
	@echo -e "ELBOX_BOARD_NAME      = \033[32m$(ELBOX_BOARD_NAME)\033[0m"
	@echo -e "ELBOX_BSP_NAME        = \033[32m$(ELBOX_BSP_NAME)\033[0m"
	@echo -e "ELBOX_BRAND_NAME      = \033[32m$(ELBOX_BRAND_NAME)\033[0m"
	@echo -e "ELBOX_MODEL_NAME      = \033[32m$(ELBOX_MODEL_NAME)\033[0m"
	@echo -e "ELBOX_TEMPLATE_NAME   = \033[32m$(ELBOX_TEMPLATE_NAME)\033[0m"
	@echo -e "ELBOX_SIGNATURE       = \033[32m$(ELBOX_SIGNATURE)\033[0m"
	@echo -e "CLEAR_TO_GO           = \033[32m$(CLEAR_TO_GO)\033[0m"
	@echo -e "HAVE_BRAND_CONFIG     = \033[32m$(HAVE_BRAND_CONFIG)\033[0m"
	@echo -e "BRAND_CONFIG          = \033[32m$(wildcard $(CUSTOMIZED_BRAND_CONFIG))\033[0m"
	@echo -e "HAVE_MODEL_CONFIG     = \033[32m$(HAVE_MODEL_CONFIG)\033[0m"
	@echo -e "MODEL_CONFIG          = \033[32m$(wildcard $(CUSTOMIZED_MODEL_CONFIG))\033[0m"
	@echo -e "HAVE_TEMPLATE_CONFIG  = \033[32m$(HAVE_TEMPLATE_CONFIG)\033[0m"
	@echo -e "TEMPLATE_CONFIG       = \033[32m$(wildcard $(TEMPLATE_CONFIG))\033[0m"
	@echo -e "GPL programs ..."
	@echo -e "  \033[32m$(PROGS_GPL_SUBDIRS)\033[0m"
	@echo -e "Private programs ..."
	@echo -e "  \033[32m$(PROGS_PRIV_SUBDIRS)\033[0m"
	@echo -e "LANGUAGE ..."
	@echo -e "  \033[32m$(LANGUAGE)\033[0m"
	@echo    "===================================================="
endif

.PHONY: showconfig

##########################################################################
# rootfs

ROOTFS_DIRS:=	bin sbin dev home lib mnt proc usr var www sys \
				etc etc/init.d etc/config etc/scripts etc/templates etc/defnodes

create_rootfs_dir:
	@echo -e "\033[32mCreating clean rootfs directory ...\033[0m"
	$(Q)rm -rf $(TARGET)
	$(Q)mkdir -p $(TARGET)
ifdef TPATH_LIBTGZ
	$(Q)cp $(TPATH_LIBTGZ) $(TARGET)/lib.tgz
	$(Q)cd $(TARGET); tar zxvf lib.tgz; rm -f lib.tgz; chmod 2775 ./lib
endif
	$(Q)cd $(TARGET); \
	for i in $(ROOTFS_DIRS); do \
		[ -d $$i ] || mkdir -p $$i; \
	done
	$(Q)ln -sf /var/tmp $(TARGET)/tmp
	$(Q)ln -sf /var/TZ $(TARGET)/etc/TZ
	$(Q)ln -sf /var/hosts $(TARGET)/etc/hosts
	$(Q)ln -sf /var/etc/ppp $(TARGET)/etc/ppp
	$(Q)ln -sf /var/etc/resolv.conf $(TARGET)/etc/resolv.conf
	$(Q)ln -sf /var/logs $(TARGET)/dev/log
	$(Q)ln -sf /var/log/message $(TARGET)/www/syslog.rg
	$(Q)ln -sf /var/log/tlogsmsg $(TARGET)/www/tsyslog.rg
ifeq ($(strip $(ELBOX_TEMPLATE_ARIES_ENABLE_USER_MANAGEMENT)),y)
	$(Q)rm -rf $(TARGET)/home
	$(Q)ln -sf /var/home		$(TARGET)/home
	$(Q)ln -sf /var/etc/passwd	$(TARGET)/etc/passwd
	$(Q)ln -sf /var/etc/group	$(TARGET)/etc/group
	$(Q)ln -sf /var/etc/shadow	$(TARGET)/etc/shadow
endif

generate_bn:
	$(Q)make -C ./tools/buildimg
	$(Q)./tools/buildimg/genbn > buildno
	$(Q)cp buildno ./tools/buildimg
	@echo -e "\033[32mGenerated buildno = `cat buildno`\033[0m"

generate_def_value:
ifneq ($(strip $(ELBOX_PROGS_PRIV_XMLDB3)),y)
	$(Q)make -C ./tools/alpha/rgdb; make -C ./tools/alpha/xmldb
	$(Q)./tools/alpha/xmldb/xmldb -n $(ELBOX_SIGNATURE) -s ./elbox_xmldb_gendef &
	$(Q)sleep 1
	$(Q)./gendef.sh ./elbox_xmldb_gendef
	$(Q)./tools/alpha/rgdb/rgdb -S ./elbox_xmldb_gendef -D ./rgdb.xml
	$(Q)cp rgdb.xml defaultvalue.xml
	$(Q)gzip rgdb.xml
	$(Q)killall xmldb
	$(Q)mv rgdb.xml.gz $(TARGET)/etc/config/defaultvalue.gz
endif

rootfs: create_rootfs_dir generate_bn generate_def_value
	$(Q)cp buildno $(TARGET)/etc/config
	$(Q)echo $(ELBOX_SIGNATURE) > $(TARGET)/etc/config/image_sign
	$(Q)echo $(ELBOX_FIRMWARE_VERSION) > $(TARGET)/etc/config/buildver
	$(Q)echo $(ELBOX_FIRMWARE_REVISION) > $(TARGET)/etc/config/buildrev
	$(Q)echo "$(shell date +"%a %d %b %Y")" > $(TARGET)/etc/config/builddate
	$(Q)echo "$(shell date +"%Y %m %d %H %M")" > $(TARGET)/etc/config/builddaytime
	$(Q)echo $(LANGUAGE) > $(TARGET)/etc/config/langs
	$(Q)cat $(TARGET)/etc/config/langs | tr -s "[:lower:]" "[:upper:]" | tr -s " " "," > $(TARGET)/etc/config/langs
	$(Q)make -C progs.board rootfs
	$(Q)make -C progs.template rootfs
	$(Q)make -C progs.brand rootfs

.PHONY: rootfs

##########################################################################
# programs

ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
ifeq ($(strip $(CLEAR_TO_GO)),y)
progs_prepare:
	@echo -e "\033[32mPreparing programs configuration ...\033[0m"
	$(Q)make -C progs.board prepare
	$(Q)make -C progs.template prepare
	$(Q)make -C progs.brand prepare

progs:
	@echo -e "\033[32mBuilding programs ...\033[0m"
	$(Q)make -C progs.gpl
	$(Q)make -C progs.priv
	$(Q)make -C progs.board
	$(Q)make -C progs.template
	$(Q)make -C progs.brand

progs_install: rootfs
	@echo -e "\033[32mInstalling programs ...\033[0m"
	$(Q)make -C progs.gpl install
	$(Q)make -C progs.priv install
	$(Q)make -C progs.board install
	$(Q)make -C progs.template install
	$(Q)make -C progs.brand install
	$(Q)make clean_CVS

test_install:
	@echo -e "\033[32mReinstall rootfs for testing ...\033[0m"
	$(Q)make clean_CVS
	$(Q)make -C progs.board install
	$(Q)make -C progs.template install
	$(Q)make -C progs.brand install

progs_clean:
	@echo -e "\033[32mCleaning programs ...\033[0m"
	$(Q)make -C progs.gpl clean
	$(Q)make -C progs.priv clean
	$(Q)make -C progs.board clean
	$(Q)make -C progs.template clean
	$(Q)make -C progs.brand clean
else
progs_clean:
	@echo -e "\033[33mUnable to clean programs while build environment is not setup!\033[0m"
progs_prepare progs progs_install: env_setup
endif
else
progs_clean:
	@echo -e "\033[33mUnable to clean programs while configuration is not setup!\033[0m"
progs_prepare progs progs_install: model
endif
.PHONY: progs_prepare progs progs_install progs_clean


##########################################################################
# Setup environment

ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)
ifdef TPATH_UC
env_setup: env_clean env_path
	@echo -e "\033[32mSetup environment for $(ELBOX_BOARD_NAME)_$(ELBOX_BRAND_NAME)_$(ELBOX_MODEL_NAME) ...\033[0m"
	@echo -e "\033[32mSignature is $(ELBOX_SIGNATURE) ...\033[0m"
	$(Q)if [ ! -d $(EL_BSP) ];			then echo -e "\033[31mDirectory $(EL_BSP) does not exist!\033[0m"; exit 9; fi
	$(Q)if [ ! -d $(EL_PROGS_TEMP) ];	then echo -e "\033[31mDirectory $(EL_PROGS_TEMP) does not exist!\033[0m"; exit 9; fi
	$(Q)if [ ! -d $(EL_BOARD) ];		then echo -e "\033[31mDirectory $(EL_BOARD) does not exist!\033[0m"; exit 9; fi
	$(Q)if [ ! -d $(EL_BRAND) ];		then echo -e "\033[31mDirectory $(EL_BRAND) does not exist!\033[0m"; exit 9; fi
ifneq ($(strip $(ELBOX_PROGS_PRIV_XMLDB3)),y)
	$(Q)if [ ! -f $(EL_GENDEF) ];		then echo -e "\033[31mFile $(EL_GENDEF) does not exist!\033[0m"; exit 9; fi
endif
	$(Q)if [ ! -f $(EL_ARCH_MK) ];		then echo -e "\033[31mFile $(EL_ARCH_MK) does not exist!\033[0m"; exit 9; fi
	$(Q)if [ ! -f $(EL_CONF_MK) ];		then echo -e "\033[31mFile $(EL_CONF_MK) does not exist!\033[0m"; exit 9; fi
	$(Q)ln -s $(EL_BSP)			kernel
	$(Q)ln -s $(EL_PROGS_TEMP)	progs.template
	$(Q)ln -s $(EL_BOARD)		progs.board
	$(Q)ln -s $(EL_BRAND)		progs.brand
ifneq ($(strip $(ELBOX_PROGS_PRIV_XMLDB3)),y)
	$(Q)ln -s $(EL_GENDEF)		gendef.sh
endif
	$(Q)for i in $(ARCH_MK_LN); do ln -s $(EL_ARCH_MK) $$i/$(ARCH_MK); done
	$(Q)for i in $(CONF_MK_LN); do ln -s $(EL_CONF_MK) $$i/$(CONF_MK); done
	$(Q)for i in $(PATH_MK_LN); do ln -s $(EL_PATH_MK) $$i/$(PATH_MK); done
	$(Q)for i in $(DCFG_MK_LN); do ln -s $(EL_DCFG_MK) $$i/$(DCFG_mk); done

env_path:
	@echo -e "\033[32mGenerate path file for Makefile ...\033[0m"
	$(Q)echo "# This file is generated automatically during setup environment." > $(PATH_MK)
	$(Q)echo "# Ths environment is setup for $(ELBOX_SIGNATURE) ." >> $(PATH_MK)
	$(Q)echo "TOPDIR:=$(TOPDIR)" >> $(PATH_MK)
	$(Q)echo "KERNELDIR:=$(TOPDIR)/kernel" >> $(PATH_MK)
	$(Q)echo "TARGET:=$(TARGET)" >> $(PATH_MK)

else
env_setup env_path:
	@echo -e "\033[32mSetup environment failed !!!"
	@echo ""
	@echo " Can NOT find toolchain path definitions !!"
	@echo " Please export environment variables TPATH_UC and TPATH_KC."
	@echo "  TPATH_UC is the path of the toolchain that used to build your user mode programs."
	@echo "  TPATH_KC is the path of the toolchain that used to build your linux kernel."
	@echo ""
	@echo -e "\033[0m"
endif
else
env_setup env_path: model
endif
env_clean:
	@echo -e "\033[32mCleanup the environment ...\033[0m"
	$(Q)for i in $(ARCH_MK_LN); do rm -f $$i/$(ARCH_MK); done
	$(Q)for i in $(CONF_MK_LN); do rm -f $$i/$(CONF_MK); done
	$(Q)for i in $(PATH_MK_LN); do rm -f $$i/$(PATH_MK); done
	$(Q)for i in $(DCFG_MK_LN); do rm -f $$i/$(DCFG_MK); done
	$(Q)rm -f kernel progs.template progs.board progs.brand gendef.sh $(PATH_MK) $(CONF_MK) $(ARCH_MK)

.PHONY: env_setup env_path env_path

##########################################################################
# Update source from SVN

SVNFLAGS:=
ifdef REV
SVNFLAGS+=-r$(REV)
endif

ifeq ($(strip $(ELBOX_HAVE_DOT_CONFIG)),y)

SVN_UP		=	svn update $(SVNFLAGS)
SVN_DIFF	=	svn diff $(SVNFLAGS)
SVN_COPY	=	svn copy $(SVNFLAGS) -m "Make a new branch svn no:$(REV) to $(DST)"
SVN_MKDIR	=	svn mkdir -m "Make a new branch svn no:$(REV) to $(DST) from $(SRC)"
SVN_TARGET	=	boards kernels progs.gpl progs.priv templates templates/$(ELBOX_TEMPLATE_NAME)
SVNR_TARGET	=	Rules.mk Vars.mk configs tools include comlib boards/$(ELBOX_BOARD_NAME) kernels/$(ELBOX_BSP_NAME) \
				templates/$(ELBOX_TEMPLATE_NAME)/progs \
				templates/$(ELBOX_TEMPLATE_NAME)/$(ELBOX_BRAND_NAME)
SVN_BRANCH_TARGET	=	Makefile Vars.mk Rules.mk configs tools include comlib boards/$(ELBOX_BOARD_NAME) kernels/$(ELBOX_BSP_NAME) \
				templates/$(ELBOX_TEMPLATE_NAME) progs.priv/Makefile progs.gpl/Makefile
branch:
ifndef DST
	@echo -e "\033[33mMake branch must input DST...\033[0m"
	@echo -e "\033[33mex:make branch REV=300 DST=svn://1.2.3.100/dap1353/branches/test300 SRC=svn://1.2.3.100/dap1353/trunk \033[0m"
	exit 9
endif	
ifndef SRC
	@echo -e "\033[33mMake branch must input SRC...\033[0m"
	@echo -e "\033[33mex:make branch REV=300 DST=svn://1.2.3.100/dap1353/branches/test300 SRC=svn://1.2.3.100/dap1353/trunk \033[0m"
	exit 9
endif
	@echo -e "\033[33mMake branch to $(DST) from $(SRC)...\033[0m"
	$(Q)$(SVN_MKDIR) $(DST)
	$(Q)$(SVN_MKDIR) $(DST)/boards
	$(Q)$(SVN_MKDIR) $(DST)/kernels
	$(Q)$(SVN_MKDIR) $(DST)/templates
	$(Q)$(SVN_MKDIR) $(DST)/progs.gpl
	$(Q)$(SVN_MKDIR) $(DST)/progs.priv
	$(Q)for i in $(SVN_BRANCH_TARGET); do \
		echo -e "\033[33mMake branch $$i (recusive) ...\033[0m" ; \
		$(SVN_COPY) $(SRC)/$$i $(DST)/$$i|| exit $? ; \
	done
	$(Q)for i in $(PROGS_GPL_SUBDIRS); do \
		echo -e "\033[33mMake branch progs.gpl/$$i (recusive) ...\033[0m" ; \
		$(SVN_COPY) $(SRC)/progs.gpl/$$i $(DST)/progs.gpl/$$i || exit $? ; \
	done
	$(Q)for i in $(PROGS_PRIV_SUBDIRS); do \
		echo -e "\033[33mMake branch progs.priv/$$i (recusive) ...\033[0m" ; \
		$(SVN_COPY) $(SRC)/progs.priv/$$i $(DST)/progs.priv/$$i || exit $? ; \
	done
				
update:
	@echo -e "\033[33mUpdating elbox sources ...\033[0m"
	$(Q)for i in $(SVN_TARGET); do \
		echo -e "\033[33mUpdating $$i\033[0m" ; \
		$(SVN_UP) -N $$i || exit $? ; \
	done
	$(Q)for i in $(SVNR_TARGET); do \
		echo -e "\033[33mUpdating $$i (recusive) ...\033[0m" ; \
		$(SVN_UP) $$i || exit $? ; \
	done
	$(Q)for i in $(PROGS_GPL_SUBDIRS); do \
		echo -e "\033[33mUpdating progs.gpl/$$i (recusive) ...\033[0m" ; \
		$(SVN_UP) progs.gpl/$$i || exit $? ; \
	done
#joel modify for prog.priv can assigned xxxx/abc we can auto svn update  -N xxxx then updat xxxx/abc
	$(Q)for i in $(PROGS_PRIV_SUBDIRS); do \
		echo -e "\033[33mUpdating progs.priv/$$i (recusive) ...\033[0m" ; \
		if [ "`echo "$$i" | cut -d'/' -f 2`" == "$$i" ]; then \
		$(SVN_UP) progs.priv/$$i || exit $? ; \
		else \
		echo -e "update progs.priv/`echo "$$i" | cut -d'/' -f 1` (Not recusive)" ;\
		$(SVN_UP) progs.priv/"`echo "$$i" | cut -d'/' -f 1`" -N; \
		echo -e "update progs.priv/$$i (recusive)" ;\
		$(SVN_UP) progs.priv/$$i || exit $? ; \
		fi \
	done
	$(Q)if [ -f .tmpconfig.h ]; then mv .tmpconfig.h include/elbox_config.h; fi

diff:
	@echo -e "\033[33mDiff elbox ...\033[0m"
	$(Q)for i in $(SVN_TARGET); do \
		echo -e "\033[33mDiff $$i\033[0m" ; \
		$(SVN_DIFF) -N $$i ; \
	done
	$(Q)for i in $(SVNR_TARGET); do \
		echo -e "\033[33mDiff $$i (recusive)\033[0m" ; \
		$(SVN_DIFF) $$i ; \
	done
	$(Q)for i in $(PROGS_GPL_SUBDIRS); do \
		echo -e "\033[33mDiff progs.gpl/$$i ...\033[0m" ; \
		$(SVN_DIFF) progs.gpl/$$i ; \
	done
	$(Q)for i in $(PROGS_PRIV_SUBDIRS); do \
		echo -e "\033[33mDiff progs.priv/$$i ...\033[0m" ; \
		$(SVN_DIFF) progs.priv/$$i ; \
	done


else
update diff: model
endif
.PHONY: update diff

##########################################################################
# Model selection

model: conf
	@echo -e "\033[32mPreparing the config file ...\033[0m"
	$(Q)cp configs/default.config .config
	$(Q)cat .config | sed '/is not set/d' | sed 's/^#/\/\//' | sed 's/^CONFIG/#define CONFIG/' |sed 's/^ELBOX/#define ELBOX/'| sed 's/=y/ /' | sed 's/=/ /' > include/elbox_config.h

.PHONY: model		

##########################################################################
# Configuration

conf menuconfig config oldconfig:

config_clean:
	@echo -e "\033[32mCleaning configuration ...\033[0m"
	$(Q)rm -f .config .config.cmd .config.old .defconfig include/elbox_config.h

clean: progs_clean kernel_clean
	@echo -e "\033[32mCleaning buildimg program ...\033[0m"
	$(Q)make -C ./tools/buildimg clean
	@echo -e "\033[32mCleaning images ...\033[0m"
	$(Q)rm -f $(KERNEL_IMG) $(ROOTFS_IMG) $(TFTPIMG) buildno defaultvalue.xml rgdb.xml.gz
	$(Q)rm -rf $(TARGET)

distclean: clean env_clean config_clean

.PHONY: conf menuconfig config oldconfig config_clean clean distclean
############################################################################
# debug_setup & debug are used to do NFS debug for elbox.
DEBUG_DIRS	:= etc htdocs lib usr sbin www
NFSROOT		:= /nfsroot/$(USER)/elbox
S := "S"
M := "M"
H := "H"
i := "i"

debug_script:
	$(Q)echo "#!/bin/sh"			> $(NFSROOT)/mountdbg.sh
	$(Q)echo "HOST=\"$(HOSTNAME)\""		>> $(NFSROOT)/mountdbg.sh
	$(Q)echo "MOUNT=\"mount\""		>> $(NFSROOT)/mountdbg.sh
	$(Q)echo "SUBDIRS=\"$(DEBUG_DIRS)\""	>> $(NFSROOT)/mountdbg.sh
	$(Q)echo "for i in $$$(S)UBDIRS; do"	>> $(NFSROOT)/mountdbg.sh
	$(Q)echo "  $$$(M)OUNT -onolock $$$(H)OST:/nfsroot/$(USER)/elbox/$$$(i) /$$$(i)" >> $(NFSROOT)/mountdbg.sh
	$(Q)echo "done"				>> $(NFSROOT)/mountdbg.sh
	$(Q)echo "#!/bin/sh"			> $(NFSROOT)/umountdbg.sh
	$(Q)echo "SUBDIRS=\"$(DEBUG_DIRS)\""	>> $(NFSROOT)/umountdbg.sh
	$(Q)echo "for i in $$$(S)UBDIRS; do"	>> $(NFSROOT)/umountdbg.sh
	$(Q)echo "  umount /$$$(i)"		>> $(NFSROOT)/umountdbg.sh
	$(Q)echo "done"				>> $(NFSROOT)/umountdbg.sh
	$(Q)chmod +x $(NFSROOT)/mountdbg.sh $(NFSROOT)/umountdbg.sh

debug_setup:
	@echo -e "\033[32mSetting up the debug environment for $(USER)\033[0m"
	$(Q)for i in $(DEBUG_DIRS); do \
		if [ ! -d $(NFSROOT)/$$i ]; then \
			mkdir -p $(NFSROOT)/$$i ; \
			echo Creating $(NFSROOT)/$$i ; \
		fi ; \
	done
	$(Q)[ -f $(NFSROOT)/mountdbg.sh ] || make debug_script
	@echo
	@echo -e "\033[32mYou need to add the following lines in /etc/exports"
	@echo -e "and restart the NFS server.\033[0m"
	@echo
	@echo "# Mount points of elbox debugging for $(USER)"
	$(Q)for i in $(DEBUG_DIRS); do \
		echo "$(NFSROOT)/$$i *(rw,sync,no_root_squash,no_all_squash)" ; \
	done
	@echo

debug:
	@echo -e "\033[32mUpdating debug rootfs \033[0m"
	$(Q)for i in $(DEBUG_DIRS); do \
		rm -rf $(NFSROOT)/$$i/* ; \
		cp -ra $(TARGET)/$$i/* $(NFSROOT)/$$i ; \
	done

.PHONY: debug_setup debug

endif
##########################################################################
