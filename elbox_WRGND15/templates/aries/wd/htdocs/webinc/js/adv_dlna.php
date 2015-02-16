<script type="text/javascript">
var EventName = null;
function Page() {}
Page.prototype =
{
	services: "UPNPAV",
	OnLoad: function()
	{
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	upnpav: null,
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.upnpav = PXML.FindModule("UPNPAV");		
		if (this.upnpav==="") { alert("InitValue ERROR!"); return false; }		
		OBJ("dms_active").checked   = (XG(this.upnpav+"/upnpav/dms/active")==="1");
		
		return true;
	},
	PreSubmit: function()
	{				
		this.upnpav = PXML.FindModule("UPNPAV");		
		if(!this.check_sdstatus()) return null;		
		XS(this.upnpav+"/upnpav/dms/active", (OBJ("dms_active").checked ? "1":"0"));			
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	check_sdstatus: function()
	{
		this.upnpav = PXML.FindModule("UPNPAV");
		var storage_count = XG(this.upnpav+"/runtime/device/storage/count");		
		if(storage_count != "0" && storage_count != "")
		{			
			return true; 
		}
		else 
		{
			alert("No storage device!");
			return false;
		}	
	},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: <?if ($layout=="bridge") echo "false"; else echo "true";?>
}
</script>
