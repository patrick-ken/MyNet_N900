<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<style>
/* The CSS is only for this page.
 * Notice:
 *  If the items are few, we put them here,
 *  If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "INET.LAN-1,DHCPS4.LAN-1,DHCPS4.LAN-2,DNS4.LAN-1,RUNTIME.INF.LAN-1",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) {},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.InitLAN()) return false;
		if (!this.InitDHCPS()) return false;
		return true;
	},
	PreSubmit: function()
	{
		if (!this.PreLAN()) return null;
		if (!this.PreDHCPS()) return null;
		PXML.IgnoreModule("RUNTIME.INF.LAN-1");
		return PXML.doc;
	},
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	lanip: null,
	inetp: null,
	dhcps4: null,
	dhcps4_inet: null,
	dhcps4_2: null,
	leasep: null,
	mask: null,
	mask_v4: null,
	ipdirty: false,
	reserv_old: null,
	reserv_new: null,
	cfg: null,
	g_edit: 0,
	g_table_index: 1,
	InitLAN: function()
	{
		var lan	= PXML.FindModule("INET.LAN-1");
		var inetuid = XG(lan+"/inf/inet");
		this.inetp = GPBT(lan+"/inet", "entry", "uid", inetuid, false);
		if (!this.inetp)
		{
			BODY.ShowAlert("InitLAN() ERROR!!!");
			return false;
		}

		if (XG(this.inetp+"/addrtype") == "ipv4")
		{
			var b = this.inetp+"/ipv4";
			this.lanip = XG(b+"/ipaddr");
			this.mask = XG(b+"/mask");
			this.mask_v4 = COMM_IPv4INT2MASK(this.mask);
		}
		
		return true;
	},
	PreLAN: function()
	{		
		if (COMM_EqBOOL(OBJ("dhcpsvr").getAttribute("modified"), true))
		{
			PXML.DelayActiveModule("INET.LAN-1", "3");
		}
		else
		{
			PXML.IgnoreModule("INET.LAN-1");
		}
		
		return true;
	},
	InitDHCPS: function()
	{				
		var svc = PXML.FindModule("DHCPS4.LAN-1");
		var svc2 = PXML.FindModule("DHCPS4.LAN-2");
		var dns = PXML.FindModule("DNS4.LAN-1");
		var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
		if (!svc || !inf1p)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.dhcps4 = GPBT(svc+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
		this.dhcps4_2 = GPBT(svc2+"/dhcps4", "entry", "uid", "DHCPS4-2", false);
		this.dhcps4_inet = svc + "/inet/entry";
		this.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);		
		if (!this.dhcps4)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.leasep += "/dhcps4/leases";
		var tmp_ip = this.lanip.substring(this.lanip.lastIndexOf('.')+1, this.lanip.length);
		var tmp_mask = this.mask_v4.substring(this.mask_v4.lastIndexOf('.')+1, this.mask_v4.length);
		var startip = parseInt(XG(this.dhcps4+"/start"),10) + (tmp_ip & tmp_mask);
		var endip = parseInt(XG(this.dhcps4+"/end"),10) + (tmp_ip & tmp_mask);
		if (endip > 255)endip=255;

		OBJ("domain").value		= XG(this.dhcps4+"/domain");
		OBJ("dhcpsvr").checked	= (XG(svc+"/inf/dhcps4")!="")? true : false;
		OBJ("startip").value	= startip;
		OBJ("endip").value		= endip;
		OBJ("leasetime").value	= Math.floor(XG(this.dhcps4+"/leasetime")/3600);	
		
		this.OnClickDHCPSvr();
						
		return true;
	},
	PreDHCPS: function()
	{
		var lan = PXML.FindModule("DHCPS4.LAN-1");
		var dns = PXML.FindModule("DNS4.LAN-1");		
		var ipaddr = COMM_IPv4NETWORK(this.lanip, "24");
		var maxhost = COMM_IPv4MAXHOST(this.mask)-1;
		var network = ipaddr.substring(0, ipaddr.lastIndexOf('.')+1);
		var hostid = parseInt(COMM_IPv4HOST(this.lanip, this.mask), 10);
		var tmp_ip = this.lanip.substring(this.lanip.lastIndexOf('.')+1, this.lanip.length);
		var tmp_mask = this.mask_v4.substring(this.mask_v4.lastIndexOf('.')+1, this.mask_v4.length)
		var startip = parseInt(OBJ("startip").value, 10) - (tmp_ip & tmp_mask);
		var endip = parseInt(OBJ("endip").value, 10) - (tmp_ip & tmp_mask);
		
		this.dhcps4 = GPBT(lan+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
		var old_domain = XG(this.dhcps4+"/domain");		

		if (isDomain(OBJ("domain").value))
			XS(this.dhcps4+"/domain", OBJ("domain").value);
		else
		{
			BODY.ShowAlert("<?echo I18N("j", "The domain name is invalid.");?>");
			OBJ("domain").focus();
			return false;
		}
		if (OBJ("dhcpsvr").checked)	XS(lan+"/inf/dhcps4",	"DHCPS4-1");
		else						XS(lan+"/inf/dhcps4",	"");
		if (COMM_EqBOOL(OBJ("dhcpsvr").checked, true))
		{
			if (!TEMP_IsDigit(OBJ("startip").value) || !TEMP_IsDigit(OBJ("endip").value))
			{
				BODY.ShowAlert("<?echo I18N("j", "DHCP IP Address Range is invalid.");?>"); 
				return false;
			}
			/*if (hostid>=parseInt(OBJ("startip").value, 10) && hostid<=parseInt(OBJ("endip").value, 10))
			{
				BODY.ShowAlert("<?echo I18N("j", "The Router IP Address belongs to the lease pool of the DHCP server.");?>");
				return false;
			}we make comment about this check because of WD ITR 43414 don't need this check*/
			if (!TEMP_IsDigit(OBJ("leasetime").value))
			{
				BODY.ShowAlert("<?echo I18N("j", "The input lease time is invalid.");?>");
				return false;
			}
			if ( (this.mask >= 24 && (startip < 1 || endip > maxhost)) || (this.mask < 24 && (startip < 1 || endip > 255)))
			{
				BODY.ShowAlert("<?echo I18N("j", "DHCP IP Address Range is out of the boundary.");?>");  
				return false;
			}
			if (parseInt(OBJ("startip").value, 10) > parseInt(OBJ("endip").value, 10))
			{
				BODY.ShowAlert("<?echo I18N("j", "The start of the DHCP IP address range must be smaller than the end.");?>");
				return false;
			}
			
			XS(this.dhcps4+"/start", startip);
			XS(this.dhcps4+"/end", endip);
			XS(this.dhcps4+"/leasetime", OBJ("leasetime").value*3600);
			XS(this.dhcps4_2+"/leasetime", OBJ("leasetime").value*3600);
		}
										
		PXML.ActiveModule("DHCPS4.LAN-1");
		if(OBJ("domain").value == old_domain)
		{
			PXML.IgnoreModule("DNS4.LAN-1");
		}	
		return true;
	},	
	OnClickDHCPSvr: function()
	{
		if (OBJ("dhcpsvr").checked)
		{
			OBJ("startip").disabled = false;
			OBJ("endip").disabled = false;
			OBJ("domain").disabled = false;
			OBJ("leasetime").disabled = false;
		}
		else
		{
			OBJ("startip").disabled = true;
			OBJ("endip").disabled = true;
			OBJ("domain").disabled = true;
			OBJ("leasetime").disabled = true;
		}
	}
}

