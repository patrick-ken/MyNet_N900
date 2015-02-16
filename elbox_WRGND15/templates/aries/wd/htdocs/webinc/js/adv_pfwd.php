<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */

div table.wd_table_pf
{
    text-align: left;
    margin-left: 10px;
    margin-right: 10px;
    margin-bottom: 5px;
    border: 1px solid gray;
    border-spacing: 0px;
    color: white;
    background-color: transparent;
}
div table.wd_table_pf tr
{
    margin: 0px;
    padding: 1px;
    border: 0px;
	height:70px;
}
div table.wd_table_pf tr th
{
    text-align: left;
    border: 0px;
    padding: 2px 1px 4px 1px;
}
div table.wd_table_pf tr td
{
    border-width: 0px 0px 0px 1px;
    border-style: solid;
    border-color: gray;
    padding: 1px 2px 1px 2px;
}
div table.wd_table_pf tr td span.table_words
{
    font-size: small;
    line-height:25px;
}
	
div table.wd_table_pf tr td table.wd_inner_table
{
    text-align: left;
    border: 0px solid gray;
    border-spacing: 0px;
    color: white;
    background-color: transparent;
}
div table.wd_table_pf tr td table.wd_inner_table tr
{
	height:24px;
    margin: 0px;
    border: 0px solid gray;
}
div table.wd_table_pf tr td table.wd_inner_table tr th
{
	width: 0px;
	height:0px;
    border:0px solid gray;
}	
div table.wd_table_pf tr td table.wd_inner_table tr td
{
	border:0px solid gray;
}	
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "PFWD.NAT-1,RUNTIME.PORT,INET.LAN-1",
	initialUPtable: function()
	{
		var lan	= PXML.FindModule("INET.LAN-1");
		var inetuid = XG(lan+"/inf/inet");
		var inetp = GPBT(lan+"/inet", "entry", "uid", inetuid, false);
		var RouterIP = XG(inetp+"/ipv4/ipaddr");

		var cnt = XG(PAGE.igdxml+"/runtime/upnpigd/portmapping/entry#");
		var apname="";
		var isupnp="";
		var ipaddr="";
		var EXport="";
		var protocols="";
		var descrip="";
		var mylist = new Array();
		var i=0;
		var j=0;
		var exist=0;
		if(cnt!="" && cnt!="0")
		{
			for(i=1 ; i <= cnt ; i++ )
			{
				exist=-1;
				descrip = XG(PAGE.igdxml+"/runtime/upnpigd/portmapping/entry:"+i+"/description");
				ipaddr = XG(PAGE.igdxml+"/runtime/upnpigd/portmapping/entry:"+i+"/internalclient");
				EXport = XG(PAGE.igdxml+"/runtime/upnpigd/portmapping/entry:"+i+"/externalport");
				protocols = XG(PAGE.igdxml+"/runtime/upnpigd/portmapping/entry:"+i+"/protocol");
				if(descrip!="WD2go" && descrip!="WD2goSSL")
				{
					for(j=0;j<mylist.length;j++)
					{
						if(mylist[j].des==descrip)
						{
							exist=j;
							break;
						}
					}
				}
				if(exist > -1)
				{
					if(protocols=="TCP")
						mylist[j].exTCP=mylist[j].exTCP+ "&nbsp;&nbsp;" +EXport;
					else
						mylist[j].exUDP=mylist[j].exUDP+ "&nbsp;&nbsp;" +EXport;
				}
				else
				{
					if(protocols=="TCP")
						mylist.push( {des: descrip , ip: ipaddr , pro: protocols, exTCP: EXport, exUDP: "" } );
					else
						mylist.push( {des: descrip , ip: ipaddr , pro: protocols, exTCP: "" , exUDP: EXport } );
				}
			}
			for(i=0 ; i < mylist.length ; i++ )
			{
				if(mylist[i].des != "PFW")
				{
					apname = isupnp = ipaddr = EXport = "";
					if(mylist[i].des=="WebMgt")
					{
						apname="<div>&nbsp;<?echo I18N('h', 'Remote Management');?>&nbsp;</div>";
						isupnp="<div>&nbsp;</div>";
						ipaddr="<div>&nbsp;<?echo $_SERVER['HTTP_HOST'];?>&nbsp;</div>";
						EXport="<div>&nbsp;TCP:  "+mylist[i].exTCP+"&nbsp;</div>";
					}
					else
					{
						apname="<div>&nbsp;"+mylist[i].des+"&nbsp;</div>";
						if( mylist[i].des=="WD2goSSL" || mylist[i].des=="WD2go" )
						{
							if(mylist[i].ip==RouterIP)
								isupnp="<div>&nbsp;</div>";
							else
								isupnp="<div>&nbsp;UPnP</div>";
						}
						else
						{
							isupnp="<div>&nbsp;UPnP</div>";
						}
						ipaddr="<div>&nbsp;"+mylist[i].ip+"&nbsp;</div>";
						if(mylist[i].exTCP!="")
							EXport="<div style='width:280px; display:inline-block; word-wrap: break-word; word-break: break-all; border-color:gray black black black;'>&nbsp;TCP:  "+mylist[i].exTCP+"&nbsp;</div>";
						if(mylist[i].exUDP!="")
						{
							if(mylist[i].exTCP!="")
								EXport+="<br>";
							EXport+="<div style='width:280px; display:inline-block; word-wrap: break-word; word-break: break-all; border-color:gray black black black;'>&nbsp;UDP:  "+mylist[i].exUDP+"&nbsp;</div>";
						}
					}
					var data = [apname, isupnp, ipaddr, EXport];
					var type = ["","","",""];
					BODY.InjectTable("usedPORT_table", "row_"+i, data, type);
				}
			}
		}
	},
	ForceSave: function()
	{
		XS(PAGE.rtemp+"/UIoption","2");
		PAGE.IsDirty = true;
		PAGE.confirmed = 1;
		BODY.OnSubmit();
	},
	ShowWarning: function(MSG)
	{
		clearTimeout(BODY.timerId);
		var str = "<img src='pic/warning_mark.png'>";
		
		if(MSG=="MSG1")
		{
			str += "<div style='text-align:left;'><? echo I18N('h','The port is already in use by another application or device and the Port Forwarding rule can not be saved. Go back and make changes on Port Forwarding rules to remove any port conflict.');?></div>";
			str += "<div style='text-align:right;'><input type='button' class='button_blue' id='ok' onclick='BODY.ShowContent();' value='<?echo I18N('h', 'OK');?>'></div>";
		}
		else if(MSG=="MSG2")
		{
			str += "<div style='text-align:left;'><? echo I18N('h','Saving this Port Forwarding rule will take over UPnP ports currently being used by another application. The router will reboot for the new setting to take effect.'); ?></div>";
			str += '<div class="emptyline"></div>';
			str += "<div style='text-align:left;'><? echo I18N('h','Click Save to proceed or Cancel to abort.'); ?></div>";
			str += "<div style='text-align:right;'><input type='button' class='button_black' id='reload' onclick='BODY.ShowContent();' value='<?echo I18N('h', 'Cancel');?>'>&nbsp;&nbsp;<input type='button' class='button_blue' id='onsumit' onclick='PAGE.ForceSave();' value='<?echo I18N('h', 'Save');?>'></div>";			
		}
		OBJ("message").innerHTML = str;
		OBJ("login").style.display	= "none";
		OBJ("menu").style.display	= "none";
		OBJ("content").style.display= "none";
		OBJ("mbox").style.display	= "block";
		OBJ("mbox2").style.display	= "none";
		OBJ("mbox_ex").style.display  = "none";
		BODY.NewWDStyle_init();
	},
	OnLoad: function()
	{
		/* draw the 'Application Name' select */
		var str = "";
		for(var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			str = "";
			str += '<select id="app_'+i+'" class="styled3">';
			for(var j=0; j<this.apps.length; j+=1)
				str += '<option value="'+j+'">'+this.apps[j].name+'</option>';
			str += '</select>';
			OBJ("span_app_"+i).innerHTML = str;
		}
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
		PAGE.confirmed = 0;
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result)
	{
		switch (code)
		{
		case "OK":
			if(PAGE.confirmed==1)
			{
				Service("REBOOT");
			}
			else
			{
				BODY.ShowContent();
				BODY.OnReload();
			}
			break;
		case "BUSY":
			BODY.ShowAlert("<?echo I18N("j", "Someone is configuring the device; please try again later.");?>");
			BODY.ShowContent();
			break;
		case "HEDWIG":
			var ret_msg = result.Get("/hedwig/message");
			var loc = ret_msg.search("Conflict:");
			if(loc != -1)
			{
				var loc = ret_msg.search(":");
				var loc2 = ret_msg.search("-");
				var app_name = ret_msg.substr(loc2+1,ret_msg.length-loc2-1);
				var conflict_port = ret_msg.substr(loc+1,loc2-loc-1);
				if(app_name!="UPNP")
				{
					PAGE.ShowWarning("MSG1");
					return true;
				}
				else
				{
					PAGE.ShowWarning("MSG2");
					return true;
				}
			}
			else
			{
				BODY.ShowAlert(result.Get("/hedwig/message"));
				BODY.ShowContent();
			}
			break;
		case "PIGWIDGEON":
			if (result.Get("/pigwidgeon/message")=="no power")
			{
				BODY.NoPower();
			}
			else
			{
				BODY.ShowAlert(result.Get("/pigwidgeon/message"));
			}
			break;
		}
		return true;
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		PAGE.igdxml = PXML.FindModule("RUNTIME.PORT");
		PXML.IgnoreModule("INET.LAN-1");
		var p = PXML.FindModule("PFWD.NAT-1");
		PAGE.rtemp = PXML.FindModule("PFWD.NAT-1");
		if(p === "" || PAGE.igdxml==="") alert("ERROR!");
		PAGE.initialUPtable();
		p += "/nat/entry/portforward";
		TEMP_RulesCount(p, "rmd");
		var count = XG(p+"/count");
		var netid = COMM_IPv4NETWORK(this.lanip, this.mask);
		for (var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			var b = p+"/entry:"+i;
			var offset = XG(b+"/external/end") - XG(b+"/external/start");
			OBJ("uid_"+i).value = XG(b+"/uid");
			OBJ("en_"+i).checked = XG(b+"/enable")==="1";
			OBJ("dsc_"+i).value = XG(b+"/description");
			OBJ("pub_start_"+i).value = XG(b+"/external/start");
			OBJ("pub_end_"+i).value = XG(b+"/external/end");
			OBJ("pri_start_"+i).value = XG(b+"/internal/start");
			if (OBJ("pri_start_"+i).value!="")
				OBJ("pri_end_"+i).value = S2I(OBJ("pri_start_"+i).value) + offset;
			else
				OBJ("pri_end_"+i).value = "";
			COMM_SetSelectValue(OBJ("pro_"+i), (XG(b+"/protocol")=="")? "TCP+UDP":XG(b+"/protocol"));
			<?
			if ($FEATURE_NOSCH!="1")	echo 'COMM_SetSelectValue(OBJ("sch_"+i), (XG(b+"/schedule")=="")? "-1":XG(b+"/schedule"));\n';
			if ($FEATURE_INBOUNDFILTER=="1")	echo 'COMM_SetSelectValue(OBJ("inbfilter_"+i), (XG(b+"/inbfilter")=="")? "-1":XG(b+"/inbfilter"));\n';
			?>
			var hostid = XG(b+"/internal/hostid");
			if (hostid !== "")	OBJ("ip_"+i).value = COMM_IPv4IPADDR(netid, this.mask, hostid);
			else				OBJ("ip_"+i).value = "";
			OBJ("pc_"+i).value = "";
		}
		return true;
	},
	PreSubmit: function()
	{
		var p = PXML.FindModule("PFWD.NAT-1");
		p += "/nat/entry/portforward";
		var old_count = parseInt(XG(p+"/count"), 10);
		var cur_count = 0;
		var cur_seqno = parseInt(XG(p+"/seqno"), 10);
		
		var rulename="";
		
		
		/* delete the old entries
		 * Notice: Must delte the entries from tail to head */
		while(old_count > 0)
		{
			XD(p+"/entry:"+old_count);
			old_count -= 1;
		}
		/* update the entries */
		for (var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			rulename = "";
			rulename = COMM_EatLeftRightSpace(OBJ("dsc_"+i).value);
			OBJ("dsc_"+i).value = rulename;
			if (OBJ("pub_start_"+i).value!="" && !TEMP_IsDigit(OBJ("pub_start_"+i).value))
			{
				BODY.ShowAlert("<?echo I18N("j", "The enter external start port range is invalid.");?>");
				OBJ("pub_start_"+i).focus();
				return null;
			}
			if (OBJ("pub_end_"+i).value!="" && !TEMP_IsDigit(OBJ("pub_end_"+i).value))
			{
				BODY.ShowAlert("<?echo I18N("j", "The enter external end port range is invalid.");?>");
				OBJ("pub_end_"+i).focus();
				return null;
			}
			if (OBJ("pri_start_"+i).value!="" && !TEMP_IsDigit(OBJ("pri_start_"+i).value))
			{
				BODY.ShowAlert("<?echo I18N("j", "The enter internal start port range is invalid.");?>");
				OBJ("pri_start_"+i).focus();
				return null;
			}
			if (OBJ("ip_"+i).value!="" && !TEMP_CheckNetworkAddr(OBJ("ip_"+i).value, null, null))
			{
				BODY.ShowAlert("<?echo I18N("j", "Invalid host IP address.");?>");
				OBJ("ip_"+i).focus();
				return null;
			}
			
			if(OBJ("dsc_"+i).value!=""||OBJ("en_"+i).checked!="0"||OBJ("ip_"+i).value!=""||OBJ("pri_start_"+i).value!=""||OBJ("pub_end_"+i).value!=""||OBJ("pub_start_"+i).value!="")
			{
				if(OBJ("pub_start_"+i).value=="")
				{
					BODY.ShowAlert("<?echo I18N("j", "Invalid external start port !");?>");
					OBJ("pub_start_"+i).focus();
					return null;
				}
				if(OBJ("dsc_"+i).value=="")
				{
					BODY.ShowAlert("<?echo I18N("j", "The Name cannot be blank. Please enter a name for the port forwarding rule.");?>");
					OBJ("dsc_"+i).focus();
					return null;
				}
			}
			/*if(OBJ("pub_start_"+i).value=="" && OBJ("dsc_"+i).value!="")
			{
				BODY.ShowAlert("<?echo I18N("j", "Invalid external start port !");?>");
				OBJ("pub_start_"+i).focus();
				return null;
			}*/
			/*if(OBJ("pri_start_"+i).value=="" && OBJ("dsc_"+i).value!="")
			{
				BODY.ShowAlert("<?echo I18N("j", "Invalid internal start port !");?>");
				OBJ("pri_start_"+i).focus();
				return null;
			}*/
			/* if the description field is empty, it means to remove this entry,
			 * so skip this entry. */
			if (OBJ("dsc_"+i).value!=="")
			{
				cur_count+=1;
				var b = p+"/entry:"+cur_count;
				XS(b+"/enable",			OBJ("en_"+i).checked ? "1" : "0");
				XS(b+"/uid",			OBJ("uid_"+i).value);
				if (OBJ("uid_"+i).value == "")
				{
					XS(b+"/uid",	"PFWD-"+cur_seqno);
					cur_seqno += 1;
				}
				<?
				if ($FEATURE_NOSCH!="1")	echo 'XS(b+"/schedule",		(OBJ("sch_"+i).value==="-1") ? "" : OBJ("sch_"+i).value);\n';
				if ($FEATURE_INBOUNDFILTER=="1")	echo 'XS(b+"/inbfilter",	(OBJ("inbfilter_"+i).value==="-1") ? "" : OBJ("inbfilter_"+i).value);\n';
				?>
				XS(b+"/description",	OBJ("dsc_"+i).value);
				XS(b+"/protocol",		OBJ("pro_"+i).value);
				XS(b+"/internal/inf",	"LAN-1");
				if (OBJ("ip_"+i).value == "") XS(b+"/internal/hostid", "");
				else XS(b+"/internal/hostid",COMM_IPv4HOST(OBJ("ip_"+i).value, this.mask));
				if(OBJ("pri_start_"+i).value!="")
					XS(b+"/internal/start",	OBJ("pri_start_"+i).value);
				else
					XS(b+"/internal/start",	OBJ("pub_start_"+i).value);
				XS(b+"/external/start",	OBJ("pub_start_"+i).value);
				XS(b+"/external/end",	OBJ("pub_end_"+i).value);
			}
		}
		// Make sure the different rules have different names and external(public) port ranges.
		for (var i=1; i<cur_count; i+=1)
		{
			for (var j=i+1; j<=cur_count; j+=1)
			{
				if(OBJ("en_"+i).checked==true && OBJ("en_"+j).checked==true)
				{
					if(OBJ("pub_start_"+i).value == OBJ("pub_start_"+j).value || OBJ("pub_start_"+i).value == OBJ("pub_end_"+j).value
						||  OBJ("pub_end_"+i).value == OBJ("pub_start_"+j).value || OBJ("pub_end_"+i).value == OBJ("pub_end_"+j).value)
					{
						BODY.ShowAlert('<?echo I18N("j", "The external port ranges of different rules are overlapping.");?>');
						OBJ("pub_start_"+j).focus();
						return null;
					}	
					if(parseInt(OBJ("pub_start_"+i).value, 10) < parseInt(OBJ("pub_end_"+j).value, 10))
					{
						if(parseInt(OBJ("pub_start_"+j).value, 10) < parseInt(OBJ("pub_end_"+i).value, 10))
						{
							BODY.ShowAlert('<?echo I18N("j", "The external port ranges of different rules are overlapping.");?>'); 
							OBJ("pub_start_"+j).focus();
							return null;
						}
					}
					if(OBJ("ip_"+i).value!="" && OBJ("ip_"+j).value!="" && OBJ("pub_start_"+i).value!="" && OBJ("pub_start_"+j).value!="" &&
						OBJ("pri_start_"+i).value!="" && OBJ("pri_start_"+j).value!=""  &&
						OBJ("dsc_"+i).value != "" && OBJ("dsc_"+j).value !="" <?if($FEATURE_NOSCH!="1") echo '&& OBJ("sch_"+i).value!="" && OBJ("sch_"+j).value!=""';?>) 
					{					
						if(OBJ("ip_"+i).value===OBJ("ip_"+j).value && OBJ("pub_start_"+i).value===OBJ("pub_start_"+j).value &&
							OBJ("pri_start_"+i).value===OBJ("pri_start_"+j).value <?if($FEATURE_NOSCH!="1") echo '&& OBJ("sch_"+i).value===OBJ("sch_"+j).value';?>) 
						{
							BODY.ShowAlert("<?echo I18N("j", "The rules can not be the same.");?>");
							OBJ("dsc_"+j).focus();
							return null;
						}
						if(OBJ("dsc_"+i).value === OBJ("dsc_"+j).value && OBJ("dsc_"+j).value !=="") 
						{
							BODY.ShowAlert("<?echo I18N("j", "The different rules can not be the same name.");?>");
							OBJ("dsc_"+j).focus();
							return null;
						}
					}
				}
			}	
		}		
		XS(p+"/count", cur_count);
		XS(p+"/seqno", cur_seqno);
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	confirmed: 0,
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	rgmode: <?if ($layout=="bridge") echo "false"; else echo "true";?>,
	apps: [	{name: "<?echo I18N("h","Application Name");?>",protocol:"TCP+UDP", port:{start:"",	end:""}},
			{name: "FTP",				protocol:"TCP", port:{start:"21",	end:"21"}},
			{name: "HTTP",				protocol:"TCP", port:{start:"80",	end:"80"}},
			{name: "HTTPS",				protocol:"TCP", port:{start:"443",	end:"443"}},
			{name: "DNS",				protocol:"UDP", port:{start:"53",	end:"53"}},
			{name: "SMTP",				protocol:"TCP", port:{start:"25",	end:"25"}},
			{name: "POP3",				protocol:"TCP", port:{start:"110",	end:"110"}},
			{name: "Telnet",			protocol:"TCP", port:{start:"23",	end:"23"}},
			{name: "IPSec",				protocol:"UDP", port:{start:"500",	end:"500"}},
			{name: "PPTP",				protocol:"TCP", port:{start:"1723",	end:"1723"}},			
			{name: "DCS-2000/DCS-5300",	protocol:"TCP", port:{start:"801",	end:"801"}},
			{name: "i2eye",				protocol:"TCP", port:{start:"1720",	end:"1720"}}
		  ],
	lanip: '<? echo INF_getcurripaddr("LAN-1"); ?>',
	mask: '<? echo INF_getcurrmask("LAN-1"); ?>',
	rtemp: null,
	igdxml: null,
	CursorFocus: function(node)
	{
		var i = node.lastIndexOf("entry:");
		var idx = node.charAt(i+6);
		if (node.lastIndexOf("description") != "-1") OBJ("dsc_"+idx).focus();
		if (node.lastIndexOf("internal/hostid") != "-1") OBJ("ip_"+idx).focus();
		if (node.lastIndexOf("external/start") != "-1") OBJ("pub_start_"+idx).focus();
		if (node.lastIndexOf("internal/start") != "-1") OBJ("pri_start_"+idx).focus();
	}
};
function ItoEyeCheck(mode)
{//search table to find if i2eye rule is existed or not
	var idx=1;
	for(idx=1;idx<=32;idx++)
	{
		if(mode==1)//want to find i2eye-1
		{
			if(OBJ("pro_"+idx).value=="TCP" && OBJ("pub_start_"+idx).value=="1720" && OBJ("pri_start_"+idx).value == "1720" &&OBJ("pub_end_"+idx).value=="1720" && OBJ("pri_end_"+idx).value == "1720")
			{
				return idx;//return find i2eye-1 index
			}
		}
		else//want to find i2eye-2
		{
			if(OBJ("pro_"+idx).value=="TCP+UDP" && OBJ("pub_start_"+idx).value=="15328" && OBJ("pri_start_"+idx).value == "15328" &&OBJ("pub_end_"+idx).value=="15333" && OBJ("pri_end_"+idx).value == "15333")
			{
				return idx;//return find i2eye-2 index
			}
		}
	}
	return 0;//return 0 if no found.
}
function EmptyFieldCheck()
{//check if there is still enough room to add new rule or not.
	var idx=1;
	for(idx=1;idx<=32;idx++)
	{
		if( OBJ("pub_start_"+idx).value=="" && OBJ("pub_end_"+idx).value=="" && OBJ("pri_start_"+idx).value=="" && OBJ("dsc_"+idx).value=="")
		{
			return idx;//return first empty index
		}
	}
	return 0;//return 0 if no found.
}
function OnClickEnable(idx)
{
	var igd=0;
	if(OBJ("pro_"+idx).value=="TCP+UDP" && OBJ("pub_start_"+idx).value=="15328" && OBJ("pri_start_"+idx).value == "15328" &&OBJ("pub_end_"+idx).value=="15333" && OBJ("pri_end_"+idx).value == "15333")
	{//this is i2eye-2, find i2eye-1
		igd = ItoEyeCheck(1);
	}
	else if(OBJ("pro_"+idx).value=="TCP" && OBJ("pub_start_"+idx).value=="1720" && OBJ("pri_start_"+idx).value == "1720" &&OBJ("pub_end_"+idx).value=="1720" && OBJ("pri_end_"+idx).value == "1720")
	{//this is i2eye-1, find i2eye-2
		igd = ItoEyeCheck(2);
	}
	if(igd!=0)
	{//if found, consist enable both i2eye rules' attributes.
		OBJ("en_"+igd).checked = OBJ("en_"+idx).checked;
	}
	BODY.NewWDStyle_refresh();
}
function OnClickAppArrow(idx)
{
	var i = OBJ("app_"+idx).value;
	var igd=0;
	var idx2=0;
	var old_dsc = OBJ("dsc_"+idx).value;
	OBJ("dsc_"+idx).value = (i==="0") ? "" : PAGE.apps[i].name;
	
	if(OBJ("dsc_"+idx).value=="i2eye")
	{
		if(old_dsc!="i2eye-2" && old_dsc!="i2eye-1")
		{
			if(ItoEyeCheck(1)==0)
			{
				idx2 = ItoEyeCheck(2);//find i2eye-2
				igd = EmptyFieldCheck();
		
				if(idx2 ==0 && igd != 0)//it means i2eye haven't added and we have enough space to add rule
				{
					OBJ("dsc_"+idx).value="i2eye-1";
					OBJ("pro_"+idx).value = PAGE.apps[i].protocol;
					OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = PAGE.apps[i].port.start;
					OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = PAGE.apps[i].port.end;
					OBJ("app_"+idx).selectedIndex = 0;
					
					OBJ("dsc_"+igd).value="i2eye-2";
					OBJ("pro_"+igd).value = "TCP+UDP";
					OBJ("pub_start_"+igd).value = OBJ("pri_start_"+igd).value = "15328";
					OBJ("pub_end_"+igd).value = OBJ("pri_end_"+igd).value = "15333";
					OBJ("app_"+igd).selectedIndex = 0;
				}
				else if(idx2 !=0)
				{
					OBJ("dsc_"+idx).value="i2eye-1";
					OBJ("pro_"+idx).value = PAGE.apps[i].protocol;
					OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = PAGE.apps[i].port.start;
					OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = PAGE.apps[i].port.end;
					OBJ("app_"+idx).selectedIndex = 0;
				}
				else
				{
					OBJ("dsc_"+idx).value="";//don't add rule.
					OBJ("app_"+idx).selectedIndex = 0;
				}
			}
			else
			{
				if(ItoEyeCheck(2)==0)
				{
					OBJ("dsc_"+idx).value="i2eye-2";
					OBJ("pro_"+idx).value = "TCP+UDP";
					OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = "15328";
					OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = "15333";
					OBJ("app_"+idx).selectedIndex = 0;
				}
				else
				{
					OBJ("dsc_"+idx).value="";//don't add rule.
					OBJ("app_"+idx).selectedIndex = 0;
				}
			}
		}
		else
		{
			if(old_dsc=="i2eye-1")
			{
				if(ItoEyeCheck(2)==0)
				{
					igd = EmptyFieldCheck();
					if(igd!=0)
					{
						OBJ("dsc_"+igd).value="i2eye-2";
						OBJ("pro_"+igd).value = "TCP+UDP";
						OBJ("pub_start_"+igd).value = OBJ("pri_start_"+igd).value = "15328";
						OBJ("pub_end_"+igd).value = OBJ("pri_end_"+igd).value = "15333";
						OBJ("app_"+igd).selectedIndex = 0;
					}
				}
				OBJ("dsc_"+idx).value="i2eye-1";
				OBJ("pro_"+idx).value = PAGE.apps[i].protocol;
				OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = PAGE.apps[i].port.start;
				OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = PAGE.apps[i].port.end;
				OBJ("app_"+idx).selectedIndex = 0;				
			}
			else
			{
				OBJ("dsc_"+idx).value=old_dsc;//don't add rule.
				OBJ("app_"+idx).selectedIndex = 0;
			}
		}
	}
	else
	{
		OBJ("pro_"+idx).value = PAGE.apps[i].protocol;
		OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = PAGE.apps[i].port.start;
		OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = PAGE.apps[i].port.end;
		OBJ("app_"+idx).selectedIndex = 0;
	}
	BODY.NewWDStyle_refresh();
}
function OnClickPCArrow(idx)
{
	var ind=0;
	if(OBJ("pro_"+idx).value=="TCP+UDP" && OBJ("pub_start_"+idx).value ==  "15328" && OBJ("pri_start_"+idx).value ==  "15328" && OBJ("pub_end_"+idx).value == "15333" && OBJ("pri_end_"+idx).value == "15333")
	{//this is i2eye-2, find i2eye-1
		ind = ItoEyeCheck(1);
	}
	else if(OBJ("pro_"+idx).value=="TCP" && OBJ("pub_start_"+idx).value=="1720" && OBJ("pri_start_"+idx).value == "1720" &&OBJ("pub_end_"+idx).value=="1720" && OBJ("pri_end_"+idx).value == "1720")
	{//this is i2eye-1, find i2eye-2
		ind = ItoEyeCheck(2);
	}
	if(ind!=0)
	{//if found, consist enable both i2eye rules' attributes.
		OBJ("ip_"+ind).value = OBJ("pc_"+idx).value;
	}
	OBJ("ip_"+idx).value = OBJ("pc_"+idx).value;
	OBJ("pc_"+idx).selectedIndex = 0;
}

