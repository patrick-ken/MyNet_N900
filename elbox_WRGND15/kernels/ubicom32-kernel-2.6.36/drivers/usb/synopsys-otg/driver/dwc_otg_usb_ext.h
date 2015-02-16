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


/*------------------------------------------------------------------------*/
/*--- this fragment is a USB Gadget API extenstion for processing --------*/
/*--- Isochronous transfers in specific way ------------------------------*/
/*------------------------------------------------------------------------*/

#define USB_REQ_ISO_ASAP 0x0002

struct usb_iso_request;


struct usb_gadget_iso_packet_descriptor
{
        unsigned int            offset;
        unsigned int            length; /* expected length  */
        unsigned int            actual_length;
        unsigned int            status;
};

struct usb_iso_request {
        void                    *buf0;
        void                    *buf1;
        dma_addr_t              dma0;
        dma_addr_t              dma1;

        uint32_t                buf_proc_intrvl;

        unsigned                no_interrupt:1;
        unsigned                zero:1;
        unsigned                short_not_ok:1;

        uint32_t                sync_frame;
        uint32_t                data_per_frame;
        uint32_t                data_pattern_frame;
        uint32_t                start_frame;
        uint32_t                flags;

        void                    (*process_buffer)(struct usb_ep*,
                                            struct usb_iso_request*);

        void                    *context;

        int                     status;

        struct usb_gadget_iso_packet_descriptor *iso_packet_desc0;
        struct usb_gadget_iso_packet_descriptor *iso_packet_desc1;
};


struct usb_isoc_ep_ops {
        struct                          usb_ep_ops ep_ops;

        int                             (*iso_ep_start)(struct usb_ep*, struct usb_iso_request*, gfp_t);
        int                             (*iso_ep_stop)(struct usb_ep*, struct usb_iso_request*);

        struct
        usb_iso_request*                (*alloc_iso_request)(struct usb_ep* ep, int packets, gfp_t gfp_flags);
        void                            (*free_iso_request)(struct usb_ep* ep, struct usb_iso_request *req);
};

static inline int usb_iso_ep_start(struct usb_ep* ep, struct usb_iso_request* iso_req, gfp_t gfp_flags)
{
        struct usb_isoc_ep_ops *isoc_ops = (struct usb_isoc_ep_ops*)ep->ops;
        return isoc_ops->iso_ep_start(ep, iso_req, gfp_flags);
}

static inline int usb_iso_ep_stop(struct usb_ep* ep, struct usb_iso_request* iso_req)
{
        struct usb_isoc_ep_ops *isoc_ops = (struct usb_isoc_ep_ops*)ep->ops;
        return isoc_ops->iso_ep_stop(ep, iso_req);
}

static inline struct usb_iso_request *usb_alloc_iso_request(struct usb_ep* ep, int packets, gfp_t gfp_flags)
{
        struct usb_isoc_ep_ops *isoc_ops = (struct usb_isoc_ep_ops*)ep->ops;
        return isoc_ops->alloc_iso_request(ep, packets, gfp_flags);
}


static inline void usb_free_iso_request(struct usb_ep* ep, struct usb_iso_request *req)
{
        struct usb_isoc_ep_ops *isoc_ops = (struct usb_isoc_ep_ops*)ep->ops;
        return isoc_ops->free_iso_request(ep, req);
}

/*------------------------------------------------------------------------*/

