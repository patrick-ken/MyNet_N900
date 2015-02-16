<script type="text/javascript">

function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function()
	{
<?	
		$referer = $_SERVER["HTTP_REFERER"];
		if($referer == "")
			$referer = "./index.php";
		
		$title	= I18N("h", "Firmware Upload Fail");
		$btn = "'<input type=\"button\" class=\"button_blueX2\" value=\"".I18N("h", "Continue")."\" onclick=\"self.location=\\'tools_fwup.php\\';\">'";
		if($_GET["REASON"]=="ERR_REQ_TOO_LONG")
		{
			$message = "'".I18N("h", "Invalid image file")." ".I18N("h", "Please select the correct image file and upload it again.")."', ".$btn;
		}
		
		echo "\t\tvar msgArray = [".$message."];\n";
		echo "\t\tBODY.ShowMessage(\"".$title."\", msgArray);\n";
?>  },
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