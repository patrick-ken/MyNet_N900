# Auto generated topology file by HOSTAPD service
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/htdocs/phplib/inf.php";
include "/etc/services/PHYINF/phywifi.php";

/********************************************************************/
function find_bridge($phyinf)
{
	foreach ("/runtime/phyinf")
	{
		if (query("type")!="eth") continue;
		foreach ("bridge/port") if ($VaLuE==$phyinf) {$find = "yes"; break;}
		if ($find=="yes") return query("uid");
	}
	return "";
}
/********************************************************************/

function trim_strip($str)
{
    $newStr     = "";
    $total_field    = cut_count($str, "-");
    $i = 0;
    while($i < $total_field)
    {
        $field = cut($str, $i, '-');
        $newStr = $newStr.$field;
        $i++;
    }

    return $newStr;
}


function generate_configs($phyinfuid, $output)
{
	$p = XNODE_getpathbytarget("", "phyinf", "uid", $phyinfuid, 0);
	$wifi = XNODE_getpathbytarget("/wifi", "entry", "uid", query($p."/wifi"), 0);
	anchor($wifi);

	/* Find the bridge & device names */
	$bruid = find_bridge($phyinfuid);
	if ($bruid!="") $brdev = PHYINF_getifname($bruid);
	$dev = devname($phyinfuid);

	$authtype	= query("authtype");
	$encrtype	= query("encrtype");
	$ssid		= query("ssid");
	$wps		= query("wps/enable");

	/* for wfa device */
	$vendor		= query("/runtime/device/vendor");
	$model      	= query("/runtime/device/modelname");
	$upnpp		= XNODE_getpathbytarget("/runtime/upnp", "dev", "deviceType",
					"urn:schemas-wifialliance-org:device:WFADevice:1", 0);
	$uuid		= query($upnpp."/guid");
	
	$Genericname = query("/runtime/device/upnpmodelname");
	if($Genericname == ""){ $Genericname = $model; }

	$freq 		= query($p."/media/freq");
	$wsc2_version	= query("wps/wsc2_version");//marco
	
	$uuid 		= trim_strip($uuid);
	
	
	/* Generate config file */
	//$authtype=="OPEN")	
	//$authtype=="SHARED")	
	//$authtype=="WEPAUTO")	
	//$authtype=="WPA")	
	//$authtype=="WPAPSK")	
	//$authtype=="WPA2")	
	//$authtype=="WPA2PSK")	
	//$authtype=="WPA+2")	
	//$authtype=="WPA+2PSK")

	/* generate the config file for hostapd */
	fwrite("w", $output, "");
	
	//todo : we shouldn't need this line
	fwrite("a", $output, 'ctrl_interface=/var/run/wpa_supplicant\n\n');
	
	fwrite("a", $output, 'network={\n');
	fwrite("a", $output, 
		'	ssid=\"'.$ssid.'\"\n');	
	/*todo 
	    proto=RSN
	    auth_alg=OPEN
	*/
	
	/* encrtype */
	//$encrtype == "NONE")	
	//$encrtype == "WEP")	
	//$encrtype == "TKIP")	
	//$encrtype == "AES")	
	//$encrtype == "TKIP+AES")

	if ($encrtype == "NONE")
	{
		fwrite("a", $output, '	key_mgmt=NONE\n');	    
	} 
	else if ($encrtype == "WEP")
	{
		$index = query($wifi."/nwkey/wep/defkey");
    
		fwrite("a", $output,
			'	wep_key'.$index.'='.query($wifi."/nwkey/wep/key:".$index).'\n'.
			'	wep_tx_keyidx='.$index.'\n'
			);	    
	}
	else
	{
		$pskkey 	= query("nwkey/psk/passphrase");
		
		fwrite("a", $output,
			'	psk='.query($wifi."/nwkey/psk/key").'\n'.	
			'	key_mgmt=WPA-PSK\n'.
//			'	pairwise=TKIP CCMP\n'.
			'	group=TKIP CCMP\n'
			);
	}

	fwrite("a", $output, '}\n');		


	fwrite("a", $output, 'wps_property={\n');

	fwrite("a", $output,
		'	uuid='.$uuid.'\n'.
		'	auth_type_flags=0x003f\n'.
		'	encr_type_flags=0x000f\n'
		);
	
	if($wsc2_version!="")
	{
		fwrite("a", $output,
			'	version=0x10\n'.
			'	conn_type_flags=0x01\n'.
			'	config_methods=0x278c\n'
			);
	}
	else
	{	
		fwrite("a", $output,
			'	version=0x10\n'.
			'	conn_type_flags=0x01\n'.
			'	config_methods=0x0086\n'
			);
	}

	//todo : i've tested with rf_bands=0x01 and why it works with 5G ? 
		
	if($freq != "5")	{ fwrite("a",$output,'	rf_bands=0x01\n'); }
	else			{ fwrite("a",$output,'	rf_bands=0x02\n'); }
	

	fwrite("a", $output,
		'	manufacturer=\"'.$vendor.'\"\n'.
		'	serial_number=00000000\n'.
		'	model_number=00000000\n'.
		'	model_name=\"'.$Genericname.'\"\n'
		);

	
	fwrite("a", $output,
		'	dev_name=\"'.$model.'\"\n'.
		'	dev_category=6\n'.
		'	dev_sub_category=1\n'.
		'	dev_oui=0050f204\n'
		);
	
	
	if($wsc2_version!="")	{ fwrite("a", $output,'	os_version=0x80000000\n'); }
	else			{ fwrite("a", $output,'	os_version=0x00000001\n'); }
	fwrite("a", $output,'	newsettings_command="/etc/scripts/wps_sta.sh restartap"\n'); /* changed by alex shi,we should use wps_sta.sh instead of wps.sh */
	fwrite("a", $output, '}\n');		
}

/********************************************************************/
//# Auto generated topology file by HOSTAPD service
//bridge br0
//{
//        interface ath0
//}
//radio ath0
//{
//        sta ath0
//        {
//            driver madwifi
//            config /mnt/plaintext.conf
//        }
//}

//# Plaintext (no encryption) network
//
//ctrl_interface=/var/run/wpa_supplicant
//
//network={
//        ssid="dlink-815b-24g"
//        key_mgmt=NONE
//}





/* generate the bridge list for topology file */
foreach ("/runtime/phyinf")
{
	if (query("type")=="eth" && query("bridge/port#")>0)
	{
		$br = query("name");
		echo "bridge ".$br."\n{\n";
		foreach ("bridge/port") echo "\tinterface ".devname($VaLuE)."\n";
		echo "}\n";
	}
}

$i = 0;
foreach ("/runtime/phyinf")
{
	if (query("type")!="wifi") continue;

	/* generate the radio list for topology file */
	$uid = query("uid");
	$dev = devname($uid);
	$cfile = '/var/run/wpa_supplicant-'.$dev.'.conf';
	echo
		"radio wifi".$i."\n".
		"{\n".
		"	sta ".$dev."\n".
		"	{\n".
		"		driver madwifi\n".
		"		config ".$cfile."\n".
		"	}\n".
		"}\n";
	$i++;
	/* generate the config file for hostapd */
	generate_configs($uid, $cfile);
}
?>
