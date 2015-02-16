<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<ezcfg>
<?
echo "<qos>".dump(0, "/bwc/entry:1/enable")."</qos>\n";

$inf = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
if ($inf!="")
{
	$rphyinf = XNODE_getpathbytarget("", "runtime/phyinf", "uid", query($inf."/phyinf"), 0);
	if (query($rphyinf."/linkstatus")!="0" && query($rphyinf."/linkstatus")!="")
	{
    	$inet = XNODE_getpathbytarget("", "inet/entry", "uid", query($inf."/inet"), 0);
    	if ($inet!="")
    	{
			if (query($inet."/addrtype") == "ipv4")
			{
				if (query($inet."/ipv4/static") == "1")
				{
					echo "<wanstatus>1</wanstatus>\n";
				}
				else
				{
					$rinet = XNODE_getpathbytarget("", "runtime/inf", "uid", "WAN-1", 0);
					if ($rinet!="")
					{
						if (query($rinet."/inet/ipv4/valid")=="1")
						{
							echo "<wanstatus>1</wanstatus>\n";
						}	
						else if (query($rinet."/inet/ipv4/conflict")=="1")
						{
							echo "<wanstatus>1</wanstatus>\n";
						}
						else
						{
							echo "<wanstatus>0</wanstatus>\n";
						}
					}
					else
					{
						echo "<wanstatus>0</wanstatus>\n";
					}
				}
			}
			else if (query($inet."/addrtype") == "ppp4" || query($inet."/addrtype") == "ppp10")
			{
				$rinet = XNODE_getpathbytarget("", "runtime/inf", "uid", "WAN-1", 0);
				//TRACE_debug("alex62=".$rinet);
				if (query($rinet."/pppd/status")=="" || query($rinet."/pppd/status")=="disconnected" || query($rinet."/pppd/status")=="on demand")
				{
					echo "<wanstatus>0</wanstatus>\n";
				}	
				else if (query($rinet."/inet/ppp4/valid")=="1")
				{
					echo "<wanstatus>1</wanstatus>\n";
				}
				else
				{
					echo "<wanstatus>0</wanstatus>\n";
				}
			}
			else
			{
				echo "<wanstatus>0</wanstatus>\n";
			}
    	}
		else
		{
			echo "<wanstatus>0</wanstatus>\n";
		}
		echo "<linkstatus>1</linkstatus>\n";
	}
	else
	{
		echo "<linkstatus>0</linkstatus>\n";
		echo "<wanstatus>0</wanstatus>\n";
	}
}
else
{
	echo "<linkstatus>0</linkstatus>\n";
	echo "<wanstatus>0</wanstatus>\n";
}
?></ezcfg>
</module>
