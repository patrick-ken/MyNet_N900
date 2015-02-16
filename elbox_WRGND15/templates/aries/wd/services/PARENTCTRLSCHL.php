<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
$lns_process_pid = "/var/run/lns_process.pid";
$ps_process_pid = "/var/run/ps_process.pid";
$at_process_pid = "/var/run/at_process.pid";
$netstar_pid="/var/run/netstar.pid";
fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

function DumpError($code)
{
	if($code==1)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"Parental Scheduling is initialized and turn ON.\" > /dev/console\n");
		fwrite("a", $START, "echo \"DeviceList entry Block Type is unknown.\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==2)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==3)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==4)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==5)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==6)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"Parental Scheduling is ON.\" > /dev/console\n");
		fwrite("a", $START, "echo \"Last state is NONE, but now state is unknown.\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==7)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"Parental Scheduling is ON.\" > /dev/console\n");
		fwrite("a", $START, "echo \"Now state is NONE, but last state is unknown.\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==8)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	else if($code==9)
	{
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
		fwrite("a", $START, "echo \"Security is turn ON, but Parental Scheduling is not enable.\" > /dev/console\n");
		fwrite("a", $START, "echo \"=====error=====\" > /dev/console\n");
	}
	fwrite("a",$START, "exit 0\n");
	fwrite("a",$STOP,  "exit 0\n");
}

fwrite("a", $STOP, "echo \"=============\" > /dev/console\n");
fwrite("a", $STOP, "echo \"Calling stop service\" > /dev/console\n");
fwrite("a", $STOP, "echo \"Initialize event settings\" > /dev/console\n");
fwrite("a", $STOP, "event BLOCK.ADD add true\n");
fwrite("a", $STOP, "event BLOCK.DEL add true\n");
fwrite("a", $STOP, "event BLOCK.CHANGE add true\n");
fwrite("a", $STOP, "echo \"=============\" > /dev/console\n");

