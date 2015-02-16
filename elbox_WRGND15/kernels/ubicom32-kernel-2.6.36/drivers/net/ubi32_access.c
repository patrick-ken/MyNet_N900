/* vi: set ts=4 sw=4: */
/*
 * ubi32_access.c
 *
 *	Access UBICOM32 kernel from user mode.
 */

#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>
#include <linux/types.h>
#include <linux/proc_fs.h>
#include <linux/reboot.h>


#define DRV_MODULE_VERSION	"0.1"
#define DRV_MODULE_NAME		"ubi32_access"

MODULE_AUTHOR("David Hsieh <david_hsieh@alphanetworks.com>");
MODULE_DESCRIPTION("UBI32 Access driver");
MODULE_LICENSE("GPL");
MODULE_VERSION(DRV_MODULE_VERSION);

/***************************************************************************/

#define DEBUG

#ifdef DEBUG
#define DPRINTK(fmt, args...) printk("%s: " fmt,__func__, ## args)
#else
#define DPRINTK(fmt, args...) do {} while (0)
#endif

static struct proc_dir_entry *g_system_reset = NULL;
static struct proc_dir_entry *g_proc_ar8327 = NULL;
static struct proc_dir_entry *g_proc_mii = NULL;
static struct proc_dir_entry *g_proc_phy = NULL;

/***************************************************************************/

static int read_system_reset(char * buf, char ** start, off_t offset, int len, int * eof, void * data)
{
	char * p = buf;
	p += sprintf(p, "n/a\n");
	*eof = 1;
	return (int)(p - buf);
}

static int write_system_reset(struct file * file, const char * buf, unsigned long count, void * data)
{
	printk("!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n");
	printk("!!!                      !!!\n");
	printk("!!!    SYSTEM RESTART    !!!\n");
	printk("!!!                      !!!\n");
	printk("!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n");
	machine_restart(NULL);
	return count;
}

/***************************************************************************/

static int mii_read = 0;
static unsigned int mii_reg = 0;
static unsigned int mii_data = 0;
static int phy_read = 0;
static unsigned int phy = 0;
static unsigned int phy_reg = 0;
static unsigned int phy_data = 0;

/* from ultra/projects/bootexec/build/include/UbicomSDK.h  */
#define SYSTEM_FREQ 600000000
#define PG3 (IO_IOM_BASE + 0x60)

#define PORT_GROUP_EVALUATOR(x)		PG##x
#define DECLARE_PORT_GROUP(x)		PORT_GROUP_EVALUATOR(x)

#define MII_MDIO1_PORT		DECLARE_PORT_GROUP(CONFIG_MDIO1_PORT_GROUP)
#define MII_MDIO1_PIN		CONFIG_MDIO1_PIN
#define MII_MDIO2_PORT		DECLARE_PORT_GROUP(CONFIG_MDIO2_PORT_GROUP)
#define MII_MDIO2_PIN		CONFIG_MDIO2_PIN
#define MII_MDC1_PORT		DECLARE_PORT_GROUP(CONFIG_MDC1_PORT_GROUP)
#define MII_MDC1_PIN		CONFIG_MDC1_PIN
#define MII_MDC2_PORT		DECLARE_PORT_GROUP(CONFIG_MDC2_PORT_GROUP)
#define MII_MDC2_PIN		CONFIG_MDC2_PIN
#define MII_DUTY_CYCLE		100

#define MII_PREAMBLE_LENGTH	32
#define MII_OPCODE_LENGTH	4
#define MII_PHY_ADDR_LENGTH	5
#define MII_REG_ADDR_LENGTH	5
#define MII_HEADER_LENGTH	(MII_OPCODE_LENGTH + MII_PHY_ADDR_LENGTH + MII_REG_ADDR_LENGTH)
#define MII_REG_DATA_LENGTH	18      /* Includes the 2 bit TA */
#define MII_OPCODE_WRITE	0x05
#define MII_OPCODE_READ		0x06
#define MII_DUTY_CYCLE_TIME	(((SYSTEM_FREQ / 1000000) * MII_DUTY_CYCLE / 1000) + 1)
#define MII_DATA_SETUP_TIME	((SYSTEM_FREQ / 100000000) + 1)

