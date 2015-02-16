<style>
.same_width
{
	width: 225px;
}
</style>
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Time and Date");?></p>
		</div>
		<div>
			<p class="text">
			<?echo I18N("h", "The Time and Date Configuration option allows you to configure, update, and maintain the correct time on the internal system clock.")." ".
				I18N("h", "In this section you can set the time zone you are in and enable Automatic Time/Date setting via NTP (Network Time Protocol) Server.")." ".
				I18N("h", "The internal system clock can be configured to accomodate local Daylight Saving time practices.");?></p>
		</div>
		<hr>	
		
		<div>
			<p class="text_title"><?echo I18N("h", "Time and Date Configuration");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Time");?></span>
			<span class="value_left250" id="st_time"></span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Time Zone");?></span>
			<span class="value_left250" id="TZ">
				<select id="timezone" class="styled4X2" onchange="PAGE.SelectTimeZone(true);">
				<?
					function StringConvert($str)//convert "&amp;" to "&"
					{
						$ind = strstr($str,"&amp;");
						if($ind != "")
						{
							$s1 = substr($str,0,$ind+1);
							$s2 = substr($str,$ind+5,strlen($str)-$ind-5);
							$str = "";
							$str = $s1.$s2;
						}
						return $str;
					}
					foreach ("/runtime/services/timezone/zone")
					{
						$org = get("h","name");//(timezone) spsce (location)
						$org = StringConvert($org);
						$ind = strstr($org,") ");
						if($ind != "")
						{
							$str1 = substr($org,0,$ind+2);//timezone
							$str2 = substr($org,$ind+2,strlen($org)-$ind+2);//location
							echo '\t\t\t<option value="'.$InDeX.'">'.$str1.I18N("h",$str2).'</option>\n';
						}
						
					}
				?>
				</select>
			</span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable Daylight Saving");?></span>
			<span class="value_left250" id="EDS">
				<input type="checkbox" class="styled" id="daylight" onclick="PAGE.DaylightSetEnable();"/>
			</span>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Daylight Saving Offset");?></span>
			<span class="value_left250" id="DSO">
				<select id="daylight_offset" class="styled1">								
		            <option value="-02:00">-02:00</option>
		            <option value="-01:30">-01:30</option>
		            <option value="-01:00">-01:00</option>
		            <option value="-00:30">-00:30</option>
		            <option value="+00:30">+00:30</option>
		            <option value="+01:00">+01:00</option>
		            <option value="+01:30">+01:30</option>
		            <option value="+02:00">+02:00</option>
		    	</select>	    	
			</span>
		</div>
		<div class="textinput" style="height:80px;">
			<span class="name"><?echo I18N("h", "Daylight Saving Dates");?></span>
			<span class="value_left250" id="DSD">
				<table style="color: white;font: small-caption;">
					<tbody>
					<tr align="center">
						<td>&nbsp;</td>
						<td><?echo I18N("h", "Month");?></td>
						<td><?echo I18N("h", "Week");?></td>
						<td><?echo I18N("h", "Day of Week");?></td>
						<td><?echo I18N("h", "Time");?></td>
					</tr>
					<tr align="center">
						<td align="left"><?echo I18N("h", "Start Time");?>&nbsp;</td>
						<td>
							<select id="daylight_sm" class="styled1">							
								<option value=1><? echo I18N("h", "Jan"); ?></option>
								<option value=2><? echo I18N("h", "Feb"); ?></option>
								<option value=3><? echo I18N("h", "Mar"); ?></option>
								<option value=4><? echo I18N("h", "Apr"); ?></option>
								<option value=5><? echo I18N("h", "May"); ?></option>
								<option value=6><? echo I18N("h", "Jun"); ?></option>
								<option value=7><? echo I18N("h", "Jul"); ?></option>
								<option value=8><? echo I18N("h", "Aug"); ?></option>
								<option value=9><? echo I18N("h", "Sep"); ?></option>
								<option value=10><? echo I18N("h", "Oct"); ?></option>
								<option value=11><? echo I18N("h", "Nov"); ?></option>
								<option value=12><? echo I18N("h", "Dec"); ?></option>
							</select>
						</td>
						<td>
							<select id="daylight_sw" class="styled1">							
								<option value=1><? echo I18N("h", "1st"); ?></option>
								<option value=2><? echo I18N("h", "2nd"); ?></option>
								<option value=3><? echo I18N("h", "3rd"); ?></option>
								<option value=4><? echo I18N("h", "4th"); ?></option>
								<option value=5><? echo I18N("h", "5th"); ?></option>							
							</select>
						</td>
						<td>
							<select id="daylight_sd" class="styled1">							
								<option value=0><? echo I18N("h", "Sun"); ?></option>
								<option value=1><? echo I18N("h", "Mon"); ?></option>
								<option value=2><? echo I18N("h", "Tue"); ?></option>
								<option value=3><? echo I18N("h", "Wed"); ?></option>
								<option value=4><? echo I18N("h", "Thu"); ?></option>
								<option value=5><? echo I18N("h", "Fri"); ?></option>
								<option value=6><? echo I18N("h", "Sat"); ?></option>
							</select>
						</td>
						<td>											
							<select id="daylight_st" class="styled1">
								<script language="javascript">							
									PAGE.DayLightTimeObj();
								</script>
							</select>						
						</td>
					</tr>					
					<tr align="center">
						<td align="left"><?echo I18N("h", "End Time");?>&nbsp;</td>
						<td>
							<select id="daylight_em" class="styled1">							
								<option value=1><? echo I18N("h", "Jan"); ?></option>
								<option value=2><? echo I18N("h", "Feb"); ?></option>
								<option value=3><? echo I18N("h", "Mar"); ?></option>
								<option value=4><? echo I18N("h", "Apr"); ?></option>
								<option value=5><? echo I18N("h", "May"); ?></option>
								<option value=6><? echo I18N("h", "Jun"); ?></option>
								<option value=7><? echo I18N("h", "Jul"); ?></option>
								<option value=8><? echo I18N("h", "Aug"); ?></option>
								<option value=9><? echo I18N("h", "Sep"); ?></option>
								<option value=10><? echo I18N("h", "Oct"); ?></option>
								<option value=11><? echo I18N("h", "Nov"); ?></option>
								<option value=12><? echo I18N("h", "Dec"); ?></option>
							</select>
						</td>
						<td>
							<select id="daylight_ew" class="styled1">							
								<option value=1><? echo I18N("h", "1st"); ?></option>
								<option value=2><? echo I18N("h", "2nd"); ?></option>
								<option value=3><? echo I18N("h", "3rd"); ?></option>
								<option value=4><? echo I18N("h", "4th"); ?></option>
								<option value=5><? echo I18N("h", "5th"); ?></option>							
							</select>
						</td>
						<td>
							<select id="daylight_ed" class="styled1">							
								<option value=0><? echo I18N("h", "Sun"); ?></option>
								<option value=1><? echo I18N("h", "Mon"); ?></option>
								<option value=2><? echo I18N("h", "Tue"); ?></option>
								<option value=3><? echo I18N("h", "Wed"); ?></option>
								<option value=4><? echo I18N("h", "Thu"); ?></option>
								<option value=5><? echo I18N("h", "Fri"); ?></option>
								<option value=6><? echo I18N("h", "Sat"); ?></option>
							</select>
						</td>
						<td>
							<select id="daylight_et" class="styled1">
								<script language="javascript">							
										PAGE.DayLightTimeObj();
								</script>
							</select>		
						</td>
					</tr>					
					</tbody>
				</table>
			</span>
		</div>				
		<br>
		<br>				
		<hr>
		
		
		<div class="textinput">
			<p class="text_title"><?echo I18N("h", "Automatic Time and Date Configuration");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Automatic Internet Time Synchronization");?></span>
			<span class="value" id="checkbox_span"><input type="checkbox" class="styled" name="ntp_enable" id="ntp_enable" onclick="PAGE.OnClickNtpEnb();" /></span>
		</div>

		<br>
		<div class="textinput">	
			<span class="name"><?echo I18N("h", "NTP Server Used");?></span>
			<span class="value" id="select_span">
				<select id="ntp_server" class="styled4" >
					<option value=""><?echo I18N("h", "Select NTP Server");?></option>
				</select>
				<input modified="ignore" id="ntpdel" type="button" class="button_blueX2" value="<?echo I18N("h", "Remove");?>" onclick="PAGE.OnClickDelServer();" />				
			</span>
		</div>
		<div class="textinput">	
			<span class="value" id="update_span">	
				<input id="ntp_sync" type="button" class="button_blueX2" style="margin-left: 235px;" value="<?echo I18N("h", "Update Time");?>" onclick="PAGE.OnClickNTPSync()" />
			</span>
		</div>
		
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Adding User Defined NTP Server");?></span>
			<span class="value" id="add_ntp_span">
				<input id="add_ntp_server" class="same_width" maxlength="64" value="" type="text" />
				<input id="avert_ENTER_then_reload_web" class="same_width" maxlength="64" value="" type="text"  style="display:none;" />
				<input modified="ignore" id="ntpadd" type="button" class="button_blueX2" value="<?echo I18N("h", "Add");?>" onclick="PAGE.OnClickAddServer();" />

			</span>
		</div>
		<br>
		<div>
			<p class="text" id="sync_msg">
		</div>
		
		<hr>
		<div class="textinput" style="display:none;" >
			<p class="text_title"><?echo I18N("h", "Set the Time and Date Manually");?></p>
		</div>
		<div class="textinput" style="height:80px; display:none;">
			<table class="timebox"  style="width:625px;">
				<tbody>
				<tr>
					<td><?echo I18N("h", "Year");?></td>
					<td class="timebox_item">
					  <select id="year" onchange="PAGE.OnChangeYear()" class="styled1">
					  <?
						$i=2008;
						while ($i<2022) { $i++; echo "<option value=".$i.">".$i."</option>\n"; }
			
					  ?></select>
					</td>
					<td><?echo I18N("h", "Month");?></td>
					<td class="timebox_item">
						<select id="month" onchange="PAGE.OnChangeMonth()" class="styled1">
							<option value=1><? echo I18N("h", "Jan"); ?></option>
							<option value=2><? echo I18N("h", "Feb"); ?></option>
							<option value=3><? echo I18N("h", "Mar"); ?></option>
							<option value=4><? echo I18N("h", "Apr"); ?></option>
							<option value=5><? echo I18N("h", "May"); ?></option>
							<option value=6><? echo I18N("h", "Jun"); ?></option>
							<option value=7><? echo I18N("h", "Jul"); ?></option>
							<option value=8><? echo I18N("h", "Aug"); ?></option>
							<option value=9><? echo I18N("h", "Sep"); ?></option>
							<option value=10><? echo I18N("h", "Oct"); ?></option>
							<option value=11><? echo I18N("h", "Nov"); ?></option>
							<option value=12><? echo I18N("h", "Dec"); ?></option>
						</select>
					</td>
					<td><?echo I18N("h", "Day");?></td>
					<td class="timebox_item">
						<select id="day" class="styled1">
							<option> &nbsp;</option>	
						</select>
					</td>
				</tr>
				<tr>
					<td><?echo I18N("h", "Hour");?></td>
					<td class="timebox_item">
						<select id="hour" class="styled1">
							<option value=0><? echo I18N("h", "12 AM"); ?></option>
							<option value=1><? echo I18N("h", "1 AM"); ?></option>
							<option value=2><? echo I18N("h", "2 AM"); ?></option>
							<option value=3><? echo I18N("h", "3 AM"); ?></option>
							<option value=4><? echo I18N("h", "4 AM"); ?></option>
							<option value=5><? echo I18N("h", "5 AM"); ?></option>
							<option value=6><? echo I18N("h", "6 AM"); ?></option>
							<option value=7><? echo I18N("h", "7 AM"); ?></option>
							<option value=8><? echo I18N("h", "8 AM"); ?></option>
							<option value=9><? echo I18N("h", "9 AM"); ?></option>
							<option value=10><? echo I18N("h", "10 AM"); ?></option>
							<option value=11><? echo I18N("h", "11 AM"); ?></option>
							<option value=12><? echo I18N("h", "12 PM"); ?></option>
							<option value=13><? echo I18N("h", "1 PM"); ?></option>
							<option value=14><? echo I18N("h", "2 PM"); ?></option>
							<option value=15><? echo I18N("h", "3 PM"); ?></option>
							<option value=16><? echo I18N("h", "4 PM"); ?></option>
							<option value=17><? echo I18N("h", "5 PM"); ?></option>
							<option value=18><? echo I18N("h", "6 PM"); ?></option>
							<option value=19><? echo I18N("h", "7 PM"); ?></option>
							<option value=20><? echo I18N("h", "8 PM"); ?></option>
							<option value=21><? echo I18N("h", "9 PM"); ?></option>
							<option value=22><? echo I18N("h", "10 PM"); ?></option>
							<option value=23><? echo I18N("h", "11 PM"); ?></option>
						</select>
					</td>
					<td><?echo I18N("h", "Minute");?></td>
					<td class="timebox_item">
						<select id="minute" class="styled1"><?
							$i=0;
							while ($i<60) { echo "<option value=".$i.">".$i."</option>\n"; $i++; }
						?></select>
					</td>
					<td><?echo I18N("h", "Second");?></td>
					<td class="timebox_item">
						<select id="second" class="styled1"><?
							$i=0;
							while ($i<60) { echo "<option value=".$i.">".$i."</option>\n"; $i++; }
						?></select>
					</td>
				</tr>	
				<tr>				
					<td colspan="5">
						<br>			
						<span class="value">			
							<input class="button_blueX3" type="button" id="manual_sync" value="<?echo I18N("h", "Sync. your computer's time settings.");?>" onclick="PAGE.onClickManualSync();" />
						</span>
					</td>
					<td colspan="1">&nbsp;</td>		
				</tr>
				<tr>
					<td colspan="6" id=sync_pc_msg></td>
				</tr>
				</tbody>
			</table>
		</div>

		<div style="height:30px;"></div>

		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>
