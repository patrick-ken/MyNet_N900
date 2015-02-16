<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$share_path    = "/var/tmp/dlna_share";
$dms_descr_xml = "/var/dms_descr.xml";
$hostname      = query("/device/hostname");
$model         = query("/runtime/device/modelname");
$vendor        = query("/runtime/device/vendor");
$url           = query("/runtime/device/producturl");
$sn            = query("/runtime/device/serial_number");
$Genericname   = query("/runtime/device/upnpmodelname");

if ($Genericname == "") {$Genericname = $model;}

if ($model == "MyNetN600") {
	$model_url = "http://products.wdc.com/MyNetN600";
	$model_num = "600";
	$modelid  = "02BF98D2-1B50-4a1f-83D9-A85402782497";
}
else if ($model == "MyNetN750") {
	$model_url = "http://products.wdc.com/MyNetN750";
	$model_num = "750";
	$modelid  = "02BF98D2-1B50-4a1f-83D9-A85402782498";
}
else if ($model == "MyNetN900") {
	$model_url = "http://products.wdc.com/MyNetN900";
	$model_num = "900&amp;REV_01";
	$modelid  = "02BF98D2-1B50-4a1f-83D9-A85402782499";
}
else if ($model == "MyNetN900C") {
	$model_url = "http://products.wdc.com/MyNetN900Central";
	$model_num = "901&amp;REV_01";
	$modelid  = "02BF98D2-1B50-4a1f-83D9-A8540278249A";
}
else if ($model == "MyNetAC1800") {
	$model_url = "http://products.wdc.com/MyNetAC1800";
	$model_num = "1800&amp;REV_01 VEN_0033&amp;DEV_0001";
}


$major = query("/runtime/upnp/dev/devdesc/specVersion/major");
$minor = query("/runtime/upnp/dev/devdesc/specVersion/minor");
/* set extension nodes. */
//we need a fixed uuid for DLNA services (tom, 20120129)
setattr("/runtime/services/dlna/guid", "get", "genuuid -s \"dlnaUDN\" -m \"".query("/runtime/devdata/lanmac")."\"");
$udn = tolower(query("/runtime/services/dlna/guid"));

fwrite("w",$dms_descr_xml, "\<\?xml version=\"1.0\" encoding=\"utf-8\"\?\>\n");
fwrite("a",$dms_descr_xml, "<root xmlns=\"urn:schemas-upnp-org:device-1-0\" xmlns:pnpx=\"http://schemas.microsoft.com/windows/pnpx/2005/11\" xmlns:df=\"http://schemas.microsoft.com/windows/2008/09/devicefoundation\">\n");
fwrite("a",$dms_descr_xml, "<specVersion>\n");
fwrite("a",$dms_descr_xml, "<major>".$major."</major>\n");
fwrite("a",$dms_descr_xml, "<minor>".$minor."</minor>\n");
fwrite("a",$dms_descr_xml, "</specVersion>\n");
fwrite("a",$dms_descr_xml, "<device>\n");
fwrite("a",$dms_descr_xml, "<pnpx:X_hardwareId>VEN_1058&amp;DEV_0".$model_num."</pnpx:X_hardwareId>\n");
fwrite("a",$dms_descr_xml, "<pnpx:X_deviceCategory>MediaDevices</pnpx:X_deviceCategory>\n");
fwrite("a",$dms_descr_xml, "<pnpx:X_compatibleId>MS_DigitalMediaDeviceClass_DMS_V001</pnpx:X_compatibleId>\n");
fwrite("a",$dms_descr_xml, "<df:X_deviceCategory>Multimedia.DMS</df:X_deviceCategory>\n");
fwrite("a",$dms_descr_xml, "<df:X_modelId>".$modelid."</df:X_modelId>\n");
fwrite("a",$dms_descr_xml, "<friendlyName>".$hostname."</friendlyName>\n");
fwrite("a",$dms_descr_xml, "<manufacturer>".$vendor."</manufacturer>\n");
fwrite("a",$dms_descr_xml, "<manufacturerURL>".$url."</manufacturerURL>\n");
fwrite("a",$dms_descr_xml, "<modelName>".$model."</modelName>\n");
fwrite("a",$dms_descr_xml, "<modelNumber>1.0</modelNumber>\n");
fwrite("a",$dms_descr_xml, "<modelURL>".$model_url."</modelURL>\n");
fwrite("a",$dms_descr_xml, "<UDN>uuid:".$udn."</UDN>\n");
fwrite("a",$dms_descr_xml, "<RUIS_UDN>uuid:".$udn."</RUIS_UDN>\n");
fwrite("a",$dms_descr_xml, "</device>\n");
fwrite("a",$dms_descr_xml, "<extension>\n");
fwrite("a",$dms_descr_xml, "<serialNumber>".$sn."</serialNumber>\n");
fwrite("a",$dms_descr_xml, "<presentationURL>http://127.0.0.1</presentationURL>\n");
fwrite("a",$dms_descr_xml, "<microsoft:magicPacketWakeSupported>0</microsoft:magicPacketWakeSupported>\n");
fwrite("a",$dms_descr_xml, "</extension>\n");
fwrite("a",$dms_descr_xml, "</root>\n");