typedef uint16_t (*mii_read_func)(uint32_t phy, uint32_t reg);
typedef void (*mii_write_func)(uint32_t phy, uint32_t reg, int16_t data);

typedef struct ctrlblock{
	mii_read_func mii_read;
	mii_write_func mii_write;
} mdio_ctrl;

uint16_t mii1_read_reg(uint32_t phy, uint32_t reg);
void mii1_write_reg(uint32_t phy, uint32_t reg, int16_t data);

uint16_t mii1_read_reg(uint32_t phy, uint32_t reg)
{
	uint32_t res = 0;
	uint32_t t0 __attribute__ ((unused));
	uint32_t t1 __attribute__ ((unused));
	uint32_t t2 __attribute__ ((unused));
	uint32_t t3 __attribute__ ((unused));

	asm volatile (
	"   bset        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), #"D(MII_MDC1_PIN)"			\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdc]), "D(IO_GPIO_CTL)"(%[mdc]), #"D(MII_MDC1_PIN)"			\n\t"
	"   lsl.4       %[t0], %[phy], #"D(MII_REG_ADDR_LENGTH)"										\n\t"
	"   or.4        %[t0], %[t0], %[reg]															\n\t"
	"   lsl.4       %[t1], #"D(MII_OPCODE_READ)", #"D(MII_PHY_ADDR_LENGTH + MII_REG_ADDR_LENGTH)"	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   bfrvrs      %[t0], %[t0], #"D(32 - MII_HEADER_LENGTH)"										\n\t"
	"   lsl.4       %[t1], #1, #"D(MII_MDC1_PIN)"													\n\t"

	/* Toggle MDC to output preamble. */
	"   move.4      %[t3], #"D(MII_PREAMBLE_LENGTH * 2)"											\n\t"

	"1: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6)"													\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   1b																				\n\t"

	/* Output start, read opcode, phy address and register address. */
	"   move.4      %[t3], #"D(MII_HEADER_LENGTH)"													\n\t"

	"2: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO1_PIN)"							\n\t"
	"   xor.4       %[t2], %[t2], %[t0]																\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   lsl.4       %[t2], %[t2], #"D(MII_MDIO1_PIN)"												\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), %[t2]						\n\t"
	"   cycles      "D(MII_DATA_SETUP_TIME)"														\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6 - MII_DATA_SETUP_TIME)"								\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 7)"													\n\t"
	"   lsr.4       %[t0], %[t0], #1																\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   2b																				\n\t"

	/* Capture TA + data. */
	"   move.4      %[t3], #"D(MII_REG_DATA_LENGTH)"												\n\t"

	"3: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   bclr        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   bclr        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 5)"													\n\t"
	"   lsl.4       %[res], %[res], #1																\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_IN)"(%[mdio]), #"D(MII_MDIO1_PIN)"							\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 8)"													\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   or.4        %[res], %[res], %[t2]															\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   3b																				\n\t"

	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
		: [res] "=d" (res), [t0] "=&d" (t0), [t1] "=d" (t1), [t2] "=d" (t2), [t3] "=d" (t3)
		: [mdio] "a" (MII_MDIO1_PORT), [mdc] "a" (MII_MDC1_PORT), [phy] "d" (phy), [reg] "d" (reg)
		: "cc", "memory"
	);

	return res;
}

