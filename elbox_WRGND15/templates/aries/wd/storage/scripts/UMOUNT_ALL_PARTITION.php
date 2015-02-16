<?
echo "#!/bin/sh\n";

$m = 1;
$disk_hd = "/runtime/device/storage";
$disk_cnt  = query($disk_hd."/disk#");
echo '#disk_cnt = '.$disk_cnt.'\n';
while($m <= $disk_cnt)
{
	$UID = query($disk_hd."/disk:".$m."/uid");
	echo '#UID= '.$UID.'\n';
	$uid = tolower($UID);
	echo '#uid= '.$uid.'\n';
	echo '#Check_WD_HD= '.$Check_WD_HD.'\n';

	if($uid == $Check_WD_HD)
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
				echo "umount ".$mntp."\n";
				echo "fi\n";
			}
			$n++;
		}
	}
		$m++;
}
echo "exit 0\n";
?>
