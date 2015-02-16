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
#include <linux/module.h>
#include <linux/sched.h>

#include <asm/cacheflush.h>
#include <asm/processor.h>
#include <asm/ubicom32.h>

#include "dwc_os.h"

/*
 * ubi32_dma_flush()
 *	Flush the buf out of cache
 */
void ubi32_dma_flush(void *buf, unsigned int size)
{
        flush_dcache_range((u32)buf, (u32)buf + size);
}
EXPORT_SYMBOL(ubi32_dma_flush);

/*
 * ubi32_dma_invalidate()
 *	Invalidate cache for the buf
 */
void ubi32_dma_invalidate(void *buf, unsigned int size)
{
	mem_d_cache_control((u32)buf, (u32)buf + size, CCR_CTRL_INV_ADDR);
}
EXPORT_SYMBOL(ubi32_dma_invalidate);

/*
 * ubi32_sync()
 *	Ubicom32 sync is done at the end of mem_d_cache_control().
 */
void ubi32_sync(void)
{
}
EXPORT_SYMBOL(ubi32_sync);

/*
 * ubi32_br_writel()
 *	Write a long to the USB blocking region.
 *
 * TODO: Remove the cycles and lock for production silicon.
 */
void ubi32_br_writel(volatile u32 * volatile addr, u32 val)
{
	UBICOM32_LOCK(USB_AERROR_LOCK_BIT);
        asm volatile (
                " move.4 0(%[addr]), %[val]     \n"
                " cycles        5               \n"
                :
                : [addr] "a" (addr), [val] "d" (val)
        );
	UBICOM32_UNLOCK(USB_AERROR_LOCK_BIT);
}
EXPORT_SYMBOL(ubi32_br_writel);

/*
 * ubi32_br_readl()
 *	Read a long from the USB blocking region.
 *
 * TODO: Remove the cycles and lock for production silicon.
 */
u32 ubi32_br_readl(volatile u32 * volatile addr)
{
        volatile register u32 ret;

	UBICOM32_LOCK(USB_AERROR_LOCK_BIT);
        asm volatile (
                " move.4 %[ret], 0(%[addr])     \n"
                " cycles        5               \n"
                : [ret] "=&d" (ret)
                : [addr] "a" (addr)
        );
	UBICOM32_UNLOCK(USB_AERROR_LOCK_BIT);
        return ret;
}

EXPORT_SYMBOL(ubi32_br_readl);
