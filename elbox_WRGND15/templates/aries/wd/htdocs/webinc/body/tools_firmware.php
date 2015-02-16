<form id="fwup" action="fwup.cgi" method="post" enctype="multipart/form-data">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Firmware Upgrade");?></p>
		</div>

		<input type="hidden" name="REPORT_METHOD" value="301" />
		<input type="hidden" name="REPORT" value="tools_fw_rlt.php" />
		<input type="hidden" name="DELAY" value="10" />
		<input type="hidden" name="PELOTA_ACTION" value="fwupdate" />

		<div class="textinput">
			<span class="name">Select a File to Upgrade</span>
			<span class="value">
				<input type="file" size="40" name="fw" modified="false">
			</span>
		</div>
		<div class="textinput">
			<span class="name">&nbsp;</span>
			<span class="value">
				<input type="submit" class="button_blue" modified="false" value="Upload" style="background-image: url(&quot;/pic/bg_button_blue.jpg&quot;);">
			</span>
		</div>
	</div>
</form>
