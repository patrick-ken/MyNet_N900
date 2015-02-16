<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";

$inf_uid = "LAN-1";
$max_user="10";
$idle_time="5";
$port="21";
$language="UTF-8";
$sharepath="/tmp/ftp_share";
$ftp_tbl ="/etc/ftp_tbl";
$pwdf="/var/passwds";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$partition_count = query("/runtime/device/storage/disk/count");
$usb1=query("/wd/storage/ftp/USB1");
$usb2=query("/wd/storage/ftp/USB2");
$anonymous_account = query("/wd/storage/public_share");

if( $usb1=="1" || $usb2=="1" )
{
	$active = "1";
	//create link to /tmp/itunes_share		
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


if($partition_count!="" || $partition_count!="0")
{
    $sd_status = "active";
}
else
{
    $sd_status = "inactive";
}

/*---------------------------------------------------------------------*/

if($sd_status == "inactive")
{
    fwrite("a", $START, "echo \"No HD found\"  > /dev/console\n");

}
else
{
	if ($active!="1")
	{
	   	fwrite("a", $START, "echo \"FTP is disabled !\" > /dev/console\n");

	}
	else
	{
		$infp = XNODE_getpathbytarget("", "inf", "uid", $inf_uid, 0);
		$inet   = query($infp."/inet");
		$inetp = XNODE_getpathbytarget("", "inet", "uid", $inet, 0);		 
		$ip_address=query($inetp."/ipv4/ipaddr");
		
		$user=query("/wd/storage/username");
		$password=query("/wd/storage/password");
		
		fwrite("w",$ftp_tbl,"".$user.",1\n");
		if ($anonymous_account == 1)
		{
			fwrite("a", $START, "adduser -h /tmp/ftp_share -D -H ftp\n");
			fwrite("a",$ftp_tbl,"anonymous,1\n");
		}
		fwrite("w",$pwdf, $user.":".$password."\n");

//		fwrite("a",$START,"deluser ".$user."\n");
//		fwrite("a",$START,"adduser ".$user." -h "."\"".$sharepath."\"\n");
		
//		fwrite("a", $START,"chpasswd < ".$pwdf."\n");
//		fwrite("a", $START,"rm -f ".$pwdf."\n");
		
		fwrite("a", $START,"pure-ftpd -c ".$max_user." -I ".$idle_time." -S ".$ip_address."".$port." -8 UTF-8 -9 ".$language." -A -H -U 0:0 -J -7 & \n");
			
	}	
}

if ($active!="1")
{
    fwrite("a", $STOP, "echo \"FTP is disabled !\" > /dev/console\n");
}
else
{
	fwrite("a",$STOP, "killall pure-ftpd\n");
	fwrite("a",$STOP, "rm -rf ".$sharepath."\n");
	if ($anonymous_account == 1)
	{
		fwrite("a", $STOP, "deluser ftp\n");
	}
}

?>
