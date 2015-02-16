<?

function startcmd($cmd)	{fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)	{fwrite(a,$_GLOBALS["STOP"],  $cmd."\n");} 

$path = "/device/log/devlist/eventmgnt";
anchor($path);
$reg_st = query("/devlist/register_st");
$enable = query("pushevent/enable");
$new_dev = query("pushevent/types/userlogin");


function restart_arpmonitor($enable,$new_dev)
{
	
	if($enable == "1" && $new_dev == "1")
	{
		
		startcmd("if [ -f /var/run/arpmonitor.pid ]; then\n");
		startcmd("	echo \"arpmonitor is started ,do nothing\"\n");
		startcmd("else\n");
		startcmd( "arpmonitor -i br0 &\n");
		startcmd( "echo $$ > /var/run/arpmonitor.pid\n");
		startcmd("fi\n");  
	}
	else
	{
		startcmd( "killall arpmonitor\n");
		startcmd( "rm -f /var/run/arpmonitor.pid\n");
	}
	
}


/* Main */
fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n"); 
if($reg_st == "1")
{
	restart_arpmonitor($enable,$new_dev);
	startcmd( "service arpmonitor restart \n");
}
	
?>
