<?
include "/htdocs/webinc/body/draw_elements.php";
?>
<form id="mainform" onsubmit="return false;">
<div id="mobile_access_1" style="display:none;">
	<table border=0>
		<tr height='40px'>
                <th width='<? if ($lang == "es") {echo "350px";} else {echo "250px";} ?>'><p class="text"><?echo I18N("h","Enable Remote Access");?></p></th>
                <th align=left><input type="checkbox" class="styled" id="remote_access_checkbox" onClick="PAGE.checkRemoteAccess()"></th>
		</tr>
		<tr height='40px'>
                <th width='<? if ($lang == "es") {echo "350px";} else {echo "250px";} ?>'><p class="text"><?echo I18N("h","Connection Status");?></p></th>
                <th align=left><div class="r_textinput" id="remote_access_status"><?echo I18N("h","Unknown");?></div></th>
		</tr>
		<tr height='40px'>
                <th width='<? if ($lang == "es") {echo "350px";} else {echo "250px";} ?>'><p class="text"><?echo I18N("h","Connection Type");?></p></th>
                <th align=left><span id="remote_access_type" class="text_ra"><?echo I18N("h","Unknown");?></span></th>
		</tr>
        <tr height='40px'>
                <th width='<? if ($lang == "es") {echo "350px";} else {echo "250px";} ?>'>
				<div class="r_textinput">
                <span class="name"><? echo I18N("h", "Firewall Settings");?></span>
                <span style="cursor:pointer;" onclick="PAGE.help_display_ex(this);"><img src="pic/help.png" onmouseover="this.src='pic/help_hover.png'" onmouseout="this.src='pic/help.png'"></span>
                <span style="position:relative;z-index:100;display:none;">
					<div class="help_box">
						<div class="help_box_top" onclick="this.parentNode.parentNode.style.display='none'"></div>
 						<div class="help_box_middle">
							<div class="help_box_middle_text"><?echo I18N("h","Lets you configure communication ports on your My Net Network Router.");?></div>
						</div>
						<div class="help_box_bottom"></div>
					</div>
                </span>
				</div>
                </th>
                <th><span id="mobile_firewll_settings"><input type='button' class='button_black_ra' value='<?echo I18N("h","Automatic");?>'><input type='button' class='button_black_ra' value='<?echo I18N("h","XP Compatibility");?>'></span></th>
				</th>
        </tr>
		<tr height='40px'>
				<th width='<? if ($lang == "es") {echo "350px";} else {echo "250px";} ?>'>
                <div class="r_textinput">
                <span class="name"><? echo I18N("h", "WD 2go Service");?></span>
                <span style="cursor:pointer;" onclick="PAGE.help_display_ex(this);"><img src="pic/help.png" onmouseover="this.src='pic/help_hover.png'" onmouseout="this.src='pic/help.png'"></span>
                <span style="position:relative;z-index:100;display:none;">
                    <div class="help_box">
                        <div class="help_box_top" onclick="this.parentNode.parentNode.style.display='none'"></div>
                        <div class="help_box_middle">
                            <div class="help_box_middle_text"><?echo I18N("h","Refreshes your WD 2go media database. Use only for troubleshooting when database corruption is suspected.");?></div>
                        </div>
                        <div class="help_box_bottom"></div>
                    </div>
                </span>
                </div>
				</th>
				<th align=left><input type="button" class="button_blue_ra" value="<?echo i18n('Refresh');?>" onClick="PAGE.reBuild();"></th>
		</tr>
	</table>
</div>
</form>
