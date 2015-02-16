<form id="mainform">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Device Mode");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "Configure this device to operate in Router or Extender Mode.");?></p>
		</div>
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Device Mode");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Mode ");?></span>
			<span class="value">
				<select id="dev_mode" class="styled5" onChange="PAGE.OnChangeLayout(this.value);">
        			<option value="router"><?echo I18N("h", "Router Mode");?></option>
        			<option value="bridge"><?echo I18N("h", "AP (Access Point) Mode");?></option>
       			</select>
			</span>
		</div>
		<hr>
	</div>
	
	<div id="ap_lan_ipv4_settings" style="display:none;">
		<div class="textinput">
			<span class="name"><? echo I18N("h", "LAN Connection Type");?></span>
			<span class="value">
				<select id="lan_type_v4" onChange="PAGE.OnChangeLANType(this.value);" class="styled5">
					<option value="lan_static"><? echo I18N("h", "Static IP");?></option>
					<option value="lan_dynamic"><? echo I18N("h", "Dynamic IP (DHCP)");?></option>
				</select>
			</span>
		</div>
		<br>
		<div class="blackbox" id="ipv4_conn_type">
			<div>
				<p class="text_title"><?echo I18N("h", "STATIC IP LAN CONNECTION TYPE");?></p>
			</div>			
			<div>	
				<p class="text"><? echo I18N("h", "This Internet setting will be used in the LAN side when the device is working in the router mode.");?></p>
			</div>
			<br>
			
			<div class="textinput">
				<span class="name"><? echo I18N("h", "LAN IP Address");?></span>
				<span class="value"><input id="ipaddr_v4" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Subnet Mask");?></span>
				<span class="value"><input id="netmask_v4" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Default Gateway");?></span>
				<span class="value"><input id="gateway_v4" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Primary DNS Server");?></span>
				<span class="value"><input id="dns1_v4" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Secondary DNS Server");?></span>
				<span class="value"><input id="dns2_v4" type="text" size="20" maxlength="15" /></span>
			</div>
			<br>
		</div>
	</div>
	
	<div class="bottom_cancel_save">
		<input type="button" class="button_black" id="reload" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;
		<input type="button" class="button_blue" id="onsumit" onclick="PAGE.BeforeOnSubmit();" value="<?echo I18N('h', 'Save');?>">
	</div>
</form>

