<? include "/htdocs/webinc/body/draw_elements.php"; ?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Email Settings");?></p>
		</div>

		<div>
			<p class="text">
				<?echo I18N("h", "The Email feature can be used to send the system log files, router alert messages, and firmware update notifications to your email address.");?>
			</p>
		</div>
		<hr>	
		
		<div>
			<p class="text_title"><?echo I18N("h", "Email Notification");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable Email Notification");?></span>
			<span class="value"><input id="en_mail" type="checkbox" class="styled" onclick="PAGE.OnClickEnable();"/></span>
		</div>

		<div>
			<p class="text_title"><?echo I18N("h", "Email Settings");?></p>
		</div>
		
		<div class="textinput">
			<span class="name"><?echo I18N("h", "From Email Address");?></span>
			<span class="value"><input id="from_addr" type="text" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "To Email Address");?></span>
			<span class="value"><input id="to_addr" type="text" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Email Subject");?></span>
			<span class="value"><input id="email_subject" type="text" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "SMTP Server Address");?></span>
			<span class="value"><input id="smtp_server_addr" type="text" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "SMTP Server Port");?></span>
			<span class="value"><input id="smtp_server_port" type="text" size="20" maxlength="5"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable Authentication");?></span>
			<span class="value"><input id="authenable" type="checkbox" class="styled" onclick="PAGE.OnClickAuthEnable();"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Account Name");?></span>
			<span class="value"><input id="account_name" type="text" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Password");?></span>
			<span class="value"><input id="passwd" type="password" size="20" maxlength="64"/></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Verify Password");?></span>
			<span class="value">
				<input id="verify_passwd" type="password" size="20" maxlength="64"/>
				<input id="sendmail" style="margin-left: 8px;" class="button_blueX2" type="button" value="<?echo I18N("h", "Send Mail Now");?>" onClick="PAGE.OnClickSendMail();"/>
			</span>
		</div>
		<hr>
		<div class="textinput" id="send_msg" style="display:none">
			<span class="name"></span>
			<span class="value"><font color="red"><?echo I18N("h", "(Mail sent already!)");?></font></span>
		</div>	


		<div>
			<p class="text_title"><?echo I18N("h", "Email log when FULL or on Schedule");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "On Log Full");?></span>
			<span class="value"><input id="en_logfull" type="checkbox" class="styled" /></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "On Schedule");?></span>
			<span class="value"><input id="en_log_sch" type="checkbox" class="styled" onclick="PAGE.OnClickEnableSchedule();"/></span>
		</div>
		<?
		if ($FEATURE_NOSCH != "1")
		{
			echo '<div class="textinput">';
			echo '<span class="name">'.I18N("h", "Schedule").'</span>';
			echo '<span class="value">';
			DRAW_select_sch("log_sch", I18N("h", "Never"), "-1", "PAGE.OnChangeSchedule()", 0, "narrow");
			echo '</span></div>';
			echo '<div class="textinput">';
			echo '<span class="name">'.I18N("h", "Detail").'</span>';
			echo '<span class="value"><input id="log_detail" type="text" size="40"/>';
			echo '</span></div>';
		}
		?>
		<hr>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
