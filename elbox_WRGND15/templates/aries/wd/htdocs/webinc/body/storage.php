
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Share function for storage.");?></p>
		</div>
		<div>	
			<span class="text"><? echo I18N("h", "Select the share function for storage and setup the account and password.");?></span>
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
				<tr id="USB1" style="display:none;">
					<td align="center" >USB1</td>
					<td align="center" ><input id="en_samba1" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_dlna1" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_itune1" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_ftp1" type="checkbox" class="styled2" /></td>
				</tr>
				<tr id="USB2" style="display:none;">
					<td align="center" ><? 
						if($FEATURE_MODEL_NAME=="storage"){echo I18N("h", "Internal HDD");}
						else {echo "USB2";}
					?></td>
					<td align="center" ><input id="en_samba2" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_dlna2" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_itune2" type="checkbox" class="styled2" /></td>
					<td align="center" ><input id="en_ftp2" type="checkbox" class="styled2" /></td>
				</tr>									
			</table>
		</div>
		<br>
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Workgroup Name");?></span>
			<span class="value70" id="WG_name" ><input id="workgroup_name" type="text" size="15" maxlength="15" /></span>
		</div>
		<br>
		<hr>
<!--	
		<div>
			<p class="text_title"><?echo I18N("h", "Share function for storage.");?></p>
		</div>
		<div>	
			<p class="text"><? echo I18N("h", "Select the share function for storage and setup the account and password.");?></p>
		</div>
-->		
		<br>		
		<div>
			<input id="info_bt" type="button" class="button_blue" onclick="PAGE.OnClickInfo();" value="<?echo I18N('h', 'Show');?>">
		</div>
		<br>
		<div id="usb_info_list" style="display:none;">				
			<div class="centerline" align="left">
				<table id="storage_list" class="general" style="text-align:center;">
					<tr>
						<th width="120px" style="text-align:center;"><?echo I18N("h", "Storage Port");?></th>
						<th width="470px" style="text-align:center;"><?echo I18N("h", "Device name");?></th>
					</tr>
				</table>
				<br>									
			</div>
		</div>
		<hr>		
		<div>
			<p class="text_title"><?echo I18N("h", "DLNA Media Server");?></p>
		</div>
		<div class="textinput">
			<span class="value70" >
				<input id="reb_bt" type="button" class="button_blueX1p5" onclick="PAGE.OnClickReb();" value="<?echo I18N('h', 'Rebuild');?>">
				<span class="help_showdown" style="padding-left: 12px;"></span>
				<span class="help_msg">
					<span class="help_title"><div class="smallemptyline"></div><? echo I18N("h", "Rebuild");?></span>
						<span class="help_text">
							<table>
								<tr align="left" valign="top">
									<td><? echo I18N("h", "Rebuilds your media database. Use only for troubleshooting when database corruption is suspected.");?></td>
								</tr>
							</table>
						</span>
				</span>
			</span>
		</div>
		<br>
		<hr>
        <div>
            <p class="text_title"><?echo I18N("h", "Storage access permission");?></p>
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
			<span class="value70" id="WD_user" ><input type="text" size="20" maxlength="15" id="username" /></span>
		</div>
		<br>
		<div class="textinput">
			<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			<span style="color:white;"><?echo I18N("h", "Password");?></span>
			<span class="value70" id="WD_pw" ><input type="text" size="20" maxlength="15" id="password" /></span>
		</div>
		<br>
		<hr>
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>				
	</div>
</form>  
	
