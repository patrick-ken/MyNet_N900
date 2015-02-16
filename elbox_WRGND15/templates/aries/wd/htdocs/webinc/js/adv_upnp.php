<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "UPNP.LAN-1",
	OnLoad: function()
	{
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var upnp = PXML.FindModule("UPNP.LAN-1");
		if (upnp==="")
		{ alert("InitValue ERROR!"); return false; }
		OBJ("upnp").checked = (XG(upnp+"/inf/upnp/count") >= 1);
	
		return true;
	},
	PreSubmit: function()
	{
		if (this.rgmode)
		{
			var upnp = PXML.FindModule("UPNP.LAN-1");
			XS(upnp+"/inf/upnp/count",	OBJ("upnp").checked ? "2":"0");		
			if(OBJ("upnp").checked)
			{
				XS(upnp+"/inf/upnp/entry:1", "urn:schemas-upnp-org:device:InternetGatewayDevice:1");
				XS(upnp+"/inf/upnp/entry:2", "urn:schemas-upnp-org:device:WDRouter:1");							
			}
			else
			{
				XS(upnp+"/inf/upnp/entry:1", "");
				XS(upnp+"/inf/upnp/entry:2", "");
			}
		}
		else
		{
			PXML.IgnoreModule("UPNP.LAN-1");
		}
		return PXML.doc;
	},
	IsDirty: null,
	DEVICEp:null,
	Synchronize: function() {},

	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: <?if ($layout=="bridge") echo "false"; else echo "true";?>
}

</script>
