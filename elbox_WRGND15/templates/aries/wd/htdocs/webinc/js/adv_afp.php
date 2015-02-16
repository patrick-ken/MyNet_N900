<script type="text/javascript">
var EventName = null;
function Page() {}
Page.prototype =
{
	services: "NETATALK",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	afp: null,
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.afp = PXML.FindModule("NETATALK");
		if (this.afp==="") { BODY.ShowAlert("InitValue ERROR!"); return false; }
		if (XG(this.afp+"/netatalk/active")=="1") OBJ("afp_active").checked = true;
		else OBJ("afp_active").checked = false;		
		
		return true;
	},
	PreSubmit: function()
	{				
		this.afp = PXML.FindModule("NETATALK");
		if(!this.check_sdstatus()) return null;
		XS(this.afp+"/netatalk/active", OBJ("afp_active").checked?"1":"0");				
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	check_sdstatus: function()
	{
		this.afp = PXML.FindModule("NETATALK");
		var storage_count = XG(this.afp+"/runtime/device/storage/count");		
		if(storage_count != "0" && storage_count != "")
		{
			return true; 
		}
		else 
		{
			alert("<? echo I18N("j", "No storage device!");?>");
			return false;
		}	
	},
	
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: <?if ($layout=="bridge") echo "false"; else echo "true";?>
}
</script>
