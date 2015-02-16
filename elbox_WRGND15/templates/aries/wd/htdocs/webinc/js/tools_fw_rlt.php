<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function()
	{
<?
		include "/htdocs/phplib/trace.php";
		$referer = $_SERVER["HTTP_REFERER"];
		$t = 0;

		if ($_GET["PELOTA_ACTION"]=="fwupdate")
		{
			if ($_GET["RESULT"]=="SUCCESS")
			{
				$size	= fread("j","/var/session/imagesize"); if ($size == "") $size = "12000000";
				$fptime	= query("/runtime/device/fptime");
				$bt		= query("/runtime/device/bootuptime");
				setattr("/runtime/device/delete", "get", 'date "+%a %d %b %Y"');
				$upgrade_time = get("x", "/runtime/device/delete");
				del("/runtime/device/delete");
				set("/device/time/upgrade_time", $upgrade_time);
				event("DBSAVE");
				$delay	= 70;
				if($FEATURE_MODEL_NAME=="storage") $delay = 120;				
				$t		= $size/64000*$fptime/1000+$bt+$delay;
				$title	= I18N("h", "Updating Firmware");
				$message= '"'.I18N("h", "Updating the firmware may take a few minutes.").
						  ' '.I18N("h", "Please DO NOT power off or disconnect any cables to the router.").'"';
			}
			else
			{
				$title = I18N("h", "Firmware Upload Fail");
				$btn = "'<input type=\"button\" class=\"button_blueX2\" value=\"".I18N("h", "Continue")."\" onclick=\"self.location=\\'tools_fwup.php\\';\">'";
				if ($_GET["REASON"]=="ERR_NO_FILE")
				{
					$message = "'".I18N("h", "No image file.")." ".I18N("h", "Please select the correct image file and upload it again.")."', ".$btn;
				}
				else if ($_GET["REASON"]=="ERR_INVALID_SEAMA" || $_GET["REASON"]=="ERR_INVALID_FILE")
				{
					$message = "'".I18N("h", "Invalid image file.")." ".I18N("h", "Please select the correct image file and upload it again.")."', ".$btn;
				}
				else if ($_GET["REASON"]=="ERR_UNAUTHORIZED_SESSION")
				{
					$message = "'".I18N("h", "You are unauthorized or authority is limited.")." ".I18N("h", "Please login first.")."', ".$btn;
				}
				else if ($_GET["REASON"]=="ERR_ANOTHER_FWUP_PROGRESS")
				{
					$message = "'".I18N("h", "Another image update process is progressing.")." ".I18N("h", "If you still want to update the image, please wait until the other process is done and try it again.")."', ".$btn;
				}
			}
		}
		else if ($_POST["ACTION"]=="langupdate")
		{
			TRACE_debug("ACTION=".$_POST["ACTION"]);
			TRACE_debug("FILE=".$_FILES["sealpac"]);
			TRACE_debug("FILETYPES=".$_FILETYPES["sealpac"]);
			$slp = "/var/sealpac/sealpac.slp";
			$title = I18N("h", "Update Language Pack");
			if ($_FILES["sealpac"]=="")
			{
				$title = I18N("h", "Language Pack Upload Fail");
				$message = "'".I18N("h", "The language pack image is invalid.")."', ".
							"'<a href=\"".$referer."\">".I18N("h", "Click here to return to the previous page.")."</a>'";
			}
			else if (fcopy($_FILES["sealpac"], $slp)!="1")
			{
				$title = I18N("h", "Language Pack Upload Fail");
				$message = "'INTERNAL ERROR: fcopy() return error!'";
			}
			else
			{			
				$langcode = sealpac($slp);
				if ($langcode != "")
				{
					$message = "'".I18N("h", "You have installed the language pack ($1) successfully.",$langcode)."', ".
								"'<a href=\"".$referer."\">".I18N("h", "Click here to return to the previous page.")."</a>'";
					fwrite(w, "/var/sealpac/langcode", $langcode);
					set("/runtime/device/langcode", $langcode);
					event("SEALPAC.SAVE");
				}
				else
				{
					$title = I18N("h", "Language Pack Upload Fail");
					$message = "'".I18N("h", "The language pack image is invalid.")."', ".
								"'<a href=\"".$referer."\">".I18N("h", "Click here to return to the previous page.")."</a>'";
					unlink($slp);
				}
			}
		}
		else if ($_POST["ACTION"]=="langclear")
		{
			$title = I18N("h", "Clear Language Pack");
			$message = "'".I18N("h", "Clearing the language pack ...")."', ".
						"'<a href=\"".$referer."\">".I18N("h", "Click here to return to the previous page.")."</a>'";
			set("/runtime/device/langcode", "en");
			event("SEALPAC.CLEAR");
		}
		else
		{
			$title = I18N("h", "Unknown action - ").$_POST["ACTION"];
			$message = "'<a href=\"./index.php\">".I18N("h", "Click here to redirect to the home page now.")."</a>'";
			$referer = "./index.php";
		}

		echo "\t\tvar msgArray = [".$message."];\n";
		if ($t > 0)
			echo "\t\tBODY.ShowCountdown_ex(\"".$title."\", msgArray, ".$t.", \"".$referer."\", \"".I18N("h", "Time until completion")."\");\n";
		else
			echo "\t\tBODY.ShowMessage(\"".$title."\", msgArray);\n";
?>	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return true; },
	InitValue: function(xml) { return true; },
	PreSubmit: function() { return null; },
	IsDirty: null,
	Synchronize: function() {}
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
}
</script>
