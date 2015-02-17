##############################################################################
# Makefile for OpenSSH
# Created by Yi-hung <yi_hung@alphanetworks.com>
##############################################################################
-include ../arch.mk
-include ../../path.mk
-include ../../.config

VERBOSE = > /dev/null
CUR_DIR := $(shell pwd)
PREFIX :=
APPLET := sshd
TARBALL := openssh-5.5p1.tar.gz
PATCH := openssh-5.5p1.diff 
SRC_DIR := openssh-5.5p1
MYJOBS = -j$(shell grep -c processor /proc/cpuinfo)

.PHONY: all
all: prepare_source
	$(Q)echo -e "\033[32mBuilding $(APPLET) ...\033[0m";
	$(Q)if test ! -f $(SRC_DIR)/Makefile; then \
		make prepare_source; \
		make _config_; \
		make -C $(SRC_DIR) $(MYJOBS) $(VERBOSE); \
	else \
		make -C $(SRC_DIR) $(MYJOBS) $(VERBOSE); \
	fi

.PHONY: prepare_source
ifeq ($(SRC_DIR), $(wildcard $(SRC_DIR)))
prepare_source:
else
prepare_source:
	$(Q)echo -e "\033[32mExtracting $(APPLET) source codes ...\033[0m";
	$(Q)tar zxf $(TARBALL)
	$(Q)if test ! -z $(PATCH); then \
		cat $(PATCH) | patch -p1; \
	fi
endif

ifeq ($(strip $(HOST_CPU)),ubicom32)
LD =
endif
SSH_OPTS := --build=$(shell uname -m)-unknown-linux-gnu
SSH_OPTS += --host=$(HOST_TYPE)
SSH_OPTS += --prefix=$(PREFIX)
ifeq ($(ELBOX_PROGS_GPL_ZLIB_123),y)
SSH_OPTS += --with-zlib=$(TOPDIR)/progs.gpl/zlib-1.2.3
endif
ifeq ($(ELBOX_PROGS_GPL_OPENSSL_098G),y)
SSH_OPTS += --with-ssl-dir=$(TOPDIR)/progs.gpl/openssl-0.9.8g/build/openssl-0.9.8g
endif
ifeq ($(ELBOX_PROGS_GPL_OPENSSL_098R),y)
SSH_OPTS += --with-ssl-dir=$(TOPDIR)/progs.gpl/openssl-0.9.8r/build/openssl-0.9.8r
endif
ifeq ($(ELBOX_PROGS_GPL_OPENSSL_100E),y)
SSH_OPTS += --with-ssl-dir=$(TOPDIR)/progs.gpl/openssl-1.0.0e/build/openssl-1.0.0e
endif
ifeq ($(strip $(ELBOX_USE_IPV6)),y)
SSH_OPTS += "--with-4in6"
endif

ifneq ($(strip $(ELBOX_PROGS_GPL_OPENSSH_RUNTIME_LIBRARY_PATH)),"")
LDFLAGS += -Wl,$(ELBOX_PROGS_GPL_OPENSSH_RUNTIME_LIBRARY_PATH)
endif

SSH_OPTS += CFLAGS="$(CFLAGS)"
SSH_OPTS += LDFLAGS="$(LDFLAGS)"

.PHONY: _config_
_config_:
	$(Q)cd $(SRC_DIR); $(CUR_DIR)/$(SRC_DIR)/configure $(SSH_OPTS) $(VERBOSE)

.PHONY: clean
clean:
	$(Q)echo -e "\033[32mCleaning $(APPLET) ...\033[0m"
	$(Q)if test -f $(SRC_DIR)/Makefile; then \
		make -C $(SRC_DIR) clean; \
	fi
	$(Q)make distclean;

.PHONY: distclean
distclean:
	$(Q)if test -d $(SRC_DIR); then rm -rf $(SRC_DIR); fi;
	$(Q)if test -d ori; then rm -rf ori; fi;

.PHONY: install
install:
	$(Q)echo -e "\033[32mInstalling $(APPLET) ...\033[0m"
	$(Q)cp $(SRC_DIR)/$(APPLET) $(TARGET)/usr/sbin/$(APPLET)
	$(Q)$(STRIP) $(TARGET)/usr/sbin/$(APPLET)
	$(Q)[ -d $(TARGET)/etc/ssh ] || mkdir -p $(TARGET)/etc/ssh
	$(Q)if [ ! -f $(TARGET)/etc/ssh/ssh_host_key ]; then \
		ssh-keygen -t rsa1 -f $(TARGET)/etc/ssh/ssh_host_key -N ""; \
	else \
		echo -e "\033[32mssh_host_key already exists, skipping.\033[0m"; \
	fi
	$(Q)if [ ! -f $(TARGET)/etc/ssh/ssh_host_dsa_key ]; then \
		ssh-keygen -t dsa -f $(TARGET)/etc/ssh/ssh_host_dsa_key -N ""; \
	else \
		echo -e "\033[32mssh_host_dsa_key already exists, skipping.\033[0m"; \
	fi
	$(Q)if [ ! -f $(TARGET)/etc/ssh/ssh_host_rsa_key ]; then \
		ssh-keygen -t rsa -f $(TARGET)/etc/ssh/ssh_host_rsa_key -N ""; \
	else \
		echo -e "\033[32mssh_host_rsa_key already exists, skipping.\033[0m"; \
	fi
	$(Q)chmod 664 ./openssh-5.5p1/moduli
	$(Q)cp ./openssh-5.5p1/moduli		$(TARGET)/etc/ssh/.
ifneq ($(strip $(ELBOX_TEMPLATE_ARIES_ENABLE_USER_MANAGEMENT)),y)
	$(Q)cp ./passwd $(TARGET)/etc/.
endif

.PHONY: diff
diff:
	@mkdir -p ori; \
	tar zxf $(TARBALL) -C ./ori; \
	if test -f $(SRC_DIR)/Makefile; then \
		make -C $(SRC_DIR) distclean; \
	fi; \
	./mkdiff  ori/$(SRC_DIR) $(SRC_DIR) > $(PATCH)
	

.PHONY: showconfig
showconfig:
	$(Q)echo CC=$(CC)
	$(Q)echo CFLAGS=$(CFLAGS)
	$(Q)echo LDFLAGS=$(LDFLAGS)
	$(Q)echo STRIP=$(STRIP)
	$(Q)echo TOPDIR=$(TOPDIR)
	$(Q)echo TARGET=$(TARGET)
	$(Q)echo "/tmp/$(USER)/$(shell echo $(ELBOX_MODEL_NAME))_path.mk"
	$(Q)echo SSH_OPTS=$(SSH_OPTS)
