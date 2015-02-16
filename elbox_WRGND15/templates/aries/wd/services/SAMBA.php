<?
include "/htdocs/phplib/inf.php";

$SAMBAP		= "/var/etc/samba";
$SAMBACFG	= $SAMBAP."/smb.conf";
$smb_passwd_file= $SAMBAP."/smbpasswdfile";
$smb_tmp	= $SAMBAP."/smbpasswd";
$sharepath = "/var/tmp/smb_share/";
$partition_count = query("/runtime/device/storage/disk/count");
$usb1=query("/wd/storage/samba/USB1");
$usb2=query("/wd/storage/samba/USB2");
$anonymous_account = query("/wd/storage/public_share");
$HostName       = query("/device/hostname");
$WorkGroup  = query("/wd/storage/workgroup");
$UserName = query("/wd/storage/username");
$UserPassword   = query("/wd/storage/password");
$active = "0";
$Master  = query("/wd/storage/master");

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

if( $usb1=="1" || $usb2=="1" )
{
	$active = "1";
	fwrite("a", $START, "mkdir -p ".$sharepath."\n");
	if($usb1=="1")
	{
		foreach ("/runtime/device/storage/disk")
		{
			if("usb1"==query("usbport"))
			{
				foreach ("entry")
				{
					//TRACE_debug("USB1 mntp=".query("mntp"));
					$mntpath = query("mntp");
					$mntfolder = scut($mntpath, 0, "/var/tmp/storage/");

					if($mntfolder != "")
					{
						if(query("state")=="MOUNTED")
						{
							//TRACE_debug("ln -s ".$mntpath." ".$sharepath."/".$mntfolder." \n");
							fwrite("a",$START, "rm -f ".$sharepath."/".$mntfolder." 2>/dev/null\n");
							fwrite("a",$START, "ln -s -n ".$mntpath." ".$sharepath."/".$mntfolder." \n");
						}
					}
				}
			}
		}
	}

	if($usb2=="1")
	{
		foreach ("/runtime/device/storage/disk")
		{
			if("usb2"==query("usbport"))
			{
				foreach ("entry")
				{
					//TRACE_debug("USB2 mntp=".query("mntp"));
					$mntpath = query("mntp");
					$mntfolder = scut($mntpath, 0, "/var/tmp/storage/");
					
					if($mntfolder != "")
					{
						if(query("state")=="MOUNTED")
						{
							//TRACE_debug("ln -s ".$mntpath." ".$sharepath."/".$mntfolder." \n");
							fwrite("a",$START, "rm -f ".$sharepath."/".$mntfolder." 2>/dev/null\n");
							fwrite("a",$START, "ln -s -n ".$mntpath." ".$sharepath."/".$mntfolder." \n");
						}
					}
				}
			}
		}
	}		
}
else
{
	$active="0";
}

