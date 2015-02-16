HTTP/1.1 200 OK

<?
/* The variables are used in js and body both, so define them here. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
$layout = query("/device/layout");

/* necessary and basic definition */
$TEMP_MYNAME    = "lan";
$TEMP_MYGROUP   = "adv_lan";
$TEMP_STYLE		= "adv";
include "/htdocs/webinc/templates.php";
?>