void mii1_write_reg(uint32_t phy, uint32_t reg, int16_t data)
{
	uint32_t t0 __attribute__ ((unused));
	uint32_t t1 __attribute__ ((unused));
	uint32_t t2 __attribute__ ((unused));
	uint32_t t3 __attribute__ ((unused));

	asm volatile (
	"   bset        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), #"D(MII_MDC1_PIN)"			\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO1_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdc]), "D(IO_GPIO_CTL)"(%[mdc]), #"D(MII_MDC1_PIN)"			\n\t"
	"   lsl.4       %[t0], %[phy], #"D(MII_REG_ADDR_LENGTH)"										\n\t"
	"   or.4        %[t0], %[t0], %[reg]															\n\t"
	"   lsl.4       %[t1], #"D(MII_OPCODE_WRITE)", #"D(MII_PHY_ADDR_LENGTH + MII_REG_ADDR_LENGTH)"	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   lsl.4       %[t0], %[t0], #"D(MII_REG_DATA_LENGTH)"											\n\t"
	"   bset        %[t0], %[t0], #"D(MII_REG_DATA_LENGTH - 1)"										\n\t"
	"   move.2      %[t1], %[data]																	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   bfrvrs      %[t0], %[t0], #"D(32 - MII_HEADER_LENGTH - MII_REG_DATA_LENGTH)"				\n\t"
	"   lsl.4       %[t1], #1, #"D(MII_MDC1_PIN)"													\n\t"

	/* Toggle MDC to output preamble. */
	"   move.4      %[t3], #"D(MII_PREAMBLE_LENGTH * 2)"											\n\t"

	"1: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6)"													\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   1b																				\n\t"

	/* Output start, write opcode, phy address, register address, TA and data. */
	"   move.4      %[t3], #"D(MII_HEADER_LENGTH + MII_REG_DATA_LENGTH)"							\n\t"

	"2: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO1_PIN)"							\n\t"
	"   xor.4       %[t2], %[t2], %[t0]																\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   lsl.4       %[t2], %[t2], #"D(MII_MDIO1_PIN)"												\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), %[t2]						\n\t"
	"   cycles      "D(MII_DATA_SETUP_TIME)"														\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6 - MII_DATA_SETUP_TIME)"								\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 7)"													\n\t"
	"   lsr.4       %[t0], %[t0], #1																\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   2b																				\n\t"
		: [t0] "=&d" (t0), [t1] "=&d" (t1), [t2] "=d" (t2), [t3] "=d" (t3)
		: [mdio] "a" (MII_MDIO1_PORT), [mdc] "a" (MII_MDC1_PORT),
			[phy] "d" (phy), [reg] "d" (reg), [data] "d" (data)
		: "cc", "memory"
	);
}

static u16_t switch_phy_reg_read(u8_t phy, u8_t reg, mdio_ctrl *p_mdio_ctrl)
{
	u16_t val;
	u32_t flags;

	/* Selmon - 20120322
	 * Per the patch from QCA.
	 */
	local_irq_save(flags);
	
	val = p_mdio_ctrl->mii_read(phy, reg);

	/* Selmon - 20120322
	 * Per the patch from QCA.
	 */
	local_irq_restore(flags);

//	printk("SW PHY read reg[%d:%d] = 0x%x\n", phy, reg, val);
	return val;
}

static void switch_phy_reg_write(u8_t phy, u8_t reg, u16_t data, mdio_ctrl *p_mdio_ctrl)
{
	u32_t flags;

	/* Selmon - 20120322
	 * Per the patch from QCA.
	 */
	local_irq_save(flags);

//	printk("SW PHY write reg[%d:%d] = 0x%x\n", phy, reg, data);
	p_mdio_ctrl->mii_write(phy, reg, data);

	/* Selmon - 20120322
	 * Per the patch from QCA.
	 */
	local_irq_restore(flags);
}

u32_t switch_reg_read(u32_t addr, mdio_ctrl *p_mdio_ctrl)
{
	u8_t phy, reg;
	u32_t val;
	u32_t flags;

	local_irq_save(flags);

	switch_phy_reg_write(0x18, 0, (addr >> 9) & 0x3ff, p_mdio_ctrl);

	/* Read low 16-bit then high 16-bit */
	phy = 0x10 | ((addr >> 6) & 0x7);
	reg = (addr >> 1) & 0x1e;
	val = switch_phy_reg_read(phy, reg, p_mdio_ctrl) & 0xffff;
	val = val | (switch_phy_reg_read(phy, reg + 1, p_mdio_ctrl) << 16);

//	printk("SW read reg[0x%x] = 0x%x\n", addr, val);
	local_irq_restore(flags);
	return val;
}

