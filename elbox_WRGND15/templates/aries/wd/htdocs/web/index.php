<?
/* Determine auto login or not to run the set up wizard. */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/wifi.php";
include "/htdocs/phplib/inet.php";
$wifip_24g = WIFI_getpathbyphyinf("BAND24G-1.1");
$wifip_5g = WIFI_getpathbyphyinf("BAND5G-1.1");
$ssid_24g = query($wifip_24g."/ssid");
$ssid_5g = query($wifip_5g."/ssid");
$ssid_24g_default = query($wifip_24g."/defaultssid");
$ssid_5g_default = query($wifip_5g."/defaultssid");
$authtype_24g = query($wifip_24g."/authtype");
$authtype_5g = query($wifip_5g."/authtype");
$password_path = XNODE_getpathbytarget("/device/account", "entry", "name", "admin", 0);
$password = query($password_path."/password");
$inetp_wan1 = INET_getpathbyinf("WAN-1");
if(query($inetp_wan1."/addrtype")=="ipv4" && query($inetp_wan1."/ipv4/static")=="0") $wan_type = "DHCP";
else $wan_type = "";
$layout	= query("/device/layout");
$wizard_config24G = query($wifip_24g."/wps/configured");
$wizard_config5G = query($wifip_5g."/wps/configured");
if($ssid_24g==$ssid_24g_default && $ssid_5g==$ssid_5g_default && $authtype_24g=="OPEN" && $authtype_5g=="OPEN" &&
	$password=="password" && $wan_type=="DHCP" && $layout=="router" && $wizard_config24G!="1" && $wizard_config5G!="1") $_GLOBALS["AUTO_LOGIN"]="1";

if($_GLOBALS["AUTO_LOGIN"]=="1") dophp("load", "/htdocs/web/main_internet.php");
else dophp("load", "/htdocs/web/main_dashboard.php");
?>
