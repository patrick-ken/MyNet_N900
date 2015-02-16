<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$usb1 = query("/wd/storage/".$SERVICE."/USB1");
$usb2 = query("/wd/storage/".$SERVICE."/USB2");
$service_base = "/runtime/services";
$storage_base = "/runtime/device/storage";
$sharedlist   = "/var/sharedlist";
$next         = 0;
fwrite("w", $sharedlist, "/tmp/itunes_share\n");
foreach($storage_base."/disk") {
	$usb = query("usbport");
	//TRACE_debug("usb:".$USB1);
	if ($usb == "usb1" && $USB1 != "1") {continue;}
	else if ($usb == "usb2" && $USB2 != "1") {continue;}
	else {
		foreach("entry") {
			$mntp = query("mntp");
			if ($mntp != "" && $SHAREDPOINT != $mntp) {
				//TRACE_debug("mntp:".$mntp);
				fwrite("a", $sharedlist, $mntp."\n");
				$next = 1;
			}
		}
	}
}
if ($next == "1") {
	echo "killall -9 inotifywait\n";
	echo "inotifywait -mr -e create -e delete -s /var/run/itunes_inotify.sh `cat ".$sharedlist."` &\n";
}
?>
