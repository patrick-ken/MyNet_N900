#
# wrapper of Synopsys DWC OTG linux driver makefiles
#
.EXPORT_ALL_VARIABLES:

BUS_INTERFACE	:=
PCI_INTERFACE	:= 0

#  
# Select the mode of the driver to optimize out unnecessary code
#
CONFIG_DWC_MODE_HOST_ONLY=y
ifdef CONFIG_DWC_MODE_HOST_ONLY
	CPPFLAGS += -DDWC_HOST_ONLY
endif

ifdef CONFIG_DWC_MODE_DEVICE_ONLY
	CPPFLAGS += -DDWC_DEVICE_ONLY
endif

ifeq ($(CONFIG_DWC_DEBUG),y)
	CPPFLAGS += -DDEBUG
endif

obj-$(CONFIG_DWC_MODE_OTG) += dwc_common_port/
obj-$(CONFIG_DWC_MODE_OTG) += dwc_perf/
obj-$(CONFIG_DWC_MODE_OTG) += driver/

clean:
	find . \( -name '*.o' -o -name '*.ko' -o -name '.*cmd' -o -name '*.mod.c' -o -name '.tmp_versions' -o -name 'm*.order' -o -name '*.symvers' \) -exec rm -f {} \;
