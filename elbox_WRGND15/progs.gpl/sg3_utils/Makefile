#
#  sg3_utils for WEG-ND06
#
-include ../arch.mk
-include ../../path.mk
include $(TOPDIR)/.config
#####################################################################
# source files
MYNAME      := sg3_utils-1.32
APPLET      := sg_inq
LOCAL_SRCS  :=
CMLIB_SRCS  :=
CPLUS_SRCS  :=

OTHER_TARGETS:=
#####################################################################

all:
ifeq ($(strip $(ELBOX_SG3_UTILS)),y)
	@echo -e "\033[32mBuilding $(MYNAME) ...\033[0m"
	#$(Q)source ../../boards/wrgnd16/env.rc;
	tar xfvz $(MYNAME).tgz
	$(Q)cd $(MYNAME);./configure --host=$(HOST_TYPE) CFLAGS="-O2 -static";
	$(Q)cd $(MYNAME); make -f Makefile
endif

install:
ifeq ($(strip $(ELBOX_SG3_UTILS)),y)
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)cp ./$(MYNAME)/src/sg_inq $(TARGET)/usr/sbin/.
	$(Q)cp ./$(MYNAME)/src/sg_readcap $(TARGET)/usr/sbin/.
	$(Q)cp ./$(MYNAME)/src/sg_map $(TARGET)/usr/sbin/.
endif

clean:
	$(Q)cd $(MYNAME); rm -f *.o ./lib/*.o ./src/*.o .depend

distclean:
	$(Q) rm -rf $(MYNAME)
.PHONY: all install clean distclean
