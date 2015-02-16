#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

if($USB_ADDR == "USB1")
{
	$hd = "/runtime/wd/USB1";
	$total_entry = query($hd."/entry#");
	echo '#total_entry= '.$total_entry.'\n';

	
	$SG="/dev/".$SG_MAP_VAL;
	$SD="/dev/".$SD_MAP_VAL;

	echo '#SG= '.$SG.'\n';
	echo '#SD= '.$SD.'\n';

	if($SD_MAP_VAL != "")
	{
		echo '#SD_MAP_VAL= '.$SD_MAP_VAL.'\n';		
		$i = 1;
		while($i <= $total_entry)
		{
			echo '#i= '.$i.'\n';
			$SN_hd  = query($hd."/entry:".$i."/serial_number");
			$REVISION_hd  = query($hd."/entry:".$i."/revision");
				
			echo '#$SN_hd= '.$SN_hd.'\n';
			echo '#$REVISION_hd= '.$REVISION_hd.'\n';
					
			echo '#$SER_VAL= '.$SER_VAL.'\n';
			echo '#$REVISION_VAL= '.$REVISION_VAL.'\n';

			if($SER_VAL == $SN_hd && $REVISION_VAL == $REVISION_hd )
			{
				set($hd."/entry:".$i."/SD_DEVICE",$SD);
				echo '#$SD_i= '.$SD.'\n';
			}
			$i++;
		}
	}
	else
	{
		$j = 1;
		while($j <= $total_entry)
		{
			echo '#j= '.$j.'\n';	
			$SD_dev  = query($hd."/entry:".$j."/SD_DEVICE");
			echo '#SD_dev= '.$SD_dev.'\n';
			if($SD_dev != "")
			{
				$SN_hd_j  = query($hd."/entry:".$j."/serial_number");
				$REVISION_hd_j  = query($hd."/entry:".$j."/revision");
				echo '#$SN_hd_j= '.$SN_hd_j.'\n';
				echo '#$REVISION_hd_j= '.$REVISION_hd_j.'\n';

				$k = $j+1;
				while($k <= $total_entry)
				{
					echo '#k= '.$k.'\n';

					$SN_hd_k  = query($hd."/entry:".$k."/serial_number");
					$REVISION_hd_k  = query($hd."/entry:".$k."/revision");

					echo '#$SN_hd_k= '.$SN_hd_k.'\n';
					echo '#$REVISION_hd_k= '.$REVISION_hd_k.'\n';
					
					if($SN_hd_j == $SN_hd_k && $REVISION_hd_j == $REVISION_hd_k )
					{
						set($hd."/entry:".$k."/SD_DEVICE",$SD_dev);
						echo '#$SD_k= '.$SD_dev.'\n';
					}
					$k++;
				}
			}
			$j++;
		}
	}
}

//USB2
else if($USB_ADDR == "USB2")
{
$hd = "/runtime/wd/USB2";
$total_entry = query($hd."/entry#");
echo '#total_entry= '.$total_entry.'\n';

	$SG="/dev/".$SG_MAP_VAL;
	$SD="/dev/".$SD_MAP_VAL;

	echo '#SG= '.$SG.'\n';
	echo '#SD= '.$SD.'\n';

	if($SD_MAP_VAL != "")
	{
		echo '#SD_MAP_VAL= '.$SD_MAP_VAL.'\n';		
		$i = 1;
		while($i <= $total_entry)
		{
			echo '#i= '.$i.'\n';
			$SN_hd  = query($hd."/entry:".$i."/serial_number");
			$REVISION_hd  = query($hd."/entry:".$i."/revision");
				
			echo '#$SN_hd= '.$SN_hd.'\n';
			echo '#$REVISION_hd= '.$REVISION_hd.'\n';
					
			echo '#$SER_VAL= '.$SER_VAL.'\n';
			echo '#$REVISION_VAL= '.$REVISION_VAL.'\n';

			if($SER_VAL == $SN_hd && $REVISION_VAL == $REVISION_hd )
			{
				set($hd."/entry:".$i."/SD_DEVICE",$SD);
			}
			$i++;
		}
	}
	else
	{
		$j = 1;
		while($j <= $total_entry)
		{
			echo '#j= '.$j.'\n';	
			$SD_dev  = query($hd."/entry:".$j."/SD_DEVICE");
			echo '#SD_dev= '.$SD_dev.'\n';
			if($SD_dev != "")
			{
				$SN_hd_j  = query($hd."/entry:".$j."/serial_number");
				$REVISION_hd_j  = query($hd."/entry:".$j."/revision");
				echo '#$SN_hd_j= '.$SN_hd_j.'\n';
				echo '#$REVISION_hd_j= '.$REVISION_hd_j.'\n';

				$k = $j+1;
				while($k <= $total_entry)
				{
					echo '#k= '.$k.'\n';

					$SN_hd_k  = query($hd."/entry:".$k."/serial_number");
					$REVISION_hd_k  = query($hd."/entry:".$k."/revision");

					echo '#$SN_hd_k= '.$SN_hd_k.'\n';
					echo '#$REVISION_hd_k= '.$REVISION_hd_k.'\n';
					
					if($SN_hd_j == $SN_hd_k && $REVISION_hd_j == $REVISION_hd_k )
					{
						set($hd."/entry:".$k."/SD_DEVICE",$SD_dev);
						echo '#$SD_k= '.$SD_dev.'\n';
					}
					$k++;
				}
			}
			$j++;
		}
	}
}

?>

