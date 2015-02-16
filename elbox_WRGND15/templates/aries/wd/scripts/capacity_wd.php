#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$base = "/wd";
	
$name_start = strchr($DEVICE_VAL,' ');//name solution for WD ITR48889 request
if($name_start!="")
{
	$company_name = substr($DEVICE_VAL,0,$name_start);
	$other_name = substr($DEVICE_VAL,$name_start+1,strlen($DEVICE_VAL)-$name_start-1);
	$name_end = strchr($other_name,'-');
	if($MODEL_VAL=="WD" && $company_name=="WDC" && $name_end!="")
	{
		$NAME = substr($other_name,0,$name_end);
	}
	else
	{
		$NAME = $other_name;
	}
}
else
{
	$NAME = $DEVICE_VAL;
}

if($USB_ADDR == "USB1")
{
	$hd = "/runtime/wd/USB1";
	$cnt = query($hd."/entry#");
//	$cnt++;
	echo '#cnt= '.$cnt.'\n';
	
	echo '#APOLLO_DEVICE= '.$APOLLO_DEVICE.'\n';

	if($APOLLO_DEVICE == "Not Apollo device")
	{
		$cnt++;
		echo '#APOLLO_DEVICE= '.$APOLLO_DEVICE.'\n';
		
		if($MODEL_VAL != "")
		{
			$Hash=$MODEL_VAL.''.$SER_VAL;
			$VENDER=$MODEL_VAL;
		}
		else if($MODEL_VAL == "")
		{
			$Hash="Hard Drive".''.$SER_VAL;
			$VENDER="Hard Drive";
		}
		set($hd."/entry:".$cnt."/handle",$Hash);
		set($hd."/entry:".$cnt."/name",$NAME);
		set($hd."/entry:".$cnt."/vendor",$VENDER);
		set($hd."/entry:".$cnt."/model",$DEVICE_VAL);
		set($hd."/entry:".$cnt."/serial_number",$SER_VAL);
		set($hd."/entry:".$cnt."/revision",$REVISION_VAL);
		set($hd."/entry:".$cnt."/Device",$WD_DEVICE);
		set($hd."/entry:".$cnt."/capacity",$CAPACITY);
		
	}
	else
	{
		
		set($hd."/entry:".$cnt."/capacity",$CAPACITY);
	}
	
	$Model_hd  = query($hd."/entry:".$cnt."/vendor");
	$Cap_hd  = query($hd."/entry:".$cnt."/capacity");
	$Device_hd = query($hd."/entry:".$cnt."/Device");

	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Cap_hd= '.$Cap_hd.'\n';
	echo '#Device= '.$Device_hd.'\n';
}
else if($USB_ADDR == "USB2")
{
	$hd = "/runtime/wd/USB2";
	$cnt = query($hd."/entry#");
	//$cnt++;
	echo '#cnt= '.$cnt.'\n';
	
	echo '#APOLLO_DEVICE= '.$APOLLO_DEVICE.'\n';
	
	if($TBCAP!="")
	{
		$cap_start = strchr($TBCAP,'[');
		$cap_end = strchr($TBCAP,']');
		$dot = strchr($TBCAP,'.');
		$detectTB = strstr($TBCAP,"TB");
		if( $cap_start!="" && $cap_end!="" && $cap_start < $cap_end && $detectTB!="")
		{
			if($dot!="" && $cap_start < $dot)
			{
				$CAPACITY = substr($TBCAP,$cap_start+1,$dot-$cap_start-1);
				$CAPACITY = $CAPACITY." TB";
			}
			else
			{
				$CAPACITY = substr($TBCAP,$cap_start+1,$cap_end-$cap_start-1);
			}
		}
	}
	if($APOLLO_DEVICE == "Not Apollo device")
	{
		$cnt++;
		if($MODEL_VAL != "")
		{
			$Hash=$MODEL_VAL.''.$SER_VAL;
			$VENDER=$MODEL_VAL;
		}
		else if($MODEL_VAL == "")
		{
			$Hash="Hard Drive".''.$SER_VAL;
			$VENDER="Hard Drive";
		}
		set($hd."/entry:".$cnt."/handle",$Hash);
		set($hd."/entry:".$cnt."/name",$NAME);
		set($hd."/entry:".$cnt."/vendor",$VENDER);
		set($hd."/entry:".$cnt."/model",$DEVICE_VAL);
		set($hd."/entry:".$cnt."/serial_number",$SER_VAL);
		set($hd."/entry:".$cnt."/revision",$REVISION_VAL);
		set($hd."/entry:".$cnt."/Device",$WD_DEVICE);
		set($hd."/entry:".$cnt."/capacity",$CAPACITY);
	}
	else
	{
		set($hd."/entry:".$cnt."/capacity",$CAPACITY);
	}

	$Model_hd  = query($hd."/entry:".$cnt."/vendor");
	$Cap_hd  = query($hd."/entry:".$cnt."/capacity");
	$Device_hd = query($hd."/entry:".$cnt."/Device");

	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Cap_hd= '.$Cap_hd.'\n';
	echo '#Device= '.$Device_hd.'\n';
}
?>

