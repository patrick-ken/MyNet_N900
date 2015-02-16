<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Admin Password");?></p>
		</div>
		<div>
			<p class="text"><?
				if ($USR_ACCOUNTS=="1")
					echo I18N("h", "The 'admin' account can access the management interface.")." ".
					I18N("h", "The admin has read/write access and can change the password.");
				else
					echo I18N("h", "The 'admin' account can access the management interface.")." ".
					I18N("h", "The admin has read/write access and can change the password.");
			?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "The default admin password is 'password'.")." ".
				I18N("h", "It is highly recommended that you create a new password to keep your router secure.");?></p>
		</div>
		<br>
		<hr>

		<div>
			<p class="text_title"><?echo I18N("h", "Admin Password");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "Please enter the same password into both boxes for confirmation.");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Current Password");?></span>
			<span class="value"><input id="admin_porigin" type="password" size="20" maxlength="15" /></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "New Password");?></span>
			<span class="value"><input id="admin_p1" type="password" size="20" maxlength="15" /></span>
			<span class="note" id="notes" style="font-size:13px;"><?echo I18N("h", "(maximum 15 characters)");?></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Verify New Password");?></span>
			<span class="value"><input id="admin_p2" type="password" size="20" maxlength="15" /></span>
		</div>
		<br>
		<hr>

		<div>
			<p class="text_title"><?echo I18N("h", "Administration");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable HTTPS Server");?></span>
			<span class="value"><input type="checkbox" class="styled" id="stunnel" onClick="PAGE.OnClickStunnel();" /></span>
		</div>
		<br>
		
		<div id="remote_1" >
		<hr>		
			<p class="text_title"><?echo I18N("h", "Remote Management");?></p>
		</div>	
		
		<div id="remote_2" class="textinput">
			<span class="name"><?echo I18N("h", "Enable Remote Management");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_remote" /></span>
		</div>
		<div id="remote_3" class="textinput">
			<span class="name"><?echo I18N("h", "Remote Admin Port");?></span>
			<span class="value"><input id="remote_port" type="text" size="5" maxlength="5" /></span>
		</div>
		<div id="HTTPS_server_notice">
			<p class="text"><?echo I18N("h", "Notice: Remote Admin Port can not be in the external port range of Port Forwarding.");?></p>
		</div>
		<div id="remote_4" class="textinput">
			<span class="name"><?echo I18N("h", "Use HTTPS");?></span>
			<span class="value"><input type="checkbox" class="styled" id="enable_https" onClick="PAGE.OnClickEnableHttps();" /></span>
		</div>
		<br>
		<hr>

		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>
