<?
include "/htdocs/webinc/body/draw_elements.php";
?>
<form id="mainform" onsubmit="return false;">
<div id="remote_access_1" style="display:none;">
        <table border=0>
            <tbody>
                <tr>
                    <td colspan=3><p class="text"><?echo I18N("h","WD 2go provides secure access to the media and files on your My Net Network Router in your home or from any location.");?></p></td>
                </tr>
                <tr>
                    <td><p class="text_title"><?echo I18N("h","Mobile Access");?></p></td>
                    <td rowspan=4 width="1" bgcolor="#939393" style="padding-left: 0px; padding-right: 0px;"><hr></td>
                    <td><p class="text_title"><?echo I18N("h","Web Account");?></p></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Remote access to your stored media and files within WD mobile apps for smartphones and tablets.");?></p></td>
                    <td><p class="text"><?echo I18N("h","Remote access to your stored media and files from any computer through the WD 2go web site.");?></p></td>
                </tr>
				<tr>
                    <td><input type="button" id="ra_button" class="button_blueX3" value="<?echo i18n('Add Mobile Access');?>" onClick="PAGE.OnClickNext(1);" disabled></td>
                    <td><input type="button" id="rw_button" class="button_blueX3" value="<?echo i18n('Add Web Access');?>" onClick="PAGE.OnClickNext(3);" disabled></td>
                </tr>
				<tr>
					<td><br><div class="scrollable" id="scrollable_access_list" style="overflow:auto;"></div></td>
					<td><br><div class="scrollable" id="scrollable_web_list" style="overflow:auto;"></div></td>
				</tr>
            </tbody>
        </table>
</div>
<div id="remote_access_2" style="display:none;">
       <table width="100%">
            <tbody>
                <tr>
                    <td><div class="textinput"><p class="text_title"><?echo I18N("h","Step 1 of 2: Download App");?></p></div></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Download the WD 2go or WD Photos app from your mobile app store.");?></p></td>
                </tr>
                <tr>
                    <td align=right><br><br><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(0);"/>
                    <input type="button" class="button_blue" value="<?echo i18n('Next');?>" onClick="PAGE.OnClickNext(2);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
<div id="remote_access_3" style="display:none;">
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
		              <td align=right><br><br><br><br><br><br>
		              	<div class="textinput">
											<span type="button" class="name_c" style="text-decoration: underline;" onclick="PAGE.OnClickNext(0);" onmouseover="this.style.cursor='pointer';">
												<?echo I18N("h", "Set up another device for WD 2go Remote Access");?>
											</span>
										</div>
	                <br>
	                	<input type="button" class="button_blue" value="<?echo i18n('Finish');?>" onClick="PAGE.OnClickNext(6);"/>
	                </td>
                </tr>
            </tbody>
        </table>
</div>
<div id="remote_access_5" style="display:none;">
       <table width="100%">
            <tbody>
                <tr>
                    <td colspan= 2><p class="text_title"><?echo I18N("h","Step 1 of 1: Enter Email Address");?></p></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Email");?></p></td>
                    <td><input id="sender_address" type="text" size="50" maxlength="64"></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Full Name");?></p></td>
                    <td><input id="sender_name" type="text" size="50" maxlength="64"></td>
                </tr>
                <tr>
                    <td colspan=2 align=right><br><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(0);"/>
                    <input type="button" class="button_blue" value="<?echo i18n('Next');?>" onClick="PAGE.OnClickNext(4);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
<div id="remote_access_6" style="display:none;">
        <table width="100%">
            <tbody>
                <tr>
                    <td><img style="width: 30px; height: 30px;" src="/pic/wps_ok.png"></td>
                    <td width="95%"><p class="text_ra"><?echo I18N("h","Email Sent");?></p></td>
                </tr>
                <tr>
                    <td colspan=2><p class="text"><br><?echo I18N("h","An email has been sent to the address associated with your account. Please check your email for information about accessing your My Net Network Router.");?></p></div></td>
                </tr>
                <tr>
                    <td colspan=2 align=right><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Finish');?>" onClick="PAGE.OnClickNext(6);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
<div id="remote_access_7" style="display:none;">
       <table width="100%">
            <tbody>
                <tr>
                    <td colspan= 2><p class="text_title"><?echo I18N("h","Step 1 of 1: Enter Sender Full Name");?></p></td>
                </tr>
                <input id="sender_address_remail" type="hidden">
                <tr>
                    <td><p class="text"><?echo I18N("h","Email");?></p></td>
                    <td><input id="email_string" type="text" size="50" maxlength="64" disabled></td>
                </tr>
                <tr>
                    <td><p class="text"><?echo I18N("h","Full Name");?></p></td>
                    <td><input id="sender_name_remail" type="text" size="50" maxlength="64"></td>
                </tr>
                <tr>
                    <td colspan=2 align=right><br><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(0);"/>                    <input type="button" class="button_blue" value="<?echo i18n('Cancel');?>" onClick="PAGE.OnClickNext(0);"/>*/
                    <input type="button" class="button_blue" value="<?echo i18n('Next');?>" onClick="PAGE.OnClickNext(5);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
</form>
