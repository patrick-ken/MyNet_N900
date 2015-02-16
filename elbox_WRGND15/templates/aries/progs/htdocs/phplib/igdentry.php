<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";
include "/htdocs/webinc/feature.php";

function ReserveEntry($PATH, $ENTRY)
{
	$cnt = query($PATH."/count");
	if($cnt != $ENTRY)
	{
		$cnt--;
		while($cnt >= $ENTRY)
		{
			$entry = $cnt + 1;
			set($PATH."/entry:".$entry, "");
			movc($PATH."/entry:".$cnt , $PATH."/entry:".$entry);
			set($PATH."/entry:".$entry."/uid", "PORTMAP-".$entry);
			$cnt--;
		}
	}
}

function ConflictDelete($PATH,$INDEX)
{
	if(query($PATH.":".$INDEX."/enable") == "1")
	{
	 	if (query($PATH.":".$INDEX."/protocol") == "TCP") $proto = " -p tcp";
     	else                            $proto = ' -p udp';
     	$extport = query($PATH.":".$INDEX."/externalport");
     	$intport = query($PATH.":".$INDEX."/internalport");
     	$intclnt = query($PATH.":".$INDEX."/internalclient");
		$cmd =  "iptables -t nat -D DNAT.UPNP".$proto." --dport ".$extport." -j DNAT --to-destination ".$intclnt.":".$intport ;
    	setattr("/runtime/upnp/run", "get", $cmd);
	    $res = get("x", "/runtime/upnp/run");
    	del("/runtime/upnp/run");
	
		XNODE_del_entry("/runtime/upnpigd/portmapping",$INDEX);
		$INDEX--;
		return $INDEX;
	}
}


function CheckIGDEntry($EXTERNALPORT, $PROTOCOL, $INTERNALPORT, $LANIP, $DESCRITION)
{
    $name = "LAN-1";
    $infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
    $upnp = query($infp."/upnp/count");
	$path = "/runtime/upnpigd/portmapping/entry";
	if ($upnp > 0)
    {
		if(query("runtime/upnpigd/conflict") == "1") // same externalport need 
		{	
			$cnt = query("/runtime/upnpigd/portmapping/count");
			$i = 1;
			$PORT = cut($EXTERNALPORT,1,-);   //check port have one or more
			if($PORT == "")    //port only have one
			{
				while($i <= $cnt)
	            {
     	        	if(query($path.":".$i."/externalport")==$EXTERNALPORT)
		        	{
						$i = ConflictDelete($path,$i);
	            	}
	            	$i++;
	        	}
			}
			else   //port have two or more
			{
				$pre_port = cut($EXTERNALPORT,0,-);
				$post_port = cut($EXTERNALPORT,1,-);
				while($i <= $cnt)
				{
					if( query($path.":".$i."/externalport")>= $pre_port && query($path.":".$i."/externalport") <= $post_port)
					{
						$i = ConflictDelete($path,$i);
					}
				$i++;	
				}
			
			}
			set("runtime/upnpigd/conflict", "0");
		    return 0;
		}
		else
		{
        	foreach($path)
    		{
    	    	if(query("externalport")==$EXTERNALPORT && query("protocol")==$PROTOCOL && query("internalport")==$INTERNALPORT && query("internalclient") == $LANIP && query("description") == $DESCRITION)	
    	    	return 1;
    		}
		}
    }
    return 0;
}

