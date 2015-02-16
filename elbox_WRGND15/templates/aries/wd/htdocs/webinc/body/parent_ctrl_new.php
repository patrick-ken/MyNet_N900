
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Internet Security & Parental Controls");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable parental control");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_parent_ctrl" onclick="PAGE.OnClickChange();"/></span>
		</div>
		<div class="smallemptyline"></div>
		<div class="textinput" id="control_option">
			<span class="name"><?echo I18N("h", "Parental control option");?></span>
			<span class="value">
				<table class="">
					<tr>
						<td><input type="radio" class="styled" id="PCoption1" name="PCradio" onclick="PAGE.OnClickRadioMethod();"/>
						</td>
						<td>&nbsp;<?echo I18N("h", "Content Filtering");?>&nbsp;
						</td>
						<td>
							<span class="help"></span>
							<span class="help_msg">
								<span class="help_title"><div class="gap"></div><? echo I18N("h", "Content Filtering");?></span>
								<span class="help_text"><? echo I18N("h", "Web filtering service to block undesirable Internet contents for safeguard of your network devices and users.");?></span>
							</span>
						</td>
					</tr>
					<tr>
						<td><input type="radio" class="styled" id="PCoption2" name="PCradio" onclick="PAGE.OnClickRadioMethod();"/>
						</td>
						<td>&nbsp;<?echo I18N("h", "Scheduling");?>&nbsp;
						</td>
						<td>
							<span class="help_showdown"></span>
							<span class="help_msg">
								<span class="help_title"><div class="gap"></div><? echo I18N("h", "Scheduling");?></span>
								<span class="help_text"><? echo I18N("h", "Control Internet access of each device by time period or by time allocation per day.");?></span>
							</span>
						</td>
					</tr>
				</table>
			</span>
		</div>
		<br>
		<div class="gap40"></div>
		<div id="first_save" class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo i18n('Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo i18n('Save');?>" />
		</div>
		<div id="hr1"><hr></div>
		<div id="limit1_title">
			<p class="text_title"><?echo I18N("h", "Internet Content Filtering");?></p>
		</div>
		<p id="limit1_Dscript" class="text">
			<? echo I18N('h', 'Internet access can be filtered to block any unwanted or dangerous contents by setting filtering level for all devices or for each device in the network. Register your router to the filtering service server first in order to use this add-on service.');?>
		</p>
		<div class="textinput" id="device_loc" style="margin-top:30px;">
			<span class="name" id="location_text"><?echo I18N("h", "Router location");?></span>
			<span class="value" id="device_span">
				<select id="device_location"  class="styled4">
					<option value="USA"><?echo I18N("h", "Americas");?></option>
					<option value="EMEA"><?echo I18N("h", "Europe, Middle East, Africa");?></option>
					<option value="APAC"><?echo I18N("h", "Asia Pacific");?></option>
				</select>
			</span>
		</div>
		<div id="policy_management" style="margin-top:30px;">
			<a id="linking" href="" target="_blank"></a>
		</div>
		<div id="Router_text" style="margin-top:30px;">
			<p class="text_title"><?echo I18N("h", "Router Registration");?></p>
			<br>
			<div class="textinput">
				<span class="name" id="email_text"><?echo I18N("h", "Email address");?></span>
				<span class="value"><input id="email_count" type="text" size="30" maxlength="100" /></span>
			</div>		
			<div class="textinput">
				<span class="name" id="password_text"><?echo I18N("h", "Password");?></span>
				<span class="value"><input id="private_password" type="text" size="30" maxlength="20" /></span>
			</div>
		</div>
		<div id ="div_restore" class="bottom_cancel_save" style="display:none;">
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RestoreUI');" value="<?echo I18N('h','Re-register Router');?>" id="restore_button">
		</div>
		<div class="bottom_cancel_save" id="register_r">
			<p id="register_result" class="text"></p><br>
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RegisterRouter');" value="<?echo I18N('h', 'Register Router');?>" id="regist_router">
		</div>
		<div>
			<p id="DHCP_text" class="text_title"><?echo I18N("h", "Device Information");?></p>
		</div>
		<p id="Dscription" class="text" style="margin-top:30px;">
			<? echo I18N('h', 'Internet access for all connected devices in the network is controlled under the router\'s master policy by default. If you want to have separate policy and time control for each device, you must select and register devices from list below. You can activate or deactivate any networked device for WD Internet Security & Parental Controls by checking the Active boxes and clicking the Register Device button.');?>
		</p>	
		<div id="DHCP_updating_text" style="display:none;">
			<p class="text"><?echo I18N("h", "Updating device information from NetStar server. Please wait.");?></p>
		</div>
		<div class="centerline" align="center" id="DHCPtable" style="margin-top:30px;">
			<table id="DHCP_info" class="general">
			<tr  align="center">
				<td width="60px"><b><?echo I18N("h", "Active");?></b></td>
				<td width="150px"><b><?echo I18N("h", "Device name");?></b></td>
				<td width="200px"><b><?echo I18N("h", "MAC address");?></b></td>
			</tr>
			</table>
		</div>
		<div class="bottom_cancel_save" id="reg_device">
			<p id="register_result2" class="text"></p>
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RegisterDevice');" value="<?echo I18N('h', 'Register Device');?>" id="regist_device">
		</div>
		<div id="hr2" style="clear:left;"><hr></div>
		<div class="parent_descript" id="option2-1">
			<p class="text_title"><?echo I18N("h", "Scheduling");?></p>
			<p class="text"><?echo I18N("h", "Select the device you wish to schedule Internet access restriction. Be sure to click Save button to save settings before selecting another device in the list to set up schedule.");?></p>
		</div>
		<br>
		<div class="parent_detail" id="option2-2">
			<select class="multi_select" id="device_list" name="device_list" size="5" multiple="multiple" onChange="PAGE.ListChange();">
				<option value="0"><?echo I18N("h", "<Guest Devices>");?></option>
			</select>
			<div class="parent_settings">
				<div>
					<span><input type="radio" class="styled" id="blocking1" name="blockradio" OnClick="PAGE.ClearBlocking2(); PAGE.ClearBlocking3(); PAGE.RestoreXMLdata();" /></span>
					<span class="ENA_text"><?echo I18N("h", "None - no scheduled Internet access restriction");?></span>
				</div>
				<div class="smallemptyline"></div>
				<div>
					<span><input type="radio" class="styled" id="blocking2" name="blockradio" OnClick="PAGE.ClearBlocking1(); PAGE.ClearBlocking3(); PAGE.RestoreXMLdata();" /></span>
					<span class="ENA_text"><?echo I18N("h", "Allow access during these days and hours");?></span>
				</div>
				<div class="gap"></div>
				<div>
					<table class="">
						<tr align="center">
							<td><b>M</b></td>
							<td><b>T</b></td>
							<td><b>W</b></td>
							<td><b>T</b></td>
							<td><b>F</b></td>
							<td><b>S</b></td>
							<td><b>Su</b></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr align="center">
							<td><span></span><input class="styled2" type="checkbox" id="Mon" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Tue" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Wen" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Thu" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Fri" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Sat" /></td>
							<td><span></span><input class="styled2" type="checkbox" id="Sun" /></td>
							<td><select id="limit2_time_start"  class="styled1">
								<option value="00:00">00:00 AM</option>
								<option value="01:00">01:00 AM</option>
								<option value="02:00">02:00 AM</option>
								<option value="03:00">03:00 AM</option>
								<option value="04:00">04:00 AM</option>
								<option value="05:00">05:00 AM</option>
								<option value="06:00">06:00 AM</option>
								<option value="07:00">07:00 AM</option>
								<option value="08:00">08:00 AM</option>
								<option value="09:00">09:00 AM</option>
								<option value="10:00">10:00 AM</option>
								<option value="11:00">11:00 AM</option>
								<option value="12:00">12:00 PM</option>
								<option value="13:00">01:00 PM</option>
								<option value="14:00">02:00 PM</option>
								<option value="15:00">03:00 PM</option>
								<option value="16:00">04:00 PM</option>
								<option value="17:00">05:00 PM</option>
								<option value="18:00">06:00 PM</option>
								<option value="19:00">07:00 PM</option>
								<option value="20:00">08:00 PM</option>
								<option value="21:00">09:00 PM</option>
								<option value="22:00">10:00 PM</option>
								<option value="23:00">11:00 PM</option>
								</select>
							</td>
							<td>To</td>
							<td><select id="limit2_time_end"  class="styled1">
								<option value="00:00">00:00 AM</option>
								<option value="01:00">01:00 AM</option>
								<option value="02:00">02:00 AM</option>
								<option value="03:00">03:00 AM</option>
								<option value="04:00">04:00 AM</option>
								<option value="05:00">05:00 AM</option>
								<option value="06:00">06:00 AM</option>
								<option value="07:00">07:00 AM</option>
								<option value="08:00">08:00 AM</option>
								<option value="09:00">09:00 AM</option>
								<option value="10:00">10:00 AM</option>
								<option value="11:00">11:00 AM</option>
								<option value="12:00">12:00 PM</option>
								<option value="13:00">01:00 PM</option>
								<option value="14:00">02:00 PM</option>
								<option value="15:00">03:00 PM</option>
								<option value="16:00">04:00 PM</option>
								<option value="17:00">05:00 PM</option>
								<option value="18:00">06:00 PM</option>
								<option value="19:00">07:00 PM</option>
								<option value="20:00">08:00 PM</option>
								<option value="21:00">09:00 PM</option>
								<option value="22:00">10:00 PM</option>
								<option value="23:00">11:00 PM</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div id="DailySetting">
				<div class="smallemptyline"></div>
				<div>
					<span><input type="radio" class="styled" id="blocking3" name="blockradio" OnClick="PAGE.ClearBlocking1(); PAGE.ClearBlocking2(); PAGE.RestoreXMLdata();" /></span>
					<span class="ENA_text"><?echo I18N("h", "Limit daily usage");?></span>
				</div>
				<div class="gap"></div>
				<div>
					<span><?echo I18N("h", "Limit weekday usage (Mon - Fri) to");?>&nbsp;</span>
					<select id="limit3_time1"  class="styled1">
						<option value="1"><?echo I18N("h", "1 hour");?></option>
						<option value="2"><?echo I18N("h", "2 hours");?></option>
						<option value="3"><?echo I18N("h", "3 hours");?></option>
						<option value="4"><?echo I18N("h", "4 hours");?></option>
						<option value="5"><?echo I18N("h", "5 hours");?></option>
						<option value="6"><?echo I18N("h", "6 hours");?></option>
						<option value="7"><?echo I18N("h", "7 hours");?></option>
						<option value="8"><?echo I18N("h", "8 hours");?></option>
						<option value="9"><?echo I18N("h", "9 hours");?></option>
						<option value="10"><?echo I18N("h", "10 hours");?></option>
						<option value="11"><?echo I18N("h", "11 hours");?></option>
						<option value="12"><?echo I18N("h", "12 hours");?></option>
						<option value="13"><?echo I18N("h", "13 hours");?></option>
						<option value="14"><?echo I18N("h", "14 hours");?></option>
						<option value="15"><?echo I18N("h", "15 hours");?></option>
						<option value="16"><?echo I18N("h", "16 hours");?></option>
						<option value="17"><?echo I18N("h", "17 hours");?></option>
						<option value="18"><?echo I18N("h", "18 hours");?></option>
						<option value="19"><?echo I18N("h", "19 hours");?></option>
						<option value="20"><?echo I18N("h", "20 hours");?></option>
						<option value="21"><?echo I18N("h", "21 hours");?></option>
						<option value="22"><?echo I18N("h", "22 hours");?></option>
						<option value="23"><?echo I18N("h", "23 hours");?></option>
					</select>					
					<span>&nbsp;<?echo I18N("h", "per day");?></span>
				</div>
				<div class="gap"></div>
				<div>
					<span><?echo I18N("h", "Limit weekend usage (Sat - Sun) to");?>&nbsp;</span>
					<select id="limit3_time2"  class="styled1">
						<option value="1"><?echo I18N("h", "1 hour");?></option>
						<option value="2"><?echo I18N("h", "2 hours");?></option>
						<option value="3"><?echo I18N("h", "3 hours");?></option>
						<option value="4"><?echo I18N("h", "4 hours");?></option>
						<option value="5"><?echo I18N("h", "5 hours");?></option>
						<option value="6"><?echo I18N("h", "6 hours");?></option>
						<option value="7"><?echo I18N("h", "7 hours");?></option>
						<option value="8"><?echo I18N("h", "8 hours");?></option>
						<option value="9"><?echo I18N("h", "9 hours");?></option>
						<option value="10"><?echo I18N("h", "10 hours");?></option>
						<option value="11"><?echo I18N("h", "11 hours");?></option>
						<option value="12"><?echo I18N("h", "12 hours");?></option>
						<option value="13"><?echo I18N("h", "13 hours");?></option>
						<option value="14"><?echo I18N("h", "14 hours");?></option>
						<option value="15"><?echo I18N("h", "15 hours");?></option>
						<option value="16"><?echo I18N("h", "16 hours");?></option>
						<option value="17"><?echo I18N("h", "17 hours");?></option>
						<option value="18"><?echo I18N("h", "18 hours");?></option>
						<option value="19"><?echo I18N("h", "19 hours");?></option>
						<option value="20"><?echo I18N("h", "20 hours");?></option>
						<option value="21"><?echo I18N("h", "21 hours");?></option>
						<option value="22"><?echo I18N("h", "22 hours");?></option>
						<option value="23"><?echo I18N("h", "23 hours");?></option>
					</select>					
					<span>&nbsp;<?echo I18N("h", "per day");?></span>
				</div>
				</div>
			</div>
		</div>
		<div id="last_save" class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo i18n('Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo i18n('Save');?>" />
		</div>
	</div>
</form>

