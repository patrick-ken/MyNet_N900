<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<?
	echo "<account>\n";
	foreach("/device/account/entry")
	{
		echo "<entry>\n";
		echo "<name>".query("name")."</name>\n";
		echo "<password>".query("password")."</password>\n";
		echo "<group>".query("group")."</group>\n";
		echo "</entry>\n";
	}
	echo "</account>\n";
	echo "<wan>\n";
        foreach("/inf")
        {
		if (query("uid")=="WAN-1" || query("uid")=="WAN-2" || query("uid")=="WAN-3" || query("uid")=="WAN-4")
		{
			$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query("inet"), 0);	
			echo "<entry>\n";
			echo "<uid>".query("uid")."</uid>\n";
			echo "<active>".query("active")."</active>\n";
			echo "<addrtype>".query($inet."/addrtype")."</addrtype>\n";
			echo "<ipv4>\n";
			echo "<static>".query($inet."/ipv4/static")."</static>\n";
			echo "<mtu>".query($inet."/ipv4/mtu")."</mtu>\n";
			echo "<dns>\n";
			echo "<count>".query($inet."/ipv4/dns/count")."</count>\n";
			echo "<entry>".query($inet."/ipv4/dns/entry:1")."</entry>\n";
			echo "<entry>".query($inet."/ipv4/dns/entry:2")."</entry>\n";
			echo "</dns>\n";
			echo "<ipaddr>".query($inet."/ipv4/ipaddr")."</ipaddr>\n";
			echo "<mask>".query($inet."/ipv4/mask")."</mask>\n";
			echo "<gateway>".query($inet."/ipv4/gateway")."</gateway>\n";
			echo "</ipv4>\n";
			echo "<ppp4>\n";
			echo "<static>".query($inet."/ppp4/static")."</static>\n";
			echo "<mtu>".query($inet."/ppp4/mtu")."</mtu>\n";
			echo "<ipaddr>".query($inet."/ppp4/ipaddr")."</ipaddr>\n";
			echo "<username>".query($inet."/ppp4/username")."</username>\n";
			echo "<password>".query($inet."/ppp4/password")."</password>\n";
			echo "<over>".query($inet."/ppp4/over")."</over>\n";
			echo "<dns>\n";
                        echo "<count>".query($inet."/ppp4/dns/count")."</count>\n";
                        echo "<entry>".query($inet."/ppp4/dns/entry:1")."</entry>\n";
                        echo "<entry>".query($inet."/ppp4/dns/entry:2")."</entry>\n";
                        echo "</dns>\n";
			echo "<dialup>\n";
			echo "<mode>".query($inet."/ppp4/dialup/mode")."</mode>\n";
			echo "<idletimeout>".query($inet."/ppp4/dialup/idletimeout")."</idletimeout>\n";
			echo "</dialup>\n";
        		echo "<mppe>\n";
                	echo "<enable>".query($inet."/ppp4/mppe/enable")."</enable>\n";
        		echo "</mppe>\n";
        		echo "<pppoe>\n";
                	echo "<acname>".query($inet."/ppp4/pppoe/acname")."</acname>\n";
                	echo "<servicename>".query($inet."/ppp4/pppoe/servicename")."</servicename>\n";
        		echo "</pppoe>\n";
			echo "<pptp>\n";
			echo "<server>".query($inet."/ppp4/pptp/server")."</server>\n";
			echo "</pptp>\n";
			echo "</ppp4>\n";
			echo "<ipv6>\n";
			echo "<mode>".query($inet."/ipv6/mode")."</mode>\n";
			echo "<mtu>".query($inet."/ipv6/mtu")."</mtu>\n";
			echo "<pdhint>\n";
			echo "<enable>". query($inet."/ipv6/pdhint/enable")."</enable>\n";
			echo "</pdhint>\n";
                        echo "<dns>\n";
                        echo "<count>".query($inet."/ipv6/dns/count")."</count>\n";
                        echo "<entry>".query($inet."/ipv6/dns/entry:1")."</entry>\n";
                        echo "<entry>".query($inet."/ipv6/dns/entry:2")."</entry>\n";
                        echo "</dns>\n";
			echo "</ipv6>\n";
			echo "<ppp6>\n";
			echo "<over>".query($inet."/ppp6/over")."</over>\n";
			echo "<static>".query($inet."/ppp6/static")."</static>\n";
			echo "<ipaddr>".query($inet."/ppp6/ipaddr")."</ipaddr>\n";
			echo "<mtu>".query($inet."/ppp6/mtu")."</mtu>\n";
			echo "<mru>".query($inet."/ppp6/mru")."</mru>\n";
			echo "<username>".query($inet."/ppp6/username")."</username>\n";
			echo "<password>".query($inet."/ppp6/password")."</password>\n";
			echo "<dialup>\n";
			echo "<mode>".query($inet."/ppp6/dialup/mode")."</mode>\n";
			echo "<idletimeout>".query($inet."/ppp6/dialup/idletimeout")."</idletimeout>\n";
			echo "</dialup>\n";
			echo "<dns>\n";
                        echo "<count>".query($inet."/ppp6/dns/count")."</count>\n";
                        echo "<entry>".query($inet."/ppp6/dns/entry:1")."</entry>\n";
                        echo "<entry>".query($inet."/ppp6/dns/entry:2")."</entry>\n";
                        echo "</dns>\n";
			echo "<pppoe>\n";
                        echo "<acname>".query($inet."/ppp6/pppoe/acname")."</acname>\n";
                        echo "<servicename>".query($inet."/ppp6/pppoe/servicename")."</servicename>\n";
                        echo "</pppoe>\n";
			echo "</ppp6>\n";
			echo "</entry>\n";
		}
	}
	echo "</wan>\n";
	echo "<lan>\n";
	foreach("/inf")
	{
		if (query("uid")=="LAN-1" || query("uid")=="LAN-2" || query("uid")=="LAN-3" || query("uid")=="LAN-4")
		{
			$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query("inet"), 0);
			$dhcps4 = XNODE_getpathbytarget("", "dhcps4/entry", "uid", query("dhcps4"), 0);
			echo "<entry>\n";
			echo "<uid>".query("uid")."</uid>\n";
			echo "<active>".query("active")."</active>\n";
			echo "<addrtype>".query($inet."/addrtype")."</addrtype>\n";
			echo "<ipv4>\n";
			echo "<ipaddr>".query($inet."/ipv4/ipaddr")."</ipaddr>\n";
			echo "<mask>".query($inet."/ipv4/mask")."</mask>\n";
			echo "</ipv4>\n";
			echo "<dhcps4>\n";
			echo "<start>".query($dhcps4."/start")."</start>\n";
			echo "<end>".query($dhcps4."/end")."</end>\n";
			echo "<domain>".query($dhcps4."/domain")."</domain>\n";
			echo "<leasetime>".query($dhcps4."/leasetime")."</leasetime>\n";
			echo "</dhcps4>\n";
			echo "</entry>\n";
		}
        }
	echo "</lan>\n";
	echo "<wifi>\n";
	foreach ("/phyinf")
	{
		if (query("uid")=="BAND24G-1.1" || query("uid")=="BAND24G-1.2" || query("uid")=="BAND5G-1.1" || query("uid")=="BAND5G-1.2")
		{
			$wifi =  XNODE_getpathbytarget("", "wifi/entry", "uid", query("wifi"), 0);
			echo "<entry>\n";
			echo "<uid>".query("uid")."</uid>\n";
			echo "<active>".query("active")."</active>\n";
			echo "<media>\n";
			echo "<wlmode>".query("media/wlmode")."</wlmode>\n";
			echo "<dot11n>\n";
			echo "<bandwidth>".query("media/dot11n/bandwidth")."</bandwidth>\n";
			echo "<guardinterval>".query("media/dot11n/guardinterval")."</guardinterval>\n";
			echo "</dot11n>\n";
			echo "<wmm>\n";
			echo "<enable>".query("media/wmm/enable")."</enable>\n";
			echo "</wmm>\n";
			echo "</media>\n";
			echo "<opmode>".query($wifi."/opmode")."</opmode>\n";
			echo "<ssid>".query($wifi."/ssid")."</ssid>\n";
			echo "<ssidhidden>".query($wifi."/ssidhidden")."</ssidhidden>\n";
			echo "<authtype>".query($wifi."/authtype")."</authtype>\n";
			echo "<encrtype>".query($wifi."/encrtype")."</encrtype>\n";
			echo "<wps>\n";
			echo "<enable>".query($wifi."/wps/enable")."</enable>\n";
			echo "</wps>\n";
			echo "<nwkey>\n";
			echo "<psk>\n";
			echo "<passphrase>".query($wifi."/nwkey/psk/passphrase")."</passphrase>\n";
			echo "<key>".query($wifi."/nwkey/psk/key")."</key>\n";
			echo "</psk>\n";
			echo "</nwkey>\n";
			echo "</entry>\n";
		}
	}
	echo "</wifi>\n";
/*
	echo "<nat>\n";
	$nat = XNODE_getpathbytarget("/nat", "entry", "uid", cut($GETCFG_SVC,1,"."));
	if ($nat!="")
	{
        	$svc = cut($GETCFG_SVC,0,".");
        	if      ($svc == "PFWD")                $target = "portforward";
        	else if ($svc == "VSVR")                $target = "virtualserver";
        	else if ($svc == "PORTT")               $target = "porttrigger";
        	else if ($svc == "DMZ")                 $target = "dmz";
        	else                                    $target = "";

        	if ($target!="")
        	{
                	echo "\t\t<entry>\n";
                	echo "\t\t\t<".$target.">\n";
                	echo dump(4, $nat."/".$target);
                	echo "\t\t\t</".$target.">\n";
                	echo "\t\t</entry>\n";
        	}
	}
	echo "</nat>\n";
*/
	echo "<security>\n";
	foreach ("/acl/firewall/entry")
	{
		echo "<entry>\n";
		echo dump(0, "");
		echo "</entry>\n";
	}
	echo "</security>\n";
?></module>
