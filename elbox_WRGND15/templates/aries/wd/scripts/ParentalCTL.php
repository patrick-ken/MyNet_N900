<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

fwrite("w","/var/etc/parentalctl.sh", "#!/bin/sh\n");

function file_write($string)  {fwrite("a","/var/etc/parentalctl.sh", $string."\n");}

function timesup($timestart,$timestop,$days, $device, $MAC)
{
	$starthour=cut($timestart, 0, ":");
	$stophour =cut($timestop,  0, ":");
	if($starthour >= $stophour)
	{
		if($device == "sdule")
		{
			file_write("event PCTL.TIMESUP add \"usockc /var/accesstimectl PCTL.TIMESUP_".$MAC."_sdule_COVER_".$timestop."_".$days."\"");
			file_write("event PCTL.TIMESUP");
		}
		else if($device == "guest")
		{
			file_write("event PCTL.TIMESUP add \"usockc /var/accesstimectl PCTL.TIMESUP_GUEST_COVER_".$timestop."_".$days."\"");
			file_write("event PCTL.TIMESUP");
		}
	}
	else 
	{
		if($device == "sdule")
		{
			file_write("event PCTL.TIMESUP add \"usockc /var/accesstimectl PCTL.TIMESUP_".$MAC."_sdule_UNCOV_".$timestop."_".$days."\"");
			file_write("event PCTL.TIMESUP");
		}
		else if($device == "guest")
		{
			file_write("event PCTL.TIMESUP add \"usockc /var/accesstimectl PCTL.TIMESUP_GUEST_UNCOV_".$timestop."_".$days."\"");
			file_write("event PCTL.TIMESUP");
		}
	}
}

function sch_add_rule($MAC,$MARK,$MARK2)
{
    $tmp =0;     
	$path = XNODE_getpathbytarget("", "security/parental/DeviceList/entry", "mac", $MAC, 0);
	$days = query($path."/days");
	$timestart = query($path."/start");
	$timestop  = query($path."/end");
    while($tmp<2)
    {
        if($tmp==0)    {$iptype="";}
        else          {$iptype="6";}     
        //accept rule
	    file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark ".$MARK);
     	file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");
      	file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -j CONNMARK2 --set-mark ".$MARK);
       	file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");
        //drop rule
        file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 ! --mark ".$MARK." -m state --state NEW -j CONNMARK2 --set-mark ".$MARK2);
        file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK2." -j RETURN");
        file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 ! --mark ".$MARK." -j CONNMARK2 --set-mark ".$MARK2);
        file_write("ip".$iptype."tables -A PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK2." -j RETURN");  
	    $tmp++;
    }
    while(isfile("/var/IS_SAVING")==1)
	{}
	if(isfile("/var/IN_SAVEING")==0)
	{
		fwrite("w+", "/var/IN_SAVEING", "1");
	}
	set($path."/acceptMark", $MARK);
	set($path."/dropMark", $MARK2);
	unlink("/var/IN_SAVEING");
	//daemon will know device timesup time	
	$schedule = "sdule";
	timesup($timestart,$timestop,$days,$schedule,$MAC);
}

function sch_del_rule($MAC)
{
    $tmp       = 0;      
	$path      = "security/parental/action";
	$days = query($path."/days");
	$timestart = query($path."/start");
	$timestop  = query($path."/end");
	$lastmark  = query($path."/acceptMark");
	$lastmark2 = query($path."/dropMark");
    while($tmp<2)
    {
        if($tmp==0)    {$iptype="";}
        else          {$iptype="6";}  
 	    //accept
     	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark ".$lastmark);
      	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$lastmark." -j RETURN");
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -j CONNMARK2 --set-mark ".$lastmark);
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$lastmark." -j RETURN");
       	//drop
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 ! --mark ".$lastmark." -m state --state NEW -j CONNMARK2 --set-mark ".$lastmark2);
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$lastmark2." -j RETURN");
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 ! --mark ".$lastmark." -j CONNMARK2 --set-mark ".$lastmark2);
       	file_write("ip".$iptype."tables -D PCTL.SCH -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$lastmark2." -j RETURN"); 
        $tmp++;          
    }
	file_write("echo -n \"--connmark ".$lastmark."\" > /proc/nf_conntrack_flush");
	file_write("echo -n \"--connmark ".$lastmark2."\" > /proc/nf_conntrack_flush");
}

