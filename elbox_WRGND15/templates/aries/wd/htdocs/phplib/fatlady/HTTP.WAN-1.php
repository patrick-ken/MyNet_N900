<?
/* fatlady is used to validate the configuration for the specific service.
 * FATLADY_prefix was defined to the path of Session Data.
 * 3 variables should be returned for the result:
 * FATLADY_result, FATLADY_node & FATLADY_message. */
include "/htdocs/phplib/inet.php";
include "/htdocs/phplib/igdentry.php";

function set_result($result, $node, $message)
{
	$_GLOBALS["FATLADY_result"]  = $result;
	$_GLOBALS["FATLADY_node"]    = $node;
	$_GLOBALS["FATLADY_message"] = $message;
}

function check_remote($entry)
{
	$needcheck = query($entry."/UIoption");
	del($entry."/UIoption");
	if($needcheck=="")
	{
	$port = query($entry."/inf/web");
		$retern_msg ="";
	if ($port != "")
	{
		if (isdigit($port)!="1")
		{
			set_result("FAILED", $entry."/inf/web", i18n("Invalid port number"));
			return 0;
		}
		if ($port<1 || $port>65535)
		{
			set_result("FAILED", $entry."/inf/web", i18n("Invalid port range"));
			return 0;
		}
			$retern_msg = PortUsingCheck("TCP",$port,$port,"Remote");
			$pos = strstr($retern_msg,"Conflict:");
			if($pos!="")
			{
				set_result("FAILED", $entry."/inf/web", $retern_msg );
				return 0;
			}
	}

	$port = query($entry."/inf/https_rport");
	if ($port != "")
	{
		if (isdigit($port)!="1")
		{
			set_result("FAILED", $entry."/inf/https_rport", i18n("Invalid port number"));
			return 0;
		}
		if ($port<1 || $port>65535)
		{
			set_result("FAILED", $entry."/inf/https_rport", i18n("Invalid port range"));
			return 0;
		}
			$retern_msg = PortUsingCheck("TCP",$port,$port,"Remote");
			$pos = strstr($retern_msg,"Conflict:");
			if($pos!="")
			{
				set_result("FAILED", $entry."/inf/web", $retern_msg );
				return 0;
			}
	}
	$host = query($entry."/inf/weballow/hostv4ip");
	if ($host != "")
	{
		if (INET_validv4addr($host)!="1")
		{
			set_result("FAILED", $entry."/inf/weballow/hostv4ip", i18n("Invalid host IP address"));
			return 0;
		}
	}
		set("/runtime/upnpigd/conflict","0");
	}
	else
	{
		set($infp."/inbfilter", query($SETCFG_prefix."/inf/inbfilter"));
		if($needcheck=="1")
			set("/runtime/upnpigd/conflict","0");
		else if($needcheck=="2")
			set("/runtime/upnpigd/conflict","1");
	}
	set_result("OK", "", "");
	return 1;
}

if (check_remote($FATLADY_prefix)=="1") set($FATLADY_prefix."/valid", "1");
?>
