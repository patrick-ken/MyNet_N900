
<? 
function draw_button()
{
	echo "src=\"pic/transparent_storage.png\" style=\"background-image: url(\'/pic/button_right_storage.png\');\" onmouseover=\"this.style.backgroundImage = \'url(/pic/button_right_over_storage.png)\';this.style.cursor = \'pointer\';\" onmouseout=\"this.style.backgroundImage = \'url(/pic/button_right_storage.png)\';\"";
}

?>

<form id="mainform" onsubmit="return false;">
	<div class="info">
		<table>
			<tr>
				<td class="name1"><?echo I18N("h", "Network Name (2.4GHz)");?></td>
				<td id="ssid" class="value1"></td>
				<td><img onClick="PAGE.OnClickAdvPage('wlan');" <? draw_button(); ?>></td>
				
				<td width="20px;">&nbsp;</td> <!-- space --> 
				<td class="name1"><?echo I18N("h", "Network Name (5GHz)");?></td>
				<td class="value1" id="ssid_Aband"></td>
				<td style="width: 10px;"><img onClick="PAGE.OnClickAdvPage('wlan');" <? draw_button(); ?>></td>
			</tr>
			<tr>
				<td class="name1"><?echo I18N("h", "Password");?></td>
				<td class="value1" id="key"></td>
				<td><img onClick="PAGE.OnClickAdvPage('wlan');" <? draw_button(); ?>></td>
				
				<td width="20px;">&nbsp;</td> <!-- space --> 
				<td class="name1"><?echo I18N("h", "Password");?></td>
				<td class="value1" id="key_Aband"></td>
				<td style="width: 10px;"><img onClick="PAGE.OnClickAdvPage('wlan');" <? draw_button(); ?>></td>								
			</tr>
			<tr><td colspan=7><hr></td></tr>
			<tr>
                <td class="name1" ><?echo I18N("h", "Device Name");?></td>
                <td class="value1" id="gw_name" ></td>
                <td class="button" ><img onClick="PAGE.OnClickAdvPage('lan');" <? draw_button(); ?>></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
                <td colspan="3" class="bname" ><?echo I18N("h", "Attached Storage");?></td>
			</tr>
			<tr>
				<td colspan=3 id="hr_1"><hr></td>
				<td width="20px;">&nbsp;</td>
                <td class="name1" id="usb_port_1" ></td>
                <td class="value2" style="text-align: right;" id="usb_status_1" ></td>
                <td class="button" id="usb_link_1"><img onClick="PAGE.OnClickAdvPage('storage');" <? draw_button(); ?>></td>
            </tr>
			<tr id="u_1" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "")  echo "style=\"display:none\""; ?>>
                <td class="name1" id="u_status_1" ><?echo I18N("h", "Internet Status");?></td>
                <td class="value1" id="u_st_networkstatus"></td>
                <td class="button"><img id="u_status_1_bt" onClick="PAGE.OnClickAdvPage('wan_status');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="usb_port_2" ></td>
                <td class="value2" style="text-align: right;" id="usb_status_2" ></td>
                <td class="button" id="usb_link_2"><img onClick="PAGE.OnClickAdvPage('storage');" <? draw_button(); ?>></td>	
			</tr>
			<tr id="d_1" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td class="name1" id="d_status_1" ><?echo I18N("h", "Internet Status");?></td>
                <td class="value1" id="d_st_networkstatus"></td>
                <td class="button"><img id="d_status_1_bt" onClick="PAGE.OnClickAdvPage('wan_status');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3 id="d_ra_0"><hr></td>
            </tr>
			<tr id="u_2" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "" || $layout != "router")  echo "style=\"display:none\""; ?>>
                <td class="name1" id="u_status_2" ><?echo I18N("h", "Number of Devices Connected");?></td>
				<td class="value1" id="u_device_number" ></td>
                <td class="button"><img id="u_status_2_bt" onClick="PAGE.OnClickAdvPage('lan_client');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3 id="u_ra_0"><hr></td>
			</tr>
			<tr id="d_2" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td class="name1" id="d_status_2" ><?echo I18N("h", "Number of Devices Connected");?></td>
                <td class="value1" id="d_device_number" ></td>
                <td class="button"><img id="d_status_2_bt" onClick="PAGE.OnClickAdvPage('lan_client');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3 class="bname"><?echo I18N("h", "WD 2go Remote Access");?></td>
            </tr>
			<tr id="u_3" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "" || $layout != "router")  echo "style=\"display:none\""; ?>>
				<td colspan=3 id="u_hr_2"><hr></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
                <td colspan=3 class="bname"><?echo I18N("h", "WD 2go Remote Access");?></td>
			</tr>
			<tr id="d_3" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td colspan=3 id="d_hr_2"><hr></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="d_ra_1"><?echo I18N("h", "Status");?></td>
                <td class="value1" id="d_remote_access_status"></td>
                <td style="width: 10px;" id="d_ra_2"><img onClick="PAGE.OnClickAdvPage('adv_remote');" <? draw_button(); ?>></td>
            </tr>
			<tr id="u_4" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "" || $layout != "router")  echo "style=\"display:none\""; ?>>
                <td class="name1" id="u_status_3"><?echo I18N("h", "Guest Network Name");?></td>
                <td class="value1" id="u_gz_ssid"></td>
                <td class="button" ><img id="u_status_3_bt" onClick="PAGE.OnClickAdvPage('wlan_gz');" <? draw_button(); ?>></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="u_ra_1"><?echo I18N("h", "Status");?></td>
                <td class="value1" id="u_remote_access_status"></td>
                <td style="width: 10px;" id="u_ra_2"><img onClick="PAGE.OnClickAdvPage('adv_remote');" <? draw_button(); ?>></td>
			</tr>
			<tr id="d_4" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td class="name1" id="d_status_3"><?echo I18N("h", "Guest Network Name");?></td>
                <td class="value1" id="d_gz_ssid"></td>
                <td class="button" ><img id="d_status_3_bt" onClick="PAGE.OnClickAdvPage('wlan_gz');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="d_ra_3"><?echo I18N("h", "Mobile Devices");?></td>
                <td class="value1" id="d_mobile_device_number"></td>
                <td style="width: 10px;" id="d_ra_4"><img onClick="PAGE.OnClickAdvPage('adv_mobile');" <? draw_button(); ?>></td>
			</tr>
			<tr id="u_5" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "" || $layout != "router")  echo "style=\"display:none\""; ?>>
                <td class="name1" id="u_status_4" ><?echo I18N("h", "Guest Password");?></td>
                <td class="value1" id="u_gz_key" ></td>
                <td class="button" ><img id="u_status_4_bt" onClick="PAGE.OnClickAdvPage('wlan_gz');" <? draw_button(); ?>></td>
                <td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="u_ra_3"><?echo I18N("h", "Mobile Devices");?></td>
                <td class="value1" id="u_mobile_device_number"></td>
                <td style="width: 10px;" id="u_ra_4"><img onClick="PAGE.OnClickAdvPage('adv_mobile');" <? draw_button(); ?>></td>
			</tr>
			<tr id="d_5" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td class="name1" id="d_status_4" ><?echo I18N("h", "Guest Password");?></td>
                <td class="value1" id="d_gz_key" ></td>
                <td class="button" ><img id="d_status_4_bt" onClick="PAGE.OnClickAdvPage('wlan_gz');" <? draw_button(); ?>></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="d_ra_5"><?echo I18N("h", "Web Access");?></td>
                <td class="value1" id="d_web_account_number"></td>
                <td style="width: 10px;" id="d_ra_6"><img onClick="PAGE.OnClickAdvPage('adv_web');" <? draw_button(); ?>></td>
			</tr>
			<tr id="u_6" <? if (query("/runtime/wd/USB1/entry#") == "0" || query("/runtime/wd/USB1/entry#") == "" || $layout != "router")  echo "style=\"display:none\""; ?>>
				<td colspan=3 id="u_hr_3"><hr></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
                <td class="name1" id="u_ra_5"><?echo I18N("h", "Web Access");?></td>
                <td class="value1" id="u_web_account_number"></td>
                <td style="width: 10px;" id="u_ra_6"><img onClick="PAGE.OnClickAdvPage('adv_web');" <? draw_button(); ?>></td>
			</tr>
			<tr id="d_6" <? if (query("/runtime/wd/USB1/entry#") != "0" && query("/runtime/wd/USB1/entry#") != "") {echo "style=\"display:none\"";} else if ($layout != "router") {echo "style=\"display:none\"";}?>>
                <td colspan=3 id="d_hr_3"><hr></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3>&nbsp;</td>
			</tr>
            <tr <? if ($layout != "router")  echo "style=\"display:none\""; ?>>
                <td id="row_1" colspan=3 class="bname"><?echo I18N("h", "Internet Security and Parental Control");?></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3>&nbsp;</td>
			</tr>
			<tr <? if ($layout != "router")  echo "style=\"display:none\""; ?> >
				<td class="status" ><?echo I18N("h", "Status");?></td>
				<td class="value1" id="parental_control_value"></td>
                <td class="button" ><img onClick="PAGE.OnClickAdvPage('parent_ctrl');" <? draw_button(); ?>></td>
				<td width="20px;">&nbsp;</td> <!-- space -->
				<td colspan=3>&nbsp;</td>
			</tr>
		</table>
	</div>
</form>