$access_conf_xml = "/var/access_conf.xml";
$maxhttpsessions = query("/wd/storage/dlna/maxhttpsessions");
if ($maxhttpsessions == "") {$maxhttpsessions = "40";}
$name = "LAN-1";
$stsp = XNODE_getpathbytarget("/runtime", "inf", "uid", $name, 0);
$atype = query($stsp."/inet/addrtype");
if      ($atype=="ipv4") {$ipaddr=query($stsp."/inet/ipv4/ipaddr");}
else if ($atype=="ppp4") {$ipaddr=query($stsp."/inet/ppp4/local");}
else if ($atype=="ipv6") {$ipaddr=query($stsp."/inet/ipv6/ipaddr");}
else if ($atype=="ppp6") {$ipaddr=query($stsp."/inet/ppp6/local");}
if ($ipaddr == "")  {$ipaddr = "192.168.1.1";}

fwrite("w",$access_conf_xml, "\<\?xml version=\"1.0\" encoding=\"utf-8\"\?\>\n");
fwrite("a",$access_conf_xml, "<accessConfigRoot>\n");
fwrite("a",$access_conf_xml, "<dmsConfig>\n");
fwrite("a",$access_conf_xml, "<language>english</language>\n");
fwrite("a",$access_conf_xml, "<refreshFreq>5000</refreshFreq>\n");
fwrite("a",$access_conf_xml, "<setTheSharePRI>1</setTheSharePRI>\n");
fwrite("a",$access_conf_xml, "<enabled>1</enabled>\n");
fwrite("a",$access_conf_xml, "<indexModeManual>0</indexModeManual>\n");
fwrite("a",$access_conf_xml, "<sharePRI>2</sharePRI>\n");
fwrite("a",$access_conf_xml, "<shareMode>1</shareMode>\n");
fwrite("a",$access_conf_xml, "<shareOption>1</shareOption>\n");
fwrite("a",$access_conf_xml, "<defaultThumbnailPath>./xml/icon/DMS-defaultTN.jpg</defaultThumbnailPath>\n");
fwrite("a",$access_conf_xml, "<defaultPictureThumbnailPath>./xml/icon/picture_normal.jpg</defaultPictureThumbnailPath>\n");
fwrite("a",$access_conf_xml, "<defaultMusicThumbnailPath>./xml/icon/music_normal.jpg</defaultMusicThumbnailPath>\n");
fwrite("a",$access_conf_xml, "<defaultVideoThumbnailPath>./xml/icon/video_normal.jpg</defaultVideoThumbnailPath>\n");
fwrite("a",$access_conf_xml, "<storageMessageFifo>/tmp/dms_ipc</storageMessageFifo>\n");
fwrite("a",$access_conf_xml, "<storeEmbeddedAlbumArtsOnDisc>0</storeEmbeddedAlbumArtsOnDisc>\n");
fwrite("a",$access_conf_xml, "<maxHttpSessions>".$maxhttpsessions."</maxHttpSessions>\n");
fwrite("a",$access_conf_xml, "<mpeServerResponseTimeOut>120</mpeServerResponseTimeOut>\n");
fwrite("a",$access_conf_xml, "<contentAggregationEnable>1</contentAggregationEnable>\n");
fwrite("a",$access_conf_xml, "<contentAggregationPath>./DMS/data</contentAggregationPath>\n");
fwrite("a",$access_conf_xml, "<contentAggregationTimeout>900</contentAggregationTimeout>\n");
fwrite("a",$access_conf_xml, "<ssdpNotifyInterval>60</ssdpNotifyInterval>\n");
fwrite("a",$access_conf_xml, "<ssdpNotifyMsgDelay>200</ssdpNotifyMsgDelay>\n");
fwrite("a",$access_conf_xml, "<ssdpNotifyMaxAge>1800</ssdpNotifyMaxAge>\n");
fwrite("a",$access_conf_xml, "<ssdpNotifyMsgCopyCount>2</ssdpNotifyMsgCopyCount>\n");
fwrite("a",$access_conf_xml, "<memCheckMemoryLimit>512</memCheckMemoryLimit>\n");
fwrite("a",$access_conf_xml, "<memCheckRepeatCount>100</memCheckRepeatCount>\n");
fwrite("a",$access_conf_xml, "<webuiPort>5566</webuiPort>\n");
fwrite("a",$access_conf_xml, "<PalDmsUpnpPortMin>2869</PalDmsUpnpPortMin>\n");
fwrite("a",$access_conf_xml, "<MpeServerPort>9978</MpeServerPort>\n");
fwrite("a",$access_conf_xml, "<localDBWAL>0</localDBWAL>\n");
fwrite("a",$access_conf_xml, "<databaseCheckpoint>100</databaseCheckpoint>\n");
fwrite("a",$access_conf_xml, "<PartialIndexingTN>0</PartialIndexingTN>\n");
fwrite("a",$access_conf_xml, "<thumbnailRebuildInterval>20</thumbnailRebuildInterval>\n");
fwrite("a",$access_conf_xml, "<jpegRescale method=\"whole_image\" memory_limit=\"4194304\" />\n");
fwrite("a",$access_conf_xml, "</dmsConfig>\n");
fwrite("a",$access_conf_xml, "<netConfig>\n");
fwrite("a",$access_conf_xml, "<usbMountPath>".$share_path."</usbMountPath>\n");
fwrite("a",$access_conf_xml, "<netInterface>br0</netInterface>\n");
fwrite("a",$access_conf_xml, "</netConfig>\n");
fwrite("a",$access_conf_xml, "<web>\n");
fwrite("a",$access_conf_xml, "<presentationURL>http://".$ipaddr."</presentationURL>\n");
fwrite("a",$access_conf_xml, "<webpageConfig>\n");
fwrite("a",$access_conf_xml, "<maxContents>50</maxContents>\n");
fwrite("a",$access_conf_xml, "<maxContentsPerScreen>50</maxContentsPerScreen>\n");
fwrite("a",$access_conf_xml, "<webServerPort>8080</webServerPort>\n");
fwrite("a",$access_conf_xml, "<maxContentsPerScreen_webui>25</maxContentsPerScreen_webui>\n");
fwrite("a",$access_conf_xml, "<maxContentPerPage>5</maxContentPerPage>\n");
fwrite("a",$access_conf_xml, "<maxContentList>50</maxContentList>\n");
fwrite("a",$access_conf_xml, "</webpageConfig>\n");
fwrite("a",$access_conf_xml, "</web>\n");
fwrite("a",$access_conf_xml, "<usbConfigPublish/>\n");
fwrite("a",$access_conf_xml, "</accessConfigRoot>\n");

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
fwrite("a",$STOP,  "xmldbc -k DLNA_MONITOR\n");