void switch_reg_write(u32_t addr, u32_t data, mdio_ctrl *p_mdio_ctrl)
{
	u8_t phy, reg;
	u32_t flags;
	local_irq_save(flags);

//	printk("SW write reg[0x%x] = 0x%x\n", addr, data);
	switch_phy_reg_write(0x18, 0, (addr >> 9) & 0x3ff, p_mdio_ctrl);

	/* Write low 16-bit then high 16-bit */
	phy = 0x10 | ((addr >> 6) & 0x7);
	reg = (addr >> 1) & 0x1e;
	switch_phy_reg_write(phy, reg, (data & 0xffff), p_mdio_ctrl);
	switch_phy_reg_write(phy, reg + 1, (data >> 16), p_mdio_ctrl);
	local_irq_restore(flags);
}

static int read_phy_cmd(
		char *buf,
		char **start,
		off_t offset,
		int len,
		int *eof,
		void *data)
{
	return 0;
}

static int write_phy_cmd(
		struct file *file,
		const char *buf,
		unsigned long count,
		void *data)
{
	char *ptr = (char *) buf;

	/* retrieve command type */
	if (strncmp(ptr, "write", 5) == 0) {
		phy_read = 0;
		ptr += 5;
	}
	else if (strncmp(ptr, "read", 4) == 0) {
		phy_read = 1;
		ptr += 4;
	}
	else {
		printk("%s: Unknown command\n", __func__);
		return count;
	}

	/* remove space & retrieve phy */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	phy = simple_strtoul(ptr, &ptr, 10);

	/* remove space & retrieve register address */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	phy_reg = simple_strtoul(ptr, &ptr, 16);

	if (phy_read == 0) {
		/* remove space & retrieve register data */
		while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
		phy_data = simple_strtoul(ptr, &ptr, 16);
		switch_phy_reg_write(phy, phy_reg, phy_data, data);
	}
	else {
		phy_data = switch_phy_reg_read(phy, phy_reg, data);
	}

	return count;
}

static int read_phy_data(
		char *buf,
		char **start,
		off_t offset,
		int len,
		int *eof,
		void *data)
{
	char *p = buf;

	p += sprintf(p, "0x%08x\n", phy_data);
	*eof = 1;

	return p - buf;
}

static int write_phy_data(
		struct file *file,
		const char *buf,
		unsigned long count,
		void *data)
{
	return 0;
}

static int read_mii_cmd(
		char *buf,
		char **start,
		off_t offset,
		int len,
		int *eof,
		void *data)
{
	return 0;
}

static int write_mii_cmd(
		struct file *file,
		const char *buf,
		unsigned long count,
		void *data)
{
	char *ptr = (char *) buf;

	/* retrieve command type */
	if (strncmp(ptr, "write", 5) == 0) {
		mii_read = 0;
		ptr += 5;
	}
	else if (strncmp(ptr, "read", 4) == 0) {
		mii_read = 1;
		ptr += 4;
	}
	else {
		printk("%s: Unknown command\n", __func__);
		return count;
	}

	/* remove space & retrieve register address */
	while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
	mii_reg = simple_strtoul(ptr, &ptr, 16);

	if (mii_read == 0) {
		/* remove space & retrieve register data */
		while (*ptr && (*ptr == ' ' || *ptr == '\t')) ptr++;
		mii_data = simple_strtoul(ptr, &ptr, 16);
		switch_reg_write(mii_reg, mii_data, data);
	}
	else {
		mii_data = switch_reg_read(mii_reg, data);
	}

	return count;
}

static int read_mii_data(
		char *buf,
		char **start,
		off_t offset,
		int len,
		int *eof,
		void *data)
{
	char *p = buf;

	p += sprintf(p, "0x%08x\n", mii_data);
	*eof = 1;

	return p - buf;
}

