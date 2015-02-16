<form id="mainform" onsubmit="return false;">
	<div id="div_wps_content">
        <div>
            <p class="text_title">Wi-Fi Protected Setup (WPS)</p>
        </div>
        <div style="height:20px;"></div>
        <div class="textinput">
            <span class="name"><? echo I18N("h", "Enable WPS");?></span>
            <span class="value">
                <input id="en_wps" type="checkbox" class="styled" onClick="PAGE.OnClickEnWPS('');" />
            </span>
        </div>
        <div class="textinput">
            <span class="name"><? echo I18N("h", "Enable Router's WPS PIN");?></span>
            <span class="value">
                <input id="en_wps_pin" type="checkbox" class="styled" />
            </span>
        </div>
        <div class="bottom_cancel_save">
            <input type="button" class="button_black" id="reload" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;
            <input type="button" class="button_blue" id="onsumit" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
        </div>
		<div id="div_wps_options">
			<hr>
			<div>
				<p class="text_title"><?echo I18N("h", "Add a new wireless device to this network using WPS");?></p>
			</div>
			<div>
				<p class="text"><? echo I18N("h", "Choose one of following three WPS methods to connect a wireless device to your network.");?></p>
			</div>
			<br>
			<div>
				<table>
					<tr>
						<td colspan=4><p class="text_title"><? echo I18N("h", "1. Push Button Method");?></p></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td><p class="text"><? echo I18N("h", "Press the WPS button");?></p></td>
						<td><img src="/pic/wps_small.png"></td>
						<td><p class="text"><? echo I18N("h", "shown here to activate the connection process.");?></p></td>
					</tr>
				</table>
			</div>
			<input type="button" class="button_WPS" id="connect_pbc" onclick="PAGE.OnClickConnectPBC();" onmouseover="this.style.cursor='pointer';" style="margin-left: 289px;"></td>		
			<div>
				<table>
					<tr>
						<td colspan=4><p class="text_title"><? echo I18N("h", "2. Device PIN Code");?></p></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td colspan=3><p class="text"><? echo I18N("h", "Enter the wireless device's WPS PIN here and click Start to activate the connection process.");?></p></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td><p class="text"><? echo I18N("h", "Device's WPS PIN Code : ");?></p></td>
						<td><input id="pincode" type="text" size="9" maxlength="9"></td>
						<td><input type="button" class="button_blue" id="connect_pin" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" onclick="PAGE.OnClickConnectPIN();" value="<?echo I18N('h', 'Start');?>"></td>
					</tr>
				</table>
			</div>
			<div>
				<table>
					<tr>
						<td colspan=4><p class="text_title"><? echo I18N("h", "3. Router PIN Code");?></p></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td colspan=3><p class="text"><? echo I18N("h", "Enter this router's PIN at your wireless device to connect.");?></p></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td><p class="text"><? echo I18N("h", "Router/Access Point PIN Code : ");?></p></td>
						<td colspan=2><span id="pin" class="value"></span></td>
					</tr>
				</table>
			</div>
			<div style="margin-left:20px;">
				<input id="gen_pin" type="button" class="button_blueX2" id="onsumit" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" value="<?echo I18N("h", "Generate New PIN");?>" onClick="PAGE.OnClickGenPIN();" />&nbsp;&nbsp;
				<input id="reset_pin" type="button" class="button_blueX2" id="onsumit" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" value="<?echo I18N("h", "Reset PIN to Default");?>" onClick="PAGE.OnClickResetPIN();" />
			</div>
		</div>
	</div>
<!--
	<div id="m" class="title1" style="width: 625px;">
		<div><p><span id="msg"></span></p></div>
	</div>
-->
</form>
