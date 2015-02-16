<form id="mainform" onsubmit="return false;">
	<div style="height:200px;">
		<div>
			<p class="text_title"><?echo I18N("h", "UPnP");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'Universal Plug and Play(UPnP) supports peer-to-peer Plug and Play functionality for network devices.');
		?></p>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable UPnP IGD");?></span>
			<span class="value"><input type="checkbox" class="styled" id="upnp"/></span>
		</div>
		<br>
		<hr>		
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>				
	</div>
</form>