function daily_add_rule($DAY,$MAC,$MARK)
{
    $tmp  = 0;     
    $path = XNODE_getpathbytarget("", "security/parental/DeviceList/entry", "mac", $MAC, 0);
    //weekend
	if($DAY == "Sun" || $DAY == "Sat")
		$hour   = query($path."/weekend");
	else
		$hour   = query($path."/weekday");

	while($tmp<2)
    {
        if($tmp==0)    {$iptype="";}
        else          {$iptype="6";}  
		
        file_write("ip".$iptype."tables -A PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m state --state NEW -j CONNMARK2 --set-mark ".$MARK);
		file_write("ip".$iptype."tables -A PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");
		file_write("ip".$iptype."tables -A PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -j CONNMARK2 --set-mark ".$MARK);
		file_write("ip".$iptype."tables -A PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");
		$tmp++; 
	}
      
    while(isfile("/var/IS_SAVING")==1)
	{}
	if(isfile("/var/IN_SAVEING")==0)
    {
 	    fwrite("w+", "/var/IN_SAVEING", "1");
	}
	if($MARK < 128)                //If mark small than 128 then mark is acceptmark
	{
		set($path."/acceptMark", $MARK);
		set($path."/dropMark", "");
		file_write("event PCTL.TIMESUP add \"usockc /var/accesstimectl PCTL.TIMESUP_".$MAC."_daily_".$hour."\"");
		file_write("event PCTL.TIMESUP");
	}
	else
	{
		set($path."/acceptMark", "");
		set($path."/dropMark", $MARK);
	}
	unlink("/var/IN_SAVEING");
}

/*	daily would have two path. one is do DEL = "security/parental/action",
	the other one is timesup have to change iptables rules = in security/parental/DeviceList/entry:?? */
function daily_del_rule($MAC,$path)  
{
	$tmp		= 0;     
	$MARK		= query($path."/acceptMark");
	if($MARK == "")	{$MARK  = query($path."/dropMark");}

	while($tmp<2)
    {
        if($tmp==0)    {$iptype="";}
        else          {$iptype="6";}  
		
		file_write("ip".$iptype."tables -D PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m state --state NEW -j CONNMARK2 --set-mark ".$MARK);
		file_write("ip".$iptype."tables -D PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");
		file_write("ip".$iptype."tables -D PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -j CONNMARK2 --set-mark ".$MARK);
		file_write("ip".$iptype."tables -D PCTL.DAILY -i br0 -m mac --mac-source ".$MAC." -m connmark2 --mark ".$MARK." -j RETURN");

		$tmp++; 
    }
	
	file_write("echo -n \"--connmark ".$MARK."\" > /proc/nf_conntrack_flush");
}

function guest_add_rule()
{
	//assume that guest zone is at LAN-2
	$lan = XNODE_getpathbytarget("", "inf", "uid", "LAN-2", 0);
	$lan_inet = XNODE_getpathbytarget("/inet", "entry", "uid", query($lan."/inet"), 0);
	$ipaddr = query($lan_inet."/ipv4/ipaddr");
	$mask = query($lan_inet."/ipv4/mask");
	$path      = "security/parental/guest";
	$days 	   = query($path."/days");
	$timestart = query($path."/start");
	$timestop  = query($path."/end");
	file_write("iptables -A PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark 127");
	file_write("iptables -A PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 --mark 127 -j RETURN");
	file_write("iptables -A PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 ! --mark 127 -m state --state NEW -j CONNMARK2 --set-mark 191");    
	file_write("iptables -A PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 --mark 191 -j RETURN");
	file_write("iptables -A PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark 127");
    file_write("iptables -A PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 --mark 127 -j RETURN");
    file_write("iptables -A PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 ! --mark 127 -m state --state NEW -j CONNMARK2 --set-mark 191");
    file_write("iptables -A PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 --mark 191 -j RETURN");
	//daemon will know device timesup time
	$device = "guest";
	timesup($timestart,$timestop,$days,$device,$device);
}

