<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "BWC, INET.INF, WISH",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function() {},
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
		
		OBJ("en_qos").checked	=(XG(bwc+"/bwc/entry:2/enable")==="1" && XG(bwc+"/runtime/device/layout")==="router");
		OBJ("en_qos").disabled	=(XG(bwc+"/runtime/device/layout")==="bridge");
		OBJ("wish_http").checked=(XG(bwc+"/bwc/entry:2/wishhttp")==="1");
		OBJ("wish_wmc").checked	=(XG(bwc+"/bwc/entry:2/wishwmc")==="1");
		OBJ("wish_auto").checked=(XG(bwc+"/bwc/entry:2/wishauto")==="1");
		
		TEMP_RulesCount(bwc+"/bwc/bwcf2", "rmd"); /*marco*/
		/*If bwc2 is in WAN interface, bwc2 is for uplink
		  If bwc2 is in LAN interface, bwc2 is for downlink*/
		var bwc2		= XG(bwc+"/bwc/entry:2/uid");
		var bwc2infp	= GPBT(inet, "inf", "bwc", bwc2, false);
		var inetinf		= XG(bwc2infp+"/uid");
		
		var bwc2cnt = S2I(XG(bwc+"/bwc/entry:2/rules/count"));
		for (var j=1; j<=<?=$QOS_MAX_COUNT?>; j+=1)
		{
			if (XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/enable")==="1")	OBJ("en_"+j).checked=true;
			else	OBJ("en_"+j).checked=false;	
			OBJ("dsc_"+j).value	=	XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/description");		
			
			if(XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/bwcqd")==="BWCQD2-1")			OBJ("pri_"+j).value	="VO";	
			else if(XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/bwcqd")==="BWCQD2-2")	OBJ("pri_"+j).value	="VI";			
			else if(XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/bwcqd")==="BWCQD2-3")	OBJ("pri_"+j).value	="BG";
			else if(XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/bwcqd")==="BWCQD2-4")	OBJ("pri_"+j).value	="BE";
			else OBJ("pri_"+j).value = "VO";
	
			var bwcf2 = XG(bwc+"/bwc/entry:2/rules/entry:"+j+"/bwcf");
			var bwcf2p = GPBT(bwc+"/bwc/bwcf2", "entry", "uid", bwcf2, false);
			if (XG(bwcf2p+"/protocol") == "TCP" || XG(bwcf2p+"/protocol") == "UDP") OBJ("pro_"+j).value = XG(bwcf2p+"/protocol");
			else OBJ("pro_"+j).value = "TCP";
			OBJ("src_startip_"+j).value		= XG(bwcf2p+"/ipv4/start");
			OBJ("src_endip_"+j).value		= XG(bwcf2p+"/ipv4/end");				
			OBJ("dst_startip_"+j).value		= XG(bwcf2p+"/dst/ipv4/start");
			OBJ("dst_endip_"+j).value		= XG(bwcf2p+"/dst/ipv4/end");
			OBJ("src_startport_"+j).value	= XG(bwcf2p+"/port/start");
			OBJ("src_endport_"+j).value		= XG(bwcf2p+"/port/end");				
			OBJ("dst_startport_"+j).value	= XG(bwcf2p+"/dst/port/start");
			OBJ("dst_endport_"+j).value		= XG(bwcf2p+"/dst/port/end");
		}
		this.OnClickQOSEnable();
		return true;
	},
	PreSubmit: function()
	{
		PXML.CheckModule("BWC", null, null, "ignore");
		
		if (this.activewan==="")
		{
			BODY.ShowAlert("<?echo I18N("j", "There is no connection to the Internet! Please check the cables and the Internet settings!");?>");
			return null;
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
				
				if(OBJ("src_startip_"+i).value === "" && OBJ("dst_startip_"+i).value ===""
					&& OBJ("src_startport_"+i).value === "" && OBJ("dst_startport_"+i).value === "")
				{
					alert("<?echo I18N("j", "The rule can not be empty.");?>");
					OBJ("dsc_"+i).focus();
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
		
		XS(bwc+"/bwc/entry:2/enable",	OBJ("en_qos").checked?"1":"0");
		XS(bwc+"/bwc/entry:2/wishhttp",	OBJ("wish_http").checked?"1":"0");
		XS(bwc+"/bwc/entry:2/wishwmc",	OBJ("wish_wmc").checked?"1":"0");
		XS(bwc+"/bwc/entry:2/wishauto",	OBJ("wish_auto").checked?"1":"0");		
				
		/* if the description field is empty, it means to remove this entry,
		 * so skip this entry. */
		var bwcf2n = 0;
		var bwcqd2 = null;
		for (var i=1; i <= <?=$QOS_MAX_COUNT?>; i++)
		{
			if (OBJ("dsc_"+i).value !== "")
			{
				bwcf2n++;
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/uid",			"BWCF2-"+bwcf2n);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/protocol", 		OBJ("pro_"+i).value);
    			XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/ipv4/start",	OBJ("src_startip_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/ipv4/end",		OBJ("src_endip_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/dst/ipv4/start",OBJ("dst_startip_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/dst/ipv4/end", 	OBJ("dst_endip_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/port/type", 	"0");
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/port/start", 	OBJ("src_startport_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/port/end", 		OBJ("src_endport_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/dst/port/type", "0");
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/dst/port/start",OBJ("dst_startport_"+i).value);
				XS(bwc+"/bwc/bwcf2/entry:"+bwcf2n+"/dst/port/end", 	OBJ("dst_endport_"+i).value);				
				
				if(OBJ("pri_"+i).value==="VO")		bwcqd2="BWCQD2-1";
				else if(OBJ("pri_"+i).value==="VI")	bwcqd2="BWCQD2-2";			
				else if(OBJ("pri_"+i).value==="BG")	bwcqd2="BWCQD2-3";
				else if(OBJ("pri_"+i).value==="BE")	bwcqd2="BWCQD2-4";
				
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcf2n+"/enable", OBJ("en_"+i).checked?"1":"0");
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcf2n+"/description", OBJ("dsc_"+i).value);
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcf2n+"/bwcqd", bwcqd2);
				XS(bwc+"/bwc/entry:2/rules/entry:"+bwcf2n+"/bwcf", "BWCF2-"+bwcf2n);
			}	
		}	
		XS(bwc+"/bwc/entry:2/rules/count", bwcf2n);
		XS(bwc+"/bwc/bwcf2/count", bwcf2n);
		
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
			OBJ("wish_http").disabled	=false;
			OBJ("wish_wmc").disabled	=false;
			OBJ("wish_auto").disabled	=false;
			OBJ("wish_http").checked	=true;
			OBJ("wish_wmc").checked		=true;
			OBJ("wish_auto").checked	=false;				
			OBJ("qos_table").disabled	=false;
		}
		else
		{	 
			OBJ("wish_http").disabled	=true;
			OBJ("wish_wmc").disabled	=true;
			OBJ("wish_auto").disabled	=true;
			OBJ("wish_http").checked	=false;
			OBJ("wish_wmc").checked		=false;
			OBJ("wish_auto").checked	=false;				
			OBJ("qos_table").disabled	=true;
		}
        for (var j=1; j<=<?=$QOS_MAX_COUNT?>; j+=1)
        {
			if (OBJ("en_qos").checked)
			{
            	OBJ("en_"+j).disabled=false;
            	OBJ("dsc_"+j).disabled=false;
            	OBJ("pri_"+j).disabled=false;
            	OBJ("pro_"+j).disabled=false;
            	OBJ("src_startip_"+j).disabled=false;
            	OBJ("src_endip_"+j).disabled=false;
            	OBJ("dst_startip_"+j).disabled=false;
            	OBJ("dst_endip_"+j).disabled=false;
            	OBJ("src_startport_"+j).disabled=false;
            	OBJ("src_endport_"+j).disabled=false;
            	OBJ("dst_startport_"+j).disabled=false;
            	OBJ("dst_endport_"+j).disabled=false;
			}
			else
			{
                OBJ("en_"+j).disabled=true;
                OBJ("dsc_"+j).disabled=true;
                OBJ("pri_"+j).disabled=true;
                OBJ("pro_"+j).disabled=true;
                OBJ("src_startip_"+j).disabled=true;
                OBJ("src_endip_"+j).disabled=true;
                OBJ("dst_startip_"+j).disabled=true;
                OBJ("dst_endip_"+j).disabled=true;
                OBJ("src_startport_"+j).disabled=true;
                OBJ("src_endport_"+j).disabled=true;
                OBJ("dst_startport_"+j).disabled=true;
                OBJ("dst_endport_"+j).disabled=true;
			}
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
