<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DEVICE.LOG,RUNTIME.LOG",
	OnLoad: function(){
		PAGE.oldType = OBJ("select_log_type").value;
		},
	OnUnload: function() {},
	OnSubmitCallback: function (){},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var logl = PXML.FindModule("DEVICE.LOG");
		if (logl === "")
		{ alert("InitValue ERROR!"); return false; }
		var logLevel = XG(logl+"/device/log/level");
		OBJ("select_log_level").value = XG(logl+"/device/log/level");
		if(PAGE.oldType==null)
		{
			OBJ("select_log_type").value = "sysact";
			this.logType = "sysact";
		}
		else
		{
			if(PAGE.oldType=="sysact")
			{
				OBJ("select_log_type").value = "sysact";
				this.logType = "sysact";
			}
			else if(PAGE.oldType=="drop")
			{
				OBJ("select_log_type").value = "drop";
				this.logType = "drop";
			}
			else if(PAGE.oldType=="attack")
			{
				OBJ("select_log_type").value = "attack";
				this.logType = "attack";
			}
		}
		this.ReNewVars();
		this.DrawLog();
		return true;
	},
	PreSubmit: function()
	{
		var logl = PXML.FindModule("DEVICE.LOG");
		XS(logl+"/device/log/level", OBJ("select_log_level").value);
		
		PXML.IgnoreModule("RUNTIME.LOG");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	logType: "sysact",
	oldType: null,
	pageInx: 0,
	msgItems: 10,
	logPages: 0,
	logItems : 0,

	ReNewVars:function()
	{
		var base = PXML.FindModule("RUNTIME.LOG");
		base += "/runtime/log/" + this.logType + "/entry#";
		this.logItems = XG(base);
		this.logPages = Math.floor(this.logItems/10);
		var isint = this.logItems/10;
		if(isint == this.logPages)
		{
			this.logPages = this.logPages-1;
		}
		this.pageInx = 0;
		return true;
	},
	OnClickToPage:function(to)
	{
		if(to == "-1" && this.pageInx > 0)
		{
			this.pageInx--;
		}
		else if(to == "+1" && this.pageInx < this.logPages)
		{
			this.pageInx++;
		}
		else if(to == "1")
		{
			this.pageInx = 0;
		}
		else if(to == "0")
		{
			this.pageInx = this.logPages;
		}
		else
		{return false;}
		this.DrawLog();
	},
	DrawLog:function()
	{
		var now_page = this.pageInx + 1;
		var total_page = 1 + this.logPages;
		if (this.logPages <= "0")
		{
			OBJ("pp").disabled=true;
			OBJ("np").disabled=true;
			OBJ("fp").disabled=true;
			OBJ("lp").disabled=true;
			/* If total page is less than 0, it means log don't exist.
			Then we don't show current page and status */
			now_page=now_page-1;
		}
		else
		{
			if(this.pageInx == "0")
			{
				OBJ("pp").disabled=true;
				OBJ("np").disabled=false;
				OBJ("fp").disabled=true;
				OBJ("lp").disabled=false;
			}
			if(this.pageInx == this.logPages)
			{
				OBJ("pp").disabled=false;
				OBJ("np").disabled=true;
				OBJ("fp").disabled=false;
				OBJ("lp").disabled=true;
			}
			if(this.pageInx > "0" && this.pageInx < this.logPages)
			{
				OBJ("pp").disabled=false;
				OBJ("np").disabled=false;
				OBJ("fp").disabled=false;
				OBJ("lp").disabled=false;
			}
		}
		var str = "<p>"+ now_page + "/" + total_page + "</p>";
		str += "<table style=\"word-wrap:break-word; word-break:break-all;\" class=\"general\"><tr>";
		str += '<th width="210px">' + "<?echo I18N("h", "Time");?>" + "</th>";
		str += '<th width="540px">' + "<?echo I18N("h", "Message");?>" + "</th>";
		str += "</tr>";
		
		var base = PXML.FindModule("RUNTIME.LOG");
		base += "/runtime/log/" + this.logType + "/entry";
		
		for(var inx=(this.logItems-this.pageInx*this.msgItems); inx > this.logItems-(this.pageInx+1)*this.msgItems && inx > 0; inx--)
		{
			var time = XG(base + ":" + inx + "/time");
			var msg = XG(base + ":" + inx + "/message");
			if(time!="" || msg!="")
			{
				str += "<tr>";
				str += "<td>" + time + "</td>";
				str += "<td class=\"msg\">" + msg + "</td>";
				str += "</tr>";
			}
		}
		str += "</table>";
		BODY.NewWDStyle_refresh();
		OBJ("sLog").innerHTML = str;
	},
	OnClickClear:function()
	{
		OBJ("clear").disabled = true;
		var ajaxObj = GetAjaxObj("clear");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			BODY.OnReload(xml);
			OBJ("clear").disabled = false;
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("log_clear.php", "act=clear&logtype="+this.logType+"&SERVICES="+"RUNTIME.LOG");

	},
	OnChangeLogType:function()
	{
		
		if(OBJ("select_log_type").value != this.logType)
		{
			this.logType = OBJ("select_log_type").value;
		}
		else	OBJ("select_log_type").value = "sysact";
		this.ReNewVars();
		this.DrawLog();
	}
}
</script>
