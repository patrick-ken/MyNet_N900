#!/bin/bash
#
# get the end address of the image (ELF) file and align it to a sector boundary.
#

NAME=`basename $0`

if [ "$1" = "" ] || [ "$2" = "" ]; then
	echo "$NAME ERROR: Missing or Bad Input" 1>&2
	echo "
USAGE: $NAME <elf-file> <sector-size in KBytes>
  uses ${NM} to get the end address of the ELF image.
  Finds and returns the next sector boundary based on the
  sector size.
  This is used to make sure we waste minimal space (at worst 
  a fraction of a sector), and ultra’s image section does 
  not overlap the vmlinux.elf image section.

  Example:
  $NAME projects/ultra/ultra.elf 256
" 1>&2
	exit $ERROR;
fi

ULTRA_FLASH_END_HEX=`${NM} $1 | grep __image_end_addr | sed 's/ .*//'`
ULTRA_FLASH_END_DEC=$((0x$ULTRA_FLASH_END_HEX))
if [ $ULTRA_FLASH_END_DEC = 0 ]; then
	IMAGE_SECTION_BEGIN_HEX=`${OBJDUMP} -h $1 | awk '/.image/ {printf "%s", $5}'`
	IMAGE_SECTION_SIZE_HEX=`${OBJDUMP} -h $1 | awk '/.image/ {printf "%s", $3}'`
	IMAGE_SECTION_BEGIN_DEC=$((0x$IMAGE_SECTION_BEGIN_HEX))
	IMAGE_SECTION_SIZE_DEC=$((0x$IMAGE_SECTION_SIZE_HEX))
	ULTRA_FLASH_END_DEC=$(($IMAGE_SECTION_BEGIN_DEC + $IMAGE_SECTION_SIZE_DEC))
fi
SECTOR_SIZE=$(($2*1024))
PARAM1=$(($ULTRA_FLASH_END_DEC + $SECTOR_SIZE - 1))
MASK=$(($SECTOR_SIZE-1))
NOTMASK=$((~$MASK))
ULTRA_FLASH_END_ALIGNED=$(($NOTMASK & $PARAM1))
echo $ULTRA_FLASH_END_ALIGNED
