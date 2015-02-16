
#include <linux/kernel.h>
#include <linux/module.h>
#include <linux/moduleparam.h>
#include <linux/init.h>
#include <linux/device.h>
#include <linux/errno.h>
#include <linux/types.h>
#include <linux/stat.h>		/* permission constants */
#include <linux/version.h>
#include <linux/interrupt.h>

#define __STOPWATCH_USE__ 
#include <asm/stopwatch.h>
#include "dwc_otg_watch.h"

struct stopwatch_instance usb_watches[NR_USB_WATCHES];
EXPORT_SYMBOL(usb_watches);

static int usb_watch_show(struct seq_file *p, void *v)
{
        char s[12];
        int usb_watch = *((loff_t *) v);

        if (usb_watch >= NR_USB_WATCHES) {
                return -1;
        }

        if (usb_watch == 0) {
                seq_puts(p, "\tmin\tavg\tmax\t(micro-seconds)\n");
        }

        sprintf(s, "%s", "test");
        stopwatch_show(&usb_watches[usb_watch], p, s, STOPWATCH_SHOW_MICRO);
        return 0;
}

static void *usb_watch_start(struct seq_file *f, loff_t *pos)
{
        return (*pos < NR_USB_WATCHES) ? pos : NULL;
}

static void *usb_watch_next(struct seq_file *f, void *v, loff_t *pos)
{
        (*pos)++;
        if (*pos > NR_USB_WATCHES)
                return NULL;
        return pos;
}

static void usb_watch_stop(struct seq_file *f, void *v)
{
        /* Nothing to do */
}


static const struct seq_operations usb_watch_seq_ops = {
        .start = usb_watch_start,
        .next  = usb_watch_next,
        .stop  = usb_watch_stop,
        .show  = usb_watch_show,
};



static int __init usb_watch_init(void)
{
        return stopwatch_register("usb_watch", NR_USB_WATCHES, usb_watch_show);
}
module_init(usb_watch_init);

static void __exit usb_watch_exit(void)
{
  /* 
   * TODO: 
   * desconstruct what the stopwatch_register did
   * 
   */
}
module_exit(usb_watch_exit);
