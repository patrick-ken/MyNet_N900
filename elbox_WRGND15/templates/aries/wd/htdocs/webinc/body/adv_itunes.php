
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "ITUNES SERVER SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'Configure iTunes Server settings for streaming music directly to clients running iTunes software.');
		?></p>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable iTunes Server");?></span>
			<span class="value"><input type="checkbox" class="styled" id="itunes_active"/></span>
		</div>
		<br>
		<hr>		
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>				
	</div>
</form>
