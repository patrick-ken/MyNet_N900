<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "",
	OnLoad: function()
	{
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result) { return false; },
	InitValue: function(xml)
	{
		return true;
	},
	PreSubmit: function()
	{
		return null;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	wcount: 0,
	pcount: 0,
	OnClick_Ping: function()
	{
		this.ResetPing();
		this.pcount=0;
		OBJ("ping").disabled = true;
		OBJ("dst").value= COMM_EatAllSpace(OBJ("dst").value);
		if (OBJ("dst").value==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Please enter a host name or IP address for pinging.");?>");
			OBJ("dst").focus();
			this.ResetPing();
			return false;
		}

		var self = this;
		var ajaxObj = GetAjaxObj("ping");

		OBJ("report").innerHTML = "<?echo I18N("h", "Pinging...");?>"

		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			self.GetPingReport();
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("diagnostic.php", "act=ping&dst="+OBJ("dst").value);
	},
	NextPing:	function()
	{
		var self = this;
		var ajaxObj = GetAjaxObj("ping");

		OBJ("report").innerHTML = "<?echo I18N("h", "Pinging...");?>"

		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			self.GetPingReport();
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("diagnostic.php", "act=ping&dst="+OBJ("dst").value);
	},
	GetPingReport: function(xml)
	{
		if(this.wcount > 2)
		{
			this.wcount = 0;
			if (this.pcount > 2 )
			{
				OBJ("report").innerHTML = "<?echo I18N("h", "Ping timeout.");?>";
				this.ResetPing();
				return;
			}
			this.pcount++;
			this.NextPing();
			return ;
		}

		var self = this;
		var ajaxObj = GetAjaxObj("pingreport");
		var str;
		var index;
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if (xml.Get("/diagnostic/report")==="")
			{
				setTimeout('PAGE.GetPingReport()',1000);
				self.wcount += 1;
			}
			else
			{
				if(self.pcount >= 2)
				{
					
					OBJ("report").innerHTML = self.WrapRetString( xml.Get("/diagnostic/report") );
					self.ResetPing();
				}
				else
				{
					str=xml.Get("/diagnostic/report");
					index = str.indexOf("is alive");
					if(index != -1)
					{
						OBJ("report").innerHTML = self.WrapRetString( xml.Get("/diagnostic/report") );
						self.ResetPing();
					}
					else
					{
						self.wcount += 1;
						setTimeout('PAGE.GetPingReport()',1000);
					}
				}
				
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("diagnostic.php", "act=pingreport");
	},
	ResetPing: function()
	{
		this.wcount = 0;
		OBJ("ping").disabled = false;
	},
	
	StrArray: ["is alive", "No response from"],
	WrapRetString: function(OrgStr)
	{
		var WrappedStr = OrgStr;
		var index;
		
		for(var i=0; i<this.StrArray.length; i+=1)
		{
			index = OrgStr.indexOf(this.StrArray[i]);
			if(index != -1)
			{
				var BgnStr = OrgStr.substring(0, index);
				var EndStr = OrgStr.substring(index+this.StrArray[i].length, OrgStr.length);
				var MidStr = null;
				switch(i)
				{
				case 0:
					MidStr = "<?echo I18N("h", "Connection check successful");?>";
					break;
				case 1:
					MidStr = "<?echo I18N("h", "No response from");?>";
				}
				WrappedStr = BgnStr+MidStr+EndStr;
				break;
			}
		}
		return WrappedStr;
	}
};
</script>
