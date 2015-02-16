<?
include "/htdocs/webinc/body/draw_elements.php";
?>
<form id="mainform" onsubmit="return false;">
<div id="mobile_access_1" style="display:none;">
	<div>
		<p class="text"><?echo I18N("h","Remote access to your stored media and files from any computer through the WD 2go web site.");?></p><br>
	</div>
	<div class="textinput">
		<table>
			<tr>
                <th><input type="button" id="rw_button" class="button_blueX2" value="<?echo i18n('Add Web Access');?>" onClick="PAGE.OnClickNext(1);" disabled></th>
            </tr>
		</table>
	</div>
    <div>
        <br><p class="text_ra"><?echo I18N("h","URL for web access : ");?><a href="http://www.wd2go.com" target="_blank"><?echo I18N("h","http://www.wd2go.com");?></a></p>
    </div>
	<br>
	<div class="scrollable" id="scrollable_mobileaccount_list" style="overflow:auto;"></div>
</div>
<div id="mobile_access_2" style="display:none;">
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
					<td colspan=2 align=right><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
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
                    <td><img style="width: 30px; height: 30px;" src="/pic/wps_ok.png"></td>
                    <td width="95%"><p class="text_ra"><?echo I18N("h","Email Sent");?></p></td>
                </tr>
                <tr>
                    <td colspan=2><p class="text"><br><?echo I18N("h","An email has been sent to the address associated with your account. Please check your email for information about accessing your My Net Network Router.");?></p></div></td>
                </tr>
                <tr>
                    <td colspan=2 align=right><br><br><br><br><br><br><br><br><br><br><br><br><br>
                    <input type="button" class="button_blue" value="<?echo i18n('Finish');?>" onClick="PAGE.OnClickNext(4);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
<div id="remote_access_4" style="display:none;">
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
                    <input type="button" class="button_black" value="<?echo i18n('Back');?>" onClick="PAGE.OnClickNext(0);"/>
                    <input type="button" class="button_blue" value="<?echo i18n('Next');?>" onClick="PAGE.OnClickNext(3);"/>
                </td>
                </tr>
            </tbody>
        </table>
</div>
</form>
