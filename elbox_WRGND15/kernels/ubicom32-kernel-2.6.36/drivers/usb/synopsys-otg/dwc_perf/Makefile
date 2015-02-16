#
# Makefile for DWC_common library
#

#CPPFLAGS	+= -DDEBUG_MEMORY
#CPPFLAGS	+= -DDEBUG
CPPFLAGS	+= -DDWC_LINUX

#
# convert CPPFLAGS to kernel kbuild format
#
ccflags-y += $(CPPFLAGS)

obj-$(CONFIG_UBICOM32_STOPWATCH_USB)	:= dwc_perf_lib.o
dwc_perf_lib-objs	:= dwc_otg_watch.o