function FocusObj(result)
{
	var found = true;
	var node = result.Get("/hedwig/node");
	var nArray = node.split("/");
	var len = nArray.length;
	var name = nArray[len-1];
	if (node.match("dhcps4"))
	{
		switch (name)
		{		
		case "start":
			OBJ("startip").focus();
			break;
		case "end":
			OBJ("endip").focus();
			break;
		case "leasetime":
			OBJ("leasetime").focus();
		default:
			found = false;
			break;
		}
	}
	else
	{
		found = false;
	}

	return found;
}

function isDomain(domain)
{
	var rlt = true;
	var dArray = new Array();
	if (domain.length==0)	return rlt;
	else					dArray = domain.split(".");

	/* the total length of a domain name is restricted to 255 octets or less. */
	if (domain.length > 255)
	{
		rlt = false;
	}
	for (var i=0; i<dArray.length; i++)
	{
		var reg = new RegExp("[A-Za-z0-9\-]{"+dArray[i].length+"}");
		/* the label must start with a letter */
		if (!dArray[i].match(/^[A-Za-z]/))
		{
			rlt = false;
			break;
		}
		/* the label must end with a letter or digit. */
		else if (!dArray[i].match(/[A-Za-z0-9]$/))
		{
			rlt = false;
			break;
		}
		/* the label must be 63 characters or less. */
		else if (dArray[i].length>63)
		{
			rlt = false;
			break;
		}
		/* the label has interior characters that only letters, digits and hyphen */
		else if (!reg.exec(dArray[i]))
		{
			rlt = false;
			break;
		}
	}

	return rlt;
}

function GetIPLastField(ipaddr)
{
	return ipaddr.substring(ipaddr.lastIndexOf('.')+1);
}
</script>
