<?
include "/htdocs/phplib/xnode.php";
TRACE_debug("ssid : ".query($SETCFG_prefix."/ezcfg/sitesurvey/ssid"));
if (query($SETCFG_prefix."/ezcfg/sitesurvey/ssid")!="")
{
    set("/ezcfg/sitesurvey/ssid", query($SETCFG_prefix."/ezcfg/sitesurvey/ssid"));
	TRACE_debug("enable_24g : ".query($SETCFG_prefix."/ezcfg/sitesurvey/enable_24g"));
	TRACE_debug("enable_5g : ".query($SETCFG_prefix."/ezcfg/sitesurvey/enable_5g"));
	if (query("/ezcfg/sitesurvey/enable_24g")=="1" || query($SETCFG_prefix."/ezcfg/sitesurvey/enable_24g")=="1")
	{
    	$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND24G-1.1", 0);
	}
	else if (query("/ezcfg/sitesurvey/enable_5g")=="1" || query($SETCFG_prefix."/ezcfg/sitesurvey/enable_5g")=="1")
	{
		$inf = XNODE_getpathbytarget("", "phyinf", "uid", "BAND5G-1.1", 0);
	}
	TRACE_debug("ALEX: ".$inf);
    if ($inf!="")
    {
		set($inf."/active", "1");
        $inet = XNODE_getpathbytarget("", "wifi/entry", "uid", query($inf."/wifi"), 0);
		TRACE_debug("ALEX: ".$inet);
        if ($inet!="")
        {
            if (query($SETCFG_prefix."/ezcfg/sitesurvey/ssid")!="")
            {
                set($inet."/ssid", query($SETCFG_prefix."/ezcfg/sitesurvey/ssid"));
            }
			
			TRACE_debug("network_key : ".query("/ezcfg/sitesurvey/network_key"));
            if (query($SETCFG_prefix."/ezcfg/sitesurvey/network_key")!="")
            {
				set($inet."/nwkey/psk/passphrase", "1");
                set($inet."/nwkey/psk/key", query($SETCFG_prefix."/ezcfg/sitesurvey/network_key"));
            }
			TRACE_debug("security : (".query($SETCFG_prefix."/ezcfg/sitesurvey/security").")");
			TRACE_debug("type : ".query($SETCFG_prefix."/ezcfg/sitesurvey/type"));
			TRACE_debug("wps : ".query($SETCFG_prefix."/ezcfg/sitesurvey/wps"));
            if (query($SETCFG_prefix."/ezcfg/sitesurvey/security")=="enabled")
            {
				if (query($SETCFG_prefix."/ezcfg/sitesurvey/type")=="WPA")
				{
                	set($inet."/authtype", "WPA+2PSK");
					set($inet."/encrtype", "AES");
				}
				else if (query($SETCFG_prefix."/ezcfg/sitesurvey/type")=="WPA/WPA2")
				{
					set($inet."/authtype", "WPA+2PSK");
					set($inet."/encrtype", "AES");
				}
				else if (query($SETCFG_prefix."/ezcfg/sitesurvey/type")=="WPA2")
				{
					set($inet."/authtype", "WPA2PSK");
					set($inet."/encrtype", "AES");
				}
				else
				{
					set($inet."/authtype", "WEPAUTO");
					set($inet."/encrtype", "WEP");
				}
            }
			else
			{
				set($inet."/authtype", "OPEN");
				set($inet."/encrtype", "NONE");
			}
            if (query($SETCFG_prefix."/ezcfg/sitesurvey/wps")!="")
            {
                set($inet."/wps/enable", query($SETCFG_prefix."/ezcfg/sitesurvey/wps"));
				
            }
			$inf24g = XNODE_getpathbytarget("", "wifi/entry", "uid", "WIFI-1", 0);
			if ($inf24g!="")
			{
				set($inf24g."/wps/configured", "1");
			}
			$inf5g = XNODE_getpathbytarget("", "wifi/entry", "uid", "WIFI-3", 0);
			if ($inf5g!="")
			{
				set($inf5g."/wps/configured", "1");
			}
        }
    }
}
else
{
	del("/ezcfg");
    if (query($SETCFG_prefix."/ezcfg/sitesurvey/enable_24g")=="1")
    {
        set("/ezcfg/sitesurvey/enable_24g", "1");
		set("/ezcfg/sitesurvey/enable_5g", "0");
    }
    else if (query($SETCFG_prefix."/ezcfg/sitesurvey/enable_5g")=="1")
    {
		set("/ezcfg/sitesurvey/enable_24g", "0");
        set("/ezcfg/sitesurvey/enable_5g", "1");
    }
}
?>
