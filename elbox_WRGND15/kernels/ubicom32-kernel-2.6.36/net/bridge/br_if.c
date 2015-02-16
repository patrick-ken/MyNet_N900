/*
 *	Userspace interface
 *	Linux ethernet bridge
 *
 *	Authors:
 *	Lennert Buytenhek		<buytenh@gnu.org>
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version
 *	2 of the License, or (at your option) any later version.
 */

#include <linux/kernel.h>
#include <linux/netdevice.h>
#include <linux/netpoll.h>
#include <linux/ethtool.h>
#include <linux/if_arp.h>
#include <linux/module.h>
#include <linux/init.h>
#include <linux/rtnetlink.h>
#include <linux/if_ether.h>
#include <linux/slab.h>
#include <net/sock.h>

#ifndef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
#include <linux/version.h>
#include <linux/sched.h>
#include <linux/types.h>
#include <linux/proc_fs.h>
#include <linux/reboot.h>

#include <net/ubi32_access.h>
#include <linux/delay.h>	/* Needed for msleep */
#endif

#include "br_private.h"

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP_DEBUG
#define log printk
#else
#define log
#endif

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP

#ifndef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
#define SWITCH_READ         0x0
#define SWITCH_WRITE        0x1

#define LAN_VID             0x1   /* Because AR8327 have IVL & SVL, so it need set VID when search in ARL table. */
#define S17_STATIC_ENTRY    0x0000000f
#define S17_DES_PORT_MASK   0x007f0000

#define S17_ATU_VID               8
#define S17_ATU_DES_PORT         16
#define S17_ATU_HASH_HIGH_ADDR   31
#define S17_AT_BUSY              31
#define S17_ATU_COPY_TO_CPU       6

/* Atheros AR8327 embedded ethernet switch registers */
#define S17_ATU_DATA0               0x0600
#define S17_ATU_DATA1               0x0604
#define S17_ATU_DATA2               0x0608
#define S17_ATU_FUNC_REG            0x060c

/* ARL Table Handle */
#define S17_ARL_FLUSH_ALL           0x80000001 /* clear all entries      */
#define S17_ARL_LOAD_MAC            0x80000002 /* add an entry           */
#define S17_ARL_PURGE_MAC           0x80000003 /* delete an entry        */
#define S17_ARL_GET_NEXT            0x80000006 /* get next entry         */
#define S17_ARL_SEARCH_MAC          0x80000007 /* search an entry by MAC */

#if CONFIG_SWITCH_NUM == 2 
	#define PAD0_MODE_SWITCH_1			0x00080080 /* 0x0004 for Pro */
	#define FRAM_ACK_CTRL0_1			0x01010100 /* 0x210 */
	#define FRAM_ACK_CTRL1_1			0x00000101 /* 0x214 */
	#define GLOBAL_FW_CTRL1_1			0x217f7f7f /* 0x624 */
	#define PORT0_STATUS_1				0x00000d7e /* 0x7c */
	#define GLOBAL_FW_CTRL0_1                       0x000004f0 /* 0x620 */
	#define MAX_FRAME_SIZE_1                        0x00002580 /* 0x78 */
	#define PWS_REG_1				0x40000000 /* 0x0010 */
	#define PORT5_STATUS_1				0x00000d00 /* 0x0090 */
	#define PORT6_STATUS_1                          0x00000d00 /* 0x0094 */
	#define SGMII_CONTROL_REG_1			0xc74164d0 /* 0x00e0 */
#else
	#define PAD0_MODE_SWITCH_1			0x07c00000 /* 0x0004 for Storage */
	#define FRAM_ACK_CTRL0_1			0x01010100 /* 0x210 */
	#define FRAM_ACK_CTRL1_1			0x00000001 /* 0x214 */
	#define GLOBAL_FW_CTRL1_1			0x017f7f7f /* 0x624 */
	#define PORT0_STATUS_1				0x0000007e /* 0x7c */
	#define GLOBAL_FW_CTRL0_1			0x000004f0 /* 0x620 */
	#define MAX_FRAME_SIZE_1			0x00002580 /* 0x78 */
	#define PWS_REG_1                               0x40000000 /* 0x0010 */
	#define PORT5_STATUS_1                          0x00000d00 /* 0x0090 */
	#define PORT6_STATUS_1                          0x00000000 /* 0x0094 */
#endif

#define FRAM_ACK_CTRL0_2				0x00000000 /* 0x210 */
#define FRAM_ACK_CTRL1_2				0x00000000 /* 0x214 */
#define GLOBAL_FW_CTRL1_2				0x007f7f7f /* 0x624 */
#define PORT0_STATUS_2					0x00000d7e /* 0x7c */
#define GLOBAL_FW_CTRL0_2				0x000000f0 /* 0x620 */
#define MAX_FRAME_SIZE_2				0x000005ee /* 0x78 */
#define PWS_REG_2					0x40000000 /* 0x0010 */
#define PORT5_STATUS_2					0x00001280 /* 0x0090 */
#define PORT6_STATUS_2					0x00000d7e /* 0x0094 */
#define SGMII_CONTROL_REG_2				0xc74164d0 /* 0x00e0 */

#define PAD5_MODE_SWITCH_1				0x00000000 /* 0x0008 */
#define PAD6_MODE_SWITCH_1				0x03c20000 /* 0x000c */
#define PAD0_MODE_SWITCH_2				0x07680000 /* 0x0004 */
#define PAD5_MODE_SWITCH_2				0x00000000 /* 0x0008 */
#define PAD6_MODE_SWITCH_2				0x01000080 /* 0x000c */

static void add_member(unsigned char *g_mac, unsigned char *c_mac);
static void del_member(unsigned char *g_mac, unsigned char *c_mac);
static void clear_group(unsigned char *g_mac);
static int snoop_init(void);
static int snoop_deinit(void);

//prototypes
static inline wait_switch_done(u8_t switch_id);
static void check_register(u8_t switch_id);

unsigned int rareg(int mode, unsigned int addr, unsigned int new_value, u8_t switch_id)
{
	unsigned int value=0;
	mdio_ctrl *p_mdio_ctrl = NULL;

	p_mdio_ctrl = mdio_ctrl_init(switch_id);

    if(mode==SWITCH_WRITE)
    {
	    switch_reg_write(addr,new_value, p_mdio_ctrl);
    }
    else /* SWITCH_READ */
    {
        value = switch_reg_read(addr, p_mdio_ctrl);
    }
    return value;
}

static unsigned int get_port_map(unsigned char *mymac, u8_t switch_id)
{
   	unsigned int value=0, mymac2=0,mymac3=0;
    unsigned short mymac1=0;

   	memcpy(&mymac1, mymac , 2);
    mymac1 = ntohs(mymac1);
    memcpy(&mymac2, mymac+2 , 4);
    mymac2 = ntohl(mymac2);
  	mymac3 = mymac1;

    rareg(SWITCH_WRITE, S17_ATU_DATA0, mymac2, switch_id);                  /* set MAC's 2th, 3th, 4th and 5th BYTE */
    rareg(SWITCH_WRITE, S17_ATU_DATA1, mymac3 | (1 << S17_ATU_HASH_HIGH_ADDR), switch_id);		/* set MAC's 0th and 1th BYTE           */
   	rareg(SWITCH_WRITE, S17_ATU_DATA2, LAN_VID << S17_ATU_VID, switch_id); /* search MAC in ATU_VID group          */
    rareg(SWITCH_WRITE, S17_ATU_FUNC_REG, S17_ARL_SEARCH_MAC, switch_id);   /* handle switch to search MAC          */

    wait_switch_done(switch_id);

    /* If the MAC in the ARL table, we can get data from S17_ATU_DATA0, S17_ATU_DATA1 and S17_ATU_DATA2  */
	value = rareg(SWITCH_READ, S17_ATU_DATA1, 0, switch_id);
   	log("oolong get_port_map : mac=%02x:%02x:%02x:%02x:%02x:%02x mymac2 =%x mymac3 =%x value=%x for switch-%d\n",
	         mymac[0],mymac[1],mymac[2],mymac[3],mymac[4],mymac[5],mymac2,mymac3,value,switch_id);
    if(value)
    {
        return ((value & S17_DES_PORT_MASK ) >> S17_ATU_DES_PORT ); /* port member: ATU_DES_PORT is bit 16~22 */
    }
    else
	    return 0;
}

