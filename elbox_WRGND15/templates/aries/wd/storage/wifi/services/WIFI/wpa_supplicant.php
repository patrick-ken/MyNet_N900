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
	$ssid		= query("sta_ssid");
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
	
	
	/* Generate Branch Conditions for Security Modes */
	if($authtype=="OPEN")	{ $wpa=0;  $ieee8021x=0; }	//WEP-Open
	if($authtype=="SHARED")	{ $wpa=0;  $ieee8021x=0; }	//WEP-Share
	if($authtype=="WEPAUTO"){ $wpa=0;  $ieee8021x=0; }	//WEP-Auto
	if($authtype=="WPA")	{ $wpa=1;  $ieee8021x=1; }	//WPA-Enterprise
	if($authtype=="WPAPSK")	{ $wpa=1;  $ieee8021x=0; }	//WPA-Personal
	if($authtype=="WPA2")	{ $wpa=2;  $ieee8021x=1; }	//WPA2-Enterprise
	if($authtype=="WPA2PSK"){ $wpa=2;  $ieee8021x=0; }	//WPA2-Personal
	if($authtype=="WPA+2")	{ $wpa=3;  $ieee8021x=1; }	//WPA/WPA2-Enterprise	
	if($authtype=="WPA+2PSK"){$wpa=3;  $ieee8021x=0; }	//WPA/WPA2-Personal

	/* generate the config file for wpa-supplicatn */
	fwrite("w", $output, "");
	fwrite("a", $output, 'ap_scan=1\n');
	fwrite("a", $output, 'network={\n');
	fwrite("a", $output, '	ssid=\"'.$ssid.'\"\n');
	fwrite("a", $output, '	scan_ssid=1\n');
	
	if($ieee8021x == 0 && $wpa == 0) //WEP mode
	{
		if($authtype=="SHARED")	
		{
			fwrite("a", $output, 'auth_alg=SHARED\n');
		}
		else if($authtype=="WEPAUTO")
		{
			fwrite("a", $output, 'auth_alg=OPEN SHARED\n');
		} 
		else if ($encrtype == "WEP")
		{
			$ascii = query("nwkey/wep/ascii");
			$index = query("nwkey/wep/defkey");
			$key     = query("nwkey/wep/key:".$index);
			$index--;
			if ($ascii == "1")  {$wepkey = '\"'.$key.'\"';}
			else                {$wepkey = $key;}
    
			fwrite("a", $output,'wep_key'.$index.'='.$wepkey.'\n'.'wep_tx_keyidx='.$index.'\n');
		}
		fwrite("a", $output, 'key_mgmt=NONE\n');
	}
	else if($ieee8021x == 0 && $wpa!=0)
	{
		//$pskkey = query("nwkey/psk/key");
		$pskkey = "12345678";
		fwrite("a", $output,'psk=\"'.$pskkey.'\"\n'.'key_mgmt=WPA-PSK\n');
		
		if ($wpa==1)	{fwrite("a", $output, 'proto=WPA\n');}
		else if ($wpa==2)   {fwrite("a", $output, 'proto=RSN\n');}
		else if ($wpa==3)   {fwrite("a", $output, 'proto=WPA RSN\n');}

		//fwrite("a", $output, 'proto=WPA\n');

		if($encrtype == "TKIP")	{fwrite("a", $output, "pairwise=TKIP\ngroup=TKIP\n");}
		else if($encrtype == "AES"){fwrite("a", $output, "pairwise=CCMP\ngroup=CCMP TKIP\n");}
		else if($encrtype == "TKIP+AES"){fwrite("a", $output, "pairwise=CCMP TKIP\ngroup=CCMP TKIP\n");}
		//fwrite("a", $output, "pairwise=CCMP TKIP\ngroup=CCMP TKIP WEP104 WEP40\n");
		//fwrite("a", $output, 'priority=1\n');
	}
	else if($ieee8021x == 1) //Enterprise mode, don't support now
	{

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
		'   version=0x20\n'.
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


/* generate the bridge list for topology file */
foreach ("/runtime/phyinf")
{
	if (query("type")=="eth" && query("bridge/port#")>0)
	{
		$br = query("name");
		echo "bridge ".$br."\n{\n";
		foreach ("bridge/port"){ 
				if($VaLuE == "STATION24G-1.2")
				echo "\tinterface ".devname($VaLuE)."\n"; 
		}
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
	/* generate the config file for wpa-supplicant */
	generate_configs($uid, $cfile);
}
?>
