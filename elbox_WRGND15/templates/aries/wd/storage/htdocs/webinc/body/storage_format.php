<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Format");?></p>
		</div>				
		<p class="text"><?
			echo I18N("h", "Use this function to format the internal hard disk drive. Operation of this function will erase all files saved on the internal disk drive. Make sure to back up important files to another storage device before executing this command.");
		?></p>
		<br>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Format the internal HDD");?>:</span>
			<span class="value">
				<input type="button" class="button_blueX2" value="<?echo I18N("h", "Format");?>" onclick="PAGE.OnClickFormatConfirm();" />
			</span>
		</div>
		<br>		
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Restore");?></p>
		</div>
		<p class="text"><?
			echo I18N("h", "Use this function to restore the internal hard drive to original factory state. Operation of this function will not only reformat the user partition, but also hidden partitions used by router system files. This operation will prompt to download new system files to restore to the hidden partition and it is recommended the Restore function to be used only if system file corruption is suspected.");
		?></p>
		<br>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Restore the internal HDD");?>:</span>
			<span class="value">
				<input type="button" class="button_blueX2" value='<?echo I18N("h", "Restore");?>' onclick="PAGE.OnClickRestoreConfirm();" />
			</span>
		</div>
	</div>
</form>