static int write_mii_data(
		struct file *file,
		const char *buf,
		unsigned long count,
		void *data)
{
	return 0;
}

static int phy_proc_init(
		struct proc_dir_entry *root,
		mdio_ctrl *p_mdio_ctrl,
		unsigned int id)
{
	char entry_name[16];
	struct proc_dir_entry *entry = NULL;

	memset(entry_name, 0, 16);
	sprintf(entry_name, "ctrl%d", id);
	entry = create_proc_entry(entry_name, 0644, root);
	if (entry) {
		entry->read_proc = read_phy_cmd;
		entry->write_proc = write_phy_cmd;
		entry->data = p_mdio_ctrl;
	}
	else {
		printk("%s: Unable to create proc entry(cmd)\n", __func__);
		return -1;
	}

	memset(entry_name, 0, 16);
	sprintf(entry_name, "data%d", id);
	entry = create_proc_entry(entry_name, 0644, root);
	if (entry) {
		entry->read_proc = read_phy_data;
		entry->write_proc = write_phy_data;
	}
	else {
		printk("%s: Unable to create proc entry(data)\n", __func__);
		return -1;
	}

	return 0;
}

static int mii_proc_init(
		struct proc_dir_entry *root,
		mdio_ctrl *p_mdio_ctrl,
		unsigned int id)
{
	char entry_name[16];
	struct proc_dir_entry *entry = NULL;

	memset(entry_name, 0, 16);
	sprintf(entry_name, "ctrl%d", id);
	entry = create_proc_entry(entry_name, 0644, root);
	if (entry) {
		entry->read_proc = read_mii_cmd;
		entry->write_proc = write_mii_cmd;
		entry->data = p_mdio_ctrl;
	}
	else {
		printk("%s: Unable to create proc entry(cmd)\n", __func__);
		return -1;
	}

	memset(entry_name, 0, 16);
	sprintf(entry_name, "data%d", id);
	entry = create_proc_entry(entry_name, 0644, root);
	if (entry) {
		entry->read_proc = read_mii_data;
		entry->write_proc = write_mii_data;
	}
	else {
		printk("%s: Unable to create proc entry(data)\n", __func__);
		return -1;
	}

	return 0;
}

/***************************************************************************/
/* second switch */
#if CONFIG_SWITCH_NUM == 2
uint16_t mii2_read_reg(uint32_t phy, uint32_t reg);
void mii2_write_reg(uint32_t phy, uint32_t reg, int16_t data);

uint16_t mii2_read_reg(uint32_t phy, uint32_t reg)
{
	uint32_t res = 0;
	uint32_t t0 __attribute__ ((unused));
	uint32_t t1 __attribute__ ((unused));
	uint32_t t2 __attribute__ ((unused));
	uint32_t t3 __attribute__ ((unused));

	asm volatile (
	"   bset        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), #"D(MII_MDC2_PIN)"			\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdc]), "D(IO_GPIO_CTL)"(%[mdc]), #"D(MII_MDC2_PIN)"			\n\t"
	"   lsl.4       %[t0], %[phy], #"D(MII_REG_ADDR_LENGTH)"										\n\t"
	"   or.4        %[t0], %[t0], %[reg]															\n\t"
	"   lsl.4       %[t1], #"D(MII_OPCODE_READ)", #"D(MII_PHY_ADDR_LENGTH + MII_REG_ADDR_LENGTH)"	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   bfrvrs      %[t0], %[t0], #"D(32 - MII_HEADER_LENGTH)"										\n\t"
	"   lsl.4       %[t1], #1, #"D(MII_MDC2_PIN)"													\n\t"

	/* Toggle MDC to output preamble. */
	"   move.4      %[t3], #"D(MII_PREAMBLE_LENGTH * 2)"											\n\t"

	"1: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6)"													\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   1b																				\n\t"

	/* Output start, read opcode, phy address and register address. */
	"   move.4      %[t3], #"D(MII_HEADER_LENGTH)"													\n\t"

	"2: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO2_PIN)"							\n\t"
	"   xor.4       %[t2], %[t2], %[t0]																\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   lsl.4       %[t2], %[t2], #"D(MII_MDIO2_PIN)"												\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), %[t2]						\n\t"
	"   cycles      "D(MII_DATA_SETUP_TIME)"														\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6 - MII_DATA_SETUP_TIME)"								\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 7)"													\n\t"
	"   lsr.4       %[t0], %[t0], #1																\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   2b																				\n\t"

	/* Capture TA + data. */
	"   move.4      %[t3], #"D(MII_REG_DATA_LENGTH)"												\n\t"

	"3: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   bclr        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   bclr        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 5)"													\n\t"
	"   lsl.4       %[res], %[res], #1																\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_IN)"(%[mdio]), #"D(MII_MDIO2_PIN)"							\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 8)"													\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   or.4        %[res], %[res], %[t2]															\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   3b																				\n\t"

	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
		: [res] "=d" (res), [t0] "=&d" (t0), [t1] "=d" (t1), [t2] "=d" (t2), [t3] "=d" (t3)
		: [mdio] "a" (MII_MDIO2_PORT), [mdc] "a" (MII_MDC2_PORT), [phy] "d" (phy), [reg] "d" (reg)
		: "cc", "memory"
	);

	return res;
}

