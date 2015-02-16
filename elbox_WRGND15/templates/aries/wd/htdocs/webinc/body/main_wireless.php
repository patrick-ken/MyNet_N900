<form id="mainform" onsubmit="return false;">
	<p class="wireless_title"><? echo I18N("h", "Secure your wireless network.");?></p>
	<span class="name" style="font-size:16px;"><? echo I18N("h", "Why do I have two network names?");?></span>
	<span class="help_showdown" style="padding-left: 12px;"></span>
	<span class="help_msg">
		<span class="help_title"><? echo I18N("h", "Why do I have two network names?");?></span>
		<span class="help_text"><? echo I18N("h", "Your new WD router is capable of operating in two wireless bands, 2.4 GHz and 5 GHz, simultaneously. It is possible to use same network name (SSID) for both bands but it is recommended to use two different names for better performance and efficiency.");?></span>
	</span>
	<div class="gap"></div>
	<span class="name" style="font-size:16px;"><? echo I18N("h", "What is the difference between 2.4 GHz and 5 GHz?");?></span>
	<span class="help" style="padding-left: 12px;"></span>
	<span class="help_msg">
		<span class="help_title"><? echo I18N("h", "What is the difference between 2.4 GHz and 5 GHz?");?></span>
		<span class="help_text"><? echo I18N("h", "The 2.4 GHz band is a smaller band used by older wireless devices. This band can occasionally experience interference from other devices using the same frequency (channel).  The 5 GHz on the other hand offers numerous non overlapping channels and therefore is not as susceptible to interference, making it ideal for AV media.");?></span>
	</span>
	<div class="gap"></div>
	<span class="name" style="font-size:16px;"><? echo I18N("j", "You can change the given wireless settings below.If you are replacing another router, click here for the guideline.");?></span>
	<span class="help" style="padding-left: 12px;"></span>
	<span class="help_msg">
		<span class="help_title"><? echo I18N("h", "Replace Your Current Router");?></span>
		<span class="help_text"><? echo I18N("h", "Enter your current wireless network names and passwords in the given fields.If you are replacing a single band router, it is most likely you currently have 2.4 GHz wireless network. Enter this network name in the Network Name (2.4GHz) field and the current password in the Password field.");?></span>
	</span>
	<div id="div_24G">
		<div class="wireless_input">
			<table>
				<tbody>
					<tr>
						<td class="text_line"><span class="name_c"><? echo I18N("h", "Network Name (2.4GHz)");?></span></td>
						<td><span class="value_c"><input id="ssid" type="text" maxlength="32" class="wireless_inputtext" value=""></span></td>
					</tr>
					<tr>
						<td><span class="name_c"><? echo I18N("h", "Password");?></span></td>
						<td rowspan="2"><span class="value_c"><input id="wpapsk" type="text" maxlength="64" class="wireless_inputtext" value=""></span></td>
					</tr>
					<tr>
						<td><span class="little_msg"><? echo I18N("h", "(minimum 8 characters)");?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div style="height: 4px;"></div>
	<div id="div_5G">
		<div class="wireless_input">
			<table>
				<tbody>
					<tr>
						<td class="text_line"><span class="name_c"><?echo I18N("h", "Network Name (5GHz)");?></span></td>
						<td><span class="value_c"><input id="ssid_Aband" type="text" maxlength="32" class="wireless_inputtext"></span></td>
					</tr>
					<tr>
						<td><span class="name_c"><? echo I18N("h", "Password");?></span></td>
						<td rowspan="2"><span class="value_c"><input id="wpapsk_Aband" type="text" maxlength="64" class="wireless_inputtext"></span></td>
					</tr>
					<tr>
						<td><span class="little_msg"><? echo I18N("h", "(minimum 8 characters)");?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" id="reload" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" id="onsumit" onclick="PAGE.GoNext(0);" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
