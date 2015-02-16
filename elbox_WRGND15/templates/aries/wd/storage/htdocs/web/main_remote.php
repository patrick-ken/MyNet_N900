HTTP/1.1 200 OK

<?
include "/htdocs/phplib/xnode.php";

/* necessary and basic definition */
$TEMP_MYNAME    = "main_remote";
$TEMP_MYGROUP   = "main_remote";
$TEMP_STYLE		= "main";
include "/htdocs/webinc/templates.php";
dophp("load", "/htdocs/web/portal/comm/drag.php");
dophp("load", "/htdocs/web/portal/comm/event.php");
dophp("load", "/htdocs/web/portal/comm/fade.php");
dophp("load", "/htdocs/web/portal/comm/overlay.php");
dophp("load", "/htdocs/web/portal/comm/scoot.php");
?>
