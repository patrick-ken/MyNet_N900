-include ../arch.mk
-include ../../path.mk
-include ../../.config

APPLET		= sdparm
SDPARM_PATH	= sdparm-1.03
PREFIX		= /usr/sbin

all: sdparm
sdparm: 
	$(Q)if [ ! -f ./$(SDPARM_PATH)/Makefile ]; then \
		echo -e "\033[32msdparm hasn't be config before, configuring...\033[0m"; \
		cd ./$(SDPARM_PATH); \
		./configure --build=i386-linux --host=$(HOST_TYPE) CFLAGS="$(CFLAGS)"; \
	fi
	@echo -e "\033[32mCompiling sdparm ...\033[0m"
	$(Q)make -C $(SDPARM_PATH) 

install:
	@echo -e "\033[32mInstalling sdparm ...\033[0m"
	$(Q)$(STRIP) $(SDPARM_PATH)/src/sdparm
	$(Q)[ -d $(TARGET)$(PREFIX) ] || mkdir -p $(TARGET)$(PREFIX)
	$(Q)cp -f ./$(SDPARM_PATH)/src/sdparm $(TARGET)$(PREFIX)
	$(Q)chmod +x $(TARGET)$(PREFIX)/sdparm

clean:
	@echo -e "\033[32mCleaning sdparm ...\033[0m"
	$(Q)make clean -C $(SDPARM_PATH) 
	$(Q)find ./$(SDPARM_PATH) -name Makefile | xargs rm -rf
	$(Q)rm -rf ./$(SDPARM_PATH)/config.status
	$(Q)rm -rf ./$(SDPARM_PATH)/config.log 
	$(Q)rm -rf ./$(SDPARM_PATH)/config.h 
	$(Q)rm -rf ./$(SDPARM_PATH)/src/.deps 
	

.PHONY: all install clean

