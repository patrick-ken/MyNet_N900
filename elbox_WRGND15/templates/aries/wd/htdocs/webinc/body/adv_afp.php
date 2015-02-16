
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "AFP SERVER SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'Configure the AFP Server for sharing files using Mac OS X and original Mac OS.');
		?></p>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable AFP Server");?></span>
			<span class="value"><input type="checkbox" class="styled" id="afp_active"/></span>
		</div>
		<br>
		<hr>		
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>				
	</div>
</form>
