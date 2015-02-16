#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$base = "/runtime/wd/remove_sn";

if($CAL_SN == "0")
{
	$cnt = query($base."/entry#");
	if($cnt=="") $cnt=0;
	$cnt++;
	echo '#cnt= '.$cnt.'\n';

	set($base."/entry:".$cnt."/sn",$SER);
	set($base."/entry:".$cnt."/dev",$WD_DEVICE);
	set($base."/entry:".$cnt."/cal_sn","");
	set($base."/entry:".$cnt."/volume_name","");
}
else if ($CAL_SN == "1")
{
	$total_entry = query($base."/entry#");
	echo '#total_entry= '.$total_entry.'\n';

	$i = 1;
	while($i <= $total_entry)
	{
		echo '#i= '.$i.'\n';
		$SN_hd  = query($base."/entry:".$i."/sn");
		$DEV_hd = query($base."/entry:".$i."/dev");
		echo '#$SN_hd= '.$SN_hd.'\n';
		echo '#$SER= '.$SER.'\n';

		echo '#$WD_DEVICE= '.$WD_DEVICE.'\n';
		echo '#$DEV_hd= '.$DEV_hd.'\n';
		
		if($PID == "0"){
			$DEV_PID=$WD_DEVICE;
		}
		else{
			$DEV_PID=$WD_DEVICE.''.$PID;
		}
		echo '#$DEV_PID= '.$DEV_PID.'\n';

		if($DEV_PID == $DEV_hd)
		{
			echo '#$SER_1= '.$SER.'\n';
			set($base."/entry:".$i."/cal_sn",$TMP);
		}
		$i++;
	}
}
else if ($CAL_SN == "2")
{//use device(sda) to compare then find out the tmp of cal_sn.
	$Device_sd = substr($WD_DEVICE,0,3);//sda 	
	echo '#Device_sd= '.$Device_sd.'\n';

	if($PID == "0"){
		$DEV_PID=$WD_DEVICE;
	}
	else{
		$DEV_PID=$WD_DEVICE.''.$PID;
	}
	echo '#$DEV_PID= '.$DEV_PID.'\n';

	$total_entry = query($base."/entry#");
	echo '#total_entry= '.$total_entry.'\n';

	$i = 1;
	while($i <= $total_entry)
	{
		echo '#i= '.$i.'\n';
		$Device_hd  = query($base."/entry:".$i."/dev");
		echo '#$Device_hd= '.$Device_hd.'\n';
		if($DEV_PID == $Device_hd)
		{
			$tmp = query($base."/entry:".$i."/cal_sn");
			if($tmp != "")
			{
				echo '#$tmp='.$tmp.'\n';
				echo "echo ".$tmp." > /var/tmp/get_suffix\n";
			
				//delete database			
				del($base."/entry:".$i."\n");//ex:delete sda1
				$j = 1;
				$cnt = query($base."/entry#");
				echo '#cnt= '.$cnt.'\n';
				while($j <= $cnt) //ex: delete sda
				{
					echo '#j= '.$j.'\n';
					$Device_hd  = query($base."/entry:".$j."/dev");
					echo '#$Device_hd= '.$Device_hd.'\n';
					if($Device_hd == $Device_sd || $DEV_PID == $Device_hd)
					{
						 del($base."/entry:".$j."\n");	
						 $cnt = query($base."/entry#");
						 echo '#cnt_1= '.$cnt.'\n';
					}
					else
					{
						$j++;
					}
				}
			}
		}
		$i++;
	}
}
else if ($CAL_SN == "3")
{
	$total_entry = query($base."/entry#");
	echo '#total_entry= '.$total_entry.'\n';

	$i = 1;
	while($i <= $total_entry)
	{
		echo '#i= '.$i.'\n';
		$SN_hd  = query($base."/entry:".$i."/sn");
		$DEV_hd = query($base."/entry:".$i."/dev");
		echo '#$SN_hd= '.$SN_hd.'\n';

		echo '#$WD_DEVICE= '.$WD_DEVICE.'\n';
		echo '#$DEV_hd= '.$DEV_hd.'\n';
		
		if($PID == "0"){
			$DEV_PID=$WD_DEVICE;
		}
		else{
			$DEV_PID=$WD_DEVICE.''.$PID;
		}
		echo '#$DEV_PID= '.$DEV_PID.'\n';

		if($DEV_PID == $DEV_hd)
		{
			set($base."/entry:".$i."/volume_name",$VOLUMENAME);//jerry
		}
		$i++;
	}
}
?>

