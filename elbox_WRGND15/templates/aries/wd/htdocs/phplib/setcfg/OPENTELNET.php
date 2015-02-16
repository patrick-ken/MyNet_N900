<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
TRACE_debug("dhcp : (".query($SETCFG_prefix."/telnet_disable").")");
if (query($SETCFG_prefix."/telnet_disable") == "1")
{
	set("/runtime/device/telnet_disable", "1");
}
else
{
	set("/runtime/device/telnet_disable", "0");	
}
?>

