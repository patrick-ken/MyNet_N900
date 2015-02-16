<? 
	include "/htdocs/webinc/body/draw_elements.php";
	$countrycode = query("/runtime/devdata/countrycode");
?>
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Internet Setup");?></p>
		</div>
		<div>
			<p class="text">
				<? echo I18N("h", "Use this section to configure your Internet connection type.");?>
				<? echo I18N("h", "There are several connection types to choose from: Static IP, DHCP, PPPoE, PPTP, and L2TP.");?>
				<? echo I18N("h", "If you are unsure of your connection method, please contact your Internet Service Provider(ISP).");?>
			</p>
		</div>
		<div>
			<p class="text">
				<strong><? echo I18N("h", "Note :");?></strong>
				<? echo I18N("h", "If using the PPPoE option, you will need to remove or disable any PPPoE client software on your computers.");?>
			</p>
		</div>
		<hr>		
		
		<div>
			<p class="text_title"><?echo I18N("h", "Internet Connection Type");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "Choose the mode the router will use to connect to the Internet.");?></p>
		</div>
		<br>			
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Internet Connection Type");?></span>
			<span class="value">
				<select id="wan_ip_mode" onchange="PAGE.OnChangeWanIpMode();" class="styled4">
					<option value="static"><? echo I18N("h", "Static IP");?></option>
					<option value="dhcp"><? echo I18N("h", "Dynamic IP (DHCP)");?></option>
					<? if ($FEATURE_DHCPPLUS=="1")		echo '<option value="dhcpplus">'.I18N("h", "DHCP Plus").'</option>\n';?>
					<option value="pppoe"><? echo I18N("h", "PPPoE");?></option>
					<? if ($FEATURE_NOPPTP!="1")		echo '<option value="pptp">'.I18N("h", "PPTP").'</option>\n';?>
					<? if ($FEATURE_NOL2TP!="1")		echo '<option value="l2tp">'.I18N("h", "L2TP").'</option>\n';?>										
					<? if ($FEATURE_NORUSSIAPPTP!="1")	echo '<option value="r_pptp">'.I18N("h", "PPTP (Russia)").'</option>\n';?>
					<? if ($FEATURE_NORUSSIAL2TP!="1")	echo '<option value="r_l2tp">'.I18N("h", "L2TP (Russia)").'</option>\n';?>					
				</select>
			</span>
		</div>
		<hr>
		
	<!-- ipv4 -->
		<!-- static -->
		<div id="box_wan_static_body" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "Static IP Address Internet Connection Type :");?></p>
			</div>			
			<div>
				<p class="text"><?echo I18N("h", "Enter the static address information provided by your Internet Service Provider (ISP).");?></p>
			</div>
			<br>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "IP Address");?></span>
				<span class="value"><input id="st_ipaddr" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Subnet Mask");?></span>
				<span class="value"><input id="st_mask" type="text" size="20" maxlength="15" /></span>
			</div>		
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Default Gateway");?></span>
				<span class="value"><input id="st_gw" type="text" size="20" maxlength="15" /></span>
			</div>
		</div>
		<!-- dhcp -->
		<div id="box_wan_dhcp_body" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "Dynamic IP (DHCP) Internet Connection Type :");?></p>
			</div>			
			<div>
				<p class="text"><?echo I18N("h", "Use this Internet connection type if your Internet Service Provider (ISP) didn't provide you with IP Address information and/or a username and password.");?></p>
			</div>
			<br>
			<div id="dhcpplus" style="display:none">
				<div class="textinput">
					<span class="name"><?echo I18N("h", "Username");?></span>
					<span class="value"><input id="dhcpplus_username" type="text" size="20" maxlength="63" /></span>
				</div>
				<div class="textinput">
					<span class="name"><?echo I18N("h", "Password");?></span>
					<span class="value"><input id="dhcpplus_password" type="password" size="20" maxlength="63" /></span>
				</div>
			</div>			
		</div>
		
		<!-- ipv4 common -->
		<div id="box_wan_ipv4_common_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value"><input id="ipv4_dns1" type="text" size="20" maxlength="15" /></span>
			</div>			
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value">
					<input id="ipv4_dns2" type="text" size="20" maxlength="15" />
					(<?echo I18N("h", "optional");?>)
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "MTU");?></span>
				<span class="value"><input id="ipv4_mtu" type="text" size="10" maxlength="4" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "MAC Address");?></span>
				<span class="value"><input id="ipv4_macaddr" type="text" size="20" maxlength="17" /></span>
			</div>								
			<div class="textinput">
				<span class="name"></span>
				<span class="value"><input id="ipv4_mac_button" type="button" class="button_blueX4" value="<?echo I18N("h", "Clone Your Computer's MAC Address");?>" onclick="PAGE.OnClickMacButton('ipv4_macaddr');" /></span>
			</div>
		</div>
		<!-- end ipv4 common -->
	<!-- end ipv4 -->				
		
	<!-- ppp4 -->
		<!-- pppoe -->
		<div id="box_wan_pppoe_body" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "PPPoE Internet Connection Type :");?></p>
			</div>
			<div>
				<p class="text"><?echo I18N("h", "Enter the information provided by your Internet Service Provider (ISP).");?></p>
			</div>
			<br>						
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Address Mode");?></span>
				<span class="value">
					<input type="radio" class="styled" id="pppoe_dynamic" name="pppoe_addr_type" onclick="PAGE.OnClickPppoeAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Dynamic IP");?>&nbsp;&nbsp;</span>
					<input type="radio" class="styled" id="pppoe_static"  name="pppoe_addr_type" onclick="PAGE.OnClickPppoeAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Static IP");?></span>			
				</span>
			</div>
			<div id="pppoe_ip_address" class="textinput">
				<span class="name"><?echo I18N("h", "IP Address");?></span>
				<span class="value"><input id="pppoe_ipaddr" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Username");?></span>
				<span class="value">
					<input id="pppoe_username" type="text" size="20" maxlength="63" />
					<span id="show_pppoe_mppe" style="display:none" >&nbsp;<?echo I18N("h", "MPPE :");?>
						<input type="checkbox" id="pppoe_mppe" name="ppp_mppe" >
					</span>				
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Password");?></span>
				<span class="value"><input id="pppoe_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Verify Password");?></span>
				<span class="value"><input id="confirm_pppoe_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Service Name");?></span>
				<span class="value"><input id="pppoe_service_name" type="text" size="30" maxlength="39" /> (<?echo I18N("h", "optional");?>)</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Reconnect Mode");?></span>
				<span class="value">
					<input type="radio" class="styled" id="pppoe_alwayson" name="pppoe_reconnect_radio" onclick="PAGE.OnClickPppoeReconnect();"/><span>&nbsp;</span><?
	
	if ($FEATURE_NOSCH=="1")	echo I18N("h", "Always on").'\n<span style="display:none">\n';
	
	DRAW_select_sch("pppoe_schedule",I18N("h", "Always on"),"","","","styled1");
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button_blueX2" id="pppoe_schedule_button" value="'.I18N("h", "New Schedule").'" onclick=\'self.location.href="tools_sch.php";\' />\n';
	
	if ($FEATURE_NOSCH=="1")	echo '</span>\n';
	
				?></span>
			</div>
			<div class="textinput">
				<span class="name"></span>
				<span class="value">
					<input type="radio" class="styled" id="pppoe_ondemand"	name="pppoe_reconnect_radio" onclick="PAGE.OnClickPppoeReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "On demand");?>&nbsp;&nbsp;</span>
				    <input type="radio" class="styled" id="pppoe_manual"	name="pppoe_reconnect_radio" onclick="PAGE.OnClickPppoeReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Manual");?></span>
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Maximum Idle Time");?></span>
				<span class="value"><input id="pppoe_max_idle_time" type="text" size="10" maxlength="5" /><?echo I18N("h", "(minutes, 0=infinite)");?></span>
			</div>
			<div class="textinput"<?if ($FEATURE_FAKEOS!="1")echo ' style="display:none"';?>>
				<span class="name"><input type="checkbox" id="en_fakeos" /></span>
				<span class="value"><?echo I18N("h", "I want to use Netblock.");?></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "DNS Mode");?></span>
				<span class="value">
					<input id="dns_isp"		class="styled" type="radio" name="dns_mode" onclick="PAGE.OnClickDnsMode();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Receive DNS from ISP");?>&nbsp;&nbsp;</span>
					<input id="dns_manual"	class="styled" type="radio" name="dns_mode" onclick="PAGE.OnClickDnsMode();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Enter DNS Manually ");?></span>
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value"><input id="pppoe_dns1" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value">
					<input id="pppoe_dns2" type="text" size="20" maxlength="15" />
					(<?echo I18N("h", "optional");?>)
				</span>
			</div>
		</div>
		<!-- pptp -->		
		<div id="box_wan_pptp_body" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "PPTP Internet Connection Type :");?></p>
			</div>
			<div>
				<p class="text"><?echo I18N("h", "Enter the information provided by your Internet Service Provider (ISP).");?></p>
			</div>
			<br>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Address Mode");?></span>
				<span class="value">
					<input id="pptp_dynamic" class="styled" type="radio" name="pptp_addr_type" onclick="PAGE.OnClickPptpAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Dynamic IP");?>&nbsp;&nbsp;</span>
					<input id="pptp_static"  class="styled" type="radio" name="pptp_addr_type" onclick="PAGE.OnClickPptpAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Static IP");?></span>
				</span>
			</div>
			<div id="pptp_ip_address" class="textinput">
				<span class="name"><?echo I18N("h", "PPTP IP Address");?></span>
				<span class="value"><input id="pptp_ipaddr" type="text" size="20" maxlength="15" /></span>
			</div>
			<div id="pptp_subnet_mask" class="textinput">
				<span class="name"><?echo I18N("h", "PPTP Subnet Mask");?></span>
				<span class="value"><input id="pptp_mask" type="text" size="20" maxlength="15" /></span>
			</div>
			<div id="pptp_gateway_ip" class="textinput">
				<span class="name"><?echo I18N("h", "PPTP Gateway IP Address");?></span>
				<span class="value"><input id="pptp_gw" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "PPTP Server IP Address");?></span>
				<span class="value">
					<input id="pptp_server" type="text" size="20" maxlength="30" />
					<span id="show_pptp_mppe" style="display:none" >&nbsp;<?echo I18N("h", "MPPE :");?>
						<input type="checkbox" id="pptp_mppe" name="pptp_mppe" >
					</span>				
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Username");?></span>
				<span class="value"><input id="pptp_username" type="text" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Password");?></span>
				<span class="value"><input id="pptp_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Verify Password");?></span>
				<span class="value"><input id="confirm_pptp_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Reconnect Mode");?></span>
				<span class="value">
					<input type="radio" class="styled" id="pptp_alwayson" name="pptp_reconnect_radio" onclick="PAGE.OnClickPptpReconnect();"/><span>&nbsp;</span><?
	
	if ($FEATURE_NOSCH=="1")	echo I18N("h", "Always on").'\n<span style="display:none">\n';
	
	DRAW_select_sch("pptp_schedule",I18N("h", "Always on"),"","","","styled1");
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button_blueX2" id="pptp_schedule_button" value="'.I18N("h", "New Schedule").'" onclick=\'self.location.href="tools_sch.php";\' />\n';
	
	if ($FEATURE_NOSCH=="1")	echo '</span>\n';
	
				?></span>
			</div>
			<div class="textinput">
				<span class="name"></span>
				<span class="value">
					<input type="radio" class="styled" id="pptp_ondemand"	name="pptp_reconnect_radio" onclick="PAGE.OnClickPptpReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "On demand");?>&nbsp;&nbsp;</span>
				    <input type="radio" class="styled" id="pptp_manual"	name="pptp_reconnect_radio" onclick="PAGE.OnClickPptpReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Manual");?></span>
				</span>
			</div>                                                 
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Maximum Idle Time");?></span>
				<span class="value"><input id="pptp_max_idle_time" type="text" size="10" maxlength="5" /><?echo I18N("h", "(minutes, 0=infinite)");?></span>
			</div>                                                                    
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value"><input id="pptp_dns1" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value">
					<input id="pptp_dns2" type="text" size="20" maxlength="15" />
					(<?echo I18N("h", "optional");?>)
				</span>
			</div>
		</div>
		<!-- l2tp -->
		<div id="box_wan_l2tp_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Address Mode");?></span>
				<span class="value">
					<input id="l2tp_dynamic" class="styled" type="radio" name="l2tp_addr_type" onclick="PAGE.OnClickL2tpAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Dynamic IP");?>&nbsp;&nbsp;</span>
					<input id="l2tp_static"  class="styled" type="radio" name="l2tp_addr_type" onclick="PAGE.OnClickL2tpAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Static IP");?></span>
				</span>
			</div>
			<div id="l2tp_ip_address" class="textinput">
				<span class="name"><?echo I18N("h", "L2TP IP Address");?></span>	
				<span class="value"><input id="l2tp_ipaddr" type="text" size="20" maxlength="15" /></span>
			</div>
			<div id="l2tp_subnet_mask" class="textinput">
				<span class="name"><?echo I18N("h", "L2TP Subnet Mask");?></span>
				<span class="value"><input id="l2tp_mask" type="text" size="20" maxlength="15" /></span>
			</div>	
			<div id="l2tp_gateway_ip" class="textinput">
				<span class="name"><?echo I18N("h", "L2TP Gateway IP Address");?></span>
				<span class="value"><input id="l2tp_gw" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "L2TP Server IP Address");?></span>
				<span class="value"><input id="l2tp_server" type="text" size="20" maxlength="30" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Username");?></span>
				<span class="value"><input id="l2tp_username" type="text" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Password");?></span>
				<span class="value"><input id="l2tp_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Verify Password");?></span>
				<span class="value"><input id="confirm_l2tp_password" type="password" size="20" maxlength="63" /></span>
			</div>	
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Reconnect Mode");?></span>
				<span class="value">
					<input type="radio" class="styled" id="l2tp_alwayson" name="l2tp_reconnect_radio" onclick="PAGE.OnClickL2tpReconnect();"/><span>&nbsp;</span><?
	
	if ($FEATURE_NOSCH=="1")	echo I18N("h", "Always on").'\n<span style="display:none">\n';
	
	DRAW_select_sch("l2tp_schedule",I18N("h", "Always on"),"","","","styled1");
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button_blueX2" id="l2tp_schedule_button" value="'.I18N("h", "New Schedule").'" onclick=\'self.location.href="tools_sch.php";\' />\n';
	
	if ($FEATURE_NOSCH=="1")	echo '</span>\n';
	
				?></span>
			</div>
			<div class="textinput">
				<span class="name"></span>
				<span class="value">
					<input type="radio" class="styled" id="l2tp_ondemand"	name="l2tp_reconnect_radio" onclick="PAGE.OnClickL2tpReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "On demand");?>&nbsp;&nbsp;</span>
				    <input type="radio" class="styled" id="l2tp_manual"	name="l2tp_reconnect_radio" onclick="PAGE.OnClickL2tpReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Manual");?></span>
				</span>
			</div>                                                 
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Maximum Idle Time");?></span>
				<span class="value"><input id="l2tp_max_idle_time" type="text" size="10" maxlength="5" /><?echo I18N("h", "(minutes, 0=infinite)");?></span>
			</div>                                                                             
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value"><input id="l2tp_dns1" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value">
					<input id="l2tp_dns2" type="text" size="20" maxlength="15" />
					(<?echo I18N("h", "optional");?>)
				</span>
			</div>
		</div>
		<!-- ppp4 common -->
		<div id="box_wan_ppp4_comm_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "MTU");?></span>
				<span class="value"><input id="ppp4_mtu" type="text" size="10" maxlength="4" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "MAC Address");?></span>
				<span class="value"><input id="ppp4_macaddr" type="text" size="20" maxlength="17" /></span>
			</div>
			<div class="textinput">
				<span class="name"></span>
				<span class="value">
					<input id="mac_button" class="button_blueX3" type="button" value="<?echo I18N("h", "Clone Your PC's MAC Address");?>" onclick="PAGE.OnClickMacButton('ppp4_macaddr');" />
				</span>
			</div>		
		</div>
		<!-- end ppp4 common -->
	<!-- end ppp4 -->
		<!-- Russia PPPoE -->
		<div id="R_PPPoE" style="display:none">
			<h2><?echo I18N("h", "WAN Physical Settings");?></h2>
			<div class="textinput">
				<span class="name"></span>
				<span class="value">
					<input id="rpppoe_dynamic" type="radio" name="rpppoe_addr_type" onclick="PAGE.OnClickRPppoeAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Dynamic IP");?>&nbsp;&nbsp;</span>
					<input id="rpppoe_static"  type="radio" name="rpppoe_addr_type" onclick="PAGE.OnClickRPppoeAddrType();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Static IP");?></span>
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "IP Address");?></span>
				<span class="value">
					<input id="rpppoe_ipaddr" type="text" size="20" maxlength="15" />
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Subnet Mask");?></span>
				<span class="value">
					<input id="rpppoe_mask" type="text" size="20" maxlength="15" />
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Gateway");?></span>
				<span class="value">
					<input id="rpppoe_gw" type="text" size="20" maxlength="15" />
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Address");?></span>
				<span class="value">
					<input id="rpppoe_dns1" type="text" size="20" maxlength="15" />
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Address");?></span>
				<span class="value">
					<input id="rpppoe_dns2" type="text" size="20" maxlength="15" />
					(<?echo I18N("h", "optional");?>)
				</span>
			</div>
			<div class="emptyline"></div>
		</div>
		<!-- end Russia PPPoE -->
		<br>
		<hr>	
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>
