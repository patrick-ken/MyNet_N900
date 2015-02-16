<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<?
	$inet = INF_getinfinfo("LAN-1", "inet");
	$ipaddr = INET_getinetinfo($inet, "ipv4/ipaddr");
?>
<script type="text/javascript">

function Page() {}
Page.prototype =
{
	services: "INET.WAN-1,RUNTIME.INF.WAN-1,RUNTIME.PHYINF,RUNTIME.TIME",	
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function () {},
	InitValue: function(xml)
	{
		LanguageToChangeCSS('<? echo $lang;?>');//Change Button's CSS according to what Language.

		PXML.doc = xml;
		this.ShowWANStatus(xml);
		return true;
	},
	PreSubmit: function()
	{		
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	waninetp: null,
	rwaninetp: null,
	rwanphyp: null,
	getwanstatus: null,
	
	ShowWANStatus: function (xml)
	{
		PXML.doc = xml;
		
		var wan	= PXML.FindModule("INET.WAN-1");
		var rwan = PXML.FindModule("RUNTIME.INF.WAN-1");
		var rphy = PXML.FindModule("RUNTIME.PHYINF");
        var timep = PXML.FindModule("RUNTIME.TIME");		
        var alertpath = PXML.FindModule("ALERTMSG");
		var waninetuid = XG(wan+"/inf/inet");
		var wanphyuid = XG(wan+"/inf/phyinf");
		this.waninetp = GPBT(wan+"/inet", "entry", "uid", waninetuid, false);
		this.rwaninetp = GPBT(rwan+"/runtime/inf", "inet", "uid", waninetuid, false);      
		this.rwanphyp = GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);     
		var str_networkstatus = str_Disconnected = "<?echo I18N("h", "Not Connected");?>";
		var str_Connected = "<?echo I18N("h", "Connected");?>";
		var str_Conflicting = "<?echo I18N("h", "Conflicting");?>";
		var wan_uptime = S2I(XG(this.rwaninetp+"/uptime"));
		var system_uptime = S2I(XG(timep+"/runtime/device/uptimes"));
		var wan_delta_uptime = (system_uptime-wan_uptime);
		var wan_uptime_sec = 0;
		var wan_uptime_min = 0;
		var wan_uptime_hour = 0;
		var wan_uptime_day = 0;
		var str_wanipaddr = str_wangateway = str_wanDNSserver = str_wanDNSserver2 = str_wannetmask ="0.0.0.0";
		var str_name_wanipaddr = "<?echo I18N("h", "IP Address");?>";
		var str_name_wangateway = "<?echo I18N("h", "Default Gateway");?>";

        var wancable_status=0;
		var wan_network_status=0;
		if ((!this.waninetp) && (AUTH.AuthorizedGroup > -1))
		{
			BODY.ShowAlert("InitWAN() ERROR!!!");
			return false;
		}

        if((XG(this.rwanphyp+"/linkstatus")!="0") && (XG(this.rwanphyp+"/linkstatus")!=""))
        {
			wancable_status=1;
		}
		if(XG(alertpath+"/alertmsg/WAN_network_status/alert")=="1") wan_network_status=1;
		OBJ("st_wancable").innerHTML  = wancable_status==1 ? str_Connected:str_Disconnected;

		if (XG  (this.waninetp+"/addrtype") == "ipv4")
		{
			if(XG  ( this.waninetp+"/ipv4/static")== "1")
			{
	    		OBJ("st_wantype").innerHTML  = "Static IP";
	    		str_networkstatus  = wan_network_status== 1 ? str_Connected:str_Disconnected;
				
				var wan_static_uptime = S2I(XG(timep+"/runtime/device/wan_static_uptime"));
				wan_delta_uptime = (system_uptime-wan_static_uptime);
			}
			else
			{
		    	OBJ("st_wantype").innerHTML  = "DHCP Client";
				if ((XG  (this.rwaninetp+"/ipv4/valid")== "1")&& (wan_network_status==1))
				{
					str_networkstatus = str_Connected;
				}
				else if (XG  (this.rwaninetp+"/ipv4/conflict")== "1")
				{
					wan_network_status=0;
					str_networkstatus = str_Conflicting;
				}
				OBJ("st_wan_dhcp_action").style.display = "block";
        
			}
		}
		else if (XG  (this.waninetp+"/addrtype") == "ppp4" || XG(this.waninetp+"/addrtype") == "ppp10")
		{
		    
			if(XG  ( this.waninetp+"/ppp4/over")== "eth")
			{
				OBJ("st_wantype").innerHTML  = "PPPoE";
			}
			else if(XG  ( this.waninetp+"/ppp4/over")== "pptp")
			{
				OBJ("st_wantype").innerHTML  = "PPTP";
			}
			else if(XG  ( this.waninetp+"/ppp4/over")== "l2tp")
			{
			        OBJ("st_wantype").innerHTML  = "L2TP";
			}
			else
			    {OBJ("st_wantype").innerHTML  = "Unknow WAN type";}
			
			var connStat = XG(rwan+"/runtime/inf/pppd/status");    
			    
		    switch (connStat)
	            {
	                case "connected":
            		if (wan_network_status == 1)
		            {
		                str_networkstatus=str_Connected;
                        OBJ("st_wan_ppp_connect").disabled = true;
                        OBJ("st_wan_ppp_disconnect").disabled = false;
		            }
		            else
		            {
		                str_networkstatus=str_Disconnected;
                        OBJ("st_wan_ppp_connect").disabled = false;
                        OBJ("st_wan_ppp_disconnect").disabled = true;
		            }
		            break;
		            case "":
	                case "disconnected":
                    {
		                str_networkstatus=str_Disconnected;
                        OBJ("st_wan_ppp_connect").disabled = false;
                        OBJ("st_wan_ppp_disconnect").disabled = true;
                        wan_network_status=0;
		            }
		            break;
	                case "on demand":
		                str_networkstatus="<?echo I18N("h", "Idle");?>";
                        OBJ("st_wan_ppp_connect").disabled = false;
                        OBJ("st_wan_ppp_disconnect").disabled = true;
                        wan_network_status=0;
		            break;
	                default:
		                str_networkstatus = "<?echo I18N("h", "Busy ...");?>";
                        OBJ("st_wan_ppp_connect").disabled = false;
                        OBJ("st_wan_ppp_disconnect").disabled = false;
		                break;
	                }	
    
			 str_name_wanipaddr = "<?echo I18N("h", "Local address");?>";
		     str_name_wangateway = "<?echo I18N("h", "Peer address");?>";
			 var ctmode = XG(this.waninetp+"/ppp4/dialup/mode");
			 if (ctmode!="auto")
			 {	
		     	OBJ("st_wan_ppp_action").style.display = "block";
			 }
		     
		     if(XG(wan+"/inf/schedule")!="" && this.rwaninetp==null)
		     {
		     	OBJ("st_wan_ppp_connect").disabled = true;
                OBJ("st_wan_ppp_disconnect").disabled = true;
		     }
		     
		     //patch for pptp check if in schedule 
		     var rwan_1 	 = GPBT(rwan+"/runtime", "inf", "uid", "WAN-1", false);     
			 var ppp_phyinf  = XG(rwan_1+"/phyinf");
			 var ppp_runtime = GPBT(rphy+"/runtime", "phyinf", "uid", ppp_phyinf, false);
		     if(XG(wan+"/inf/schedule")!="" && ppp_runtime==null)
		     {
		     	this.pptp_NOT_IN_schedule = true;
		     }
		}
		
		if ((XG(this.rwaninetp+"/addrtype") == "ipv4"))
		{
		    str_wanipaddr = XG  (this.rwaninetp+"/ipv4/ipaddr");
		    str_wangateway =  XG  (this.rwaninetp+"/ipv4/gateway");
		    
		    str_wannetmask =  COMM_IPv4INT2MASK(XG  (this.rwaninetp+"/ipv4/mask"));
		    str_wanDNSserver = XG  (this.rwaninetp+"/ipv4/dns:1");
		    str_wanDNSserver2 = XG  (this.rwaninetp+"/ipv4/dns:2");
		}
		else if ((XG(this.rwaninetp+"/addrtype") == "ppp4"))
		{
		    str_wanipaddr = XG  (this.rwaninetp+"/ppp4/local");
		    str_wangateway = XG  (this.rwaninetp+"/ppp4/peer");
		    str_wannetmask = "255.255.255.255";
		    str_wanDNSserver = XG  (this.rwaninetp+"/ppp4/dns:1");
		    str_wanDNSserver2 = XG  (this.rwaninetp+"/ppp4/dns:2");
		    if(str_wanDNSserver == "" && str_wanDNSserver2 == "")
		    {
		        var wan2 = PXML.FindModule("INET.WAN-2");
		        var rwan2 = PXML.FindModule("RUNTIME.INF.WAN-2");
		        var waninetuid2 = XG  (wan2+"/inf/inet");
		        var rwaninetp2 = GPBT(rwan2+"/runtime/inf", "inet", "uid", waninetuid2, false); 
		        str_wanDNSserver = XG  (rwaninetp2+"/ipv4/dns:1");
		        str_wanDNSserver2 = XG  (rwaninetp2+"/ipv4/dns:2");
		    }
		}
		else if ((XG(this.rwaninetp+"/addrtype") == "ppp10"))
		{
		    str_wanipaddr = XG  (this.rwaninetp+"/ppp4/local");
		    str_wangateway = XG  (this.rwaninetp+"/ppp4/peer");
		    str_wannetmask = "255.255.255.255";
		    str_wanDNSserver = XG  (this.rwaninetp+"/ppp4/dns:1");
		    str_wanDNSserver2 = XG  (this.rwaninetp+"/ppp4/dns:2");
		    if(str_wanDNSserver == "" && str_wanDNSserver2 == "")
		    {
		        var wan2 = PXML.FindModule("INET.WAN-2");
		        var rwan2 = PXML.FindModule("RUNTIME.INF.WAN-2");
		        var waninetuid2 = XG  (wan2+"/inf/inet");
		        var rwaninetp2 = GPBT(rwan2+"/runtime/inf", "inet", "uid", waninetuid2, false); 
		        str_wanDNSserver = XG  (rwaninetp2+"/ipv4/dns:1");
		        str_wanDNSserver2 = XG  (rwaninetp2+"/ipv4/dns:2");
		    }
		}
		
        if ((wan_network_status==1)&& (wan_delta_uptime > 0)&& (wan_uptime > 0))
		{
			wan_uptime_sec = wan_delta_uptime%60;
			wan_uptime_min = Math.floor(wan_delta_uptime/60)%60;
		 	wan_uptime_hour = Math.floor(wan_delta_uptime/3600)%24;
		 	wan_uptime_day = Math.floor(wan_delta_uptime/86400);
		 	if (wan_uptime_sec < 0)
		 	{
		 	    wan_uptime_sec=0;
		 	    wan_uptime_min=0;
		 	    wan_uptime_hour=0;
		 	    wan_uptime_day=0;
		 	}
		 	
		 	
		}

		if (str_networkstatus == str_Connected )
		{
			OBJ("st_wan_dhcp_renew").disabled	= true;
        	OBJ("st_wan_dhcp_release").disabled	= false;
        }
        else if (str_networkstatus == str_Disconnected )
		{
			OBJ("st_wan_dhcp_renew").disabled	= false;
			OBJ("st_wan_dhcp_release").disabled	= true;
        }
        
		OBJ("st_networkstatus").innerHTML = str_networkstatus; 
		OBJ("name_wanipaddr").innerHTML = str_name_wanipaddr;
		OBJ("name_wangateway").innerHTML = str_name_wangateway;
		OBJ("st_wanipaddr").innerHTML  = str_wanipaddr!="" ? str_wanipaddr:"0.0.0.0";
		OBJ("st_wangateway").innerHTML  =  str_wangateway!="" ? str_wangateway:"0.0.0.0";
		OBJ("st_wanDNSserver").innerHTML  = str_wanDNSserver!="" ? str_wanDNSserver:"0.0.0.0";
		OBJ("st_wanDNSserver2").innerHTML  = str_wanDNSserver2!="" ? str_wanDNSserver2:"0.0.0.0";
		OBJ("st_wannetmask").innerHTML  =  str_wannetmask;
		OBJ("st_wan_mac").innerHTML  =  XG(this.rwanphyp+"/macaddr");
		if(OBJ("st_wanipaddr").innerHTML!="0.0.0.0")	OBJ("st_wan_dhcp_release").disabled	= false;
	
		OBJ("st_connection_uptime").innerHTML=  wan_uptime_day+" "+"<?echo I18N("h", "Day(s)");?>"+" "+wan_uptime_hour+" "+"<?echo I18N("h", "Hour(s)");?>"+" "+wan_uptime_min+" "+"<?echo I18N("h", "Min(s)");?>"+" "+wan_uptime_sec+" "+"<?echo I18N("h", "Sec(s)");?>";
		
        /* If Open DNS function is enabled, the DNS server would be fixed. */
		if(XG(wan+"/inf/open_dns/type")==="advance")
		{
			OBJ("st_wanDNSserver").innerHTML  = XG(wan+"/inf/open_dns/adv_dns_srv/dns1");
			OBJ("st_wanDNSserver2").innerHTML  = XG(wan+"/inf/open_dns/adv_dns_srv/dns2");
		}
		else if(XG(wan+"/inf/open_dns/type")==="family")
		{
			OBJ("st_wanDNSserver").innerHTML  = XG(wan+"/inf/open_dns/family_dns_srv/dns1");
			OBJ("st_wanDNSserver2").innerHTML  = XG(wan+"/inf/open_dns/family_dns_srv/dns2");
		}
		else if(XG(wan+"/inf/open_dns/type")==="parent")
		{
			OBJ("st_wanDNSserver").innerHTML  = XG(wan+"/inf/open_dns/parent_dns_srv/dns1");
			OBJ("st_wanDNSserver2").innerHTML  = XG(wan+"/inf/open_dns/parent_dns_srv/dns2");
		}
		
		this.getwanstatus =setTimeout("COMM_GetCFG(false, 'INET.WAN-1,RUNTIME.INF.WAN-1,RUNTIME.PHYINF,RUNTIME.TIME,ALERTMSG', PAGE.ShowWANStatus)", 5*1000);
		return true;
	},
	DHCP_Renew: function()
	{
	 	OBJ("st_wan_dhcp_renew").disabled	= true;
	    WAN1DHCPRENEW();
	},
	DHCP_Release: function()
	{
		OBJ("st_wan_dhcp_release").disabled	= true;
	    WAN1DHCPRELEASE();
	},
	PPP_Connect: function()
	{
	    var wan	= PXML.FindModule("INET.WAN-1");
	    var combo = XG  (wan+"/inf/lowerlayer");
	    if (combo !="") WAN1COMBODIALUP();
	    else WAN1PPPDIALUP();
	   
	   	if(this.pptp_NOT_IN_schedule)
	   	{
	   		BODY.ShowAlert("<?echo I18N("j", "Can't connect! Check your router schedule. You may have set it to disable your Internet. ")." ".
					I18N("j","Delete the schedule in 'Reconnect Mode'.");?>");
	  	}
	},
	PPP_Disconnect: function()
	{
	    var wan	= PXML.FindModule("INET.WAN-1");
	    var combo = XG  (wan+"/inf/lowerlayer");
	    if (combo !="") WAN1COMBOHANGUP();
	    else WAN1PPPHANGUP();
	}
}

var EventName=null;
function SendEvent(str)
{
	var ajaxObj = GetAjaxObj(str);
	if (EventName != null) return;

	EventName = str;
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		EventName = null;
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+EventName);
}
function S2I(str)	{ var num = parseInt(str, 10); return isNaN(num)?0:num;}	
function WAN1DHCPRENEW()	{ SendEvent("UI.DHCP.RENEW"); }
function WAN1DHCPRELEASE()	{ SendEvent("UI.DHCP.RELEASE"); }
/*PPPoE or 3G*/
function WAN1PPPDIALUP()	{ SendEvent("WAN-1.PPP.DIALUP"); }
function WAN1PPPHANGUP()	{ SendEvent("WAN-1.PPP.HANGUP"); }
/*PPTP/L2TP*/
function WAN1COMBODIALUP()	{ SendEvent("WAN-1.COMBO.DIALUP"); }
function WAN1COMBOHANGUP()	{ SendEvent("WAN-1.COMBO.HANGUP"); }


function Service(svc)
{	
	var banner = "<?echo I18N("h", "Rebooting");?>...";
	var msgArray = ["<?echo I18N("h", "Reboot...");?>"];
	var delay = 10;
	var sec = <?echo query("/runtime/device/bootuptime");?> + delay;
	var url = null;
	var ajaxObj = GetAjaxObj("SERVICE");
	if (svc=="FRESET")		url = "http://192.168.1.1/index.php";
	else if (svc=="REBOOT")	url = "http://<?echo $_SERVER["HTTP_HOST"];?>/index.php";
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
function LanguageToChangeCSS(lcode)
{//Only needs to consider tr and ru.Others languages are fine.
		if( lcode =='tr')
		{
			document.getElementById("st_wan_dhcp_renew").className = "button_blueX1p5";
			document.getElementById("st_wan_dhcp_release").className = "button_blueX1p5";
		}
		if( lcode =='ru')
		{
			document.getElementById("st_wan_dhcp_renew").className = "button_blueX2";
			document.getElementById("st_wan_dhcp_release").className = "button_blueX2";
		}
}
</script>
