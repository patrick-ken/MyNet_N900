<?include "/htdocs/phplib/phyinf.php";?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "sshd",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		BODY.ShowContent();
		switch (code)
		{
			case "OK":
				return true;
				break;
			default :
				return false;
		}
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.InitialWan()) return false;
		return true;
	},
	PreSubmit: function()
	{
		if (!this.SaveWanXML()) return null;

		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	InitialWan: function()
	{
		var base = PXML.FindModule("sshd");
		var intf = GPBT(base, "entry", "uid", "SSHD-1", false);
		if (XG(intf+"/active") == "1")
		{
			OBJ("enable_sshd").checked = true;
		}
		else
		{
			OBJ("enable_sshd").checked = false;
		}
		return true;
	},
	SaveWanXML: function()
	{
		var base = PXML.FindModule("sshd");
		var intf = GPBT(base, "entry", "uid", "SSHD-1", false);

		XS(intf+"/active", OBJ("enable_sshd").checked ? "1" : "0");
		return true;
	}
}
</script>
