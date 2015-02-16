HTTP/1.1 200 OK

<?
/* The variables are used in js and body both, so define them here. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/inf.php";
$SCH_MAX_COUNT = query("/schedule/max");
if ($SCH_MAX_COUNT == "") $SCH_MAX_COUNT = 10;

$TEMP_MYNAME	= "tools_sch";
$TEMP_MYGROUP	= "adv_add";
$TEMP_STYLE		= "adv";
include "/htdocs/webinc/templates.php";
?>
