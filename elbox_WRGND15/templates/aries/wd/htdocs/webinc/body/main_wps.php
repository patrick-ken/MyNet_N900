
<form id="mainform" onsubmit="return false;">
	<div style="margin-left:50px;">
		<!-- show this if wps is disabled -->
		<div id="wiz_stage_wps_disabled" style="display:none;">	
			<div>
				<span class="title2"><?echo I18N("h", "Add Wireless Device with WPS.");?></span>
			</div>
			<br>
			<div>
				<span class="main_text">
					<?
					echo I18N("h", "The WPS Function is currently disabled. Please go to the advanced WPS page to enable it!");
					?>
				</span>
			</div>
			<br>
			<br>
			<div>
				<input type="button" class="button_blueX3" value="<?echo I18N('h', 'Go to Advanced WPS page');?>" onclick="self.location.href='./wlan_wps.php';"/>
			</div>
		</div>
		
		<!-- Start of Stage 1 (Choose WPS)-->
		<div id="wiz_stage_1" style="display:none;">
			<div style="height: 30px;"></div>
			<div style="width: 600px;">
				<div>
					<span class="add_dev_desc">
						<?
							echo I18N("h", "If your wireless device is capable of WPS (Wi-Fi Protected Setup), ")." ".
							I18N("h", "you can connect to the My Net router by simply pushing the WPS buttons or by entering a PIN.")."<br><br>".
							I18N("h", "If the device is not capable of WPS, you can still connect by entering a network name")." ".
							I18N("h", "and password.");
						?>
					</span>
				</div>
				<br><br>
				<div class="textinput">
					<span type="button" class="name_c" style="text-decoration: underline;" onclick="PAGE.OnClickChooseWPS();" onmouseover="this.style.cursor='pointer';">
						<?echo I18N("h", "Click here to connect using WPS");?>
					</span>
					<br>
					<span type="button" class="name_c" style="text-decoration: underline;" onclick="PAGE.OnClickChooseNonWPS();" onmouseover="this.style.cursor='pointer';">
						<?echo I18N("h", "Click here to connect by entering network name and password");?>
					</span>
				</div>
			</div>
		</div>
		<!-- End stage 1 (Choose WPS) -->
        <div id="wiz_stage_nowireless" style="display:none;">
            <div style="height: 30px;"></div>
            <div style="width: 600px;">
                <div>
                    <span class="add_dev_desc">
                        <?
							echo I18N("h", "Before adding a wireless device to your network, you must enable the wireless. ")."<br>".
                            I18N("h", "Click OK to enable your wireless.");

                        ?>
                    </span>
                </div>
                <br><br>
            	<div class="main_wps_nowireless">
                	<input type="button" class="button_blue" value="<?echo I18N('h', 'OK');?>" onClick="COMM_GoToWebPageByPOST('/wlan.php',{FromWPS:'1'});"/>
                	<input type="button" class="button_black" value="<?echo I18N('h', 'Cancel');?>" onClick="self.location.href='main_dashboard.php'"/>
            	</div>
            </div>
        </div>
        <div id="wiz_wireless" style="display:none;">
            <div style="height: 30px;"></div>
            <div style="width: 600px;">
                <div>
                    <span class="add_dev_desc" id="encrypt_reason_msg">
                    </span>
                </div>
                <br><br>
            	<div class="main_wps_nowireless">
                	<input type="button" class="button_blue" value="<?echo I18N('h', 'OK');?>" onClick="COMM_GoToWebPageByPOST('/wlan.php',{FromWPS:'1'});"/>
                	<input type="button" class="button_black" value="<?echo I18N('h', 'Cancel');?>" onClick="self.location.href='main_dashboard.php'"/>
            	</div>
            </div>
        </div>
        <div id="wiz_stage_nowps" style="display:none;">
            <div style="height: 30px;"></div>
            <div style="width: 600px;">
                <div>
                    <span class="add_dev_desc">
                        <?
                            echo I18N("h", "WPS must be enabled in order to use WPS button or PIN.")."<br>".
                            I18N("h", "Click OK to enable WPS.");

                        ?>
                    </span>
                </div>
                <br><br>
                <div class="main_wps_nowireless">
                    <input type="button" class="button_blue" value="<?echo I18N('h', 'OK');?>" onClick="PAGE.nowps=1;BODY.OnSubmit();"/>
                    <input type="button" class="button_black" value="<?echo I18N('h', 'Cancel');?>" onClick="PAGE.OnClickCancel();"/>
                </div>
            </div>
        </div>
		
		<!-- Start of Stage 2 (WPS Method) -->
		<div id="wiz_stage_2_auto" class="wps_item" style="display:none;">
            <div class="wps_item item1"> <span class="title2"><? echo I18N("h", "Select one of three WPS (Wi-Fi Protected Setup) methods. ");?></span></div>
            <div class="wps_item item2"><span class="name_c"><strong><? echo I18N("h", "Method 1: Push Button method");?></strong></span></div>
            <div class="wps_item item3"><span class="name_c" style="margin-left: 30px;margin-bottom: -5px;"><? echo I18N("h", "Press the WPS button"). "<img src=\"/pic/wps_small.png\" style=\"margin-left: 5px; margin-right: 5px; margin-bottom: -4px;\">".I18N("h", "shown here to start the connection process");?></span></div>
            <div class="wps_item item4"><input type="button" class="button_WPS" id="connect_pbc" onclick="PAGE.OnClickPBC();" onmouseover="this.style.cursor='pointer';" style="margin-left: 280px;"></div>
            <div class="wps_item item5"><span class="name_c"><strong><? echo I18N("h", "Method 2: Device PIN method");?></strong></span></div>
            <div class="wps_item item6"><span class="name_c" style="margin-left: 30px;"><? echo I18N("h", "Enter the wireless device's WPS PIN here and click Next to start the connection process.");?></span></div>
            <div class="wps_item item7">
            <table>
                <tr>
					<td><span class="name_c" style="margin-left: 30px;"><? echo I18N("h", "Device's WPS PIN Code : ");?></span></td>
					<td><input id="pincode_ex" type="text" size="9" maxlength="9" onKeyUp="button_change(pincode_ex.value);"></td>
                </tr>
            </table>
            </div>
            <div class="wps_item item8"><span class="name_c"><strong><? echo I18N("h", "Method 3: Router PIN method");?></strong></span></div>
            <div class="wps_item item9"><span class="name_c" style="margin-left: 30px;"><? echo I18N("h", "If requested for the router's WPS PIN, enter the following PIN on your device:");?></span></div>
            <div class="wps_item item10"><span id="pincode" class="title1" style="margin-left: 280px;"></span></div>
			<div class="bottom_cancel_save">
				<input type="button" class="button_black" onclick="PAGE.OnClickCancel();" value="<?echo I18N('h', 'Back');?>">&nbsp;&nbsp;			
				<input type="button" class="button_blue" id="btn_next" onclick="PAGE.OnClickConnectPIN();" value="<?echo I18N('h', 'Next');?>" disabled = ture;>
            </div>	
		</div>

		<div id="wiz_stage_2_manu" style="display:none;">
			<div>
				<div>
					<span class="title2">
						<? echo I18N("h", "Step 1: ");?>
					</span>
					<div>
						<span class="main_text">
							<?
							echo I18N("h", "From your wireless device, select one of the wireless network names (sometimes called SSID, Wi-Fi network, or home network) below.");
							?>
						</span>
					</div>
					<div class="textinput"  style="margin-left: 40px;height: 30px;">
				        <span class="name"><? echo I18N("h", "2.4Ghz SSID ");?></span>
				        <span id="ssid" class="valueb"><? echo I18N("h", "2.4G ssid ");?></span>
				    </div>
				    <div class="textinput"  style="margin-left: 40px;height: 30px;">
				        <span class="name"><? echo I18N("h", "5Ghz SSID ");?></span>
				        <span id="ssid_Aband" class="valueb"><? echo I18N("h", "5G ssid ");?></span>
				    </div>
				    <br>
				    <span class="title2">
							<? echo I18N("h", "Step 2: ");?>
						</span>
					<div>
						<span class="main_text">
							<?
							echo I18N("h", "Once you select the wireless network, you will be prompted to enter the password (sometimes called passphrase, security key, pass key or network key)."). "". 
								I18N("h", " Enter the corresponding password below for the wireless network you selected. ");
							?>
						</span>
					</div>
					<div class="textinput"  style="margin-left: 40px;"> 
						<span id="frequency"  class="name"><? echo I18N("h", "Settings for 2.4Ghz ");?></span>
				    </div>
				    <div class="textinput"  style="margin-left: 60px;display:none;"> 
				        <span class="name"><?echo I18N("h", "Security Mode");?></span> <span id="security" class="valueb">aaaa</span>
				    </div>
				    <div id="st_wep" class="textinput"  style="margin-left: 60px; display:none;"> 
				        <span  class="name"><?echo I18N("h", "WEP Key");?></span> <span id="wepkey" class="valueb">aaaa</span>
				    </div>
				    <div id="st_cipher" class="textinput"  style="margin-left: 60px;display:none;"> 
						<span class="name"><?echo I18N("h", "Cipher Type");?>:</span> <span id="cipher" class="valueb">aaaa</span>
					</div>
					<div id="st_pskkey" class="textinput"  style="margin-left: 60px;height: 30px;">
						<span  class="name"><?echo I18N("h", "Password");?>:</span> <span id="pskkey" class="valueb">( <?echo I18N("h", "no password needed");?> )</span>
				    </div>
				    
				    
				    <div class="textinput"  style="margin-left: 40px;"> 
						<span id="frequency_Aband" class="name"><?echo I18N("h", "Settings for 5Ghz ");?></span>
				    </div>
				    <div class="textinput"  style="margin-left: 60px;display:none;"> 
				        <span class="name"><?echo I18N("h", "Security Mode");?></span> <span id="security_Aband" class="valueb"></span>
				    </div>
				    <div id="st_wep_Aband" class="textinput"  style="margin-left: 60px;display:none;"> 
				        <span class="name"><?echo I18N("h", "WEP Key");?></span> <span id="wepkey_Aband" class="valueb">aaaa</span>
				    </div>
				    <div id="st_cipher_Aband" class="textinput"  style="margin-left: 60px;display:none;"> 
						<span class="name"><?echo I18N("h", "Cipher Type");?>:</span> <span id="cipher_Aband" class="valueb">aaaa</span>
					</div>
					<div id="st_pskkey_Aband" class="textinput"  style="margin-left: 60px;height: 30px;">
						<span class="name"><?echo I18N("h", "Password");?>:</span> <span id="pskkey_Aband" class="valueb">( <?echo I18N("h", "no password needed");?> )</span>
				    </div>
				</div>
			</div>
			<div style="height: 6px;"></div>
			<div class="main_wps_next_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Back');?>" onClick="PAGE.OnClickCancel();"/>
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Close');?>" onClick="PAGE.OnClickClosed();"/>
			</div>
		</div>
		<!-- End of Stage 2 (WPS Method) -->
		
		<!-- Start of Stage 3 -->
		<div id="wiz_stage_2_msg" style="display:none;">
			<div style="height:30px;"></div>
			<div>
				<span class="title2"><?echo I18N("h", "Connecting your wireless device...");?></span>
				<br>
				<div class="textinput"><span class="wps_name_ex" id="msg"></span>
				</div>
			</div>
		</div>
		<!-- End of Stage 3 -->
		
		<!-- Start of Stage 4 -->
		<div id="wiz_wps_success" style="display:none;">
		  <div style="height:30px;"></div>
			<div>
				<table>
				<tbody>
					<tr>
						<td ><img src="/pic/wps_ok.png" style="margin-left: 200px; margin-right: 25px;"></td>
						<td ><span class="title2"> <? echo I18N("h", "The wireless device is ")." <br> ".I18N("h", "successfully connected."); ?> </span> </td>
					</tr>
				</tbody>
				</table>
			</div>
			<br><br><br>
			<div class="textinput">
				<span type="button" class="name_c" style="text-decoration: underline;margin-left: 200px;" onclick="PAGE.OnClickAddAnother();" onmouseover="this.style.cursor='pointer';">
					<? echo I18N("h", "Add another wireless device to the network"); ?>
				</span>
			</div>	
			<div class="bottom_cancel_save">
				<br><br><br><br>
				<input type="button" class="button_blue" onclick="javascript:self.location.href='\main_dashboard.php';" value="<?echo I18N('h', 'Finish');?>" />
			</div>
		</div>
		<!-- Message of Stage 4 -->
				
		<!-- Start of Stage 5 -->
		<div id="wiz_wps_failed" style="display:none;">
		  <div style="height:30px;"></div>
			<div style="margin-left: 150px;">
				<span class="title2">
					<? echo I18N("h", "Wireless Connection was not successful.")." <br> ".I18N("h", "Select one of following options to retry:");?>
				</span>
				<br>
				<div><span class="main_text" id="msg"></span>
				</div>
				<br><br><br>
				<div class="textinput">
					<span type="button" class="name_c" style="text-decoration: underline;" onclick="PAGE.OnClickChooseWPS();" onmouseover="this.style.cursor='pointer';">
						<? echo I18N("h", "Retry WPS");?>
					</span><br><br>
					<span type="button" class="name_c" style="text-decoration: underline;" onclick="PAGE.OnClickChooseNonWPS();" onmouseover="this.style.cursor='pointer';">
						<? echo I18N("h", "Connect by entering network name and password manually");?>
					</span>
				</div>
			</div>
			<div class="bottom_cancel_save">
				<br><br><br><br>
				<input type="button" class="button_blue" onclick="javascript:self.location.href='\main_dashboard.php';" value="<?echo I18N('h', 'Finish');?>" />
			</div>
		</div>
		<!-- Message of Stage 5 -->				

	</div>	
</form>  
