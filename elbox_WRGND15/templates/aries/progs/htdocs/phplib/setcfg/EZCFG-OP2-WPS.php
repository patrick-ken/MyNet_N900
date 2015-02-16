<?
include "/htdocs/phplib/xnode.php";
del("/ezcfg");
if (query($SETCFG_prefix."/ezcfg/wps/enable_24g")=="1")
{
	set("/ezcfg/wps/enable_24g", "1");
}
else if (query($SETCFG_prefix."/ezcfg/wps/enable_5g")=="1")
{
	set("/ezcfg/wps/enable_5g", "1");
}
?>
