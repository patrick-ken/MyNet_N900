=========================
Introduction & Initial Setup
=========================

This file will show you how to build the GPL linux system.
- Install fedora linux 12 (choose Software Development) on 32bit CPU.
- Login as a normal user (have the root password handy).
- Install fakeroot-libs-1.12.2 and fakeroot-1.12.2 from the Packages directory in the ISO.

Upgrade the version git from 1.65 to the latest one.
Use this guide http://git-scm.com/book/en/v2/Getting-Started-Installing-Git
- Download the latest git source tarball
- Extract and use the following commands to upgrade git:
		$ make configure
        $ ./configure --prefix=/usr
        $ make all
        $ su
        $ make install
        $ exit

- Create and change into directory where you'll put the source code (~/Development works fine)
- Clone the source repository from GitHub:
		$git clone https://github.com/PeterFalken/MyNet_N900.git

- Copy the Ubicom32 toolbox into /opt directory
		$ cd MyNet_N900/elbox_WRGND15
		$ su
		$ cp -rf ubicom32_sdk32x_gcc-4.4.1_uclibc-0.9.30.1_v02 /opt
		$ exit


=======================================
Compiling the source code
=======================================
- Login as a normal user.
- Navigate to your source code directory.
	$ cd MyNet_N900/elbox_WRGND15

- Load the necessary environment variables.
	$ source ./setupenv

- Set the config file
	$ make

- Setup the environment for your particular model
	$ make

- Modify the source code to your liking
- Building the image
	$ make

===================================================
	You are going to build the f/w images.
	Both the release and tftp images will be generated.
===================================================
	Do you want to rebuild the linux kernel ? (yes/no) : yes

    There are some options need to be selected , please input "enter" key to execute the default action.
	After make finishes successfully, you will find the image file in ./images/.
