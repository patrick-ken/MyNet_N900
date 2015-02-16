
<form id="mainform" onsubmit="return false;">
<div>
	<div>
		<p class="text_title"><?echo I18N("h", "IPv6");?></p>
	</div>				
	<p class="text">
		<? echo I18N("h", "Use this section to configure your IPv6 Connection Type.");?>
		<? echo I18N("h", "If you are unsure of your connection method, please contact your Internet Service Provider(ISP).");?>
	</p>
	<hr>
	
	<!-- IPV6 CONNECTION TYPE START -->
	<div>
		<div>
			<p class="text_title"><? echo I18N("h", "IPv6 Connection Type");?></p>
		</div>				
		<p class="text"><? 
			echo I18N("h", "Choose the mode the router will use to connect to the IPv6 Internet.");
		?></p>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "My IPv6 Connection is ");?></span>
			<span class="value_ipv6">
				<select id="wan_ipv6_mode" onchange="PAGE.OnChangewan_ipv6_mode();" class="styled3">
				<!--<option value="AUTODETECT"><?echo I18N("h", "Auto Detection");?></option>-->
				<option value="STATIC"><?echo I18N("h", "Static IPv6");?></option>
				<option value="AUTO"><?echo I18N("h", "Auto(SLAAC/DHCPv6)");?></option>
				<!--<option value="DHCP"><?echo I18N("h", "DHCPv6(Stateful)");?></option>-->
				<!--<option value="RA"><?echo I18N("h", "Stateless Autoconfiguration");?></option>-->
				<option value="PPPOE"><?echo I18N("h", "PPPoE");?></option>
				<!--<option value="6IN4"><?echo I18N("h", "IPv6 in IPv4 Tunnel");?></option>-->
				<!--<option value="6TO4"><?echo I18N("h", "6to4");?></option>-->
				<!--<option value="6RD"><?echo I18N("h", "6rd");?></option>-->
				<option value="LL"><?echo I18N("h", "Link-local Only");?></option>
				</select>
			</span>
		</div>
	</div>
	<hr>
	<!-- IPV6 CONNECTION TYPE END -->	
	
	<!-- WAN block START-->
	<div id="bbox_wan" style="display:none">
		<div id="box_wan_title" style="display:none">
			<p class="text_title"><? echo I18N("h", "WAN IPv6 ADDRESS SETTINGS");?></p>
			<p class="text"><? 
				echo I18N("h", "Enter the IPv6 address information provided by your Internet Service Provider (ISP).");
			?></p>
			<br>		
		</div>
		<div id="box_wan_static_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Use Link-Local Address");?></span>
				<span class="value_ipv6"><input id="usell" value="" type="checkbox" class="styled" onClick="PAGE.OnClickUsell();"/></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "IPv6 Address");?></span>
				<span class="value_ipv6"><input id="w_st_ipaddr" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Subnet Prefix Length");?></span>
				<span class="value_ipv6"><input id="w_st_pl" type="text" size="4" maxlength="3" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Default Gateway");?></span>
				<span class="value_ipv6"><input id="w_st_gw" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_st_pdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_st_sdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<hr>
		</div>
		
		<div id="box_wan_pppoe" style="display:none">
			<p class="text_title"><? echo I18N("h", "PPPoE Internet Connection Type :");?></p>
			<p class="text"><? 
				echo I18N("h", "Enter the information provided by your Internet Service Provider (ISP).");
			?></p>
			<br>
		</div>
		<div id="box_wan_pppoe_body" style="display:none">
			<div class="textinput" style="display:none"><!--New PPPoE session is default for WD.-->
				<span class="name"><?echo I18N("h", "PPPoE Session");?></span>
				<span class="value_ipv6">
					<input type="radio" class="styled" id="pppoe_sess_share" name="pppoe_sess_type" value="share" onclick="PAGE.OnClickPppoeSessType();"/><?echo I18N("h", "Share with IPv4");?>
					<input type="radio" class="styled" id="pppoe_sess_new"  name="pppoe_sess_type" value="new" onclick="PAGE.OnClickPppoeSessType();"/><?echo I18N("h", "Create a new session.");?>
				</span>
			</div>
			<div class="textinput" style="display:none"><!--New PPPoE session is default for WD.-->
				<span class="name"><?echo I18N("h", "Address Mode");?></span>
				<span class="value_ipv6">
					<input type="radio" class="styled" id="pppoe_dynamic" name="pppoe_addr_type" value="dynamic" onclick="PAGE.OnClickPppoeAddrType();"/><?echo I18N("h", "Dynamic IP");?>
					<input type="radio" class="styled" id="pppoe_static"  name="pppoe_addr_type" value="static" onclick="PAGE.OnClickPppoeAddrType();"/><?echo I18N("h", "Static IP");?>
				</span>
			</div>
			<div class="textinput" style="display:none"><!--New PPPoE session is default for WD.-->
				<span class="name"><?echo I18N("h", "IP Address");?></span>
				<span class="value_ipv6"><input id="pppoe_ipaddr" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Username");?></span>
				<span class="value_ipv6">
					<input id="pppoe_username" type="text" size="20" maxlength="63" />
					<span id="show_pppoe_mppe" style="display:none" >&nbsp;<?echo I18N("h", "MPPE :");?>
						<input type="checkbox" class="styled" id="pppoe_mppe" name="ppp_mppe" >
					</span>
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Password");?></span>
				<span class="value_ipv6"><input id="pppoe_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Verify Password");?></span>
				<span class="value_ipv6"><input id="confirm_pppoe_password" type="password" size="20" maxlength="63" /></span>
			</div>
			<div class="textinput" style="display:none"><!--Without service name in WD.-->
				<span class="name"><?echo I18N("h", "Service Name");?></span>
				<span class="value_ipv6"><input id="pppoe_service_name" type="text" size="30" maxlength="39" /> (<?echo I18N("h", "optional");?>)</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Reconnect Mode");?></span>
				<span class="value_ipv6">
					<input type="radio" class="styled" id="pppoe_alwayson"	name="pppoe_reconnect_radio" value="alwayson" onclick="PAGE.OnClickPppoeReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Always on");?>&nbsp;&nbsp;</span>
					<!--<input type="radio" id="pppoe_ondemand"	name="pppoe_reconnect_radio" onclick="PAGE.OnClickPppoeReconnect();"/><?echo I18N("h", "On demand");?>-->
				    <input type="radio" class="styled" id="pppoe_manual"	name="pppoe_reconnect_radio" value="manual" onclick="PAGE.OnClickPppoeReconnect();"/><span style="float:left;">&nbsp;<?echo I18N("h", "Manual");?></span>
				</span>
			</div>
	
	<!--
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Maximum Idle Time");?></span>
				<span class="delimiter">:</span>
				<span class="value_ipv6"><input id="pppoe_max_idle_time" type="text" size="10" maxlength="5" /><?echo I18N("h", "(minutes, 0=infinite)");?></span>
			</div>
	-->
			<div class="textinput">
				<span class="name"><?echo I18N("h", "MTU");?></span>
				<span class="value_ipv6"><input id="ppp6_mtu" type="text" size="10" maxlength="4" /><?echo I18N("h", "(bytes) MTU default = 1492");?></span>
			</div>
			<hr>
		</div>
		
		<div id="box_wan_6to4_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "6to4 Address");?></span>
				<span class="value_ipv6">
					<span id="w_6to4_ipaddr"></span>
					<span id="w_6to4_pl"></span>
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "6to4 Relay");?></span>
				<span class="value_ipv6"><input id="w_6to4_relay" type="text" size="42" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_6to4_pdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_6to4_sdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<hr>
		</div>
		
		<div id="box_wan_6rd_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "6th Configuration");?></span>
				<span class="value_ipv6">
					<input type="radio" class="styled" id="6rd_dhcp_option" name="6rd_dhcp_option_rad" value="dhcp" onclick="PAGE.OnClick6rdDHCPOPT();"/><?echo I18N("h", "6th DHCPv4 option");?>
					<input type="radio" class="styled" id="6rd_manual"  name="6rd_dhcp_option_rad" value="manual" onclick="PAGE.OnClick6rdDHCPOPT();"/><?echo I18N("h", "Manual Configuration");?>
				</span>
				<span class="name"><?echo I18N("h", "6th IPv6 Prefix");?></span>
				<span class="value_ipv6">
					<input id="w_6rd_prefix_1" type="text" size="20" maxlength="39" />	/
					<input id="w_6rd_prefix_2" type="text" size="4" maxlength="3" />
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "IPv4 Address");?></span>
				<!--<span  ><span id="w_6rd_v4addr"></span></span>-->
				<span class="value_ipv6">
					<label class="label" id="w_6rd_v4addr"></label>
				<!--<span style="font-weight:bold;position:absolute;margin-left: 120px;"><?echo I18N("h", "Mask Length");?>-->
				<span style="font-weight:bold;"><?echo I18N("h", "Mask Length");?>
				:
				<!--<span class="delimiter">:</span>-->
				<input id="w_6rd_v4addr_mask" type="text" size="3" maxlength="2" /></span>	
				</span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Assigned IPv6 Prefix");?></span>
				<span class="value_ipv6"><span id="w_6rd_prefix_3"></span></span>
			</div>
	<!--
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Tunnel Link-Local Address");?></span>
				<span class="delimiter">:</span>
				<span class="value_ipv6"><span id="w_6rd_ll_addr"></span></span>
			</div>
	-->
	<!--
			<div class="textinput">
				<span class="name"><?echo I18N("h", "6rd Address");?></span>
				<span class="delimiter">:</span>
				<span class="value_ipv6"><span id="w_6rd_addr"></span></span>
			</div>
	-->
			<div class="textinput">
				<span class="name"><?echo I18N("h", "6th Border Relay IPv4 Address");?></span>
				<span class="value_ipv6"><input id="w_6rd_relay" type="text" size="15" maxlength="15" /></span></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_6rd_pdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_6rd_sdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<hr>
		</div>
		
		<div id="box_wan_ll_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "IPv6 Link-Local Address");?></span>
				<span class="value_ipv6">
					<span id="wan_ll"></span>
					<span id="wan_ll_pl"></span>
				</span>
			</div>
		</div>
				
		<div id="box_wan_tunnel" style="display:none">
			<p class="text_title"><?echo I18N("h", "IPv6 in IPv4 TUNNEL SETTINGS");?></p>
			<p class="text"><?
				echo I18N('h', 'Enter the IPv6 in IPv4 Tunnel information provided by your Tunnel Broker.');
			?></p>
		</div>			
		<br>
		<div id="box_wan_tunnel_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Remote IPv4 Address");?></span>
				<span class="value_ipv6"><input id="w_tu_rev4_ipaddr" type="text" size="21" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Remote IPv6 Address");?></span>
				<span class="value_ipv6"><input id="w_tu_rev6_ipaddr" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Local IPv4 Address");?></span>
				<span class="value_ipv6"><span id="w_tu_lov4_ipaddr"></span></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Local IPv6 Address");?></span>
				<span class="value_ipv6"><input id="w_tu_lov6_ipaddr" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Subnet Prefix Length");?></span>
				<span class="value_ipv6"><input id="w_tu_pl" type="text" size="4" maxlength="3" /></span>
			</div>
			<hr>
		</div>
	</div>
	<!-- WAN block END -->
	
	<!-- DNS START -->
	<div id="bbox_wan_dns" style="display:none">
		<div id="box_wan_dns">
			<p class="text_title"><?echo I18N("h", "IPv6 DNS SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'Obtain DNS server address automatically or enter a specific DNS server address.');
		?></p>
		<br>
		<div id="box_wan_dns_body">
			<!-->
			<div class="textinput_dns">
				<table>
					<tbody>
						<tr>
							<td><input type="radio" class="styled" id="w_dhcp_dns_auto" name="w_dhcp_dns_rad" value="auto" onclick="PAGE.OnClickDHCPDNS();" /></td>
							<td class="name_dns">&nbsp;<strong><?echo I18N("h", "Obtain IPv6 DNS Servers automatically");?></strong></td>
						</tr>
						<tr>
							<td><input type="radio" class="styled" id="w_dhcp_dns_manual" name="w_dhcp_dns_rad" value="manual" onclick="PAGE.OnClickDHCPDNS();" /></span></td>
							<td class="name_dns">&nbsp;<strong><?echo I18N("h", "Use the following IPv6 DNS Servers:");?></strong></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="textinput">
				<span class="name" style="position: absolute; left:65px"><?echo I18N("h", "Primary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_dhcp_pdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<div class="textinput">
				<span class="name" style="position: absolute; left:65px"><?echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value_ipv6"><input id="w_dhcp_sdns" type="text" size="42" maxlength="45" /></span>
			</div>
			<!-->
		</div>
		<hr>
	</div>
	<!-- DNS END -->
	
	<!-- LAN block START -->
	<div>
		<div id="box_lan" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "LAN IPv6 ADDRESS SETTINGS");?></p>
			</div>				
			<p class="text">
				<? echo I18N('h', 'Use this section to configure the internal network settings of your router.');?>
				<span id="span_dsc1" style="display:none"><?echo I18N("h", "If you change the LAN IPv6 Address here, you may need to adjust your PC network settings to access the network again.");?></span>
			</p>
			<br>
		</div>
		<div id="box_lan_pd_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Enable DHCP-PD");?></span>
				<span class="value_ipv6"><input id="en_dhcp_pd" type="checkbox" class="styled" onclick="PAGE.OnClickpd();"/></span>
			</div>
		</div>
		<div id="box_lan_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "LAN IPv6 Address");?></span>
				<span class="value_ipv6">
					<span id="l_span_6rd" style="display:none">
						<!--<label class="label" id="l_ipaddr_rd"></label>-->
					</span>
					<span id="l_span_6to4" style="display:none">
						<span id="l_prefix_6to4">XXXX:XXXX:XXXX:</span>
						<input id="l_ipaddr_6to4" type="text" size="6" maxlength="4" />:<span id="l_ipaddreui_6to4">:1</span> /64
					</span>
					<span id="l_span" style="display:none">
					<span><input id="l_ipaddr" type="text" size="42" maxlength="45" /></span>
					<span id="l_pl"></span>
				</span>
				</span>
			</div>
		</div>
		<div id="box_lan_ll_body" style="display:none">
			<div class="textinput">
				<span class="name"><?echo I18N("h", "LAN IPv6 Link-Local Address");?></span>
				<span class="value_ipv6">
					<span id="lan_ll"></span>	
					<span id="lan_ll_pl"></span>
				</span>
			</div>
		</div>
		<hr>
	</div>
	<!-- LAN block END -->
	
	<!-- LAN IPV6 ADDRESS SETTING START -->
	<div id="bbox_lan_auto" style="display:none">
		<div id="box_lan_auto" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "ADDRESS AUTOCONFIGURATION SETTINGS");?></p>
			</div>				
			<p class="text"><?
				echo I18N('h', 'Use this section to set up IPv6 autoconfiguration to assign IP addresses to the computers on your network.');
			?></p>
			<br>
		</div>
		<div id="box_lan_auto_pd" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "ADDRESS AUTOCONFIGURATION SETTINGS");?></p>
			</div>				
			<p class="text"><?
				echo I18N('h', 'Use this section to set up IPv6 autoconfiguration to assign IP addresses to the computers on your network. You can also enable DHCP-PD to delegate prefixes for routers in your LAN.');
			?></p>
			<br>
		</div>
		<div id="box_lan_auto_body" style="display:none">  
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Automatic IPv6 address assignment");?></span>
				<span class="value_ipv6"><input id="enableAuto" value="" type="checkbox" class="styled2" onClick="PAGE.OnClickAuto();"/></span>
			</div>
