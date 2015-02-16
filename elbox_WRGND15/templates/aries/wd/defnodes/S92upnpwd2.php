<? /* vi: set sw=4 ts=4: */
$vendor     = query("/runtime/device/vendor");
$model      = query("/runtime/device/modelname");
$url        = query("/runtime/device/producturl");
$modeldesc  = query("/runtime/device/description");
$sn         = query("/runtime/device/serial_number");/* Modified from "None" to sn, by Argus, 130117*/
$ver        = query("/runtime/device/firmwareversion");
$hostname   = query("/device/hostname");    // michael_lee
$Genericname = query("/runtime/device/upnpmodelname");
if($Genericname == ""){ $Genericname = $model; }

if($model == "MyNetN600"){
    $model_url  = "http://products.wdc.com/MyNetN600";
}
else if($model == "MyNetN750"){
	$model_url  = "http://products.wdc.com/MyNetN750";
}
else if($model == "MyNetN900"){
    $model_url  = "http://products.wdc.com/MyNetN900";
}
else if($model == "MyNetN900C"){
    $model_url = "http://products.wdc.com/MyNetN900Central";
}
else if($model == "MyNetAC1800"){
	$model_url = "http://products.wdc.com/MyNetAC1800";
}
/* find out the root device path. */
$pbase      = "/runtime/upnp/dev";
$i          = query($pbase."#") + 1;
$dev_root   = $pbase.":".$i;
$dtype      = "urn:schemas-upnp-org:device:WDRouter:2";     // Added by Jerry Kao.

/********************************************************************/
/* root device: WFADevice */
/* create $dev_root */
set($dev_root, "");                     anchor($dev_root);

/* set extension nodes. */
setattr("mac",  "get", "devdata get -e wanmac");
$dtype2    = "urn:schemas-upnp-org:device:WDRouter:1"; 
setattr("guid", "get", "genuuid -s \"".$dtype2."\" -m \"".query("mac")."\"");
$udn = "uuid:".query("guid");

/* set WDRouter nodes. */
set("UDN",              $udn);
set("deviceType",       $dtype);
set("port",             "49152");
set("location",         "WDRouter.xml");
set("maxage",           "1800");        // 100
set("server",           "Linux, UPnP/1.0, ".$model." Ver ".$ver);

/* set the description file names */
add("xmldoc",           "WDRouter.xml");

/********************************************************************/
/* set the device description nodes */
$desc_root = $dev_root."/devdesc";

/* devdesc/specVersion */
set($desc_root."/specVersion",  "");
anchor($desc_root."/specVersion");
set("major",                "1");
set("minor",                "0");

/* devdesc/URLBase */
set($desc_root."/URLBase",      "");

/* devdesc/device */
set($desc_root."/device",       "");    anchor($desc_root."/device");
set("modelURL",             $model_url);
set("deviceType",           $dtype);
set("friendlyName",         $hostname);
set("manufacturer",         $vendor);
set("manufacturerURL",      $url);
set("modelDescription",     $modeldesc);
set("modelName",            $model);
set("modelNumber",          $model);
set("serialNumber",         $sn);
set("UDN",                  $udn);

/* devdesc/device/serviceList */
$sub_root = $desc_root."/device/serviceList/service:1";
set($sub_root, "");
anchor($sub_root);
set("serviceType",          "urn:schemas-microsoft-com:service:OSInfo:1");      // NULL:1
set("serviceId",            "urn:microsoft-com:serviceId:OSInfo1");
set("controlURL",           "/soap.cgi?service=OSInfo1");
set("eventSubURL",          "/gena.cgi?service=OSInfo1");
set("SCPDURL",              "/OSInfo.xml");

/* devdesc/device/presentationURL */
/* We keep the 'presentationURL' & 'URLBase' empty here,
/* and set the real value in when 'elbox/progs.template/htdocs/upnpdevdesc/WDRouter.xml.php' is called. */
set($desc_root."/device/presentationURL","");
?>
