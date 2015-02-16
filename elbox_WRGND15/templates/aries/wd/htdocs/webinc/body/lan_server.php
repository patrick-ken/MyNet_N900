<?
	include "/htdocs/webinc/config.php";
	include "/htdocs/phplib/inet.php";
	$lanip = query(INET_getpathbyinf($LAN1)."/ipv4/ipaddr");
	$netmask = query(INET_getpathbyinf($LAN1)."/ipv4/mask");
	$networkid = ipv4networkid($lanip, $netmask);
	$a = cut($networkid, "3", ".");
	$networkid_sub = substr($networkid, 0, strlen($networkid)-strlen($a));
?>
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "DHCP Server");?></p>
		</div>
		<div>	
			<p class="text"><? 
				echo I18N("h", "The DHCP server is used to provide the IP address to the LAN side computers dynamically.")." ".
					I18N('h', 'Configured as a built-in DHCP server when the device is in router mode.').' '.
					I18N('h', 'There should be only one DHCP server on each network.').' '.
					I18N('h', 'You should disable the DHCP server if there is another DHCP server in your network.');?>
			</p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Enable DHCP Server");?></span>
			<span class="value"><input id="dhcpsvr" type="checkbox" class="styled" onClick="PAGE.OnClickDHCPSvr();" /></span>
		</div>			
		<div class="textinput">
			<span class="name"><? echo I18N("h", "DHCP IP Address Range");?></span>
			<span class="value"><?=$networkid_sub?><input id="startip" type="text" size="3" maxlength="3"/>&nbsp;to&nbsp;<?=$networkid_sub?><input id="endip" type="text" size="3" maxlength="3"/></span>
		</div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Local Domain Name");?></span>
			<span class="value"><input id="domain" type="text" size="20" maxlength="15" /></span>
		</div>		
		<div class="textinput">
			<span class="name"><? echo I18N("h", "DHCP Lease Time");?></span>
			<span class="value"><input id="leasetime" type="text" size="6" maxlength="5" />(<?echo I18N("h", "hours");?>)</span>
		</div>		
		<hr>
				
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
