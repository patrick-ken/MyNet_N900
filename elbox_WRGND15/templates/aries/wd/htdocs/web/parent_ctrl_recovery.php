HTTP/1.1 200 OK
Content-Type: text/xml

<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}  

if ($AUTHORIZED_GROUP < 0)
{
	$res = "Authenication fail";
}
else if ($_POST["act"] == "Recovery")
{
	$validmail = $_POST["validemail"];
	
	if($validmail != "" && $validmail != null && $validmail != "undefined")
		set("/security/netstar/email", $validmail);
	else
		set("/security/netstar/email", "");
	event("DBSAVE");
}
else if($_POST["act"] == "KillHistoryNode")
{
	$history =  query("/runtime/netstar/user/mac");
	
	while($history!="")
	{
		del("/runtime/netstar/user");
		$history =  query("/runtime/netstar/user/mac");
	}
}
?>
<result><?=$res?></result>