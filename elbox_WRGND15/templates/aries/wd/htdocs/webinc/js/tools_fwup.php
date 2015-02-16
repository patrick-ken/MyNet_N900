<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div.textinput span.value_left400BigMargin
{
	color: white;
	margin-top: 25px;
	position: absolute;
	left: 400px;
}

</style>
<script type="text/javascript">
<?	
	$size	= fread("j","/var/session/imagesize"); if ($size == "") $size = "12000000";
	$fptime	= query("/runtime/device/fptime");
	$bt		= query("/runtime/device/bootuptime");
	$delay	= 70;
	if($FEATURE_MODEL_NAME=="storage") $delay = 120;
	$firmware_update_time = $size/64000*$fptime/1000+$bt+$delay;
?>
function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function()
	{
		lanugage_StyleSet('<? echo $lang;?>' , "<?echo $TEMP_MYNAME; ?>");
		var model=client_browser();
		if(model=="Chrome")
		{
			OBJ("dst").style.width="149px";
			OBJ("browse_btn").style.left="157px";
			OBJ("UPLOAD").style.left="247px";
		}
		else if(model=="Opera"  || model=="Safari")
		{
			OBJ("dst").style.width="121px";
			OBJ("browse_btn").style.left="130px";
			OBJ("UPLOAD").style.left="220px";
		}
		else if(model=="Firefox")
		{
			OBJ("dst").style.width="139px";
		}
		else if(model=="IE")
		{
			OBJ("dst").style.width="157px";
			OBJ("browse_btn").style.left="165px";
			OBJ("UPLOAD").style.left="255px";
		}
		else if(model=="Android")
		{
			OBJ("dst").style.width="170px";
			OBJ("browse_btn").style.left="182px";
			OBJ("UPLOAD").style.left="280px";
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml) { return true; },
	PreSubmit: function() { return null; },
	IsDirty: null,
	Check_time: 0,
	Download_time: 0,
	Update_time: 0,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	OnClickFWCHK: function(act)
	{
		var self = this;
		var ajaxObj = GetAjaxObj("fw_check");

		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if(act=="fw_check") 
			{
				OBJ("chkfw_btn").disabled = true;
				BODY.NewWDStyle_refresh();
				setTimeout('PAGE.OnClickFWCHK("fw_check_result")',1000);
			}
			else if(act=="fw_check_result")
			{
				if(xml.Get("/fw_update_online/report")=="true") 
				{
					OBJ("fw_lastest_ver").innerHTML = xml.Get("/fw_update_online/fw_lastest_ver");  
					OBJ("fw_update").style.display = "";
				}
				else if(xml.Get("/fw_update_online/report")=="false")
				{ OBJ("fw_lastest").style.display = ""; }
				else if(xml.Get("/fw_update_online/report")=="ConnectionFail")
				{ OBJ("fw_checkWANconnect").style.display = ""; }
				else if(PAGE.Check_time < 10)
				{
					PAGE.Check_time++;
					setTimeout('PAGE.OnClickFWCHK("fw_check_result")',1000);
				}
				else OBJ("fw_checkWANconnect").style.display = "";
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("fw_update_online.php", "act="+act);	
	},
	OnClickFWUpdateOnline: function(act)
	{
		var self = this;
		var ajaxObj = GetAjaxObj("fw_update_online");
		
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if(act=="fw_download") 
			{			
				if(xml.Get("/fw_update_online/report")=="OK")
				{			
					setTimeout('PAGE.OnClickFWUpdateOnline("fw_download_result")',1000);
					var title = "<? echo I18N('h', 'Updating Firmware');?>";
					var msgArray = ["<? echo I18N('h', 'Updating the firmware may take a few minutes.');?>",
									"<? echo I18N('h', 'Please DO NOT power off or disconnect any cables to the router.');?>",
									"<? echo I18N('h', 'Download');?>"+": 0%"];
					BODY.ShowMessage(title, msgArray);
				}
				else BODY.ShowAlert("<?echo I18N('h', "Please check the connection to the router.");?>");
			}
			else if(act=="fw_download_result")
			{
				if(xml.Get("/fw_update_online/report")=="true")
				{
					PAGE.OnClickFWUpdateOnline("fw_update");
				}
				else if(xml.Get("/fw_update_online/report")=="failed")
				{
					clearTimeout(BODY.timerId);
					var title = "<? echo I18N('h', 'Firmware Download Fail');?>";
					var msgArray = ["<? echo I18N('h', 'The Network may be Disconnected or the Download Speed is Too Slow.');?>",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"tools_fwup.php\"'>"];
					BODY.ShowMessage(title, msgArray);
				}				
				else if(PAGE.Download_time < 3600)
				{
					var download_percentage = 0;
					if(xml.Get("/fw_update_online/complete_percent")!="") download_percentage = xml.Get("/fw_update_online/complete_percent");
					var title = "<? echo I18N('h', 'Updating Firmware');?>";
					var msgArray = ["<? echo I18N('h', 'Updating the firmware may take a few minutes.');?>",
									"<? echo I18N('h', 'Please DO NOT power off or disconnect any cables to the router.');?>",
									"<? echo I18N('h', 'Download');?>"+": "+download_percentage+"%"];				
					BODY.ShowMessage(title, msgArray);					 
					PAGE.Download_time++;
					setTimeout('PAGE.OnClickFWUpdateOnline("fw_download_result")',1000);
				}
				else
				{
					clearTimeout(BODY.timerId);
					var title = "<? echo I18N('h', 'Firmware Download Fail');?>";
					var msgArray = ["<? echo I18N('h', 'The Network may be Disconnected or the Download Speed is Too Slow.');?>",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"tools_fwup.php\"'>"];
					BODY.ShowMessage(title, msgArray);
				}		
			}
			else if(act=="fw_update")
			{
				if(xml.Get("/fw_update_online/report")=="OK")
				{					
					setTimeout('PAGE.OnClickFWUpdateOnline("fw_update_result")',20000);
					var title = "<? echo I18N('h', 'Updating Firmware');?>";
					var msgArray = ["<? echo I18N('h', 'Updating the firmware may take a few minutes.');?>",  
									"<? echo I18N('h', 'Please DO NOT power off or disconnect any cables to the router.');?>"];
					var firmware_update_time = <? echo $firmware_update_time;?>;
					var url = "http://<? echo $_SERVER["HTTP_HOST"];?>/index.php";
					var wait_descript = "<? echo I18N('h', 'Time until completion');?>";
					BODY.ShowCountdown(title, msgArray, firmware_update_time, url, wait_descript);
				}
				else BODY.ShowAlert("<?echo I18N('h', "Please check the connection to the router.");?>");
			}
			else if(act=="fw_update_result")
			{				
				if(xml.Get("/fw_update_online/report")!="0")
				{
					var title = "<? echo I18N('h', 'Firmware Update Fail');?>";
					var msgArray = ["",
									"<input type='button' class='button_blueX2' value='<? echo I18N('h', 'Continue');?>' onclick='javascript:self.location.href=\"tools_fwup.php\"'>"];				
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
						clearTimeout(BODY.timerId);
						BODY.ShowMessage(title, msgArray);
					}
					else if(PAGE.Update_time < 20)
					{ 
						PAGE.Update_time++;
						setTimeout('PAGE.OnClickFWUpdateOnline("fw_update_result")',3000);
					}
					else
					{
						msgArray[0] = "<? echo I18N('h', 'Unknown Condition. Please Try Again');?>";
						clearTimeout(BODY.timerId);
						BODY.ShowMessage(title, msgArray);						
					}
				}
			}							
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("fw_update_online.php", "act="+act);		
	}
}

function Service(svc)
{	
	var banner = "<?echo I18N('h', "Rebooting");?>...";
	var msgArray = ["<?echo I18N('h', "If you changed the IP address of the router, you may need to renew the IP address of your device before accessing the router web page again.");?>"];
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
