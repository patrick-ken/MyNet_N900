<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

fwrite("w", $START, "");
fwrite("w", $STOP, "");

$hostname_fixed = "WDRouter";
$layout = query("/device/layout");
if ($layout=="bridge")
{
	$hostname_fixed = "WDAp";
}
	
$HOSTNAME = query("/device/hostname");
$INF = "br0";
$MDNS_CONF   = "/var/rendezvous.conf";
$dtype="mdnsresponderUDN";
fwrite("w", $MDNS_CONF, "");
//setattr("/device/mdnsresponder/guid", "get", "genuuid -s \"".$dtype."\" -m \"".query("mac")."\""); 
setattr("/runtime/services/mdnsresponder/guid", "get", "genuuid -s \"".$dtype."\" -m \"".query("/runtime/devdata/lanmac")."\""); 
if (query("/device/mdnsresponder/enable")=="1")
{
	foreach ("/runtime/services/mdnsresponder/server")
	{
		if(strstr(query("uid"), "MDNSRESPONDER")!="")
		{
			if ($InDeX > 1) { fwrite("a", $MDNS_CONF, "\n"); }
			//fwrite("a", $MDNS_CONF, query("srvname")."\n");
			fwrite("a", $MDNS_CONF, query("/device/hostname")."\n");
			fwrite("a", $MDNS_CONF, query("srvcfg")."\n");
			fwrite("a", $MDNS_CONF, query("port")."\n");
			/*for wd special*/
			if(query("srvcfg") == "_http._tcp local.")
			{
				fwrite("a", $MDNS_CONF, "TXTVersion=1.1\n");
				fwrite("a", $MDNS_CONF, "UDN=".query("/runtime/services/mdnsresponder/guid")."\n");
				fwrite("a", $MDNS_CONF, "Vendor=WDC\n");
				fwrite("a", $MDNS_CONF, "manufacturer=".query("/runtime/device/vendor")."\n");
				fwrite("a", $MDNS_CONF, "modelDescription=".query("/runtime/device/description")."\n");
				fwrite("a", $MDNS_CONF, "modelName=".query("/runtime/device/modelname")."\n");
				fwrite("a", $MDNS_CONF, "modelNumber=".query("/runtime/device/modelname")."\n");
				fwrite("a", $MDNS_CONF, "modelURL=".query("/runtime/device/producturl")."\n");
				fwrite("a", $MDNS_CONF, "serialNumber=".query("/runtime/device/serial_number")."\n");
			}
	  	}
	}
	fwrite("a", $START, "echo \"mdnsresponder server start !\" > /dev/console\n");
	fwrite("a", $START, "hostname ".$HOSTNAME."\n");
	fwrite("a", $START, "mDNSResponderPosix -b -i ".$INF." -e ".$hostname_fixed." -f ".$MDNS_CONF."\n");
	fwrite("a", $STOP, "echo \"mdnsresponder server stop !\" > /dev/console\n");
	fwrite("a", $STOP, "killall -9 mDNSResponderPosix\n");
}
else
{
	fwrite("a", $START, "echo \"mdnsresponder server is disabled !\" > /dev/console\n");
	fwrite("a", $STOP, "echo \"mdnsresponder server is disabled !\" > /dev/console\n");
}

?>