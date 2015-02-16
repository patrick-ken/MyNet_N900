-include ../arch.mk
-include ../../path.mk
-include ../../.config

APPLET		= json
JSON_PATH	= json-c-0.9
PREFIX		= /lib

all: json
json: 
	$(Q)if [ ! -f ./$(JSON_PATH)/Makefile ]; then \
		echo -e "\033[32mjson hasn't be config before, configuring...\033[0m"; \
		cd ./$(JSON_PATH); \
		./configure --build=i386-linux --host=$(HOST_TYPE) CFLAGS="$(CFLAGS)"; \
	fi
	@echo -e "\033[32mCompiling json ...\033[0m"
	$(Q)make -C $(JSON_PATH) 

install:
	@echo -e "\033[32mInstalling json ...\033[0m"
	$(Q)[ -d $(TARGET)$(PREFIX) ] || mkdir -p $(TARGET)$(PREFIX)
	$(Q)cp -avf ./json-c-0.9/.libs/*.so* $(TARGET)$(PREFIX)
	

clean:
	@echo -e "\033[32mCleaning json ...\033[0m"
	$(Q)make clean -C $(JSON_PATH) 
	$(Q)find ./$(JSON_PATH) -name Makefile | xargs rm -rf

.PHONY: all install clean

