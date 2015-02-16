#!/bin/sh
# If you have samba, ftp to do this on linux file system access.
# We need to adjust the vm to avoid increasing cache memory to much cause out of memory

echo 10 > /proc/sys/vm/dirty_ratio
echo 1 > /proc/sys/vm/dirty_background_ratio
echo 16000 > /proc/sys/vm/min_free_kbytes
echo 300 > /proc/sys/vm/dirty_expire_centisecs
echo 100 > /proc/sys/vm/dirty_writeback_centisecs

