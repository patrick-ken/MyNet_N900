<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
<?	
	$size	= fread("j","/var/session/imagesize"); if ($size == "") $size = "12000000";
	$fptime	= query("/runtime/device/fptime");
	$bt		= query("/runtime/device/bootuptime");
	$delay	= 120;//same with tools_fw_rlt.php's delay time.
	$firmware_update_time = $size/64000*$fptime/1000+$bt+$delay;
?>
function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		return true;
	},
	PreSubmit: function()
	{		
		if (!this.PreDMZ()) return null;
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},

	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	dmz: null,
	format_n: 0,
	restore_n: 0,
	Check_time: 0,
	Download_time: 0,
	Update_time: 0,
	ReturnPage_timeId: null,
	OnClickFormatConfirm: function()
	{
		var title = "";
		var msgArray = ["<? echo I18N("h", "Warning: Formatting will erase all files on the disk drive. Please confirm to proceed.");?>",
						"<div><span><input type='button' class='button_black' value='<? echo I18N('h', 'Cancel');?>' onclick='javascript:self.location.href=\"storage_format.php\"'></span><span><input type='button' class='button_blue' value=\"<? echo I18N('h', "Confirm");?>\" onclick=\"PAGE.OnClickFormat('format');\"></span></div>"];
		BODY.ShowMessage(title, msgArray);
	},	
	OnClickFormat: function(act)
	{
		var self = this;
		var ajaxObj = GetAjaxObj("format");
		
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if(act=="format")
			{
				setTimeout('PAGE.OnClickFormat("format_result")',30000);
				var title = "<? echo I18N("h", "Formatting the hard drive");?>"+"...";
				var msgArray = ["<? echo I18N("h", "This may take a few minutes. Please DO NOT power off or disconnect any cables. The router will reboot after the format process is completed.");?>"];
				BODY.ShowCountdown_ex(title, msgArray, 70, "1", "");
			}
			else if(act=="format_result")
			{
				if(xml.Get("/format/format_report")=="success")
				{
					setTimeout('Service("REBOOT")', (8-PAGE.format_n)*5000);				
				}
				else if(PAGE.format_n < 6) 
				{
					setTimeout('PAGE.OnClickFormat("format_result")',5000);
					PAGE.format_n++;
				}
				else
				{
					var title = "<? echo I18N("h", "Format failed");?>";
					var msgArray = ["<? echo I18N("h", "The format process is failed.");?>",
									"<div><input type='button' class='button_blue' value='<? echo I18N('h', "return");?>' onclick='javascript:self.location.href=\"storage_format.php\"'></div>"];
					BODY.ShowMessage(title, msgArray);					
				}		
			}				
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("storage_format_act.php", "act="+act);
	},
	OnClickRestoreConfirm: function()
	{
		var title = "";
		var msgArray = ["<? echo I18N("h", "Warning: Restoring the HDD will erase all files on the disk drive. Please confirm to proceed.");?>",
						"<div><span><input type='button' class='button_black' value='<? echo I18N('h', 'Cancel');?>' onclick='javascript:self.location.href=\"storage_format.php\"'></span><span><input type='button' class='button_blue' value=\"<? echo I18N('h', "Confirm");?>\" onclick=\"PAGE.OnClickRestore('fw_check_force');\"></span></div>"];
		BODY.ShowMessage(title, msgArray);		
	},
	OnClickRestore: function(act)
	{
		//Use fw_check_force to build the runtime nodes for the fw_download and fw_update
		if(act=="fw_check_force" || act=="fw_check_result" || act=="fw_download" || act=="fw_download_result" || act=="fw_update" || act=="fw_update_result") 
		{ var action_file = "fw_update_online.php";}
		else var action_file = "storage_format_act.php";
		var self = this;
		var ajaxObj = GetAjaxObj("restore");
		
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if(act=="fw_check_force")
			{
				setTimeout('PAGE.OnClickRestore("fw_check_result")',5000);
				var title = "<? echo I18N("h", "Downloading system file pack");?>"+"...";
				var msgArray = ["<? echo I18N("h", "This may take a few minutes. Please DO NOT power off or disconnect any cables.");?>"];
				BODY.ShowCountdown_ex(title, msgArray, 80, "1", "");		
			}
			else if(act=="fw_check_result")
			{
				var fw_check_result = true;
				if(xml.Get("/fw_update_online/report")=="true") 
				{ PAGE.OnClickRestore("fw_download"); }
				else if(xml.Get("/fw_update_online/report")=="false")
				{ fw_check_result = false;}
				else if(PAGE.Check_time < 10)
				{
					PAGE.Check_time++;
					setTimeout('PAGE.OnClickRestore("fw_check_result")',3000);
				}
				else { fw_check_result = false;}		
				
				if(!fw_check_result)
				{
					var title = "<? echo I18N('h', 'Firmware Check Fail');?>";
					var msgArray = ["<? echo I18N('h', 'The Network may be Disconnected or the Download Speed is Too Slow.');?>",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"storage_format.php\"'>"];
					BODY.ShowMessage(title, msgArray);
				}
			}	
			else if(act=="fw_download")
			{
				if(xml.Get("/fw_update_online/report")=="OK") 
				{
					clearTimeout(BODY.timerId);
					setTimeout('PAGE.OnClickRestore("fw_download_result")',1000);
				}
				else 
				{
					clearTimeout(BODY.timerId);
					BODY.ShowAlert("<?echo I18N('h', "Please check the connection to the router.");?>");				
				}
			}
			else if(act=="fw_download_result")
			{
				var percentage = xml.Get("/fw_update_online/complete_percent");
				var status_tmp = 50 + parseInt(percentage/2);
				var status = parseInt(status_tmp/5);
				BODY.ShowProcessBar(status);	
				if(xml.Get("/fw_update_online/report")=="true")
				{
					setTimeout('restore_result_ex()',3000);
				}
				else if(xml.Get("/fw_update_online/report")=="failed")
				{
					var title = "<? echo I18N('h', 'Firmware Download Fail');?>";
					var msgArray = ["<? echo I18N('h', 'The Network may be Disconnected or the Download Speed is Too Slow.');?>",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"storage_format.php\"'>"];
					BODY.ShowMessage(title, msgArray);
				}				
				else if(PAGE.Download_time < 3600)
				{				
					PAGE.Download_time++;
					setTimeout('PAGE.OnClickRestore("fw_download_result")',1000);
				}
				else
				{
					var title = "<? echo I18N('h', 'Firmware Download Fail');?>";
					var msgArray = ["<? echo I18N('h', 'The Network may be Disconnected or the Download Speed is Too Slow.');?>",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"storage_format.php\"'>"];
					BODY.ShowMessage(title, msgArray);
				}				
			}
			else if(act=="restore_result")
			{
				clearTimeout(BODY.timerId);
				var status_tmp = 50 + parseInt(50/15);
				var status = parseInt(status_tmp/5);
				if (xml.Get("/format/restore_report")=="success")
				{
					BODY.ShowProcessBar(20);
				}
				else
				{
					BODY.ShowProcessBar(status);
				}

				if(xml.Get("/format/restore_report")=="success")
				{
					setTimeout('fw_update_ex()',3000);
				}
				else if(PAGE.restore_n < 15)
				{
					setTimeout('PAGE.OnClickRestore("restore_result")',5000);
					PAGE.restore_n++;
				}
				else
				{
					var title = "<? echo I18N("h", "Restore failed");?>";
					var msgArray = ["<? echo I18N("h", "The Restore process is failed.");?>",
									"<div><input type='button' class='button_blue' value='<? echo I18N('h', "return");?>' onclick='javascript:self.location.href=\"storage_format.php\"'></div>"];
					BODY.ShowMessage(title, msgArray);					
				}
			}
			else if(act=="fw_update")
			{
				if(xml.Get("/fw_update_online/report")=="OK") 
				{
					setTimeout('PAGE.OnClickRestore("fw_update_result")',2000);
					this.ReturnPage_timeId = setTimeout("self.location.href='/'", <? echo $firmware_update_time*1000;?>);
				}
				else BODY.ShowAlert("<?echo I18N('h', "Please check the connection to the router.");?>");				
			}
			else if(act=="fw_update_result")
			{
				if(xml.Get("/fw_update_online/report")!="0")
				{
					var title = "<? echo I18N('h', 'Firmware Update Fail');?>";
					var msgArray = ["",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"storage_format.php\"'>"];				
					var report = xml.Get("/fw_update_online/report");
					switch (report)
					{
						case "200":
							msgArray[0] = "<? echo I18N('h', 'Invalid Firmware Package');?>";
							break;
						case "201":
							msgArray[0] = "<? echo I18N('h', 'Not Enough Space on Device for Upgrade');?>";
							break;
						case "202":
							msgArray[0] = "<? echo I18N('h', 'Upgrade Download Failure');?>";
							break;
						case "203":
							msgArray[0] = "<? echo I18N('h', 'Upgrade Unpack Failure');?>";
							break;
						case "204":
							msgArray[0] = "<? echo I18N('h', 'Upgrade Copy Failure');?>";
							break;
						default:
							break;
					}
					
					if(report=="200" || report=="201" || report=="202" || report=="203" || report=="204")
					{
						BODY.ShowMessage(title, msgArray);
						clearTimeout(this.ReturnPage_timeId);
					}
					else
					{
						if(PAGE.Update_time < 20)
						{ 
							PAGE.Update_time++;
							setTimeout('PAGE.OnClickRestore("fw_update_result")',2000);
						}
						else
						{
							msgArray[0] = "<? echo I18N('h', 'Unknown Condition. Please Try Again');?>";
							BODY.ShowMessage(title, msgArray);
							clearTimeout(this.ReturnPage_timeId);					
						}
					}
				}
			}																	
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest(action_file, "act="+act);
	}
}

