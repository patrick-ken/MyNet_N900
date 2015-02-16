<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

if(query("/security/active")=="1")
{
    if(query("/security/parental/enable")=="1")
	{
		//check is initialize or already turn on function
	    if(isfile("/var/run/accesstimectl.pid")!=1)
	    {//is initialize(OFF --> ON) or router just power on
			fwrite("a", $START, "echo \"=============\" > /dev/console\n");
		    fwrite("a", $START, "echo \"Execute accesstimectl\" > /dev/console\n");
		    fwrite("a", $START, "accesstimectl -d &\n");
			fwrite("a", $START, "insmod lib/modules/accesstime_monitor.ko\n");
		    fwrite("a", $START, "sleep 1\n");

			fwrite("a", $STOP, "echo \"=============\" > /dev/console\n");
			fwrite("a", $STOP, "echo \"Calling stop service\" > /dev/console\n");
			fwrite("a", $STOP, "echo \"Initialize event settings\" > /dev/console\n");
			fwrite("a", $STOP, "event BLOCK.ADD add true\n");
			fwrite("a", $STOP, "event BLOCK.DEL add true\n");
			fwrite("a", $STOP, "event BLOCK.CHANGE add true\n");
			fwrite("a", $STOP, "echo \"=============\" > /dev/console\n");
			fwrite("a", $STOP, "killall accesstimectl\n");
			fwrite("a", $STOP, "rm /var/run/accesstimectl.pid\n");
			fwrite("a", $STOP, "rmmod lib/modules/accesstime_monitor.ko\n");
			fwrite("a", $STOP, "iptables -F PCTL.GUEST\n");
			fwrite("a", $STOP, "iptables -F PCTL.SCH\n");
			fwrite("a", $STOP, "iptables -F PCTL.DAILY\n");
			fwrite("a", $STOP, "ip6tables -F PCTL.SCH\n");
			fwrite("a", $STOP, "ip6tables -F PCTL.DAILY\n");

 		    $guest = "/security/parental/guest";
			if(query($guest."/type")== "1")
			{
				fwrite("a", $START, "event PCTL.START add \"usockc /var/accesstimectl PCTL.START_GUEST\"\n");
				fwrite("a", $START, "event PCTL.START\n");
			}
		    foreach("/security/parental/DeviceList/entry")
		    {
			    $BlockType = query("type");
			    $mac       = query("mac");
			    if($BlockType=="1")
			    {
			        fwrite("a", $START, "event PCTL.START add \"usockc /var/accesstimectl PCTL.START_".$mac."_sdule\"\n");
			        fwrite("a", $START, "event PCTL.START\n");
			    }
			    else if($BlockType=="2")
				{
					fwrite("a", $START, "event PCTL.START add \"usockc /var/accesstimectl PCTL.START_".$mac."_daily\"\n");
				    fwrite("a", $START, "event PCTL.START\n");
				}
				else
				{
					fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
			        fwrite("a", $START, "echo \"Parental Scheduling is initialized and turn ON.\" > /dev/console\n");
			        fwrite("a", $START, "echo \"DeviceList entry Block Type is unknown.\" > /dev/console\n");
			        fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
				}
			}
		}
	}
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
