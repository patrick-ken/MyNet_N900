<? include "/htdocs/webinc/body/draw_elements.php"; ?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "DMZ SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'DMZ means "Demilitarized Zone". The DMZ will allow a computer behind the router firewall to be accessible to Internet traffic, if the firewall is not blocking the services.');
		?></p>	
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "DMZ Host");?></p>
		</div>
		<p class="text"><?
			echo I18N('h', 'The DMZ option lets you allow WAN traffic to a single computer on your network. Note: Putting a computer in the DMZ may expose that computer to a variety of security risks. Use of this option is only recommended as a last resort.');
		?></p>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable DMZ");?></span>
			<span class="value"><input type="checkbox" class="styled" id="dmzenable" onclick="PAGE.OnClickDMZEnable();" /></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "DMZ IP Address");?></span>
			<span class="value">
				<span>
					<? DRAW_select_dhcpclist("LAN-1","hostlist", I18N("h", "Computer Name"), "", "", "1", "styled3"); ?>
				</span>	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input modified="ignore" id="dmzadd" type="button" class="button_blueX05" value=">>" onclick="PAGE.OnClickDMZAdd();" />				
				<input id="dmzhost" size="20" maxlength="15" value="0.0.0.0" type="text"/>
			</span>
		</div>		
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>

