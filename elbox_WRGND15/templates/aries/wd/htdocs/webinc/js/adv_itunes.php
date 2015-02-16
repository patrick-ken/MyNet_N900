<script type="text/javascript">
var EventName = null;
function Page() {}
Page.prototype =
{
	services: "ITUNES",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function() {},
	itunes: null,
	InitValue: function(xml)
	{
		PXML.doc = xml;
		itunes = PXML.FindModule("ITUNES");			
		if (itunes==="") { alert("InitValue ERROR!"); return false; }
		if (XG(itunes+"/itunes/server/active")=="1") OBJ("itunes_active").checked = true;
		else OBJ("itunes_active").checked = false;	
		
		return true;
	},
	PreSubmit: function()
	{				
		itunes = PXML.FindModule("ITUNES");		
		if(!this.check_sdstatus()) return null;		
		XS(itunes+"/itunes/server/active", OBJ("itunes_active").checked?"1":"0");			
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	check_sdstatus: function()
	{
		itunes = PXML.FindModule("ITUNES");
		var storage_count = XG(itunes+"/runtime/device/storage/count");		
		if((storage_count != "0" && storage_count != "") || OBJ("itunes_active").checked==false)
		{			
			return true; 
		}
		else 
		{
			alert("No storage device!");
			return false;
		}	
	}
	
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
}
</script>
