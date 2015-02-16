
# Section: Global Settings

# Enable Global Debug
DEBUG = 1

# Enable all Assertions

# Project Name
PROJECT_NAME = ultra

# Section: Internal Settings

# Build Directory Path
BUILD_DIR = $(PROJECT_DIR)/build

# SDK directory
SDK_DIR = /home/jeremy_lin/project/GPL/0514_pro/elbox_WRGND15/boards/wrgnd15/ubicom/ubicom-private/ultra

# Compiler Flags
GLOBAL_CFLAGS = -O2 -g -fgnu89-inline -fleading-underscore

# Architecture Directory
ARCH_DIR = ip3k

# Architecture
ARCH = IP3K

# Package: Application
APPLICATION = 1

# Firmware Identity String
APP_IDENTITY_STRING = WRG-ND15

# Reserved sectors in flash for the bootloader
APP_BOOTLOADER_RESERVED_SPACE_IN_SECTORS = 8

# Reserved kilobytes for SNV in bootloader sectors
APP_SNV_RESERVED_KB = 1

# Reserved sectors in flash for the kernel
APP_KERNEL_RESERVED_SPACE_IN_SECTORS = 0

# Section: uClinux

# Kernel command line parameter extension for root file systems other than initramfs
APP_BOOTARGS_EXTRA = root=/dev/mtdblock2 rootfstype=squashfs,jffs2 init=/init

# Kernel command line
APP_BOOTARGS = mtdparts=ubicom32fc.0:512k(bootloader)ro,14080k(upgrade),1536k(rootfs_data),256k(fw_env)

# uClinux memory start address offset
APP_UCLINUX_MEM_START_ADDR = 0x00100000

# Board Name
AP_BOARD_NAME = IP8K_WRGND15_BOARD

# Board Name
IP8K_WRGND15_BOARD = 1

# Build Bootexec
BOOTEXEC_ULTRA = 1

# Enable U-Boot
APP_UBOOT_ENABLE = 1

# U-Boot Path
APP_UBOOT_DIR = /home/jeremy_lin/project/GPL/0514_pro/elbox_WRGND15/boards/wrgnd15/ubicom/ubicom-private/../build/u-boot

# U-Boot RAM image size
APP_UBOOT_MEM_SIZE = 0x100000

# Environment variables space size in sectors
APP_UBOOT_ENV_SIZE_IN_SECTORS = 1

# Build Main Exec

# Package: ipBootDecompressor - Bootstrap decompressor.
IPBOOTDECOMPRESSOR = 1

# Package directory name
IPBOOTDECOMPRESSOR_PKG_DIR = ipBootDecompressor

# Package sub-directories
PKG_SUBDIRS += $(IPBOOTDECOMPRESSOR_PKG_DIR)

# Package: ipDebug - Runtime Debug Support
IPDEBUG = 1

# Package directory name
IPDEBUG_PKG_DIR = ipDebug

# Package sub-directories
PKG_SUBDIRS += $(IPDEBUG_PKG_DIR)

# Package: ipDevTree - Device Tree
IPDEVTREE = 1

# Package directory name
IPDEVTREE_PKG_DIR = ipDevTree

# Package sub-directories
PKG_SUBDIRS += $(IPDEVTREE_PKG_DIR)

# Package: ipDSR - Device Service Routine Support
IPDSR = 1

# Package directory name
IPDSR_PKG_DIR = ipDSR

# Package sub-directories
PKG_SUBDIRS += $(IPDSR_PKG_DIR)

# Package: ipEthernetDMA - On-Chip Ethernet Driver VP support
IPETHERNETDMA = 1

# Package directory name
IPETHERNETDMA_PKG_DIR = ipEthernetDMA

# Package sub-directories
PKG_SUBDIRS += $(IPETHERNETDMA_PKG_DIR)

# Multiple Instances
IPETHERNET_MI_ENABLED = 1
IPETHERNET_MI_ENABLED_INSTANCES += eth_lan_
IPETHERNET_MI_ENABLED_INSTANCES += eth_wan_

# Section: eth_lan_Link mode options

# Section: eth_wan_Link mode options

# Package: ipEthernetHeader - Ethernet Packet Header Library
IPETHERNETHEADER = 1

# Package directory name
IPETHERNETHEADER_PKG_DIR = ipEthernetHeader

# Package sub-directories
PKG_SUBDIRS += $(IPETHERNETHEADER_PKG_DIR)

