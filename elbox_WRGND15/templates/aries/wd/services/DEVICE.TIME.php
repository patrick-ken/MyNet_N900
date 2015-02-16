<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/trace.php";


function operator_append($op, $num)
{		
	if($op == "-")
	{
		if($num > 0)
		{
			$num = "-".$num;	
		}		
	}	
	return $num;
}

function operator_append2($op, $num)
{
	if($op == "+")
	{
		$num = "-".$num;
	}
	return $num;
}

function gen_two_digit($num)
{		
	if($num < 0)
	{
		$num = substr($num, 1, 2); //skip "-";
	}	
	if($num < 10) $num = "0".$num;
	
	return $num;
}	

fwrite(w, $START, "#!/bin/sh\n");
fwrite(w, $STOP,  "#!/bin/sh\n");

$DEBUG = 0;

/* Create /etc/TZ */
$index = query("/device/time/timezone");

if(query("/device/time/ForwardZone")!="1")
{
	if($index >= 14 && $index <= 26)
	{
		$index = $index + 1;
	}
	else if($index >= 27 && $index <= 38)
	{
		$index = $index + 2;
	}
	else if($index >= 42 && $index <= 43)
	{
		$index = $index + 2;
	}
	else if($index >= 59 && $index <= 61)
	{
		$index = $index + 3;
	}
	else if($index >= 55 && $index <= 57)
	{
		$index = $index + 4;
	}
	else if($index >= 67 && $index <= 71)
	{
		$index = $index + 5;
	}
	else if($index >= 72 && $index <= 73)
	{
		$index = $index + 6;
	}
	else
	{
		if($index==39 ||$index==40 ||$index==44 ||$index==45 ||$index==48 ||$index==49 ||$index==54)
		{//offset 3
			$index = $index + 3;
		}
		else if($index==46 ||$index==50 ||$index==52 ||$index==65 ||$index==66)
		{//offset 4
			$index = $index + 4;
		}
		else if($index==41 ||$index==62 ||$index==63)
		{//offset 5
			$index = $index + 5;
		}
		else if($index==51 ||$index==64 ||$index==74 ||$index==75)
		{//offset 7
			$index = $index + 7;
		}
		else if($index==47 ||$index==58)
		{//offset 8
			$index = $index + 8;
		}
	}
	set("/device/time/ForwardZone","1");
	set("/device/time/timezone",$index);
}
anchor("/runtime/services/timezone/zone:".$index);

/* Set and Save the TZ status */
set("/runtime/device/timezone/index", $index);
set("/runtime/device/timezone/name",  query("name"));

$TZ = get("s","gen");
if (query("/device/time/dst")=="1")
{	
	set("/runtime/device/timezone/dst", "1");
	
	//sam_pan add
	
	$dstmanual = query("/device/time/dstmanual");
	$dstoffset = query("/device/time/dstoffset");
		
	$gmttime = substr($TZ, 3, strlen($TZ)-3);
	$op1 = substr($gmttime,0,1);
	$h1  = substr($gmttime,1,2);
	$h1  = operator_append($op1, $h1);		
	$m1  = substr($gmttime,4,2);
	$m1  = operator_append($op1, $m1);
			
	$op2 = substr($dstoffset,0,1);
	$h2  = substr($dstoffset,1,2);
	$h2  = operator_append2($op2, $h2);	
	$m2  = substr($dstoffset,4,2);
	$m2  = operator_append2($op2, $m2);
			
	if($DEBUG==1)
	{									
		echo "GMT".$op1." ".$h1." ".$m1."\n";
		echo "GDT".$op2." ".$h2." ".$m2."\n";
	}
					
	$h = $h1+$h2;
	$m = $m1+$m2;
	
	if($DEBUG==1)
	{
		echo "compute: ->".$h." ".$m."\n";
	}	
	
	if($m > 0 && $h < 0) $h++; 			
	if($m < 0 && $h > 0) $h--;
	if($m <= 0) {$m = gen_two_digit($m);}
			
	if($m >= 60)
	{				
		$a = 0;
		if($h < 0) 
		{
			$a = "-1";
		}
		else 
		{
			$a =" 1";
		}
		$h= $h + $a;
		$m = $m - 60;
		if($m <= 0) {$m = gen_two_digit($m);}
	}
			
	if($h >= 0)
		{ $op3 = "+";}
	else
		{ $op3 = "-"; }		
	
	$h = gen_two_digit($h);						
	$dstoffset = $op3.$h.":".$m;		
	if($DEBUG==1)
	{
		echo "result:".$dstoffset."\n";		
	}		
	
	$TZ = $TZ."GDT".$dstoffset.query("/device/time/dstmanual");
	echo $TZ."\n";
}
else 
{
	set("/runtime/device/timezone/dst", "0");
}