static void set_port_map(unsigned char *mymac, unsigned int port_map, u8_t switch_id)
{
	unsigned int mymac2=0,mymac3=0;
	unsigned short mymac1=0;

	memcpy(&mymac1, mymac , 2);
	mymac1 = ntohs(mymac1);
	memcpy(&mymac2, mymac+2 , 4);
	mymac2 = ntohl(mymac2);
	mymac3=mymac1;

	if(port_map != S17_ARL_PURGE_MAC)
	{
		//add port 0, cause our cpu port is port 0 (tom, 20111208)
		mymac3= mymac3 | (((port_map | 1) << S17_ATU_DES_PORT ) + (1 << S17_ATU_HASH_HIGH_ADDR)) ;
	}

	log("oolong set_port_map 0: mac=%02x:%02x:%02x:%02x:%02x:%02x port_map =%x mymac2 =%x mymac3 =%x \n",
		mymac[0],mymac[1],mymac[2],mymac[3],mymac[4],mymac[5],port_map,mymac2,mymac3 );

	rareg(SWITCH_WRITE, S17_ATU_DATA0, mymac2, switch_id); /* set MAC's 2th, 3th, 4th and 5th BYTE */
	rareg(SWITCH_WRITE, S17_ATU_DATA1, mymac3, switch_id); /* set MAC's 0th and 1th BYTE           */
	rareg(SWITCH_WRITE, S17_ATU_DATA2, (S17_STATIC_ENTRY | (LAN_VID << S17_ATU_VID)), switch_id);

	if(port_map == S17_ARL_PURGE_MAC) 
	{
		rareg(SWITCH_WRITE, S17_ATU_FUNC_REG, S17_ARL_PURGE_MAC, switch_id); /* delete mac from ARL table */
	}
	else
	{
		rareg(SWITCH_WRITE, S17_ATU_FUNC_REG, S17_ARL_LOAD_MAC, switch_id);  /* add mac to ARL table      */
	}

	wait_switch_done(switch_id);	
	log(" set mac 0x%04x%08x port list 0x%08x for switch-%d\n", mymac1,mymac2, port_map, switch_id);

	check_register(switch_id);
}

