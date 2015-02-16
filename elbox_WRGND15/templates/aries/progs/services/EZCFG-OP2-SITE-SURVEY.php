<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
include "/etc/services/PHYINF/phywifi.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");
if (query("/ezcfg/sitesurvey/ssid")!="")
{
	del("/ezcfg");
	del("/runtime/site_survey");
	fwrite("a",$START, "service PHYINF.WIFI restart\n");
	fwrite("a",$STOP, "service PHYINF.WIFI stop\n");
}
else
{
	if (query("/ezcfg/sitesurvey/enable_24g")=="1")
	{
		del("/runtime/site_survey");
		$uid = "STATION24G-1.1";
		$dev = devname($uid);
		if ($dev!="")
		{
    		fwrite("a",$START, "service PHYINF.WIFI stop\n");
			fwrite("a",$START, "xmldbc -t \"ss1:15:service WIFI_MODS start\"\n");
    		fwrite("a",$START, "xmldbc -t \"ss2:21:/etc/ath/makeVAP sta 'My Net N600 sta' 'BANDMODE=2G;CH_MODE=11NGHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=".$dev.";'\"\n");
    		fwrite("a",$START, "xmldbc -t \"ss3:24:/etc/ath/activateVAP ".$dev." br0\"\n");
    		fwrite("a",$START, "xmldbc -t \"ss4:27:sitesurvey ".$dev."\"\n");
			fwrite("a",$START, "xmldbc -t \"ss5:41:ifconfig ".$dev." down\"\n");
    		fwrite("a",$START, "xmldbc -t \"ss6:42:wlanconfig ".$dev." destroy\"\n");
			fwrite("a",$START, "xmldbc -t \"ss7:43:service WIFI_MODS stop\"\n");
		}
	}	
    else if (query("/ezcfg/sitesurvey/enable_5g")=="1")
    {
        del("/runtime/site_survey");
        $uid = "STATION5G-1.1";
        $dev = devname($uid);
        if ($dev!="")
        {
            fwrite("a",$START, "service PHYINF.WIFI stop\n");
            fwrite("a",$START, "xmldbc -t \"ss1:15:service WIFI_MODS start\"\n");
            fwrite("a",$START, "xmldbc -t \"ss2:21:/etc/ath/makeVAP sta 'My Net N600 sta' 'BANDMODE=5G;CH_MODE=11NAHT20;PUREN=0;PUREG=0;RF=RF;PRI_CH=0;ATH_NAME=".$dev.";'\"\n");
            fwrite("a",$START, "xmldbc -t \"ss3:24:/etc/ath/activateVAP ".$dev." br0\"\n");
            fwrite("a",$START, "xmldbc -t \"ss4:27:sitesurvey ".$dev."\"\n");
            fwrite("a",$START, "xmldbc -t \"ss5:41:ifconfig ".$dev." down\"\n");
            fwrite("a",$START, "xmldbc -t \"ss6:42:wlanconfig ".$dev." destroy\"\n");
            fwrite("a",$START, "xmldbc -t \"ss7:43:service WIFI_MODS stop\"\n");
        }
    }
}
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
