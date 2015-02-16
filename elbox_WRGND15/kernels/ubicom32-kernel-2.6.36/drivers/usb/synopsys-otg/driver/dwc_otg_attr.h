/*
 * (C) Copyright 2010, Ubicom, Inc.
 *
 * This file is part of the Ubicom32 Linux Kernel Port.
 *
 * The Ubicom32 Linux Kernel Port is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 2 of the
 * License, or (at your option) any later version.
 *
 * The Ubicom32 Linux Kernel Port is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Ubicom32 Linux Kernel Port.  If not,
 * see <http://www.gnu.org/licenses/>.
 *
 */

/* ==========================================================================
 * $File: //dwh/usb_iip/dev/software/otg/linux/drivers/dwc_otg_attr.h $
 * $Revision: #11 $
 * $Date: 2009/04/03 $
 * $Change: 1225160 $
 *
 * Synopsys HS OTG Linux Software Driver and documentation (hereinafter,
 * "Software") is an Unsupported proprietary work of Synopsys, Inc. unless
 * otherwise expressly agreed to in writing between Synopsys and you.
 * 
 * The Software IS NOT an item of Licensed Software or Licensed Product under
 * any End User Software License Agreement or Agreement for Licensed Product
 * with Synopsys or any supplement thereto. You are permitted to use and
 * redistribute this Software in source and binary forms, with or without
 * modification, provided that redistributions of source code must retain this
 * notice. You may not view, use, disclose, copy or distribute this file or
 * any information contained herein except pursuant to this license grant from
 * Synopsys. If you do not agree with this notice, including the disclaimer
 * below, then you are not authorized to use the Software.
 * 
 * THIS SOFTWARE IS BEING DISTRIBUTED BY SYNOPSYS SOLELY ON AN "AS IS" BASIS
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE HEREBY DISCLAIMED. IN NO EVENT SHALL SYNOPSYS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 * ========================================================================== */

#if !defined(__DWC_OTG_ATTR_H__)
#define __DWC_OTG_ATTR_H__

/** @file
 * This file contains the interface to the Linux device attributes.
 */
extern struct device_attribute dev_attr_regoffset;
extern struct device_attribute dev_attr_regvalue;

extern struct device_attribute dev_attr_mode;
extern struct device_attribute dev_attr_hnpcapable;
extern struct device_attribute dev_attr_srpcapable;
extern struct device_attribute dev_attr_hnp;
extern struct device_attribute dev_attr_srp;
extern struct device_attribute dev_attr_buspower;
extern struct device_attribute dev_attr_bussuspend;
extern struct device_attribute dev_attr_busconnected;
extern struct device_attribute dev_attr_gotgctl;
extern struct device_attribute dev_attr_gusbcfg;
extern struct device_attribute dev_attr_grxfsiz;
extern struct device_attribute dev_attr_gnptxfsiz;
extern struct device_attribute dev_attr_gpvndctl;
extern struct device_attribute dev_attr_ggpio;
extern struct device_attribute dev_attr_guid;
extern struct device_attribute dev_attr_gsnpsid;
extern struct device_attribute dev_attr_devspeed;
extern struct device_attribute dev_attr_enumspeed;
extern struct device_attribute dev_attr_hptxfsiz;
extern struct device_attribute dev_attr_hprt0;
#ifdef CONFIG_USB_DWC_OTG_LPM
extern struct device_attribute dev_attr_lpm_response;
extern struct device_attribute dev_attr_sleep_local_dev;
extern struct device_attribute devi_attr_sleep_status;
#endif

void dwc_otg_attr_create (
#ifdef LM_INTERFACE
	struct lm_device *dev
#elif  PCI_INTERFACE
	struct pci_dev *dev
#endif
	);

void dwc_otg_attr_remove (
#ifdef LM_INTERFACE
	struct lm_device *dev
#elif  PCI_INTERFACE
	struct pci_dev *dev
#endif
	);
#endif
