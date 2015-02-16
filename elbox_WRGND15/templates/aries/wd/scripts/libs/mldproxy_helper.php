#!/bin/sh
<?/* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

/*
we got these from igmpproxyd :
$ACTION 
$GROUP		--> group ip
$IF		--> interface
$SRC		--> client ip
$GROUPMAC	--> group mac
$SRCMAC		--> client mac
*/

$mld = "/runtime/services/mldproxy";
if ($ACTION=="add_member")
{
   $found = 0;
   foreach ($mld."/group") if ($VaLuE==$GROUP) $found=1;
   if ($found == 0)
   {
		add($mld."/group", $GROUP);
   }
	echo 'echo "add '.$GROUPMAC.' '.$SRCMAC.'" > /proc/alpha/multicast_'.$IF.'\n';
}
else if ($ACTION=="del_member")
{
	$found = 0;
	foreach ($mld."/group") if ($VaLuE==$GROUP) $found=$InDeX;
	if ($found > 0)
	{
		del($mld."/group:".$found);
	}
	echo 'echo "remove '.$GROUPMAC.' '.$SRCMAC.'" > /proc/alpha/multicast_'.$IF.'\n';
}
?>
