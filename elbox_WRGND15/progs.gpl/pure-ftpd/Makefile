-include ../arch.mk
-include ../../path.mk
-include ../../.config 

ifeq ($(strip $(ELBOX_PROGS_GPL_LIBICONV_1_9_1)),y)
	CFLAGS  += -I$(TOPDIR)/progs.gpl/libiconv/libiconv-1.9.1/include
	LDFLAGS += -liconv
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv/libiconv-1.9.1/lib/.libs
endif
ifeq ($(strip $(ELBOX_PROGS_GPL_LIBICONV_1_13_1)),y)
	CFLAGS  += -I$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/include
	LDFLAGS += -liconv
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/lib
	LDFLAGS += -L$(TOPDIR)/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/lib/.libs
endif

all:
	cd pure-ftpd-1.0.21 && ./configure --host=$(HOST_TYPE) CC=$(CC) AS=$(AS) AR=$(AR) \
	LD=$(LD) RANLIB=$(RANLIB) STRIP=$(STRIP)  CFLAGS="$(CFLAGS)" \
	LDFLAGS="$(LDFLAGS)" --prefix=$(TARGET)/sbin \
	--with-throttling --with-largefile --with-sysquotas --with-virtualchroot --with-rfc2640 
	make -C pure-ftpd-1.0.21
	
install:
	
	cp pure-ftpd-1.0.21/src/pure-ftpd $(TARGET)/sbin/.
	ln -s /var/ftp_tbl $(TARGET)/etc/ftp_tbl
	ln -s /var/ftpgroup $(TARGET)/etc/ftpgroup
clean:
	make -C pure-ftpd-1.0.21/ clean ;
