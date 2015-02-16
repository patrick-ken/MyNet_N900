<?
include "/htdocs/phplib/phyinf.php";

$loglevel = query("/device/log/level");
if		($loglevel == "WARNING")	$loglevel = "warn";
else if	($loglevel == "NOTICE")		$loglevel = "notice";
else if	($loglevel == "DEBUG")		$loglevel = "debug";
else $loglevel = "notice";

$cmd = "";
foreach("/runtime/inf")
{
	$uid = query("uid");
	$phyinf = query("phyinf");
	$ifdev = PHYINF_getifname($phyinf);

	if ($uid!="" && $ifdev!="") $cmd = $cmd." -e ".$ifdev."=".$uid;
}


fwrite(w, $START, "#!/bin/sh\n");
fwrite(a, $START, "logd -p ".$loglevel." &\n");
fwrite(a, $START, "klogd -p ".$loglevel.$cmd." &\n");
fwrite(a, $START, "xmldbc -k remotelog\n");
fwrite(a, $START, "xmldbc -t remotelog:120:\"/etc/scripts/remotelog.sh\"\n");

fwrite(w, $STOP, "#!/bin/sh\n");
fwrite(a, $STOP, "killall klogd\n");
fwrite(a, $STOP, "killall logd\n");
?>
