<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/mdnsresponder.php";

$verbose         = query("/runtime/services/itune/verbose");
$partition_count = query("/runtime/device/storage/disk/count");
$usb1            = query("/wd/storage/itune/USB1");
$usb2            = query("/wd/storage/itune/USB2");
$INOTIFY_SCRIPT  = "/var/run/itunes_inotify.sh";
$sharepath       = "/tmp/itunes_share";
$dbpath          = "";
$sharedlist      = "/var/sharedlist";

fwrite("w", $START,          "#!/bin/sh\n");
fwrite("w", $STOP,           "#!/bin/sh\n");
fwrite("w", $INOTIFY_SCRIPT, "#!/bin/sh\n");
fwrite("w", $sharedlist,     $sharepath."\n");

//TRACE_debug("***ITUNES*** usb1 = ".$usb1."\n");
function find_shared($usb) {
	$tmp = "";
	foreach ("/runtime/device/storage/disk") {
		if ($usb == query("usbport")) {
			foreach ("entry") {
				$mntpath = query("mntp");
				$mntfolder = scut($mntpath, 0, "/var/tmp/storage/");

				if ($mntfolder != "") {
					if(query("state") == "MOUNTED") {
						$START = $_GLOBALS["START"];
						$STOP  = $_GLOBALS["STOP"];
						$sharepath = $_GLOBALS["sharepath"];
						fwrite("a",$START, "rm ".$sharepath."/".$mntfolder." 2>/dev/null \n");
						fwrite("a",$START, "ln -s -n ".$mntpath." ".$sharepath."/".$mntfolder." \n");
						if ($tmp == "") {
							$tmp = $mntpath;
						}
						fwrite("a",$_GLOBALS["sharedlist"],$mntpath."\n");
					}
				}
			}
		}
	}
	return $tmp;
}

if ($usb1 == "1" || $usb2 == "1") {
	$active = "1";
	//create link to /tmp/itunes_share
	fwrite("a", $START, "mkdir -p ".$sharepath."\n");
	if($usb1 == "1") {$dbpath = find_shared("usb1");}
	if($usb2 == "1") {
		$tmp = find_shared("usb2");
		if ($dbpath == "") {$dbpath = $tmp;}
	}
}
else {
	$active="0";
}

if ($partition_count!="" || $partition_count!="0") {$sd_status = "active";}
else                                               {$sd_status = "inactive";}

$ITUNES_CONF = "/var/mt-daapd.conf";
$INF = "br0";
$INF_NODE = XNODE_getpathbytarget("/runtime", "inf", "devnam", $INF, 0);
$INF_ADDR="";
if ($INF_NODE != "") {$INF_ADDR=query($INF_NODE."/inet/ipv4/ipaddr");}
$lanip  = XNODE_get_var("LAN-1.IPADDR");
/* info for mdnsresponder */
$port	= "3689";
$srvname = query("/device/hostname");//use hostname as srvname!
$srvcfg = "_daap._tcp. local.";
$mdirty = 0;
/*---------------------------------------------------------------------*/

if ($INF_ADDR == "") {
	fwrite("a", $START, "echo \"itunes server need ".$INF." address!\" > /dev/console\n");
	// need address to start daap service.
	$active="0";
}

if ($dbpath == "") {
	fwrite("a", $START, "echo \"itunes server need mounted device!\" > /dev/console\n");
	// need address to start daap service.
	$active="0";
}

