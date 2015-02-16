<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
if(query("/runtime/devdata/countrycode")=="RU")
{
	$bwcp = XNODE_getpathbytarget("/bwc", "entry", "uid", "BWC-1", 0);
	if ($bwcp!="")
	{
		if(query($bwcp."/user_define")!="1" && query($bwcp."/autobandwidth")=="1")
		{
			set($bwcp."/autobandwidth", "0");
			set($bwcp."/bandwidth", "RUSSIA");
		}
	}
}
?>
