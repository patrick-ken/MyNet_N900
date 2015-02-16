<?
/* fatlady is used to validate the configuration for the specific service.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/PFWD/pfwd.php";
$needcheck = query($FATLADY_prefix."/UIoption");
del($FATLADY_prefix."/UIoption");
if($needcheck=="")
{
	fatlady_pfwd($FATLADY_prefix, "NAT-1", "PFWD");
}
else
{
	if($needcheck=="1")
		set("/runtime/upnpigd/conflict","0");
	else if($needcheck=="2")
		set("/runtime/upnpigd/conflict","1");

	set($FATLADY_prefix."/valid", "1");
	$_GLOBALS["FATLADY_result"] = "OK";
	$_GLOBALS["FATLADY_node"]   = "";
	$_GLOBALS["FATLADY_message"]= "";
}
?>
