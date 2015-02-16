#!/bin/sh
<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

$base = "/wd";

if($USB_ADDR == "USB1")
{
	$hd = "/runtime/wd/USB1";
	$cnt = query($hd."/entry#");
	if($cnt=="") $cnt=0;
	$cnt++;
	echo '#cnt= '.$cnt.'\n';
	$NAME=$MODEL_VAL.'_'.$DEVICE_VAL;
	$Hash=$MODEL_VAL.''.$SER_VAL;
	set($hd."/entry:".$cnt."/handle",$Hash);
	set($hd."/entry:".$cnt."/name",$NAME);
	set($hd."/entry:".$cnt."/vendor",$MODEL_VAL);
	set($hd."/entry:".$cnt."/model",$DEVICE_VAL);
	set($hd."/entry:".$cnt."/serial_number",$SER_VAL);
	set($hd."/entry:".$cnt."/revision",$REVISION_VAL);
	set($hd."/entry:".$cnt."/Device",$WD_DEVICE);
	
	$Model_hd  = query($hd."/entry:".$cnt."/vendor");
	$Product_hd  = query($hd."/entry:".$cnt."/model");
	$SN_hd  = query($hd."/entry:".$cnt."/serial_number");
	$Revision_hd  = query($hd."/entry:".$cnt."/revision");
	$Device_hd = query($hd."/entry:".$cnt."/Device");
	$handle_hd = query($hd."/entry:".$cnt."/handle");
	
	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Product_hd= '.$Product_hd.'\n';
	echo '#SN_hd= '.$SN_hd.'\n';
	echo '#Revision_hd= '.$Revision_hd.'\n';
	echo '#NOLOCK= '.$NOLOCK.'\n';
	echo '#UNLOCK= '.$UNLOCK.'\n';
	echo '#LOCK= '.$LOCK.'\n';
	echo '#Device= '.$Device_hd.'\n';
	echo '#handle_hd= '.$handle_hd.'\n';
	
	if($NOLOCK == "WD_ENCRYPTION_STATUS_OFF")
	{
		TRACE_debug("state: got service [".$NOLOCK."]");
		set($hd."/entry:".$cnt."/lock_status","OFF");
		TRACE_debug("state: [".$cnt."]");

	}
	else if($LOCK == "WD_ENCRYPTION_STATUS_LOCKED")
	{	
			set($hd."/entry:".$cnt."/lock_status","LOCK");
	}
	else if($UNLOCK == "WD_ENCRYPTION_STATUS_UNLOCKED")
	{
		set($hd."/entry:".$cnt."/lock_status","UNLOCK");
	}
}
else if($USB_ADDR == "USB2")
{
	$hd = "/runtime/wd/USB2";
	$cnt = query($hd."/entry#");
	if($cnt=="") $cnt=0;
	$cnt++;
	echo '#cnt= '.$cnt.'\n';
	$NAME=$MODEL_VAL.'_'.$DEVICE_VAL;
	$Hash=$MODEL_VAL.''.$SER_VAL;
	set($hd."/entry:".$cnt."/handle",$Hash);
	set($hd."/entry:".$cnt."/name",$NAME);
	set($hd."/entry:".$cnt."/vendor",$MODEL_VAL);
	set($hd."/entry:".$cnt."/model",$DEVICE_VAL);
	set($hd."/entry:".$cnt."/serial_number",$SER_VAL);
	set($hd."/entry:".$cnt."/revision",$REVISION_VAL);
	set($hd."/entry:".$cnt."/Device",$WD_DEVICE);

	$Model_hd  = query($hd."/entry:".$cnt."/vendor");
	$Product_hd  = query($hd."/entry:".$cnt."/model");
	$SN_hd  = query($hd."/entry:".$cnt."/serial_number");
	$Revision_hd  = query($hd."/entry:".$cnt."/revision");
	$Device_hd = query($hd."/entry:".$cnt."/Device");
	$handle_hd = query($hd."/entry:".$cnt."/handle");

	echo '#Model_hd= '.$Model_hd.'\n';
	echo '#Product_hd= '.$Product_hd.'\n';
	echo '#SN_hd= '.$SN_hd.'\n';
	echo '#Revision_hd= '.$Revision_hd.'\n';
	echo '#NOLOCK= '.$NOLOCK.'\n';
	echo '#UNLOCK= '.$UNLOCK.'\n';
	echo '#LOCK= '.$LOCK.'\n';
	echo '#Device= '.$Device_hd.'\n';
	echo '#handle_hd= '.$handle_hd.'\n';

	if($NOLOCK == "WD_ENCRYPTION_STATUS_OFF")
	{
		TRACE_debug("state: got service [".$NOLOCK."]");
		set($hd."/entry:".$cnt."/lock_status","OFF");
		TRACE_debug("state: [".$cnt."]");

	}
	else if($LOCK == "WD_ENCRYPTION_STATUS_LOCKED")
	{	
		set($hd."/entry:".$cnt."/lock_status","LOCK");
	}
	else if($UNLOCK == "WD_ENCRYPTION_STATUS_UNLOCKED")
	{
		set($hd."/entry:".$cnt."/lock_status","UNLOCK");
	}
}
?>

