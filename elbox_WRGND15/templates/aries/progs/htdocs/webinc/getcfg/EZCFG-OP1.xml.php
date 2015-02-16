<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<wifi>
<uid>BAND24G-1.1</uid>
<?
$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
if ($inf!="")
{
        $inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
        if ($inet!="")
        {
                echo "<ssid>".dump(0, $inet."/ssid")."</ssid>\n";
		echo "<nwkey>\n";
		echo "<psk>\n";
                echo "<key>".dump(0, $inet."/nwkey/psk/key")."</key>\n";
		echo "</psk>\n";
		echo "</nwkey>";
        }
}
?>
</wifi>
<wifi>
<uid>BAND5G-1.1</uid>
<?
$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
if ($inf!="")
{
        $inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
        if ($inet!="")
        {
                echo "<ssid>".dump(0, $inet."/ssid")."</ssid>\n";
                echo "<nwkey>\n";
                echo "<psk>\n";
                echo "<key>".dump(0, $inet."/nwkey/psk/key")."</key>\n";
                echo "</psk>\n";
                echo "</nwkey>";
        }
}
?>
</wifi>
</module>
