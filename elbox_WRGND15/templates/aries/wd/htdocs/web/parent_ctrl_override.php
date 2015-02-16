HTTP/1.1 200 OK
Content-Type: text/xml

<?
$firmeare_version = query("/runtime/device/firmwareversion");
$cp_server1 = query("/runtime/netstar/cp_server:1/url");
$cp_server2 = query("/runtime/netstar/cp_server:2/url");
$wan_mac = query("/runtime/netstar/wanmac");

set("/runtime/parent_ctrl/pwd",		$_POST["pwd"]);
set("/runtime/parent_ctrl/user_mac",$_POST["user_mac"]);
set("/runtime/parent_ctrl/url_host",$_POST["url_host"]);
$pwd 		= get("s", "/runtime/parent_ctrl/pwd");
$user_mac	= get("s", "/runtime/parent_ctrl/user_mac");
$url_host	= get("s", "/runtime/parent_ctrl/url_host");
if($cp_server1 != "" || $cp_server2 != "")
{
	if($_POST["override_time"]=="permanent")
	{
		setattr("/runtime/parent_ctrl/override_pwd","get", "cp_process -s ".$cp_server1." -x ".$cp_server2." -f ".$firmeare_version." -p ".$pwd." -m ".$user_mac." -l ".$url_host." -z ".$wan_mac);		
	}
	else
	{	
		setattr("/runtime/parent_ctrl/override_pwd","get", "cp_process -s ".$cp_server1." -x ".$cp_server2." -f ".$firmeare_version." -p ".$pwd." -z ".$wan_mac);
	}
}

if(query("/runtime/parent_ctrl/override_pwd") == "Correct")
{
	$result = "OK";
	$user_n = query("/runtime/parent_ctrl/user#");
	$new_user = true;
	if($user_n > 0)
	{
		foreach("/runtime/parent_ctrl/user")
		{
			if(query("mac")==$_POST["user_mac"])
			{
				$user_n = $InDeX;
				$new_user = false;
				break;
			}	
		}
		if($new_user==true)
		{
			$user_n++;
			set("/runtime/parent_ctrl/user:".$user_n."/mac", $_POST["user_mac"]);
		}
	}
	else
	{ 
		$user_n=1;
		set("/runtime/parent_ctrl/user:".$user_n."/mac", $_POST["user_mac"]);
	}
	
	$n = query("/runtime/parent_ctrl/user:".$user_n."/entry#");
	$n++;
	set("/runtime/parent_ctrl/user:".$user_n."/entry:".$n."/url_host", $_POST["url_host"]);
	set("/runtime/parent_ctrl/user:".$user_n."/entry:".$n."/category", $_POST["category"]);
	set("/runtime/parent_ctrl/user:".$user_n."/entry:".$n."/override_type", $_POST["override_type"]);
	$uptime = query("/runtime/device/uptime");
	$expire_time = 0;
	if ($_POST["override_time"]=="1") $expire_time = "one_time";
	else if ($_POST["override_time"]=="1hour") $expire_time = $uptime + 3600;
	else if ($_POST["override_time"]=="2hour") $expire_time = $uptime + 7200;
	else if ($_POST["override_time"]=="1day")  $expire_time = $uptime + 86400;
	else if ($_POST["override_time"]=="permanent") $expire_time = "permanent";
	set("/runtime/parent_ctrl/user:".$user_n."/entry:".$n."/expire_time", $expire_time);
	
	event("PARENTCTRL");
	
while($n > 0)
{
	$n--; 
	$expire_time = query("/runtime/parent_ctrl/user:".$user_n."/entry:".$n."/expire_time");
	if($expire_time!="permanent" && $expire_time!="one_time")
	{
		if($expire_time < $uptime)	del("/runtime/parent_ctrl/user:".$user_n."/entry:".$n);
	}	
}
}
else
{
	$result = query("/runtime/parent_ctrl/override_pwd");
}	

echo '<?xml version="1.0"?>\n';
?><parent_ctrl_override>
	<report><?=$result?></report>
</parent_ctrl_override>
