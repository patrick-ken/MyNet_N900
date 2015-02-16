=========================
0. Intriduction

   This file will show you how to build the GPL linux system.
   
Step 1.	Install fedora linux 10 (choose Software Development) on 32bit CPU.

Step 2.	Setup Build Enviornment($means command)

	1) please login as a normal user such as john,and copy the gpl file to normal user folder,
	
	such as the folder /home/john
	
	2) $cd /home/john
	
	3) $tar zxvf My_Net_N900_1.07.16_d5ec.bin.tar.gz
	
	4) $cd elbox_WRGND15
	
	5) #cp -rf ubicom32_sdk32x_gcc-4.4.1_uclibc-0.9.30.1_v02 /opt	(ps : switch to root permission)

	6) #rpm -ivh ./build_gpl/fakeroot-1.9.7-18.fc10.i386.rpm
		  
	7) $source ./setupenv	(ps : switch back to normal user permission)
	
Step 3. Building the image
	1) $make
	2) $make
	3) $make
     	===================================================
	 You are going to build the f/w images.
	 Both the release and tftp images will be generated.
	 ===================================================
	 Do you want to rebuild the linux kernel ? (yes/no) : yes
	 
      4) there are some options need to be selected , please input "enter" key to execute the default action. 
	 
      5) After make successfully, you will find the image file in ./images/.
 
