<form id="mainform" onsubmit="PAGE.OnClick_Ping();return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Connection Check");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N('h', 'Connection Check sends "ping" packets to test connection to a computer on the Internet.');?></p><br />
			<p class="text"><?echo I18N('h', 'Enter a host name or IP address below and click "Ping."');?></p><br />
		</div>
		<div class="textinput">
			<span class="name"><b><FONT SIZE=2.5><?echo I18N("h", "Host Name or IP Address");?></b></span>
			<span class="value">
				<input type="text" id="dst" maxlength="63" size="20">
				<input id="ping" type="button" class="button_blue" style="margin-left: 8px;" value="Ping" onclick="PAGE.OnClick_Ping()" />
			</span>
		</div>
		<br />
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Connection Check Result");?></p>
		</div>
		<div>
			<p id="report" class="text"></p>
		</div>
		<br /><br />
		<hr>
	</div>	
</form>

