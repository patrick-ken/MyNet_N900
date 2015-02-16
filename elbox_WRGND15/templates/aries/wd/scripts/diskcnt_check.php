<?
if($PARTITION=="varCheck")
{
	$cnt = 0;
	foreach("/runtime/device/storage/disk")
	{
		foreach("entry")
		{
			$mntp = query("mntp");
			if($mntp!=$DIRNAME)
			{
				$cnt++;
			}
		}
	}
}
else
{
	$base = "/runtime/device/storage";
	$cnt = query("/runtime/device/storage/count");
	if($cnt=="")	$cnt="0";

	if($cnt=="1")
	{
		$entry_cnt = query($base."/disk/entry#");
		if($entry_cnt=="1")
		{
			$uid = tolower(query($base."/disk/entry/uid"));
			if($uid==$PARTITION)
			{
				$cnt="0";
			}
		}
	}
}
echo "echo ".$cnt."\n";
?>