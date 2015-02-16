HTTP/1.1 200 OK

<?
/* The variables are used in js and body both, so define them here. */
$QOS_MAX_COUNT = query("/bwc/bwcf/max");
if ($QOS_MAX_COUNT == "") $QOS_MAX_COUNT = 32;

/*necessary and basic definition */
$TEMP_MYNAME    = "adv_qos_wish";
$TEMP_MYGROUP   = "adv_add";
$TEMP_STYLE		= "adv";
include "/htdocs/webinc/templates.php";
?>
