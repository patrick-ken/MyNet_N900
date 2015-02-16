HTTP/1.1 200 OK
Content-Type: text/html

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?
include "/htdocs/phplib/langpack.php";
fwrite("w", "/dev/console", "AUTHORIZED_GROUP=".$AUTHORIZED_GROUP."\n");
if (/*$AUTHORIZED_GROUP>=0 &&*/ $AUTHORIZED_GROUP<100)
{
	if	($_POST["multilanguage"]=="id")			$lcode="id";
	else if	($_POST["multilanguage"]=="ms")		$lcode="ms";
	else if	($_POST["multilanguage"]=="ca")		$lcode="ca";
	else if	($_POST["multilanguage"]=="cs")		$lcode="cs";
	else if	($_POST["multilanguage"]=="da")		$lcode="da";
	else if	($_POST["multilanguage"]=="de")		$lcode="de";
	else if	($_POST["multilanguage"]=="et")		$lcode="et";
	else if	($_POST["multilanguage"]=="engb")	$lcode="engb";
	else if	($_POST["multilanguage"]=="en")		$lcode="en";
	else if	($_POST["multilanguage"]=="es")		$lcode="es";
	else if	($_POST["multilanguage"]=="eu")		$lcode="eu";
	else if	($_POST["multilanguage"]=="tl")		$lcode="tl";
	else if	($_POST["multilanguage"]=="freu")	$lcode="freu";
	else if	($_POST["multilanguage"]=="fr")		$lcode="fr";
	else if	($_POST["multilanguage"]=="hr")		$lcode="hr";
	else if	($_POST["multilanguage"]=="it")		$lcode="it";
	else if	($_POST["multilanguage"]=="is")		$lcode="is";
	else if	($_POST["multilanguage"]=="sw")		$lcode="sw";
	else if	($_POST["multilanguage"]=="lv")		$lcode="lv";
	else if	($_POST["multilanguage"]=="lt")		$lcode="lt";
	else if	($_POST["multilanguage"]=="hu")		$lcode="hu";
	else if	($_POST["multilanguage"]=="nl")		$lcode="nl";
	else if	($_POST["multilanguage"]=="no")		$lcode="no";
	else if	($_POST["multilanguage"]=="pl")		$lcode="pl";
	else if	($_POST["multilanguage"]=="pt")		$lcode="pt";
	else if	($_POST["multilanguage"]=="ro")		$lcode="ro";
	else if	($_POST["multilanguage"]=="sk")		$lcode="sk";
	else if	($_POST["multilanguage"]=="sl")		$lcode="sl";
	else if	($_POST["multilanguage"]=="fi")		$lcode="fi";
	else if	($_POST["multilanguage"]=="sv")		$lcode="sv";
	else if	($_POST["multilanguage"]=="vi")		$lcode="vi";
	else if	($_POST["multilanguage"]=="tr")		$lcode="tr";
	else if	($_POST["multilanguage"]=="el")		$lcode="el";
	else if	($_POST["multilanguage"]=="ru")		$lcode="ru";
	else if	($_POST["multilanguage"]=="sr")		$lcode="sr";
	else if	($_POST["multilanguage"]=="uk")		$lcode="uk";
	else if	($_POST["multilanguage"]=="bg")		$lcode="bg";
	else if	($_POST["multilanguage"]=="iw")		$lcode="iw";
	else if	($_POST["multilanguage"]=="ar")		$lcode="ar";
	else if	($_POST["multilanguage"]=="ur")		$lcode="ur";
	else if	($_POST["multilanguage"]=="mr")		$lcode="mr";
	else if	($_POST["multilanguage"]=="hi")		$lcode="hi";
	else if	($_POST["multilanguage"]=="bn")		$lcode="bn";
	else if	($_POST["multilanguage"]=="gu")		$lcode="gu";
	else if	($_POST["multilanguage"]=="or")		$lcode="or";
	else if	($_POST["multilanguage"]=="ta")		$lcode="ta";
	else if	($_POST["multilanguage"]=="te")		$lcode="te";
	else if	($_POST["multilanguage"]=="kn")		$lcode="kn";
	else if	($_POST["multilanguage"]=="ml")		$lcode="ml";
	else if	($_POST["multilanguage"]=="th")		$lcode="th";
	else if	($_POST["multilanguage"]=="am")		$lcode="am";
	else if	($_POST["multilanguage"]=="zhtw")	$lcode="zhtw";
	else if	($_POST["multilanguage"]=="zhcn")	$lcode="zhcn";
	else if	($_POST["multilanguage"]=="ja")		$lcode="ja";
	else if	($_POST["multilanguage"]=="ko")		$lcode="ko";
	else if	($_POST["multilanguage"]=="ptbr")		$lcode="ptbr";//Fixed WD ITR 43549
	else $lcode="auto";

	set("/device/features/language", $lcode);
	LANGPACK_setsealpac();
	event("DBSAVE");
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
</html>
