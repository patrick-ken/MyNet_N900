<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$name = "LAN-1";
$infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
$upnp = query($infp."/upnp/count");
if ($upnp > 0) {
	$base = "/runtime/upnpigd/portmapping";
    
    foreach($base."/entry")
    {
		if(query("externalport") == $EXTERNALPORT && query("description") == $DEST)
		{
		    $igd_stsp = $base."/entry:".$InDeX;
			$index    = $InDeX;
			break;
		}
	}
	//TRACE_debug("Remove: ".$igd_stsp);
	if ($igd_stsp != "") {
		del($igd_stsp);
		$cnt = query($base."/count");
		$cnt--;
		set($base."/count", $cnt);
		$seqno = query($base."/seqno");
		$seqno++;
		set($base."/seqno", $seqno);
		
        foreach($base."/entry")
		{
			if($InDeX >= $index)
			set($base."/entry:".$InDeX."/uid", "PORTMAP-".$InDeX);
		}
	}
}
?>
