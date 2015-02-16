<form id="mainform" onsubmit="return false;">
	<div style="text-align:center;">
		<!-- Start check cable connection stage -->
		<div id="cable_check" style="margin: 0 auto;width: 750px;">
			<div style="text-align:left;">
				<span class="title2"><?echo I18N("h", "Step 1.  Check cable connections.");?></span>
			</div>
			<div style="text-align:left;">
				<span class="main_text_gray"><?echo I18N("h", "Check your cables and make sure your modem is connected.");?></span>
			</div>
			<br>
			<div class="internet_list_item">
				<div class="internet_list_item item1"></div>	
				<div class="internet_list_item item2"><img src="pic/check_cable_connection_ex.png"  style="width: 635px;"/></div>
				<div class="internet_list_item item3"><input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=1;PAGE.OnClickNext();"/></div>
			</div>
		</div>	
		<!-- End check cable connection stage -->
		
		<!-- Start Connect to Internet stage -->
		<div id="internet_check" style="margin: 0 auto;width: 750px;display:none;">
			<div style="text-align:left;">
				<span id="internet_check_title" class="title2"><?echo I18N("h", "Step 2.  Connect to Internet.");?></span>
			</div>
			<div style="text-align:left;">
				<span class="main_text_gray"><?echo I18N("h", "Checking your connection.");?></span>
			</div>
			<br>
			<div style="text-align:left;">
				<img id="pc_rt_internet" src="pic/connect_internet.png" />
				<img id="pc_rt_fail" src="pic/connect_internet_fail.png" style="display:none;" />
				<img id="pc_rt_ok" src="pic/connect_internet_ok.gif" style="display:none;" />
				<img id="rt_internet_fail" src="pic/connect_internet_ok_fail.png" style="display:none;" />
				<img id="pc_internet_ok" src="pic/connect_internet_ok_ok.png" style="display:none;" />				
			</div>
			<br>
			<div id="connect_detect_info" style="text-align:left;">
				<span id="connect_device" class="main_text_gray"><?echo I18N('h', 'We are checking your connection to the Internet.');?></span>
			</div>
			<div id="connect_internet_fail" class="internet_status_item_ex" style="display:none;">
				<div class="internet_status_item_ex item1"><img src="pic/warning_mark.png"></div>
				<div class="internet_status_item_ex item2">
					<span class="main_text_gray"><?echo i18n('Internet not connected. Click Next to verify your router is wired correctly. You will then be guided to fix your internet connection.');?></span>	
				</div>
            	<div class="internet_status_item_ex item3">
                	<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=8;PAGE.OnClickNext();"/>
            	</div>
			</div>
			<div id="connect_internet_ok" class="internet_status_item" style="display:none;">
                <div class="internet_status_item item1"><img src="pic/checked.png"></div>
				<div class="internet_status_item item2">
				<p class="main_text_green"><?echo I18N('h', 'Internet connection successful.');?></p>
				</div>				
				<div class="internet_status_item item3">
					<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=7;PAGE.OnClickNext();"/>
				</div>
			</div>						
		</div>
		<!-- End Connect to Internet stage -->
		
		<!-- Start Modem power unplug -->
		<div id="modem_unplug" style="margin: 0 auto;width: 750px;display:none;">
		</div>		
		<!-- End Modem power unplug -->

		<!-- Start Count down after modem unplug -->
		<div id="modem_unplug_after" style="margin: 0 auto;width: 750px;display:none;">
			<div style="text-align:left;">
				<span class="title2"><?echo I18N("h", "Step 3.  Recheck cable connections and power off the modem.");?></span>
			</div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "Wait at least 20 seconds after removing power from your modem.");?></span>
            </div>
			<div class="main_internet_process_bar">
				<img src="pic/process_bar.gif" />
			</div>			
			<div style="text-align:center;">
				<span id="modem_unplug_after_info" class="main_text"><?echo I18N("h", "Count down")." 20 ".I18N("h", "seconds");?></span>
			</div>			
		</div>		
		<!-- End Count down after modem unplug -->	
		
		<!-- Start Modem power plug -->
		<div id="modem_plug" style="margin: 0 auto;width: 750px;display:none;">
			<div style="text-align:left;">
				<span class="title2"><?echo I18N("h", "Step 4.  Restart the modem.");?></span>
			</div>
			<div style="text-align:left;">
				<span class="main_text_gray"><?echo I18N("h", "Plug power and batteries, if any, back into the modem. Power on the modem.");?></span>
			</div>
			<br>
			<div style="display:none;"><!--Wait for picture from WD Joseph-->
				<img src="pic/modem_power_plug.png" />
			</div>
			<br>			
			<div class="main_internet_next_button">
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=5;PAGE.OnClickNext();"/>
			</div>					
		</div>		
		<!-- End Modem power plug -->
		
		<!-- Start Count down after modem_plug -->
		<div id="modem_plug_after" style="margin: 0 auto;width: 750px;display:none;">
			<div style="text-align:left;">
				<span class="title2"><?echo I18N("h", "Step 4.  Restart the modem.");?></span>
			</div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "Wait at least 20 seconds after restarting the modem. Internet connection check will resume automatically.");?></span>
            </div>
			<div class="main_internet_process_bar">
				<img src="pic/process_bar.gif" />
			</div>
			<div style="text-align:center;">
				<span id="modem_plug_after_info" class="main_text"><?echo I18N("h", "Count down")." 20 ".I18N("h", "seconds");?></span>
			</div>		
		</div>		
		<!-- End Count down after modem_plug -->
		
		<!-- Start Connect to Internet stage again -->
		<div id="internet_check_again" style="display:none;">
            <div style="text-align:left;">
                <span class="title2"><?echo I18N("h", "Step 5.  Connect to Internet.");?></span>
            </div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "Checking your connection.");?></span>
            </div>
            <br>
            <div style="text-align:left;">
                <img id="pc_rt_internet_ex" src="pic/connect_internet.png" />
                <img id="pc_rt_fail_ex" src="pic/connect_internet_fail.png" style="display:none;" />
                <img id="pc_rt_ok_ex" src="pic/connect_internet_ok.gif" style="display:none;" />
                <img id="rt_internet_fail_ex" src="pic/connect_internet_ok_fail.png" style="display:none;" />
                <img id="pc_internet_ok_ex" src="pic/connect_internet_ok_ok.png" style="display:none;" />
            </div>
            <br>
            <div id="connect_detect_info_ex" style="text-align:left;">
                <span id="connect_device_ex" class="main_text_gray"><?echo I18N('h', 'We are checking your connection to the Internet.');?></span>
            </div>
            <div id="connect_internet_fail_ex" class="internet_status_item_ex" style="display:none;">
                <div class="internet_status_item_ex item1"><img src="pic/warning_mark.png"></div>
                <div class="internet_status_item_ex item2">
                    <span class="main_text_gray"><?echo i18n('Internet not connected. Click Next to verify your Internet service provider(ISP) account and your connection to the Internet.');?></span>
                </div>
                <div class="internet_status_item_ex item3">
                    <input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=9;PAGE.OnClickNext();">
                </div>
            </div>
            <div id="connect_internet_ok_ex" class="internet_status_item" style="display:none;">
                <div class="internet_status_item item1"><img src="pic/checked.png"></div>
                <div class="internet_status_item item2">
                <p class="main_text_green"><?echo I18N('h', 'Internet connection successful.');?></p>
                </div>
                <div class="internet_status_item item3">
                    <input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=7;PAGE.OnClickNext();"/>
                </div>
            </div>
		</div>
		<!-- End Connect to Internet stage again -->					
	
		<!-- Start Success message -->
		<div id="final_success" class="final_success_item" style="display:none;">
			<div class="final_success_item item1"><img src="pic/checked.png"></div>
			<div class="final_success_item item2">
				<span class="main_text_gray"><?echo I18N('h', 'Internet connection successful.');?></span>
			</div>
            <div class="final_success_item item3">
                <span class="main_text_gray"><?echo query("/device/hostname")." ";?><?echo I18N("h", "Network router is now connected to the Internet.");?></span>
            </div>
			<div class="final_success_item item4" id="final_success_btn_1" style="display:none;">
				<span><input type='button' class='button_blue' value="<?echo I18N('h', 'Next');?>" onClick="PAGE.DecideToGoWhere();"></span>
			</div>											
			<div class="final_success_item item4" id="final_success_btn_2" style="display:none;">
				<span><input type='button' class='button_blue' value="<?echo I18N('h', 'Finish');?>" onClick="PAGE.DecideToGoWhere();"></span>
			</div>
		</div>		
		<!-- End Success message -->	
	
        <div id="cable_check_again" style="margin: 0 auto;width: 750px;display:none;">
            <div style="text-align:left;">
                <span class="title2"><?echo I18N("h", "Step 3.  Recheck cable connections and power off the modem.");?></span>
            </div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "Confirm that cables are connected correctly. Next, unplug power and remove any battery from your modem.");?></span>
            </div>
            <div class="internet_list_item">
                <div class="internet_list_item item1"></div>
                <div class="internet_list_item item2"><img src="pic/check_cable_connection_ex.png"  style="width: 635px;"/></div>
                <div class="internet_list_item item3"><input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=3;PAGE.OnClickNext();"/></div>
            </div>
        </div>

        <div id="internet_type_select" style="margin: 0 auto;width: 750px;display:none;">
        <div style="text-align:left;">
            <span class="title2"><?echo I18N("h", "Step 6. Verify your Internet connnection type");?></span>
        </div>
        <div style="text-align:left;">
            <span class="main_text_gray">
                <? echo I18N("h", "Your Internet Service Provider(ISP) may require login username and password. Select the connection type and enter the username and password below to establish the Internet connection. If you do not have the login information, please contact your ISP for assistance.");?>
            </span>
        </div>
        <br>
        <div class="textinput_ex">
            <span class="name_ex"><? echo I18N("h", "Internet Connection Type");?></span>
            <span class="value_ex">
                <select id="wan_ip_mode" onchange="PAGE.OnChangeWanIpMode();" class="styled4">
                    <? if ($FEATURE_DHCPPLUS=="1")      echo '<option value="dhcpplus">'.I18N("h", "DHCP Plus").'</option>\n';?>
                    <option value="pppoe"><? echo I18N("h", "PPPoE");?></option>
                    <? if ($FEATURE_NOPPTP!="1")        echo '<option value="pptp">'.I18N("h", "PPTP").'</option>\n';?>
                    <? if ($FEATURE_NOL2TP!="1")        echo '<option value="l2tp">'.I18N("h", "L2TP").'</option>\n';?>
                    <? if ($FEATURE_NORUSSIAPPTP!="1")  echo '<option value="r_pptp">'.I18N("h", "PPTP (Russia)").'</option>\n';?>
                    <? if ($FEATURE_NORUSSIAL2TP!="1")  echo '<option value="r_l2tp">'.I18N("h", "L2TP (Russia)").'</option>\n';?>
					<option value="static"><? echo I18N("h", "Static IP");?></option>
                </select>
            </span>
        </div>

        <div id="box_wan_static_body" style="display:none">
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "IP Address");?></span>
                <span class="value_ex"><input id="st_ipaddr" type="text" size="20" maxlength="15" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Subnet Mask");?></span>
                <span class="value_ex"><input id="st_mask" type="text" size="20" maxlength="15" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex" id="DG" ><?echo I18N("h", "Default Gateway");?></span>
                <span class="value_ex"><input id="st_gw" type="text" size="20" maxlength="15" /></span>
            </div>
        </div>
        <div id="box_wan_ipv4_common_body" style="display:none">
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Primary DNS Server");?></span>
                <span class="value_ex"><input id="ipv4_dns1" type="text" size="20" maxlength="15" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Secondary DNS Server");?></span>
                <span class="value_ex">
                    <input id="ipv4_dns2" type="text" size="20" maxlength="15" />
                    (<?echo I18N("h", "optional");?>)
                </span>
            </div>
        </div>
        <!-- pppoe -->
        <div id="box_wan_pppoe_body" style="display:none">
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Username");?></span>
                <span class="value_ex">
                    <input id="pppoe_username" type="text" size="20" maxlength="63" />
                </span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Password");?></span>
                <span class="value_ex"><input id="pppoe_password" type="password" size="20" maxlength="63" /></span>
            </div>
        </div>
        <!-- pptp -->
        <div id="box_wan_pptp_body" style="display:none">
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Username");?></span>
                <span class="value_ex"><input id="pptp_username" type="text" size="20" maxlength="63" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Password");?></span>
                <span class="value_ex"><input id="pptp_password" type="password" size="20" maxlength="63" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "PPTP Server IP Address");?></span>
                <span class="value_ex">
                    <input id="pptp_server" type="text" size="20" maxlength="30" />
                </span>
            </div>
        </div>
        <!-- l2tp -->
        <div id="box_wan_l2tp_body" style="display:none">
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Username");?></span>
                <span class="value_ex"><input id="l2tp_username" type="text" size="20" maxlength="63" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "Password");?></span>
                <span class="value_ex"><input id="l2tp_password" type="password" size="20" maxlength="63" /></span>
            </div>
            <div class="textinput_ex">
                <span class="name_ex"><?echo I18N("h", "L2TP Server IP Address");?></span>
                <span class="value_ex"><input id="l2tp_server" type="text" size="20" maxlength="30" /></span>
            </div>
        </div>

        <div class="bottom_cancel_save">
            <input type="button" class="button_black" id="btn_skip" value="<?echo I18N('h', 'Skip');?>" onClick="PAGE.currentStage=10;PAGE.OnClickNext();">&nbsp;&nbsp;
            <input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Next');?>" />
        </div>

		</div>

        <div id="internet_check_again_again" style="margin: 0 auto;width: 750px;display:none;">
            <div style="text-align:left;">
                <span class="title2"><?echo I18N("h", "Step 7.  Connect to Internet.");?></span>
            </div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "Checking your connection.");?></span>
            </div>
            <br>
            <div style="text-align:left;">
                <img id="pc_rt_internet_ex_ex" src="pic/connect_internet.png" />
                <img id="pc_rt_fail_ex_ex" src="pic/connect_internet_fail.png" style="display:none;" />
                <img id="pc_rt_ok_ex_ex" src="pic/connect_internet_ok.gif" style="display:none;" />
                <img id="rt_internet_fail_ex_ex" src="pic/connect_internet_ok_fail.png" style="display:none;" />
                <img id="pc_internet_ok_ex_ex" src="pic/connect_internet_ok_ok.png" style="display:none;" />
            </div>
            <br>
            <div id="connect_detect_info_ex_ex" style="text-align:left;">
                <span id="connect_device_ex_ex" class="main_text_gray"><?echo I18N('h', 'We are checking your connection to the Internet.');?></span>
            </div>
            <div id="connect_internet_fail_ex_ex" class="internet_status_item_ex_ex" style="display:none;">
                <div class="internet_status_item_ex_ex item1"><img src="pic/warning_mark.png"></div>
                <div class="internet_status_item_ex_ex item2">
                    <span class="main_text_gray"><?echo i18n('Internet not connected.');?></span>
                </div>
                <div class="internet_status_item_ex item3">
                    <input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=11;PAGE.OnClickNext();"/>
                </div>
            </div>
            <div id="connect_internet_ok_ex_ex" class="internet_status_item" style="display:none;">
                <div class="internet_status_item item1"><img src="pic/checked.png"></div>
                <div class="internet_status_item item2">
                <p class="main_text_green"><?echo I18N('h', 'Internet connection successful.');?></p>
                </div>
                <div class="internet_status_item item3">
                    <input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.currentStage=7;PAGE.OnClickNext();"/>
                </div>
            </div>
        </div>

        <div id="final_fail_again_again" style="margin: 0 auto;width: 750px;display:none;">
            <br>
            <div style="text-align:left;">
                <span class="title2"><?echo I18N("h", "Internet not connected.");?></span>
            </div>
            <br>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "1. Power off your computer, then modem and then router.");?></span>
            </div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "2. Wait 30 seconds.");?></span>
            </div>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "3. Power up your devices in the following order:  (1)modem, (2)router, and (3)computer.");?></span>
            </div>
            <br>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "If you still cannot establish the Internet connection, please contact your Internet service provider(ISP) for assistance.");?></span>
            </div>
            <br>
            <div style="text-align:left;">
                <span class="main_text_gray"><?echo I18N("h", "You may also configure the Internet connection manually in the Advanced Settings>Internet Setup.");?></span>
            </div>
            <br>
            <div class="main_internet_next_button">
                <span><input type='button' class='button_blue' value="<?echo I18N('h', 'Next');?>" onClick="PAGE.DecideToGoWhere();"></span>
            </div>
        </div>

	</div>	
</form>  
	
