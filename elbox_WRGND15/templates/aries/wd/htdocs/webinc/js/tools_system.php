<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function()
	{
		var model = client_browser();
		if(model=="Chrome"||model=="Safari"||model=="Android")
		{
			OBJ("browse_btn").style.left="0px";//button
			OBJ("dst").style.left="90px";//text
		}
		if("<? echo $lang;?>" == "hu")
		{
			OBJ("Reset_text").style.fontSize="13px";
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)  {return true; },
	PreSubmit: function() { return null; },
	IsDirty: null,
	Check_time: 0,
	Download_time: 0,
	Update_time: 0,
	Synchronize: function() {},
	WD_model: "<? echo $FEATURE_MODEL_NAME;?>",
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	OnClickDownload: function()
	{
		OBJ("dlcfgbin").submit();
	},
	OnClickUpload: function()
	{
		if (OBJ("ulcfg").value=="")
		{
			BODY.ShowAlert("<?echo I18N("j", "You must enter the name of a configuration file first.");?>");
			return false;
		}
		OBJ("ulcfgbin").submit();
	},
	OnClickFReset: function()
	{
		if (confirm("<?echo I18N("j", "Are you sure you want to reset the device to its factory default settings?")."\\n".
					I18N("j", "This will cause all current settings to be lost.");?>"))
		{
			Service("FRESET");
		}
	},
	OnClickReboot: function()
	{
		if (confirm("<?echo I18N("j", "Are you sure you want to reboot the device?")."\\n".
					I18N("j", "Rebooting will disconnect any active Internet sessions.");?>"))
		{
			Service("REBOOT");
		}
	},
	OnClickLogout: function()
	{
		if (confirm("<?echo I18N("j", "Are you sure you want to logout?");?>"))
		{
			BODY.Logout();
		}
	}
}

function Service(svc)
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
	var msgArray = ["<?echo I18N("h", "If you changed the IP address of the router, you may need to renew the IP address of your device before accessing the router web page again.");?>"];
	var delay = 10;
	if(PAGE.WD_model=="storage") delay = delay + 20;
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
