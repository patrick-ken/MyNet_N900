<?
include "/etc/services/PHYINF/phywifi.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

/* reset wifi config 
- we delete the db node, and let it reboot
*/

$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
if ($p!="") del($p);
$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.2", 0);
if ($p!="") del($p);
$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
if ($p!="") del($p);
$p = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.2", 0);
if ($p!="") del($p);

del("/wifi");

TRACE_error("Delete wifi config success... \n");

?> 
