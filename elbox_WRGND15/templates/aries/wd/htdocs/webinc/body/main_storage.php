
<form id="mainform" onsubmit="return false;">
	<div style="margin-left:100px;">
		<!-- Start start to set up stage -->
		<div id="start_setup" style="display:none;">
			<div>
				<span class="title2"><?echo I18N("h", "Step 1.  Start to set up.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "This wizard will take you through the steps to set up your router's attached USB storage device or internal hard drive as a network share.  If you are setting up a USB storage device, make sure it is connected to the router USB port.");?></span>
			</div>
			<div style="position:relative;">
				<span  style="top: 5px;position: absolute;">
					<img src="pic/storage_to_router.png" />
				</span>
				<span style="left: 570px; top: 255px;position: absolute;">
					<input  type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.OnClickNext();"/>
				</span>
			</div>				
		</div>	
		<!-- End start to set up stage -->
		
		<!-- Start list the detected storage stage -->
		<div id="list_storage" style="display:none;">
			<br>
			<div>
				<span class="title2"><?echo I18N("h", "Step 2.  Detected Storage Devices");?></span>
			</div>
			<div id="list_storage_hasusb">
				<span class="main_text"><?echo I18N("h", "The following storage devices are detected.  Please select the device you want to set up for network share.");?></span>
			</div>
			<div id="list_storage_nousb" style="display:none;">
				<span class="main_text"><?echo I18N("h", "No drive attached. Please attach a hard drive.");?></span>
			</div>			
			<br>
			<div style="margin-left:580px;">
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Refresh');?>" onClick="PAGE.OnClickRefresh();"/>
			</div>			
			<div id="USB_1" style="display:none;">
				<span><input type="radio" class="styled" name="storage_device" value="usb1_check" /></span>
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span id="USB_1_name" class="main_text"></span>			
			</div>
			<br>
			<div id="USB_2" style="display:none;">
				<span><input type="radio" class="styled" name="storage_device" value="usb2_check" /></span>
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span id="USB_2_name" class="main_text"></span>		
			</div>
			<br>					
			<br>
			<br>
			<br>
			<div class="main_storage_bottom_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Back');?>" onClick="PAGE.OnClickPre();"/>
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.OnClickNext();" id="list_storage_next"/>
			</div>						
		</div>
		<!-- End list the detected storage stage -->
		
		<!-- Start input password to unlock the device stage -->
		<div id="unlock_stage" style="display:none;">
			<br>
			<div>
				<span class="title2"><?echo I18N("h", "Step 2.  List the Detected Storage Devices.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "The storage device is locked.  Please enter the password to unlock.");?></span>
			</div>
			<br>
			<div id="unlock_pwd_input" class="textinput">
				<span class="name"><?echo I18N("h", "Password Key:");?> <input id="unlock_pwd" type="password" size="48" maxlength="25" /></span>
				
			</div>
			<div id="unlock_countdown" style="display:none;color: white;">
				<span style="text-align:center;">
					<div>
						<span><? echo I18N("h", "Please wait");?></span>
						<span id="count_down_second" style="color:red;"></span>
						<span>&nbsp;<? echo I18N("h", "seconds");?></span>
					</div>
				</span>
			</div>
			<div id="unlock_fail" style="display:none;color: white;">
				<span style="text-align:center;">
					<div>
						<span><? echo I18N("h", "Failed to unlock the storage because of incorrect password; please try again.");?></span>
					</div>
				</span>
			</div>					
			<br>		
			<div class="main_storage_bottom_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Back');?>" onClick="PAGE.OnClickPre();"/>
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.OnClickNext();" id="next_button"/>
			</div>						
		</div>
		<!-- End input password to unlock the device stage -->
		
		<!-- Start unlock fail three times stage -->
		<div id="unlock_fail_3" style="display:none;">
			<div>
				<span class="title2"><?echo I18N("h", "Step 2.  List the Detected Storage Devices.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "The hard drive has failed to unlock five times. You have exceeded the maximum unlock attempts. Please follow below steps and try to unlock again.");?></span>
			</div>
			<div><span class="main_text">1.<?echo I18N("h", "Safely unplug your drive from the router.");?></span></div>
			<div><span class="main_text">2.<?echo I18N("h", "Turn your drive off, and then on.");?></span></div>
			<div><span class="main_text">3.<?echo I18N("h", "Plug your drive into the router.");?></span></div>
			<div class="main_storage_bottom_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Cancel');?>" onClick="self.location.href = 'http://<? echo $_SERVER["HTTP_HOST"];?>/main_dashboard.php'"/>
			</div>			
		</div>
		<!-- End unlock fail three times stage -->
		
		<!-- Start share setup -->
		<div id="share_setup" style="display:none;">
			<div>
				<span class="title2"><?echo I18N("h", "Step 3.  Share function for storage.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "Select the share function for storage and setup the account and password.");?></span>
				<span class="help_showdown" style="padding-left: 12px;"></span>
				<span class="help_msg">
					<span class="help_title"><div class="smallemptyline"></div><? echo I18N("h", "Storage Share Functions");?></span>
						<span class="help_text">
							<table>
								<tr align="left" valign="top">
									<td><? echo I18N("h", "Share");?>:</td>
									<td><? echo I18N("h", "Network file sharing. It allows sharing of files stored in the storage device.");?></td>
								</tr>
								<tr align="left" valign="top">
									<td><? echo I18N("h", "DLNA");?>:</td>
									<td><? echo I18N("h", "Digital Living Network Alliance. Set up as a media server to make video, music and pictures available to networked media players and renderers.");?></td>
								</tr>
								<tr align="left" valign="top">
									<td><? echo I18N("h", "iTunes");?>:</td>
									<td><? echo I18N("h", "Set up as an iTunes server to stream music contents to other devices in the network.");?></td>
								</tr>
								<tr align="left" valign="top">
									<td><? echo I18N("h", "FTP");?>:</td>
									<td><? echo I18N("h", "File Transfer Protocol. Set up as a FTP server to transfer files with a FTP client over TCP-based network.");?></td>
								</tr>
							</table>
						</span>
				</span>
			</div>
			<br>
			<div class="centerline" align="center">
				<table id="" class="general">
					<tr>
						<th width="100px" style="text-align:center;" ><?echo I18N("h", "Storage");?></th>
						<th width="100px" style="text-align:center;" ><?echo I18N("h", "Share");?></th>
						<th width="100px" style="text-align:center;" >DLNA</th>
						<th width="100px" style="text-align:center;" >iTunes</th>
						<th width="100px" style="text-align:center;" >FTP</th>
					</tr>
					<tr>
						<td id="hard_drive" align="center" >USB1</td>
						<td align="center" ><input id="en_samba" type="checkbox" class="styled2" /></td>
						<td align="center" ><input id="en_dlna" type="checkbox" class="styled2" /></td>
						<td align="center" ><input id="en_itune" type="checkbox" class="styled2" /></td>
						<td align="center" ><input id="en_ftp" type="checkbox" class="styled2" /></td>
					</tr>					
				</table>
			</div>
			<br>
			<div class="textinput">
		        <span class="name"><?echo I18N("h", "Workgroup Name");?></span>
		        <span class="value70"><input id="workgroup_name" type="text" size="15" maxlength="15" /></span>
			</div>
			<div>
				<p class="text_title"><?echo I18N("h", "Set up access permission for storage");?></p>
			</div>
        	<div>
           		<span><input type="radio" class="styled" name="public_share" value="1" onClick="PAGE.PublicShareCheck();"><p class="text_title">&nbsp;&nbsp;<?echo I18N("h", "Public Share");?></p></span>
        	</div>
        	<div>
            	<span><input type="radio" class="styled" name="public_share" value="0" onClick="PAGE.PublicShareCheck();"><p class="text_title">&nbsp;&nbsp;<?echo I18N("h", "User Account");?></p></span>
        	</div>
			<div class="textinput">
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color:white;"><?echo I18N("h", "Username");?></span>
				<span class="value70"><input type="text" size="20" maxlength="15" id="username" /></span>
			</div>
			<br>
			<div class="textinput">
				<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color:white;"><?echo I18N("h", "Password");?></span>
				<span class="value70"><input type="text" size="20" maxlength="15" id="password" /></span>
			</div>
			<div class="main_storage_bottom_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Back');?>" onClick="PAGE.OnClickPre();"/>
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Next');?>" onClick="PAGE.OnClickNext();"/>
			</div>						
		</div>		
		<!-- End share setup -->
		
		<!-- Start Finish stage -->
		<div id="finish" style="display:none;">
			<br>
			<div>
				<span class="title2"><?echo I18N("h", "Step 4. Setup Complete.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "You have finished basic setup of attached storage for use in the network.");?></span>
			</div>
			<div>
				<span class="main_text"><?echo I18N("h", "To change other options of the shared drive, please go to the Storage section of the Advanced Settings.");?></span>
			</div>			
			<br>
			<div class="main_storage_bottom_button">
				<input type="button" class="button_black" value="<?echo I18N('h', 'Back');?>" onClick="PAGE.OnClickPre();"/>
				<input type="button" class="button_blue" value="<?echo I18N('h', 'Finish');?>" onClick="PAGE.OnClickNext();"/>
			</div>						
		</div>
		<!-- End Finish stage -->
	</div>	
</form>  
	
