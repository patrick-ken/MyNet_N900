<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Dynamic DNS");?></p>
		</div>
		<div>
			<p class="text">
				<?echo I18N("h", "The Dynamic DNS feature allows you to host a server (Web, FTP, Game Server, etc.) ")." ".
				I18N("h", "using a domain name that you have purchased (www.whateveryournameis.com) with your dynamically assigned IP address. ")." ".
				I18N("h", "Most broadband Internet Service Providers assign dynamic (changing) IP addresses. Using a DDNS service provider, enter your host name to connect to your game server no matter what your IP address is.");?>
			</p>
		</div>
		<hr>

		<div>
			<p class="text_title"><?echo I18N("h", "Dynamic DNS Settings");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Dynamic DNS");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_ddns" onClick="PAGE.EnableDDNS();" /></span>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "DNS Server");?></span>
			<span class="value">
				<select id="server" class="styled2">
					<option value="DYNDNS">DynDns.org</option>
					<option value="TZO">TZO</option>
				</select>
			</span>
		</div>

		<div class="textinput">
			<span class="name"><?echo I18N("h", "Host Name");?></span>
			<span class="value"><input type="text" id="host" maxlength="60" size="40"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Username");?></span>
			<span class="value"><input type="text" id="user" maxlength="100" size="40"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Password (Key)");?></span>
			<span class="value"><input type="password" id="passwd" maxlength="100" size="40"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Verify Password (Key)");?></span>
			<span class="value"><input type="password" id="passwd_verify" maxlength="100" size="40"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Status");?></span>
			<span class="value" id="report" ></span>
		</div>
		<div class="bottom_cancel_save" style="position:relative;z-index:1">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>
