<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$service_base = "/runtime/services";
$storage_base = "/runtime/device/storage";
$verbose = query($service_base."/".$SERVICE."/verbose");
$prefix = "[".toupper($SERVICE).":".$PARTITION."] ";
$usb1 = query("/wd/storage/".$SERVICE."/USB1");
$usb2 = query("/wd/storage/".$SERVICE."/USB2");

function msg($level, $string) {
	if ($_GLOBALS["verbose"] >= $level) TRACE_debug($string);
}

function find_usb_port($partition) {
	$disk_base = $_GLOBALS["storage_base"]."/disk";
	foreach($disk_base) {
		$usbport = query("usbport");
		msg(3, $_GLOBALS["prefix"]."usb port: ".$usbport);
		//foreach($disk_base.":".$InDeX."/entry") {
		foreach("entry") {
			$uid = tolower(query("uid"));
			$pid = query("pid");
			msg(3, $_GLOBALS["prefix"]."uid/pid:".$uid."/".$pid);
			if ($uid == $partition || $pid == "0") {
				msg(1, $_GLOBALS["prefix"].$partition." on ".$usbport);
				return $usbport;
			}
		}
	}
	return;
}

function find_shared_point($partition) {
	$disk_base = $_GLOBALS["storage_base"]."/disk";
	foreach($disk_base) {
		foreach("entry") {
			$uid = tolower(query("uid"));
			msg(3, $_GLOBALS["prefix"]."uid/partition: ".$uid."/".$partition);
			$spoint = query("mntp");
			msg(3, $_GLOBALS["prefix"]."Shared point: ".$spoint);
			if (query("pid") == "0" && query("fs") != "UNKNOWN" && $spoint != "") {
				return $spoint;
			}
			if ($uid == $partition) {
				return $spoint;
			}
		}
	}
	return;
}

function check_usb($usb1, $usb2, $usbport, $partition) {
	if ($usb1 == 1 && $usbport == "usb1") {
		$check = 1;
		$string = "USB 1 shared. Checking ".$partition;
	}
	else if ($usb2 == 1 && $usbport == "usb2") {
		$check = 1;
		$string = "USB 2 shared. Checking ".$partition;
	}
	else {
		$check = 0;
		if ($partition == "") {$string = "No shared partition";}
		else {$string = $partition." doesn't share";}
	}
	msg(1, $_GLOBALS["prefix"].$string);
		return $check;
}

//TRACE_debug("disk base:".$disk_base);
msg(1, $prefix.$ACTION." ".$PARTITION);
msg(3, $prefix."usb1/usb2: ".$usb1."/".$usb2);
$usbport = find_usb_port($PARTITION);
msg(1, $prefix."Find ".$PARTITION." on ".$usbport);

if ($SERVICE == "samba") {
	$service = 1;
	$share_base = "/var/tmp/smb_share";
}
else if ($SERVICE == "ftp") {
	$service = 1;
	$share_base = "/var/tmp/ftp_share";
}
else if ($SERVICE == "itune") {
	$service = 1;
	$share_base = "/var/tmp/itunes_share";
}
else if ($SERVICE == "afp") {
	$service = 0;
	$share_base = "";
}

if (check_usb($usb1, $usb2, $usbport, $PARTITION) == 1 && $service == 1) {
	$sharedpoint = find_shared_point($PARTITION);
	msg(1, $prefix.$PARTITION." -> ".$sharedpoint);
	$shareddir = cut($sharedpoint, 4, "/");
	if ($SERVICE == "itune") {
		echo "daap=`ps|grep daap|grep -v grep`\n";
		echo "cnt=`xmldbc -g /runtime/device/storage/count`\n";
	}
	if ($ACTION == "add") {
		msg(1, $prefix."Add shared linking: ".$share_base."/".$shareddir);
		echo "rm -f ".$share_base."/".$shareddir." 2> /dev/null\n";
		echo "ln -s -n ".$sharedpoint." ".$share_base."/".$shareddir."\n";
		if ($SERVICE == "itune") {
			echo "if [ \"$daap\" = \"\" ]; then\n";
			echo "	service ITUNES restart\n";
			echo "else\n";
			echo "	phpsh /etc/scripts/inotifywait.php USB1=".$usb1." USB2=".$usb2."\n";
			echo "fi\n";
		}
	}
	else if ($ACTION == "remove") {
		msg(1, $prefix."Remove shared linking: ".$share_base."/".$shareddir);
		echo "rm -f ".$share_base."/".$shareddir." 2> /dev/null\n";
		if ($SERVICE == "itune") {
			echo "if [ \"$cnt\" = \"0\" ]; then\n";
			echo "	service ITUNES stop\n";
			echo "else\n";
			echo "	phpsh /etc/scripts/inotifywait.php USB1=".$usb1." USB2=".$usb2." SHAREDPOINT=".$sharedpoint."\n";
			echo "fi\n";
		}	
	}

}
   /*after plugging in or removing device, smb.conf will be re-created.*/
   if($SERVICE == "samba") {
   		echo "phpsh /etc/scripts/smb_reloadcfg.php\n";}

?>