<!--
			<div id="box_lan_auto_pd_body" style="display:none">
				<div class="textinput">
					<span class="name"><?echo I18N("h", "Enable Automatic DHCP-PD in LAN");?></span>
					<span class="value_ipv6"><input id="en_lan_pd" value="" type="checkbox" class="styled" onClick="PAGE.OnClickLanpd();"/></span>
				</div>
			</div>
-->
			<div class="textinput">
				<span class="name"><?echo I18N("h", "Autoconfiguration Type");?></span>
				<span class="value_ipv6">
					<select id="lan_auto_type" onchange="PAGE.OnChangelan_auto_type();" class="styled3">
					<option value="STATELESSR"><?echo I18N("h", "SLAAC+RDNSS");?></option>
					<option value="STATELESSD"><?echo I18N("h", "SLAAC+Stateless DHCP");?></option>
					<option value="STATEFUL"><?echo I18N("h", "Stateful DHCPv6");?></option>
					</select>
				</span>
			</div>
		</div>
	         <div id="box_lan_stless" style="display:none">
	                        <div class="textinput">
	                                <span class="name"><?echo I18N("h", "Router Advertisement Lifetime");?></span>
	                                <span class="value_ipv6"><input id="ra_lifetime" type="text" size="5" maxlength="3" />
	                                (<?echo I18N("h", "minutes");?>)</span>
	                        </div>
	         </div>
	         <div id="box_lan_dhcp" style="display:none">
	                <div class="textinput">
				        <span class="name"><?echo I18N("h", "IPv6 Address Range (Start)");?></span>
				        <span class="value_ipv6"><input id="dhcps_start_ip_prefix" type="text" size="30" maxlength="39" />
				        <span id="sp_dli_s"></span>
				        <input id="dhcps_start_ip_value" type="text" size="3" maxlength="2" />
				        <span id="l_range_start_pl"></span>
				        </span>	
			        </div>
			        <div class="textinput">
				        <span class="name"><?echo I18N("h", "IPv6 Address Range (End)");?></span>
				        <span class="value_ipv6"><input id="dhcps_stop_ip_prefix" type="text" size="30" maxlength="39" />
				        <span id="sp_dli_e"></span>
				        <input id="dhcps_stop_ip_value" type="text" size="3" maxlength="2" />
				        <span id="l_range_end_pl"></span>
				        </span>	
			        </div>
	                <div class="textinput">
	                        <span class="name"><?echo I18N("h", "IPv6 Address Lifetime");?></span>
	                        <span class="value_ipv6"><input id="ip_lifetime" type="text" size="5" maxlength="3" />
	                        (<?echo I18N("h", "minutes");?>)</span>
	                </div>
		</div>
		<hr>
	</div>
	<!-- LAN IPV6 ADDRESS SETTING END -->
	<div class="bottom_cancel_save">
		<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
		<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
	</div>
</div>
</form>
