/*
 *	FAN driver for the Ubicom32 platform
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
 *
 * Refer to drivers/video/backlight/ubicom32bl.c
 */
#include <linux/init.h>
#include <linux/kernel.h>
#include <linux/module.h>
#include <linux/uaccess.h>
#include <linux/proc_fs.h>
#include <asm/gpio.h>

#define PROC_NAME	"fanctl"
#define DRIVER_NAME	"ubi32-fan"
#define UBICOM32BL_MAX_BRIGHTNESS	255
#define FAN_GPIO	GPIO_PG5_3
#define PWM_CHANNEL	1

#define UBICOM32FAN_NUM_PWM_CHANNELS 4
static volatile u32_t *ubicom32fan_channel_ctl1[] = {
	&(UBICOM32_IO_PORT(IO_PORT_RQ)->ctl1),
	&(UBICOM32_IO_PORT(IO_PORT_RR)->ctl1),
	&(UBICOM32_IO_PORT(IO_PORT_RS)->ctl1),
	&(UBICOM32_IO_PORT(IO_PORT_RT)->ctl1),
};
static volatile u32_t *ubicom32fan_channel_ctl0[] = {
	&(UBICOM32_IO_PORT(IO_PORT_RQ)->ctl0),
	&(UBICOM32_IO_PORT(IO_PORT_RR)->ctl0),
	&(UBICOM32_IO_PORT(IO_PORT_RS)->ctl0),
	&(UBICOM32_IO_PORT(IO_PORT_RT)->ctl0),
};
/*
 * Functoin Control0,	0:7,	Sets the non-overlap margin of pwm_out_n.
 * 				pwm_out_n. The margin is specified in timer cycles.
 * Function Control0,	11:8,	Timer prescaler. The PWM timer is clocked 
 * 				at a rate of (clk_core / (2^pre_scale[3:0])).
 * Function Control0,	12,	Invert the polarity of the output signals.
 * Function Control1,	15:0,	High-time duty cycle of PWM ouput.
 * 				pwm_out, specified in number of timer cycles.
 * Function Control1,	31:16,	Period of time, specified in number of timer cycles.
 *
 */
static unsigned int period = 60; 
static unsigned int duty = 30;
static unsigned int pre_scale = 11;
static unsigned int nol_margin = 0;
static unsigned int output_invert = 0;
static unsigned int pwm_channel = PWM_CHANNEL;

static struct proc_dir_entry *fanctl_proc;

#if 0
/*
 * ubicom32fan_set_intensity_gpio
 */
static int ubicom32fan_set_intensity_gpio(struct ubicom32bl_data *ud, int intensity)
{
	ud->cur_intensity = intensity ? 255 : 0;

	if (intensity) {
		gpio_set_value(ud->pdata->gpio, !ud->pdata->invert);
		return 0;
	}

	gpio_set_value(ud->pdata->gpio, ud->pdata->invert);
	return 0;
}
#endif

/*
 * ubicom32fan_set_intensity_hw
 */
static int ubicom32fan_set_intensity_hw(void)
{
	//u16_t period = 60;
	//u16_t pwm_period = 60;
	u32_t pwm_channel = PWM_CHANNEL;
	int intensity = 100;
	int cur_intensity;

	if (pwm_channel > UBICOM32FAN_NUM_PWM_CHANNELS) {
		return -ENODEV;
	}

	/*
	 * Calculate the new duty cycle
	 */
#if 0
	if (intensity == UBICOM32BL_MAX_BRIGHTNESS) {
		duty = pwm_period + 1;
	} else {
		duty = (pwm_period * intensity) / UBICOM32BL_MAX_BRIGHTNESS;
	}
#endif
	/*
	 * Set the new duty cycle
	 */
	printk(KERN_INFO DRIVER_NAME ": period = %d, duty = %d\n", period, duty);
	/* period 31:16, duty 15:0 */
	*ubicom32fan_channel_ctl1[pwm_channel] = (period << 16) | duty;
	printk(KERN_INFO DRIVER_NAME ": pre_scale = %d, nol_margin = %d, output_invert = %d\n", pre_scale, nol_margin, output_invert);
	/* pre_scale 11:8, nol_margin 7:0 */
	*ubicom32fan_channel_ctl0[pwm_channel] = ((output_invert << 12) | (pre_scale << 8) | nol_margin) & 0x1fff;

	cur_intensity = intensity;

	return 0;
}

/*
 * ubicom32fan_init_hw_pwm
 *	Set the appropriate PWM registers
 */
