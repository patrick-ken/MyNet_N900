<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Host Name");?></p>
		</div>
		<div>	
			<p class="text"><? echo I18N("h", "The host name is used to identify your device in the network.");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Host Name");?></span>
			<span class="value"><input id="device" type="text" size="20" maxlength="15" /></span>
		</div>
		<hr>
		
		<div <? if($layout!="router") echo "style='display:none;'";?>>			
			<div>
				<p class="text_title"><?echo I18N("h", "IP Address");?></p>
			</div>
			<div>
				<p class="text"><? echo I18N("h", "This Internet setting will be used in the LAN side when the device is working in the router mode.");?></p>
			</div>
			<br>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "LAN IP Address");?></span>
				<span class="value"><input id="ipaddr" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Subnet Mask");?></span>
				<span class="value"><input id="netmask" type="text" size="20" maxlength="15" /></span>
			</div>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Enable DNS Proxy");?></span>
				<span class="value"><input id="dnsr" type="checkbox" class="styled" /></span>
			</div>			
		</div>
		
		<div <? if($layout!="bridge") echo "style='display:none;'";?>>
			<div class="textinput">
				<span class="name"><? echo I18N("h", "LAN Connection Type");?></span>
				<span class="value">
					<select id="lan_type_v4" onChange="PAGE.OnChangeLANType(this.value);" class="styled2">
						<option value="lan_static">Static IP</option>
						<option value="lan_dynamic">Dynamic IP (DHCP)</option>
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
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Enable as the Local Master Browser");?></span>
				<span class="help" style="padding-left: 12px;"></span>
				<span class="help_msg">
					<span class="help_title"><div class="gap"></div><? echo I18N("h", "Local Master Browser");?></span>
					<span class="help_text"><? echo I18N("h", "The Local Master Browser works on a subnet providing a resource list of available clients.");?></span>
				</span>
				<span class="value"><input id="locm" type="checkbox" class="styled" /></span>
			</div>
		<hr>	
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
