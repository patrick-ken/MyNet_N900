<div>
	<div>
		<p class="text_title"><?echo I18N("h", "Firmware Update");?></p>
	</div>
	<div>
		<p class="text">
			<? echo I18N("h", "There may be new firmware for your router to improve functionality and performance.");?>
		</p>
	</div>
	<hr>

	<div>
		<p class="text_title"><?echo I18N("h", "Firmware Information");?></p>
	</div>
	<div class="textinput">
		<span class="name"><? echo I18N("h", "Current Firmware Version");?></span>
		<span class="value_left400" id="CFV" ><? echo query("/runtime/device/firmwareversion");?></span>
	</div>
	<div class="textinput">
		<span class="name"><? echo I18N("h", "Firmware Build Date");?></span>
		<span class="value_left400" id="CFD" >
		<? 
			$origin = get("h","/runtime/device/firmwarebuilddate"); 
			$inx= strchr($origin," ");
			$output = "";
			while($inx!="")
			{
				$str = substr($origin,0,$inx);
				$origin = substr($origin,$inx+1,strlen($origin)-$inx-1);
				if(isalpha($str)!=0)
				{
					$output = $output.I18N("h",$str)."&nbsp;&nbsp;";
				}
				else
				{
					$output = $output.$str."&nbsp;&nbsp;";
				}
				$inx= strchr($origin," ");
			}
			if(isalpha($origin)!=0)
			{
				$output = $output.I18N("h",$origin)."&nbsp;&nbsp;";
			}
			else
			{
				$output = $output.$origin."&nbsp;&nbsp;";
			}
			echo $output;
		?></span>
	</div>
	<div class="textinput">
		<span class="name"><? echo I18N("h", "Check Online Now for Latest Firmware Version");?></span>
		<span class="value_left400" id="CON" >
			<input type="button" class="button_blueX3" id="chkfw_btn" value='<?echo I18N("h", "Check Now");?>' onclick="PAGE.OnClickFWCHK('fw_check');" />
		</span>
	</div>
	<div id="fw_lastest" class="textinput" style="display:none;">
		<span class="name"><span class="value_left400"><? echo I18N("h", "This Firmware is the Latest Version");?></span>
	</div>			
	<div id="fw_checkWANconnect" class="textinput" style="display:none;">
		<span class="name"><span class="value_left400"><? echo I18N("h", "Firmware check cannot be completed.");?></span>
		<span class="name"><span class="value_left400BigMargin"><? echo I18N("h", "Check your Internet connection and try again.");?></span>
	</div>
	<div id="fw_update" style="display:none;">
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Lastest Firmware Version");?></span>
			<span id="fw_lastest_ver" class="value_left400"></span>
		</div>	
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Please Update the Latest Firmware Version");?></span>
			<span class="value_left400">
				<input type="button" class="button_blueX3" value='<?echo I18N("h", "Update Now");?>' onclick="PAGE.OnClickFWUpdateOnline('fw_download');" />
			</span>
		</div>
	</div>	
	<br>
	<hr>

	<div>
		<p class="text_title"><? echo I18N("h", "Firmware Upgrade from a File");?></p>
		<p class="text"><? echo I18N("h", "To upgrade the firmware, locate the upgrade file on the local hard drive with the Browse button. Once you have found the file, click the Upload button to start the firmware upgrade.");?></p>
	</div>
	<br>
	<form id="fwup" action="fwup.cgi" method="post" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="REPORT_METHOD" value="301" />
			<input type="hidden" name="REPORT" value="tools_fw_rlt.php" />
			<input type="hidden" name="DELAY" value="10" />
			<input type="hidden" name="PELOTA_ACTION" value="fwupdate" />
			<div class="textinput">
				<span class="name"><? echo I18N("h", "Select a File to Upgrade");?></span>
				<span class="help" style="padding-left: 12px;"></span>
				<span class="help_msg">
					<span class="help_title"><? echo I18N("h", "Firmware Upgrade");?></span>
					<span class="help_text"><? echo I18N("h", "To upgrade the firmware, your PC must have a wired connection to the router. Enter the name of the firmware upgrade file, and click the Upload button.");?></span>
				</span>			
				<span class="value_left400" id="FWBottom">
					<input type="file" size="22" name="fw" id="ulcfg" modified="false"
					class="FWuploadunit" onchange="var str=this.form.fw.value;
					var ind=-1;
					ind=str.lastIndexOf('\\');
					if(ind!=-1) OBJ('dst').value=str.substr(ind+1,str.length-ind-1);
					else OBJ('dst').value=str;"
					/>
					<input type="text" class="FWupload_text" id="dst" maxlength="100" />
					<input type="button" class="FWupload_button" id="browse_btn" value="<?echo I18N("h", "Browse");?>" />					
					<input type="submit" class="button_blue" modified="false" value="<? echo I18N("h", "Upload");?>" id="UPLOAD" style="position:absolute; left:236px;">
				</span>
			</div>
			<? if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE") == "") echo "<div style=\"height: 12px;\"></div>"; ?>
		</div>
	</form>
</div>	