# Package: ipEthSwitchDev - Driver for external Ethernet Switch Device
IPETHSWITCHDEV = 1

# Package directory name
IPETHSWITCHDEV_PKG_DIR = ipEthSwitchDev

# Package sub-directories
PKG_SUBDIRS += $(IPETHSWITCHDEV_PKG_DIR)

# Section: Switch Vendor Selection

# VITESSE VSC7385 - Gigabit

# Marvell Switch 88E6061

# RealTek Switch RTL8305

# RealTek Switch RTL8306

# RealTek RTL8366 - Gigabit

# Broadcom BCM5385 - Gigabit

# Broadcom BCM5395 - Gigabit

# Atheros AR8316 - Gigabit

# Atheros AR8327 - Gigabit
ETHSWITCH_ATHEROS_AR8327 = 1

# None

# Package: ipGDB - Debugger support
IPGDB = 1

# Package directory name
IPGDB_PKG_DIR = ipGDB

# Package sub-directories
PKG_SUBDIRS += $(IPGDB_PKG_DIR)

# Package: ipHAL - Hardware Abstraction Layer
IPHAL = 1

# Package directory name
IPHAL_PKG_DIR = ipHAL

# Package sub-directories
PKG_SUBDIRS += $(IPHAL_PKG_DIR)

# Architecture Extension
IP8000_PROD = 1

# Section: Timing

# External Flash
USE_EXTFLASH = 1

# Max page size (k)
EXTFLASH_MAX_PAGE_SIZE_KB = 64

# External DRAM
USE_EXTMEM = 1

# External SPI-ER NAND Flash
SPI_ER_NAND = 1

# Enable Gate Port
SPI_ER_NAND_PORT = PG5

# Enable Gate Pin
SPI_ER_NAND_PIN = 2

# Watchdog Enable

# Stack Checking

# Package: ipHeap - Heap Memory Management Features
IPHEAP = 1

# Package directory name
IPHEAP_PKG_DIR = ipHeap

# Package sub-directories
PKG_SUBDIRS += $(IPHEAP_PKG_DIR)

# Runtime Debugging

# Package: ipInterrupt - Hardware Interrupt Infrastructure
IPINTERRUPT = 1

# Package directory name
IPINTERRUPT_PKG_DIR = ipInterrupt

# Package sub-directories
PKG_SUBDIRS += $(IPINTERRUPT_PKG_DIR)

# Package: ipLibC - Standard C library
IPLIBC = 1

# Package directory name
IPLIBC_PKG_DIR = ipLibC

# Package sub-directories
PKG_SUBDIRS += $(IPLIBC_PKG_DIR)

# Package: ipLock - Lock Implementation for multi-threading.
IPLOCK = 1

# Package directory name
IPLOCK_PKG_DIR = ipLock

# Package sub-directories
PKG_SUBDIRS += $(IPLOCK_PKG_DIR)

# Package: ipMACAddr - MAC Address Support Library
IPMACADDR = 1

# Package directory name
IPMACADDR_PKG_DIR = ipMACAddr

# Package sub-directories
PKG_SUBDIRS += $(IPMACADDR_PKG_DIR)

# Package: ipMII - MII Phy Control
IPMII = 1

# Package directory name
IPMII_PKG_DIR = ipMII

# Package sub-directories
PKG_SUBDIRS += $(IPMII_PKG_DIR)

# Runtime Debugging

# Package: ipOneshot - Oneshot Timers
IPONESHOT = 1

# Package directory name
IPONESHOT_PKG_DIR = ipOneshot

# Package sub-directories
PKG_SUBDIRS += $(IPONESHOT_PKG_DIR)

# Package: ipPCIE - PCI Express driver package
IPPCIE = 1

# Package directory name
IPPCIE_PKG_DIR = ipPCIE

# Package sub-directories
PKG_SUBDIRS += $(IPPCIE_PKG_DIR)

# Section: PCI Devices

# Package: ipThread - Hardware Multi-Threading Infrastructure
IPTHREAD = 1

# Package directory name
IPTHREAD_PKG_DIR = ipThread

# Package sub-directories
PKG_SUBDIRS += $(IPTHREAD_PKG_DIR)

# Package: ipTimer - System Tick Timer
IPTIMER = 1

# Package directory name
IPTIMER_PKG_DIR = ipTimer

# Package sub-directories
PKG_SUBDIRS += $(IPTIMER_PKG_DIR)
