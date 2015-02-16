<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div table.wd_table tr
{
	height:80px;
}
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "FIREWALL,ACL,ICMP.WAN-1",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.InitFWR()) return false;
		return true;
	},
	
	PreSubmit: function()
	{	
		if (!this.PreFWR()) return null;
		return PXML.doc;
	},

	IsDirty: null,
	Synchronize: function() {},

	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: <? if($layout=="bridge") echo "false"; else echo "true";?>,
	dmz: null,
	passth: null,
	lanip: "<? echo INF_getcurripaddr("LAN-1"); ?>",
	mask: "<? echo INF_getcurrmask("LAN-1"); ?>",

	InitFWR: function()
	{
		var acl = PXML.FindModule("FIREWALL");
		var SPIpath = PXML.FindModule("ACL");
		if (acl === "" || SPIpath=== "") {BODY.ShowAlert("ERROR!"); return false;}
		var spi = XG(SPIpath+"/acl/spi/enable");
		var pingresp = XG(SPIpath+"/acl/pingresp/enable");
		var fw = acl+"/acl/firewall";
		TEMP_RulesCount(fw, "rmd");
		var count = XG(fw+"/count");
		OBJ("EN_SPI").checked = spi=="1" ? true : false;
		OBJ("EN_WANping").checked = pingresp=="1" ? true : false;
		for (var i=1; i<=<?=$FW_MAX_COUNT?>; i+=1)
		{
			var b = fw+"/entry:"+i;
			OBJ("en_"+i).checked = XG(b+"/enable")==="1";
			OBJ("dsc_"+i).value = XG(b+"/description");

			if(XG(b+"/src/inf") !== "")	OBJ("src_inf_"+i).value = XG(b+"/src/inf");
			else				OBJ("src_inf_"+i).value = "";

			var startip = XG(b+"/src/host/start");
			var endip = XG(b+"/src/host/end");
			if(XG(b+"/description")!=="" && startip === "" && endip === "")
			{
				OBJ("src_startip_"+i).value = "*";
				OBJ("src_endip_"+i).value = "";
			}
			else
			{
				OBJ("src_startip_"+i).value = XG(b+"/src/host/start");
				OBJ("src_endip_"+i).value = XG(b+"/src/host/end");
			}

			if(XG(b+"/protocol") !== "")	OBJ("pro_"+i).value = XG(b+"/protocol");
			else				OBJ("pro_"+i).value = "TCP+UDP";
			if(XG(b+"/policy") !== "")	OBJ("action_"+i).value = XG(b+"/policy");
			else				OBJ("action_"+i).value = "ACCEPT";
			if(XG(b+"/dst/inf") !== "")	OBJ("dst_inf_"+i).value = XG(b+"/dst/inf");
			else				OBJ("dst_inf_"+i).value	= "";

			startip = XG(b+"/dst/host/start");
			endip = XG(b+"/dst/host/end");
			if(XG(b+"/description")!=="" && startip === "" && endip === "")
			{
				OBJ("dst_startip_"+i).value = "*";
				OBJ("dst_endip_"+i).value = "";
			}
			else
			{
				OBJ("dst_startip_"+i).value = XG(b+"/dst/host/start");
				OBJ("dst_endip_"+i).value = XG(b+"/dst/host/end");
			}
			
			if(XG(b+"/protocol") === "TCP" || XG(b+"/protocol") === "UDP" || XG(b+"/protocol") === "TCP+UDP")
			{
				if(XG(b+"/description")!=="" && XG(b+"/dst/port/start")==="" && XG("/dst/port/end")==="")
				{
					OBJ("dst_startport_"+i).value	= "*";
					OBJ("dst_endport_"+i).value	= "";
				}
				else
				{
					OBJ("dst_startport_"+i).value	= XG(b+"/dst/port/start");
					OBJ("dst_endport_"+i).value	= XG(b+"/dst/port/end");
				}
			}
			
			<?
			if ($FEATURE_NOSCH!="1")
			{
				echo 'if (XG(b+"/schedule")!=="")	OBJ("sch_"+i).value = XG(b+"/schedule");\n';
				echo 'else				OBJ("sch_"+i).value = "-1";\n';
			}
			?>
			if(i > count)
			{
				OBJ("dst_startport_"+i).value = "";
				OBJ("dst_endport_"+i).value	= "";
				OBJ("dst_startport_"+i).value = "";
				OBJ("dst_endport_"+i).value	= "";
				OBJ("dst_startip_"+i).value = "";
				OBJ("dst_endip_"+i).value = "";
				OBJ("src_startip_"+i).value = "";
				OBJ("src_endip_"+i).value = "";
				OBJ("pro_"+i).value = "TCP+UDP";
				OBJ("action_"+i).value = "";
				OBJ("en_"+i).checked = false;
				OBJ("dsc_"+i).value = "";
			}
			this.OnChangeProt(i);
		}
		return true;
	},	
	PreFWR: function()
	{
		var acl = PXML.FindModule("FIREWALL");
		var SPIpath = PXML.FindModule("ACL");
		var ICMP = PXML.FindModule("ICMP.WAN-1");
		var fw = acl+"/acl/firewall";
		var old_count = XG(fw+"/count");
		var cur_count = 0;

		var network = COMM_IPv4NETWORK(this.lanip, this.mask);
		var maxhost = COMM_IPv4MAXHOST(this.mask);
		
		/* delete the old entries
		 * Notice: Must delte the entries from tail to head */
		while(old_count > 0)
		{
			XD(fw+"/entry:"+old_count);
			old_count -= 1;
		}
		/* update the entries */
		for (var i=1; i<=<?=$FW_MAX_COUNT?>; i+=1)
		{
			/* if the description field is empty, it means to remove this entry,
			 * so skip this entry. */
			if(OBJ("en_"+i).checked && OBJ("src_inf_"+i).value === "")
			{
				BODY.ShowAlert("<?echo I18N('j', 'Please select a source interface !');?>");
				OBJ("src_inf_"+i).focus();
				return null;
			}
			if(OBJ("en_"+i).checked && OBJ("dst_inf_"+i).value === "")
			{
				BODY.ShowAlert("<?echo I18N('j', 'Please select a destination interface !');?>");
				OBJ("dst_inf_"+i).focus();
				return null;
			}
			if (OBJ("en_"+i).checked && OBJ("dsc_"+i).value === "")
			{
				BODY.ShowAlert("<?echo I18N('j', 'The Firewall Name can not be empty !');?>");
				OBJ("dsc_"+i).focus();
				return null;
			}
			if (OBJ("dsc_"+i).value!=="")
			{
				cur_count+=1;
				var b = fw+"/entry:"+cur_count;
				XS(b+"/uid",		"FWL-"+i);
				XS(b+"/enable",		OBJ("en_"+i).checked ? "1" : "0");
				XS(b+"/description",	OBJ("dsc_"+i).value);

				var sinf = OBJ("src_inf_"+i).value;
				XS(b+"/src/inf",	sinf);

				var sipstart = OBJ("src_startip_"+i).value;
				if(sinf !== "LAN-1" && sipstart === "*")
				{
					XS(b+"/src/host/start", "");
					XS(b+"/src/host/end", "");
				}
				else
				{
					var srcip1 = OBJ("src_startip_"+i).value;
					var srcip2 = OBJ("src_endip_"+i).value;
					var srcnet1 = COMM_IPv4NETWORK(srcip1, this.mask);
					var srcnet2 = COMM_IPv4NETWORK(srcip2, this.mask);
					
				
					if (srcip1 === "")
					{
						BODY.ShowAlert("<?echo I18N('j', 'The starting IP address of the source can not be empty.');?>");
						OBJ("src_startip_"+i).focus();
						return null;
					}
					else if( !check_ip_validity(srcip1) )
					{
						BODY.ShowAlert("<?echo I18N('j', 'The starting IP address of the source is not valid.');?>");
						OBJ("src_startip_"+i).focus();
						return null;
					}

					if( srcip2 !== "" && !check_ip_validity(srcip2))
					{
						BODY.ShowAlert("<?echo I18N('j', 'The IP address is not valid.');?>");
						OBJ("src_endip_"+i).focus();
						return null;
					}
					
					if (sinf === "LAN-1")
					{
						if (srcnet1 === "0.0.0.0") 
						{
							BODY.ShowAlert("<?echo I18N('j', 'Incorrect source IP address. The start IP address is invalid.');?>");
							OBJ("src_startip_"+i).focus();
							return null;						
						}
						if (srcip2 !== "" && srcnet2 === "0.0.0.0") 
						{
							BODY.ShowAlert("<?echo I18N('j', 'Incorrect source IP address. The end IP address is invalid.');?>");
							OBJ("src_endip_"+i).focus();
							return null;						
						}
						if(network !== srcnet1)
						{
							BODY.ShowAlert("<?echo I18N('j', 'The source IP should be in the same network as the LAN!');?>");
							OBJ("src_startip_"+i).focus();
							return null;
						}
						if(srcip2 !== "" && network !== srcnet2)
						{
							BODY.ShowAlert("<?echo I18N('j', 'The source IP should be in the same network as the LAN!');?>");
							OBJ("src_endip_"+i).focus();
							return null;
						}
					}
					XS(b+"/src/host/start", OBJ("src_startip_"+i).value);
					XS(b+"/src/host/end", OBJ("src_endip_"+i).value);
				}

				XS(b+"/protocol",	OBJ("pro_"+i).value);
				XS(b+"/policy",		OBJ("action_"+i).value);
			
				var dinf = OBJ("dst_inf_"+i).value;
				XS(b+"/dst/inf",	dinf);

				var dipstart = OBJ("dst_startip_"+i).value;
				if(dinf !== "LAN-1" && dipstart === "*")
				{
					XS(b+"/dst/host/start","");
					XS(b+"/dst/host/end","");
				}
				else
				{
					var dstip1 = OBJ("dst_startip_"+i).value;
					var dstip2 = OBJ("dst_endip_"+i).value;
					var dstnet1 = COMM_IPv4NETWORK(dstip1, this.mask);
					var dstnet2 = COMM_IPv4NETWORK(dstip2, this.mask);
					
					if (dstip1 === "")
					{
						BODY.ShowAlert("<?echo I18N('j', 'The starting IP address of the destination can not be empty.');?>");
						OBJ("dst_startip_"+i).focus();
						return null;
					}else if( !check_ip_validity(dstip1) )
					{
						BODY.ShowAlert("<?echo I18N('j', 'The starting IP address of the destination is not valid.');?>");
						OBJ("dst_startip_"+i).focus();
						return null;
					}
					
					if( dstip2 !== "" && !check_ip_validity(dstip2) )
					{
						BODY.ShowAlert("<?echo I18N('j', 'The ending IP address of the destination is not valid.');?>");
						OBJ("dst_endip_"+i).focus();
						return null;
					}
					
					if (dinf === "LAN-1")
					{
						if (dstnet1 === "0.0.0.0") 
						{
							BODY.ShowAlert("<?echo I18N('j', 'Incorrect destination IP address. The start IP address is invalid.');?>");
							OBJ("dst_startip_"+i).focus();
							return null;						
						}
						if (dstip2 !== "" && dstnet2 === "0.0.0.0") 
						{
							BODY.ShowAlert("<?echo I18N('j', 'Incorrect destination IP address. The end IP address is invalid.');?>");
							OBJ("dst_endip_"+i).focus();
							return null;						
						}
						if(network !== dstnet1 || (dstip2 !== "" && network !== dstnet2))
						{
							BODY.ShowAlert("<?echo I18N('j', 'The destination IP should be in the same network as the LAN!');?>");
							OBJ("dst_startip_"+i).focus();
							return null;
						}
					}
					XS(b+"/dst/host/start", OBJ("dst_startip_"+i).value);
					XS(b+"/dst/host/end", 	OBJ("dst_endip_"+i).value);
				}

				var dstartport = OBJ("dst_startport_"+i).value;
				var dendport = OBJ("dst_endport_"+i).value;
				if(OBJ("pro_"+i).value === "TCP" || OBJ("pro_"+i).value === "UDP" || OBJ("pro_"+i).value === "TCP+UDP")
				{
					if(dstartport === "")
					{
						BODY.ShowAlert("<?echo I18N('j', 'The destination starting port can not be empty.');?>");
						OBJ("dst_startport_"+i).focus();
						return null;
					}
					if(dstartport !== "")
					{
						if(dstartport.charAt(0) === "0")
						{
							BODY.ShowAlert("<?echo I18N('j', 'Invalid Port !');?>");
							OBJ("dst_startport_"+i).focus();
							return null;
						}
					}
					if(dendport !== "")
					{
						if(dendport.charAt(0) === "0")
						{
							BODY.ShowAlert("<?echo I18N('j', 'Invalid Port !');?>");
							OBJ("dst_endport_"+i).focus();
							return null;
						}
					}
					if(dstartport === "*")
					{
						XS(b+"/dst/port/start",	"");
						XS(b+"/dst/port/end",	"");
					}
					else
					{
						XS(b+"/dst/port/start",	dstartport);
						XS(b+"/dst/port/end",	dendport);
					}
				}
				<?
				if ($FEATURE_NOSCH!="1")
				{
					echo 'XS(b+"/schedule",	(OBJ("sch_"+i).value==="-1") ? "" : OBJ("sch_"+i).value);\n';
				}
				?>
				//check the different rules has the same neme or not
				for(var j="1"; j < i ; j++)
				{
					var dsc = OBJ("dsc_"+i).value;
					if(OBJ("dsc_"+i).value === OBJ("dsc_"+j).value)
					{
						BODY.ShowAlert("<? echo I18N('j', 'The Name ');?>"+dsc+"<? echo I18N('j', ' is already used !');?>");
						return null;
					}
				}
				//check same rule exist or not
				for(j="1"; j < i ; j++)
				{
					var dsc = OBJ("dsc_"+i).value;
					if(OBJ("src_inf_"+j).value === OBJ("src_inf_"+i).value
					&& OBJ("src_startip_"+j).value === OBJ("src_startip_"+i).value
					&& OBJ("src_endip_"+j).value === OBJ("src_endip_"+i).value
					&& OBJ("pro_"+j).value === OBJ("pro_"+i).value
					&& OBJ("action_"+j).value === OBJ("action_"+i).value
					&& OBJ("dst_inf_"+j).value === OBJ("dst_inf_"+i).value
					&& OBJ("dst_startip_"+j).value === OBJ("dst_startip_"+i).value
					&& OBJ("dst_endip_"+j).value === OBJ("dst_endip_"+i).value<?
					if ($FEATURE_NOSCH!="1") echo '&& OBJ("sch_"+j).value === OBJ("sch_"+i).value\n';
					?>&& ((OBJ("pro_"+j).value !== "TCP" && OBJ("pro_"+j).value !== "UDP")||
					(OBJ("dst_startport_"+j).value === OBJ("dst_startport_"+i).value
					&& OBJ("dst_endport_"+j).value === OBJ("dst_endport_"+i).value)))
					{
						BODY.ShowAlert("<? echo I18N('j', 'The Rule ');?>"+dsc+"<? echo I18N('j', ' already exists !');?>");
						return null;
					}
				}
			}
		}
		/* we only handle 'count' here, the 'seqno' and 'uid' will handle by setcfg.
		 * so DO NOT modified/generate 'seqno' and 'uid' here. */
		XS(fw+"/count", cur_count);
		XS(SPIpath+"/acl/spi/enable", OBJ("EN_SPI").checked ?"1" :"0");
		XS(SPIpath+"/acl/pingresp/enable", OBJ("EN_WANping").checked ?"1" :"0");
		XS(ICMP+"/inf/icmp/", OBJ("EN_WANping").checked ?"ACCEPT" :"DROP");
		return true;
	},
	OnChangeProt: function(index)
	{
		var prot = OBJ("pro_"+index).value;

		if (prot==="TCP" || prot==="UDP" || prot==="TCP+UDP")
		{
			OBJ("dst_startport_"+index).disabled = false;
			OBJ("dst_endport_"+index).disabled = false;
		}
		else
		{
			OBJ("dst_startport_"+index).disabled = true;
			OBJ("dst_endport_"+index).disabled = true;
		}
	},
	CursorFocus: function(node)
	{
		var i = node.lastIndexOf("entry:");
		if(node.charAt(i+7)==="/") var idx = parseInt(node.charAt(i+6), 10);
		else var idx = parseInt(node.charAt(i+6), 10)*10 + parseInt(node.charAt(i+7), 10);
		var indx = 1;
		var valid_dsc_cnt = 0;		
		for(indx=1; indx <= <?=$FW_MAX_COUNT?>; indx++)
		{
			if(OBJ("dsc_"+indx).value!=="") valid_dsc_cnt++;
			if(valid_dsc_cnt===idx) break;
		}
		if(node.match("inf"))			OBJ("dsc_"+indx).focus();
		else if(node.match("src/host"))	OBJ("src_startip_"+indx).focus();
		else if(node.match("dst/host"))	OBJ("dst_startip_"+indx).focus();
		else if(node.match("dst/port"))	OBJ("dst_startport_"+indx).focus();
	},
	OnDelete: function(idx)
	{
		OBJ("en_"+idx).checked = false;
		OBJ("dsc_"+idx).value = "";
		OBJ("src_inf_"+idx).selectedIndex = 0;
		OBJ("action_"+idx).selectedIndex = 0;
		OBJ("dst_inf_"+idx).selectedIndex = 0;
		OBJ("src_startip_"+idx).value = "";
		OBJ("src_endip_"+idx).value = "";
		OBJ("dst_startip_"+idx).value = "";
		OBJ("dst_endip_"+idx).value = "";
		OBJ("pro_"+idx).selectedIndex = 0;
		OBJ("dst_startport_"+idx).value = "";
		OBJ("dst_startport_"+idx).disabled = false;
		OBJ("dst_endport_"+idx).value = "";
		OBJ("dst_endport_"+idx).disabled = false;
		BODY.NewWDStyle_refresh();
	}
}


function check_ip_validity(ipstr)
{
	var vals = ipstr.split(".");
	if (vals.length!=4) 
		return false;
	
	for (var i=0; i<4; i++)
	{
		if (!TEMP_IsDigit(vals[i]) || vals[i]>255)
			return false;
	}
	return true;
}
</script>