static int ubicom32fan_init_hw_pwm(void)
{
	unsigned fan_gpio = FAN_GPIO;

	struct ubicom32_io_port *port = UBICOM32_IO_PORT(IO_PORT_RQ + (pwm_channel * 0x4000));
	struct ubicom32_gpio_port *gpio = UBICOM32_PORT_NUM_TO_GPIO(gpio_bank(fan_gpio));
	unsigned int mask = 0;

	/*
	 * Check to see if the pin is busy so we don't clobber any functions
	 */
	u32_t fn_sel = gpio->fn_sel[0] | gpio->fn_sel[1] | gpio->fn_sel[2] | gpio->fn_sel[3];
	if (fn_sel & gpio_bit(fan_gpio)) {
		printk(KERN_WARNING DRIVER_NAME ": GPIO %d in use %08x\n", 
			fan_gpio, fn_sel);
		return -EBUSY;
	}

	if (pwm_channel > UBICOM32FAN_NUM_PWM_CHANNELS) {
		return -ENODEV;
	}

	switch (pwm_channel) {
		case 0:
			/*
			 * PWM Q can go on PG4[30](fn4), PG4[31](fn4), PG5[0](fn3), PG5[1](fn3)
			 */
			if (fan_gpio == GPIO_PG4_30) {
				mask = (1 << 30);
				gpio->fn_sel[3] |= mask;
			} else
			if (fan_gpio == GPIO_PG4_31) {
				mask = (1 << 31);
				gpio->fn_sel[3] |= mask;
			} else
			if (fan_gpio == GPIO_PG5_0) {
				mask = (1 << 0);
				gpio->fn_sel[2] |= mask;
			} else
			if (fan_gpio != GPIO_PG5_1) {
				mask = (1 << 1);
				gpio->fn_sel[2] |= mask;
			} else {
				printk(KERN_WARNING DRIVER_NAME ": GPIO %d invalid for pwm channel %d\n", 
					fan_gpio, pwm_channel);
				return -EINVAL;
			}
			break;

		case 1:
			/*
			 * PWM R can go on PG5[3](fn3), PG5[2](fn3)
			 */
			if (fan_gpio == GPIO_PG5_3) {
				mask = (1 << 3);
				gpio->fn_sel[2] |= mask; /* PG5_FN3_SEL */
			} else
			if (fan_gpio != GPIO_PG5_2) {
				mask = (1 << 2);
				gpio->fn_sel[2] |= mask;
			} else {
				printk(KERN_WARNING DRIVER_NAME ": GPIO %d invalid for pwm channel %d\n", 
					fan_gpio, pwm_channel);
				return -EINVAL;
			}
			break;

		case 2:
			/*
			 * PWM S can go on PG5[1](fn2), PG5[0](fn2)
			 */
			if (fan_gpio == GPIO_PG5_1) {
				mask = (1 << 1);
				gpio->fn_sel[1] |= mask;
			} else
			if (fan_gpio != GPIO_PG5_0) {
				mask = (1 << 0);
				gpio->fn_sel[1] |= mask;
			} else {
				printk(KERN_WARNING DRIVER_NAME ": GPIO %d invalid for pwm channel %d\n", 
					fan_gpio, pwm_channel);
				return -EINVAL;
			}
			break;

		case 3:
			/*
			 * PWM T can go on PG5[3](fn2), PG5[2](fn2)
			 */
			if (fan_gpio == GPIO_PG5_3) {
				mask = (1 << 3);
				gpio->fn_sel[1] |= mask;
			} else
			if (fan_gpio != GPIO_PG5_2) {
				mask = (1 << 2);
				gpio->fn_sel[1] |= mask;
			} else {
				printk(KERN_WARNING DRIVER_NAME ": GPIO %d invalid for pwm channel %d\n", 
					fan_gpio, pwm_channel);
				return -EINVAL;
			}
			break;
	}

	/*
	 * PWM is clocked at clk_core / (2 ^ pwm_prescale[3:0])
	 */
	port->function = 1<<24 | 1;
	port->ctl0 = (pre_scale << 8); /* PWM pre_scale */
	gpio->gpio_ctl |= mask; /* PGx_GPIO_DIR */

	/* PWM reset */
	port->function |= 1<<4;
	port->function ^= 1<<4;
	return 0;
}

#if 0
/*
 * ubicom32fan_init_gpio
 *	Allocate the appropriate GPIO
 */
static int ubicom32fan_init_gpio()
{
	if (gpio_request(GPIO_PG5_3, "BLCTL GPIO")) {
		printk(KERN_WARNING DRIVER_NAME ": Could not request BLCTL gpio %d\n", GPIO_PG5_3);
		return -EBUSY;
	}
	gpio_direction_output(GPIO_PG5_3, 0);

	return 0;
}
#endif