function InsertIGDEntry($EXTERNALPORT, $PROTOCOL, $INTERNALPORT, $LANIP ,$DESCRITION)
{
    $name = "LAN-1";
    $infp = XNODE_getpathbytarget("", "inf", "uid", $name, 0);
    $upnp = query($infp."/upnp/count");
    if ($upnp > 0)
    {
        $base  = "/runtime/upnpigd/portmapping";
		$base2 = "/nat/entry/portforward";
        $cnt = query($base."/count");
        $cnt++;
        $seqno = query($base."/seqno");
        $seqno++;
        set($base."/seqno", $seqno);
        set($base."/count", $cnt);
	
		if($DESCRITION == "WebMgt")           //RemoteManagement
		{
			ReserveEntry($base, "1");
			$cnt = 1;
		}
		else if($DESCRITION == "PFW")          //PortForwarding
		{	
			$dirty = 1;
			foreach($base."/entry")
			{
				if(query("description") == "WebMgt" || query("description") == "PFW")	
				$dirty++;
			}
			ReserveEntry($base, $dirty);
			$cnt = $dirty;
		}
		else                                   //Orion
		{
			$dirty = 1;
			foreach($base."/entry")
			{
				if(query("description") == "WebMgt" || query("description") == "PFW" ||  substr(query("description"),0,2) == "WD")	
				$dirty++;
			}	
			ReserveEntry($base, $dirty);
			$cnt = $dirty;
				
		}

        $igd_stsp = $base."/entry:".$cnt;
        //TRACE_debug("igd_stsp=".$igd_stsp);
        set($igd_stsp."/uid", "PORTMAP-".$cnt);
        set($igd_stsp."/enable", "1");
        set($igd_stsp."/protocol", $PROTOCOL);
        set($igd_stsp."/remotehost", "");
        set($igd_stsp."/externalport", $EXTERNALPORT);
        set($igd_stsp."/internalport", $INTERNALPORT);
        set($igd_stsp."/internalclient", $LANIP);
        set($igd_stsp."/description", $DESCRITION);
        set($igd_stsp."/leaseduration", "0");
    }
}

