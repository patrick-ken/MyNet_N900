<?
echo "#!/bin/sh\n";
include "/htdocs/phplib/trace.php";//Joseph
TRACE_debug("WD-AUTO-UNLOCK");//Joseph

function SpecialCharCheck($password,$charac)
{
	$result = "";
	$idx = "";
	$sumize = "";
	
	$idx = strchr($password, $charac);

	if($idx!="")
	{
		while($idx!="")
		{
			$pre = "";
			$pre = substr($password,0,$idx);
			$password = substr($password,$idx+1,strlen($password)-$idx-1);
			$sumize = $sumize.$pre."\\".$charac;
			$idx = "";
			$idx = strchr($password, $charac);
		}
		$sumize = $sumize.$password;
		$result = $sumize;
	}
	else
	{
		$result = $password;
	}
	return $result;
}
function PasswordChange($password)
{
	$password = SpecialCharCheck($password,'"');
	$password = SpecialCharCheck($password,'$');
	$password = SpecialCharCheck($password,'`');
	$password = "\"".$password."\"";
	return $password;
}
$base = "/wd";

$hd = "/runtime/wd/USB1";
$total_entry = query($hd."/entry#");
if($total_entry=="") $total_entry=0;
$hd_cnt=0;
echo '#total_entry= '.$total_entry.'\n';
$hd_cnt++;
echo '#hd_cnt= '.$hd_cnt.'\n';

while($hd_cnt <= $total_entry)
{
	$Model_hd  = query($hd."/entry:".$hd_cnt."/vendor");
	$Product_hd  = query($hd."/entry:".$hd_cnt."/model");
	$Status_hd  = query($hd."/entry:".$hd_cnt."/lock_status");
	$SN_hd  = query($hd."/entry:".$hd_cnt."/serial_number");
	$Device_hd = query($hd."/entry:".$hd_cnt."/Device");
	
	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Product_hd= '.$Product_hd.'\n';
	echo '#Status_hd= '.$Status_hd.'\n';
	echo '#SN_hd= '.$SN_hd.'\n';
	echo '#Device_hd= '.$Device_hd.'\n';
	echo '#WD_DEVICE= '.$WD_DEVICE.'\n';
	
	if($Device_hd == $WD_DEVICE)
	{
		if($Status_hd == "LOCK")
		{
			$i = 1;
			$base_cnt = query($base."/entry#");
			echo '#base_cnt= '.$base_cnt.'\n';
			while($i <= $base_cnt)
			{
				$Model  = query($base."/entry:".$i."/Vendor");
				$Product = query($base."/entry:".$i."/Model");
				$SN  = query($base."/entry:".$i."/serial_number");
				$PWD = query($base."/entry:".$i."/PWD");
				$PWD = PasswordChange($PWD);

				echo '#Model= '.$Model.'\n';
				echo '#Product= '.$Product.'\n';
				echo '#SN= '.$SN.'\n';
				echo '#PWD= '.$PWD.'\n';

				if($Model == $Model_hd && $Product == $Product_hd && $SN == $SN_hd)//found
				{	//this information is the same to do unlock
					$Device_hd  = query($hd."/entry:".$hd_cnt."/Device");// /dev/sg2
					$Device_sd  = query($hd."/entry:".$hd_cnt."/SD_DEVICE");// /dev/sda
					echo '#Device_hd= '.$Device_hd.'\n';// /dev/sg2 
					echo '#Device_sd= '.$Device_sd.'\n';// /dev/sda
					echo '/usr/sbin/apollo '.$Device_hd.' '.$PWD.'\n'; // /dev/sg2

					echo "if [ $? -eq 0 ]; then\n";
					echo "xmldbc -s ".$hd."/entry:".$hd_cnt."/lock_status UNLOCK\n";
					echo "/usr/sbin/hdparm -z ".$Device_sd."\n"; // /dev/sda
					echo "else\n";
					echo "xmldbc -s ".$hd."/entry:".$hd_cnt."/lock_status LOCK\n";
					echo "fi\n";
				}
				$i++;
			}
		}	
	}
	$hd_cnt++;
}
//USB2
$hd = "/runtime/wd/USB2";
$total_entry = query($hd."/entry#");
if($total_entry=="") $total_entry=0;
$hd_cnt=0;
echo '#total_entry= '.$total_entry.'\n';
$hd_cnt++;
echo '#hd_cnt= '.$hd_cnt.'\n';

while($hd_cnt <= $total_entry)
{
	$Model_hd  = query($hd."/entry:".$hd_cnt."/vendor");
	$Product_hd  = query($hd."/entry:".$hd_cnt."/model");
	$Status_hd  = query($hd."/entry:".$hd_cnt."/lock_status");
	$SN_hd  = query($hd."/entry:".$hd_cnt."/serial_number");
	$Device_hd = query($hd."/entry:".$hd_cnt."/Device");
	
	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Product_hd= '.$Product_hd.'\n';
	echo '#Status_hd= '.$Status_hd.'\n';
	echo '#SN_hd= '.$SN_hd.'\n';
	echo '#Device_hd= '.$Device_hd.'\n';
	echo '#WD_DEVICE= '.$WD_DEVICE.'\n';

	if($Device_hd == $WD_DEVICE)
	{
		if($Status_hd == "LOCK")
		{
			$i = 1;
			$base_cnt = query($base."/entry#");
			echo '#base_cnt= '.$base_cnt.'\n';
			while($i <= $base_cnt)
			{
				$Model  = query($base."/entry:".$i."/Vendor");
				$Product = query($base."/entry:".$i."/Model");
				$SN  = query($base."/entry:".$i."/serial_number");
				$PWD = query($base."/entry:".$i."/PWD");
				$PWD = PasswordChange($PWD);

				echo '#Model= '.$Model.'\n';
				echo '#Product= '.$Product.'\n';
				echo '#SN= '.$SN.'\n';
				echo '#PWD= '.$PWD.'\n';

				if($Model == $Model_hd && $Product == $Product_hd && $SN == $SN_hd)//found
				{	//this information is the same to do unlock
					$Device_hd  = query($hd."/entry:".$hd_cnt."/Device");
					$Device_sd  = query($hd."/entry:".$hd_cnt."/SD_DEVICE");// /dev/sda
					echo '#Device_hd= '.$Device_hd.'\n';
					echo '/usr/sbin/apollo '.$Device_hd.' '.$PWD.'\n';
				
					echo "if [ $? -eq 0 ]; then\n";
					echo "/usr/sbin/hdparm -z ".$Device_sd."\n"; // /dev/sda
					echo "xmldbc -s ".$hd."/entry:".$hd_cnt."/lock_status UNLOCK\n";
					echo "else\n";
					echo "xmldbc -s ".$hd."/entry:".$hd_cnt."/lock_status LOCK\n";
					echo "fi\n";
				}
				$i++;
			}
		}
	}
	$hd_cnt++;
}

echo "exit 0\n";
?>
