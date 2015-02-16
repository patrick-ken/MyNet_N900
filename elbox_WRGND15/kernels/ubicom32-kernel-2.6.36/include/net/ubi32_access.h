#ifndef _NET_UBI32_ACCESS_H
#define _NET_UBI32_ACCESS_H

typedef uint16_t (*mii_read_func)(uint32_t phy, uint32_t reg);
typedef void (*mii_write_func)(uint32_t phy, uint32_t reg, int16_t data);

typedef struct ctrlblock{
	mii_read_func mii_read;
	mii_write_func mii_write;
} mdio_ctrl;

mdio_ctrl *mdio_ctrl_init(unsigned int id);

u32_t switch_reg_read(u32_t addr, mdio_ctrl *p_mdio_ctrl);
void switch_reg_write(u32_t addr, u32_t data, mdio_ctrl *p_mdio_ctrl);


#endif
