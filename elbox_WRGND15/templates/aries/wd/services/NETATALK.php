<?
/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";
include "/htdocs/phplib/mdnsresponder.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

$port	= "548";
$product = query("/runtime/device/modelname");
$srvname = "WD ".$product;
$srvcfg = "_afpovertcp._tcp. local.";
$mdirty = 0;
//$mdirty = setup_mdns("MDNSRESPONDER.NETATALK",$port,$srvname,$srvcfg);

$active = query("/netatalk/active");
//$sharepath = query("/runtime/device/storage/disk:1/entry:1/mntp");
$sharepath = "/var/tmp/storage/Public";
$user = query("/wd/storage/username");
$public_share = query("/wd/storage/public_share");

//$AFPD_CONF = "/var/afpd.conf";
$APPLE_VOLUME_CONF = "/var/AppleVolumes.default";

$stsp = XNODE_getpathbytarget("/inet", "entry", "uid","INET-1", 0);
$ipaddr=query($stsp."/ipv4/ipaddr");

if ($public_share=="1")
{
	//$afpd_cmd="".$product." -tcp -unixcodepage UTF8 -ipaddr ".$ipaddr." -uamlist uams_clrtxt.so,uams_guest.so,uams_dhx2.so -nosavepassword -defaultvol ".$APPLE_VOLUME_CONF." -systemvol /etc/AppleVolumes.system -uservol -uampath /lib/uams \n";
	//$vol_cmd="".$sharepath." ".$product." perm:0777 options:usedots,tm dbpath:".$sharepath." cnidscheme:dbd\n";
	//$vol_cmd1 = "".$sharepath."/wd_vol WD_Volume allow:".$user." cnidscheme:cdb volcharset:UTF8 options:usedots,upriv dbpath:".$sharepath."/wd_vol/.AppleDB\n";
	//$vol_cmd2 = "".$sharepath."/tm_vol TM_Volume allow:".$user." cnidscheme:cdb volcharset:UTF8 options:usedots,upriv,tm dbpath:".$sharepath."/tm_vol/.AppleDB\n";
	//$vol_cmd1 = "".$sharepath."/wd_vol WD_Volume deny:nobody,admin cnidscheme:cdb volcharset:UTF8 options:usedots,invisibledots dbpath:".$sharepath."/wd_vol/.AppleDB\n";
	//$vol_cmd2 = "".$sharepath."/tm_vol TM_Volume deny:nobody,admin cnidscheme:cdb volcharset:UTF8 options:usedots,invisibledots,tm dbpath:".$sharepath."/tm_vol/.AppleDB\n";
	$vol_cmd1 = "".$sharepath." Public cnidscheme:cdb volcharset:UTF8 options:usedots,invisibledots,tm dbpath:".$sharepath."/.AppleDB\n";
}
else
{
	$vol_cmd1 = "".$sharepath." Public deny:nobody cnidscheme:cdb volcharset:UTF8 options:usedots,invisibledots,tm dbpath:".$sharepath."/.AppleDB\n";
}

if ($active=="1")
{
        $mdirty = setup_mdns("MDNSRESPONDER.NETATALK",$port,$srvname,$srvcfg);
        //fwrite("w", $AFPD_CONF, "".$afpd_cmd."\n");
        fwrite("w", $APPLE_VOLUME_CONF, "".$vol_cmd1."\n");
        //fwrite("a", $APPLE_VOLUME_CONF, "".$vol_cmd2."\n");
	fwrite("a",$START, "mkdir -p /var/lock\n");
	//fwrite("a",$START, "mkdir -p ".$sharepath."/wd_vol\n");
	//fwrite("a",$START, "mkdir -p ".$sharepath."/tm_vol\n");
	//fwrite("a",$START, "chmod 777 ".$sharepath."/wd_vol\n");
	//fwrite("a",$START, "chmod 777 ".$sharepath."/tm_vol\n");

	fwrite("a",$START, "afpd -f /var/AppleVolumes.default -F /etc/netatalk/afpd.conf -s /etc/netatalk/AppleVolumes.system -U /lib/uams &\n");
	//fwrite("a",$START, "afpd -f /etc/netatalk/AppleVolumes.default -F /etc/netatalk/afpd.conf -s /etc/netatalk/AppleVolumes.system -U /lib/uams &\n");
	//if ($mdirty>0)  fwrite("a",$START,"service MDNSRESPONDER restart");

	fwrite("a", $STOP, "echo \"Netatalk is disabled !\" > /dev/console\n");

	fwrite("a",$STOP, "killall -9 afpd\n");
	fwrite("a",$STOP, "rm -rf /var/lock\n");
	//fwrite("a",$STOP, "killall -9 cnid_metad\n");

}
else
{
	$mdirty = setup_mdns("MDNSRESPONDER.NETATALK","0",null,null);
	//if ($mdirty>0)  fwrite("a", $STOP,"service MDNSRESPONDER restart");
}

if ($mdirty>0)
{
	fwrite("a", $START, "service MDNSRESPONDER restart\n");
	fwrite("a", $STOP, "service MDNSRESPONDER restart\n");
}

?>