if(query("/security/active")=="1")
{
	if(query("/security/parental/enable")=="1")
	{
		//stop netstar parental control
		fwrite("a", $START, "if [ -f \"".$ps_process_pid."\" ]; then\n");
		fwrite("a", $START, "echo \"Stop PS process ..\"  > /dev/console\n");
		fwrite("a", $START, "	pid=`cat \"".$ps_process_pid."\"`\n");
		fwrite("a", $START, "	if [ $pid != 0 ]; then\n");
		fwrite("a", $START, "		kill $pid \n");
		fwrite("a", $START, "	fi\n");
		fwrite("a", $START, "	rm -f \"".$ps_process_pid."\"\n");
		fwrite("a", $START, "fi\n");

		fwrite("a", $START, "echo \"Stoping NETSTAR ...\" > /dev/console\n");
		fwrite("a", $START, "killall  netstar \n");
		fwrite("a", $START, "rm -f ".$netstar_pid."\n");

		fwrite("a", $START, "if [ -f \"".$at_process_pid."\" ]; then\n");
		fwrite("a", $START, "echo \"Stop AT process ..\"  > /dev/console\n");
		fwrite("a", $START, "	pid=`cat \"".$at_process_pid."\"`\n");
		fwrite("a", $START, "	if [ $pid != 0 ]; then\n");
		fwrite("a", $START, "		kill $pid \n");
		fwrite("a", $START, "	fi\n");
		fwrite("a", $START, "	rm -f \"".$at_process_pid."\"\n");
		fwrite("a", $START, "fi\n");

		fwrite("a", $START, "if [ -f \"".$lns_process_pid."\" ]; then\n");
		fwrite("a", $START, "echo \"Stop  Start LNS process ..\"  > /dev/console\n");
		fwrite("a", $START, "	pid=`cat \"".$lns_process_pid."\"`\n");
		fwrite("a", $START, "	if [ $pid != 0 ]; then\n");
		fwrite("a", $START, "		kill $pid \n");
		fwrite("a", $START, "	fi\n");
		fwrite("a", $START, "	rm -f \"".$lns_process_pid."\"\n");
		fwrite("a", $START, "fi\n");	

		fwrite("a", $START, "service PROXYD stop\n");
		fwrite("a", $START, "service IPTPROXYD stop\n");
		/////
		//check is initialize or already turn on function
		if(isfile("/var/run/accesstimectl.pid")!=1)
		{//is initialize(OFF --> ON) or router just power on
			fwrite("a", $START, "echo \"=============\" > /dev/console\n");
			fwrite("a", $START, "echo \"Execute accesstimectl\" > /dev/console\n");
			fwrite("a", $START, "accesstimectl -d &\n");
			//parental control on will insert kernel module 
			fwrite("a", $START, "insmod lib/modules/accesstime_monitor.ko\n");  
			fwrite("a", $START, "sleep 1\n");

			if(query("/device/time/ntp/enable") == "0" || query("/runtime/device/ntp/state") == "SUCCESS")
			{
				fwrite("a", $START,'iptables -A FORWARD -j FWD.PCTL-1\n');
	            fwrite("a", $START,'iptables -A FORWARD -j FWD.PCTL-2\n');
	            fwrite("a", $START,'ip6tables -A FORWARD -j FWD.PCTL-1\n');
				fwrite("a", $START,'ip6tables -A FORWARD -j FWD.PCTL-2\n');
			}

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
					DumpError(1);
					return;
				}
			}
		}
		else
		{//already turn on function
			if(query("/security/parental/action/target")!="")
			{
				$lastS = query("/security/parental/action/LastState");
				$nowS = query("/security/parental/action/NowState");
				$target = query("/security/parental/action/target");
				$cmd = "usockc /var/accesstimectl ";
				if($lastS=="0")
				{//event BLOCK.ADD
					if($nowS=="1")
					{//None state -----> Block state
						$cmd = $cmd."BLOCK.ADD_".$target."_sdule";
					}
					else if($nowS=="2")
					{//None state -----> Block state
						$cmd = $cmd."BLOCK.ADD_".$target."_daily";
					}
					else
					{
						DumpError(6);
						return;
					}
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "echo BLOCK.ADD add \"".$cmd."\" > /dev/console\n");
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "event BLOCK.ADD add \"".$cmd."\"\n");
					fwrite("a", $START, "event BLOCK.ADD\n");
				}
				else if($nowS=="0")
				{//event BLOCK.DEL
					if($lastS=="1")
					{//Block state -----> None state
						$cmd = $cmd."BLOCK.DEL_".$target."_sdule";
					}
					else if($lastS=="2")
					{//Daily state -----> None state
						$cmd = $cmd."BLOCK.DEL_".$target."_daily";
					}
					else
					{
						DumpError(7);
						return;
					}
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "echo BLOCK.DEL add \"".$cmd."\" > /dev/console\n");
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "event BLOCK.DEL add \"".$cmd."\"\n");
					fwrite("a", $START, "event BLOCK.DEL\n");
				}
				else
				{//event BLOCK.CHANGE
					if($lastS=="1" && $nowS=="2")
					{//Block state -----> Daily state
						$cmd = $cmd."BLOCK.CHANGE_".$target."_sdule-daily";
					}
					else if($lastS=="2" && $nowS=="1")
					{//Daily state -----> Block state
						$cmd = $cmd."BLOCK.CHANGE_".$target."_daily-sdule";
					}
					else if($lastS=="1" && $nowS=="1")
					{//Block state -----> Block state
						$cmd = $cmd."BLOCK.CHANGE_".$target."_sdule-sdule";
					}
					else if($lastS=="2" && $nowS=="2")
					{//Daily state -----> Daily state
						$cmd = $cmd."BLOCK.CHANGE_".$target."_daily-daily";
					}
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "echo BLOCK.CHANGE add \"".$cmd."\" > /dev/console\n");
					fwrite("a", $START, "echo \"=============\" > /dev/console\n");
					fwrite("a", $START, "event BLOCK.CHANGE add \"".$cmd."\"\n");
					fwrite("a", $START, "event BLOCK.CHANGE\n");					
				}
			}
		}
	}
	else
	{
		DumpError(9);
		return;
	}
}
else
{
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
	fwrite("a", $START, "echo \"Disable Parental control block\" > /dev/console\n");
	fwrite("a", $START, "killall accesstimectl\n");
	fwrite("a", $START, "rm /var/run/accesstimectl.pid\n");
	//close parental control remove kernel module
	fwrite("a", $START, "rmmod lib/modules/accesstime_monitor.ko\n");
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
	fwrite("a", $START, "echo \"Flush relative iptables\" > /dev/console\n");
	fwrite("a", $START, "iptables -F PCTL.GUEST\n");
	fwrite("a", $START, "iptables -F PCTL.SCH\n");
	fwrite("a", $START, "iptables -F PCTL.DAILY\n");
    fwrite("a", $START, "ip6tables -F PCTL.SCH\n");
    fwrite("a", $START, "ip6tables -F PCTL.DAILY\n");
	fwrite("a", $START,'iptables -D FORWARD -j FWD.PCTL-1\n');
	fwrite("a", $START,'iptables -D FORWARD -j FWD.PCTL-2\n');
	fwrite("a", $START,'ip6tables -D FORWARD -j FWD.PCTL-1\n');
	fwrite("a", $START,'ip6tables -D FORWARD -j FWD.PCTL-2\n');
	fwrite("a", $START, "echo \"=============\" > /dev/console\n");
}

fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");

?>