$ntp_enable = query("/device/time/ntp/enable");

/* Originally add by Kloat. */
$tmp_date = query("/runtime/device/tmp_date"); del("/runtime/device/tmp_date");
$tmp_time = query("/runtime/device/tmp_time"); del("/runtime/device/tmp_time");
if($ntp_enable==0)
{
   $tmp_date = query("/device/time/date");
   $tmp_time = query("/device/time/time");
}
if ($tmp_date!="") set("/runtime/device/date", $tmp_date);
if ($tmp_time!="") set("/runtime/device/time", $tmp_time);
set("/runtime/device/timestate", "SUCCESS");

/* Manually set the date, clear NTP status. */
if ($tmp_date!="" || $tmp_time!="")
{
	set("/runtime/device/ntp/state", "MANUAL");
	set("/runtime/device/ntp/uptime", "");
	set("/runtime/device/ntp/server", "");
	set("/runtime/device/ntp/nexttime", "");
}

/* NTP ... */
$enable = query("/device/time/ntp/enable");
if($enable=="") $enable = 0;
$enablev6 = query("/device/time/ntp6/enable");
if($enablev6=="") $enablev6 = 0;
$server = query("/device/time/ntp/server");
$period = query("/device/time/ntp/period");	if ($period=="") $period="86400";
$period6 = query("/device/time/ntp6/period");	if ($period6=="") $period6="86400";
$ntp_run = "/var/run/ntp_run.sh";
if(query("/security/active")=="1" && query("/security/parental/enable")=="1")
{
	fwrite(a, $START,'iptables -D FORWARD -j FWD.PCTL-1\n');		//NTP on or off delete jump.
	fwrite(a, $START,'iptables -D FORWARD -j FWD.PCTL-2\n');
	fwrite(a, $START,'ip6tables -D FORWARD -j FWD.PCTL-1\n');        //NTP on or off delete jump.
	fwrite(a, $START,'ip6tables -D FORWARD -j FWD.PCTL-2\n');
}

