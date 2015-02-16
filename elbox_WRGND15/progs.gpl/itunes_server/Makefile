-include ../arch.mk
-include ../../path.mk
-include $(TOPDIR)/.config

VERBOSE = > /dev/null
pwd = $(shell pwd)

COMM_CONFS := --build=$(shell uname -m)-unknown-linux-gnu --prefix=$(TARGET) --host=$(HOST_TYPE)
MT_CONFS := --enable-gdbm --disable-rpath --disable-mdns
CFLAGS += -I$(pwd)/gdbm-1.8.3
CFLAGS += -I$(pwd)/libid3tag-0.15.0b
LDFLAGS += -L$(pwd)/gdbm-1.8.3 -L$(pwd)/gdbm-1.8.3/.libs
LDFLAGS += -L$(pwd)/libid3tag-0.15.0b -L$(pwd)/libid3tag-0.15.0b/.libs

ifeq ($(strip $(ELBOX_PROGS_GPL_ZLIB_123)),y)
	CFLAGS  += -I$(TOPDIR)/progs.gpl/zlib-1.2.3
	LDFLAGS += -L$(TOPDIR)/progs.gpl/zlib-1.2.3
else
	CFLAGS  += -I$(pwd)/zlib-1.2.1
	LDFLAGS += -L$(pwd)/zlib-1.2.1
endif

ifeq ($(strip $(ELBOX_PROGS_GPL_LIBICONV_1_9_1)),y)
	CFLAGS  += -I$(TOPDIR)/progs.gpl/libiconv/libiconv-1.9.1/include
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv/libiconv-1.9.1/lib
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv/libiconv-1.9.1/lib/.libs
	MT_CONFS += --enable-iconv
endif

ifeq ($(strip $(ELBOX_PROGS_GPL_LIBICONV_1_13_1)),y)
	CFLAGS  += -I$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/include
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/lib
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/lib/.libs
	MT_CONFS += --enable-iconv
endif

ifeq ($(strip $(ELBOX_PROGS_GPL_SQLITE_374)),y)
	SQLITE3_INCS = $(TOPDIR)/progs.gpl/sqlite-3.7.4/build/sqlite-autoconf-3070400
	SQLITE3_LIBS = $(TOPDIR)/progs.gpl/sqlite-3.7.4/rootfs/lib
	MT_CONFS += --enable-sqlite3 --with-sqlite3-includes=$(SQLITE3_INCS) --with-sqlite3-libs=$(SQLITE3_LIBS)
endif

ifeq ($(strip $(ELBOX_PROGS_GPL_OPENSSL_098G)),y)
	MT_CONFS += --with-ssl-headers=$(TOPDIR)/progs.gpl/openssl-0.9.8g/build/openssl-0.9.8g/include
	MT_CONFS += --with-ssl-lib=$(TOPDIR)/progs.gpl/openssl-0.9.8g/build/openssl-0.9.8g
	MT_CONFS += --enable-ssl
endif

COMM_CONFS += CFLAGS="$(CFLAGS)"
COMM_CONFS += LDFLAGS="$(LDFLAGS) -lgcc_s"

all: zlib gdbm id3tag mtdaapd mkend

zlib:
ifeq ($(strip $(ELBOX_ZLIB)),)
	make -C zlib-1.2.1/
	cp zlib-1.2.1/zlib.h mt-daapd-0.2.4/src
	cp zlib-1.2.1/zconf.h mt-daapd-0.2.4/src
else
	$(Q)echo "Use common zlib."
endif

gdbm:
	if [ ! -f ./.configured ]; then \
	cd gdbm-1.8.3 && ./configure $(COMM_CONFS) --enable-shared --enable-static; \
	fi
	make -C gdbm-1.8.3 $(VERBOSE)

id3tag:
	if [ ! -f ./.configured ]; then \
	cd libid3tag-0.15.0b && ./configure $(COMM_CONFS) CPPFLAGS="$(CFLAGS)" --enable-shared --enable-static;\
	cp -f ./patch/frametype.c ./; \
	cp -f ./patch/compat.c ./; \
	fi
	make -C libid3tag-0.15.0b $(VERBOSE)

mtdaapd:
	if [ ! -f ./.configured ]; then \
	cd mt-daapd-svn-1696 && ./configure $(COMM_CONFS) $(MT_CONFS); \
	fi
	make -C mt-daapd-svn-1696 $(VERBOSE)

mkend:
	touch ./.configured;

install:
	cp -f libid3tag-0.15.0b/.libs/libid3tag.so.0.2.0 $(TARGET)/lib/libid3tag.so.0
	cp -f gdbm-1.8.3/.libs/libgdbm.so.3.0.0 $(TARGET)/lib/libgdbm.so.3
	mkdir -p $(TARGET)/etc/admin-root
	touch $(TARGET)/etc/admin-root/index.html
	$(STRIP) mt-daapd-svn-1696/src/.libs/mt-daapd
	cp mt-daapd-svn-1696/src/.libs/mt-daapd $(TARGET)/sbin/.
	cp mt-daapd-svn-1696/src/plugins/.libs/out-daap.so $(TARGET)/lib/.
	ln -f -s /var/mt-daapd.conf $(TARGET)/etc/mt-daapd.conf

clean:
	-make -C gdbm-1.8.3 distclean >/dev/null 2>&1
	rm -f gdbm-1.8.3/gdbm.info
	rm -f gdbm-1.8.3/autoconf.h
	rm -f gdbm-1.8.3/gdbm.h
	-make -C libid3tag-0.15.0b distclean >/dev/null 2>&1
	-make -C mt-daapd-0.2.4 distclean >/dev/null 2>&1
	make -C zlib-1.2.1/ clean
	rm -f .configured

clean_config:
	rm -f .configured

.PHONY: all install clean

