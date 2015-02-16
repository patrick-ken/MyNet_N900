<form id="mainform" onsubmit="return false;">
	<div id="div_wps_content">
		<div>
			<p class="text_title"><?echo I18N("h", "Wi-Fi Protected Setup (WPS)");?></p>
		</div>
		<div style="height:20px;"></div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Enable WPS");?></span>
			<span class="value">
				<input id="en_wps" type="checkbox" class="styled" onClick="PAGE.OnClickEnWPS('');" />
				</span>
		</div>
		<hr>
		<div>	
			<p class="text"><? echo I18N("h", "If your device is requesting the router WPS PIN, enter the following :");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Router/Ap PIN Code");?></span>
			<span id="pin" class="value">aaaa</span>
		</div>
		
		<div class="textinput">
			<span class="name"></span>
			<span id="pin" class="value">
				<input id="gen_pin" type="button" class="button_blueX3" id="onsumit" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" value="<?echo I18N("h", "Generate New PIN");?>"
				onClick="PAGE.OnClickGenPIN();" />
				</span>
		</div>
		<div class="textinput">
			<span class="name"></span>
			<span id="pin" class="value">
				
				<input id="reset_pin" type="button" class="button_blueX3" id="onsumit" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" value="<?echo I18N("h", "Reset PIN to Default");?>"
				onClick="PAGE.OnClickResetPIN();" />&nbsp;&nbsp;
				</span>
		</div>
		
		<br>
		<hr>
		<div>
			<p class="text_title"><?echo I18N("h", "Add new wireless device into this network");?></p>
		</div>
		<div>	
			<p class="text"><? echo I18N("h", "You can add new wireless devices into your network via the following two ways. ");?></p>
			<p class="text"><? echo I18N("h", "1. If your wireless device has PIN code, please fill PIN code of your wireless device below. Then click \"Connect\" button, start to connect to your wireless device.");?></p>
		</div>				
		<br>
		<div class="textinput" style="height: 30px;">
			<span class="name"><? echo I18N("h", "PIN Code");?></span>
			<span class="value">
				<input id="pincode" type="text" size="9" maxlength="9" />
			</span>
		</div>
		<div class="textinput">
			<span class="name"></span>
			<span class="value">
				<input type="button" class="button_blueX3" id="connect_pin" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" onclick="PAGE.OnClickConnectPIN();" value="<?echo I18N('h', 'Connect PIN');?>">
			</span>
		</div>
		<div>	
			<p class="text"><? echo I18N("h", "2. If your wireless device has Push Button, please click the \"Virtual Button\" below. Then push the button on your wireless device, start to connect to your wireless device.");?></p>
		</div>			
		
		<div class="textinput">
			<span class="name"></span>
			<span class="value">
				<input type="button" class="button_blueX3" id="connect_pbc" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" onclick="PAGE.OnClickConnectPBC();" value="<?echo I18N('h', 'Connect PBC');?>">
			</span>
		</div>
		
		<div style="height:30px;"></div>
		<hr>
		<div class="bottom_revert_save">
			<input type="button" class="button_black" id="reload" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Revert');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" id="onsumit" onMouseOver="TEMP_ButtonMouseOver(this.id)" onMouseOut="TEMP_ButtonMouseOut(this.id)" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>

<!--	
	<div id="m" class="title1" style="width: 625px;">
		<div><p><span id="msg"></span></p></div>
	</div>
-->
</form>