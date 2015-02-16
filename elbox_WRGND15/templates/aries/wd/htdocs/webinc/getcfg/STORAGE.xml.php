<?
//Don¡¦t save the password after unlock procedure due to WD requirement.
include "/htdocs/phplib/xnode.php";
if(query("/wd/unlock_device")!="")
{
	$unlock_device = query("/wd/unlock_device");
	$device_path_USB1 = XNODE_getpathbytarget("/runtime/wd/USB1", "entry", "Device", $unlock_device, 0);
	$device_path_USB2 = XNODE_getpathbytarget("/runtime/wd/USB2", "entry", "Device", $unlock_device, 0);
	if($device_path_USB1 != "") $device_path = $device_path_USB1;
	else if($device_path_USB2 != "") $device_path = $device_path_USB2;
	if(query($device_path."/lock_status")=="UNLOCK") del($device_path."/unlock_fail_time");
	else if(query($device_path."/lock_status")=="LOCK")
	{
		$unlock_fail_time = query($device_path."/unlock_fail_time");
		if($unlock_fail_time=="") $unlock_fail_time=0;
		$unlock_fail_time++;
		set($device_path."/unlock_fail_time", $unlock_fail_time);
	}	
	
	$pwd_path = XNODE_getpathbytarget("/wd", "entry", "Device", $unlock_device, 0);
	set($pwd_path."/PWD", "");
	set("/wd/unlock_device", "");
	event("DBSAVE"); 
}
?>
<module>
	<service><?=$GETCFG_SVC?></service>   
        <wd>
			<? echo dump(1, "/wd");?>        	
    	</wd>	            
		<runtime>
			<wd>
				<? echo dump(2, "/runtime/wd");?>						
			</wd>
			<device>
				<storage>
					<? echo dump(3, "/runtime/device/storage");?>					
				</storage>	
			</device>
		</runtime>
</module>