function guest_del_rule()
{
	//assume that guest zone is at LAN-2
	$lan = XNODE_getpathbytarget("", "inf", "uid", "LAN-2", 0);
	$lan_inet = XNODE_getpathbytarget("/inet", "entry", "uid", query($lan."/inet"), 0);
	$ipaddr = query($lan_inet."/ipv4/ipaddr");
	$mask = query($lan_inet."/ipv4/mask");
    $path      = "security/parental/action";  
    $days      = query($path."/days");
    $timestart = query($path."/start");
    $timestop  = query($path."/end");
    file_write("iptables -D PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark 127");
	file_write("iptables -D PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 --mark 127 -j RETURN");
    file_write("iptables -D PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 ! --mark 127 -m state --state NEW -j CONNMARK2 --set-mark 191"); 
	file_write("iptables -D PCTL.GUEST -i br1 -s ".$ipaddr."/".$mask." -m connmark2 --mark 191 -j RETURN");
	file_write("iptables -D PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m time --timestart ".$timestart." --timestop ".$timestop." --days ".$days." -m state --state NEW -j CONNMARK2 --set-mark 127");
	file_write("iptables -D PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 --mark 127 -j RETURN");
	file_write("iptables -D PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 ! --mark 127 -m state --state NEW -j CONNMARK2 --set-mark 191");
	file_write("iptables -D PCTL.GUEST -i br1 -d ".$ipaddr."/".$mask." -m connmark2 --mark 191 -j RETURN");
	file_write("echo -n \"--connmark 127\" > /proc/nf_conntrack_flush");
	file_write("echo -n \"--connmark 191\" > /proc/nf_conntrack_flush");
}


if($ACTION == "ADD")
{
	if($MAC != "GUEST")
	{	
		$path = XNODE_getpathbytarget("", "security/parental/DeviceList/entry", "mac", $MAC, 0);
		//Add device schedule rules
		if(query($path."/type")==1)
		{
	        sch_add_rule($MAC,$MARK,$MARK2);
		}
		//Add device daily rules
		else if(query($path."/type")==2)
		{
			daily_add_rule($DAY,$MAC,$MARK);
		}
		else{file_write("echo ADD in wrong type!!");}
	}
	//GUEST
	else
	{	
		//Add guest rules
		guest_add_rule();
	}
}
else if ( $ACTION == "DEL")
{
    $path   = "security/parental/action";
	if($MAC != "GUEST")
	{
		//Delete device schedule rules
        if(query($path."/LastState")==1)
        {
			sch_del_rule($MAC);
        }
		//Delete device daily rules
		else if(query($path."/LastState")==2)
		{
			daily_del_rule($MAC,$path);
		}
		else {file_write("echo DEL in wrong type!!");}
	}
	//GUEST
	else
	{
		//Delete guest rules
        guest_del_rule();
	}
}
else if ( $ACTION == "CHANGE")
{	
	$actionpath= "security/parental/action";
	$laststate = query($actionpath."/LastState");
	$nowstate  = query($actionpath."/NowState");
	$target    = query($actionpath."/target");
	if($target == "GUEST")
	{
		//delete guest iptables rules
		guest_del_rule();

		guest_add_rule();
	}
	else
	{
		//sch--->daily should delete schedule rule and add daily rule
		if($laststate == "1" && $nowstate == "2")
		{
			//Delete old schedule rules
    		sch_del_rule($MAC);
			//Add new daily rule
			daily_add_rule($DAY,$MAC,$MARK);
		}
		// daily--->sch should delete daily rule and add schedule rule
		else if($laststate == "2" && $nowstate == "1")
		{   
    	    //no trigger, delete daily rule
			daily_del_rule($MAC,$actionpath);
    	    //add schedule rules
	        sch_add_rule($MAC,$MARK,$MARK2);
		}
		
		//change parental control schedule time
		else if($laststate == "1" && $nowstate == "1")
		{
			//delete old schedule rule
            sch_del_rule($MAC);
			//add new schedule rule
			sch_add_rule($MAC,$MARK,$MARK2);
		}
		//change parental control daily time
		else if($laststate == "2" && $nowstate == "2")
		{
			daily_del_rule($MAC,$actionpath);

			daily_add_rule($DAY,$MAC,$MARK);
		}
		else{file_write("echo CAHNGE-no state!!");}

	}
}
else if ( $ACTION == "START")
{
	if($MAC == "GUEST")
	{
		guest_add_rule();
	}
	else
	{
		$path = XNODE_getpathbytarget("", "security/parental/DeviceList/entry", "mac", $MAC, 0);	
		if(query($path."/type") == "1")
        {
			sch_add_rule($MAC,$MARK,$MARK2);
		}
		else if(query($path."/type") == "2")
		{
			daily_add_rule($DAY,$MAC,$MARK);
		}
	}
}

else if ( $ACTION == "TIMESOUT")
{
	$path = XNODE_getpathbytarget("", "security/parental/DeviceList/entry", "mac", $MAC, 0);
	//Timesup!! Delete accept rules and add drop rules
	daily_del_rule($MAC,$path);
    daily_add_rule($DAY,$MAC,$MARK2);
}

else{ file_write("echo ACTION-No this action!!");}

file_write("exit 0");
?>
