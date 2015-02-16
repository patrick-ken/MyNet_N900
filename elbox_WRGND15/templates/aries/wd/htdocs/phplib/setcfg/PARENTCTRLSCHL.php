<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
include "/htdocs/phplib/trace.php"; 
TRACE_debug("==================================");
TRACE_debug("==================================");
TRACE_debug("=========we are in setcfg=========");
$path = "/security/parental";
while(isfile("/var/IN_SAVEING")==1)
{
	TRACE_debug("==================================");
	TRACE_debug("Wait other service do saving......");
	TRACE_debug("==================================");
}
if(isfile("/var/IN_SAVEING")==0)
{
	fwrite("w+", "/var/IN_SAVEING", "1");
}

if(query($SETCFG_prefix."/security/active")=="1" && query($SETCFG_prefix.$path."/enable")=="1")
{
	foreach($path."/DeviceList/entry")
	{
		if(query("mac") == "")
		{
			TRACE_debug("Do Error handler!");
			TRACE_debug("Do Error handler!");
			TRACE_debug("Do Error handler!");
			del($path."/DeviceList/entry:".$InDeX);
			break;
		}
	}
	set("/security/netstar/enable", "0");
	set($path."/enable", "1");
	set("/security/active", "1");
	$target = query($SETCFG_prefix.$path."/action/target");
	TRACE_debug("target is: ".$target);
	$nowState = query($SETCFG_prefix.$path."/action/NowState");
	TRACE_debug("nowState is: ".$nowState);
	$lastState = query($SETCFG_prefix.$path."/action/LastState");
	TRACE_debug("lastState is: ".$lastState);
	if($target=="GUEST")
	{
		TRACE_debug("Maintain GUEST DB node.");
		set($path."/guest/","");
		movc($SETCFG_prefix.$path."/guest/" , $path."/guest/");
		TRACE_debug("Maintain action DB node.");
		set($path."/action/","");
		movc($SETCFG_prefix.$path."/action/", $path."/action/");
	}
	else if($target != "" && $nowState != "" && $lastState != "")
	{
		TRACE_debug("Get DeviceList entry number.");
		$idx = 0;
		foreach($SETCFG_prefix.$path."/DeviceList/entry")
		{
			TRACE_debug("$InDeX is: ".$InDeX);
			TRACE_debug("query(mac) is: ".query("mac"));
			if(query("mac") == $target)
			{
				$idx = $InDeX;
				break;
			}
		}
		if($idx==0)
		{
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("Get entry number error.");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			TRACE_debug("*********error*********");
			unlink("/var/IN_SAVEING");
			return;
		}
		TRACE_debug("Entry number is: ".$idx);

		if($lastState=="0")
		{
			TRACE_debug("Add entry to DB node.");
			add($path."/DeviceList/entry:".$idx,"");
			movc($SETCFG_prefix.$path."/DeviceList/entry:".$idx, $path."/DeviceList/entry:".$idx);
			TRACE_debug("Maintain action DB node.");
			set($path."/action/","");
			movc($SETCFG_prefix.$path."/action/", $path."/action/");
		}
		else if($lastState=="1")
		{
			TRACE_debug("Maintain action DB node.");
			set($SETCFG_prefix.$path."/action/acceptMark" , query($path."/DeviceList/entry:".$idx."/acceptMark"));
			set($SETCFG_prefix.$path."/action/dropMark" , query($path."/DeviceList/entry:".$idx."/dropMark"));
			set($SETCFG_prefix.$path."/action/trigger" , query($path."/DeviceList/entry:".$idx."/trigger"));
			set($path."/action/","");
			movc($SETCFG_prefix.$path."/action/", $path."/action/");
			if($nowState=="0")
			{//event BLOCK.DEL
				TRACE_debug("DELETE entry from DB node.");
				del($path."/DeviceList/entry:".$idx);
			}
			else if($nowState=="1")
			{//event BLOCK.CHANGE
				TRACE_debug("Maintain entry DB node.");
				set($path."/DeviceList/entry:".$idx."/days" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/days"));
				set($path."/DeviceList/entry:".$idx."/start" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/start"));
				set($path."/DeviceList/entry:".$idx."/end" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/end"));
				set($path."/DeviceList/entry:".$idx."/weekday" , "");
				set($path."/DeviceList/entry:".$idx."/weekend" , "");
				set($path."/DeviceList/entry:".$idx."/trigger" , "0");
			}
			else if($nowState=="2")
			{//event BLOCK.CHANGE
				TRACE_debug("Maintain entry DB node.");
				set($path."/DeviceList/entry:".$idx."/type" , "2");
				set($path."/DeviceList/entry:".$idx."/days" , "");
				set($path."/DeviceList/entry:".$idx."/start" , "");
				set($path."/DeviceList/entry:".$idx."/end" , "");
				set($path."/DeviceList/entry:".$idx."/acceptMark" , "");
				set($path."/DeviceList/entry:".$idx."/dropMark" , "");
				set($path."/DeviceList/entry:".$idx."/trigger" , "0");
				set($path."/DeviceList/entry:".$idx."/weekday" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/weekday"));
				set($path."/DeviceList/entry:".$idx."/weekend" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/weekend"));
			}
		}
		else if($lastState=="2")
		{
			TRACE_debug("Entry number is: ".$idx);
			TRACE_debug("Maintain action DB node.");
			set($SETCFG_prefix.$path."/action/acceptMark" , query($path."/DeviceList/entry:".$idx."/acceptMark"));
			set($SETCFG_prefix.$path."/action/dropMark" , query($path."/DeviceList/entry:".$idx."/dropMark"));
			set($SETCFG_prefix.$path."/action/trigger" , query($path."/DeviceList/entry:".$idx."/trigger"));
			set($SETCFG_prefix.$path."/action/start" , query($path."/DeviceList/entry:".$idx."/start"));
			set($SETCFG_prefix.$path."/action/end" , query($path."/DeviceList/entry:".$idx."/end"));
			set($path."/action/","");
			movc($SETCFG_prefix.$path."/action/", $path."/action/");
			if($nowState=="0")
			{
				TRACE_debug("DELETE entry from DB node.");
				del($path."/DeviceList/entry:".$idx);
			}
			else if($nowState=="1")
			{
				TRACE_debug("Maintain entry DB node.");
				set($path."/DeviceList/entry:".$idx."/type" , "1");
				set($path."/DeviceList/entry:".$idx."/days" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/days"));
				set($path."/DeviceList/entry:".$idx."/start" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/start"));
				set($path."/DeviceList/entry:".$idx."/end" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/end"));
				set($path."/DeviceList/entry:".$idx."/trigger" , "0");
				set($path."/DeviceList/entry:".$idx."/weekday" , "");
				set($path."/DeviceList/entry:".$idx."/weekend" , "");
				set($path."/DeviceList/entry:".$idx."/acceptMark" , "");
				set($path."/DeviceList/entry:".$idx."/dropMark" , "");
			}
			else if($nowState=="2")
			{
				TRACE_debug("Maintain entry DB node.");
				set($path."/DeviceList/entry:".$idx."/weekday" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/weekday"));
				set($path."/DeviceList/entry:".$idx."/weekend" , query($SETCFG_prefix.$path."/DeviceList/entry:".$idx."/weekend"));
			}
		}
	}
	else
	{
		TRACE_debug("$target is empty or $nowState is empty or $lastState is empty");
	}
}
else
{
    foreach($path."/DeviceList/entry") 
    { 
        $BlockType = query("type"); 
        if($BlockType==2) 
        { 
            set("trigger","0"); 
            set("start",""); 
            set("end",""); 
			set("acceptMark","");
			set("dropMark","");
        } 
    }
	set("/security/active", "0");
	set($path."/action/target", "");
	set($path."/action/LastState", "");
	set($path."/action/NowState", "");
	set($path."/action/acceptMark", "");
	set($path."/action/dropMark", "");
	set($path."/action/trigger", "");
	set($path."/action/days", "");
	set($path."/action/start", "");
	set($path."/action/end", "");
}
unlink("/var/IN_SAVEING");
TRACE_debug("<security>");
TRACE_debug("	<parental>");
TRACE_debug("		<action>");
TRACE_debug("			<target>".query("security/parental/action/target")."</target>");
TRACE_debug("			<LastState>".query("security/parental/action/LastState")."</LastState>");
TRACE_debug("			<NowState>".query("security/parental/action/NowState")."</NowState>");
TRACE_debug("			<acceptMark>".query("security/parental/action/acceptMark")."</acceptMark>");
TRACE_debug("			<dropMark>".query("security/parental/action/dropMark")."</dropMark>");
TRACE_debug("			<trigger>".query("security/parental/action/trigger")."</trigger>");
TRACE_debug("			<days>".query("security/parental/action/days")."</days>");
TRACE_debug("			<start>".query("security/parental/action/start")."</start>");
TRACE_debug("			<end>".query("security/parental/action/end")."</end>");
TRACE_debug("		</action>");
TRACE_debug("	</parental>");
TRACE_debug("</security>");
foreach($path."/DeviceList/entry")
{
	if(query("mac") == "")
	{
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		TRACE_debug("ERROR!!");
		break;
	}
}
TRACE_debug("======we are leaving setcfg=======");
TRACE_debug("==================================");
TRACE_debug("==================================");
?>
