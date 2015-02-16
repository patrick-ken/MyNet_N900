<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/setcfg/libs/wifi.php";
include "/htdocs/phplib/wifi.php";//for get wifi config use
wifi_setcfg($SETCFG_prefix);
set("/device/radio24gonoff",query($SETCFG_prefix."/device/radio24gonoff"));
$wifip_24g = WIFI_getpathbyphyinf("BAND24G-1.1");
$wifip_5g = WIFI_getpathbyphyinf("BAND5G-1.1");
set("/device/wizardconfig", "1");//Set the wizard config if the setup wizard is finished in WD product.
set($wifip_24g."/wps/configured", "1");//Set the wizard config if the setup wizard is finished in WD product.
set($wifip_5g."/wps/configured", "1");//Set the wizard config if the setup wizard is finished in WD product.
?>
