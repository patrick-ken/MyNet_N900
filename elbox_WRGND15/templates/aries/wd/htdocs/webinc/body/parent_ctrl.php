
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
		<br>
		<div class="textinput">
			<span class="name" id="location_text"><?echo I18N("h", "Router location");?></span>
			<span class="value" id="device_span">
				<select id="device_location"  class="styled4">
					<option value="USA"><?echo I18N("h", "Americas");?></option>
					<option value="EMEA"><?echo I18N("h", "Europe, Middle East, Africa");?></option>
					<option value="APAC"><?echo I18N("h", "Asia Pacific");?></option>
				</select>
			</span>
		</div>
		<br>
		<div id="policy_management">
			<a id="linking" href="" target="_blank"></a>
		</div>
		<div class="bottom_cancel_save">
			<input type="button" class="button_blueX2" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Apply');?>" id="button_apply">
		</div>
		<div id="hr1"><hr></div>
		<div>
			<p id="Router_text" class="text_title"><?echo I18N("h", "Router Registration");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name" id="email_text"><?echo I18N("h", "Email address");?></span>
			<span class="value"><input id="email_count" type="text" size="30" maxlength="100" /></span>
		</div>		
		<div class="textinput">
			<span class="name" id="password_text"><?echo I18N("h", "Password");?></span>
			<span class="value"><input id="private_password" type="text" size="30" maxlength="20" /></span>
		</div>
		<div id ="div_restore" class="bottom_cancel_save" style="display:none;">
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RestoreUI');" value="<?echo I18N('h','Re-register Router');?>" id="restore_button">
		</div>
		<div class="bottom_cancel_save">
			<p id="register_result" class="text"></p><br>
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RegisterRouter');" value="<?echo I18N('h', 'Register Router');?>" id="regist_router">
		</div>
		<div>
			<p id="hide_button_info" class="text"></p>
		</div>
		<div id="hr2" style="clear:left;"><hr></div>
		<div>
			<p id="DHCP_text" class="text_title"><?echo I18N("h", "Device Information");?></p>
		</div>
		<p id="Dscription" class="text">
			<? echo I18N('h', 'Internet access for all connected devices in the network is controlled under the router\'s master policy by default. If you want to have separate policy and time control for each device, you must select and register devices from list below. You can activate or deactivate any networked device for WD Internet Security & Parental Controls by checking the Active boxes and clicking the Register Device button.');?>
		</p>
		<br>	
		<div>
			<p id="DHCP_updating_text" class="text" style="display:none;"><?echo I18N("h", "Updating device information form NetStar server. Please wait.");?></p>
		</div>
		<div class="centerline" align="center">
			<table id="DHCP_info" class="general">
			<tr  align="center">
				<td width="60px"><b><?echo I18N("h", "Active");?></b></td>
				<td width="150px"><b><?echo I18N("h", "Device name");?></b></td>
				<td width="200px"><b><?echo I18N("h", "MAC address");?></b></td>
			</tr>
			</table>
		</div>
		<div class="bottom_cancel_save">
			<p id="register_result2" class="text"></p>
			<input type="button" class="button_blueX2" onclick="PAGE.OnClickButtonRegister('RegisterDevice');" value="<?echo I18N('h', 'Register Device');?>" id="regist_device">
		</div>	
	</div>
</form>

