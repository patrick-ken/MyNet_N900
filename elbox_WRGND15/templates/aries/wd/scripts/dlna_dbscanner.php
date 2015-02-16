<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$service_base = "/runtime/services";
$storage_base = "/runtime/device/storage";
$dlna_shared_base="/var/tmp/dlna_share";
$dlna_ipc="/tmp/dms_ipc";
$dlna_base = "/runtime/services/dlna/dbscanner";
$verbose = query("/runtime/services/dlna/verbose");
$prefix = "[".toupper("DLNA")."] ";
$usb1 = query("/wd/storage/dlna/USB1");
$usb2 = query("/wd/storage/dlna/USB2");

function msg($level, $string) {
	if ($_GLOBALS["verbose"] >= $level) TRACE_debug($string);
}

function find_usb_port($partition) {
	$disk_base = $_GLOBALS["storage_base"]."/disk";
	foreach($disk_base) {
		$usbport = query("usbport");
		msg(3, $_GLOBALS["prefix"]."usb port: ".$usbport);
		foreach($disk_base.":".$InDeX."/entry") {
			$uid = tolower(query("uid"));
			msg(3, $_GLOBALS["prefix"]."uid :".$uid);
			if ($uid == $partition || query("pid") == 0) {
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
		foreach($disk_base.":".$InDeX."/entry") {
			$uid = tolower(query("uid"));
			if ($uid == $partition || query("pid") == 0) {
				$spoint = query("mntp");
				msg(3, $_GLOBALS["prefix"]."Shared point: ".$spoint);
				return $spoint;
			}
		}
	}
	return;
}

function check_usb($usb1, $usb2, $usbport) {
	if ($usb1 == 1 && $usbport == "usb1") {
		$check = 1;
		$string = "USB 1 shared. Checking ".$PARTITION;
	}
	else if ($usb2 == 1 && $usbport == "usb2") {
		$check = 1;
		$string = "USB 2 shared. Checking ".$PARTITION;
	}
	else {
		$check = 0;
		if ($PARTITION == "") $string = "No shared partition";
		else $string = $PARTITION." doesn't share";
	}
	msg(1, $_GLOBALS["prefix"].$string);
		return $check;
}


if ($ACTION == "add") {
	$usbport = find_usb_port($PARTITION);
	msg(1, $prefix."Find usb port:".$usbport);
	if (check_usb($usb1, $usb2, $usbport) == 0) exit;
	$hit = 0;
	$disk_base = "/runtime/device/storage/disk";
	foreach($disk_base) {
		foreach($disk_base.":".$InDeX."/entry") {
			$uid = query("uid");
			if (toupper($PARTITION) == $uid || query("pid") == 0) {
				$hit = 1;
				$sharedpoint = query("mntp");
			}
		}
	}
	$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", "partition", $PARTITION, 0);
	if ($hit == 1 && $dlna_stsp == "")
	{
		$cnt = query($dlna_base."/count");
		$cnt++;
		$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", "partition", $PARTITION, 1);
		msg(1, $prefix.$ACTION." ".$PARTITION."@".$dlna_stsp);
		set($dlna_base."/count", $cnt);
		set($dlna_stsp."/name", $NAME);
		set($dlna_stsp."/partition", $PARTITION);
		set($dlna_stsp."/sharedpoint", $sharedpoint);
		set($dlna_stsp."/status", "inactive");
	}
}
else if ($ACTION == "remove") {
	$usbport = find_usb_port($PARTITION);
	msg(1, $prefix."Find usb port:".$usbport);
	if (check_usb($usb1, $usb2, $usbport) == 0) exit;
	$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", "partition", $PARTITION, 0);
	msg(1, $prefix.$ACTION." ".$PARTITION."@".$dlna_stsp);
	if ($dlna_stsp != "") {
		$sharedpoint = query($dlna_stsp."/sharedpoint");
		$shareddir = cut($sharedpoint, 4, "/");
		$cmd="remove;".$shareddir.";".$shareddir.";local;".$dlna_shared_base."/".$shareddir.";";
		msg(2, $prefix."cmd ".$cmd);
		fwrite("w", $dlna_ipc, $cmd);
		msg(1, $prefix.$ACTION." ".$dlna_shared_base."/".$shareddir);
		unlink($dlna_shared_base."/".$shareddir);
		del($dlna_stsp);
		$cnt = query($dlna_base."/count");
		$cnt--;
		set($dlna_base."/count", $cnt);
	}
	$sharedpoint = find_shared_point($PARTITION);
	if ($sharedpoint != "" ) {
		$shareddir = cut($sharedpoint, 4, "/");
		unlink($dlna_shared_base."/".$shareddir);
	}
}
else if ($ACTION == "start") {
	$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", "name", $NAME, 0);
	msg(1, $prefix."Start ".$NAME."@".$dlna_stsp);
	if ($dlna_stsp != "") {
		$sharedpoint = query($dlna_stsp."/sharedpoint");
		$shareddir = cut($sharedpoint, 4, "/");
		if ($verbose >= 2) echo "echo [DLNA] Scan ".$dlna_shared_base."/".$shareddir."\n";
		echo "rm -f ".$dlna_shared_base."/".$shareddir."\n";
		echo "ln -s ".$sharedpoint." ".$dlna_shared_base."/".$shareddir."\n";
		$cmd="echo \"add;".$shareddir.";".$shareddir.";local;".$dlna_shared_base."/".$shareddir.";\" > ".$dlna_ipc;
		if ($verbose > 2) echo "echo [DLNA] cmd ".$cmd."\n";
		echo $cmd."\n";
		set($dlna_stsp."/status", "active");
	}
}
else if ($ACTION == "stop") {
	msg(1, $prefix.$ACTION." scanner: ".$NAME);
	$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", "name", $NAME, 0);
	set($dlna_stsp."/status", "scanned");
}
else if ($ACTION == "path") {
	$dlna_stsp = XNODE_getpathbytarget($dlna_base, "entry", $NODE, $VALUE, 0);
	echo $dlna_stsp;
}
?>
