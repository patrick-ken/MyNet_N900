Umask 026
PIDFile /var/run/httpdGuest.pid
#LogGMT On
#ErrorLog /dev/console

Tuning
{
	NumConnections 15
	BufSize 12288
	InputBufSize 4096
	ScriptBufSize 4096
	NumHeaders 100
	Timeout 60
	ScriptTimeout 60
}

Control
{
	Types
	{
		text/html	{ html htm }
		text/xml	{ xml }
		text/plain	{ txt }
		image/gif	{ gif }
		image/jpeg	{ jpg }
		text/css	{ css }
		application/octet-stream { * }
	}
	Specials
	{
		Dump		{ /dump }
		CGI			{ cgi }
		Imagemap	{ map }
		Redirect	{ url }
	}
	External
	{
		/usr/sbin/phpcgi { php txt }
	}
}

<?
include "/htdocs/phplib/phyinf.php";

function http_server($sname, $uid, $ifname, $af, $ipaddr, $port)
{
	echo
		"Server".									"\n".
		"{".										"\n".
		"	ServerName \"".$sname."\"".				"\n".
		"	ServerId \"".$uid."\"".					"\n".
		"	Family ".$af.						"\n".
		"	Interface ".$ifname.					"\n".
		"	Address ".$ipaddr.					"\n".
		"	Port ".$port.							"\n".
		"	Virtual".								"\n".
		"	{".										"\n".
		"		AnyHost".							"\n".
		"		Priority 1".						"\n".
		"		Control".							"\n".
		"		{".									"\n".
		"			Alias /".						"\n".
		"			Location /htdocs/web_GuestZone"."\n".
		"			IndexNames { index.php }".		"\n".
		"	    	PathInfo Off".					"\n".
		"			External".						"\n".
		"			{".								"\n".
		"				/usr/sbin/phpcgi { txt }".	"\n".
		"			}".								"\n".
		"			External".						"\n".
		"			{".								"\n".
		"				/usr/sbin/phpcgi { router_info.xml }"."\n".
		"				/usr/sbin/phpcgi { post_login.xml }"."\n".
		"			}".								"\n".
		"		}".									"\n".
		"       Control".                           "\n".
		"       {".                                 "\n".
		"           Alias /api".                    "\n".
		"           Location /htdocs/web/api/dummy"."\n".
		"	    	PathArgs On".					"\n".
		"	    	External".						"\n".
		"	    	{".								"\n".
		"	    		/usr/sbin/restcgi { * }".	"\n".
		"	    	}".								"\n".
		"       }".                                 "\n".
		"		Control".							"\n".
		"		{".									"\n".
		"			Alias /parentalcontrols".		"\n".
		"			Location /htdocs/parentalcontrols"."\n".
		"			External".						"\n".
		"			{".								"\n".
		"				/usr/sbin/phpcgi { php }".	"\n".
		"			}".								"\n".
		"		}".									"\n".
		"	}".										"\n".
		"}".										"\n";
}
$model	= query("/runtime/device/modelname");
$ver	= query("/runtime/device/firmwareversion");
$sname	= "Linux, HTTP/1.1, ".$model." Ver ".$ver;	
http_server($sname, $inf,$ifname,$af,$ipaddr,$port);

?>
