<?
include "/htdocs/webinc/feature.php";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

if(query("/wd/unlock_device")!="")
{
	$unlock_device = query("/wd/unlock_device");
	fwrite("a",$START, "event UNLOCK ".$unlock_device."\n");
}
fwrite("a",$START, "service NETATALK restart\n");
$checkpoint = 0;
if(query("/wd/storage/configured")=="1")
{
	fwrite("a",$START, "service DEVICE.ACCOUNT restart\n");
	fwrite("a",$START, "service FTP restart\n");
	fwrite("a",$START, "service SAMBA restart\n");
	$checkpoint = 1;
}
if(query("/wd/storage/samba/configured")=="1")
{
	if(query("/wd/storage/samba/USB1")=="1" || query("/wd/storage/samba/USB2")=="1")
	{
		if($checkpoint==0)
			fwrite("a",$START, "service SAMBA restart\n");
	}
	else
	{
		fwrite("a",$START, "service SAMBA stop\n");
	}
}
if(query("/wd/storage/itune/configured")=="1")
{
	if(query("/wd/storage/itune/USB1")=="1" || query("/wd/storage/itune/USB2")=="1")
	{
		fwrite("a",$START, "service ITUNES restart\n");
	}
	else
	{
		fwrite("a",$START, "service ITUNES stop\n");
	}
}
if(query("/wd/storage/ftp/configured")=="1")
{
	if(query("/wd/storage/ftp/USB1")=="1" || query("/wd/storage/ftp/USB2")=="1")
	{
		if($checkpoint==0)
			fwrite("a",$START, "service FTP restart\n");
	}
	else
	{
		fwrite("a",$START, "service FTP stop\n");
	}
}
if(query("/wd/storage/dlna/configured")=="1")
{
	if(query("/wd/storage/dlna/USB1")=="1" || query("/wd/storage/dlna/USB2")=="1")
	{
		fwrite("a",$START, "service DLNA restart\n");
	}
	else
	{
		fwrite("a",$START, "service DLNA stop\n");
	}
}
//fwrite("a",$START, "xmldbc -t \"dlna:5:service DLNASCAN restart\"\n");

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