if ($sd_status == "inactive") {
    fwrite("a", $START, "echo \"No HD found\"  > /dev/console\n");
    $mdirty = setup_mdns("MDNSRESPONDER.ITUNES","0",null,null);
}
else {
	if ($active != "1") {
	   	fwrite("a", $START, "echo \"itunes server is disabled !\" > /dev/console\n");
	   	$mdirty = setup_mdns("MDNSRESPONDER.ITUNES","0",null,null);
	}
	else {
		$mdirty = setup_mdns("MDNSRESPONDER.ITUNES",$port,$srvname,$srvcfg);
		fwrite("w", $ITUNES_CONF, "[general]\n");
		fwrite("a", $ITUNES_CONF, "servername=".query("/device/hostname")."\n");//instead of product name
		fwrite("a", $ITUNES_CONF, "web_root=/etc/admin-root\n");
		fwrite("a", $ITUNES_CONF, "port=3689\n");
		if ($verbose != "") {
			fwrite("a", $ITUNES_CONF, "logfile=/dev/console\n");
			fwrite("a", $ITUNES_CONF, "debuglevel=".$verbose."\n");
			$dbgopts = " ".query("/runtime/services/itune/dbgopts");
		}
		fwrite("a", $ITUNES_CONF, "runas=root\n");
		fwrite("a", $ITUNES_CONF, "admin_pw=admin1561\n");
		fwrite("a", $ITUNES_CONF, "mp3_dir=".$sharepath."\n");
		fwrite("a", $ITUNES_CONF, "extensions=.mp3,.m4a,.m4p\n");
		fwrite("a", $ITUNES_CONF, "db_type=sqlite3\n");
		fwrite("a", $ITUNES_CONF, "db_parms=".$dbpath."/.systemfile/\n");
		fwrite("a", $ITUNES_CONF, "db_BindTo=".$dbpath."\n");
		fwrite("a", $ITUNES_CONF, "scan_type=2\n");
		fwrite("a", $ITUNES_CONF, "rescan_interval=0\n");
		fwrite("a", $ITUNES_CONF, "always_scan=1\n");
		fwrite("a", $ITUNES_CONF, "[scanning]\n");
		fwrite("a", $ITUNES_CONF, "follow_symlinks=1\n");
		fwrite("a", $ITUNES_CONF, "[daap]\n");
		fwrite("a", $ITUNES_CONF, "supports_update=1\n");
		fwrite("a", $ITUNES_CONF, "supports_browse=1\n");
		fwrite("a", $ITUNES_CONF, "\n[plugins]\n");
		fwrite("a", $ITUNES_CONF, "plugin_dir=/lib/\n");
		fwrite("a", $START, "rm -f ".$dbpath."/.systemfile/sqlite3.db\n");
		fwrite("a", $START, "mt-daapd ".$dbgopts." -m -i ".$INF." -c ".$ITUNES_CONF." -u UTF-8\n");
		if ($verbose != "") {
			fwrite("a", $INOTIFY_SCRIPT, "echo [$0] $@ > /dev/console\n");
		}
		fwrite("a", $INOTIFY_SCRIPT, "name=\"ITUNESNOTIFY\"\n");
		fwrite("a", $INOTIFY_SCRIPT, "xmldbc -k ${name}\n");
		fwrite("a", $INOTIFY_SCRIPT, "if [ \"${1}\" = \"DONOTIFY\" ]; then\n");
		fwrite("a", $INOTIFY_SCRIPT, "	wget http://root:admin1561@".$lanip.":3689/xml-rpc?method=rescan -O - > /dev/null 2>&1\n");
		if ($verbose != "") {
			fwrite("a", $INOTIFY_SCRIPT, "	echo stop inotify timer > /dev/console\n");
		}
		fwrite("a", $INOTIFY_SCRIPT, "	exit\n");
		fwrite("a", $INOTIFY_SCRIPT, "fi\n");
		fwrite("a", $INOTIFY_SCRIPT, "if [ \"${1}\" = \"DEL\" ]; then\n");
		fwrite("a", $INOTIFY_SCRIPT, "	res=`cat /var/mt-daapd.conf | grep db_BindTo | cut -d \"=\" -f2`\n");
		fwrite("a", $INOTIFY_SCRIPT, "	res2=`echo $res | cut -d \"/\" -f5`\n");
		fwrite("a", $INOTIFY_SCRIPT, "	res3=`echo $2 | cut -d \"/\" -f4`\n");
		fwrite("a", $INOTIFY_SCRIPT, "	if [ \"$res2\" = \"$res3\" ]; then\n");
		fwrite("a", $INOTIFY_SCRIPT, "		cnt=`phpsh /etc/scripts/diskcnt_check.php PARTITION=\"varCheck\" DIRNAME=$res`\n");
		fwrite("a", $INOTIFY_SCRIPT, "		if [ $cnt -gt 0 ]; then\n");
		fwrite("a", $INOTIFY_SCRIPT, "			xmldbc -t RESTARTITUNES:6:\"service ITUNES restart\"\n");
		fwrite("a", $INOTIFY_SCRIPT, "			exit\n");
		fwrite("a", $INOTIFY_SCRIPT, "		fi\n");
		fwrite("a", $INOTIFY_SCRIPT, "	fi\n");
		fwrite("a", $INOTIFY_SCRIPT, "fi\n");
		fwrite("a", $INOTIFY_SCRIPT, "xmldbc -t ${name}:30:\"${0} DONOTIFY\"\n");
		if ($verbose != "") {
			fwrite("a", $INOTIFY_SCRIPT, "echo start inotify timer > /dev/console\n");
		}
		fwrite("a", $START, "chmod 777 ".$INOTIFY_SCRIPT."\n");
		fwrite("a", $START, "inotifywait -mr -e create -e delete -s ".$INOTIFY_SCRIPT." `cat ".$sharedlist."` &\n");
	}
}

if ($active != "1") {
    fwrite("a", $STOP, "echo \"itunes server is disabled !\" > /dev/console\n");
}
else {
	fwrite("a",$STOP, "killall -9 inotifywait\n");
	fwrite("a",$STOP, "killall -9 mt-daapd\n");
	fwrite("a",$STOP, "rm -f /var/run/mt-daapd.pid\n");
	fwrite("a",$STOP, "rm -rf ".$sharepath."\n");
}

if ($mdirty > 0) {
	fwrite("a", $START, "service MDNSRESPONDER restart\n");
	fwrite("a", $STOP, "sh /etc/scripts/delpathbytarget.sh /runtime/services/mdnsresponder server uid MDNSRESPONDER.ITUNES\n");
	fwrite("a", $STOP, "service MDNSRESPONDER restart\n");
}

if ($active == "1")
{
	fwrite("a", $START, "sh /etc/services/ITUNES_loop.sh &\n");
	fwrite("a", $START, "echo $! > /var/run/ITUNES_loop.pid &\n");
}
fwrite("a", $STOP, "if [ -f \"/var/run/ITUNES_loop.pid\" ]; then\n");
fwrite("a", $STOP, "	GetPID=`cat /var/run/ITUNES_loop.pid`\n");
fwrite("a", $STOP, "	kill $GetPID\n");
fwrite("a", $STOP, "	rm /var/run/ITUNES_loop.pid\n");
fwrite("a", $STOP, "fi\n");
?>
