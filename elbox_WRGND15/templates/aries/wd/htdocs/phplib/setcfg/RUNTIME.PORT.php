<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

$conflict = query($SETCFG_prefix."/runtime/upnpigd/portmapping/conflict");
if($conflict=="1")
{
	set("runtime/upnpigd/portmapping/conflict", "1");
}
else
{
	set("runtime/upnpigd/portmapping/conflict", "0");
}
?>
