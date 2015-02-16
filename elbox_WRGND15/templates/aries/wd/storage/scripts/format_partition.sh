#!/bin/sh 
CONSOLE=/dev/console;
STATUS=/var/partition_status;
echo "[$0] $1" > $CONSOLE;
disk=/dev/$1;

# input: index partition FORMAT
#        1     /dev/sda1 ext3
forpart () {
	blksize=4096;
	i=`echo $idx | grep $1`;
	case $3 in
		ext3) cmd="mkfs.ext3 -c -c -b $blksize"; ;;
		swap) cmd="mkswap"; ;;
		ntfs) cmd=" echo -e 'y\n' | mkntfs -b:$blksize"; ;;
		*) cmd="mkfs.ext3 -b $blksize"; ;;
	esac
	if [ "$i" != "" ]; then 
		echo "Format $2 as $3" > $CONSOLE;
		
		if [ "$3" = "ntfs" ]; then 
			chkntfs -f $2 > $CONSOLE;
		fi
		
		eval "$cmd $2" > $CONSOLE;
		nid=`echo $i | sed "s/$1//"`;
		echo "$act $nid" > $STATUS;
		cat $STATUS > $CONSOLE;
	else
		echo "$2 Formated" > $CONSOLE;
	fi
}
act=`cat $STATUS | cut -d' ' -f1`;
idx=`cat $STATUS | cut -d' ' -f2`;
if [ -f $STATUS -a "$act" = "FORMAT" ]; then
	case $1 in
		sd[a-z]1) forpart 1 $disk ext3; ;;
		sd[a-z]2) forpart 2 $disk ext3; ;;
		sd[a-z]3) forpart 3 $disk ext3; ;;
		sd[a-z]5) forpart 5 $disk swap; ;;
		sd[a-z]6) forpart 6 $disk ntfs; ;;
		*) echo "Unformated partition($1)" > $CONSOLE;	;;
	esac
else
	echo "Normal partition($disk)." > $CONSOLE;
fi

