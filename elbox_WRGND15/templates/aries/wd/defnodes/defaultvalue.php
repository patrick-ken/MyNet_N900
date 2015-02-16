<?
setattr("/runtime/devdata/countrycode", "get", "devdata get -e countrycode");
$CC = get("x", "/runtime/devdata/countrycode");
del("/runtime/devdata/countrycode");
setattr("/runtime/device/telnet", "get", "devdata get -e telnet_disable");
$telnet = get("x", "/runtime/device/telnet");
del("/runtime/device/telnet");
set("/runtime/device/telnet_disable", $telnet);
if($CC=="US"||$CC=="CA") $timezone = 8; //North America 
else if($CC=="RU"||$CC=="CZ"||$CC=="HU"||$CC=="PL"||$CC=="SK"||
		$CC=="SI"||$CC=="RO"||$CC=="TR"||$CC=="AT"||$CC=="BE"||
		$CC=="DK"||$CC=="FI"||$CC=="FR"||$CC=="DE"||$CC=="GR"||
		$CC=="IS"||$CC=="IE"||$CC=="IT"||$CC=="LU"||$CC=="NL"||
		$CC=="NO"||$CC=="PT"||$CC=="ES"||$CC=="SE"||$CC=="CH") $timezone = 27; //North America
else if($CC=="AR"||$CC=="BR"||$CC=="MX") $timezone = 21; //Latin America
else if($CC=="AU"||$CC=="NZ") $timezone = 68;
else if($CC=="CN"||$CC=="HK"||$CC=="ID"||$CC=="MY"||$CC=="PH"||
		$CC=="SG"||$CC=="VN") $timezone = 57;
else if($CC=="IN"||$CC=="TH") $timezone = 49;
else if($CC=="TW") $timezone = 61;
set("/device/time/timezone", $timezone);
?>
