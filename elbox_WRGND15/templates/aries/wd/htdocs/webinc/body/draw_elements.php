<?
include "/htdocs/phplib/xnode.php";

function DRAW_select_v6dhcpclist($inf, $select_id, $op1dsc, $op1value, $onchange, $ignore, $class)
{
	if ($select_id != "")	$id	 = ' id="'.$select_id.'"';
	if ($onchange != "")	$chg = ' onchange="'.$onchange.';"';
	if ($ignore == 1)		$mdf = ' modified="ignore"';
	if ($class != "")		$cls = ' class="'.$class.'"';

	echo '<select'.$id.$chg.$mdf.$cls.' >\n';
	echo '\t<option value="'.$op1value.'">'.$op1dsc.'</option>\n';

	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	foreach ($p."/dhcps6/leases/entry")
	{
		echo '\t<option value="'.query("ipaddr").'">'.query("hostname").' ('.query("ipaddr").')</option>\n';
	}

	echo '</select>\n';
}

function DRAW_select_dhcpclist($inf, $select_id, $op1dsc, $op1value, $onchange, $ignore, $class)
{
	if ($select_id != "")	$id	 = ' id="'.$select_id.'"';
	if ($onchange != "")	$chg = ' onchange="'.$onchange.';"';
	if ($ignore == 1)		$mdf = ' modified="ignore"';
	if ($class != "")		$cls = ' class="'.$class.'"';

	echo '<select'.$id.$chg.$mdf.$cls.'>\n';
	echo '\t<option value="'.$op1value.'">'.$op1dsc.'</option>\n';

	$p = XNODE_getpathbytarget("/runtime", "inf", "uid", $inf, 0);
	foreach ($p."/dhcps4/leases/entry")
	{
		echo '\t<option value="'.query("ipaddr").'">'.query("hostname").' ('.query("ipaddr").')</option>\n';
	}

	echo '</select>\n';
}

function DRAW_select_sch($select_id, $op1dsc, $op1value, $onchange, $ignore, $class)
{
	if ($select_id != "")	$id  = ' id="'.$select_id.'"';
	if ($onchange != "")	$chg = ' onchange="'.$onchange.';"';
	if ($ignore == 1)		$mdf = ' modified="ignore"';
	if ($class != "")		$cls = ' class="'.$class.'"';
	echo '<select'.$id.$mdf.$cls.$chg.' >\n';
	echo '	<option value="'.$op1value.'">'.$op1dsc.'</option>\n';
	$max = query("/schedule/max");
	foreach ("/schedule/entry")
	{
		if ($InDeX > $max) break;
		echo '<option value="'.query("uid").'">'.query("description").'</option>\n';
	}
	echo '</select>\n';
}

function DRAW_select_inbfilter($select_id, $op1dsc, $op1value, $op2dsc, $op2value, $onchange, $ignore, $class)
{
	if ($select_id != "")	$id  = ' id="'.$select_id.'"';
	if ($onchange != "")	$chg = ' onchange="'.$onchange.';"';
	if ($ignore == 1)		$mdf = ' modified="ignore"';
	if ($class != "")		$cls = ' class="'.$class.'"';
	echo '<select'.$id.$mdf.$cls.' >\n';
	echo '	<option value="'.$op1value.'">'.$op1dsc.'</option>\n';
	echo '	<option value="'.$op2value.'">'.$op2dsc.'</option>\n';
	$max = query("/acl/inbfilter/max");
	foreach ("/acl/inbfilter/entry")
	{
		if ($InDeX > $max) break;
		echo '<option value="'.query("uid").'">'.query("description").'</option>\n';
	}
	echo '</select>\n';
}
?>
