# Makefile for libiconv/src

#### Start of system configuration section. ####

# Directories used by "make":
srcdir = .
top_srcdir = ..

# Directories used by "make install":
prefix = /home/jeremy_lin/project/GPL/0514_pro/elbox_WRGND15/build
local_prefix = /usr/local
exec_prefix = ${prefix}
bindir = ${exec_prefix}/bin
libdir = ${exec_prefix}/lib
datarootdir = ${prefix}/share
datadir = ${datarootdir}
localedir = ${datarootdir}/locale

# Programs used by "make":
CC = ubicom32-linux-uclibc-gcc
CFLAGS = -Os -Wall -D_LARGEFILE_SOURCE -D_LARGEFILE64_SOURCE -D_FILE_OFFSET_BITS=64 -D__ARCH_USE_MMU__ -D__UCLIBC__ -fno-strict-aliasing -pipe -march=ubicom32v5 -DUBICOM32_ARCH_VERSION=5 -DIP8000 -Dubicom32 -I/opt/ubicom32_sdk32x_gcc-4.4.1_uclibc-0.9.30.1_v02/ubicom32-linux-uclibc/runtime/usr/include -mfdpic -muclibc -fmath-errno -ftrapping-math -fweb -mhard-float -Os -Wall -D_LARGEFILE_SOURCE -D_LARGEFILE64_SOURCE -D_FILE_OFFSET_BITS=64 -D__ARCH_USE_MMU__ -D__UCLIBC__ -fno-strict-aliasing -pipe -march=ubicom32v5 -DUBICOM32_ARCH_VERSION=5 -DIP8000 -Dubicom32 -I/opt/ubicom32_sdk32x_gcc-4.4.1_uclibc-0.9.30.1_v02/ubicom32-linux-uclibc/runtime/usr/include -mfdpic -muclibc -fmath-errno -ftrapping-math -fweb -mhard-float
CPPFLAGS = 
LDFLAGS = -march=ubicom32v5
INCLUDES = -I. -I$(srcdir) -I.. -I../include -I$(srcdir)/../include -I../srclib -I$(srcdir)/../srclib
LIBTOOL = /bin/sh $(top_builddir)/libtool
LIBTOOL_COMPILE = $(LIBTOOL) --mode=compile
LIBTOOL_LINK = $(LIBTOOL) --mode=link
LIBTOOL_INSTALL = $(LIBTOOL) --mode=install
LIBTOOL_UNINSTALL = $(LIBTOOL) --mode=uninstall
WINDRES = 
RM = rm -f


# Programs used by "make install":
INSTALL = /usr/bin/install -c
INSTALL_PROGRAM = ${INSTALL}
INSTALL_DATA = ${INSTALL} -m 644
mkinstalldirs = $(SHELL) ../build-aux/mkinstalldirs

# Programs used by "make install-strip":
STRIP = ubicom32-linux-uclibc-strip
INSTALL_STRIP_PROGRAM = $(install_sh) -c -s
install_sh = ${SHELL} /home/jeremy_lin/project/GPL/0514_pro/elbox_WRGND15/progs.gpl/libiconv-1.13.1/libiconv-1.13.1/build-aux/install-sh

#### End of system configuration section. ####

SHELL = /bin/sh

PACKAGE_VERSION = 1.13.1

# Needed by $(LIBTOOL).
top_builddir = ..

# Needed by SET_RELOCATABLE.
EXEEXT = 

# Needed by RELOCATABLE_LDFLAGS.
host = ubicom32-unknown-linux-uclibc

OBJECTS_RES_yes = iconv.res
OBJECTS_RES_no =

# We cannot link with libintl until libiconv is installed. (When we call
# libtool with arguments "../lib/libiconv.la -lintl", libtool will call ld
# with "../lib/.libs/libiconv.so $libdir/libintl.so $libdir/libiconv.so",
# (remember that $libdir/libintl.la lists $libdir/libiconv.so as a dependency),
# and this gives a fatal linker error on Solaris because the two libiconv.so
# files are different but have the same soname.
# So we can link the iconv executable only after we have installed libiconv,
# i.e. during "make install". The intermediate 'iconv' executable is built
# without internationalization and not linked with libintl.

all : iconv_no_i18n iconv.o $(OBJECTS_RES_no)
	test `ls -ld . | sed -e 's/^d\(.........\).*/\1/'` = rwxrwxrwx || chmod 777 .

# This is the temporary iconv executable, without internationalization.
iconv_no_i18n : iconv_no_i18n.o ../lib/libiconv.la $(OBJECTS_RES_no)
	$(LIBTOOL_LINK) $(CC) $(LDFLAGS) $(CFLAGS) iconv_no_i18n.o ../srclib/libicrt.a ../lib/libiconv.la $(OBJECTS_RES_no) -o $@

