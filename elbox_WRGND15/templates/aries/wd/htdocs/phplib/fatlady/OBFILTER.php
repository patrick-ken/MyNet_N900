<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/fatlady/OBFILTER/obfilter.php";

/* Firewall MAIN */
if (verify_setting($FATLADY_prefix."/acl/obfilter")=="OK")
{
	set($FATLADY_prefix."/valid", "1");
}
?>
