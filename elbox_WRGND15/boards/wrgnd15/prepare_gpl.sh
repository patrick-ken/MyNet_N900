#!/bin/bash
#This shell script will help you to modify some files and remove some files
#automatically , which "./build_gpl/build_gpl.sh" doesn't support.
#You can get more information from "How_to_Creat_and_Build_GPL_Code_on_Seattle.doc"

# modify boards\wrgnd14\ubicom\Makefile
sed -i 's/ultra check_ocm_size_h//g' ./ubicom/Makefile

# modify boards\wrgnd14\template.aries\config.mk
sed -i 's/$(Q)make -C progs.board\/ubicom -f sources.mk//g' ./config.mk
sed -i 's/$(Q)make -C progs.board\/ubicom ultra V=$(V) DEBUG=$(DEBUG)//g' ./config.mk

# remove the unnecessary file and private file
rm -rf ubicom/ubicom-private
rm -rf ubicom/sources
rm -rf ubicom/build/u-boot

pushd ubicom/bin
find -name "rootfs.img" | xargs rm -f
find -name "upgrade*" | xargs rm -f
find -name "vmlinux*" | xargs rm -f
find -name "bootexec*" | xargs rm -f   # it exists if you make uboot
find -name "u-boot*" | xargs rm -f     # it exists if you make uboot
popd

