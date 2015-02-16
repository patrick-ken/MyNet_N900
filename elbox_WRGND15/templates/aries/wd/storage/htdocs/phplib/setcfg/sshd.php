<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
$sshd = XNODE_getpathbytarget("sshd", "entry", "uid", "SSHD-1", 0);
if ($sshd!="")
{
    TRACE_debug("dhcp : (".query($SETCFG_prefix."/entry:1/active").")");
    set("/runtime/sshd/active", query($SETCFG_prefix."/entry:1/active"));
}
?>

