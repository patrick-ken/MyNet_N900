/*
 * arch/ubicom32/mach-common/profilenode.h
 *	devtree node for the profiler, to communicate with the ultra ipProfile package
 *
 * (C) Copyright 2009, Ubicom, Inc.
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
 * Ubicom32 implementation derived from (with many thanks):
 *   arch/m68knommu
 *   arch/blackfin
 *   arch/parisc
 */

#include <linux/types.h>
#include <asm/devtree.h>
#include <asm/profpkt.h>

#ifndef _PROFILE_H_
#define _PROFILE_H_

#define PROFILE_MAX_THREADS 12
#define PROFILE_MAX_RATE 1000
#define PROFILE_MAX_SAMPLES (PROFILE_MAX_THREADS * PROFILE_MAX_RATE)

struct profile_sample;
struct oprofile_sample;

/*
 * values chosen so all counter values fit in a single UDP packet
 */
#define PROFILE_NODE_MAX_COUNTERS 24

struct profile_counter {
	char name[PROFILE_COUNTER_NAME_LENGTH];
	unsigned int value;
};

struct profilenode {
	struct devtree_node dn;
	volatile u32_t enabled;				/* Is the profiler enabled to take samples? */
	volatile u32_t busy;				/* set when the samples are being read by the driver */
	volatile u32_t rate;				/* What is the sampling rate? */
	volatile u32_t enabled_threads;			/* which threads were enabled at the last sample time */
	volatile u32_t hrt;				/* HRT threads */
	volatile u32_t profiler_thread;			/* thread running the profile sampler */
	volatile u32_t clocks;				/* system clock timer at last sample */
	volatile u32_t cpu_id;				/* chip_id register */
	volatile u32_t inst_count[PROFILE_MAX_THREADS];	/* sampled instruction counts at most recent sample */
	volatile u32_t stats[PROFILE_COUNTERS];		/* contents of the cache statistics counters */
	volatile u16_t head;				/* sample taker puts samples here */
	volatile u16_t tail;				/* packet filler takes samples here */
	volatile u16_t count;				/* number of valid samples */
	volatile u16_t max_samples;			/* how many samples can be in the samples array */
	volatile u32_t dropped_samples;			/* how many samples did ipProfile drop due to buffer overrun */
	struct profile_sample *samples;			/* samples array allocated by the linux driver */
	volatile u32_t num_counters;			/* how many registered performance counters */
	volatile struct profile_counter counters[PROFILE_NODE_MAX_COUNTERS];
	volatile u32_t *sw_ksp_ptr;			/* pointer to array (per hardware thread) of pointers to struct thread_info */
	u32_t task_offset;				/* offset in bytes in thread_info to task_struct pointer */
	u32_t pid_offset;				/* offset in bytes in task_struct to the PID */

	/* unimplemented interface for future oprofile work */
	volatile u16_t oprofile_head;			/* sample taker puts samples here */
	volatile u16_t oprofile_tail;			/* packet filler takes samples here */
	volatile u16_t oprofile_count;			/* how many oprofile sampels are are in use */
	volatile u16_t oprofile_max_samples;		/* samples array size for oprofile samples */
	struct oprofile_sample *oprofile_samples;	/* oprofile sample buffer */
};

#endif
