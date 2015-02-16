<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<style>
div.textinput_ex
{
    clear: both;
    position: relative;
    height: 35px;
    line-height: 35px;
    *line-height: 17px;/*For IE 7*/
}
div.textinput_ex span.name
{
    color: white;
    text-align: left;
    margin-top: 4px;
    font-size: 14px;
}
div.textinput_ex span.value
{
    color: white;
    margin-top: 4px;
    position: absolute;
    left: 310px;
}
div.textinput_ex span.name_c
{
    color: white;
    text-align: left;
    margin-top: 4px;
    font-size: 14px;
    margin-left: 10px;
}
div.textinput_ex span.value_c
{
    color: white;
    margin-top: 4px;
    position: absolute;
    left: 180px;
}
</style>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "BWC, INET.INF, STREAMENGINE, REBOOT",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		switch (code)
		{
		case "OK":
			var banner = "<?echo I18N("h", "Rebooting");?>...";
			var msgArray = ['<?echo I18N("j","QoS settings changed. Rebooting device for new settings to take effect.");?>'];
			var delay = 10;
			var sec = <? echo query("/runtime/device/bootuptime");?> + delay;
			var url = "http://<?echo $_SERVER["HTTP_HOST"];?>/adv_qos.php";			
			BODY.ShowCountdown(banner, msgArray, sec, url);
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
		
		OBJ("en_qos").checked = (XG(bwc+"/bwc/entry:1/enable")==="1" && XG(bwc+"/runtime/device/layout")==="router");
		OBJ("en_qos").disabled =(XG(bwc+"/runtime/device/layout")==="bridge");
		OBJ("auto_classfy").checked = (XG(bwc+"/bwc/entry:1/autoclassification")==="1");
		OBJ("dynamic_fraq").checked = (XG(bwc+"/bwc/entry:1/dynamicfragmentation")==="1");
		OBJ("en_shaping").checked 	= (XG(bwc+"/bwc/entry:1/trafficshaping")==="1");
		TEMP_RulesCount(bwc+"/bwc/bwcf1", "rmd"); /*marco*/
		/*If bwc1 is in WAN interface, bwc1 is for uplink
		  If bwc1 is in LAN interface, bwc1 is for downlink*/
		var bwc1		= XG(bwc+"/bwc/entry:1/uid");
		var bwc1infp	= GPBT(inet, "inf", "bwc", bwc1, false);
		var inetinf		= XG(bwc1infp+"/uid");
		
		OBJ("uplink_user_define").value = "";
		if(XG(bwc+"/bwc/entry:1/autobandwidth")==="1")
		{
			OBJ("select_upstream").value = 0;
			OBJ("uplink_user_define").disabled = true;
			OBJ("uplink_user_define").setAttribute("modified", "ignore");
			OBJ("uplink_user_define").value = XG(bwc+"/bwc/entry:1/bandwidth");
		}
		else if(XG(bwc+"/bwc/entry:1/user_define")==="1")
		{
			OBJ("select_upstream").value = 1;
			OBJ("uplink_user_define").disabled = false;
			OBJ("uplink_user_define").setAttribute("modified", "false");
			OBJ("uplink_user_define").value = XG(bwc+"/bwc/entry:1/bandwidth");
		}
		else
		{
			OBJ("select_upstream").value = XG(bwc+"/bwc/entry:1/bandwidth");						
			OBJ("uplink_user_define").disabled = true;
			OBJ("uplink_user_define").setAttribute("modified", "ignore");
			OBJ("uplink_user_define").value = OBJ("select_upstream").value;
		}
		
		var bwc1cnt = S2I(XG(bwc+"/bwc/entry:1/rules/count"));
		for (var j=1; j<=<?=$QOS_MAX_COUNT?>; j+=1)
		{
			if (XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/enable")==="1")	OBJ("en_"+j).checked=true;
			else	OBJ("en_"+j).checked=false;	
			OBJ("dsc_"+j).value	=	XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/description");		
			
			var bwcqd1 = XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcqd");
			var bwcqd1p = GPBT(bwc+"/bwc/bwcqd1", "entry", "uid", bwcqd1, false);
			if(XG(bwcqd1p+"/priority")!="") COMM_SetSelectValue(OBJ("pri_"+j), XG(bwcqd1p+"/priority"));
			
			var bwcf1 = XG(bwc+"/bwc/entry:1/rules/entry:"+j+"/bwcf");
			var bwcf1p = GPBT(bwc+"/bwc/bwcf1", "entry", "uid", bwcf1, false);
			if (XG(bwcf1p+"/protocol") =="TCP" || XG(bwcf1p+"/protocol") == "UDP") OBJ("pro_"+j).value = XG(bwcf1p+"/protocol");
			else OBJ("pro_"+j).value	= "TCP";
			OBJ("src_startip_"+j).value		= XG(bwcf1p+"/ipv4/start");
			OBJ("src_endip_"+j).value		= XG(bwcf1p+"/ipv4/end");				
			OBJ("dst_startip_"+j).value		= XG(bwcf1p+"/dst/ipv4/start");
			OBJ("dst_endip_"+j).value		= XG(bwcf1p+"/dst/ipv4/end");
			OBJ("src_startport_"+j).value	= XG(bwcf1p+"/port/start");
			OBJ("src_endport_"+j).value		= XG(bwcf1p+"/port/end");				
			OBJ("dst_startport_"+j).value	= XG(bwcf1p+"/dst/port/start");
			OBJ("dst_endport_"+j).value		= XG(bwcf1p+"/dst/port/end");
		}
		this.OnClickQOSEnable();
		return true;
	},
	PreSubmit: function()
	{
		PXML.CheckModule("BWC", null, null, "ignore");
		PXML.IgnoreModule("INET.INF");
		PXML.IgnoreModule("STREAMENGINE");
		//PXML.CheckModule("REBOOT", "ignore", "ignore", null);	

		if (this.activewan==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "There is no connection to the Internet! Please check the cables and the Internet settings!");?>");
			return null;
		}
		
		if(OBJ("select_upstream").value == 1)
		{
			var user_define = OBJ("uplink_user_define").value;
			if(!TEMP_IsDigit(user_define) || parseInt(user_define, 10) < 1)
			{
				alert("<?echo I18N("j", "Invalid Uplink Speed.");?>");
				OBJ("uplink_user_define").focus();
				return null;	
			}
		}
		
		/* If one of the local IP is empty, fill it with the other. The same way about destination IP, local port and remote port.*/    		
		for(var i=1; i <= <?=$QOS_MAX_COUNT?>; i++)
		{
			if(OBJ("src_startip_"+i).value !== "" && OBJ("src_endip_"+i).value ==="") OBJ("src_endip_"+i).value=OBJ("src_startip_"+i).value;
			else if(OBJ("src_startip_"+i).value === "" && OBJ("src_endip_"+i).value !=="") OBJ("src_startip_"+i).value=OBJ("src_endip_"+i).value;
			if(OBJ("dst_startip_"+i).value !== "" && OBJ("dst_endip_"+i).value ==="") OBJ("dst_endip_"+i).value=OBJ("dst_startip_"+i).value;
			else if(OBJ("dst_startip_"+i).value === "" && OBJ("dst_endip_"+i).value !=="") OBJ("dst_startip_"+i).value=OBJ("dst_endip_"+i).value;
			if(OBJ("src_startport_"+i).value !== "" && OBJ("src_endport_"+i).value ==="") OBJ("src_endport_"+i).value=OBJ("src_startport_"+i).value;
			else if(OBJ("src_startport_"+i).value === "" && OBJ("src_endport_"+i).value !=="") OBJ("src_startport_"+i).value=OBJ("src_endport_"+i).value;
			if(OBJ("dst_startport_"+i).value !== "" && OBJ("dst_endport_"+i).value ==="") OBJ("dst_endport_"+i).value=OBJ("dst_startport_"+i).value;
			else if(OBJ("dst_startport_"+i).value === "" && OBJ("dst_endport_"+i).value !=="") OBJ("dst_startport_"+i).value=OBJ("dst_endport_"+i).value;
			if(OBJ("src_startip_"+i).value !== "" && OBJ("src_endip_"+i).value !=="" && OBJ("src_startport_"+i).value !== "" && OBJ("src_endport_"+i).value !=="")
			{
				if(OBJ("dst_startip_"+i).value === "" && OBJ("dst_endip_"+i).value ==="") {OBJ("dst_startip_"+i).value="0.0.0.0";OBJ("dst_endip_"+i).value="255.255.255.255";}
				if(OBJ("dst_startport_"+i).value === "" && OBJ("dst_endport_"+i).value ==="") {OBJ("dst_startport_"+i).value="0";OBJ("dst_endport_"+i).value="65535";}
			}
		}		
		          		
		for(var i=1; i <= <?=$QOS_MAX_COUNT?>; i++)
		{	
			OBJ("dsc_"+i).value = OBJ("dsc_"+i).value.replace(/(^\s*)|(\s*$)/g, "");//trim left space and right space
			if(OBJ("dsc_"+i).value !== "")
			{
				if(PAGE.checkSpace(OBJ("dsc_"+i).value)==1)
				{
					BODY.ShowAlert("<?echo I18N("j","Rule name can not contain space.");?>");
					OBJ("dsc_"+i).focus();
					return null;
				}
				if(OBJ("src_startip_"+i).value === "" || OBJ("src_endip_"+i).value === "" 
					|| OBJ("src_startport_"+i).value === "" || OBJ("src_endport_"+i).value === "")
				{
					alert("<?echo I18N("j", "The rule can not be empty.");?>");
					if(OBJ("src_startip_"+i).value === "")	OBJ("src_startip_"+i).focus();
					else if(OBJ("src_endip_"+i).value === "")	OBJ("src_endip_"+i).focus();
					else if(OBJ("src_startport_"+i).value === "")	OBJ("src_startport_"+i).focus();
					else if(OBJ("src_endport_"+i).value === "")	OBJ("src_endport_"+i).focus();
					return null;
				}		
				
				if (!this.IPRangeCheck(i))	return null;
				if (!this.PortRangeCheck(i))return null;
					
				for(var j=1; j <= <?=$QOS_MAX_COUNT?>; j++)
				{
					OBJ("dsc_"+j).value = OBJ("dsc_"+j).value.replace(/(^\s*)|(\s*$)/g, "");//trim left space and right space
					if(OBJ("dsc_"+j).value !== "")
					{
						if(i!==j && OBJ("dsc_"+i).value===OBJ("dsc_"+j).value)
						{
							BODY.ShowAlert("<?echo I18N("j","The 'Name' can not be the same.");?>");
							OBJ("dsc_"+i).focus();
							return null;			
						}
						if(i!==j && OBJ("src_startip_"+i).value===OBJ("src_startip_"+j).value && OBJ("src_endip_"+i).value===OBJ("src_endip_"+j).value 
							&& OBJ("dst_startip_"+i).value===OBJ("dst_startip_"+j).value && OBJ("dst_endip_"+i).value===OBJ("dst_endip_"+j).value)
						{
							if(OBJ("src_startport_"+i).value===OBJ("src_startport_"+j).value && OBJ("src_endport_"+i).value===OBJ("src_endport_"+j).value 
								&& OBJ("dst_startport_"+i).value===OBJ("dst_startport_"+j).value && OBJ("dst_endport_"+i).value===OBJ("dst_endport_"+j).value)
							{	
								if(OBJ("pro_"+i).value===OBJ("pro_"+j).value)
								{
									BODY.ShowAlert("<?echo i18n("The rules can not be the same.");?>");
									OBJ("src_startip_"+i).focus();
									return null;						
								}
							}	
						}
					}	
				}	
			}
			else
			{
				if(OBJ("en_"+i).checked==true)
				{
					BODY.ShowAlert("<?echo I18N("j", "The rule name can not be empty.");?>");
					OBJ("dsc_"+i).focus();
					return null;
				}
			}
		}
		
		if (!confirm("<?echo I18N("j", "After applying updates, the router will reboot. Are you sure?");?>"))
			return null;
		XS(bwc+"/bwc/entry:1/enable", 				OBJ("en_qos").checked?"1":"0");
		XS(bwc+"/bwc/entry:1/autoclassification",	OBJ("auto_classfy").checked?"1":"0");
		XS(bwc+"/bwc/entry:1/dynamicfragmentation",	OBJ("dynamic_fraq").checked?"1":"0");
		XS(bwc+"/bwc/entry:1/trafficshaping",		OBJ("en_shaping").checked?"1":"0");		
		
		if(OBJ("select_upstream").value == 0)
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "1");
			XS(bwc+"/bwc/entry:1/user_define", "0");		
		}	
		else if(OBJ("select_upstream").value == 1)
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "0");
			XS(bwc+"/bwc/entry:1/user_define", "1");
			XS(bwc+"/bwc/entry:1/bandwidth", OBJ("uplink_user_define").value);					
		}		
		else
		{
			XS(bwc+"/bwc/entry:1/autobandwidth", "0");
			XS(bwc+"/bwc/entry:1/user_define", "0");
			XS(bwc+"/bwc/entry:1/bandwidth", OBJ("select_upstream").value);			
		}	
				
		/* if the description field is empty, it means to remove this entry,
		 * so skip this entry. */
		var bwcf1n = 0;
		var bwcqd1 = null;
		for (var i=1; i <= <?=$QOS_MAX_COUNT?>; i++)
		{
			if (OBJ("dsc_"+i).value !== "")
			{
				bwcf1n++;
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/uid",				"BWCF1-"+bwcf1n);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/protocol", 		OBJ("pro_"+i).value);
    			XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/ipv4/start",		OBJ("src_startip_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/ipv4/end",		OBJ("src_endip_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/dst/ipv4/start", 	OBJ("dst_startip_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/dst/ipv4/end", 	OBJ("dst_endip_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/port/type", 		"0");
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/port/start", 		OBJ("src_startport_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/port/end", 		OBJ("src_endport_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/dst/port/type", 	"0");
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/dst/port/start", 	OBJ("dst_startport_"+i).value);
				XS(bwc+"/bwc/bwcf1/entry:"+bwcf1n+"/dst/port/end", 	OBJ("dst_endport_"+i).value);				
				
				XS(bwc+"/bwc/bwcqd1/entry:"+bwcf1n+"/uid",	"BWCQD1-"+bwcf1n);
				XS(bwc+"/bwc/bwcqd1/entry:"+bwcf1n+"/priority", OBJ("pri_"+i).value);
				
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcf1n+"/enable", OBJ("en_"+i).checked?"1":"0");
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcf1n+"/description", OBJ("dsc_"+i).value);
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcf1n+"/bwcqd", "BWCQD1-"+bwcf1n);
				XS(bwc+"/bwc/entry:1/rules/entry:"+bwcf1n+"/bwcf", "BWCF1-"+bwcf1n);
			}	
		}	
		XS(bwc+"/bwc/entry:1/rules/count", bwcf1n);
		XS(bwc+"/bwc/bwcf1/count", bwcf1n);
		
		return PXML.doc;
	},
	bwc: null,
	IsDirty: null,
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
			OBJ("auto_classfy").disabled	=false;
			OBJ("dynamic_fraq").disabled	=false;
			OBJ("en_shaping").disabled		=false;			
			OBJ("select_upstream").disabled	=false;	
			OBJ("qos_table").disabled		=false;
		}
		else
		{	 
			OBJ("auto_classfy").disabled	=true;
			OBJ("dynamic_fraq").disabled	=true;
			OBJ("en_shaping").disabled		=true;
			OBJ("select_upstream").disabled	=true;		
			OBJ("qos_table").disabled		=true;			
			OBJ("uplink_user_define").disabled = true;	
			OBJ("uplink_user_define").setAttribute("modified", "ignore");			
		}
        for (var j=1; j<=<?=$QOS_MAX_COUNT?>; j+=1)
        {
			if (OBJ("en_qos").checked)
			{
            	OBJ("en_"+j).disabled = false;
            	OBJ("dsc_"+j).disabled = false;
            	OBJ("pri_"+j).disabled = false; 
				OBJ("pro_"+j).disabled = false;
            	OBJ("src_startip_"+j).disabled = false;
            	OBJ("src_endip_"+j).disabled = false;
            	OBJ("dst_startip_"+j).disabled = false;
            	OBJ("dst_endip_"+j).disabled = false;
            	OBJ("src_startport_"+j).disabled = false;
            	OBJ("src_endport_"+j).disabled = false;
            	OBJ("dst_startport_"+j).disabled = false;
            	OBJ("dst_endport_"+j).disabled = false;
			}
			else
			{
                OBJ("en_"+j).disabled = true;
                OBJ("dsc_"+j).disabled = true;
                OBJ("pri_"+j).disabled = true;
                OBJ("pro_"+j).disabled = true;
                OBJ("src_startip_"+j).disabled = true;
                OBJ("src_endip_"+j).disabled = true;
                OBJ("dst_startip_"+j).disabled = true;
                OBJ("dst_endip_"+j).disabled = true;
                OBJ("src_startport_"+j).disabled = true;
                OBJ("src_endport_"+j).disabled = true;
                OBJ("dst_startport_"+j).disabled = true;
                OBJ("dst_endport_"+j).disabled = true;
			}
		}
	},
	OnChangeQOSUpstream: function()
	{
		OBJ("uplink_user_define").value = "";
		if(OBJ("select_upstream").value == 0)
		{
			OBJ("uplink_user_define").disabled = true;
			OBJ("uplink_user_define").setAttribute("modified", "ignore");			
		}
		else if(OBJ("select_upstream").value == 1)
		{
			OBJ("uplink_user_define").disabled = false;			
			OBJ("uplink_user_define").setAttribute("modified", "false");			
			OBJ("uplink_user_define").value = XG(bwc+"/bwc/entry:1/bandwidth");						
		}
		else
		{
			OBJ("uplink_user_define").disabled = true;	
			OBJ("uplink_user_define").setAttribute("modified", "ignore");			
			OBJ("uplink_user_define").value = OBJ("select_upstream").value;
		}
		
	},
	IPRangeCheck: function(i)
	{
		var lan1ip 	= 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", 0); echo query($inf."/inet/ipv4/ipaddr");?>";
		var lan2ip 	= 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-2", 0); echo query($inf."/inet/ipv4/ipaddr");?>";
		var lan2mask = 	"<?$inf = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-2", 0); echo query($inf."/inet/ipv4/mask");?>";
		var router_mode = "<?echo query("/runtime/device/router/mode");?>";
				
		if(OBJ("src_startip_"+i).value !== "")
		{
			if(lan1ip === OBJ("src_startip_"+i).value || (router_mode === "1W2L" && lan2ip === OBJ("src_startip_"+i).value))
			{
				alert("<?echo I18N("j", "The IP address can not be the same as LAN IP address.");?>");
				OBJ("src_startip_"+i).focus();
				return false;
			}	
			if(!TEMP_CheckNetworkAddr(OBJ("src_startip_"+i).value) && (router_mode === "1W2L" && !TEMP_CheckNetworkAddr(OBJ("src_startip_"+i).value, lan2ip, lan2mask)))
			{	
				alert("<?echo I18N("j", "IP address should be in LAN subnet.");?>");
				OBJ("src_startip_"+i).focus();
				return false;
			}
		}
		if(OBJ("src_endip_"+i).value !== "")
		{
			if(lan1ip === OBJ("src_endip_"+i).value || (router_mode === "1W2L" && lan2ip === OBJ("src_endip_"+i).value))
			{
				alert("<?echo I18N("j", "The IP address can not be the same as LAN IP address.");?>");
				OBJ("src_endip_"+i).focus();
				return false;
			} 
			if(!TEMP_CheckNetworkAddr(OBJ("src_endip_"+i).value) && (router_mode === "1W2L" && !TEMP_CheckNetworkAddr(OBJ("src_endip_"+i).value, lan2ip, lan2mask)))
			{	
				alert("<?echo I18N("j", "IP address should be in LAN subnet.");?>");
				OBJ("src_endip_"+i).focus();
				return false;
			}
		}
		if(OBJ("src_startip_"+i).value !== "" && OBJ("src_endip_"+i).value !== "")
		{
			if(COMM_IPv4ADDR2INT(OBJ("src_startip_"+i).value) > COMM_IPv4ADDR2INT(OBJ("src_endip_"+i).value))
			{
				alert("<?echo I18N("j", "The end IP address should be greater than the start address.");?>");
				OBJ("src_startip_"+i).focus();
				return false;
			}
			if(!(TEMP_CheckNetworkAddr(OBJ("src_startip_"+i).value) && TEMP_CheckNetworkAddr(OBJ("src_endip_"+i).value)) &&     
				!(TEMP_CheckNetworkAddr(OBJ("src_startip_"+i).value, lan2ip, lan2mask) && TEMP_CheckNetworkAddr(OBJ("src_endip_"+i).value, lan2ip, lan2mask)))
			{	
				alert("<?echo I18N("j", "The start IP address and the end IP address should be in the same LAN subnet.");?>");
				OBJ("src_startip_"+i).focus();
				return false;
			}
		}
		
		if(OBJ("dst_startip_"+i).value !== "")
		{
			var DSIP_array	= OBJ("dst_startip_"+i).value.split(".");
			if (DSIP_array.length!==4)
			{
				alert("<?echo I18N("j", "Incorrect Dest IP address. The start IP address is invalid.");?>");
				OBJ("dst_startip_"+i).focus();
				return false;
			}
			for (var j=0; j<4; j++)
			{
				if (!TEMP_IsDigit(DSIP_array[j]) || DSIP_array[j]>255)
				{
					alert("<?echo I18N("j", "Incorrect Dest IP address. The start IP address is invalid.");?>");
					OBJ("dst_startip_"+i).focus();
					return false;
				}
			}	
		}
		if(OBJ("dst_endip_"+i).value !== "")
		{
			var DEIP_array	= OBJ("dst_endip_"+i).value.split(".");
			if (DEIP_array.length!==4)
			{
				alert("<?echo I18N("j", "Incorrect Dest IP address. The end IP address is invalid.");?>");
				OBJ("dst_endip_"+i).focus();
				return false;
			}
			for (var j=0; j<4; j++)
			{
				if (!TEMP_IsDigit(DEIP_array[j]) || DEIP_array[j]>255)
				{
					alert("<?echo I18N("j", "Incorrect Dest IP address. The end IP address is invalid.");?>");
					OBJ("dst_endip_"+i).focus();
					return false;
				}
			}
		}
		if(OBJ("dst_startip_"+i).value !== "" && OBJ("dst_endip_"+i).value !== "")
		{
			if(COMM_IPv4ADDR2INT(OBJ("dst_startip_"+i).value) > COMM_IPv4ADDR2INT(OBJ("dst_endip_"+i).value))
			{
				alert("<?echo I18N("j", "The end IP address should be greater than the start address.");?>");
				OBJ("dst_startip_"+i).focus();
				return false;
			}
		}
		
		return true;
	},
	PortRangeCheck: function(i)
	{		
		var port_array = new Array("src_startport_"+i, "src_endport_"+i, "dst_startport_"+i, "dst_endport_"+i);
		for(var m=0; m < port_array.length; m++)
		{
			if(OBJ(port_array[m]).value!=="")
			{
				if(!TEMP_IsDigit(OBJ(port_array[m]).value) || parseInt(OBJ(port_array[m]).value, 10) < 0 || parseInt(OBJ(port_array[m]).value, 10) > 65535)
				{
					alert("<?echo I18N("j", "Invalid port value");?>");
					OBJ(port_array[m]).focus();
					return false;	
				}
			}
		}
		
		if(OBJ("src_startport_"+i).value !== "" && OBJ("src_endport_"+i).value !== "")
		{
			if(parseInt(OBJ("src_startport_"+i).value, 10) > parseInt(OBJ("src_endport_"+i).value, 10))
			{
				alert("<?echo I18N("j", "The end port should be greater than the start port.");?>");
				OBJ("src_startport_"+i).focus();
				return false;
			}
		}		
		if(OBJ("dst_startport_"+i).value !== "" && OBJ("dst_endport_"+i).value !== "")
		{
			if(parseInt(OBJ("dst_startport_"+i).value, 10) > parseInt(OBJ("dst_endport_"+i).value, 10))
			{
				alert("<?echo I18N("j", "The end port should be greater than the start port.");?>");
				OBJ("dst_startport_"+i).focus();
				return false;
			}
		}		
		
		return true;	
	},
	checkSpace: function(str)
	{
		var i=0;
		var result=0;

		for(i=0;i<str.length;i++) 
		{ 
			if(str.charAt(i)==' ')
			{ 
				result=1; 
				break;
			}
		}
		return result;
	}		
}
</script>
