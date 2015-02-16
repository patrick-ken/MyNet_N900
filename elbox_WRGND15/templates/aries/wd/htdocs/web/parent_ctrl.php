HTTP/1.1 200 OK

<?
/* The variables are used in js and body both, so define them here. */
include "/htdocs/phplib/inf.php";

$DEVICE_COUNT = query("/security/netstar/devicecount");
if ($DEVICE_COUNT == "") $DEVICE_COUNT = 0;

/*necessary and basic definition */
$TEMP_MYNAME    = "parent_ctrl";
$TEMP_MYGROUP   = "adv_secure";
$TEMP_STYLE		= "adv";
include "/htdocs/webinc/templates.php";
?>