void mii2_write_reg(uint32_t phy, uint32_t reg, int16_t data)
{
	uint32_t t0 __attribute__ ((unused));
	uint32_t t1 __attribute__ ((unused));
	uint32_t t2 __attribute__ ((unused));
	uint32_t t3 __attribute__ ((unused));

	asm volatile (
	"   bset        "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), #"D(MII_MDC2_PIN)"			\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdio]), "D(IO_GPIO_CTL)"(%[mdio]), #"D(MII_MDIO2_PIN)"		\n\t"
	"   bset        "D(IO_GPIO_CTL)"(%[mdc]), "D(IO_GPIO_CTL)"(%[mdc]), #"D(MII_MDC2_PIN)"			\n\t"
	"   lsl.4       %[t0], %[phy], #"D(MII_REG_ADDR_LENGTH)"										\n\t"
	"   or.4        %[t0], %[t0], %[reg]															\n\t"
	"   lsl.4       %[t1], #"D(MII_OPCODE_WRITE)", #"D(MII_PHY_ADDR_LENGTH + MII_REG_ADDR_LENGTH)"	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   lsl.4       %[t0], %[t0], #"D(MII_REG_DATA_LENGTH)"											\n\t"
	"   bset        %[t0], %[t0], #"D(MII_REG_DATA_LENGTH - 1)"										\n\t"
	"   move.2      %[t1], %[data]																	\n\t"
	"   or.4        %[t0], %[t0], %[t1]																\n\t"
	"   bfrvrs      %[t0], %[t0], #"D(32 - MII_HEADER_LENGTH - MII_REG_DATA_LENGTH)"				\n\t"
	"   lsl.4       %[t1], #1, #"D(MII_MDC2_PIN)"													\n\t"

	/* Toggle MDC to output preamble. */
	"   move.4      %[t3], #"D(MII_PREAMBLE_LENGTH * 2)"											\n\t"

	"1: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6)"													\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   1b																				\n\t"

	/* Output start, write opcode, phy address, register address, TA and data. */
	"   move.4      %[t3], #"D(MII_HEADER_LENGTH + MII_REG_DATA_LENGTH)"							\n\t"

	"2: xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   lsr.4       %[t2], "D(IO_GPIO_OUT)"(%[mdio]), #"D(MII_MDIO2_PIN)"							\n\t"
	"   xor.4       %[t2], %[t2], %[t0]																\n\t"
	"   and.4       %[t2], #1, %[t2]																\n\t"
	"   lsl.4       %[t2], %[t2], #"D(MII_MDIO2_PIN)"												\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdio]), "D(IO_GPIO_OUT)"(%[mdio]), %[t2]						\n\t"
	"   cycles      "D(MII_DATA_SETUP_TIME)"														\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 6 - MII_DATA_SETUP_TIME)"								\n\t"
	"   xor.4       "D(IO_GPIO_OUT)"(%[mdc]), "D(IO_GPIO_OUT)"(%[mdc]), %[t1]						\n\t"
	"   cycles      "D(MII_DUTY_CYCLE_TIME - 7)"													\n\t"
	"   lsr.4       %[t0], %[t0], #1																\n\t"
	"   add.4       %[t3], #-1, %[t3]																\n\t"
	"   jmpgt.w.t   2b																				\n\t"
		: [t0] "=&d" (t0), [t1] "=&d" (t1), [t2] "=d" (t2), [t3] "=d" (t3)
		: [mdio] "a" (MII_MDIO2_PORT), [mdc] "a" (MII_MDC2_PORT),
			[phy] "d" (phy), [reg] "d" (reg), [data] "d" (data)
		: "cc", "memory"
	);
}
#endif

