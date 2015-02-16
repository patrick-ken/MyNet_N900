<?
if ($AUTHORIZED_GROUP >= 0)
{
	$serial_number=query("/runtime/devdata/sn");
	if($_POST["optin"]=="") $optin="0";
	else $optin=$_POST["optin"];
	setattr("/runtime/register", "get", "urlget get 'https://websupport.wdc.com/app/registration/register.asmx/RegisterProduct?fn=".$_POST["fn"]."&ln=".$_POST["ln"]."&e=".$_POST["e"]."&sn=".$serial_number."&lang=eng&cc=US&optin=".$optin."&os=&mac=' > /var/register");
	get("x", "/runtime/register");
	echo fread("", "/var/register");
	setattr("/runtime/register/delete", "get", "rm /var/register");
	get("x", "/runtime/register/delete");
	del("/runtime/register");
}
?>