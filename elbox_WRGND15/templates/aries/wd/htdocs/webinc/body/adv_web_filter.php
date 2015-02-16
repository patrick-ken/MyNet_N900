<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "WEBSITE FILTER");?></p>
		</div>				
		<p class="text"><? echo I18N('h', 'The Website Filter option allows you to set up a list of web sites you would like to allow or deny through your network.');?></p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "WEBSITE FILTERING RULES");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Configure the Website Filter below:");?></span>
			<span class="value">
				<select id="url_mode">
					<option value="ACCEPT"><?echo I18N("h", "ALLOW computers access to ONLY these sites:");?></option>
					<option value="DROP"><?echo I18N("h", "DENY computers access to ONLY these sites");?></option>
				</select>				
			</span>
		</div>
		<div>
			<input id="clear_url" type="button" class="button_blueX3" value="<?echo I18N("h", "Clear the list below:");?>..." onClick="PAGE.OnClickClearURL();" />
		</div>
		<br>
		
		<div class="centerline" align="center">
			<table id="" class="general">
				<tr  align="center">
					<td colspan="2"><?echo I18N("h", "Website URL");?>/<?echo I18N("h", "Domain");?></td>
				</tr>			
	<?
	$INDEX = 1;
	while ($INDEX <= $URL_MAX_COUNT)
	{	
		echo	"<tr  align='center'>"."\n";
		echo	"	<td><input type=text id=url_".$INDEX." size=44 maxlength=99></td>"."\n";
		$INDEX++;
		echo	"	<td><input type=text id=url_".$INDEX." size=44 maxlength=99></td>"."\n";
		$INDEX++;	
		echo	"</tr>"."\n";
	}			
	?>			
			</table>
		</div>
		<br>

		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