function restore_result_ex()
{
	PAGE.OnClickRestore('restore');
	setTimeout('PAGE.OnClickRestore("restore_result")',30000);
	var title = "<? echo I18N("h", "Restoring and reformatting the hard drive");?>"+"...";
	var msgArray = ["<? echo I18N("h", "This may take a few minutes. Please DO NOT power off or disconnect any cables.");?>"];
	BODY.ShowCountdown_ex(title, msgArray, 80, "1", "");
}

function fw_update_ex()
{
	PAGE.OnClickRestore("fw_update");
    var title = "<? echo I18N("h", "Updating the firmware");?>"+"...";
    var msgArray = ["<? echo I18N("h", "This may take a few minutes. Please DO NOT power off or disconnect any cables. The router will reboot after the firmware update is completed.");?>"];
	BODY.ShowCountdown_ex(title, msgArray, "<? echo $firmware_update_time;?>", "1", "");
}

function Service(svc)
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
	var msgArray = ["<?echo I18N("h", "If you changed the IP address of the router, you may need to renew the IP address of your device before accessing the router web page again.");?>"];
	var delay = 10;
	var sec = <?echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");

	if (svc=="FRESET")		url = "http://192.168.1.1/index.php";
	else if (svc=="REBOOT")	url = "http://<?echo $_SERVER["HTTP_HOST"];?>/index.php";
	else					return false;
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK")
			BODY.ShowAlert("Internal ERROR!\nEVENT "+svc+": "+xml.Get("/report/message"));
		else
			BODY.ShowCountdown(banner, msgArray, sec, url);
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
}
</script>
