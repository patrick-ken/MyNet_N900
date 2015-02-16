<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DMZ.NAT-1",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	InitValue: function(xml)
	{
		PXML.doc = xml;		
		if (!this.InitDMZ()) return false;
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
	lanip: "<? echo INF_getcurripaddr("LAN-1"); ?>",
	mask: "<? echo INF_getcurrmask("LAN-1"); ?>",

	InitDMZ: function()
	{
		this.dmz = PXML.FindModule("DMZ.NAT-1");
		if (this.dmz==="")
		{
			alert("InitDMZ() ERROR!!");
			return false;
		}
		this.dmz += "/nat/entry/dmz";
		var hostid = XG(this.dmz+"/hostid");
		if (hostid=="")
		{
			OBJ("dmzhost").value = "";
		}
		else
		{
			var network = COMM_IPv4NETWORK(this.lanip, this.mask);
			OBJ("dmzhost").value = COMM_IPv4IPADDR(network, this.mask, hostid);
		}
		OBJ("dmzenable").checked = (XG(this.dmz+"/enable") === "1");
		COMM_SetSelectValue(OBJ("hostlist"), "");

		this.OnClickDMZEnable();
		return true;
	},
	PreDMZ: function()
	{
		if (OBJ("dmzenable").checked)
		{
			var network = COMM_IPv4NETWORK(this.lanip, this.mask);

			var hostip	= OBJ("dmzhost").value;
			var hostnet	= COMM_IPv4NETWORK(hostip, this.mask);
			var maxhost	= COMM_IPv4MAXHOST(this.mask);
			if (network !== hostnet)
			{
				BODY.ShowAlert("<?echo I18N('j', 'The DMZ IP Address should be in the same network as the LAN!');?>");
				return null;
			}

			var lanip_hostid = COMM_IPv4HOST(this.lanip, this.mask);
			var hostid = COMM_IPv4HOST(hostip, this.mask);
			if (hostid === 0 || hostid === maxhost || hostid === lanip_hostid)
			{
				BODY.ShowAlert("<?echo I18N('j', 'Invalid DMZ IP address !');?>");
				return null;
			}

			XS(this.dmz+"/enable",	"1");
			XS(this.dmz+"/inf",		"LAN-1");
			XS(this.dmz+"/hostid",	COMM_IPv4HOST(hostip, this.mask));
		}
		else
		{
			XS(this.dmz+"/enable",	"0");
			XS(this.dmz+"/inf",		"");
			XS(this.dmz+"/hostid",	"");
		
		}
		return true;
	},
		
	OnClickDMZEnable: function()
	{
		if (OBJ("dmzenable").checked)
		{
			OBJ("dmzhost").setAttribute("modified", "false");
			OBJ("dmzhost").disabled = false;
			OBJ("dmzadd").disabled = false;
			OBJ("hostlist").disabled = false;
		}
		else
		{
			OBJ("dmzhost").setAttribute("modified", "ignore");
			OBJ("dmzhost").disabled = true;
			OBJ("dmzadd").disabled = true;
			OBJ("hostlist").disabled = true;
		}
	},
	OnClickDMZAdd: function()
	{
		if(OBJ("hostlist").value === "")
		{
			BODY.ShowAlert("<?echo I18N('j', 'Please select a machine first!');?>");
			return null;
		}
		OBJ("dmzhost").value = OBJ("hostlist").value;
	}
}
</script>