static int proc_read_fan_config(char * buf, char ** start, off_t offset,
		int count, int * eof, void * data)
{
	char * p = buf;
	period = (*ubicom32fan_channel_ctl1[pwm_channel] >> 16) & 0xffff;
	duty = *ubicom32fan_channel_ctl1[pwm_channel] & 0xffff;

	pre_scale = (*ubicom32fan_channel_ctl0[pwm_channel] >> 8) & 0xf;
	nol_margin = *ubicom32fan_channel_ctl0[pwm_channel] & 0xff;
	output_invert = (*ubicom32fan_channel_ctl0[pwm_channel] >> 12) & 0x1;
	p += sprintf(p, "period duty pre_scale nol_margin output_invert\n");
	p += sprintf(p, "%d %d %d %d %d\n", period, duty, pre_scale, nol_margin, output_invert);
	*eof = 1;
	return p - buf;
}
static int proc_write_fan_config(struct file * file, const char * buffer,
		unsigned long count, void * data)
{
	char buf[20];
	char *ptr = (char *) buf;
	unsigned int tmp_period = 0, tmp_duty = 0, tmp_scale = 0, tmp_margin = 0, tmp_invert = 0;
	
	if (count > sizeof(buf))
		return -EINVAL;

	if (copy_from_user(buf, buffer, count))
		return -EFAULT;

	/* Function Control 1 register, period 31:16*/
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	tmp_period = simple_strtoul(ptr, &ptr, 10);
	if ((tmp_period < 0) || (tmp_period > 65535)) {
		printk(KERN_WARNING DRIVER_NAME" : invalid parameter for pwm_period %d\n", tmp_period);
		return count;
	}

	/* Function Control 1 register, duty 15:0 */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	tmp_duty = simple_strtoul(ptr, &ptr, 10);
	if ((tmp_duty < 0) || (tmp_duty > 65535)) {
		printk(KERN_WARNING DRIVER_NAME" : invalid parameter for pwm_duty %d\n", tmp_duty);
		return count;
	}

	/* Function Control 0, pre_scale 11:8 */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	tmp_scale = simple_strtoul(ptr, &ptr, 10);
	if ((tmp_scale < 0) || (tmp_scale > 15)) {
		printk(KERN_WARNING DRIVER_NAME" : invalid parameter for pre_scale %d\n", tmp_scale);
		return count;
	}

	/* Function Control 0, nol_margin 7:0 */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	tmp_margin = simple_strtoul(ptr, &ptr, 10);
	if ((tmp_margin < 0) || (tmp_margin > 255)) {
		printk(KERN_WARNING DRIVER_NAME" : invalid parameter for nol_margin %d\n", tmp_margin);
		return count;
	}

	/* Function Control 0, output_invert 12 */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	tmp_invert = simple_strtoul(ptr, &ptr, 10);
	if ((tmp_invert != 0) && (tmp_invert != 1)) {
		printk(KERN_WARNING DRIVER_NAME" : invalid parameter for output_invert %d\n", tmp_invert);
		return count;
	}

	period = tmp_period;
	duty = tmp_duty;
	pre_scale = tmp_scale;
	nol_margin = tmp_margin;
	output_invert = tmp_invert;
	ubicom32fan_set_intensity_hw();
	return count;
}

/*
 * ubicom32fan_init
 */
static int __init ubicom32fan_init(void)
{
	printk(KERN_INFO DRIVER_NAME ": ubicom32fan_init\n");

	fanctl_proc = create_proc_entry(PROC_NAME, 0644, 0);
	if (fanctl_proc) {
		fanctl_proc->read_proc = (read_proc_t *) proc_read_fan_config;
		fanctl_proc->write_proc = (write_proc_t *) proc_write_fan_config;
	}
	else {
		printk(KERN_WARNING DRIVER_NAME" : Unable to create proc entry\n");
	}

	//ubicom32fan_init_gpio();
	ubicom32fan_init_hw_pwm();
	ubicom32fan_set_intensity_hw();
	printk(KERN_INFO DRIVER_NAME ": HW PWM\n");
	return 0;
}
module_init(ubicom32fan_init);

/*
 * ubicom32fan_exit
 */
static void __exit ubicom32fan_exit(void)
{
	remove_proc_entry(PROC_NAME, 0);

	printk(KERN_INFO DRIVER_NAME ": ubicom32fan_exit\n");
}
module_exit(ubicom32fan_exit);

MODULE_AUTHOR("BSD6 <@alphanetworks.com>");
MODULE_DESCRIPTION("Ubicom32 FAN driver");
MODULE_LICENSE("GPL");
