<form id="mainform">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Schedules");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'The Schedule configuration option is used to manage schedule rules for "WAN", "Wireless", "Virtual Server", "Port Forwarding", "Applications" and "Network Filter".');
		?></p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Add Schedule Rule");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Name");?></span>
			<span class="value"><input id="schdesc" size="20" maxlength="16" type="text"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Day(s)");?></span>
			<span class="value">
				<input type="radio" class="styled" id="schallweek" name="schdayselect" onclick="PAGE.OnClickSelectDays();" /><span style="float:left;">&nbsp;<?echo I18N("h", "All Week");?>&nbsp;&nbsp;</span>
				<input type="radio" class="styled" id="schdays" name="schdayselect" onclick="PAGE.OnClickSelectDays();" /><span style="float:left;">&nbsp;<?echo I18N("h", "Select Day(s)");?></span>
			</span>
		</div>
		<div class="textinput">
			<span class="name"></span>
			<span class="value">
				<span style="float:left;"><input type="checkbox" id="schsun" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Sun");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schmon" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Mon");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schtue" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Tue");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schwed" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Wed");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schthu" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Thu");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schfri" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Fri");?>&nbsp;&nbsp;</span>
				<span style="float:left;"><input type="checkbox" id="schsat" class="styled2"></span><span style="float:left;">&nbsp;<?echo I18N("h", "Sat");?>&nbsp;&nbsp;</span>							
			</span>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "All Day - 24 hrs");?></span>
			<span class="value">
				<input type="checkbox" id="sch24hrs" class="styled" onclick="PAGE.OnClick24Hours();">
			</span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Time Format");?></span>
			<span class="delimiter">:</span>
			<span class="value">
				<select id="timeformat" onChange="PAGE.OnChangeTimeFormat(this.value)" class="styled1">
					<option value="12-hour"><?echo I18N("h", "12-hour");?></option>
					<option value="24-hour"><?echo I18N("h", "24-hour");?></option>
				</select>
			</span>
		</div>	
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Start Time");?></span>
			<span class="delimiter">:</span>
			<span class="value">
				<input type="text" id="schstarthrs" size=3 maxlength=2
				>:<input type="text" id="schstartmin" size=3 maxlength=2>
				<select id="schstartapm" class="styled1">
					<option value="AM"><?echo I18N("h", "AM");?></option>
					<option value="PM"><?echo I18N("h", "PM");?></option>
				</select>
				(<?echo I18N("h", "hour:minute");?>)
			</span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "End Time");?></span>
			<span class="delimiter">:</span>
			<span class="value">
				<input type="text" id="schendhrs" size=3 maxlength=2
				>:<input type="text" id="schendmin" size=3 maxlength=2>
				<select id="schendapm" class="styled1">
					<option value="AM"><?echo I18N("h", "AM");?></option>
					<option value="PM"><?echo I18N("h", "PM");?></option>
				</select>
				(<?echo I18N("h", "hour:minute");?>)
			</span>
		</div>
		<div class="gap"></div>
		<div class="centerline">
			<input type="button" class="button_blue" id="schsubmit" value="<?echo I18N("h", "Add");?>" onclick="PAGE.OnClickSchSubmit();" />
			<input type="button" class="button_blue" id="schcancel" value="<?echo I18N("h", "Cancel");?>" onclick="PAGE.OnClickSchCancel();" />
		</div>
		<div class="gap"></div>
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Schedule Rules List");?></p>
		</div>		
		<table id="schtable" class="general">
			<tr>
				<th width="151px"><?echo I18N("h", "Name");?></th>
				<th width="201px"><?echo I18N("h", "Day(s)");?></th>
				<th width="116px"><?echo I18N("h", "Time Frame");?></th>
				<th width="30px"> </th>
				<th width="30px"> </th>
			</tr>
		</table>
		<div class="gap"></div>
	</div>
</form>
