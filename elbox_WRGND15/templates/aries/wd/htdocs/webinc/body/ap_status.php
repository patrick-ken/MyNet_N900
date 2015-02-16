<?
	include "/htdocs/phplib/xnode.php";
	include "/htdocs/phplib/phyinf.php";
	$bridge_run_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", "BRIDGE-1", "0");
	$bridge_run_phyinfp = PHYINF_getphypath("BRIDGE-1");
	if(query($bridge_run_infp."/inet/ipv4/static")=="1") $connect_type = I18N("h", "Static IP");
	else $connect_type = I18N("h", "DHCP");
	$system_uptime = query("/runtime/device/uptime");
	$wan_uptime = query($bridge_run_infp."/inet/uptime");
	$uptime = $system_uptime-$wan_uptime;
	$uptime_sec = 0;
	$uptime_min = 0;
	$uptime_hour = 0;
	$uptime_day = 0;
	if(query("/runtime/device/wan_status")=="1" && $uptime>0 && $wan_uptime>0)
	{
		$uptime_sec = $uptime%60;
		$uptime_min = $uptime/60%60;
 		$uptime_hour = $uptime/3600%24;
 		$uptime_day = $uptime/86400;
	}
	$connect_uptime = $uptime_day." ".I18N("h", "Day")." ".$uptime_hour." ".I18N("h", "Hour")." ".$uptime_min." ".I18N("h", "Min")." ".$uptime_sec." ".I18N("h", "Sec");
	$mac = query($bridge_run_phyinfp."/macaddr");
	$ipaddr = query($bridge_run_infp."/inet/ipv4/ipaddr");
	if(query("/runtime/device/wan_status")!="1") $connect_status = I18N("h", "Not Connected");
	else $connect_status = I18N("h", "Connected");
	$mask = query($bridge_run_infp."/inet/ipv4/mask");
	$mask = ipv4int2mask($mask);
	$gateway = query($bridge_run_infp."/inet/ipv4/gateway"); 
	$dns = query($bridge_run_infp."/inet/ipv4/dns:1");
	$dns2 = query($bridge_run_infp."/inet/ipv4/dns:2");	
?>
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Connection Status");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "All of your Internet connection details are displayed on this page.");?></p>
		</div>
		<hr>
		<br>

	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection Type");?></span>
	        <span class="value" id="st_connecttype"><? echo $connect_type;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection to Internet");?></span>
	        <span class="value" id="st_networkstatus"><? echo $connect_status;?></span>
	    </div>
		<div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection Up Time");?></span>
	        <span class="value" id="st_connection_uptime"><? echo $connect_uptime;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "MAC Address");?></span>
		    <span class="value" id="st_mac"><? echo $mac;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "IP Address");?></span>
	        <span class="value" id="st_ipaddr"><? echo $ipaddr;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Subnet Mask");?></span>
	        <span class="value" id="st_mask"><? echo $mask;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Default Gateway");?></span>
	        <span class="value" id="st_gateway"><? echo $gateway;?></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
	        <span class="value" id="st_DNSserver"><? echo $dns;?></span>
	    </div>
	    <div class="textinput" >
	        <span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
	        <span class="value" id="st_DNSserver2"><? echo $dns2;?></span>
	    </div>
	    <div class="gap"></div>
	</div>
</form>
