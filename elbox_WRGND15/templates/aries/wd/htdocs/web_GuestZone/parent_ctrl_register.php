HTTP/1.1 200 OK
Content-Type: text/xml

<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}  

$wan_mac = query("/runtime/netstar/wanmac");

if ($AUTHORIZED_GROUP < 0)
{
	$res = "Authenication fail";
}
else if ($_POST["act"] == "RegisterRouter")
{
	$mail = $_POST["email"];
	$old_mail = query("/security/netstar/email");
	$old_sn = query("/security/netstar/password");
	$sn = $_POST["password"];
	$ver = query("/runtime/device/firmwareversion");
	$loc = $_POST["location"];
	$devname =  query("/device/hostname");
	$mac = $_POST['mac'];
	
	set("/security/netstar/email", $mail);
	set("/security/netstar/enable", "1" );
	set("/security/netstar/location", $loc );
	//according to WD ITR 42115 suggestion, we start to remember password.
	set("/security/netstar/password", $sn);

	$rw_server1 = query("/runtime/netstar/rw_server:1/url");
	$rw_server2 = query("/runtime/netstar/rw_server:2/url");
	$language = query("/device/features/language");
	if($language=="auto")
	{
		$language = query("/runtime/device/auto_language");	
	}	
	if($language=="")
	{
		$language = "en";	
	}	
	
	if($rw_server1!=""||$rw_server2!="")
	{
		$command = "rw_process -s ".$rw_server1." -x ".$rw_server2." -f ".$ver." -l ".$loc." -e ".$mail." -p ".$sn." -n ".$devname."_".$mac." -q 'what is your e-mail?' -a ".$mail." -y ".$language." -z ".$wan_mac;
		setattr("/runtime/netstar/run", "get", $command);
		$res = get("x", "/runtime/netstar/run");
		del("/runtime/netstar/run");
	}
	else
	{
		$res = "Request fail";
	}
	
	if($res=="Correct"||$res=="Duplicated E-Mail and Password")
	{
		$history =  query("/runtime/netstar/user/mac");
	
		while($history!="")
		{
			del("/runtime/netstar/user");
			$history =  query("/runtime/netstar/user/mac");
		}
	}
	else
	{
		if($old_mail!=null||$old_mail!=""||$old_mail!="undefined")
		{
			set("/security/netstar/email", $old_mail);
			set("/security/netstar/password", $old_sn);
		}
		else
		{
			set("/security/netstar/email", "");
			set("/security/netstar/password", "");
		}
	}
	
	$com2 = "service PARENTCTRL restart";
	setattr("/runtime/netstar/com", "get", $com2);
	$start = get("x", "/runtime/netstar/com");
	del("/runtime/netstar/com");
	event("DBSAVE");

}
else if ($_POST["act"] == "RegisterDevice")
{
	$add= $_POST["add"];
	$del= $_POST["del"];
	$mail = $_POST["email"];
	$sn = query("/security/netstar/password");//$_POST["password"];
	$ver = query("/runtime/device/firmwareversion");
	$loc = $_POST["location"];
	$cp_server1 = query("/runtime/netstar/cp_server:1/url");
	$cp_server2 = query("/runtime/netstar/cp_server:2/url");

	set("/security/netstar/email", $mail);
	set("/security/netstar/enable", "1" );
	set("/security/netstar/location", $loc );
	
	if($del==null || $del=="")
	{
		$command="cp_device_process -s ".$cp_server1." -x ".$cp_server2." -f ".$ver." -e ".$mail." -p ".$sn." -a ".$add." -z ".$wan_mac;
	}
	else if($add==null || $add=="")
	{
		$command="cp_device_process -s ".$cp_server1." -x ".$cp_server2." -f ".$ver." -e ".$mail." -p ".$sn." -d ".$del." -z ".$wan_mac;
	}
	else
	{
		$command="cp_device_process -s ".$cp_server1." -x ".$cp_server2." -f ".$ver." -e ".$mail." -p ".$sn." -a ".$add." -d ".$del." -z ".$wan_mac;
	}
	if($cp_server1!=""||$cp_server2!="")
	{
		setattr("/runtime/netstar/run2", "get", $command);
		$res = get("x", "/runtime/netstar/run2");
		del("/runtime/netstar/run2");
	
		$com2 = "service PARENTCTRL_UPDATE restart";
		setattr("/runtime/netstar/com2", "get", $com2);
		$start = get("x", "/runtime/netstar/com2");
		del("/runtime/netstar/com2");
		event("DBSAVE");
	}
	else
	{
		$res = "Request fail";
	}
}
else if($_POST["act"] == "Apply")
{
	$enable= $_POST["en"];
	$loc = $_POST["location"];
	$com2 = "service PARENTCTRL restart";
	
	set("/security/netstar/enable", $enable );
	set("/security/netstar/location", $loc );
	
	setattr("/runtime/netstar/com3", "get", $com2);
	$start = get("x", "/runtime/netstar/com3");
	del("/runtime/netstar/com3");
	event("DBSAVE");
}
else if($_POST["act"] == "GetLink")
{
	$res = query("/runtime/netstar/cp_server:1/url");
	$res2 = query("/security/netstar/email");
}
else if($_POST["act"] == "GetFieldData")
{
	$res = query("/security/netstar/location");
	$res2 = query("/security/netstar/email");
	$res3 = query("/security/netstar/password");
}
?>
<result>
	<result1><?=$res?></result1>
	<result2><?=$res2?></result2>
	<result3></result3>
</result>