static void check_register(u8_t switch_id)
{
	unsigned int value=0;
	if(switch_id==1)
	{
		value = rareg(SWITCH_READ, 0x0004, 0, switch_id);
		if(value != PAD0_MODE_SWITCH_1)
		{	
			rareg(SWITCH_WRITE,0x0004, PAD0_MODE_SWITCH_1, switch_id);
			printk("check_register: switch-1 0x0004 value (0x%08x) is wrong\n",value);
		}	

		value = rareg(SWITCH_READ, 0x0008, 0, switch_id);
		if(value != PAD5_MODE_SWITCH_1)
		{	
			rareg(SWITCH_WRITE,0x0008, PAD5_MODE_SWITCH_1, switch_id);
			printk("check_register: switch-1 0x0008 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x000c, 0, switch_id);
		if(value != PAD6_MODE_SWITCH_1)
		{	
			rareg(SWITCH_WRITE,0x000c, PAD6_MODE_SWITCH_1, switch_id);
			printk("check_register: switch-1 0x000c value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x210, 0, switch_id);
		if(value != FRAM_ACK_CTRL0_1)
		{
			rareg(SWITCH_WRITE,0x210, FRAM_ACK_CTRL0_1, switch_id);
			printk("check_register: switch-1 0x210 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x214, 0, switch_id);
		if(value != FRAM_ACK_CTRL1_1)
		{
			rareg(SWITCH_WRITE,0x214, FRAM_ACK_CTRL1_1, switch_id);
			printk("check_register: switch-1 0x214 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x624, 0, switch_id);
		if(value != GLOBAL_FW_CTRL1_1)
		{
			rareg(SWITCH_WRITE,0x624, GLOBAL_FW_CTRL1_1, switch_id);
			printk("check_register: switch-1 0x624 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x7c, 0, switch_id);
		if(value != PORT0_STATUS_1)
		{
			rareg(SWITCH_WRITE,0x7c, PORT0_STATUS_1, switch_id);
			printk("check_register: switch-1 0x7c value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x620, 0, switch_id);
		if(value != GLOBAL_FW_CTRL0_1)
		{
			rareg(SWITCH_WRITE,0x620, GLOBAL_FW_CTRL0_1, switch_id);
			printk("check_register: switch-1 0x620 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x78, 0, switch_id);
		if(value != MAX_FRAME_SIZE_1)
		{
			rareg(SWITCH_WRITE,0x78, MAX_FRAME_SIZE_1, switch_id);
			printk("check_register: switch-1 0x78 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0010, 0, switch_id);
		if(value != PWS_REG_1)
		{
			rareg(SWITCH_WRITE,0x0010, PWS_REG_1, switch_id);
			printk("check_register: switch-1 0x0010 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0090, 0, switch_id);
		if(value != PORT5_STATUS_1)
		{
			rareg(SWITCH_WRITE,0x0090, PORT5_STATUS_1, switch_id);
			printk("check_register: switch-1 0x0090 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0094, 0, switch_id);
		if(value != PORT6_STATUS_1)
		{
			rareg(SWITCH_WRITE,0x0094, PORT6_STATUS_1, switch_id);
			printk("check_register: switch-1 0x0094 value (0x%08x) is wrong\n",value);
		}
#if CONFIG_SWITCH_NUM == 2
		value = rareg(SWITCH_READ, 0x00e0, 0, switch_id);
		if(value != SGMII_CONTROL_REG_1)
		{
			rareg(SWITCH_WRITE,0x00e0, SGMII_CONTROL_REG_1, switch_id);
			printk("check_register: switch-1 0x00e0 value (0x%08x) is wrong\n",value);
		}
#endif		
	}
#if CONFIG_SWITCH_NUM == 2	
	else if(switch_id==2)
	{
		value = rareg(SWITCH_READ, 0x0004, 0, switch_id);
		if(value != PAD0_MODE_SWITCH_2)
		{	
			rareg(SWITCH_WRITE,0x0004, PAD0_MODE_SWITCH_2, switch_id);
			printk("check_register: switch-2 0x0004 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0008, 0, switch_id);
		if(value != PAD5_MODE_SWITCH_2)
		{	
			rareg(SWITCH_WRITE,0x0008, PAD5_MODE_SWITCH_2, switch_id);
			printk("check_register: switch-2 0x0008 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x000c, 0, switch_id);
		if(value != PAD6_MODE_SWITCH_2)
		{	
			rareg(SWITCH_WRITE,0x000c, PAD6_MODE_SWITCH_2, switch_id);
			printk("check_register: switch-2 0x000c value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x210, 0, switch_id);
		if(value != FRAM_ACK_CTRL0_2)
		{
			rareg(SWITCH_WRITE,0x210, FRAM_ACK_CTRL0_2, switch_id);
			printk("check_register: switch-2 0x210 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x214, 0, switch_id);
		if(value != FRAM_ACK_CTRL1_2)
		{
			rareg(SWITCH_WRITE,0x214, FRAM_ACK_CTRL1_2, switch_id);
			printk("check_register: switch-2 0x214 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x624, 0, switch_id);
		if(value != GLOBAL_FW_CTRL1_2)
		{
			rareg(SWITCH_WRITE,0x624, GLOBAL_FW_CTRL1_2, switch_id);
			printk("check_register: switch-2 0x624 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x7c, 0, switch_id);
		if(value != PORT0_STATUS_2)
		{
			rareg(SWITCH_WRITE,0x7c, PORT0_STATUS_2, switch_id);
			printk("check_register: switch-2 0x7c value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x620, 0, switch_id);
		if(value != GLOBAL_FW_CTRL0_2)
		{
			rareg(SWITCH_WRITE,0x620, GLOBAL_FW_CTRL0_2, switch_id);
			printk("check_register: switch-2 0x620 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x78, 0, switch_id);
		if(value != MAX_FRAME_SIZE_2)
		{
			rareg(SWITCH_WRITE,0x78, MAX_FRAME_SIZE_2, switch_id);
			printk("check_register: switch-2 0x78 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0010, 0, switch_id);
		if(value != PWS_REG_2)
		{
			rareg(SWITCH_WRITE,0x0010, PWS_REG_2, switch_id);
			printk("check_register: switch-2 0x0010 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0090, 0, switch_id);
		if(value != PORT5_STATUS_2)
		{
			rareg(SWITCH_WRITE,0x0090, PORT5_STATUS_2, switch_id);
			printk("check_register: switch-2 0x0090 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x0094, 0, switch_id);
		if(value != PORT6_STATUS_2)
		{
			rareg(SWITCH_WRITE,0x0094, PORT6_STATUS_2, switch_id);
			printk("check_register: switch-2 0x0094 value (0x%08x) is wrong\n",value);
		}
		value = rareg(SWITCH_READ, 0x00e0, 0, switch_id);
		if(value != SGMII_CONTROL_REG_2)
		{
			rareg(SWITCH_WRITE,0x00e0, SGMII_CONTROL_REG_2, switch_id);
			printk("check_register: switch-2 0x00e0 value (0x%08x) is wrong\n",value);
		}
	}
#endif	
}

static inline wait_switch_done(u8_t switch_id)
{
	int i=0;
	unsigned int value=0;
		
	for (i = 0; i < 20; i++)
	{
		value = 0;
		value = rareg(SWITCH_READ, S17_ATU_FUNC_REG, 0, switch_id);    
		if ( (value & ( 1 << S17_AT_BUSY ) ) == 0)
		{
			log("oolong wait_switch_done: value=%x \n",value);
			break;
		}
		msleep(1);
	}
	if (i == 20)
		log("*** AR8327: register S17_ATU_FUNC_REG timeout.\n");
}

int portLookUpByMac(char *mac)
{
	unsigned int value;
	int i;
	for (i = 1; i <= CONFIG_SWITCH_NUM; i++)	// We need to serach from switch-1 and switch-2
	{
		value = get_port_map(mac,i);		
		if( value && value != 0x1 && value != 0x40)
			break;

			/*
			 *	0x1:	this MAC is belong to switch-2
			 *	0x40:	this MAC is belong to switch-1
			 */		
	}

	#if CONFIG_SWITCH_NUM == 2
	/*	Pro layout:
	 *		switch-2: LAN1(0x2)/LAN2(0x4)/LAN3(0x8)/LAN4(0x10)/LAN5(0x20)
	 *		switch-1: LAN6(0x8)/LAN7(0x10)
	 */	
 	if (i==2)	// switch-2
	{
		switch(value){
			case 0x2:
				return 1;
			case 0x4:
				return 2;
			case 0x8:
				return 3;
			case 0x10:
				return 4;
			case 0x20:
				return 5;	
			default:
				log(" portLookUpByMac error, 0x%08x\n", value);
				return -1;
		}
	}
	else if (i==1)	// switch-1
	{
		switch(value){
			case 0x8:
				return 6;
			case 0x10:
				return 7;
			default:
				log(" portLookUpByMac error, 0x%08x\n", value);
				return -1;
		}
	}
	#else
		/*	Storage layout:
		 *		LAN1(0x2)/LAN2(0x4)/LAN3(0x8)/LAN4(0x10)/LAN5(0x20)
		 */
		switch(value){
		case 0x2:
			return 1;
		case 0x4:
			return 2;
		case 0x8:
			return 3;
		case 0x10:
			return 4;
		default:
			log(" portLookUpByMac error, 0x%08x\n", value);
			return -1;
		}
	#endif
}
#endif

typedef void (*add_member_cb)(unsigned char *, unsigned char *);
typedef void (*del_member_cb)(unsigned char *, unsigned char *);
typedef void (*clear_group_cb)(unsigned char *);
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
typedef void (*snoop_init_cb)(void);
typedef void (*snoop_deinit_cb)(void);

add_member_cb add_member=NULL;
del_member_cb del_member=NULL;
clear_group_cb clear_group=NULL;
snoop_init_cb snoop_init=NULL;
snoop_deinit_cb snoop_deinit=NULL;
#else
typedef int (*snoop_init_cb)(void);
typedef int (*snoop_deinit_cb)(void);

add_member_cb add_member_cb1=NULL;
del_member_cb del_member_cb1=NULL;
clear_group_cb clear_group_cb1=NULL;
snoop_init_cb snoop_init_cb1=NULL;
snoop_deinit_cb snoop_deinit_cb1=NULL;
#endif

void register_igmp_callbacks(add_member_cb fun1, del_member_cb fun2, clear_group_cb fun3)
{
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	add_member = fun1;
	del_member = fun2;
	clear_group = fun3;
#else
	add_member_cb1 = fun1;
	del_member_cb1 = fun2;
	clear_group_cb1 = fun3;
#endif
}

void unregister_igmp_callbacks()
{
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	add_member = NULL;
	del_member = NULL;
	clear_group = NULL;
#else
	add_member_cb1 = NULL;
	del_member_cb1 = NULL;
	clear_group_cb1 = NULL;
#endif
}

void register_snoop_init_callback (snoop_init_cb funa,snoop_deinit_cb funb)
{
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	snoop_init = funa;
	snoop_deinit = funb;
#else
	snoop_init_cb1 = funa;
	snoop_deinit_cb1 = funb;
#endif
}

void unregister_snoop_init_callback()
{
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	snoop_init = NULL;
	snoop_deinit = NULL;
#else
	snoop_init_cb1 = NULL;
	snoop_deinit_cb1 = NULL;
#endif
}

#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
EXPORT_SYMBOL(register_snoop_init_callback);
EXPORT_SYMBOL(unregister_snoop_init_callback);
EXPORT_SYMBOL(register_igmp_callbacks);
EXPORT_SYMBOL(unregister_igmp_callbacks);
#else
//EXPORT_SYMBOL(register_snoop_init_callback);
//EXPORT_SYMBOL(unregister_snoop_init_callback);
//EXPORT_SYMBOL(register_igmp_callbacks);
//EXPORT_SYMBOL(unregister_igmp_callbacks);

int sw_init(void)
{
	register_snoop_init_callback(snoop_init,snoop_deinit);
	return 0;
}

void sw_deinit(void)
{
	unregister_igmp_callbacks();
	unregister_snoop_init_callback();
}

int snoop_deinit()
{
    unregister_igmp_callbacks();
    return 0;
}

int snoop_init()
{
    register_igmp_callbacks(add_member,del_member,clear_group);
    return 0;
}

//=== igmp ====
LIST_HEAD(grp_list) ;

struct grp_mac {
	struct list_head list;
	struct list_head member_list;
	unsigned char grpmac[6];
};

struct client_mac {
	struct list_head list;
	unsigned char member_mac[6];
};

void show_gentry()
{
	struct grp_mac *pgentry;
	struct client_mac *pcentry;
	printk("Show All entries \n");
	list_for_each_entry(pgentry, &grp_list, list)
	{
		printk(" mac 0x%02x%02x%02x%02x%02x%02x\n",pgentry->grpmac[0],
														pgentry->grpmac[1],
														pgentry->grpmac[2],
														pgentry->grpmac[3],
														pgentry->grpmac[4],
														pgentry->grpmac[5]);

		list_for_each_entry(pcentry, &pgentry->member_list, list)
		{
			printk("   cmac 0x%02x%02x%02x%02x%02x%02x\n",pcentry->member_mac[0],
														pcentry->member_mac[1],
														pcentry->member_mac[2],
														pcentry->member_mac[3],
														pcentry->member_mac[4],
														pcentry->member_mac[5]);
		}
	}
}

/*
1. find old group if exist
2. find old client mac if exist
3. snooping : update the group port list
*/
static void add_member(unsigned char *g_mac, unsigned char *c_mac)
{
	struct grp_mac *pgentry;
	struct client_mac *pcentry;
	int found=0, cport=-1;
	spinlock_t igmp_lock;
	unsigned long flags;
	unsigned int gport_list = 0;
	unsigned int gport_list_2 = 0;		// for switch-2

	if(g_mac==NULL || c_mac==NULL)
		return;

	spin_lock_irqsave(&igmp_lock,flags);
	//1. find old group if exist
	list_for_each_entry(pgentry, &grp_list, list)
	{
		if(!memcmp(pgentry->grpmac,g_mac, 6))
		{
			found = 1;
			break;
		}
	}
	if(!found)	//create new group
	{
		pgentry = (struct grp_mac *)kmalloc(sizeof(struct grp_mac), GFP_ATOMIC);
		INIT_LIST_HEAD(&pgentry->list);
		INIT_LIST_HEAD(&pgentry->member_list);
		list_add(&pgentry->list, &grp_list);
		memcpy(pgentry->grpmac , g_mac , 6);
		log("sw : Create new group aa 0x%02x%02x%02x%02x%02x%02x\n",
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5]);

	}
	//2. find old client mac if exist
	found = 0;
	list_for_each_entry(pcentry, &pgentry->member_list, list)
	{
		if(!memcmp(pcentry->member_mac,c_mac, 6))
		{	/* member already exist, do nothing ~*/
			found = 1;
			break;
		}
	}

	if(!found)
	{	/* member NOT exist, create NEW ONE and attached it to this group-mac linked list ~*/
		pcentry	= (struct client_mac *)kmalloc(sizeof(struct client_mac), GFP_ATOMIC);
		INIT_LIST_HEAD(&pcentry->list);
		list_add(&pcentry->list, &pgentry->member_list);
		memcpy( pcentry->member_mac ,c_mac , 6);
		log("sw : Added client mac 0x%02x%02x%02x%02x%02x%02x to group 0x%02x%02x%02x%02x%02x%02x\n",
			pcentry->member_mac[0],pcentry->member_mac[1],pcentry->member_mac[2],pcentry->member_mac[3],pcentry->member_mac[4],pcentry->member_mac[5],
			pgentry->grpmac[0],pgentry->grpmac[1],pgentry->grpmac[2],pgentry->grpmac[3],pgentry->grpmac[4],pgentry->grpmac[5]
		);
	}

	spin_unlock_irqrestore (&igmp_lock, flags);

	/*
		TODO : what if user unplugged cable without sending leave packet ? The port will be flooded with multicast
		packets. So, in add_member, we should check ALL client_macs and update ALL PORTS !!
	*/
		
	list_for_each_entry(pcentry, &pgentry->member_list, list)
	{
 		cport=portLookUpByMac(pcentry->member_mac);
		if(cport==-1)
		{
			log("Can't find mac in which port. \n");
			continue;
		}
		#if CONFIG_SWITCH_NUM == 2
		if (cport>5)	// it belongs to switch-1
		{
			gport_list = gport_list | (0x1 << (cport-3));
			/*
			 *		LAN6 is prot 0x8 for switch-1
			 *		LAN7 is prot 0x10 for switch-1
			 *		thus , (cport-3)
			 */
		}
		else			// it belongs to switch-2
			gport_list_2 = gport_list_2 | (0x1 << cport);
		#else		
		gport_list = gport_list | (0x1 << cport);
		#endif
	}

	#if CONFIG_SWITCH_NUM == 2
	set_port_map( g_mac, gport_list_2 | (1<<6) , 2);	// We need add port 0x40 beacuse it's input for switch-1
	#endif	
	set_port_map( g_mac, gport_list, 1);
}

/*
1. find old group
2. find old client mac
3. if group is empty, delete group
4. snooping : update the group port list
*/
static void del_member(unsigned char *g_mac, unsigned char *c_mac)
{
	struct grp_mac *pgentry;
	struct client_mac *pcentry;
	int found = 0,  cport=-1;
	unsigned int gport_list = 0;
	int i;
	
	//0. sanity check
	if(g_mac==NULL || c_mac==NULL)
		return;

	//1. find old group
	list_for_each_entry(pgentry, &grp_list, list)
	{
		if(!memcmp(pgentry->grpmac,g_mac, 6))
		{
			found = 1;
			break;
		}
	}

	if(!found)
	{
		log("sw : Can't delete 0x%02x%02x%02x%02x%02x%02x, group NOT FOUND.\n",
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5] );
		return;
	}

	//2. find old client mac
	found = 0;
	list_for_each_entry(pcentry, &pgentry->member_list, list)
	{
		if(!memcmp(pcentry->member_mac,c_mac, 6))
		{
			found = 1;
			break;
		}
	}

	if(found)
	{
		/* member to be deleted FOUND, DELETE IT ! */
		list_del(&pcentry->list);
		kfree(pcentry);
		log("sw : Delete client 0x%02x%02x%02x%02x%02x%02x in group 0x%02x%02x%02x%02x%02x%02x\n",
			c_mac[0],c_mac[1],c_mac[2],c_mac[3],c_mac[4],c_mac[5],
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5] );
	}else
	{	/* do nothing, just debug */
		log("sw : Can't delete client 0x%02x%02x%02x%02x%02x%02x, client NOT FOUND in group 0x%02x%02x%02x%02x%02x%02x\n",
			c_mac[0],c_mac[1],c_mac[2],c_mac[3],c_mac[4],c_mac[5],
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5] );
	}

	//3. if group is empty, delete group
	if(list_empty(&pgentry->member_list))
	{
		list_del(&pgentry->member_list);
		list_del(&pgentry->list);
		kfree(pgentry);
		//remove group mac from port_list
		for (i = 1; i <= CONFIG_SWITCH_NUM; i++) 
			set_port_map( g_mac, S17_ARL_PURGE_MAC, i);
			
		log("sw : Delete group 0x%02x%02x%02x%02x%02x%02x since its empty \n",
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5] );
		return;
	}

	//4. snooping : update the group port list
	cport=portLookUpByMac(c_mac);
	if(cport==-1)
	{
 		log("Can't find mac in which port. \n");
		return;
	}
	
	#if CONFIG_SWITCH_NUM == 2
 	if (cport>5)
	{
		gport_list = get_port_map(g_mac,1);					//get current group map port map
		gport_list = gport_list & ~(0x1 << (cport-3));		//update portmap to group mac
		set_port_map( g_mac, gport_list, 1);
 	}
	else
	{
		gport_list = get_port_map(g_mac,2);				//get current group map port map
		gport_list = gport_list & ~(0x1 << cport);		//update portmap to group mac
		set_port_map( g_mac, gport_list, 2);
	}
	#else
	//get current group map port map
	gport_list = get_port_map(g_mac, 1);
	//update portmap to group mac
	gport_list = gport_list & ~(0x1 << cport);
	set_port_map( g_mac, gport_list, 1);
	#endif
}

static void clear_group(unsigned char *g_mac)
{
	struct grp_mac *pgentry;
	struct list_head *pos, *q, *tlist;
	int found = 0;
	int i;
	
	//0. sanity check
	if(g_mac==NULL)
		return;

	//1. find the group
	list_for_each_entry(pgentry, &grp_list, list)
	{
		if(!memcmp(pgentry->grpmac,g_mac, 6))
		{
			found = 1;
			break;
		}
	}
	if(!found)
	{
		log("sw : Can't clear group 0x%02x%02x%02x%02x%02x%02x, NOT FOUND.\n",
			g_mac[0],g_mac[1],g_mac[2],g_mac[3],g_mac[4],g_mac[5] );
		return;
	}

	//2. delete all the group members.
	list_for_each_safe(pos, q, &pgentry->member_list)
	{
		tlist= list_entry(pos, struct client_mac, list);
		list_del(pos);
		kfree(tlist);
	}
	//3. delete the group
	list_del(&pgentry->member_list);
	list_del(&pgentry->list);
	kfree(pgentry);

	//4. remove group mac from port_list
	for (i = 1; i <= CONFIG_SWITCH_NUM; i++) 
		set_port_map( g_mac, S17_ARL_PURGE_MAC, i);
	
}
#endif

#endif

/*
 * Determine initial path cost based on speed.
 * using recommendations from 802.1d standard
 *
 * Since driver might sleep need to not be holding any locks.
 */
static int port_cost(struct net_device *dev)
{
	if (dev->ethtool_ops && dev->ethtool_ops->get_settings) {
		struct ethtool_cmd ecmd = { .cmd = ETHTOOL_GSET, };

		if (!dev->ethtool_ops->get_settings(dev, &ecmd)) {
			switch(ecmd.speed) {
			case SPEED_10000:
				return 2;
			case SPEED_1000:
				return 4;
			case SPEED_100:
				return 19;
			case SPEED_10:
				return 100;
			}
		}
	}

	/* Old silly heuristics based on name */
	if (!strncmp(dev->name, "lec", 3))
		return 7;

	if (!strncmp(dev->name, "plip", 4))
		return 2500;

	return 100;	/* assume old 10Mbps */
}


/*
 * Check for port carrier transistions.
 * Called from work queue to allow for calling functions that
 * might sleep (such as speed check), and to debounce.
 */
void br_port_carrier_check(struct net_bridge_port *p)
{
	struct net_device *dev = p->dev;
	struct net_bridge *br = p->br;

	if (netif_carrier_ok(dev))
		p->path_cost = port_cost(dev);

	if (netif_running(br->dev)) {
		spin_lock_bh(&br->lock);
		if (netif_carrier_ok(dev)) {
			if (p->state == BR_STATE_DISABLED)
				br_stp_enable_port(p);
		} else {
			if (p->state != BR_STATE_DISABLED)
				br_stp_disable_port(p);
		}
		spin_unlock_bh(&br->lock);
	}
}

static void release_nbp(struct kobject *kobj)
{
	struct net_bridge_port *p
		= container_of(kobj, struct net_bridge_port, kobj);
	kfree(p);
}

static struct kobj_type brport_ktype = {
#ifdef CONFIG_SYSFS
	.sysfs_ops = &brport_sysfs_ops,
#endif
	.release = release_nbp,
};

static void destroy_nbp(struct net_bridge_port *p)
{
	struct net_device *dev = p->dev;

	p->br = NULL;
	p->dev = NULL;
	dev_put(dev);

	kobject_put(&p->kobj);
}

static void destroy_nbp_rcu(struct rcu_head *head)
{
	struct net_bridge_port *p =
			container_of(head, struct net_bridge_port, rcu);
	destroy_nbp(p);
}

/* Delete port(interface) from bridge is done in two steps.
 * via RCU. First step, marks device as down. That deletes
 * all the timers and stops new packets from flowing through.
 *
 * Final cleanup doesn't occur until after all CPU's finished
 * processing packets.
 *
 * Protected from multiple admin operations by RTNL mutex
 */
static void del_nbp(struct net_bridge_port *p)
{
	struct net_bridge *br = p->br;
	struct net_device *dev = p->dev;

	sysfs_remove_link(br->ifobj, p->dev->name);

	dev_set_promiscuity(dev, -1);

	spin_lock_bh(&br->lock);
	br_stp_disable_port(p);
	spin_unlock_bh(&br->lock);

	br_ifinfo_notify(RTM_DELLINK, p);

	br_fdb_delete_by_port(br, p, 1);

	list_del_rcu(&p->list);

	dev->priv_flags &= ~IFF_BRIDGE_PORT;

	netdev_rx_handler_unregister(dev);

	br_multicast_del_port(p);

	kobject_uevent(&p->kobj, KOBJ_REMOVE);
	kobject_del(&p->kobj);

	br_netpoll_disable(p);

	call_rcu(&p->rcu, destroy_nbp_rcu);
}

/* called with RTNL */
static void del_br(struct net_bridge *br, struct list_head *head)
{
	struct net_bridge_port *p, *n;

	list_for_each_entry_safe(p, n, &br->port_list, list) {
		del_nbp(p);
	}

	del_timer_sync(&br->gc_timer);

	br_sysfs_delbr(br->dev);
	unregister_netdevice_queue(br->dev, head);
}

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
static int check_mac(char *s)
{
	char * accept = MAC_ACCEPT_CHAR;

	if (!s || !(*s)) return (-1);
	if (strlen(s) == strspn(s, accept))
		return 0;

	return (-1);
}

/* search device'name that matched */
/* called under bridge lock */
static struct net_bridge_port *search_device(struct net_bridge *br, char *name)
{
	struct net_bridge_port *p;

	list_for_each_entry(p, &br->port_list, list) {
		if (strcmp(p->dev->name, name) == 0) {
			return p;
		}
	}

	return NULL;
}

static uint8_t *hex2dec(char *ch)
{
	if (*ch >= '0' && *ch <= '9') *ch = *ch - '0';
	else if (*ch >= 'A' && *ch <= 'F')  *ch = *ch - 'A' + 10;
	else if (*ch >= 'a' && *ch <= 'f')  *ch = *ch - 'a' + 10;

	return ch;
}

static void split_MAC(unsigned char *mac_addr, char *token)
{
	char *macDelim = MAC_DELIM;
	char **pMAC = &token;
	char *macField_char[6];
	int i;

	for (i = 0; i < 6; ++i) {
		int j;
		char temp[2];

		macField_char[i] = strsep(pMAC, macDelim);

		/* copy each char byte and convert to dec number */
		for (j = 0; j < 2; ++j) {
			memcpy(&temp[j], macField_char[i] + j, sizeof(char));
			hex2dec(&temp[j]);
		}

		temp[0] = temp[0] << 4;
		*(mac_addr + i)= (temp[0] ^ temp[1]);
	}
}

/* called under bridge lock */
static int table_setsnoop(struct net_bridge *br, int type)
{
	switch (type) {
		case ENABLE:
			br->snooping = 1;
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
			if (snoop_init)
#else
			if (snoop_init_cb1)
#endif
				snoop_init();
			else {
				printk("No snooping implementation. Please check !! \n");
				return (-1);
			}

			break;

		case DISABLE:
			br->snooping = 0;
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
			if (snoop_deinit)
#else
			if (snoop_deinit_cb1)
#endif
				snoop_deinit();

			else {
				printk("No snooping implementation. Please check !! \n");
				return (-1);
			}

			break;
	}

	return 0;
}

/* set wireless identifier */
/* called under bridge lock */
static int table_setwl(struct net_bridge *br, char *name, int type)
{
	struct net_bridge_port *hit_port;

	hit_port = search_device(br, name);
	if (hit_port != NULL) {
		if (type == ENABLE)
			atomic_set(&hit_port->wireless_interface, 1);
		else
			atomic_set(&hit_port->wireless_interface, 0);

		return 0;
	}
	else
		return (-1);
}

static void table_add(
		struct net_bridge_port *p,
		unsigned char *group_addr,
		unsigned char *member_addr)
{
	int found = 0;
	unsigned long flags;
	spinlock_t lock;
	struct port_group_mac *pgentry;
	struct port_member_mac *pcentry;

	if (group_addr == NULL || member_addr == NULL)
		return;

	/* Selmon - 20120320
	 * Looks like we are not accessing anything about hardware components,
	 * I think we do need to do spin_lock_irqsave() here.
	 */
//	spin_lock_irqsave(&lock, flags);

	//1. find old group if exist
	list_for_each_entry(pgentry, &p->igmp_group_list, list) {
		if (!memcmp(pgentry->grpmac,group_addr, 6)) {
			found = 1;
			break;
		}
	}

	if (!found)	//create new group
	{
		pgentry = (struct port_group_mac *)kmalloc(sizeof(struct port_group_mac), GFP_ATOMIC);
		INIT_LIST_HEAD(&pgentry->list);
		INIT_LIST_HEAD(&pgentry->member_list);
		list_add(&pgentry->list, &p->igmp_group_list);
		memcpy(pgentry->grpmac, group_addr, 6);
		print_debug("brg : Create new group 0x%02x%02x%02x%02x%02x%02x\n",
				group_addr[0],
				group_addr[1],
				group_addr[2],
				group_addr[3],
				group_addr[4],
				group_addr[5]);
	}

	//2. find old client mac if exist
	found = 0;
	list_for_each_entry(pcentry, &pgentry->member_list, list) {
		if (!memcmp(pcentry->member_mac, member_addr, 6)) {
			/* member already exist, do nothing ~*/
			found = 1;
			break;
		}
	}

	if (!found) {
		/* member NOT exist, create NEW ONE and attached it to this group-mac linked list ~*/
		pcentry = (struct port_member_mac *)kmalloc(sizeof(struct port_member_mac), GFP_ATOMIC);
		INIT_LIST_HEAD(&pcentry->list);
		list_add(&pcentry->list, &pgentry->member_list);
		memcpy(pcentry->member_mac, member_addr, 6);
		print_debug("brg : Added client mac 0x%02x%02x%02x%02x%02x%02x to group 0x%02x%02x%02x%02x%02x%02x\n",
				pcentry->member_mac[0],
				pcentry->member_mac[1],
				pcentry->member_mac[2],
				pcentry->member_mac[3],
				pcentry->member_mac[4],
				pcentry->member_mac[5],
				pgentry->grpmac[0],
				pgentry->grpmac[1],
				pgentry->grpmac[2],
				pgentry->grpmac[3],
				pgentry->grpmac[4],
				pgentry->grpmac[5]);
	}

	/* Selmon - 20120320
	 * No spin_lock_irqsave(), no spin_unlock_irqrestore().
	 */
//	spin_unlock_irqrestore(&lock, flags);
}

/*
 * 1. find old group
 * 2. find old client mac
 * 3. if group is empty, delete group
 * 4. snooping : update the group port list
 */
static void table_remove(
		struct net_bridge_port *p,
		unsigned char *group_addr,
		unsigned char *member_addr)
{
	struct port_group_mac *pgentry;
	struct port_member_mac *pcentry;
	int found = 0;

	//0. sanity check
	if (group_addr == NULL || member_addr == NULL)
		return;

	//1. find old group
	list_for_each_entry(pgentry, &p->igmp_group_list, list) {
		if (!memcmp(pgentry->grpmac, group_addr, 6)) {
			found = 1;
			break;
		}
	}

	if (!found) {
		print_debug("dbg : Can't delete 0x%02x%02x%02x%02x%02x%02x, group NOT FOUND.\n",
				group_addr[0],
				group_addr[1],
				group_addr[2],
				group_addr[3],
				group_addr[4],
				group_addr[5]);
		return;
	}

	//2. find old client mac
	found = 0;
	list_for_each_entry(pcentry, &pgentry->member_list, list) {
		if (!memcmp(pcentry->member_mac,member_addr, 6)) {
			found = 1;
			break;
		}
	}

	if (found) {
		/* member to be deleted FOUND, DELETE IT ! */
		list_del(&pcentry->list);
		kfree(pcentry);
		print_debug("dbg : Delete client 0x%02x%02x%02x%02x%02x%02x in group 0x%02x%02x%02x%02x%02x%02x\n",
				member_addr[0],
				member_addr[1],
				member_addr[2],
				member_addr[3],
				member_addr[4],
				member_addr[5],
				group_addr[0],
				group_addr[1],
				group_addr[2],
				group_addr[3],
				group_addr[4],
				group_addr[5]);
	}
	else {
		/* do nothing, just debug */
		print_debug("dbg : Can't delete client 0x%02x%02x%02x%02x%02x%02x, client NOT FOUND in group 0x%02x%02x%02x%02x%02x%02x\n",
				member_addr[0],
				member_addr[1],
				member_addr[2],
				member_addr[3],
				member_addr[4],
				member_addr[5],
				group_addr[0],
				group_addr[1],
				group_addr[2],
				group_addr[3],
				group_addr[4],
				group_addr[5]);
	}

	//3. if group is empty, delete group
	if (list_empty(&pgentry->member_list)) {
		list_del(&pgentry->member_list);
		list_del(&pgentry->list);
		kfree(pgentry);

		//remove group mac from port_list
		print_debug("dbg : Delete group 0x%02x%02x%02x%02x%02x%02x since its empty \n",
				group_addr[0],
				group_addr[1],
				group_addr[2],
				group_addr[3],
				group_addr[4],
				group_addr[5]);
		return;
	}
}

static int proc_read_alpha_multicast(
		char *buf,
		char **start,
		off_t offset,
		int len,
		int *eof,
		void *data)
{
	int count = 0;
	struct net_bridge *br = (struct net_bridge *) data;
	struct net_bridge_port *p;
	struct port_group_mac *pgentry;
	struct port_member_mac *pmentry;

	spin_lock_bh(&br->lock);	// bridge lock
	printk("**********************************************************************\n");
	printk("* bridge name    : %s\n", br->dev->name);
	printk("* snooping         : %d\n", br->snooping);
	printk("**********************************************************************\n");
	list_for_each_entry_rcu(p, &br->port_list, list) {
		printk("* ==============================================================\n");
		printk("* <%d> port name : %s\n", p->port_no, p->dev->name);
		printk("* <%d> wireless_interface -> %d\n", p->port_no, atomic_read(&p->wireless_interface));

		//traverse through all group list, list all the members inside
		list_for_each_entry(pgentry, &p->igmp_group_list, list) {
			printk(" Group Mac  0x%02x%02x%02x%02x%02x%02x\n",
					pgentry->grpmac[0],
					pgentry->grpmac[1],
					pgentry->grpmac[2],
					pgentry->grpmac[3],
					pgentry->grpmac[4],
					pgentry->grpmac[5]);

			list_for_each_entry(pmentry, &pgentry->member_list, list) {
				printk("   membermac 0x%02x%02x%02x%02x%02x%02x\n",
						pmentry->member_mac[0],
						pmentry->member_mac[1],
						pmentry->member_mac[2],
						pmentry->member_mac[3],
						pmentry->member_mac[4],
						pmentry->member_mac[5]);
			}
		}

		printk("* ==============================================================\n");
	}	// list_for_each_entry_rcu() - END

	printk("**********************************************************************\n");
	spin_unlock_bh(&br->lock);	// bridge unlock

	*eof = 1;
	return count;
}

extern void fdb_delete(struct net_bridge_fdb_entry *f);

static int proc_write_alpha_multicast(
		struct file *file,
		const char *buf,
		unsigned long count,
		void *data)
{
	int len = MESSAGE_LENGTH + 1;
	char message[len];
	char *msgDelim = MESSAGE_DELIM;
	char *pmesg;
	char *action_token, *action;
	struct net_bridge *br;

	if (count > MESSAGE_LENGTH) {len = MESSAGE_LENGTH;}
	else {len = count; }
	if (copy_from_user(message, buf, len))
		return -EFAULT;

	message[len - 1] = '\0';

	/* split input message that get from user space
	 * token[0] => action token --> add or remove
	 * token[1] => multicast group mac address
	 * token[2] => member MAC address of host
	 */
	pmesg = message;

	action_token = strsep(&pmesg, msgDelim);

	br = (struct net_bridge *) data;

	/* ============================  set wireless enhance =====================*/
	action = ACTION_SET_ENHANCE;
	if (memcmp(action_token, action, sizeof(ACTION_SET_ENHANCE) )== 0) {
		spin_lock_bh(&br->lock);	// bridge lock
		if (table_setwl(br, pmesg, ENABLE) != 0) {
			print_debug("[BR_IGMPP_PROC]->WARNING SETWL FAILURE-> %s\n", pmesg);
		}

		spin_unlock_bh(&br->lock);	// bridge unlock for goto proc_write_br_igmpp_out
		goto proc_write_br_igmpp_out;
	}

	/* ============================  unset wireless enhance  ===================*/
	action = ACTION_UNSET_ENHANCE;
	if (memcmp(action_token, action, sizeof(ACTION_UNSET_ENHANCE)) == 0) {
		spin_lock_bh(&br->lock);	// bridge lock
		if (table_setwl(br, pmesg, DISABLE) != 0) {
			print_debug(KERN_INFO "[BR_IGMPP_PROC]->WARNING SETWL FAILURE-> %s\n", pmesg);
		}

		spin_unlock_bh(&br->lock);	// bridge unlock for goto proc_write_br_igmpp_out
		goto proc_write_br_igmpp_out;
	}

	/* ============================  set snooping ============================*/
	action = ACTION_SET_SNOOPING;
	if (memcmp(action_token, action, sizeof(ACTION_SET_SNOOPING)) == 0) {
		spin_lock_bh(&br->lock);	// bridge lock
		if (table_setsnoop(br, ENABLE) != 0) {
			print_debug(KERN_INFO "[BR_IGMPP_PROC]->WARNING SET snooping FAILURE-> %s\n", pmesg);
		}

		spin_unlock_bh(&br->lock);	// bridge unlock for goto proc_write_br_igmpp_out
		goto proc_write_br_igmpp_out;
	}

	/* ============================  unset snooping ==========================*/
	action = ACTION_UNSET_SNOOPING;
	if (memcmp(action_token, action, sizeof(ACTION_UNSET_SNOOPING)) == 0) {
		spin_lock_bh(&br->lock);	// bridge lock
		if (table_setsnoop(br, DISABLE) != 0) {
			print_debug(KERN_INFO "[BR_IGMPP_PROC]->WARNING UNSET snooping FAILURE-> %s\n", pmesg);
		}

		spin_unlock_bh(&br->lock);	// bridge unlock for goto proc_write_br_igmpp_out
		goto proc_write_br_igmpp_out;
	}

	/* ============================  add - START =====================================*/
	action = ACTION_ADD;
	if (memcmp(action_token, action, sizeof(ACTION_ADD)) == 0) {
		/********** add - START of processing input string **********/
		char *token[2] = {0, 0};
		int i;
		unsigned char mac_addr[6];
		unsigned char grp_mac_addr[6];
		struct net_bridge_fdb_entry *hit_fdb_entry;

		for (i = 0; i < 2; ++i)
			token[i] = strsep(&pmesg, msgDelim);

		/* Only accept MAC, split host MAC address */
		if (check_mac(token[0]) == -1 || check_mac(token[1]) == -1) {
			print_debug(KERN_INFO "[BR_IGMPP_PROC]-> Host MAC address: %s,%s is illegal !!\n",
					(token[0]) ? (token[0]) : "null",
					(token[1]) ? (token[1]) : "null");
			goto proc_write_br_igmpp_out;
		}

		/* Selmon - 20120320
		 * Do spin_lock_bh() later.
		 */
//		spin_lock_bh(&br->lock);	// bridge lock
		split_MAC(grp_mac_addr, token[0]);
		split_MAC(mac_addr, token[1]);

		print_debug("brg : group 0x%02x%02x%02x%02x%02x%02x member 0x%02x%02x%02x%02x%02x%02x\n",
				grp_mac_addr[0],
				grp_mac_addr[1],
				grp_mac_addr[2],
				grp_mac_addr[3],
				grp_mac_addr[4],
				grp_mac_addr[5],
				mac_addr[0],
				mac_addr[1],
				mac_addr[2],
				mac_addr[3],
				mac_addr[4],
				mac_addr[5]);

		/* Selmon - 20120320
		 * spin_lock_bh() to protect struct net_bridge,
		 * rcu_read_lock() to protect struct net_bridge_fdb_entry
		 */
		spin_lock_bh(&br->lock);
		rcu_read_lock();

		/* searching bridge_fdb_entry */
		hit_fdb_entry = __br_fdb_get(br, mac_addr);

		/* NOTE: The effect of successful called br_fdb_get() also takes lock bridge and reference counts. */
		if (hit_fdb_entry != NULL) {
			table_add(hit_fdb_entry->dst, grp_mac_addr, mac_addr);
			/* Selmon - 20120320
			 * Calling fdb_delete() was
			 * for releasing locks br_fdb_get() did before.
			 * But looks like we do not need it now
			 * because __br_fdb_get() seems not to acquire any lock.
			 * Therefore, remove it temporarily.
			 */
//			fdb_delete(hit_fdb_entry);	// release br_fdb_get() locks
		}
		else {
			print_debug(KERN_INFO "The return value of __br_fdb_get() is NULL -> MAC: %X:%X:%X:%X:%X:%X \n",
					mac_addr[0],
					mac_addr[1],
					mac_addr[2],
					mac_addr[3],
					mac_addr[4],
					mac_addr[5]);
		}

		/* Selmon - 20120320
		 * rcu_read_unlock() to release the lock
		 * for struct net_bridge_fdb_entry.
		 * spin_unlock_bh() to release the lock
		 * for struct net_bridge.
		 */
		rcu_read_unlock();
		spin_unlock_bh(&br->lock);

		//do snoop if implemented in switch
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
		if (add_member) add_member(grp_mac_addr, mac_addr);
#else
		if (add_member_cb1) add_member(grp_mac_addr, mac_addr);
#endif
		goto proc_write_br_igmpp_out;
	}

	action = ACTION_REMOVE;
	if (memcmp(action_token, action, sizeof(ACTION_REMOVE)) == 0) {
		char *token[2] = {0, 0};
		int i;
		unsigned char mac_addr[6];
		struct net_bridge_fdb_entry *hit_fdb_entry;
		unsigned char grp_mac_addr[6];

		for(i = 0; i < 2; ++i)
			token[i] = strsep(&pmesg, msgDelim);

		/* Only accept MAC, split host MAC address */
		if (check_mac(token[0]) == -1 || check_mac(token[1]) == -1) {
			print_debug(KERN_INFO "[BR_IGMPP_PROC]-> Host MAC address: %s,%s is illegal !!\n",
					(token[0]) ? (token[0]) : "null",
					(token[1]) ? (token[1]) : "null");
			goto proc_write_br_igmpp_out;
		}

		/* Selmon - 20120320
		 * Do spin_lock_bh() later.
		 */
//		spin_lock_bh(&br->lock);	// bridge lock
		split_MAC(grp_mac_addr, token[0]);
		split_MAC(mac_addr, token[1]);

		print_debug("brg : group 0x%02x%02x%02x%02x%02x%02x member 0x%02x%02x%02x%02x%02x%02x\n",
				grp_mac_addr[0],
				grp_mac_addr[1],
				grp_mac_addr[2],
				grp_mac_addr[3],
				grp_mac_addr[4],
				grp_mac_addr[5],
				mac_addr[0],
				mac_addr[1],
				mac_addr[2],
				mac_addr[3],
				mac_addr[4],
				mac_addr[5]);

		/* Selmon - 20120320
		 * spin_lock_bh() to protect struct net_bridge,
		 * rcu_read_lock() to protect struct net_bridge_fdb_entry
		 */
		spin_lock_bh(&br->lock);
		rcu_read_lock();

		/* searching bridge_fdb_entry */
		hit_fdb_entry = __br_fdb_get(br, mac_addr);

		/* NOTE: The effect of successful called __br_fdb_get() also takes lock bridge and reference counts. */
		if (hit_fdb_entry != NULL) {
			table_remove(hit_fdb_entry->dst, grp_mac_addr, mac_addr);
			/* Selmon - 20120320
			 * Calling fdb_delete() was
			 * for releasing locks br_fdb_get() did before.
			 * But looks like we do not need it now
			 * because __br_fdb_get() seems not to acquire any lock.
			 * Therefore, remove it temporarily.
			 */
//			fdb_delete(hit_fdb_entry);	// release br_fdb_get() locks
		}
		else {
			print_debug(KERN_INFO "The return value of __br_fdb_get() is NULL -> MAC: %X:%X:%X:%X:%X:%X \n",
					mac_addr[0],
					mac_addr[1],
					mac_addr[2],
					mac_addr[3],
					mac_addr[4],
					mac_addr[5]);
		}

		/* Selmon - 20120320
		 * rcu_read_unlock() to release the lock
		 * for struct net_bridge_fdb_entry.
		 * spin_unlock_bh() to release the lock
		 * for struct net_bridge.
		 */
		rcu_read_unlock();
		spin_unlock_bh(&br->lock);

		//do snoop if implemented in switch
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
		if (del_member) del_member(grp_mac_addr, mac_addr);
#else
		if (del_member_cb1) del_member(grp_mac_addr, mac_addr);
#endif

		goto proc_write_br_igmpp_out;
	}
	/* ============================= remove - END ======================================*/

proc_write_br_igmpp_out:
	return len;
}
#endif

static struct net_device *new_bridge_dev(struct net *net, const char *name)
{
	struct net_bridge *br;
	struct net_device *dev;
#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
	char alpha_proc_name[32];
#endif

	dev = alloc_netdev(sizeof(struct net_bridge), name,
			   br_dev_setup);

	if (!dev)
		return NULL;
	dev_net_set(dev, net);

	br = netdev_priv(dev);
	br->dev = dev;

	br->stats = alloc_percpu(struct br_cpu_netstats);
	if (!br->stats) {
		free_netdev(dev);
		return NULL;
	}

	spin_lock_init(&br->lock);
	INIT_LIST_HEAD(&br->port_list);
	spin_lock_init(&br->hash_lock);

	br->bridge_id.prio[0] = 0x80;
	br->bridge_id.prio[1] = 0x00;

	memcpy(br->group_addr, br_group_address, ETH_ALEN);

	br->feature_mask = dev->features;
	br->stp_enabled = BR_NO_STP;
	br->designated_root = br->bridge_id;
	br->root_path_cost = 0;
	br->root_port = 0;
	br->bridge_max_age = br->max_age = 20 * HZ;
	br->bridge_hello_time = br->hello_time = 2 * HZ;
	br->bridge_forward_delay = br->forward_delay = 15 * HZ;
	br->topology_change = 0;
	br->topology_change_detected = 0;
	br->ageing_time = 300 * HZ;

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
	
	snprintf(alpha_proc_name, sizeof(alpha_proc_name), "alpha/multicast_%s", name);
	br->alpha_multicast_proc = create_proc_entry(alpha_proc_name, 0644, NULL);
	if (br->alpha_multicast_proc == NULL) {
		printk("create  proc FAILED %s\n", name);
		return ERR_PTR(-ENOMEM);
	}

	br->alpha_multicast_proc->data = (void *) br;
	br->alpha_multicast_proc->read_proc = proc_read_alpha_multicast;
	br->alpha_multicast_proc->write_proc = proc_write_alpha_multicast;
	br->snooping = 0;

#ifndef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	sw_init();
#endif
#endif

	br_netfilter_rtable_init(br);

	br_stp_timer_init(br);
	br_multicast_init(br);

	return dev;
}

/* find an available port number */
static int find_portno(struct net_bridge *br)
{
	int index;
	struct net_bridge_port *p;
	unsigned long *inuse;

	inuse = kcalloc(BITS_TO_LONGS(BR_MAX_PORTS), sizeof(unsigned long),
			GFP_KERNEL);
	if (!inuse)
		return -ENOMEM;

	set_bit(0, inuse);	/* zero is reserved */
	list_for_each_entry(p, &br->port_list, list) {
		set_bit(p->port_no, inuse);
	}
	index = find_first_zero_bit(inuse, BR_MAX_PORTS);
	kfree(inuse);

	return (index >= BR_MAX_PORTS) ? -EXFULL : index;
}

/* called with RTNL but without bridge lock */
static struct net_bridge_port *new_nbp(struct net_bridge *br,
				       struct net_device *dev)
{
	int index;
	struct net_bridge_port *p;

	index = find_portno(br);
	if (index < 0)
		return ERR_PTR(index);

	p = kzalloc(sizeof(*p), GFP_KERNEL);
	if (p == NULL)
		return ERR_PTR(-ENOMEM);

	p->br = br;
	dev_hold(dev);
	p->dev = dev;
	p->path_cost = port_cost(dev);
	p->priority = 0x8000 >> BR_PORT_BITS;
	p->port_no = index;
	p->flags = 0;
	br_init_port(p);
	p->state = BR_STATE_DISABLED;
	br_stp_port_timer_init(p);
	br_multicast_add_port(p);

	return p;
}

static struct device_type br_type = {
	.name	= "bridge",
};

int br_add_bridge(struct net *net, const char *name)
{
	struct net_device *dev;
	int ret;

	dev = new_bridge_dev(net, name);
	if (!dev)
		return -ENOMEM;

	rtnl_lock();
	if (strchr(dev->name, '%')) {
		ret = dev_alloc_name(dev, dev->name);
		if (ret < 0)
			goto out_free;
	}

	SET_NETDEV_DEVTYPE(dev, &br_type);

	ret = register_netdevice(dev);
	if (ret)
		goto out_free;

	ret = br_sysfs_addbr(dev);
	if (ret)
		unregister_netdevice(dev);
 out:
	rtnl_unlock();
	return ret;

out_free:
	free_netdev(dev);
	goto out;
}

int br_del_bridge(struct net *net, const char *name)
{
	struct net_device *dev;
	int ret = 0;
#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
	char alpha_proc_name[32];
#endif

	rtnl_lock();
	dev = __dev_get_by_name(net, name);
	if (dev == NULL)
		ret =  -ENXIO; 	/* Could not find device */

	else if (!(dev->priv_flags & IFF_EBRIDGE)) {
		/* Attempt to delete non bridge device! */
		ret = -EPERM;
	}

	else if (dev->flags & IFF_UP) {
		/* Not shutdown yet. */
		ret = -EBUSY;
	}

	else
		del_br(netdev_priv(dev), NULL);

	rtnl_unlock();

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
#ifndef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	sw_deinit();
#endif
	print_debug("remove proc entry %s\n", name);
	snprintf(alpha_proc_name, sizeof(alpha_proc_name), "alpha/multicast_%s", name);
	remove_proc_entry(alpha_proc_name, 0);
#endif

	return ret;
}

/* MTU of the bridge pseudo-device: ETH_DATA_LEN or the minimum of the ports */
int br_min_mtu(const struct net_bridge *br)
{
	const struct net_bridge_port *p;
	int mtu = 0;

	ASSERT_RTNL();

	if (list_empty(&br->port_list))
		mtu = ETH_DATA_LEN;
	else {
		list_for_each_entry(p, &br->port_list, list) {
			if (!mtu  || p->dev->mtu < mtu)
				mtu = p->dev->mtu;
		}
	}
	return mtu;
}

/*
 * Recomputes features using slave's features
 */
void br_features_recompute(struct net_bridge *br)
{
	struct net_bridge_port *p;
	unsigned long features, mask;

	features = mask = br->feature_mask;
	if (list_empty(&br->port_list))
		goto done;

	features &= ~NETIF_F_ONE_FOR_ALL;

	list_for_each_entry(p, &br->port_list, list) {
		features = netdev_increment_features(features,
						     p->dev->features, mask);
	}

done:
	br->dev->features = netdev_fix_features(features, NULL);
}

/* called with RTNL */
int br_add_if(struct net_bridge *br, struct net_device *dev)
{
	struct net_bridge_port *p;
	int err = 0;

	/* Don't allow bridging non-ethernet like devices */
	if ((dev->flags & IFF_LOOPBACK) ||
	    dev->type != ARPHRD_ETHER || dev->addr_len != ETH_ALEN)
		return -EINVAL;

	/* No bridging of bridges */
	if (dev->netdev_ops->ndo_start_xmit == br_dev_xmit)
		return -ELOOP;

	/* Device is already being bridged */
	if (br_port_exists(dev))
		return -EBUSY;

	/* No bridging devices that dislike that (e.g. wireless) */
	if (dev->priv_flags & IFF_DONT_BRIDGE)
		return -EOPNOTSUPP;

	p = new_nbp(br, dev);
	if (IS_ERR(p))
		return PTR_ERR(p);

	err = dev_set_promiscuity(dev, 1);
	if (err)
		goto put_back;

	err = kobject_init_and_add(&p->kobj, &brport_ktype, &(dev->dev.kobj),
				   SYSFS_BRIDGE_PORT_ATTR);
	if (err)
		goto err0;

	err = br_fdb_insert(br, p, dev->dev_addr);
	if (err)
		goto err1;

	err = br_sysfs_addif(p);
	if (err)
		goto err2;

	if (br_netpoll_info(br) && ((err = br_netpoll_enable(p))))
		goto err3;

	err = netdev_rx_handler_register(dev, br_handle_frame, p);
	if (err)
		goto err3;

	dev->priv_flags |= IFF_BRIDGE_PORT;

	dev_disable_lro(dev);

	list_add_rcu(&p->list, &br->port_list);

	spin_lock_bh(&br->lock);

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
	INIT_LIST_HEAD(&p->igmp_group_list);
	atomic_set(&p->wireless_interface, 0);
#endif

	br_stp_recalculate_bridge_id(br);
	br_features_recompute(br);

	if ((dev->flags & IFF_UP) && netif_carrier_ok(dev) &&
	    (br->dev->flags & IFF_UP))
		br_stp_enable_port(p);
	spin_unlock_bh(&br->lock);

	br_ifinfo_notify(RTM_NEWLINK, p);

	dev_set_mtu(br->dev, br_min_mtu(br));

	kobject_uevent(&p->kobj, KOBJ_ADD);

	return 0;
err3:
	sysfs_remove_link(br->ifobj, p->dev->name);
err2:
	br_fdb_delete_by_port(br, p, 1);
err1:
	kobject_put(&p->kobj);
	p = NULL; /* kobject_put frees */
err0:
	dev_set_promiscuity(dev, -1);
put_back:
	dev_put(dev);
	kfree(p);
	return err;
}

/* called with RTNL */
int br_del_if(struct net_bridge *br, struct net_device *dev)
{
	struct net_bridge_port *p;

	if (!br_port_exists(dev))
		return -EINVAL;

	p = br_port_get(dev);
	if (p->br != br)
		return -EINVAL;

	del_nbp(p);

	spin_lock_bh(&br->lock);
	br_stp_recalculate_bridge_id(br);
	br_features_recompute(br);
	spin_unlock_bh(&br->lock);

	return 0;
}

void __net_exit br_net_exit(struct net *net)
{
	struct net_device *dev;
	LIST_HEAD(list);

	rtnl_lock();
	for_each_netdev(net, dev)
		if (dev->priv_flags & IFF_EBRIDGE)
			del_br(netdev_priv(dev), &list);

	unregister_netdevice_many(&list);
	rtnl_unlock();

}
