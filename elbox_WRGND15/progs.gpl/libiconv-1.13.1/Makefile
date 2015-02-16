-include ../arch.mk
-include ../../path.mk
-include $(TOPDIR)/.config


pwd = $(shell pwd)
BUILD=$(TOPDIR)/build

all: iconv mkend  

iconv:	
	if [ ! -f ./.configured ]; then \
	cd libiconv-1.13.1 && /bin/sh configure --prefix=$(BUILD) --host=$(HOST_TYPE) CC=$(CC) AS=$(AS) AR=$(AR) \
	LD=$(LD) RANLIB=$(RANLIB) STRIP=$(STRIP) --enable-static LDFLAGS="${LDFLAGS}" CFLAGS="${CFLAGS}" ;\
	fi	
	make -C libiconv-1.13.1 
	make -C libiconv-1.13.1 install
	cp $(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/lib/.libs/* $(BUILD)/lib/.
	
mkend:
	touch ./.configured;

install:
	if test ! -s "$(TARGET)/lib/libiconv.so.2.5.0"; \
	then cp libiconv-1.13.1/lib/.libs/libiconv.so.2.5.0 $(TARGET)/lib/. ;\
	ln -s -f libiconv.so.2.5.0 $(TARGET)/lib/libiconv.so.2 ;\
	fi
	
clean:
	make -C libiconv-1.13.1 clean
	rm .configured

.PHONY: all install clean
