<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$dlna_base = "/runtime/services/dlna";
$verbose = query($dlna_base."/verbose");

if ($verbose >= 2) TRACE_debug("---------------");
foreach($dlna_base."/dbscanner/entry") {
	$name = query("name");
	$sharedpoint = query("sharedpoint");
	$status = query("status");
	if ($status == "inactive") {
		if ($verbose >= 2) TRACE_debug("[DLNA] Monitor: ".$name." is inactive");
	}
}
?>