if ($active!="1")
{
	fwrite("a", $START, "echo \"SAMBA: server is disabled !\" > /dev/console\n");
}
else
{
	fwrite("a",$START, "if [ ! -d ".$SAMBAP." ]; then mkdir -p ".$SAMBAP."; fi\n");
	if($anonymous_account == 1)
	{	
		fwrite("a",$START, "if [ ! -f ".$SAMBAP."/smbpasswd ]; then adduser nobody; smbpasswd -a nobody -n; fi\n");
	}
	else
	{
		fwrite("a",$START, "if [ ! -f ".$SAMBAP."/smbpasswd ]; then ( echo \"".$UserPassword."\"; echo \"".$UserPassword."\" ) | smbpasswd -s -a ".$UserName."; fi\n");	
	}	
	fwrite("a",$START, "smbd -D\n");
	fwrite("a",$START, "nmbd -D\n");
	fwrite("a",$STOP,  "killall nmbd\n");
	fwrite("a",$STOP,  "killall smbd\n");
	fwrite("a",$STOP,  "rm -rf ".$SAMBAP."\n");

	fwrite("w",$SAMBACFG, "[global]\n");
	fwrite("a",$SAMBACFG, "\tworkgroup = ".$WorkGroup."\n");
	fwrite("a",$SAMBACFG, "\tserver string = ".query("/runtime/device/modelname")." Samba Server\n");
	fwrite("a",$SAMBACFG, "\tnetbios name = ".$HostName."\n");
	//fwrite("a",$SAMBACFG, "\tkernel change notify = no\n");
	fwrite("a",$SAMBACFG, "\twinbind nested groups = no\n");
	fwrite("a",$SAMBACFG, "\tdomain master = no\n");
	if ( $Master !=0 )
	{
	fwrite("a",$SAMBACFG, "\tlocal master = yes\n");
	}
	else
	{
	fwrite("a",$SAMBACFG, "\tlocal master = no\n");
	}
	//fwrite("a",$SAMBACFG, "\tpublic = yes\n");
	/* If you want the samba to work only in the lan side, you also need to set the "bind interfaces only = yes" */
	//fwrite("a",$SAMBACFG, "\tinterfaces = ".$lan_ip."/24 br0\n");
	//fwrite("a",$SAMBACFG, "\tbind interfaces only = yes\n");
	/* Ensure "#define HAVE_IFACE_IFCONF 1" in the config.h of the samba */
	fwrite("a",$SAMBACFG, "\tinterfaces = ".INF_getcfgipaddr("LAN-1")."/".INF_getcfgmask("LAN-1")." br0\n");
	fwrite("a",$SAMBACFG, "\tbind interfaces only = yes\n");
	//fwrite("a",$SAMBACFG, "\tload printers = no\n");
	//fwrite("a",$SAMBACFG, "\tprinting = bsd\n");
	//fwrite("a",$SAMBACFG, "\tprintcap name = /dev/null\n");
	if($anonymous_account == 1)
	{
		fwrite("a",$SAMBACFG, "\tsecurity = share\n");
	}
	else
	{	
		fwrite("a",$SAMBACFG, "\tsecurity = user\n");
		fwrite("a",$SAMBACFG, "\tnull passwords = yes\n");
		fwrite("a",$SAMBACFG, "\tencrypt passwords = yes\n");
	}	
	fwrite("a",$SAMBACFG, "\tsocket options = IPTOS_LOWDELAY TCP_NODELAY SO_KEEPALIVE SO_RCVBUF=65536 SO_SNDBUF=65536\n");
	fwrite("a",$SAMBACFG, "\tsmb ports = 445 139 \n");
	fwrite("a",$SAMBACFG, "\tunix charset = UTF8\n");
	fwrite("a",$SAMBACFG, "\tdisplay charset = UTF8\n");
	fwrite("a",$SAMBACFG, "\tdos charset = UTF8\n");
	fwrite("a",$SAMBACFG, "\tdns proxy = no\n");
	fwrite("a",$SAMBACFG, "\twriteable = yes\n");
	fwrite("a",$SAMBACFG, "\tpublic = yes\n");
	fwrite("a",$SAMBACFG, "\toplocks = no\n");
	fwrite("a",$SAMBACFG, "\tcreate mask = 0777\n");
	fwrite("a",$SAMBACFG, "\tdirectory mask = 0777\n");
	/* For compatibility with Windows Vista */
	fwrite("a",$SAMBACFG, "\tclient lanman auth = no\n");
	fwrite("a",$SAMBACFG, "\tclient ntlmv2 auth = yes\n");
	fwrite("a",$SAMBACFG, "\tdomain logons = yes\n\n");

	foreach("/runtime/device/storage/disk")
	{
		$disk_n=$InDeX;
		foreach("entry")
		{
			$mntpath = query("/runtime/device/storage/disk:".$disk_n."/entry:".$InDeX."/mntp");
			$mntname = cut($mntpath, 4, "/");
			if($mntname != "")
			{
				fwrite("a",$SAMBACFG, "[".$mntname."]\n");
				fwrite("a",$SAMBACFG, "\tcomment = MyNetShare\n");
				fwrite("a",$SAMBACFG, "\tpath = ".$mntpath."\n");
				if($anonymous_account != 1)
				{		
					fwrite("a",$SAMBACFG, "\tvalid users = ".$UserName."\n");
				}	
			}
		}
	}
}

if ($active!="1")
{
	fwrite("a", $STOP, "echo \"SAMBA: server is disabled !\" > /dev/console\n");
}
else
{
	fwrite("a",$STOP, "rm -rf ".$sharepath."\n");
}
?>
