#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

	$Device_sd = substr($REMOVE_WD,0,3);//sda
	echo '#Device_sd= '.$Device_sd.'\n';
	$DEVICE_SD = "/dev/".''.$Device_sd; // /dev/sda
	echo '#DEVICE_SD= '.$DEVICE_SD.'\n';

	$hd = "/runtime/wd/USB1";
	echo '#hd= '.$hd.'\n';
	if($ACTION == "remove")
	{	
		$i = 1;
		$cnt = query($hd."/entry#");
		echo '#cnt= '.$cnt.'\n';
		while($i <= $cnt)
		{	
			echo '#i= '.$i.'\n';
			$Device_hd  = query($hd."/entry:".$i."/SD_DEVICE");
			$SN_hd  = query($hd."/entry:".$i."/serial_number");
			$REVISION_hd = query($hd."/entry:".$i."/revision");
			
			echo '#Device_hd= '.$Device_hd.'\n';
			echo '#DEVICE_SD= '.$DEVICE_SD.'\n';

			if($Device_hd == $DEVICE_SD)
			{	
				$j = 1;
				$cnt = query($hd."/entry#");
				echo '#cnt= '.$cnt.'\n';
				while($j <= $cnt)
				{
					echo '#j= '.$j.'\n';
					$SN1_hd  = query($hd."/entry:".$j."/serial_number");
					$REVISION1_hd  = query($hd."/entry:".$j."/revision");

					echo '#$SN_hd= '.$SN_hd.'\n';
					echo '#$SN1_hd= '.$SN1_hd.'\n';

					if($SN_hd == $SN1_hd && $REVISION_hd == $REVISION1_hd )
					{
						del($hd."/entry:".$j);
						$cnt = query($hd."/entry#");
						echo '#cnt_1= '.$cnt.'\n';
					}
					else
					{
						$j++;
					}
				}
				
			}
			$i++;
		}
	}
	
	
	$hd = "/runtime/wd/USB2";
	echo '#hd= '.$hd.'\n';
	if($ACTION == "remove")
	{	
		$j = 1;
		$cnt = query($hd."/entry#");
		echo '#cnt= '.$cnt.'\n';
		while($j <= $cnt)
		{	
			echo '#j= '.$j.'\n';
			$Device_hd  = query($hd."/entry:".$j."/SD_DEVICE");
			$SN_hd  = query($hd."/entry:".$j."/serial_number");
			$REVISION_hd = query($hd."/entry:".$j."/revision");

			if($Device_hd == $DEVICE_SD)
			{	
				$j = 1;
				$cnt = query($hd."/entry#");
				echo '#cnt= '.$cnt.'\n';
				while($j <= $cnt)
				{
					echo '#j= '.$j.'\n';
					$SN1_hd  = query($hd."/entry:".$j."/serial_number");
					$REVISION1_hd  = query($hd."/entry:".$j."/revision");

					echo '#$SN_hd= '.$SN_hd.'\n';
					echo '#$SN1_hd= '.$SN1_hd.'\n';

					if($SN_hd == $SN1_hd && $REVISION_hd == $REVISION1_hd )
					{
						del($hd."/entry:".$j);
						$cnt = query($hd."/entry#");
						echo '#cnt_1= '.$cnt.'\n';
					}
					else
					{
						$j++;
					}
				}
				
			}
			$j++;
		}
	}

?>
