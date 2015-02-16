<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/phyinf.php";

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)	{startcmd("exit ".$errno); stopcmd("exit ".$errno);}

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

$country = query("/runtime/devdata/countrycode");
	if		($country == "AU")	{  }
	else if	($country == "CA")	{  }
	else if	($country == "CN")	{  }
	else if	($country == "SG")	{  }
//	else if	($country == "LA")	{ $country="BR"; }
	else if	($country == "IL")	{  }
	else if	($country == "KR")	{  }
	else if	($country == "JP")	{  }
	else if	($country == "EG")	{  }
	else if	($country == "BR")	{  }
	else if	($country == "RU")	{  }
	else if	($country == "US")	{  }
	else if ($country == "EU") 	{ $country="GB"; }
	/* EU == GB */
	else if ($country == "GB") 	{ }
	else if ($country == "TW") 	{ }
	else if ($country == "HK")  { $country="SG"; } //anny_for_wd
	else if ($country == "PH")  { $country="SG"; } //anny_for_wd
	else if ($country == "TH")  { $country="SG"; } //anny_for_wd
	else if ($country == "BR")  { $country="MX"; } //anny_for_wd
	else if ($country == "AR")  { $country="MX"; } //anny_for_wd
	else if ($country == "TR")  { $country="GB"; } //anny_for_wd



$wlanmac = query("/runtime/devdata/wlanmac");				
if ($wlanmac=="")	{$wlanmac="00:11:22:33:44:55";}
$wlanmac2 = query("/runtime/devdata/wlanmac2");				
if ($wlanmac2=="")	{$wlanmac="00:11:22:33:44:57";}

fwrite("a",$START,"insmod /lib/modules/adf.ko\n");
fwrite("a",$START,"insmod /lib/modules/asf.ko\n");	
fwrite("a",$START,"insmod /lib/modules/ath_hal.ko\n");	
fwrite("a",$START,"insmod /lib/modules/ath_rate_atheros.ko\n");	
//fwrite("a",$START,"insmod /lib/modules/ath_dfs.ko\n");	
fwrite("a",$START,"insmod /lib/modules/ath_dev.ko\n");	
fwrite("a",$START,"insmod /lib/modules/umac.ko\n");

//fwrite("a",$START,"sleep 1\n");
//fwrite("a",$START,"ifconfig wifi1 hw ether ".$wlanmac."\n");
//fwrite("a",$START,"ifconfig wifi0 hw ether ".$wlanmac2."\n");
//fwrite("a",$START,"iwpriv wifi0 setCountry ".$country."\n");	
//fwrite("a",$START,"iwpriv wifi1 setCountry ".$country."\n");	


fwrite("a",$STOP,"rmmod umac.ko\n");
fwrite("a",$STOP,"rmmod ath_dev.ko\n");	
//fwrite("a",$STOP,"rmmod ath_dfs.ko\n");	
fwrite("a",$STOP,"rmmod ath_rate_atheros.ko\n");	
fwrite("a",$STOP,"rmmod ath_hal.ko\n");
fwrite("a",$STOP,"rmmod adf.ko\n");
fwrite("a",$STOP,"rmmod asf.ko\n");		

fwrite("a",$START,	"exit 0\n");
fwrite("a",$STOP,	"exit 0\n");
?>