function CheckPort(port)
{
	var vals = port.toString().split("-");
	switch (vals.length)
	{
	case 1:
		if (!TEMP_IsDigit(vals))
			return false;
		break;
	case 2:
		if (!TEMP_IsDigit(vals[0])||!TEMP_IsDigit(vals[1]))
			return false;
		break;
	default:
		return false;
	}
	return true;
}
function check_valid_port(list)
{
	var port = list.split(",");

	if (port.length > 1)
	{
		for (var i=0; i<port.length; i++)
		{
			if (!CheckPort(port[i]))
				return false;
		}
		return true;
	}
	else
	{
		return CheckPort(port);
	}
}
function port_using_check(PROTO,PORTST,PORTEN,WhoAmI)
{
	PORTST = COMM_ToNUMBER(PORTST);
	PORTEN = COMM_ToNUMBER(PORTEN);
	var XMLptr = PXML.FindModule("RUNTIME.PORT");
	var XMLrm = XMLptr + "/inf";
	var XMLupnp = XMLptr + "/runtime/upnpigd/portmapping";
	var XMLorion = XMLptr + "/runtime/orion";
	var XMLpfwd = XMLptr + "/nat/entry/portforward";
	var cnt=0;
	var UPNPenable = XG(XMLrm+"/upnp/count");
	if(WhoAmI=="PortForward")
	{/* check remote management */
		if(XG(XMLrm+"/web")!="")
		{
			if(PROTO=="TCP"||PROTO=="TCP+UDP")
			{
				if( COMM_ToNUMBER(XG(XMLrm+"/web"))>=PORTST && COMM_ToNUMBER(XG(XMLrm+"/web"))<=PORTEN )
				{
					if(confirm("<? echo I18N("j","Conflict with Remote Management service HTTP port"); ?>"+": "+XG(XMLrm+"/web")+". "+"<? echo I18N("j","This may cause unpredictable problem. Are you sure you want to override?"); ?>"))
					{
						PAGE.confirmed = 1;
						XS(XMLptr+"/runtime/upnpigd/conflict","1");
						return 0;
					}
					else
					{
						PAGE.confirmed = 0;
						return -1;
					}
				}
			}
		}
		else if(XG(XMLrm+"/https_rport")!="")
		{
			if(PROTO=="TCP"||PROTO=="TCP+UDP")
			{
				if( COMM_ToNUMBER(XG(XMLrm+"/https_rport"))>=PORTST && COMM_ToNUMBER(XG(XMLrm+"/https_rport"))<=PORTEN )
				{
					if(confirm("<? echo I18N("j","Conflict with Remote Management service HTTPS port"); ?>"+": "+XG(XMLrm+"/https_rport")+". "+"<? echo I18N("j","This may cause unpredictable problem. Are you sure you want to override?"); ?>"))
					{
						PAGE.confirmed = 1;
						XS(XMLptr+"/runtime/upnpigd/conflict","1");
						return 0;
					}
					else
					{
						PAGE.confirmed = 0;
						return -1;
					}
				}
			}
		}
	}
	else if(WhoAmI=="Remote")
	{/* check Port Forwarding */
		cnt = XG(XMLpfwd+"/entry#");
		if(cnt!="")
		{
			for(var z=1; z<=cnt; z++)
			{
				var porS = COMM_ToNUMBER(XG(XMLpfwd+"/entry:"+z+"/external/start"));
				var porE = COMM_ToNUMBER(XG(XMLpfwd+"/entry:"+z+"/external/end"));
				var pro = XG(XMLpfwd+"/entry:"+z+"/protocol");
				
				if(pro=="TCP+UDP")
				{
					if(PORTST>=porS && PORTST<=porE)//because start == end
					{
						if(confirm("<? echo I18N("j","Port:");?>" + " " + PORTST + " " + "<? echo I18N("j","Conflict with Port Forwarding service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
						{
							PAGE.confirmed = 1;
							XS(XMLptr+"/runtime/upnpigd/conflict","1");
							return 0;
						}
						else
						{
							PAGE.confirmed = 0;
							return -1;
						}
					}
				}
				else
				{
					if(PROTO==pro)
					{
						if(PORTST>=porS && PORTST<=porE)//because start == end
						{
							if(confirm("<? echo I18N("j","Port:");?>" + " " + PORTST + " " + "<? echo I18N("j","Conflict with Port Forwarding service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
							{
								PAGE.confirmed = 1;
								XS(XMLptr+"/runtime/upnpigd/conflict","1");
								return 0;
							}
							else
							{
								PAGE.confirmed = 0;
								return -1;
							}
						}
					}
				}	
			}
		}
	}
	/* 80, 443 ports can not use */
	if(PROTO=="TCP"||PROTO=="TCP+UDP")
	{
		if( 80>=PORTST && 80<=PORTEN )
		{
			if(confirm("<? echo I18N("j","Port 80 Conflict with another service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
			{
				PAGE.confirmed = 1;
				XS(XMLptr+"/runtime/upnpigd/conflict","1");
				return 0;
			}
			else
			{
				PAGE.confirmed = 0;
				return -1;
			}
		}
		if( 443>=PORTST && 443<=PORTEN )
		{
			if(confirm("<? echo I18N("j","Port 443 Conflict with another service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
			{
				PAGE.confirmed = 1;
				XS(XMLptr+"/runtime/upnpigd/conflict","1");
				return 0;
			}
			else
			{
				PAGE.confirmed = 0;
				return -1;
			}
		}
	}
	/* check orion */
	cnt = XG(XMLorion+"/entry#");
	if(cnt!="" && $FEATURE_MODEL_NAME=="storage")
	{
		for(var j=1; j<=cnt; j++)
		{
			var por = COMM_ToNUMBER(XG(XMLorion+"/entry:"+j+"/external_port"));
			if(PROTO=="TCP+UDP")
			{
				if(por>=PORTST && por<=PORTEN)
				{
			        if(confirm("<? echo I18N("j","Port:");?>" + " " + por + " " + "<? echo I18N("j","Conflict with WD service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
			        {
			        	PAGE.confirmed = 1;
			        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
			            return 0;
			        }
			        else
			        {
			        	PAGE.confirmed = 0;
			        	return -1;
			        }
				}
			}
			else
			{
				var pro = XG(XMLorion+"/entry:"+j+"/protocol");
				if(PROTO==pro)
				{
					if(por>=PORTST && por<=PORTEN)
					{
				        if(confirm("<? echo I18N("j","Port:");?>" + " " + por + " " + "<? echo I18N("j","Conflict with WD service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
				        {
				        	PAGE.confirmed = 1;
				        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
				            return 0;
				        }
				        else
				        {
				        	PAGE.confirmed = 0;
				        	return -1;
				        }
					}
				}
			}	
		}
	}
	/* check upnp igd */
	if(UPNPenable>=0)
	{
		cnt=0;
		cnt = XG(XMLupnp+"/entry#");
		if(cnt!="")
		{
			for (var f=1; f<=cnt; f++)
			{
				var ena = XG(XMLupnp+"/entry:"+f+"/enable");
				if(ena=="1")
				{
					var por = XG(XMLupnp+"/entry:"+f+"/externalport");
					var porS=0;
					var porE=0;
					var loc = por.search("-");
					if(loc==-1)
					{
					 	porS=COMM_ToNUMBER(por);
					 	porE=porS;
					}
					else
					{
					 	porS=COMM_ToNUMBER(por.substr(0,loc));
					 	porE=COMM_ToNUMBER(por.substr(loc+1,por.length-loc-1));
					}
					var descrip = XG(XMLupnp+"/entry:"+f+"/description");
					var lookip = XG(XMLupnp+"/entry:"+f+"/internalclient");
					if(PROTO=="TCP+UDP")
					{
					 	if((descrip!="PFW"&&descrip!="WebMgt") || ( WhoAmI=="PortForward" && lookip=="127.0.0.1" && descrip=="WebMgt") || ( WhoAmI=="Remote" && lookip!="127.0.0.1" && descrip=="PFW") )
					 	{
			 				if(porS>=PORTST && porS<=PORTEN)
						 	{
						        if(confirm("<? echo I18N("j","Port:");?>" + " " + porS + " " + "<? echo I18N("j","Conflict with UPnP service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
						        {
						        	PAGE.confirmed = 1;
						        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
						            return 0;
						        }
						        else
						        {
						        	PAGE.confirmed = 0;
						        	return -1;
						        }
						 	}
						 	else if(porE>=PORTST && porE<=PORTEN)
						 	{
						        if(confirm("<? echo I18N("j","Port:");?>" + " " + porE + " " + "<? echo I18N("j","Conflict with UPnP service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
						        {
						        	PAGE.confirmed = 1;
						        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
						            return 0;
						        }
						        else
						        {
						        	PAGE.confirmed = 0;
						        	return -1;
						        }
						 	}
					 	}
					}
					else
					{
					 	var pro = XG(XMLupnp+"/entry:"+f+"/protocol");
					 	if(PROTO==pro)
					 	{
						 	if((descrip!="PFW"&&descrip!="WebMgt") || ( WhoAmI=="PortForward" && lookip=="127.0.0.1" && descrip=="WebMgt") || ( WhoAmI=="Remote" && lookip!="127.0.0.1" && descrip=="PFW") )
						 	{
				 				if(porS>=PORTST && porS<=PORTEN)
							 	{
							        if(confirm("<? echo I18N("j","Port:");?>" + " " + porS + " " + "<? echo I18N("j","Conflict with UPnP service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
							        {
							        	PAGE.confirmed = 1;
							        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
							            return 0;
							        }
							        else
							        {
							        	PAGE.confirmed = 0;
							        	return -1;
							        }
							 	}
							 	else if(porE>=PORTST && porE<=PORTEN)
							 	{
							        if(confirm("<? echo I18N("j","Port:");?>" + " " + porE + " " + "<? echo I18N("j","Conflict with UPnP service port. This may cause unpredictable problem. Are you sure you want to override?");?>"))
							        {
							        	PAGE.confirmed = 1;
							        	XS(XMLptr+"/runtime/upnpigd/conflict","1");
							            return 0;
							        }
							        else
							        {
							        	PAGE.confirmed = 0;
							        	return -1;
							        }
							 	}
						 	}
					 	}
					}
				}				 		
			}
		}
	}
}
function Service(svc)
{	
	var banner = "<?echo I18N('h', 'Rebooting');?>...";
	var msgArray = ["<?echo I18N('h', 'If you changed the IP address of the router, you may need to renew the IP address of your device before accessing the router web page again.');?>"];
	var delay = 10;
	if("<? echo $FEATURE_MODEL_NAME; ?>" == "storage") delay = delay + 20;
	var sec = <? echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");

	if (svc=="FRESET")		url = "http://192.168.1.1/index.php";
	else if (svc=="REBOOT")	url = "http://<?echo $_SERVER['HTTP_HOST'];?>/index.php";
	else					return false;
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK")
			BODY.ShowAlert("Internal ERROR!\nEVENT "+svc+": "+xml.Get("/report/message"));
		else
			BODY.ShowCountdown(banner, msgArray, sec, url);
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
} 
</script>