if ($enable==1 && $enablev6==1)
{
	if ($server=="") fwrite(a, $START, 'echo "No NTP server, disable NTP client ..." > /dev/console\n');
	else
	{
		fwrite(w, $ntp_run, '#!/bin/sh\n');
		fwrite(a, $ntp_run,
			'echo "Run NTP client ..." > /dev/console\n'.
			'echo [$1] [$2] > /dev/console\n'.
			'STEP=$1\n'.
			'RESULT="Null"\n'.
			'xmldbc -s /runtime/device/ntp/state RUNNING\n'.
			'SERVER4='.$server.'\n'.
			'SERVER6=`xmldbc -g /runtime/device/ntp6/server | cut -f 1 -d " "`\n'.
			'if [ "$STEP" == "V4" ]; then\n'.
			'	xmldbc -t "ntp:'.$period.':'.$ntp_run.' $STEP"\n'.
			'	echo "ntpclient -h $SERVER4 -i 5 -s" > /dev/console\n'.
			'	ntpclient -h $SERVER4 -i 5 -s > /dev/console\n'.
			'	if [ $? != 0 ]; then\n'.
			'		xmldbc -k ntp\n'.
			'		xmldbc -t "ntp:10:'.$ntp_run.' V6"\n'.
			'		echo NTP4 will run in 10 seconds! > /dev/console\n'.
			'	else\n'.
			'		RESULT="OK"\n'.
			'	fi\n'.
			'elif [ "$SERVER6" != "" ] && [ "$STEP" == "V6" ];then\n'.
			'   xmldbc -t "ntp:'.$period6.':'.$ntp_run.' $STEP"\n'.
			'   echo "ntpclient -h $SERVER6 -i 5 -s" > /dev/console\n'.
			'   ntpclient -h $SERVER6 -i 5 -s > /dev/console\n'.
			'	if [ $? != 0 ]; then\n'.
			'		xmldbc -k ntp\n'.
			'		xmldbc -t "ntp:10:'.$ntp_run.' V4"\n'.
			'		echo NTP4 will run in 10 seconds! > /dev/console\n'.
			'	else\n'.
			'		RESULT="OK"\n'.
			'	fi\n'.
			'fi\n'.
			'if [ $RESULT == "OK" ]; then\n'.
			'	echo NTP will run in '.$period.' seconds! > /dev/console\n'.
			'	sleep 1\n'.
			'	UPTIME=`xmldbc -g /runtime/device/uptime`\n'.
			'	if [ "$STEP" == "V4" ]; then\n'.
			'		xmldbc -s /runtime/device/ntp/state SUCCESS\n'.
			'		xmldbc -s /runtime/device/ntp/uptime "$UPTIME"\n'.
			'		xmldbc -s /runtime/device/ntp/period '.$period.'\n'.
			'		xmldbc -s /runtime/device/ntp/server '.$server.'\n'.
			'	elif [ "$STEP" == "V6" ]; then\n'.
			'		xmldbc -s /runtime/device/ntp6/state SUCCESS\n'.
			'		xmldbc -s /runtime/device/ntp6/uptime "$UPTIME"\n'.
			'		xmldbc -s /runtime/device/ntp6/period '.$period6.'\n'.
			'		xmldbc -s /runtime/device/ntp6/server "$SERVER6"\n'.
			'	fi\n'.
			'	service schedule on\n'.
			'	PARENTCTRLrestart=`xmldbc -g /runtime/device/ParentControlRestart`\n'.
			'	if [ "$PARENTCTRLrestart" == "1" ]; then\n'.
			'		echo System is using Parental Control Block function and service should restart! > /dev/console\n'.
			'		xmldbc -P /etc/scripts/PARENTCTRLSCHL_TIME.php > /var/run/PARENTCTRLSCHL_RESTART.sh\n'.
			'		sh /var/run/PARENTCTRLSCHL_RESTART.sh  > /dev/console\n'.
			'		xmldbc -s /runtime/device/ParentControlRestart "0"\n'.
			'	fi\n'.
			'fi\n'
			);
		fwrite(a, $ntp_run, 'echo "'.$TZ.'" > /etc/TZ\n');
		fwrite(a, $START, 'chmod +x '.$ntp_run.'\n');
		fwrite(a, $START, $ntp_run.' V4 > /dev/console &\n'); //default from 'V4'

		fwrite(a, $STOP,
			'xmldbc -k ntp\n'.
			'killall ntpclient\n'.
			'sleep 1\n'.
			'xmldbc -k ntp\n'.
			'xmldbc -s /runtime/device/ntp/state STOPPED\n'.
			'xmldbc -s /runtime/device/ntp/period ""\n'.
			'xmldbc -s /runtime/device/ntp/nexttime ""\n'
			);
	}
}
else if ($enable==1 && $enablev6==0)
{
	if ($server=="") fwrite(a, $START, 'echo "No NTP server, disable NTP client ..." > /dev/console\n');
	else
	{
		fwrite(w, $ntp_run, '#!/bin/sh\n');
		fwrite(a, $ntp_run,
			'echo "Run NTP client ..." > /dev/console\n'.
			'xmldbc -s /runtime/device/ntp/state RUNNING\n'.
			'xmldbc -t "ntp:'.$period.':'.$ntp_run.'"\n'.
			'ntpclient -h '.$server.' -i 5 -s > /dev/console\n'.
			'if [ $? != 0 ]; then\n'.
			'	xmldbc -k ntp\n'.
			'	xmldbc -t "ntp:10:'.$ntp_run.'"\n'.
			'	echo NTP will run in 10 seconds! > /dev/console\n'.
			'	xmldbc -s /runtime/device/ntp/state FAILED\n'.
			'else\n'.
			'	echo NTP will run in '.$period.' seconds! > /dev/console\n'.
			'	sleep 1\n'.
			'	xmldbc -s /runtime/device/ntp/state SUCCESS\n'.
			'	PARENTCTRLactive=`xmldbc -g /security/active`\n'.
			'	PARENTCTRLenable=`xmldbc -g /security/parental/enable`\n'.
			'	if [ "$PARENTCTRLactive" == "1" ] && [ "$PARENTCTRLenable" == "1" ]; then\n'.
			'		iptables -A FORWARD -j FWD.PCTL-1\n'.
			'		iptables -A FORWARD -j FWD.PCTL-2\n'.
			'		ip6tables -A FORWARD -j FWD.PCTL-1\n'.
			'		ip6tables -A FORWARD -j FWD.PCTL-2\n'.
			'	fi\n'.
			'	UPTIME=`xmldbc -g /runtime/device/uptime`\n'.
			'	xmldbc -s /runtime/device/ntp/uptime "$UPTIME"\n'.
			'	xmldbc -s /runtime/device/ntp/period '.$period.'\n'.
			'	xmldbc -s /runtime/device/ntp/server '.$server.'\n'.
			'	service schedule on\n'.
			'	PARENTCTRLrestart=`xmldbc -g /runtime/device/ParentControlRestart`\n'.
			'	if [ "$PARENTCTRLrestart" == "1" ]; then\n'.
			'		echo System is using Parental Control Block function and service should restart! > /dev/console\n'.
			'		xmldbc -P /etc/scripts/PARENTCTRLSCHL_TIME.php > /var/run/PARENTCTRLSCHL_RESTART.sh\n'.
			'		sh /var/run/PARENTCTRLSCHL_RESTART.sh  > /dev/console\n'.
			'		xmldbc -s /runtime/device/ParentControlRestart "0"\n'.
			'	fi\n'.
			'fi\n'
			);
		
		fwrite(a, $ntp_run, 'echo "'.$TZ.'" > /etc/TZ\n');			
		fwrite(a, $START, 'chmod +x '.$ntp_run.'\n');
		fwrite(a, $START, $ntp_run.' > /dev/console &\n');

		fwrite(a, $STOP,
			'xmldbc -k ntp\n'.
			'killall ntpclient\n'.
			'sleep 1\n'.
			'xmldbc -k ntp\n'.
			'xmldbc -s /runtime/device/ntp/state STOPPED\n'.
			'xmldbc -s /runtime/device/ntp/period ""\n'.
			'xmldbc -s /runtime/device/ntp/nexttime ""\n'
			);
	}
}
else if ($enable==0 && $enablev6==1)
{
	fwrite(w, $ntp_run, '#!/bin/sh\n');
	fwrite(a, $ntp_run,
		'echo "Run NTP6 client ..." > /dev/console\n'.
		'xmldbc -s /runtime/device/ntp6/state RUNNING\n'.
		'SERVER6=`xmldbc -g /runtime/device/ntp6/server | cut -f 1 -d " "`\n'.
		'xmldbc -t "ntp:'.$period6.':'.$ntp_run.'"\n'.
		'[ "$SERVER6" != "" ] && ntpclient -h $SERVER6 -i 5 -s > /dev/console\n'.
		'if [ $? != 0 ]; then\n'.
		'	xmldbc -k ntp\n'.
		'	xmldbc -t "ntp:10:'.$ntp_run.'"\n'.
		'	echo NTP6 will run in 10 seconds! > /dev/console\n'.
		'else\n'.
		'	echo NTP6 will run in '.$period6.' seconds! > /dev/console\n'.
		'	sleep 1\n'.
		'	xmldbc -s /runtime/device/ntp6/state SUCCESS\n'.
		'	UPTIME=`xmldbc -g /runtime/device/uptime`\n'.
		'	xmldbc -s /runtime/device/ntp6/uptime "$UPTIME"\n'.
		'	xmldbc -s /runtime/device/ntp6/period '.$period6.'\n'.
		'	xmldbc -s /runtime/device/ntp6/server "$SERVER6"\n'.
		'	service schedule on\n'.
		'fi\n'
		);
		fwrite(a, $ntp_run, 'echo "'.$TZ.'" > /etc/TZ\n');
	fwrite(a, $START, 'chmod +x '.$ntp_run.'\n');
	fwrite(a, $START, $ntp_run.' > /dev/console &\n');

	fwrite(a, $STOP,
		'xmldbc -k ntp\n'.
		'killall ntpclient\n'.
		'sleep 1\n'.
		'xmldbc -k ntp\n'.
		'xmldbc -s /runtime/device/ntp/state STOPPED\n'.
		'xmldbc -s /runtime/device/ntp/period ""\n'.
		'xmldbc -s /runtime/device/ntp/nexttime ""\n'
		);
}
else
{
	fwrite(a, $START, 'echo "NTP is disabled ..." > /dev/console\n');
	fwrite(a, $START, 'echo "'.$TZ.'" > /etc/TZ\n');
	if(query("/runtime/phyinf:3/linkstatus") != "")
	{
		if(query("/security/active")=="1" && query("/security/parental/enable")=="1")
        {
			fwrite(a, $START,'iptables -A FORWARD -j FWD.PCTL-1\n');
			fwrite(a, $START,'iptables -A FORWARD -j FWD.PCTL-2\n');
			fwrite(a, $START,'ip6tables -A FORWARD -j FWD.PCTL-1\n');
        	fwrite(a, $START,'ip6tables -A FORWARD -j FWD.PCTL-2\n');
		}
	}
}


?>
