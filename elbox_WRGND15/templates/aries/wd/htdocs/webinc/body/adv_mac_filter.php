<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "MAC Address Filter");?></p>
		</div>				
		<p class="text">
			<? echo I18N('h', 'The MAC (Media Access Controller) Address filter option is used to control network access based on the MAC Address of the network adapter.');?>
			<? echo I18N("h", "A MAC address is a unique ID assigned by the manufacturer of the network device.");?>
			<? echo I18N("h", "This feature can be configured to ALLOW or DENY network/Internet access.");?>
		</p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "MAC Filtering Rules");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Configure MAC Filtering below:");?></span>
			<span class="value">
				<select id="mode" onchange="PAGE.OnChangeMode();" class="styled4X2" >
					<option value="DISABLE"><?echo I18N("h", "Turn MAC Filtering OFF");?></option>
					<option value="DROP"><?echo I18N("h", "Turn MAC Filtering ON and ALLOW devices listed to access the network.");?></option>
					<option value="ACCEPT"><?echo I18N("h", "Turn MAC Filtering ON and DENY devices listed to access the network.");?></option>
				</select>				
			</span>
		</div>
		<p class="text"><?echo I18N('h', 'Remaining number of rules that can be created');?>: <span id="rmd" style="color:red;"></span></p>
		<br>

		<div class="centerline" align="center">
			<table id="" class="general">
			<tr  align="center">
				<td width="30px"><b><?echo I18N("h", "Active");?></b></td>
				<td width="180px"><b><?echo I18N("h", "MAC Address");?></b></td>
				<td width="60px">&nbsp;</td>
				<td width="210px"><b><?echo I18N("h", "DHCP Client List");?></b></td>
				
				<?if ($FEATURE_NOSCH!="1"){echo '<td width="188px"><b>'.I18N("h", "Schedule").'</b></td>\n';}?>
			</tr>
	<?
	$INDEX = 1;
	while ($INDEX <= $MAC_FILTER_MAX_COUNT) {	dophp("load", "/htdocs/webinc/body/adv_mac_filter_list.php");	$INDEX++; }
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