$usb1 = query("/wd/storage/dlna/USB1");
$usb2 = query("/wd/storage/dlna/USB2");
//TRACE_debug("***DLNA*** usb1 = ".$usb1."\n");

function find_shared($usb) {
	foreach ("/runtime/device/storage/disk") {
		if(query("usbport") == $usb) {
			foreach ("entry") {
				$mntpath = query("mntp");
				$mntfolder = scut($mntpath, 0, "/var/tmp/storage/");

				if($mntfolder != "") {
					if(query("state") == "MOUNTED") {
						$START = $_GLOBALS["START"];
						$STOP  = $_GLOBALS["STOP"];
						$share_path = $_GLOBALS["share_path"];
						fwrite("a", $START, "rm -f ".$share_path."/".$mntfolder." 2>/dev/null\n");
						fwrite("a",	$START, "ln -s -n ".$mntpath." ".$share_path."/".$mntfolder." \n");
						fwrite("a",	$START, "sleep 2;\n");
						fwrite("a",	$START, "echo \"add;".$mntfolder.";".$mntfolder.";local;".$share_path."/".$mntfolder.";\" >/tmp/dms_ipc\n");
						fwrite("a",	$START, "if [ ! -e /tmp/dms_ipc ]; then\n");
						fwrite("a", $START, "	if [ \"\$verbose\" = \"1\" ]; then /etc/scripts/dlna_loop.sh -v & else /etc/scripts/dlna_loop.sh & fi\n");
//						fwrite("a",	$START, "	echo \"Creating file /tmp/dms_ipc failure, and the create again !\" > /dev/console\n");
//						fwrite("a", $START, "	mpe_server &\n");
						fwrite("a",	$START, "	sleep 2;\n");
						fwrite("a",	$START, "	echo \"add;".$mntfolder.";".$mntfolder.";local;".$share_path."/".$mntfolder.";\" >/tmp/dms_ipc\n");
						fwrite("a",	$START, "fi\n");
						fwrite("a",	$STOP,  "if [ \"`ps| grep dms_smm | grep -v grep`\" != \"\" ]; then\n");
						fwrite("a",	$STOP,  "	echo \"remove;".$mntfolder.";".$mntfolder.";local;".$share_path."/".$mntfolder.";\" >/tmp/dms_ipc\n");
						fwrite("a",	$STOP, 	"fi\n");
						fwrite("a",	$STOP,  "sleep 1\n");
						fwrite("a",	$STOP,  "rm ".$share_path."/".$mntfolder." \n");
					}
				}
			}
		}
	}
}