function PortUsingCheck($PROTOCOL,$PORTSTART,$PORTEND,$WhoCall)
{	
	$PFW = XNODE_getpathbytarget("/nat", "entry", "uid", "NAT-1");
	$RM = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
	$UPNP = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
	if( $PFW=="" || $RM=="" || $UPNP=="" ) return "0";
	$cnt = 0;
	$z = 1;
	if($WhoCall == "PortForward")
	{/* check remote management */
		if(query($RM."/web") != "")
		{
			if($PROTOCOL=="TCP"||$PROTOCOL=="TCP+UDP")
			{
				if( query($RM."/web") >= $PORTSTART && query($RM."/web") <= $PORTEND )
				{
					return "Conflict:".query($RM."/web")."-RM";
				}
			}
		}
		else if(query($RM."/https_rport") != "")
		{
			if($PROTOCOL=="TCP" || $PROTOCOL=="TCP+UDP")
			{
				if( query($RM."/https_rport") >= $PORTSTART && query($RM."/https_rport") <= $PORTEND )
				{
					return "Conflict:".query($RM."/https_rport")."-RM";
				}
			}
		}
	}
	else if($WhoCall == "Remote")
	{/* check Port Forwarding */
		$cnt = query($PFW."/portforward/entry#");
		if($cnt!="")
		{
			while($z <= $cnt)
			{
				if(query($PFW."/portforward/entry:".$z."/enable")=="1")
				{
					$porS = query($PFW."/portforward/entry:".$z."/external/start");
					$porE = query($PFW."/portforward/entry:".$z."/external/end");
					$pro = query($PFW."/portforward/entry:".$z."/protocol");
					if($pro=="TCP+UDP")
					{
						if($PORTSTART >= $porS && $PORTSTART <= $porE)//because start == end
						{
							return "Conflict:".$PORTSTART."-PFW";
						}
					}
					else
					{
						if($PROTOCOL == $pro)
						{
							if($PORTSTART >= $porS && $PORTSTART <= $porE)//because start == end
							{
								return "Conflict:".$PORTSTART."-PFW";
							}
						}
					}
				}
				$z=$z+1;
			}
		}
	}
	/* 80, 443 ports can not use
	if($PROTOCOL == "TCP" || $PROTOCOL == "TCP+UDP")
	{
		if( "80" >= $PORTSTART && "80" <= $PORTEND )
		{
			return "Conflict:80-S";
		}
		if( "443" >= $PORTEND && "443" <= $PORTEND )
		{
			return "Conflict:443-S";
		}
	} */
	/* check orion */
	$cnt = query("/runtime/orion/entry#");
	$z = 1;
	if($cnt != "" && $FEATURE_MODEL_NAME == "storage")
	{
		while($z <= $cnt)
		{
			$por = query("/runtime/orion/entry:".$z."/external_port");
			if($PROTOCOL == "TCP+UDP")
			{
				if($por >= $PORTSTART && $por <= $PORTEND)
				{
					return "Conflict:".$por."-ORION";
				}
			}
			else
			{
				$pro = query("/runtime/orion/entry:".$z."/protocol");
				if($PROTOCOL == $pro)
				{
					if($por >= $PORTSTART && $por <= $PORTEND)
					{
						return "Conflict:".$por."-ORION";
					}
				}
			}
			$z = $z + 1;
		}
	}
	/* check upnp igd */
	$cnt = 0;
	$z = 1;
	$UPNPenable = query($UPNP."/upnp/count");
	$inetXML = XNODE_getpathbytarget("/inet", "entry", "uid", query($UPNP."/inet") );
	$RouterIP = query($inetXML."/ipv4/ipaddr");
	if($UPNPenable >= "0")
	{
		$cnt = query("/runtime/upnpigd/portmapping/entry#");
		if($cnt!="")
		{
			while($z <= $cnt)
			{
				$active = query("/runtime/upnpigd/portmapping/entry:".$z."/enable");
				if($active == "1")
				{
					$por = query("/runtime/upnpigd/portmapping/entry:".$z."/externalport");
					$porS="";
					$porE="";
					$loc = strchr($por,'-');
					if($loc != "")
					{
						$porS = cut($por, 0, '-');
						$porE = cut($por, 1, '-');
					}
					else
					{
					 	$porS=$por;
					 	$porE=$porS;
					}
					$descrip = query("/runtime/upnpigd/portmapping/entry:".$z."/description");
					$LookIP = query("/runtime/upnpigd/portmapping/entry:".$z."/internalclient");
					if($PROTOCOL == "TCP+UDP")
					{
					 	if( $descrip != "PFW" && $descrip != "WebMgt" )
					 	{
					 		if($descrip == "WD2go" ||$descrip == "WD2goSSL")
					 		{//special case to examine Orion or UPnP such like MyBookLive service
					 			if($LookIP==$RouterIP)
					 			{//is Orion service
					 				if($porS >= $PORTSTART && $porS <= $PORTEND)
								 	{
								 		return "Conflict:".$porS."-ORION";
								 	}
								 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
								 	{
								 		return "Conflict:".$porE."-ORION";
								 	}
					 			}
					 			else
					 			{//is UPnP
					 				if($porS >= $PORTSTART && $porS <= $PORTEND)
								 	{
								 		return "Conflict:".$porS."-UPNP";
								 	}
								 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
								 	{
								 		return "Conflict:".$porE."-UPNP";
								 	}
					 			}
					 		}
					 		else
					 		{//Pure UPnP services, not WD services or Orion
				 				if($porS >= $PORTSTART && $porS <= $PORTEND)
							 	{
							 		return "Conflict:".$porS."-UPNP";
							 	}
							 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
							 	{
							 		return "Conflict:".$porE."-UPNP";
							 	}
					 		}
					 	}
					 	else if( $WhoCall == "PortForward" && $LookIP == "127.0.0.1" && $descrip == "WebMgt" )
					 	{
			 				if($porS >= $PORTSTART && $porS <= $PORTEND)
						 	{
						 		return "Conflict:".$porS."-RM";
						 	}
						 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
						 	{
						 		return "Conflict:".$porE."-RM";
						 	}
					 	}
					 	else if( $WhoCall == "Remote" && $LookIP != "127.0.0.1" && $descrip == "PFW" )
					 	{
			 				if($porS >= $PORTSTART && $porS <= $PORTEND)
						 	{
						 		return "Conflict:".$porS."-PFW";
						 	}
						 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
						 	{
						 		return "Conflict:".$porE."-PFW";
						 	}
					 	}
					}
					else
					{
					 	$pro = query("/runtime/upnpigd/portmapping/entry:".$z."/protocol");
					 	if($PROTOCOL == $pro)
					 	{
						 	if( $descrip != "PFW" && $descrip != "WebMgt" )
						 	{
						 		if($descrip == "WD2go" ||$descrip == "WD2goSSL")
						 		{//special case to examine Orion or UPnP such like MyBookLive service
						 			if($LookIP==$RouterIP)
						 			{//is Orion service
						 				if($porS >= $PORTSTART && $porS <= $PORTEND)
									 	{
									 		return "Conflict:".$porS."-ORION";
									 	}
									 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
									 	{
									 		return "Conflict:".$porE."-ORION";
									 	}
						 			}
						 			else
						 			{//is UPnP
						 				if($porS >= $PORTSTART && $porS <= $PORTEND)
									 	{
									 		return "Conflict:".$porS."-UPNP";
									 	}
									 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
									 	{
									 		return "Conflict:".$porE."-UPNP";
									 	}
						 			}
						 		}
						 		else
						 		{
					 				if($porS >= $PORTSTART && $porS <= $PORTEND)
								 	{
								 		return "Conflict:".$porS."-UPNP";
								 	}
								 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
								 	{
								 		return "Conflict:".$porE."-UPNP";
								 	}
						 		}
						 	}
						 	else if( $WhoCall == "PortForward" && $LookIP == "127.0.0.1" && $descrip == "WebMgt" )
						 	{
				 				if($porS >= $PORTSTART && $porS <= $PORTEND)
							 	{
							 		return "Conflict:".$porS."-RM";
							 	}
							 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
							 	{
							 		return "Conflict:".$porE."-RM";
							 	}
						 	}
						 	else if( $WhoCall == "Remote" && $LookIP != "127.0.0.1" && $descrip == "PFW" )
						 	{
				 				if($porS >= $PORTSTART && $porS <= $PORTEND)
							 	{
							 		return "Conflict:".$porS."-PFW";
							 	}
							 	else if($porE >= $PORTSTART && $porE <= $PORTEND)
							 	{
							 		return "Conflict:".$porE."-PFW";
							 	}
						 	}
					 	}
					}
				}
				$z = $z +1;
			}
		}
	}
	return "1";
}
function PFWDPortCheckFirst($PROTOCOL,$PORTSTART,$PORTEND)
{	
	$PFW = XNODE_getpathbytarget("/nat", "entry", "uid", "NAT-1");
	$RM = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
	$UPNP = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
	if( $PFW=="" || $RM=="" || $UPNP=="" ) return "0";
	$cnt = 0;
	$z = 1;

	/* check remote management */
	if(query($RM."/web") != "")
	{
		if($PROTOCOL=="TCP"||$PROTOCOL=="TCP+UDP")
		{
			if( query($RM."/web") >= $PORTSTART && query($RM."/web") <= $PORTEND )
			{
				return "Conflict:".query($RM."/web")."-RM";
			}
		}
	}
	else if(query($RM."/https_rport") != "")
	{
		if($PROTOCOL=="TCP" || $PROTOCOL=="TCP+UDP")
		{
			if( query($RM."/https_rport") >= $PORTSTART && query($RM."/https_rport") <= $PORTEND )
			{
				return "Conflict:".query($RM."/https_rport")."-RM";
			}
		}
	}

	/* 80, 443 ports can not use */
	/*if($PROTOCOL == "TCP" || $PROTOCOL == "TCP+UDP")
	{
		if( "80" >= $PORTSTART && "80" <= $PORTEND )
		{
			return "Conflict:80-S";
		}
		if( "443" >= $PORTEND && "443" <= $PORTEND )
		{
			return "Conflict:443-S";
		}
	}*/
	/* check orion */
	$cnt = query("/runtime/orion/entry#");
	$z = 1;
	if($cnt != "" && $FEATURE_MODEL_NAME == "storage")
	{
		while($z <= $cnt)
		{
			$por = query("/runtime/orion/entry:".$z."/external_port");
			if($PROTOCOL == "TCP+UDP")
			{
				if($por >= $PORTSTART && $por <= $PORTEND)
				{
					return "Conflict:".$por."-ORION";
				}
			}
			else
			{
				$pro = query("/runtime/orion/entry:".$z."/protocol");
				if($PROTOCOL == $pro)
				{
					if($por >= $PORTSTART && $por <= $PORTEND)
					{
						return "Conflict:".$por."-ORION";
					}
				}
			}
			$z = $z + 1;
		}
	}
	return "1";
}
?>
