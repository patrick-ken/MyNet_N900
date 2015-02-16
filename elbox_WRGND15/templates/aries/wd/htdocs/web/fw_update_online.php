HTTP/1.1 200 OK
Content-Type: text/xml

<?
include "/htdocs/phplib/xnode.php";

$uid = "WAN-1";
if ($AUTHORIZED_GROUP < 0)
{
	$result = "Authenication fail";
}
else if ($_POST["act"] == "fw_check")
{
	set("/runtime/device/upgrades/available", "");
	event("FW.CHECK");
	$result = "OK";
}
else if ($_POST["act"] == "fw_check_force")
{
	set("/runtime/device/upgrades/available", "");
	//Event the firmware is the latest, it would still get the path to download.
	event("FW.CHECK.FORCE");
	$result = "OK";
}
else if ($_POST["act"] == "fw_download")
{
	set("/runtime/device/upgrades/upgrade/complete", "");
	set("/runtime/device/upgrades/upgrade/complete_percent", "");
	event("FW.DOWNLOAD");
	$result = "OK";
}
else if ($_POST["act"] == "fw_update")
{
	set("/runtime/device/upgrades/upgrade/error_code", "");
	event("FW.UPDATE");
	$result = "OK";
}
else if ($_POST["act"] == "fw_check_result")
{
	$result = get("x", "/runtime/device/upgrades/available");
	if($result=="")
	{
		if(query("/runtime/device/layout") == "bridge")
		{
			$wan_status = query("/runtime/device/wan_status");
			if($wan_status!="1")
			{
				$result = "ConnectionFail";
			}
		}
		else
		{
			$p = XNODE_getpathbytarget("", "inf", "uid", $uid, 0);
			$phy = query($p."/phyinf");
			$q = XNODE_getpathbytarget("", "runtime/phyinf", "uid", $phy, 0);
			$link_status = query($q."/linkstatus");
			if($link_status=="")
			{//If WAN is physically down, directly return ConnectionFail
				$result = "ConnectionFail";
			}
		}
	}
}
else if ($_POST["act"] == "fw_download_result")
{
	$result = get("x", "/runtime/device/upgrades/upgrade/complete");
	if($result=="failed") event("FW.DOWNLOAD.FAIL");
	$complete_percent = get("x", "/runtime/device/upgrades/upgrade/complete_percent");
}
else if ($_POST["act"] == "fw_update_result")
{
	$result = get("x", "/runtime/device/upgrades/upgrade/error_code");
}
echo '<?xml version="1.0"?>\n';
?><fw_update_online>
	<report><?=$result?></report>
	<fw_lastest_ver><? echo query("/runtime/device/upgrades/upgrade/version");?></fw_lastest_ver>
	<complete_percent><?=$complete_percent?></complete_percent>
</fw_update_online>
