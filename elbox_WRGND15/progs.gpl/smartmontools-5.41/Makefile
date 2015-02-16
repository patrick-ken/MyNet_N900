.EXPORT_ALL_VARIABLES:
-include ../arch.mk
-include ../../path.mk
-include ../../.config

CUR_DIR := $(shell pwd)
PREFIX :=
APPLET := smartd smartctl
TARBALL := $(CUR_DIR)/smartmontools-5.41.tar.gz
PATCH := $(CUR_DIR)/smartmontools-5.41.diff
SRC_DIR := smartmontools-5.41
OPTS :=

.PHONY: all
all: prepare_source
	@echo -e "\033[32mBuilding $(APPLET) ...\033[0m";
	@if test ! -f $(SRC_DIR)/Makefile; then \
		make prepare_source; \
		make _config_; \
		make -C $(SRC_DIR); \
	else \
		make -C $(SRC_DIR); \
	fi

.PHONY: prepare_source
ifeq ($(SRC_DIR), $(wildcard $(SRC_DIR)))
prepare_source:
else
prepare_source:
	@echo -e "\033[32mExtracting $(APPLET) source codes ...\033[0m";
	@tar zxf $(TARBALL)
	@if test ! -z $(PATCH); then \
		cat $(PATCH) | patch -p1; \
	fi
endif

.PHONY: _config_
_config_:
	@cd $(SRC_DIR); \
		$(CUR_DIR)/$(SRC_DIR)/configure \
			--build=i686-pc-linux-gnu \
			--host=$(HOST_TYPE) \
			--prefix=$(PREFIX) \
			CFLAGS="${CFLAGS}" \
			LDFLAGS="${LDFLAGS} -static" \
			CPPFLAGS="${CFLAGS} ${LDFLAGS}" \
			$(OPTS)

.PHONY: clean
clean:
	@echo -e "\033[32mCleaning $(APPLET) ...\033[0m"
	@if test -f $(SRC_DIR)/Makefile; then \
		make -C $(SRC_DIR) clean; \
	fi
	@make distclean;

.PHONY: distclean
distclean:
	@if test -d $(SRC_DIR); then rm -rf $(SRC_DIR); fi;
	@if test -d ori; then rm -rf ori; fi;

.PHONY: install
install:
ifeq ($(strip $(PREFIX)),)
	@echo -e "\033[32mInstalling $(APPLET) ...\033[0m"
	@for i in $(APPLET); do \
		cp $(SRC_DIR)/$$i $(TARGET)/usr/sbin/.; \
		$(STRIP) $(TARGET)/usr/sbin/$$i; \
		done
else
	@echo -e "\033[32mInstalling $(APPLET) onto $(PREFIX) ...\033[0m"
	@make -C $(SRC_DIR) install
endif

.PHONY: uninstall
uninstall:
ifneq ($(strip $(PREFIX)),)
	@echo -e "\033[32mUninstalling $(APPLET) on $(PREFIX) ...\033[0m"
	@make -C $(SRC_DIR) uninstall
else
	@for i in $(APPLET); do \
		rm -f $$i; \
		done
endif

.PHONY: diff
diff:
	@mkdir -p ori; \
	tar zxf $(TARBALL) -C ./ori; \
	if test -f $(SRC_DIR)/Makefile; then \
		make -C $(SRC_DIR) distclean; \
	fi; \
	./mkdiff ori/$(SRC_DIR) $(SRC_DIR) > $(PATCH)

.PHONY: info
info:
	@echo "APPLET: $(APPLET)";
	@echo "Options: $(OPTS)";
