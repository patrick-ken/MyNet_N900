
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Application Level Gateway (ALG) Configuration");?></p>
		</div>
		
		<div class="textinput">
			<span class="name"><?echo I18N("h", "PPTP");?></span>
			<span class="value"><input id="pptp" type="checkbox" class="styled" /></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "IPSec (VPN)");?></span>
			<span class="value"><input id="ipsec" type="checkbox" class="styled" /></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "SIP");?></span>
			<span class="value"><input id="sip" type="checkbox" class="styled" /></span>
		</div>
		<hr>							

		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>

