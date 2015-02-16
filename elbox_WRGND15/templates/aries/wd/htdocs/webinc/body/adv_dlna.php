 
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "DLNA Settings");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'DLNA (Digital Living Network Alliance) is the standard for the interoperability of Network Media Devices (NMDs). The user can enjoy multi-media applications (music, pictures, and videos) on your network connected PC or media devices.');
		?></p>
		<br>
		<p class="text"><?
			echo I18N('h', 'If you agree to share media with devices, any computer or device that connects to your network can access your shared music, pictures and videos.');
		?></p>
		<br>
		<p class="text"><?
			echo I18N('h', 'NOTE: The shared media may not be secure. Allowing all devices to access this is only recommended on secure networks.');
		?></p>						
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable DLNA");?></span>
			<span class="value"><input type="checkbox" class="styled" id="dms_active"/></span>
		</div>
		<br>
		<hr>		
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>				
	</div>
</form>
