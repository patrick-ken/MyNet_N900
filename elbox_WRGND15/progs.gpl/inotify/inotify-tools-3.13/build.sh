PREFIX=${PWD}/prefix
./configure --build=i386-linux --host=${TARGET_HOST} --exec_prefix=${PREFIX} --prefix=${PREFIX} LIBS=-lxmldbc
make clean
make
make install

${CROSS_COMPILE}strip ${PREFIX}/bin/inotifywait
${CROSS_COMPILE}strip ${PREFIX}/lib/libinotifytools.so.0.4.1

cp -vf ${PREFIX}/bin/inotifywait ${ROOT_FS}/usr/bin/inotify_uPNP
cp -vaf ${PREFIX}/lib/libinotifytools.so.0.4.1 ${ROOT_FS}/usr/lib/
