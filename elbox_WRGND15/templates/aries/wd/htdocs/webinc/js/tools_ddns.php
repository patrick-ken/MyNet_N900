<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DDNS4.WAN-1, DDNS4.WAN-3, RUNTIME.DDNS4.WAN-1",    
	OnLoad: function()
	{
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("DDNS4."+this.devicemode);
		if (p === "") alert("ERROR!");
		OBJ("en_ddns").checked = (XG(p+"/inf/ddns4")!=="");
		var ddnsp = GPBT(p+"/ddns4", "entry", "uid", this.ddns, 0);
		OBJ("server").value	= ( XG(ddnsp+"/provider") == "" ? "DYNDNS" : XG(ddnsp+"/provider") );
		OBJ("host").value	= XG(ddnsp+"/hostname");
		OBJ("user").value	= XG(ddnsp+"/username");
		OBJ("passwd").value	= XG(ddnsp+"/password");
		OBJ("passwd_verify").value	= XG(ddnsp+"/password");

		if(OBJ("en_ddns").checked) PAGE.GetReport();
		else OBJ("report").innerHTML = "";
		PAGE.EnableDDNS();
		return true;
	},
	PreSubmit: function()
	{
		if (!COMM_EqSTRING(OBJ("passwd").value, OBJ("passwd_verify").value))
		{
			BODY.ShowAlert("<?echo I18N('j', 'Pasword and Verify Password do not match.');?>");
			return null;
		}
		
		if(OBJ("en_ddns").checked)
		{
			if(OBJ("server").value=="TZO")
			{
				if(OBJ("passwd").value.length < 16)
				{
					BODY.ShowAlert("<?echo I18N('j', 'TZO Pasword(Key) length can not less than 16.');?>");
					return null;
				}
				if(OBJ("user").value.length > 60)
				{
					BODY.ShowAlert("<?echo I18N('j', 'TZO Username length can not exceed 60.');?>");
					return null;
				}
			}
		}
		PXML.ActiveModule("DDNS4.WAN-1");  
		PXML.ActiveModule("DDNS4.WAN-3");
		PXML.IgnoreModule("RUNTIME.DDNS4.WAN-1");
			
		var p = PXML.FindModule("DDNS4."+this.devicemode);

		
		if (OBJ("user").value=="" && OBJ("host").value=="" && OBJ("passwd").value=="")
		{
			XS(p+"/inf/ddns4", "");
			var ddnsp = GPBT(p+"/ddns4", "entry", "uid", this.ddns, 0);
			if (ddnsp)
			{
				var c = XG(p+"/ddns4/count");
				XS(p+"/ddns4/count", c-1);
				XD(ddnsp);
			}
		}
		else
		{
			XS(p+"/inf/ddns4", OBJ("en_ddns").checked ? this.ddns : "");
			if (OBJ("en_ddns").checked || (OBJ("server").value!=="")	|| (OBJ("host").value!=="") || (OBJ("user").value!=="")		|| (OBJ("passwd").value!==""))
			{
				var ddnsp = GPBT(p+"/ddns4", "entry", "uid", this.ddns, 0);
				if (!ddnsp)
				{
					var c = XG(p+"/ddns4/count");
					var s = XG(p+"/ddns4/seqno");
					c += 1;
					s += 1;
					XS(p+"/ddns4/entry:"+c+"/uid", this.ddns);
					XS(p+"/ddns4/count", c);
					XS(p+"/ddns4/seqno", s);
					ddnsp = p+"/ddns4/entry:"+c;
				}
				XS(ddnsp+"/provider", OBJ("server").value);
				XS(ddnsp+"/hostname", OBJ("host").value);
				XS(ddnsp+"/username", OBJ("user").value);
				XS(ddnsp+"/password", OBJ("passwd").value);
			}
		}
		if (this.devicemode == "WAN-3")	PXML.IgnoreModule("DDNS4.WAN-1");
		else				PXML.IgnoreModule("DDNS4.WAN-3");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	rgmode: <?if ($layout=="bridge") echo "false"; else echo "true";?>,
	devicemode: "WAN-1",
	ddns: "DDNS4-1",
	GrayItems: function(disabled)
	{
		var frmObj = document.forms[0];
		if(OBJ("en_ddns").checked)
		{
			for (var idx = 0; idx < frmObj.elements.length; idx+=1)
			{
				var obj = frmObj.elements[idx];
				var name = obj.tagName.toLowerCase();
				if (name === "input" || name === "select")
				{
					obj.disabled = disabled;
				}
			}
		}
	},
	
	GetReport: function()
	{
		var self = this;
		var ajaxObj = GetAjaxObj("getreport");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			//xml.dbgdump();
			ajaxObj.release();
			var msg = "";
			var mode ;
			if (xml.Get("/ddns4/valid")==="1" && xml.Get("/ddns4/testtimeCheck")== self.ddns_testtime)  
			{
				var p = xml.Get("/ddns4/provider");
				var s = xml.Get("/ddns4/status");
				var r = xml.Get("/ddns4/result");
				if (s === "IDLE")
				{
					if(p === "TZO")
					{
						switch(r)
						{
							case "SUCCESS":
							msg = "<?echo I18N('h','IP address has updated successfully.');?>";
							break;
							case "NONE":
							msg = "<?echo I18N('h','IP address had been updated into TZO server.');?>";
							break;
							case "DUPLICATE":
							msg = "<?echo I18N('h','Multiple DNS updates within 60 seconds.');?>";
							break;
							case "SVRERR":
							mode="FAIL";
							msg = "<?echo I18N('h','TZO server is under maintenance.');?>";
							break;
							case "NOAUTH":
							mode="FAIL";
							msg = "<?echo I18N('h','Authentication failure. Please check Host name, Username and Password again.');?>";
							break;
							case "ERROR":
							mode="FAIL";
							msg = "<?echo I18N('h','Account blocked. Please contact TZO for more details.');?>";
							break;
						}
					}
					else
					{
						switch(r)
						{
							case "SUCCESS":
							msg = "<?echo I18N('h','IP address has updated successfully.');?>";
							break;
							case "NONE":
							msg = "<?echo I18N('h','IP address had been updated into DynDns server.');?>";
							break;
							case "SVRERR":
							mode="FAIL";
							msg = "<?echo I18N('h','DynDns server is under maintenance.');?>";
							break;
							case "NOAUTH":
							mode="FAIL";
							msg = "<?echo I18N('h','Authentication failure. Please check Username and Password again.');?>";
							break;
							case "BADHOST":
							mode="FAIL";
							msg = "<?echo I18N('h','Authentication failure. Please check Username and Password again.');?>";
							break;
							case "ERROR":
							mode="FAIL";
							msg = "<?echo I18N('h','Account blocked. Please contact DynDns for more details.');?>";
							break;
						}
					}
					self.GrayItems(false); 
				}
				else
				{
					if		(s === "CONNECTING")msg = "<?echo I18N('h', 'Waiting');?>"	+ "...";
					else if (s === "UPDATING")	msg = "<?echo I18N('h', 'Waiting');?>"	+ "...";
					else						msg = "<?echo I18N('h', 'Waiting');?>"	+ "...";
                    
                    self.ddns_count += 1 ;
					if (self.ddns_count < 10) setTimeout('PAGE.GetReport()', 1000);
					else
					{	
						self.GrayItems(false); 
						msg = "<?echo I18N('h', 'Update failed.');?>";  
						mode="FAIL";
					}
				}
			}
			else
			{	
				self.ddns_count += 1 ;
				if (self.ddns_count < 15)
				{
					msg = "<?echo I18N('h', 'Waiting');?>"	+ "...";
					setTimeout('PAGE.GetReport()', 1000);
				}	
				else
				{
					self.GrayItems(false);
					msg = "<?echo I18N('h', 'Update failed.');?>";
					mode="FAIL";
				}	
			}
			OBJ("report").innerHTML = msg;
			if(mode=="FAIL")
			{
				OBJ("en_ddns").checked = false;
				PAGE.EnableDDNS();
				BODY.NewWDStyle_refresh();
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("ddns_act.php", "act=getreport");
	},
	ddns_count: 0,
	ddns_testtime: "",
	OnClickUpdateNow: function()
	{	
		if(OBJ("host").value == "") return alert("Please enter the host name.");
		if(OBJ("user").value == "") return alert("Please enter the user account");
		if(OBJ("passwd").value == "") return alert("Please enter the password");

		PXML.IgnoreModule("DDNS4.WAN-1");
		PXML.IgnoreModule("DDNS4.WAN-3");
		PXML.ActiveModule("RUNTIME.DDNS4.WAN-1");
		
		var self = this;
		var time_now = new Date();
		self.ddns_testtime = time_now.getHours().toString() + time_now.getMinutes().toString() + time_now.getSeconds().toString();
		
		var p = PXML.FindModule("RUNTIME.DDNS4.WAN-1");
		XS(p+"/runtime/inf/ddns4/provider", OBJ("server").value);
		XS(p+"/runtime/inf/ddns4/hostname", OBJ("host").value);
		XS(p+"/runtime/inf/ddns4/username", OBJ("user").value);
		XS(p+"/runtime/inf/ddns4/password", OBJ("passwd").value);
		XS(p+"/runtime/inf/ddns4/testtime", self.ddns_testtime);
		
		var xml = PXML.doc;
		PXML.UpdatePostXML(xml);
        COMM_CallHedwig(PXML.doc, function(xml){PXML.hedwig_callback(xml);});
 		
		this.GrayItems(true);   
		OBJ("report").innerHTML = "<?echo I18N("h", "Start updating...");?>";
		self.ddns_count = 0 ;
		
		var ajaxObj = GetAjaxObj("updatenow");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			self.GetReport();
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("ddns_act.php", "act=getreport");
	},
	EnableDDNS: function()
    {
        if(OBJ("en_ddns").checked)
        {
            OBJ("server").disabled = OBJ("host").disabled = OBJ("user").disabled = OBJ("passwd").disabled = OBJ("passwd_verify").disabled = false;
        }
        else
        {
            OBJ("server").disabled = OBJ("host").disabled = OBJ("user").disabled = OBJ("passwd").disabled = OBJ("passwd_verify").disabled = true;
            OBJ("passwd").value = "";
            OBJ("passwd_verify").value = "";
        }
    }

};

</script> 