iconv_no_i18n.o : $(srcdir)/iconv_no_i18n.c $(srcdir)/iconv.c
	$(CC) -c $(INCLUDES) -I../lib $(CFLAGS) $(CPPFLAGS) -DINSTALLDIR=\"$(bindir)\" -DLOCALEDIR=\"$(localedir)\" $(srcdir)/iconv_no_i18n.c

iconv.o : $(srcdir)/iconv.c
	$(CC) -c $(INCLUDES) -I../lib $(CFLAGS) $(CPPFLAGS) -DINSTALLDIR=\"$(bindir)\" -DLOCALEDIR=\"$(localedir)\" $(srcdir)/iconv.c

iconv.res : $(srcdir)/../windows/iconv.rc
	$(WINDRES) `$(SHELL) $(srcdir)/../windows/windres-options --escape $(PACKAGE_VERSION)` -i $(srcdir)/../windows/iconv.rc -o iconv.res --output-format=coff

# The following rule is necessary to avoid a toplevel "make -n check" failure.
../lib/libiconv.la :
	cd ../lib && $(MAKE) libiconv.la

# Support for relocatability.
RELOCATABLE_LIBRARY_PATH = $(libdir)
RELOCATABLE_SRC_DIR = $(top_srcdir)/srclib
RELOCATABLE_BUILD_DIR = ../srclib
RELOCATABLE_CONFIG_H_DIR = ..
RELOCATABLE_LDFLAGS = 
RELOCATABLE_STRIP = :
INSTALL_PROGRAM_ENV = 
iconv_LDFLAGS = `if test -n '$(RELOCATABLE_LDFLAGS)'; then $(RELOCATABLE_LDFLAGS) $(bindir); fi`

# During "make install", we can build the final iconv executable.
# On HP-UX, in order to ensure that the new libiconv.sl will override the old
# one that is hardcoded in libintl.sl, we need to mention libiconv.sl before
# libintl.sl on the link command line. We have to bypass libtool in order to
# achieve this.
# On Solaris, the linker gives an error if we are using libintl.so and it
# refers to a libiconv.so in $prefix/lib since then it sees two libiconv.so's,
# one in $prefix/lib and one in ../lib/.libs. So we have to avoid using
# ../lib/libiconv.la entirely.
install : all force
	if [ ! -d $(DESTDIR)$(bindir) ] ; then $(mkinstalldirs) $(DESTDIR)$(bindir) ; fi
	case "linux-uclibc" in \
	  hpux*) $(CC) $(LDFLAGS) $(CFLAGS) $(iconv_LDFLAGS) iconv.o ../srclib/libicrt.a -L$(DESTDIR)$(libdir) -liconv  $(OBJECTS_RES_no) `if test -n '$(DESTDIR)'; then echo " -Wl,+b -Wl,$(libdir)"; fi` -o iconv$(EXEEXT);; \
	  *) $(LIBTOOL_LINK) $(CC) $(LDFLAGS) $(CFLAGS) $(iconv_LDFLAGS) iconv.o ../srclib/libicrt.a $(DESTDIR)$(libdir)/libiconv.la  $(OBJECTS_RES_no) -o iconv$(EXEEXT);; \
	esac
	$(INSTALL_PROGRAM_ENV) $(LIBTOOL_INSTALL) $(INSTALL_PROGRAM) iconv$(EXEEXT) $(DESTDIR)$(bindir)/iconv$(EXEEXT)

install-strip : force
	case '$(INSTALL_PROGRAM)' in \
	  */install-reloc) \
	    $(MAKE) install prefix='$(prefix)' exec_prefix='$(exec_prefix)' libdir='$(libdir)' RELOCATABLE_STRIP='$(STRIP)' ;; \
	  *) \
	    $(MAKE) install prefix='$(prefix)' exec_prefix='$(exec_prefix)' libdir='$(libdir)' INSTALL_PROGRAM='$(INSTALL_STRIP_PROGRAM)' ;; \
	esac

installdirs : force
	if [ ! -d $(DESTDIR)$(bindir) ] ; then $(mkinstalldirs) $(DESTDIR)$(bindir) ; fi

uninstall : force
	$(LIBTOOL_UNINSTALL) $(RM) $(DESTDIR)$(bindir)/iconv$(EXEEXT)

check : all

mostlyclean : clean

clean : force
	$(RM) *.o *.lo iconv.res iconv_no_i18n iconv_no_i18n$(EXEEXT) iconv$(EXEEXT) core *.stackdump
	$(RM) -r .libs _libs

distclean : clean
	$(RM) Makefile

maintainer-clean : distclean

force :

