#
# Variables definition
#

#####################################################################
# Image signature
ELBOX_SIGNATURE := ""
ifneq ($(strip $(ELBOX_FIRMWARE_SIGNATURE)), "N/A")
ELBOX_SIGNATURE := $(shell echo $(ELBOX_FIRMWARE_SIGNATURE))
endif
ifeq ($(strip $(ELBOX_SIGNATURE)), "")
ELBOX_SIGNATURE := $(shell echo $(ELBOX_BOARD_NAME)_$(ELBOX_BRAND_NAME)_$(ELBOX_MODEL_NAME))
endif
TFTPIMG			:= $(shell echo tftp_$(ELBOX_SIGNATURE).bin)

#####################################################################
# Language
LANGUAGE =
ifeq ($(strip $(ELBOX_LANGUAGE_EN)),y)
LANGUAGE += en
endif
ifeq ($(strip $(ELBOX_LANGUAGE_JA)),y)
LANGUAGE += ja
endif
ifeq ($(strip $(ELBOX_LANGUAGE_DE)),y)
LANGUAGE += de
endif
ifeq ($(strip $(ELBOX_LANGUAGE_ZH_TW)),y)
LANGUAGE += zhtw
endif
ifeq ($(strip $(ELBOX_LANGUAGE_ZH_CN)),y)
LANGUAGE += zhcn
endif
ifeq ($(strip $(ELBOX_LANGUAGE_FR)),y)
LANGUAGE += fr
endif
ifeq ($(strip $(ELBOX_LANGUAGE_ES)),y)
LANGUAGE += es
endif
ifeq ($(strip $(ELBOX_LANGUAGE_IT)),y)
LANGUAGE += it
endif
ifeq ($(strip $(ELBOX_LANGUAGE_NL)),y)
LANGUAGE += nl
endif
ifeq ($(strip $(ELBOX_LANGUAGE_PT)),y)
LANGUAGE += pt
endif
ifeq ($(strip $(ELBOX_LANGUAGE_FI)),y)
LANGUAGE += fi
endif
ifeq ($(strip $(ELBOX_LANGUAGE_SV)),y)
LANGUAGE += sv
endif
ifeq ($(strip $(ELBOX_LANGUAGE_NO)),y)
LANGUAGE += no
endif
ifeq ($(strip $(ELBOX_LANGUAGE_DA)),y)
LANGUAGE += da
endif
ifeq ($(strip $(ELBOX_LANGUAGE_KO)),y)
LANGUAGE += ko
endif
ifeq ($(strip $(ELBOX_LANGUAGE_RU)),y)
LANGUAGE += ru
endif