if ($usb1 == "1" || $usb2 == "1")
{
	/*************** Initinal path ****************/
	fwrite("a",	$START, "verbose=`xmldbc -g /runtime/services/dlna/verbose`\n");
	fwrite("a",	$START, "mkdir -p /var/access/DMS/media\n");
	fwrite("a",	$START, "mkdir -p /var/access/DMS/data\n");
	fwrite("a",	$START, "cp -rf /etc/scripts/dms_xml /var/access/xml\n");
	fwrite("a",	$START, "mv ".$dms_descr_xml." /var/access/xml/dms_descr.xml\n");
	fwrite("a", $START, "mv ".$access_conf_xml." /var/access/xml/access_conf.xml\n");
	fwrite("a",	$START, "mkdir -p ".$share_path."\n"); // is /var/tmp/dlna_share
    fwrite("a", $START, "mpe_server &\n");
	fwrite("a",	$START, "cd /var/access;\n");
	fwrite("a", $START, "if [ \"\$verbose\" = \"1\" ]; then /etc/scripts/dlna_loop.sh -v & else /etc/scripts/dlna_loop.sh & fi\n");
	fwrite("a",	$START, "sleep 6;\n"); // Delay for make sure dms_smm start.
	fwrite("a",	$START, "if [ \"`ps| grep dms_smm | grep -v grep`\" = \"\" ]; then\n");
	fwrite("a",	$START, "	echo \"DLNA service restart again !\" > /dev/console\n");
	fwrite("a",	$START, "	cd /var/access;\n");
	fwrite("a", $START, "	if [ \"\$verbose\" = \"1\" ]; then /etc/scripts/dlna_loop.sh -v & else /etc/scripts/dlna_loop.sh & fi\n");
	fwrite("a",	$START, "fi\n");

	if($usb1 == "1") {find_shared("usb1");}
	if($usb2 == "1") {find_shared("usb2");}

	if ($model == "MyNetN900" || $model == "MyNetN900C") {
		fwrite("a", $STOP, "killall -9 mpe_server\n");
	}
	fwrite("a", $STOP, "ps | grep /etc/scripts/dlna_loop.sh | awk '{print $1}' | xargs kill -SIGTERM\n");
	fwrite("a",	$STOP, "killall -9 dms_smm\n");
	fwrite("a",	$STOP, "rm -rf /var/access\n");
	fwrite("a",	$STOP, "rm /tmp/dms_ipc\n");
}

/* Done */
fwrite("a",	$START, "xmldbc -t DLNA_MONITOR:60:\"/etc/scripts/dlna_monitor.sh\"\n");
fwrite("a",	$START, "exit 0\n");
fwrite("a", $STOP, 	"exit 0\n");
?>
