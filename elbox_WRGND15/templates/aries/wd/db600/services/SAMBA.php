<?
include "/htdocs/phplib/inf.php";

$SAMBACFG = "/var/smb.conf";
$sharepath = "/var/tmp/smb_share/";
$partition_count = query("/runtime/device/storage/disk/count");
$usb1=query("/wd/storage/samba/USB1");
$usb2=query("/wd/storage/samba/USB2");
$anonymous_account = query("/wd/storage/public_share");
$active = "0";

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

	/*for kcode samba...*/
	//fwrite("a",$START, "/etc/scripts/killpid.sh /var/run/samba.pid\n");
	//fwrite("a",$START, "KC_SMB &\n");
	//fwrite("a",$START, "echo $! >  /var/run/samba.pid\n");
	fwrite("a",$START, "/etc/services/SAMBA_loop.sh &\n");
	fwrite("a",$STOP,  "ps | grep SAMBA_loop.sh | awk '{print $1}' | xargs kill -SIGTERM\n");
	fwrite("a",$STOP,  "killall KC_SMB\n");

	/*kcode config link /etc/smbConfig ===> /var/smb.conf*/
//	$ModelName	= query("/runtime/device/modelname");
	$HostName	= query("/device/hostname");
	$WorkGroup  = query("/wd/storage/workgroup");
	//$WorkGroup  = "\"".$WorkGroup."\"";
	$UserName = query("/wd/storage/username");
	//$UserName = "\"".$UserName."\"";
	$UserPassword	= query("/wd/storage/password");
	$Master  = query("/wd/storage/master");
	if ( $Master != 0 )	{ $Master = 1;	}
	
	fwrite("w",$SAMBACFG, "HostName=\"".$HostName."\"\n");
	//fwrite("w",$SAMBACFG, "HostName=\"".$ModelName."\"\n");
	fwrite("a",$SAMBACFG, "GroupName=\"".$WorkGroup."\"\n");
	fwrite("a",$SAMBACFG, "Comment=\"MyNetShare\"\n");
	if ($anonymous_account == 1)
	{
		fwrite("a",$SAMBACFG, "AuthLevel=\"3\"\n");
	} else {
		fwrite("a",$SAMBACFG, "AuthLevel=\"2\"\n");
	}
	fwrite("a",$SAMBACFG, "Master=\"".$Master."\"\n");
	fwrite("a",$SAMBACFG, "MountRoot=\"".$sharepath."\"\n");
	fwrite("a",$SAMBACFG, "UserName=\"".$UserName."\"\n");
	fwrite("a",$SAMBACFG, "UserPassword=\"".$UserPassword."\"\n");
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
