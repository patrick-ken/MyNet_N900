<?
include "/htdocs/webinc/body/draw_elements.php";
?>
<form id="mainform" onsubmit="return false;">
	<div id="div_gzone_content">
		<div>
			<p class="text_title"><?echo I18N("h", "Guest Access");?></p>
		</div>
		<div>	
			<p class="text"><? echo I18N("h", "Guest Network allows visitors to get Internet access without giving them access to your home network. ");?></p>
			<p class="text"><? echo I18N("h", "You can set two separate guest network names and network passkeys for 2.4GHz and 5GHz wireless networks. ");?></p>
		</div>				
				
		<div style="height:20px;"></div>
		<table>
			<tr>
				<td style="width: 390px;vertical-align: top;">
					<div class="textinput">
						<span class="title"> <?echo I18N("h", "Wireless 2.4Ghz");?></span> 
						<span class="value_c">
							<input id="en_gzone" type="checkbox" class="styled" onClick="PAGE.OnClickEnGzone('');" />
						</span>
					</div>
					<br>
					<div class="textinput">
						<span class="name_c"><?echo I18N("h", "Network Name (SSID)");?></span>
						<span class="value_c"><input id="ssid" class="same_width" type="text" maxlength="32"></span>
					</div>
								
					<div class="textinput">
						<span class="name_c"><?echo I18N("h", "Security Mode");?></span>
						<span class="value_c">
							 <select id="security_type" onChange="PAGE.OnChangeSecurityType('');" class="styled3">
									<option value=""><?echo I18N("h", "None");?></option>
									<option value="111"><?echo I18N("h", "None1");?></option><!--Joseph-->
			         </select>
						</span>
					</div>
					
					<!-- Security -->
					<div id="wep">
						<div class="textinput" style="display:none;">
							<span class="name_c">Authentication</span>
							<span class="value_c">
								 <select id="auth_type" onChange="PAGE.OnChangeWEPAuth('');" class="styled3">
	                    <option value="WEPAUTO">Both</option>
	                    <option value="SHARED">Shared Key</option>
	                </select>
							</span>
						</div>
						<div class="textinput" style="display:none;">
							<span class="name_c">WEP Encryption</span>
							<span class="value_c">
								<select id="wep_key_len" onChange="PAGE.OnChangeWEPKey('');" class="styled3">
                    <option value="64">64Bit</option>
                    <option value="128">128Bit</option>
                </select>
                <select style="display:none;" id="wep_def_key" onChange="PAGE.OnChangeWEPKey('');" class="styled3">
                        <option value="1">WEP Key 1</option>
                </select>
							</span>
						</div>
						<div id="wep_64" class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
							<span class="value_c">
								<input id="wep_64_1" name="wepkey_64" type="text" size="15" maxlength="10" />
								(5 ASCII or 10 HEX)
							</span>
						</div>
						<div id="wep_128" class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
							<span class="value_c">
								<input id="wep_128_1" name="wepkey_128" type="text" size="15" maxlength="26" />
								(13 ASCII or 26 HEX)
							</span>
						</div>
					</div>
					
					<div id="wpa">
						<div class="textinput" style="display:none;">
							<span class="name_c">Cipher Type</span>
							<span class="value_c">
								<select id="cipher_type" class="styled3">
			                        <option value="TKIP">TKIP</option>
			                        <option value="AES">AES</option>
			                        <option value="TKIP+AES">TKIP and AES</option>
			                    </select>
							</span>
						</div>
						<div class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "PSK / EAP");?></span>
							<span class="value_c">
								<select id="psk_eap" onChange="PAGE.OnChangeWPAAuth('');" class="styled3">
									<option value="psk">PSK</option>
									<option value="eap">EAP</option>
								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Password");?></span>
							<span class="value_c">
								<input id="wpapsk" class="same_width" type="text" maxlength="64" size="20">
							</span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "RADIUS Server IP Address");?></span>
							<span class="value_c"><input id="srv_ip" type="text" size="15" maxlength="15" /></span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "Port");?></span>
							<span class="value_c"><input id="srv_port" type="text" size="5" maxlength="5" /></span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "Shared Secret");?></span>
							<span class="value_c"><input id="srv_sec" type="password" size="20" maxlength="64" /></span>
						</div>
					</div>
					<!-- Security end --> 
				</td>




				<td id="div_5G" style="width: 390px;vertical-align: top;">
					<div class="textinput">
						<span class="title"> <?echo I18N("h", "Wireless 5Ghz");?></span> 
						<span class="value_c">
							<input id="en_gzone_Aband" type="checkbox" class="styled" onClick="PAGE.OnClickEnGzone('_Aband');" />
						</span>
					</div>
					<br>
					<div class="textinput">
						<span class="name_c"><?echo I18N("h", "Network Name (SSID)");?></span>
						<span class="value_c"><input id="ssid_Aband" class="same_width" type="text" maxlength="32" size="20"></span>
					</div>
								
					<div class="textinput">
						<span class="name_c"><?echo I18N("h", "Security Mode");?></span>
						<span class="value_c">
							 <select id="security_type_Aband" class="styled3" onChange="PAGE.OnChangeSecurityType('_Aband');">
									<option value=""><?echo I18N("h", "None");?></option>
			         </select>
						</span>
					</div>
					
					<!-- Security -->
					<div id="wep_Aband">
						<div class="textinput" style="display:none;">
							<span class="name_c">Authentication</span>
							<span class="value_c">
								 <select id="auth_type_Aband" onChange="PAGE.OnChangeWEPAuth('_Aband');" class="styled3">
	                    <option value="WEPAUTO">Both</option>
	                    <option value="SHARED">Shared Key</option>
	                </select>
							</span>
						</div>
						<div class="textinput" style="display:none;">
							<span class="name_c">WEP Encryption</span>
							<span class="value_c">
								<select id="wep_key_len_Aband" onChange="PAGE.OnChangeWEPKey('_Aband');" class="styled3">
                    <option value="64">64Bit</option>
                    <option value="128">128Bit</option>
                </select>
                <select style="display:none;" id="wep_def_key_Aband" onChange="PAGE.OnChangeWEPKey('_Aband');" class="styled3">
                        <option value="1">WEP Key 1</option>
                </select>
							</span>
						</div>
						<div id="wep_64_Aband" class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
							<span class="value_c">
								<input id="wep_64_1_Aband" name="wepkey_64_Aband" type="text" size="15" maxlength="10" />
								(5 ASCII or 10 HEX)
							</span>
						</div>
						<div id="wep_128_Aband" class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
							<span class="value_c">
								<input id="wep_128_1_Aband" name="wepkey_128_Aband" type="text" size="15" maxlength="26" />
								(13 ASCII or 26 HEX)
							</span>
						</div>
					</div>
					
					<div id="wpa_Aband">
						<div class="textinput" style="display:none;">
							<span class="name_c">Cipher Type</span>
							<span class="value_c">
								<select id="cipher_type_Aband" class="styled3">
                    <option value="TKIP">TKIP</option>
                    <option value="AES">AES</option>
                    <option value="TKIP+AES">TKIP and AES</option>
                </select>
							</span>
						</div>
						<div class="textinput" style="display:none;">
							<span class="name_c"><?echo I18N("h", "PSK / EAP");?></span>
							<span class="value_c">
								<select id="psk_eap_Aband" onChange="PAGE.OnChangeWPAAuth('_Aband');" class="styled3">
									<option value="psk">PSK</option>
									<option value="eap">EAP</option>
								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Password");?></span>
							<span class="value_c">
								<input id="wpapsk_Aband" class="same_width" type="text" maxlength="64" size="20">
							</span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "RADIUS Server IP Address");?></span>
							<span class="value_c"><input id="srv_ip_Aband" type="text" size="15" maxlength="15" /></span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "Port");?></span>
							<span class="value_c"><input id="srv_port_Aband" type="text" size="5" maxlength="5" /></span>
						</div>
						<div name="eap" class="textinput">
							<span class="name_c"><?echo I18N("h", "Shared Secret");?></span>
							<span class="value_c"><input id="srv_sec_Aband" type="password" size="20" maxlength="64" /></span>
						</div>
					</div>
					<!-- Security end --> 
				</td>


			<tr>
		</table>
		
			<div class="textinput" style="margin-bottom: 15px;display:none;">
				<span class="name">Schedule</span>
				<span class="value">
				<?
					if ($FEATURE_NOSCH!="1")
					{
						DRAW_select_sch("sch_gz", I18N("h", "Always"), "", "", "0", "styled3");
						echo '<input id="go2sch_gz" type="button" style="margin-top: 5px;" value="'.I18N("h", "New Schedule").'" onClick="javascript:self.location.href=\'./tools_sch.php\';" />\n';
					}
				?>	
				</span>
			</div>
		<hr>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" id="reload" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" id="onsumit" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
