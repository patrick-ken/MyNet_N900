<?
include "/htdocs/phplib/xnode.php";

// delete default rules default = 1
function del_default_rules($entry)
{
	$rule_idx = 1;
	anchor($entry);
	$rule_count = query("rules/count");
	while ($rule_count > 0)
	{
		//$desp = query("rules/entry:".$rule_idx."/description");
		$def_rule = query("rules/entry:".$rule_idx."/default");
		$uid = query("rules/entry:".$rule_idx."/bwcf");
		if ($def_rule == "1")
		{
			del("rules/entry:".$rule_idx."");
			$rule_idx -= 1;
			// del bwcf rules
			$src_path = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $uid, 0);
			if ($src_path != "")
			{
				del($src_path);
			}
		}
		$rule_idx += 1;
		$rule_count -= 1;
	}
	$rule_idx -= 1;
	set("rules/count", $rule_idx);

	// delete remain items if have
	$rule_count = query("rules/entry#");
	$del_idx = $rule_idx + 1;
	while ($rule_count > 0)
	{
		if ($rule_idx > 0)
		{
			$rule_idx -= 1;
		} else {
			del("rules/entry:".$del_idx);
		}
		$rule_count -= 1;
	}
}

function action_clean()
{
	// init temp xml
	del("/runtime/bwctemp/");
	set("/runtime/bwctemp/", "");

	// loop all entries and delete default rules
	$entry_count = query("/bwc/count");
	while ($entry_count > 0)
	{
		del_default_rules("/bwc/entry");
		mov("/bwc/entry", "/runtime/bwctemp");
		$entry_count -= 1;
	}

	// back up user bwcf
	mov("/bwc/bwcf", "/runtime/bwctemp");
}

function add_bwcf_entry($src_path, $nameindex)
{
	$root = "/bwc/bwcf/entry";
	add($root, "");
	$index = query($root."#");
	anchor($root.":".$index);
	movc($src_path, $root.":".$index);
	set("uid", "BWCF-".$nameindex);
}

function add_bwc_entry($root, $enable, $desp, $bwcqd, $bwcf)
{
	add($root, "");
	$index = query($root."#");
	anchor($root.":".$index);
	set("enable", $enable);
	set("description", $desp);
	set("bwcqd", $bwcqd);
	$bwcf_idx = "BWCF-".$index;
	set("bwcf", $bwcf_idx);

	// restore bwcf data
	$src_path = XNODE_getpathbytarget("/bwc/bwcf", "entry", "uid", $bwcf_idx, 0);
	if ($src_path == "")
	{
		$src_path = XNODE_getpathbytarget("/runtime/bwctemp/bwcf", "entry", "uid", $bwcf, 0);
		add_bwcf_entry($src_path, $index);
	}
}

function action_restore()
{
	$src_path = "/runtime/bwctemp/entry";
	foreach($src_path)
	{
		$uid = query("uid");
		$dst_path = XNODE_getpathbytarget("/bwc", "entry", "uid", $uid, 0);
		set($dst_path."/autobandwidth", query("autobandwidth"));
		set($dst_path."/bandwidth", query("bandwidth"));
		set($dst_path."/flag", query("flag"));
		set($dst_path."/enable", query("enable"));
		set($dst_path."/user_define", query("user_define"));
		$max_entry = query($dst_path."/rules/max");
		$cur_entry = query($dst_path."/rules/count");
		$rem_entry = $max_entry - $cur_entry;
		foreach("rules/entry")
		{
			if ($rem_entry > 0)
			{
				$enable = query("enable");
				$desp = query("description");
				$bwcqd = query("bwcqd");
				$bwcf = query("bwcf");
				add_bwc_entry($dst_path."/rules/entry", $enable, $desp, $bwcqd, $bwcf);
				$rem_entry -= 1;
			}
		}
	}
	del("/runtime/bwctemp");

	// update entry counts
	$dst_path = "/bwc/entry";
	foreach($dst_path)
	{
		$entry_count = query("rules/entry#");
		set("rules/count", $entry_count);
	}

	// update bwcf counts
	$entry_count = query("/bwc/bwcf/entry#");
	set("/bwc/bwcf/count", $entry_count);
}

if ($ACTION == "CLEAN") 
{
	action_clean();
}
else if ($ACTION == "RESTORE")
{
	action_restore();
}
else
{
	echo "echo fastrack ACTION not support\n";
}
?>
