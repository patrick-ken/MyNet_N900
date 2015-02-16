<?
include "/htdocs/webinc/body/draw_elements.php";
?>
<form id="mainform" onsubmit="return false;">
<div id="mobile_access_1" style="display:none;">
	<div>
		<p class="text"><?echo I18N("h","Remote access to your stored media and files within WD mobile apps for smartphones and tablets.");?></p><br>
	</div>
	<div class="textinput">
		<table>
			<tr>
                <th><input type="button" id="ra_button"  class="button_blueX3" value="<?echo I18N("h","Add Mobile Access");?>" onClick="PAGE.OnClickNext(1);" disabled></th>
            </tr>
		</table>
	</div>
	<br>
	<div class="scrollable" id="scrollable_mobileaccount_list" style="overflow:auto;"></div>
</div>
<div id="mobile_access_2" style="display:none;">
       <table width="100%">
            <tbody>
                <tr>
                    <td><div class="textinput"><p class="text_title"><?echo I18N("h","Step 1 of 2: Download App");?></p></div></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Download the WD 2go or WD Photos app from your mobile app store.");?></p></td>
                </tr>
				<tr>
					<td align=right><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
					<input type="button" class="button_black" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(0);"/>
					<input type="button" class="button_blue" value="<?echo i18n('Next');?>" onClick="PAGE.OnClickNext(2);"/>
				</td>
				</tr>
            </tbody>
        </table>
</div>
<div id="mobile_access_3" style="display:none;">
        <table width="100%">
            <tbody>
                <tr>
                    <td><div class="textinput"><p class="text_title"><?echo I18N("h","Step 2 of 2: Activate");?></p></div></td>
                </tr>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Enter this activation code on your mobile device. This code will expire in 48 hours.");?></p></td>
                </tr>
                <tr>
                    <td>
                        <br><br><br>
                        <input type="text" id="dac_part1" class="button_blue_ra" size=4 disabled>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" id="dac_part2" class="button_blue_ra" size=4 disabled>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" id="dac_part3" class="button_blue_ra" size=4 disabled>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td align=right><br><br><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(1);"/>
                    <input type="button" class="button_black" value="<?echo i18n('Cancel');?>" onClick="PAGE.remove_account(3);"/>
                    <input type="button" class="button_blue" value="<?echo i18n('Finish');?>" onClick="PAGE.OnClickNext(3);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
</form>
