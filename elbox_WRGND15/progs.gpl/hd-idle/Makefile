#
#  sg3_utils for WEG-ND06
#
-include ../arch.mk
-include ../../path.mk
include $(TOPDIR)/.config
#####################################################################
# source files
MYNAME      := HD-IDLE for Aries
APPLET      := hd-idle
LOCAL_SRCS  := hd-idle.c
CMLIB_SRCS  :=
CPLUS_SRCS  :=

OTHER_TARGETS:=
#####################################################################

-include $(TOPDIR)/Rules.mk

all: $(APPLET)

install:
ifeq ($(strip $(ELBOX_HD_IDLE)),y)
	@echo -e "\033[32mInstalling $(MYNAME) ...\033[0m"
	$(Q)cp ./$(APPLET) $(TARGET)/usr/sbin/.
endif

clean:
	$(Q)rm -f $(APPLET) *.o .depend

.PHONY: all install clean
#####################################################################
