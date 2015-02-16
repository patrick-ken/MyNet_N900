<?
include "/htdocs/webinc/body/draw_elements.php";
include "/htdocs/phplib/xnode.php";
?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "SYSTEM LOG");?></p>
		</div>
		<p class="text"><?
			echo I18N("h", "The page will show the information about the system.");?>
		</p>
		<hr>
        <div>
			<p class="text_title"><?echo I18N("h", "Save Log File");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Save Log File To Local Hard Drive.");?></span>
			<span class="value" style="padding-left: 163px;">
				<input name="save_log" class="button_blue" value="<?echo I18N("h", "Save");?>" onclick="window.location.href='/log_get.php';" type="button"/>
			</span>
		</div>	
		<hr>
		<div>
			<p class="text_title"><?echo I18N("h", "Log Level");?></p>
		</div>	
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Log Level");?></span>
			<span class="value">
				<select id="select_log_level" onchange="PAGE.OnChangeLogLevel();" class="styled2">
					<option value="WARNING" selected><?echo I18N("h", "Warning");?></option>
					<option value="NOTICE" selected><?echo I18N("h", "Notice");?></option>
					<option value="DEBUG" selected><?echo I18N("h", "Debug");?></option>
				</select>&nbsp;&nbsp;
				<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
			</span>
		</div>
		<br>
		<hr>
		<div>
			<p class="text_title"><?echo I18N("h", "System Log Status");?></p>
		</div>			
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Log Type");?></span>
			<span class="value">
				<select id="select_log_type" onchange="PAGE.OnChangeLogType();" modified="ignore" class="styled1">
					<option value="sysact" selected><?echo I18N("h", "System");?></option>
					<option value="attack"><?echo I18N("h", "Attack");?></option>
					<option value="drop"><?echo I18N("h", "Drop");?></option>
				</select>
			</span>
		</div>
	  <div>
			<p class="text_title"><?echo escape("h",I18N("h", "Log Table"));?></p>
		</div>
		<div>
			<table  class="general">
			<tr>
				<td>
					<div>
						<input type="button" class="button_black<? if($lang!="ja") echo "X2"; ?>" value="<?echo I18N("h", "First Page");?>" id="fp"  onclick="PAGE.OnClickToPage('1')">
						<input type="button" class="button_black<? if($lang!="ja") echo "X2"; ?>" value="<?echo I18N("h", "Last Page");?>" id="lp"  onclick="PAGE.OnClickToPage('0')">
						<input type="button" class="button_black" value="<?echo I18N("h", "Previous");?>" id="pp"  onclick="PAGE.OnClickToPage('-1')">
						<input type="button" class="button_black" value="<?echo I18N("h", "Next");?>" id="np" onclick="PAGE.OnClickToPage('+1')">
						<input type="button" class="button_black" value="<?echo I18N("h", "Clear");?>" id="clear" onclick="PAGE.OnClickClear()">
						<input type="button" class="button_black<? if($lang=="ja") echo "X2"; ?>" value="<?echo I18N("h", "Refresh");?>" id="clear" onclick="BODY.OnReload()">					</div>
				</td>
			</tr>
			<tr>
				<td>
				 <div id="sLog"></div> 
				</td>
			</tr>
			</table>
       </div>
	   <hr>

	</div>
</form>
