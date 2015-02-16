<?
echo "#!/bin/sh\n";

if($REMOVE_ALL == "1")
{
	echo "service NETATALK stop\n";
	echo "sleep 2\n";
	$m = 1;
	$disk_hd = "/runtime/device/storage";
	$disk_cnt  = query($disk_hd."/disk#");
	echo '#disk_cnt = '.$disk_cnt.'\n';
	while($m <= $disk_cnt)
	{
		$n = 1;
		$disk_entry = query($disk_hd."/disk:".$m."/entry#");
		echo '#disk_entry= '.$disk_entry.'\n';

		while($n <= $disk_entry)
		{
			$disk_state = query($disk_hd."/disk:".$m."/entry:".$n."/state");
			$UID = query($disk_hd."/disk:".$m."/entry:".$n."/uid");
			echo '#UID= '.$UID.'\n';
			$uid = tolower($UID);
			echo '#uid= '.$uid.'\n';

			if($disk_state == "MOUNTED")
			{
				$mntp = query($disk_hd."/disk:".$m."/entry:".$n."/mntp");
				echo '#mntp= '.$mntp.'\n';
				echo "if [ -d ".$mntp." ]; then\n";
				echo "sync\n";
				echo "sdparm --command=sync /dev/".$uid."\n";
				echo "for i in 0 1 \n";
				echo "do\n";
				echo "sleep 1\n";
				echo "umount -l ".$mntp."\n";
				echo "if [ $? -eq 0 ]; then\n";
				echo "xmldbc -s ".$disk_hd."/disk:".$m."/entry:".$n."/state 'NOT MOUNTED'\n";
				echo "rmdir ".$mntp."\n";
				echo "break\n";
				echo "fi\n";
				echo "done\n";
				echo "fi\n";
			}
			$n++;
		}
		$m++;
		echo "hd-idle -t ".$uid."\n";
	}
}
else if($REMOVE_ALL == "0")
{

	echo "service NETATALK stop\n";
	echo "sleep 2\n";
			
	$hd = "/runtime/wd/USB1";
	$remove_hd = "/runtime/wd/remove";
	$disk_hd = "/runtime/device/storage";

	echo '#hd= '.$hd.'\n';

	$remove_entry = query($remove_hd."/entry#");
	if($remove_entry=="") $remove_entry=0;

	echo '#remove_entry= '.$remove_entry.'\n';
	$remove_cnt=0;
	$remove_cnt++;

	echo '#remove_cnt= '.$remove_cnt.'\n';

	while($remove_cnt <= $remove_entry)
	{
		$Model  = query($remove_hd."/entry:".$remove_cnt."/Vendor");
		$SN  = query($remove_hd."/entry:".$remove_cnt."/serial_number");
		$UNMOUNT  = query($remove_hd."/entry:".$remove_cnt."/unmount");

		echo '#Model= '.$Model.'\n';
		echo '#SN= '.$SN.'\n';
		echo '#UNMOUNT= '.$UNMOUNT.'\n';

		if($UNMOUNT == "0")
		{
			
			$i = 1;
			$hd_cnt = query($hd."/entry#");
			echo '#hd_cnt = '.$hd_cnt.'\n';
			$disk_cnt  = query($disk_hd."/disk#");
			echo '#disk_cnt = '.$disk_cnt.'\n';
			$point = 1;
			while($i <= $hd_cnt)
			{
				$Model_hd  = query($hd."/entry:".$point."/vendor");
				$SN_hd  = query($hd."/entry:".$point."/serial_number");

				echo '#Model_hd= '.$Model_hd.'\n';
				echo '#SN_hd= '.$SN_hd.'\n';

				if($Model == $Model_hd && $SN == $SN_hd)//found
				{
					$Device_hd  = query($hd."/entry:".$point."/SD_DEVICE");// /dev/sda
					echo '#Device_hd= '.$Device_hd.'\n';// /dev/sda	
					del($hd."/entry:".$point."\n");
					echo '#point= '.$point.'\n';
					set($remove_hd."/entry:".$remove_cnt."/SD_DEVICE",$Device_hd);
					$j = 1;
					while($j <= $disk_cnt)
					{
						$UID = query($disk_hd."/disk:".$j."/uid");
						echo '#UID= '.$UID.'\n';
						$uid = tolower($UID);
						echo '#uid= '.$uid.'\n';
					
				
						$Device_hd  = query($remove_hd."/entry:".$remove_cnt."/SD_DEVICE");// /dev/sda
						echo '#Device_hd= '.$Device_hd.'\n';// /dev/sda
						$Device_sd   = scut($Device_hd, 0, "/dev/");
						echo '#Device_sd= '.$Device_sd.'\n'; //sda

						if($Device_sd == $uid)
						{
							$k = 1;
							$disk_entry = query($disk_hd."/disk:".$j."/entry#");
							echo '#disk_entry= '.$disk_entry.'\n';
							while($k <= $disk_entry)
							{
								$disk_state = query($disk_hd."/disk:".$j."/entry:".$k."/state");

								if($disk_state == "MOUNTED")
								{			
									if($Device_sd == $uid)	
									{
										echo '#uid_3= '.$uid.'\n';
										echo '#Device_sd_3= '.$Device_sd.'\n'; //sda
										$mntp = query($disk_hd."/disk:".$j."/entry:".$k."/mntp");
										echo '#mntp= '.$mntp.'\n';
										set($disk_hd."/disk:".$j."/entry:".$k."/state","NOT MOUNTED");
										set($remove_hd."/entry:".$remove_cnt."/unmount","1");
										echo "if [ -d ".$mntp." ]; then\n";
										echo "sync\n";
										echo "sdparm --command=sync ".$Device_hd."\n";
										echo "for i in 0 1 \n";
										echo "do\n";
										echo "sleep 1\n";
										echo "umount -l ".$mntp."\n";
										echo "if [ $? -eq 0 ]; then\n";
										echo "rmdir ".$mntp."\n";									
										echo "break\n";
										echo "fi\n";
										echo "done\n";
										echo "fi\n";
									}
								}
								$k++;
							}
							echo "hd-idle -t ".$Device_sd."\n";
				  		}
						$j++;
			 		}		
				}
				else
				{
					$point++;
				}
				$i++;	
			}
			
		 }	
		 $remove_cnt++;
	}					
					
	echo '#USB2\n';
	//USB2
	$hd = "/runtime/wd/USB2";
	$remove_hd = "/runtime/wd/remove";
	$disk_hd = "/runtime/device/storage";
	
	echo '#hd= '.$hd.'\n';

	$remove_entry = query($remove_hd."/entry#");
	if($remove_entry=="") $remove_entry=0;
	$remove_cnt=0;
	echo '#remove_entry= '.$remove_entry.'\n';
	$remove_cnt++;

	echo '#remove_cnt= '.$remove_cnt.'\n';

	
	while($remove_cnt <= $remove_entry)
	{
		$Model  = query($remove_hd."/entry:".$remove_cnt."/Vendor");
		$SN  = query($remove_hd."/entry:".$remove_cnt."/serial_number");
		$UNMOUNT  = query($remove_hd."/entry:".$remove_cnt."/unmount");

		echo '#Model= '.$Model.'\n';
		echo '#SN= '.$SN.'\n';
		echo '#UNMOUNT= '.$UNMOUNT.'\n';

		if($UNMOUNT == "0")
		{
			
			$i = 1;
			$hd_cnt = query($hd."/entry#");
			echo '#hd_cnt = '.$hd_cnt.'\n';
			$disk_cnt  = query($disk_hd."/disk#");
			echo '#disk_cnt = '.$disk_cnt.'\n';
			$point = 1;
			while($i <= $hd_cnt)
			{
				$Model_hd  = query($hd."/entry:".$point."/vendor");
				$SN_hd  = query($hd."/entry:".$point."/serial_number");

				echo '#Model_hd= '.$Model_hd.'\n';
				echo '#SN_hd= '.$SN_hd.'\n';

				if($Model == $Model_hd && $SN == $SN_hd)//found
				{
					$Device_hd  = query($hd."/entry:".$point."/SD_DEVICE");// /dev/sda
					echo '#Device_hd= '.$Device_hd.'\n';// /dev/sda	
					del($hd."/entry:".$point."\n");
					echo '#point= '.$point.'\n';
					set($remove_hd."/entry:".$remove_cnt."/SD_DEVICE",$Device_hd);
					$j = 1;
					while($j <= $disk_cnt)
					{
						$UID = query($disk_hd."/disk:".$j."/uid");
						echo '#UID= '.$UID.'\n';
						$uid = tolower($UID);
						echo '#uid= '.$uid.'\n';
					
				
						$Device_hd  = query($remove_hd."/entry:".$remove_cnt."/SD_DEVICE");// /dev/sda
						echo '#Device_hd= '.$Device_hd.'\n';// /dev/sda
						$Device_sd   = scut($Device_hd, 0, "/dev/");
						echo '#Device_sd= '.$Device_sd.'\n'; //sda

						if($Device_sd == $uid)
						{
							$k = 1;
							$disk_entry = query($disk_hd."/disk:".$j."/entry#");
							echo '#disk_entry= '.$disk_entry.'\n';
							while($k <= $disk_entry)
							{
								$disk_state = query($disk_hd."/disk:".$j."/entry:".$k."/state");

								if($disk_state == "MOUNTED")
								{			
									if($Device_sd == $uid)	
									{
										echo '#uid_3= '.$uid.'\n';
										echo '#Device_sd_3= '.$Device_sd.'\n'; //sda
										$mntp = query($disk_hd."/disk:".$j."/entry:".$k."/mntp");
										echo '#mntp= '.$mntp.'\n';
										set($disk_hd."/disk:".$j."/entry:".$k."/state","NOT MOUNTED");
										set($remove_hd."/entry:".$remove_cnt."/unmount","1");
										echo "if [ -d ".$mntp." ]; then\n";
										echo "sync\n";
										echo "sdparm --command=sync ".$Device_hd."\n";
										echo "for i in 0 1 \n";
										echo "do\n";
										echo "sleep 1\n";
										echo "umount -l ".$mntp."\n";
										echo "if [ $? -eq 0 ]; then\n";
										echo "rmdir ".$mntp."\n";									
										echo "break\n";
										echo "fi\n";
										echo "done\n";
										echo "fi\n";
									}
								}
								$k++;
							}
							echo "hd-idle -t ".$Device_sd."\n";
				 		 }
						$j++;
					 }
				}
				else
				{
					$point++;
				}
				$i++;	
			}
			
		 }	
		 $remove_cnt++;
	}					
	echo "service NETATALK start\n";
}
echo "exit 0\n";

?>
