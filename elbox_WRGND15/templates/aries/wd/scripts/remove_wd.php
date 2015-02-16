#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

//$hd = "/runtime/wd";

if($USB_ADDR == "USB1")
{
	$hd = "/runtime/wd/USB1";
	if($ACTION == "remove")
	{	
		$i = 1;
		$cnt = query($hd."/entry#");
		echo '#cnt= '.$cnt.'\n';
		while($i <= $cnt)
		{	
			echo '#i= '.$i.'\n';
			$Device_hd  = query($hd."/entry:".$i."/Device");
			$Device_SD  = query($hd."/entry:".$i."/SD_DEVICE");
			$SN_hd  = query($hd."/entry:".$i."/serial_number");
			$REVISION_hd = query($hd."/entry:".$i."/revision");
			$device_WD = substr($REMOVE_WD,0,strlen($Device_SD));//JERRY add this to get SD map value.
			if($Device_hd == $REMOVE_WD)
			{	
				//del($hd."/entry:".$i);
				//echo '#i= '.$i.'\n';
				//$Device_hd  = query($hd."/entry:".$i."/Device");
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
			else if($Device_SD==$device_WD)//JERRY add this because pro and storage kernel may dump SD map value
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
}
else if($USB_ADDR == "USB2")
{
	$hd = "/runtime/wd/USB2";
	if($ACTION == "remove")
	{
		$i = 1;
		$cnt = query($hd."/entry#");
		echo '#cnt= '.$cnt.'\n';
		while($i <= $cnt)
		{	
			echo '#i= '.$i.'\n';
			$Device_hd  = query($hd."/entry:".$i."/Device");
			$Device_SD  = query($hd."/entry:".$i."/SD_DEVICE");
			$SN_hd  = query($hd."/entry:".$i."/serial_number");
			$REVISION_hd = query($hd."/entry:".$i."/revision");
			$device_WD = substr($REMOVE_WD,0,strlen($Device_SD));

			if($Device_hd == $REMOVE_WD)
			{	
				//del($hd."/entry:".$i);
				//echo '#i= '.$i.'\n';
				//$Device_hd  = query($hd."/entry:".$i."/Device");
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
					if($SN_hd == $SN1_hd && $REVISION_hd == $REVISION1_hd)
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
			else if($Device_SD==$device_WD)
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
					if($SN_hd == $SN1_hd && $REVISION_hd == $REVISION1_hd)
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
}

?>
