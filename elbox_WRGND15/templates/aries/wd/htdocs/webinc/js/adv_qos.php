<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "BWC, INET.INF",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		BODY.ShowContent();
		switch (code)
		{
		case "OK":
			Service("REBOOT");
			break;
		case "BUSY":
			BODY.ShowAlert("<?echo I18N("j", "Someone is configuring the device; please try again later.");?>");
			break;
		case "HEDWIG":
			if (result.Get("/hedwig/result")=="FAILED")
			{
				FocusObj(result);
				BODY.ShowAlert(result.Get("/hedwig/message"));
			}
			break;
		case "PIGWIDGEON":
			BODY.ShowAlert(result.Get("/pigwidgeon/message"));
			break;
		}
		return true;
	},
	InitValue: function(xml)
	{		
		PXML.doc = xml;
		bwc = PXML.FindModule("BWC");					
		var inet = PXML.FindModule("INET.INF");
		PXML.IgnoreModule("INET.INF"); 
		
		if (!inet) { alert("InitValue ERROR!"); return false; }
		
		if (this.activewan==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "There is no connection to the Internet! Please check the cables and the Internet settings!");?>");
			return false;
		}
		if (bwc === "")		{ alert("InitValue ERROR!"); return false; }

		this.CheckFieldEmpty();														
		
		OBJ("en_qos").checked = (XG(bwc+"/bwc/entry:1/enable")==="1" && XG(bwc+"/runtime/device/layout")==="router");
		OBJ("en_qos").disabled =(XG(bwc+"/runtime/device/layout")==="bridge");
		TEMP_RulesCount(bwc+"/bwc/bwcf", "rmd"); /*marco*/
		/*If bwc1 is in WAN interface, bwc1 is for uplink
		  If bwc1 is in LAN interface, bwc1 is for downlink*/
		var bwc1		= XG(bwc+"/bwc/entry:1/uid");
		var bwc1infp	= GPBT(inet, "inf", "bwc", bwc1, false);
		var inetinf		= XG(bwc1infp+"/uid");
		
		if(XG(bwc+"/bwc/entry:1/autobandwidth")==="1")
		{
			OBJ("select_upstream").value = 0;
			var band = 0;
			band = COMM_ToNUMBER(XG(bwc+"/runtime/inf/auto_detect_bw"));
			OBJ("uplink_user_define").value = XG(bwc+"/runtime/inf/auto_detect_bw");
			PAGE.auto_uplink_speed = OBJ("uplink_user_define").value;
			OBJ("uplink_user").disabled = true;
			if(band==0 && XG(bwc+"/runtime/inf/BandWidthField")=="FAKE")
			{
				OBJ("en_qos").checked = false;
			}
		}
		else if(XG(bwc+"/bwc/entry:1/user_define")==="1")
		{
			OBJ("select_upstream").value = 1;
			OBJ("uplink_user_define").value = XG(bwc+"/bwc/entry:1/user_define_bw");
			OBJ("uplink_user").disabled = false;						
		}
		else
		{	 
			OBJ("select_upstream").value = XG(bwc+"/bwc/entry:1/bandwidth");		
			OBJ("uplink_user_define").value = "";
			OBJ("uplink_user").disabled = true;
		}
		
		this.cnt_rule_nember=parseInt(XG(bwc+"/bwc/bwcf/count"), 10);
		this.max_rule_nember=parseInt(XG(bwc+"/bwc/bwcf/max"), 10);
		this.temp_rule_list = new Array();
		
		this.EntryList();
		this.EntryFill();
		NewSelect.init();//Generate the new style of select for WD.
		this.OnClickQOSEnable();
		return true;
	},
	CheckFieldEmpty: function()
	{
		var bwc1cnt = parseInt(XG(bwc+"/bwc/bwcf"+"/max"), 10) - parseInt(OBJ("rmd").innerHTML,10);
		
		for(var i=1; i <= bwc1cnt; i++)
		{
			if(OBJ("dsc_"+i).value=="")
			{
				OBJ("b_save").disabled=true;
				break;	
			}
			else if(OBJ("dsc_"+i).value!=="" && OBJ("div_type_"+i).style.display!=="none")
			{
				if(OBJ("type_"+i).value==="app" && OBJ("port_start_"+i).value==="")
				{	
					OBJ("b_save").disabled=true;
					break;		
				}
				else if(OBJ("type_"+i).value==="device" && OBJ("lanip_"+i).value==="" && OBJ("remoteip_"+i).value==="")
				{
					OBJ("b_save").disabled=true;
					break;	
				}
			}
			OBJ("b_save").disabled=false;
		}
		setTimeout('PAGE.CheckFieldEmpty()',1000);	
	},	
	GroupDisabled:function(preid,stat)
	{
		bwc = PXML.FindModule("BWC");					
		var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/entry#"));
		for (var i=1; i <= bwc1cnt; i++)
		{
			OBJ(preid+i).disabled = stat;
		}
	},
	EntryList: function()
	{
		BODY.CleanTable("qos_table");
		
		var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/entry#"));
		for (var i=1; i <= bwc1cnt; i++)
		{
			var name_html = "<input type='text' id='dsc_"+i+"' size=18 maxlength=18>";
			var priority_html = "<select id='pri_"+i+"' class='styled2'><option value='VO'>1-<?echo I18N('h', 'Highest');?></option><option value='VI'>2-<?echo I18N('h', 'High');?></option><option value='BG'>3-<?echo I18N('h', 'Medium');?></option><option value='BE'>4-<?echo I18N('h', 'Low');?></option></select>";
			var type_html = "<br /><div id='div_type_"+i+"'><select  id='type_"+i+"' onchange='PAGE.OnChangeType("+i+")' class='styled2'><option value='app'><?echo I18N('h', 'Application');?></option><option value='device'><?echo I18N('h', 'Device');?></option></select></div>&nbsp;";
			var detail_html = 	"<div><div id='qos_port_"+i+"' style='float:left;'>" +
									"<div>&nbsp;<?echo I18N('h', 'Start Port');?><input style='"+lanugage_StyleGet(PAGE.NowUsingLang , "qos_start_port")+"' type='text' id='port_start_"+i+"' size=11 maxlength=5></div>" +
									"<div>&nbsp;<?echo I18N('h', 'End Port');?><input style='"+lanugage_StyleGet(PAGE.NowUsingLang , "qos_end_port")+"' type='text' id='port_end_"+i+"' size=11 maxlength=5></div>" +
								"</div>" +
								"<div id='qos_ip_"+i+"' style='float:left;display:none;'>" +
									"<div>&nbsp;<?echo I18N('h', 'LAN IP');?><input style='"+lanugage_StyleGet(PAGE.NowUsingLang , "qos_lan_ip")+"' type='text' id='lanip_"+i+"' size=11 maxlength=15></div>" +
									"<div>&nbsp;<?echo I18N('h', 'Remote IP');?><input style='"+lanugage_StyleGet(PAGE.NowUsingLang , "qos_remote_ip")+"' type='text' id='remoteip_"+i+"' size=11 maxlength=15></div>" +
								"</div>" +	
								"<div id='pro_show_"+i+"' style='float:left;' >&nbsp;<?echo I18N('h', 'protocol');?>"+lanugage_StyleGet(PAGE.NowUsingLang , "qos_protocol")+"<select id='pro_"+i+"' class='styled1'><option value='ALL'><?echo I18N('h', 'Both');?></option><option value='TCP'><?echo I18N('h', 'TCP');?></option><option value='UDP'><?echo I18N('h', 'UDP');?></option></select></div>&nbsp;</div>";
			var delete_html = "<a href='javascript:PAGE.OnDelete("+i+");'><img src='pic/img_delete.gif' title='<?echo I18N('h', 'Delete');?>'></a>";
			var data = [name_html, priority_html, type_html, detail_html, delete_html];
			var type = ["","","","",""];
			BODY.InjectTable("qos_table", "qos_rule"+i, data, type);
		}
	},
	EntryFill: function()
	{
		var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/entry#"));
		for (var j=1; j <= bwc1cnt; j++)
		{
			OBJ("dsc_"+j).value	=	XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/description");
			if(XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcqd")==="BWCQD-1")		OBJ("pri_"+j).value	="VO";	
			else if(XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcqd")==="BWCQD-2")	OBJ("pri_"+j).value	="VI";			
			else if(XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcqd")==="BWCQD-3")	OBJ("pri_"+j).value	="BG";
			else if(XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcqd")==="BWCQD-4")	OBJ("pri_"+j).value	="BE";		
			
			var bwcf = XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcf");
			var bwcfp = GPBT(bwc+"/bwc/bwcf", "entry", "uid", bwcf, false);
			OBJ("pro_"+j).value			= XG(bwcfp+"/protocol");
			OBJ("port_start_"+j).value	= XG(bwcfp+"/dst/port/start");
			OBJ("port_end_"+j).value	= XG(bwcfp+"/dst/port/end");
			OBJ("lanip_"+j).value		= XG(bwcfp+"/ipv4/start");
			OBJ("remoteip_"+j).value	= XG(bwcfp+"/dst/ipv4/start");
			if(XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/default")!=="1")
			{
				if(OBJ("port_start_"+j).value!=="") OBJ("type_"+j).value = "app";
				else OBJ("type_"+j).value = "device";
				this.OnChangeType(j);
			}
			else
			{
				OBJ("div_type_"+j).style.display="none";
				OBJ("qos_port_"+j).style.display="none";
				OBJ("qos_ip_"+j).style.display	="none";
				OBJ("pro_show_"+j).style.display		="none";
			}		
		}
	},
	OnDelete: function(i)
	{
		if(OBJ("en_qos").checked)
		{
			XD(bwc+"/bwc/bwcf/entry:"+i);
			XD(bwc+"/bwc/entry:1/rules/entry:"+i);
			XD(bwc+"/bwc/entry:2/rules/entry:"+i);
			this.remaining_rule =parseInt(OBJ("rmd").innerHTML,10)+1;
			OBJ("rmd").innerHTML=this.remaining_rule;
		
			this.SaveTableData(this.max_rule_nember - (this.remaining_rule-1));
		
			this.EntryList();
			this.EntryFill();
			NewSelect.init();//Generate the new style of select for WD.
			this.RestoreTableData(this.max_rule_nember-(this.remaining_rule-1),i);
		}
	},
	SaveTableData: function(rule_number)
	{
		for(var i=1;i <=rule_number;i++)
		{
			if(XG(bwc+"/bwc/entry:1/rules/entry:"+i+"/default")!=="1")
			{
				this.temp_rule_list[i] = {
				name:           OBJ("dsc_"+i).value,
				priority:		OBJ("pri_"+i).value,
				type:			OBJ("type_"+i).value,
				ipv4_start:		OBJ("lanip_"+i).value,
				ipv4_end:	    OBJ("lanip_"+i).value,
				dst_ipv4_start:	OBJ("remoteip_"+i).value,
				dst_ipv4_end:	OBJ("remoteip_"+i).value,
				dst_port_start:	OBJ("port_start_"+i).value,
				dst_port_end:	OBJ("port_end_"+i).value,
				protocol:  		OBJ("pro_"+i).value	};
			}
			else
			{
				this.temp_rule_list[i] = {
				name:           OBJ("dsc_"+i).value,
				priority:		OBJ("pri_"+i).value};
			}
		}
	},
	RestoreTableData: function(rule_number,delete_rule)
	{
		if(delete_rule != 0)
		{
			for(i=delete_rule+1;i <=(rule_number);i++)
			{
				if(XG(bwc+"/bwc/entry:1/rules/entry:"+i+"/default")!=="1")
				{
					this.temp_rule_list[i-1].name           = this.temp_rule_list[i].name;
					this.temp_rule_list[i-1].priority       = this.temp_rule_list[i].priority;
					this.temp_rule_list[i-1].type           = this.temp_rule_list[i].type;
					this.temp_rule_list[i-1].ipv4_start     = this.temp_rule_list[i].ipv4_start;
					this.temp_rule_list[i-1].dst_ipv4_start = this.temp_rule_list[i].dst_ipv4_start;
					this.temp_rule_list[i-1].dst_port_start = this.temp_rule_list[i].dst_port_start;
					this.temp_rule_list[i-1].dst_port_end   = this.temp_rule_list[i].dst_port_end;
					this.temp_rule_list[i-1].protocol       = this.temp_rule_list[i].protocol;
				}
				else
				{
					this.temp_rule_list[i-1].name           = this.temp_rule_list[i].name;
					this.temp_rule_list[i-1].priority       = this.temp_rule_list[i].priority;
				}
			}
			rule_number-=1;
		}
		
		for(i=1;i <=(rule_number);i++)
		{
			if(XG(bwc+"/bwc/entry:1/rules/entry:"+i+"/default")!=="1")
			{
				OBJ("dsc_"+i).value        = this.temp_rule_list[i].name;
				OBJ("pri_"+i).value        = this.temp_rule_list[i].priority;
				OBJ("type_"+i).value       = this.temp_rule_list[i].type;
				PAGE.OnChangeType(i);
				OBJ("lanip_"+i).value      = this.temp_rule_list[i].ipv4_start;
				OBJ("remoteip_"+i).value   = this.temp_rule_list[i].dst_ipv4_start;
				OBJ("port_start_"+i).value = this.temp_rule_list[i].dst_port_start;
				OBJ("port_end_"+i).value   = this.temp_rule_list[i].dst_port_end;
				OBJ("pro_"+i).value        = this.temp_rule_list[i].protocol;
			}
			else
			{
				OBJ("dsc_"+i).value        = this.temp_rule_list[i].name;
				OBJ("pri_"+i).value        = this.temp_rule_list[i].priority;
			}
		}		
	},
	OnAddRule: function()
	{
		var rmd = this.max_rule_nember - this.cnt_rule_nember;
		this.remaining_rule =parseInt(OBJ("rmd").innerHTML,10)-1;

		if(rmd <= 0 || this.remaining_rule<0)
		{
			BODY.ShowAlert("<? echo I18N("j", "The remaining number of rules is 0.");?>");
			return;
		}
		OBJ("rmd").innerHTML=this.remaining_rule;

		this.SaveTableData(this.max_rule_nember-(this.remaining_rule+1));

		var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/entry#"));
		bwc1cnt++;
		/* If it' no setup the data to database, RestoreTableData() will have error. */
		XS(bwc+"/bwc/entry:1/rules/entry:"+bwc1cnt+"/enable", "1");
		this.EntryList();
		this.EntryFill();
		NewSelect.init();//Generate the new style of select for WD.

		this.RestoreTableData(this.max_rule_nember-(this.remaining_rule+1),0);
		
		NewSelect.select();//fixed ITR62213 refresh select bar to show correct value.
	},	
	Submit_and_Reboot: function(i)
	{
		if (confirm("<?echo I18N("j", "After applying updates, the router will reboot. Are you sure?");?>"))
			BODY.OnSubmit();
	},
	PreSubmit: function()
	{
		if (this.activewan==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "There is no connection to the Internet! Please check the cables and the Internet settings!");?>");
			return null;
		}
		
		//var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/entry#"));
		var bwc1cnt = parseInt(XG(bwc+"/bwc/bwcf"+"/max"), 10) - parseInt(OBJ("rmd").innerHTML,10);

		for(var i=1; i <= bwc1cnt; i++)
		{
			/* If one of QoS port is empty, fill it with the other.*/
			if(OBJ("port_start_"+i).value!=="" && OBJ("port_end_"+i).value==="") OBJ("port_end_"+i).value=OBJ("port_start_"+i).value;
			else if(OBJ("port_start_"+i).value==="" && OBJ("port_end_"+i).value!=="") OBJ("port_start_"+i).value=OBJ("port_end_"+i).value;
				
			if(OBJ("dsc_"+i).value=="")
			{
				//BODY.ShowAlert("<?echo I18N("j","The Name can not be blank.");?>");
				//OBJ("dsc_"+i).focus();
				//return null;
			
				j=1;
				for(var k=1; k <= bwc1cnt; k++)
				{
					if(OBJ("dsc_"+k).value == "QoS_Rule"+j)
					{
						j++;
						k=1;
					}							
				}	
				OBJ("dsc_"+i).value = "QoS_Rule"+j;
			}
			
			if(OBJ("dsc_"+i).value!=="" && OBJ("div_type_"+i).style.display!=="none")
			{
				if(OBJ("type_"+i).value==="app" && OBJ("port_start_"+i).value==="")
				{
					BODY.ShowAlert("<?echo I18N("j","The port should not be blank.");?>");
					OBJ("port_start_"+i).focus();
					return null;
				}
				else if(OBJ("type_"+i).value==="device" && OBJ("lanip_"+i).value==="" && OBJ("remoteip_"+i).value==="")
				{
					BODY.ShowAlert("<?echo I18N("j","The IP address can not be blank.");?>");
					OBJ("lanip_"+i).focus();
					return null;					
				}
				if(OBJ("type_"+i).value==="app"){ if (!this.PortCheck(i))	return null;}
				else if(OBJ("type_"+i).value==="device") {if (!this.IPCheck(i)) return null;}				
				
				for(var j=1; j <= bwc1cnt; j++)
				{
					if(OBJ("dsc_"+j).value !== "")
					{
						if(i!==j && OBJ("dsc_"+i).value===OBJ("dsc_"+j).value)
						{
							BODY.ShowAlert("<?echo I18N("j","The 'Name' can not be the same.");?>");
							OBJ("dsc_"+i).focus();
							return null;			
						}
						if(i!==j && OBJ("type_"+i).value==="app")
						{
							if(OBJ("port_start_"+i).value===OBJ("port_start_"+j).value && OBJ("port_end_"+i).value===OBJ("port_end_"+j).value)
							{	
								if(OBJ("pro_"+i).value===OBJ("pro_"+j).value)
								{	
									BODY.ShowAlert("<?echo I18N("j", "The rules can not be the same.");?>");
									OBJ("dsc_"+i).focus();
									return null;						
								}
							}
						}
						else if(i!==j && OBJ("type_"+i).value==="device")
						{
							if(i!==j && OBJ("lanip_"+i).value===OBJ("lanip_"+j).value && OBJ("remoteip_"+i).value===OBJ("remoteip_"+j).value)
							{
								if(OBJ("pro_"+i).value===OBJ("pro_"+j).value)
								{
									BODY.ShowAlert("<?echo I18N("j", "The rules can not be the same.");?>");
									OBJ("dsc_"+i).focus();
									return null;						
								}
							}
						}
					}						
				}	
			}
		}		
		
		XS(bwc+"/bwc/entry:1/enable", OBJ("en_qos").checked?"1":"0");
		XS(bwc+"/bwc/entry:2/enable", OBJ("en_qos").checked?"1":"0");
		
		if(OBJ("select_upstream").value == 0)
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "1");
			XS(bwc+"/bwc/entry:1/user_define", "0");
			XS(bwc+"/bwc/entry:1/bandwidth", "");
			XS(bwc+"/bwc/entry:2/autobandwidth", "1");
			XS(bwc+"/bwc/entry:2/user_define", "0");
			XS(bwc+"/bwc/entry:2/bandwidth", "");					
		}	
		else if(OBJ("select_upstream").value == 1)
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "0");
			XS(bwc+"/bwc/entry:1/user_define", "1");
			XS(bwc+"/bwc/entry:1/bandwidth", "");
			XS(bwc+"/bwc/entry:1/user_define_bw", OBJ("uplink_user_define").value);
			XS(bwc+"/bwc/entry:2/autobandwidth", "0");
			XS(bwc+"/bwc/entry:2/user_define", "1");
			XS(bwc+"/bwc/entry:2/bandwidth", "");
			XS(bwc+"/bwc/entry:2/user_define_bw", OBJ("uplink_user_define").value);						
		}
		else
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "0");
			XS(bwc+"/bwc/entry:1/user_define", "0");
			XS(bwc+"/bwc/entry:1/bandwidth", OBJ("select_upstream").value);
			XS(bwc+"/bwc/entry:2/autobandwidth", "0");
			XS(bwc+"/bwc/entry:2/user_define", "0");
			XS(bwc+"/bwc/entry:2/bandwidth", OBJ("select_upstream").value);				
		}		
				
		/* if the description field is empty, it means to remove this entry,
		 * so skip this entry. */
		var bwcfn = 0;
		var bwcqd = null;
		for (var i=1; i <= bwc1cnt; i++)
		{
			if (OBJ("dsc_"+i).value !== "")
			{
				bwcfn++;
				var port_type = XG(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/port/type");
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/uid",				"BWCF-"+bwcfn);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/protocol", 		OBJ("pro_"+i).value);
    			XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/ipv4/start",		OBJ("lanip_"+i).value);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/ipv4/end",		OBJ("lanip_"+i).value);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/ipv4/start", 	OBJ("remoteip_"+i).value);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/ipv4/end", 	OBJ("remoteip_"+i).value);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/port/type", 	port_type==""?"0":port_type);					
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/port/start", 	OBJ("port_start_"+i).value);
				XS(bwc+"/bwc/bwcf/entry:"+bwcfn+"/dst/port/end", 	OBJ("port_end_"+i).value);
				
				if(OBJ("pri_"+i).value==="VO")		bwcqd="BWCQD-1";
				else if(OBJ("pri_"+i).value==="VI")	bwcqd="BWCQD-2";			
				else if(OBJ("pri_"+i).value==="BG")	bwcqd="BWCQD-3";
				else if(OBJ("pri_"+i).value==="BE")	bwcqd="BWCQD-4";
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcfn+"/enable", "1");
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcfn+"/description", OBJ("dsc_"+i).value);
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcfn+"/bwcqd", bwcqd);
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcfn+"/bwcf", "BWCF-"+bwcfn);
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcfn+"/enable", "1");
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcfn+"/description", OBJ("dsc_"+i).value);
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcfn+"/bwcqd", bwcqd);
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcfn+"/bwcf", "BWCF-"+bwcfn);				
			}	
		}	
		XS(bwc+"/bwc/entry:1/rules/count", bwcfn);
		XS(bwc+"/bwc/entry:2/rules/count", bwcfn);
		XS(bwc+"/bwc/bwcf/count", bwcfn);
		return PXML.doc;
	},
	bwc: null,
	bwcqd1p: null,
	bwcqd2p: null,
	bwcqd3p: null,
	bwcqd4p: null,
	bwc1link: null,
	auto_uplink_speed: null,
	cnt_rule_nember: null,
	max_rule_nember: null,
	remaining_rule: null,
	temp_rule_list: null,
	NowUsingLang: 
	"<? echo $lang; ?>",
	IsDirty: function()
	{
		this.remaining_rule =parseInt(OBJ("rmd").innerHTML,10);
		if(this.cnt_rule_nember != this.max_rule_nember - this.remaining_rule)
			return true; /* The data had be changed */
		else
			return false;
	},
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: function()
	{	
		devmode = XG(bwc+"/runtime/device/layout");
		if(devmode == "bridge") return false;
		return true;
	},			
	activewan: function()
	{
		wan = XG(bwc+"/runtime/device/activewan");		
		return wan;				
	},			
	OnClickQOSEnable: function()
	{
		if (OBJ("en_qos").checked)
		{
			this.GroupDisabled("dsc_",false);
			this.GroupDisabled("pri_",false);
			OBJ("add_rule_button").disabled =false;
			OBJ("select_upstream").disabled =false;
			this.OnChangeQOSUpstream();
		}
		else 
		{
			this.GroupDisabled("dsc_",true);
			this.GroupDisabled("pri_",true);
			OBJ("add_rule_button").disabled =true;
			OBJ("select_upstream").disabled =true;
			OBJ("uplink_user_define").disabled = true;
		}
	},
	OnChangeType: function(i)
	{
		if(OBJ("type_"+i).value == "app")
		{ 
			OBJ("qos_port_"+i).style.display="block";
			OBJ("qos_ip_"+i).style.display="none";
			OBJ("lanip_"+i).value="";
			OBJ("remoteip_"+i).value="";			
		}
		else
		{
			OBJ("qos_port_"+i).style.display="none";
			OBJ("qos_ip_"+i).style.display="block";
			OBJ("port_start_"+i).value="";
			OBJ("port_end_"+i).value="";					
		}		
	},
	OnChangeQOSUpstream: function()
	{
		
		if(OBJ("select_upstream").value == 0)
		{
			OBJ("uplink_user_define").disabled = true;
			OBJ("uplink_user_define").value=PAGE.auto_uplink_speed;
		}
		else if(OBJ("select_upstream").value == 1)
		{
			OBJ("uplink_user_define").value = XG(bwc+"/bwc/entry:1/user_define_bw");
			OBJ("uplink_user").disabled = false;
			OBJ("uplink_user_define").disabled = false;					
		}
		else
		{
			OBJ("uplink_user_define").value = "";
			OBJ("uplink_user_define").disabled = true;
		}
		
	},
	OnRestore: function()
	{
		Service("BWC.RESDEF");
		OBJ("restore_button").disabled=true;
		setTimeout('OBJ("restore_button").disabled=false', 1000);
	},	
	IPCheck: function(i)
	{
		var lan1ip 	= 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0); echo query($inf."/inet/ipv4/ipaddr");?>";
		var lan2ip 	= 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-2", 0); echo query($inf."/inet/ipv4/ipaddr");?>";
		var lan2mask = 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-2", 0); echo query($inf."/inet/ipv4/mask");?>";
		var router_mode = "<?echo query("/runtime/device/router/mode");?>";
		if(OBJ("lanip_"+i).value !== "")
		{
			var LanIP_array	= OBJ("lanip_"+i).value.split(".");
			if (LanIP_array.length!==4 || OBJ("lanip_"+i).value === "0.0.0.0" || LanIP_array[3] === "0" || LanIP_array[3] === "255" )
			{
				alert("<?echo I18N("j", "Incorrect LAN IP address");?>");
				OBJ("lanip_"+i).focus();
				return false;
			}
					
			if(lan1ip === OBJ("lanip_"+i).value || (router_mode === "1W2L" && lan2ip === OBJ("lanip_"+i).value))
			{
				alert("<?echo I18N("j", "The IP address can not be the same as LAN IP address.");?>");
				OBJ("lanip_"+i).focus();
				return false;
			}	
			if(!TEMP_CheckNetworkAddr(OBJ("lanip_"+i).value) && (router_mode === "1W2L" && !TEMP_CheckNetworkAddr(OBJ("lanip_"+i).value, lan2ip, lan2mask)))
			{	
				alert("<?echo I18N("j", "IP address should be in LAN subnet.");?>");
				OBJ("lanip_"+i).focus();
				return false;
			}
		}
		
		if(OBJ("remoteip_"+i).value !== "")
		{
			var DSIP_array	= OBJ("remoteip_"+i).value.split(".");
			if (DSIP_array.length!==4 || OBJ("remoteip_"+i).value === "0.0.0.0" || DSIP_array[3] === "0" || DSIP_array[3] === "255" )
			{
				alert("<?echo I18N("j", "Incorrect Remote IP address");?>");
				OBJ("remoteip_"+i).focus();
				return false;
			}
			for (var j=0; j<4; j++)
			{
				if (!TEMP_IsDigit(DSIP_array[j]) || DSIP_array[j]>255)
				{
					alert("<?echo I18N("j", "Incorrect Remote IP address");?>");
					OBJ("remoteip_"+i).focus();
					return false;
				}
			}	
		}
		return true;
	},
	PortCheck: function(i)
	{
		if(!TEMP_IsDigit(OBJ("port_start_"+i).value) || parseInt(OBJ("port_start_"+i).value, 10) < 1 || parseInt(OBJ("port_start_"+i).value, 10) > 65535)
		{
			alert("<?echo I18N("j", "Invalid port value");?>");
			OBJ("port_start_"+i).focus();
			return false;
		}
		if(!TEMP_IsDigit(OBJ("port_end_"+i).value) || parseInt(OBJ("port_end_"+i).value, 10) < 1 || parseInt(OBJ("port_end_"+i).value, 10) > 65535)
		{
			alert("<?echo I18N("j", "Invalid port value");?>");
			OBJ("port_end_"+i).focus();
			return false;
		}
		if(parseInt(OBJ("port_start_"+i).value, 10) > parseInt(OBJ("port_end_"+i).value, 10))
		{
			alert("<?echo I18N("j", "The end port should be greater than the start port.");?>");
			OBJ("port_start_"+i).focus();
			return false;
		}
			
		return true;	
	}		
}
function Service(svc)
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
/*	var msgArray = ["<?echo I18N('j','QoS settings changed. Rebooting device for new settings to take effect.');?>",
					"<?echo I18N('h','If you changed the IP address of the router, you will need to change the IP address in your browser before accessing the configuration web page again.');?>"];
*/
//According to WD ITR 41349, we remove string "If you changed the IP address of the router..."
	var msgArray = ['<?echo I18N("j","QoS settings changed. Rebooting device for new settings to take effect.");?>'];
	var delay = 10;
	var sec = <?echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");
	if (svc=="FRESET")		url = "http://<?echo $_SERVER["HTTP_HOST"];?>/index.php";
	else if (svc=="REBOOT")	url = "http://<?echo $_SERVER["HTTP_HOST"];?>/index.php";
	else if (svc=="BWC.RESDEF")url = "http://<?echo $_SERVER["HTTP_HOST"];?>/adv_qos.php";
	else					return false;
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK")
			BODY.ShowAlert("Internal ERROR!\nEVENT "+svc+": "+xml.Get("/report/message"));
		else
		{
			if(svc=="BWC.RESDEF") 
				setTimeout('COMM_GetCFG(false, "BWC, INET.INF", function(xml) { PAGE.InitValue(xml);})', 1000);
			else BODY.ShowCountdown(banner, msgArray, sec, url);
		}
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
}
</script>
