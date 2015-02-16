<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DEVICE.ACCOUNT,HTTP.WAN-1",
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
			str += '<div style="text-align:left;"><? echo I18N("h","The port selected for the Remote Management is already being used by another device/application. Go to \"Additional Features\" --> \"Port Forwarding\" to resolve any port conflict before saving the Remote Management setting."); ?></div>';
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
		OBJ("admin_porigin").value = "";
		OBJ("admin_p1").value = "";
		OBJ("admin_p2").value = "";
		PAGE.confirmed = 0;
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
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
			BODY.ShowAlert('<?echo I18N("j", "Someone is configuring the device; please try again later.");?>');
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
		var ver = IE_browser_version();
		if( ver!=false && ver<9 )	OBJ("notes").style.margin="7 0 0 0px";
		PXML.doc = xml;
		if (!this.Initial()) return false;
		return true;
	},
	PreSubmit: function()
	{
		if (!this.SaveXML()) return null;
		PXML.ActiveModule("HTTP.WAN-1");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	confirmed: 0,
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	admin: null,
	device_mode: "<? echo $layout;?>",
	actp: null,
	rcp: null,
	rtemp: null,
	rport: null,
	stunnel: null,
	https_rport: null,
	Initial: function()
	{
		this.actp = PXML.FindModule("DEVICE.ACCOUNT");
		this.rcp = PXML.FindModule("HTTP.WAN-1");
		this.rtemp = PXML.FindModule("HTTP.WAN-1");
		if (!this.actp||!this.rcp)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.actp += "/device/account";
		
		this.stunnel = this.rcp+"/inf/stunnel";
		this.https_rport  = this.rcp+"/inf/https_rport";
		this.rcp += "/inf/web";
		this.rport = XG(this.rcp);
		this.admin = XG(this.actp+"/entry:1/password");

		OBJ("stunnel").checked = COMM_EqSTRING(XG(this.stunnel), "1");
		OBJ("enable_https").checked = !COMM_EqSTRING(XG(this.https_rport), "");
		if(XG(this.rcp)!=""||XG(this.https_rport)!="")
			OBJ("en_remote").checked = true;
		else
			OBJ("en_remote").checked = false;
		
		this.OnClickStunnel();
		//this.OnClickEnableHttps();

		if(PAGE.device_mode != "router")
		{
			OBJ("remote_1").style.display = "none";
			OBJ("remote_2").style.display = "none";
			OBJ("remote_3").style.display = "none";
			OBJ("remote_4").style.display = "none";
			OBJ("HTTPS_server_notice").style.display = "none";
		}

		return true;
	},
	SaveXML: function()
	{		
		if(OBJ("admin_porigin").value!="" || OBJ("admin_p1").value!="" || OBJ("admin_p2").value!="")
		{
			if (!COMM_EqSTRING(OBJ("admin_porigin").value, this.admin))
			{
				BODY.ShowAlert("<?echo I18N("j", "Original password is incorrect.");?>");
				return false;
			}
			if (!COMM_EqSTRING(OBJ("admin_p1").value, OBJ("admin_p2").value))
			{
				BODY.ShowAlert("<?echo I18N("j", "New password and verify password do not match. Please reconfirm new password.");?>");
				return false;
			}
			if (COMM_CheckSpace(OBJ("admin_p1").value))
			{
				BODY.ShowAlert("<?echo I18N("j", "New password can not have space.");?>");
				return false;
			}		
			if (!COMM_EqSTRING(OBJ("admin_p1").value, this.admin))
			{
				XS(this.actp+"/entry:1/password", OBJ("admin_p1").value);
			}
			if((OBJ("admin_p1").value=="password" || OBJ("admin_p1").value=="") && OBJ("en_remote").checked) //The password is default or blank
			{	
				BODY.ShowAlert("<?echo I18N("j", "The admin password cannot be the factory default or none to enable the remote management.");?>");
				return false;	
			}			
		}
		else if((this.admin=="password" || this.admin=="") && OBJ("en_remote").checked) //The password is default or blank
		{
			BODY.ShowAlert("<?echo I18N("j", "The admin password cannot be the factory default or none to enable the remote management.");?>");
			return false;
		}
		if( !OBJ("stunnel").checked && OBJ("enable_https").checked && PAGE.device_mode=="bridge" )
		{
			OBJ("enable_https").checked = false;
		}
		if(!OBJ("stunnel").checked || !OBJ("en_remote").checked)
		{
			if(OBJ("enable_https").checked)
			{
				BODY.ShowAlert("<?echo I18N("j", "The HTTPS for remote management should not be enabled when the remote management or local HTTPS server is disabled.");?>");
				return false;
			}
		}	
		if (OBJ("en_remote").checked)
		{
			if (!TEMP_IsDigit(OBJ("remote_port").value))
			{
				BODY.ShowAlert("<?echo I18N("j", "The remote admin port number is not valid.");?>");
				return false;
			}
			if (OBJ("enable_https").checked)
			{
				XS(this.rcp, "");
				XS(this.https_rport, OBJ("remote_port").value);
			}
			else
			{
				XS(this.rcp, OBJ("remote_port").value);
				XS(this.https_rport, "");
			}
		}
		else
		{
			XS(this.rcp, "");
			XS(this.https_rport, "");
		}
		
		if (OBJ("stunnel").checked)
		{
			XS(this.stunnel, "1");
		}
		else
		{
			XS(this.stunnel, "0");
		}
		
		return true;
	},
	OnClickStunnel: function()
	{
		this.OnClickEnableHttps();
	},
	
	OnClickEnableHttps: function()
	{
		if (OBJ("enable_https").checked)	
		{
			OBJ("remote_port").value=COMM_EqSTRING(XG(this.https_rport), "") ? 8181:XG(this.https_rport);
		}
		else
		{
			OBJ("remote_port").value=COMM_EqSTRING(XG(this.rcp), "") ? 8080:XG(this.rcp);
		}
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
