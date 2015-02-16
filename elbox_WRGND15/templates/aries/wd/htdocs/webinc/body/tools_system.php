<div>
	<div>
		<p class="text_title"><?echo I18N("h", "Save and Restore Settings");?></p>
	</div>
	<div>
		<p class="text">
		<?echo I18N("h", "Once the router is configured, you can save the configuration settings to a configuration file on your computer. You also have the option to load configuration settings, or restore the factory default settings.");?>
		</p>
	</div>
	<hr>
	<form id="dlcfgbin" action="dlcfg.cgi" method="post">
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Save Settings to a File");?></span>
			<span class="value">
				<input id="id_btn_save" type="button" class="button_blueX4" value="<?echo I18N("h", "Save Configuration");?>" onClick="PAGE.OnClickDownload();" />
			</span>
		</div>
	</form>

	<form id="ulcfgbin" action="seama.cgi" method="post" enctype="multipart/form-data">
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Restore Settings from a File");?></span>
			<span class="value">
				<input type="hidden" name="REPORT_METHOD" value="301" />
				<input type="hidden" name="REPORT" value="tools_sys_ulcfg.php" />
				<input type="file" id="ulcfg" name="sealpac" size="45" class="uploadunit" 
				onchange="var str=this.form.sealpac.value;
				var ind=-1;
				ind=str.lastIndexOf('\\');
				if(ind!=-1) OBJ('dst').value=str.substr(ind+1,str.length-ind-1);
				else OBJ('dst').value=str;"
				/>
				<input type="text" class="upload_text" id="dst" maxlength="100" size="31" />
				<input type="button" class="upload_button" id="browse_btn" value="<?echo I18N("h", "Browse");?>" />
			</span>
		</div>
		<div class="textinput">
			<span class="name"></span>
			<span class="value">
				<input id="id_btn_load" type="button" class="button_blueX4" value="<?echo I18N("h", "Restore Configuration");?>" onClick="PAGE.OnClickUpload();" />
			</span>
		</div>
	</form>

	<form>
		<div class="textinput">
			<span class="name" id="Reset_text" ><?echo I18N("h", "Reset to Factory Default Settings");?></span>
			<span class="value">
				<input id="id_btn_restore" type="button" class="button_blueX4" value="<?echo I18N("h", "Reset");?>" onClick="PAGE.OnClickFReset();" />
			</span>
		</div>
	</form>

	<form>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Reboot the Router");?></span>
			<span class="value">
				<input id="id_btn_reboot" type="button" class="button_blueX4" value="<?echo I18N("h", "Reboot");?>" onClick="PAGE.OnClickReboot();" />
			</span>
		</div>
	</form>
	<hr>
	<div>
		<p class="text_title"><?echo I18N("h", "Logout");?></p>
	</div>
	<div>
		<p class="text">
		<?echo I18N("h", "");?>
		</p>
	</div>
	<hr>
	<div class="textinput">
			<span class="name"><?echo I18N("h", "Logout");?></span>
			<span class="value">
				<input id="id_btn_logout" type="button" class="button_blueX4" value="<?echo I18N("h", "Logout");?>" onClick="PAGE.OnClickLogout();" />
			</span>
	</div>
</div>
