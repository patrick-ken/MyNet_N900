-include ../arch.mk
-include ../../path.mk
-include $(TOPDIR)/.config


all:  inotify mkend  


inotify:
	$(Q)make -k inotify0 

inotify0:
	$(Q)if [ ! -f ./.configured ]; then \
	cd inotify-tools-3.13 && ./configure --prefix=$(TARGET)/lib --build=i386-linux --host=$(HOST_TYPE) CC=$(CC); \
	make -k -f Makefile; patch ./configure < ./patch/configure.patch; make -k -f Makefile; \
	fi
	make -C inotify-tools-3.13

mkend:
	touch ./.configured;

install:	
	cp -f inotify-tools-3.13/src/.libs/inotifywait $(TARGET)/usr/sbin/.	
	$(STRIP) inotify-tools-3.13/libinotifytools/src/.libs/libinotifytools.so.0
	cp -f inotify-tools-3.13/libinotifytools/src/.libs/libinotifytools.so.0 $(TARGET)/lib/.

clean:
	make -C inotify-tools-3.13 clean
	rm .configured
	
clean_config:
	rm .configured

.PHONY: all install clean