/***************************************************************************/

mdio_ctrl *mdio_ctrl_init(unsigned int id)
{
	mdio_ctrl *p_mdio_ctrl = NULL;

	p_mdio_ctrl = kzalloc(sizeof(mdio_ctrl), GFP_KERNEL);
	if (p_mdio_ctrl == NULL) {
		printk("%s: no memory...\n", __func__);
		return NULL;
	}

	if (id == 1) {
		p_mdio_ctrl->mii_read = &mii1_read_reg;
		p_mdio_ctrl->mii_write = &mii1_write_reg;
	}
	#if CONFIG_SWITCH_NUM == 2
	else if (id == 2) {
		p_mdio_ctrl->mii_read = &mii2_read_reg;
		p_mdio_ctrl->mii_write = &mii2_write_reg;
	}
	#endif
	else {
		printk("%s: un-available id(%d)\n", __func__, id);
		kfree(p_mdio_ctrl);
		return NULL;
	}

	return p_mdio_ctrl;
}

static void ubi32_access_exit(void)
{
	printk("%s exit!\n", DRV_MODULE_NAME);
	if (g_system_reset)
	{
		remove_proc_entry("system_reset", NULL);
		g_system_reset = NULL;
	}
}

static int __init ubi32_access_init(void)
{
	int ret, i;
	mdio_ctrl *p_mdio_ctrl = NULL;

	printk("%s init!\n", DRV_MODULE_NAME);

	do {
		/* Create system reset */
		g_system_reset = create_proc_entry("system_reset", 0644, NULL);
		if (g_system_reset) {
			g_system_reset->data = 0;
			g_system_reset->read_proc = read_system_reset;
			g_system_reset->write_proc = write_system_reset;
		}
		else {
			printk("%s: Unable to create system_reset entry!!\n", __func__);
			ret = -ENOMEM;
			break;
		}

		/* Create proc entries for accessing switch registers */
		g_proc_ar8327 = proc_mkdir("ar8327", NULL);
		if (g_proc_ar8327 == NULL) {
			printk("%s: Unable to create proc folder(ar8327)\n", __func__);
			ret = -ENOMEM;
			break;
		}

		g_proc_mii = proc_mkdir("mii", g_proc_ar8327);
		if (g_proc_mii == NULL) {
			printk("%s: Unable to create proc folder(ar8327/mii)\n", __func__);
			ret = -ENOMEM;
			break;
		}

		g_proc_phy = proc_mkdir("phy", g_proc_ar8327);
		if (g_proc_phy == NULL) {
			printk("%s: Unable to create proc folder(ar8327/phy)\n", __func__);
			ret = -ENOMEM;
			break;
		}

		for (i = 1; i <= CONFIG_SWITCH_NUM; i++) {
			p_mdio_ctrl = mdio_ctrl_init(i);
			ret = mii_proc_init(g_proc_mii, p_mdio_ctrl, i);
			ret = phy_proc_init(g_proc_phy, p_mdio_ctrl, i);
		}
	} while (0);

	if (ret != 0) ubi32_access_exit();

	return ret;
}

module_init(ubi32_access_init);
module_exit(ubi32_access_exit);
