<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/xnode.php";
$have_configured = 0;/* 0: not yet configured 1: have configured */
$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
if ($inf!="")
{
	$inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
	if ($inet!="")
	{
		if (query($SETCFG_prefix."/wifi:1/ssid")!="")
		{
			set($inet."/ssid", query($SETCFG_prefix."/wifi:1/ssid"));
		}
		if (query($SETCFG_prefix."/wifi:1/nwkey/psk/key")!="")
		{
			set($inet."/authtype", "WPA+2PSK");
			set($inet."/encrtype", "AES");
			set($inet."/nwkey/psk/passphrase","1");
			set($inet."/nwkey/psk/key", query($SETCFG_prefix."/wifi:1/nwkey/psk/key"));
		}

		if (query($SETCFG_prefix."/wifi:1/ssid")!="" || query($SETCFG_prefix."/wifi:1/nwkey/psk/key")!="")
		{
			set($inet."/wps/configured", "1");
			$inf5g = XNODE_getpathbytarget("", "wifi/entry", "uid", "WIFI-3", 0);
            if ($inf5g!="")
            {
                set($inf5g."/wps/configured", "1");
            }
            $have_configured = 1;
		}
	}
}

$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
if ($inf!="")
{
	$inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
	if ($inet!="")
	{
		if (query($SETCFG_prefix."/wifi:2/ssid")!="")
		{
			set($inet."/ssid", query($SETCFG_prefix."/wifi:2/ssid"));
		}
		if (query($SETCFG_prefix."/wifi:2/nwkey/psk/key")!="")
		{
			set($inet."/authtype", "WPA+2PSK");
			set($inet."/encrtype", "AES");
			set($inet."/nwkey/psk/passphrase","1");
			set($inet."/nwkey/psk/key", query($SETCFG_prefix."/wifi:2/nwkey/psk/key"));
		}

        if (query($SETCFG_prefix."/wifi:2/ssid")!="" || query($SETCFG_prefix."/wifi:2/nwkey/psk/key")!="")
        {
            set($inet."/wps/configured", "1");
            $inf24g = XNODE_getpathbytarget("", "wifi/entry", "uid", "WIFI-1", 0);
            if ($inf24g!="")
            {
                set($inf24g."/wps/configured", "1");
            }
            $have_configured = 1;
        }
	}
}
if($have_configured==1) set("/device/wizardconfig","1");
else set("/device/wizardconfig","0");
?>
