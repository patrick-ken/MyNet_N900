<? /* vi: set sw=4 ts=4: */
/* The menu definitions */
$layout	= query("/device/layout");

if ($TEMP_MYGROUP=="adv_admin")
{
	$menu = i18n("Administrator").	"|".
			i18n("Time Settings").	"|".
			i18n("Firmware Update"). "|".
			i18n("System").			"|".
			i18n("Connection Check")."|".
			i18n("Registration").	"|".			
			i18n("Log"). "|". 
			i18n("Language"). "|". 
			i18n("Device Mode");
			
	$link = "tools_admin.php".		"|".
			"tools_time.php".		"|".
			"tools_fwup.php".  		"|".
			"tools_system.php".		"|".
			"tools_check.php".      "|".
			"register.php".      	"|".			
			"tools_syslog.php".			"|".
			"tools_language.php".		"|".
			"tools_devmode.php";
}
else if ($layout=="router" && $TEMP_MYGROUP=="adv_wan")
{
	$menu = i18n("Internet Status").		"|".
			i18n("Internet Setup").			"|".
			i18n("IPv6 Status").			"|".
			i18n("IPv6").					"|".
			i18n("Dynamic DNS");
	$link = "wan_status.php".				"|".
			"wan.php".						"|".
			"adv_ipv6_status.php".			"|".
			"adv_ipv6.php".					"|".
			"tools_ddns.php";
}
else if ($layout=="router" && $TEMP_MYGROUP=="adv_lan")
{
	$menu = i18n("Host and LAN Settings").		"|".
			i18n("DHCP Server Settings").		"|".
			i18n("Device and Client Tables");
	$link = "lan.php".						"|".
			"lan_server.php".				"|".
			"lan_client.php";
}
else if ($layout=="bridge" && $TEMP_MYGROUP=="adv_lan")
{
	$menu =	i18n("Connection Status").		"|".
			i18n("Host and LAN Settings");
	$link =	"ap_status.php".				"|".
			"lan.php";
}
else if ($layout=="router" && $TEMP_MYGROUP=="adv_wlan")
{
	$menu = i18n("Wireless Setup").	"|".
			"Wi-Fi Protected Setup".		"|".
			i18n("Guest Access");
	$link = "wlan.php".						"|".
			"wlan_wps.php".					"|".
			"wlan_gz.php";
}
else if ($layout=="bridge" && $TEMP_MYGROUP=="adv_wlan")
{
	$menu = i18n("Wireless Setup"). "|".
			"Wi-Fi Protected Setup";
	$link = "wlan.php".                     "|".
			"wlan_wps.php";

}
else if ($TEMP_MYGROUP=="adv_storage")
{
	$menu = i18n("Storage").			"|".
			i18n("Safely Remove").		"|".
			i18n("Format").				"|".
			i18n("AFP Server");
	$link = "storage.php".				"|".
			"storage_remove.php".		"|".
			"storage_format.php".		"|".
			"adv_afp.php";
}
else if ($layout=="router" && $TEMP_MYGROUP=="adv_secure")
{
	$menu = i18n("Firewall").			"|".
			i18n("DMZ").				"|".
			i18n("MAC Filter").			"|".
			i18n("Parental Controls");
	$link = "adv_firewall.php".			"|".
			"adv_dmz.php".				"|".
			"adv_mac_filter.php".		"|".
			"parent_ctrl.php";
}
else if ($TEMP_MYGROUP=="adv_remote")
{
	$menu = i18n("WD 2go Setup").      "|".
			i18n("Mobile Access").      "|".
			i18n("Web Access");
	$link = "adv_remote.php".             "|".
			"adv_mobile.php".             "|".
			"adv_web.php";
}
else if ($layout=="router" && $TEMP_MYGROUP=="adv_add")
{
	$menu = i18n("Port Forwarding").	"|".
			i18n("ALG").				"|".
			i18n("Routing").			"|".
			i18n("FasTrack Plus QoS").	"|".
			i18n("Enhanced WMM").		"|".
			i18n("Network UPnP");
	$link = "adv_pfwd.php".				"|".
			"adv_alg.php".				"|".
			"adv_routing.php".			"|".
			"adv_qos.php".				"|".
			"adv_qos_wish.php".			"|".
			"adv_upnp.php";
}
?>
