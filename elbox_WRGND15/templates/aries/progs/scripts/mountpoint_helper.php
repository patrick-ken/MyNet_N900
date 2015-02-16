<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$UID = toupper($uid);
$cnt = query("/device/storage/count");
if ($cnt=="" || $cnt==0) echo "";
$sdp="/device/storage/disk";
foreach($sdp)
{
	foreach($sdp.$InDex."/entry")
	{
		if (query("uid")!=$UID) continue;
		echo query("mntp");
		return;
	}
}
echo "";
?>
