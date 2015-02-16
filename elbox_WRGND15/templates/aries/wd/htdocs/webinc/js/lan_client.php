<?include "/htdocs/phplib/inet.php";?>
<?include "/htdocs/phplib/inf.php";?>
<style>
/* The CSS is only for this page.
 * Notice:
 *  If the items are few, we put them here,
 *  If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "INET.LAN-1,DHCPS4.LAN-1,DHCPS4.LAN-2,RUNTIME.INF.LAN-1,RUNTIME.INF.LAN-2,RUNTIME.PHYINF",
	OnLoad: function()
	{
		SetDelayTime(500);	//add delay for event updatelease finished
		BODY.CleanTable("reserves_list");
		BODY.CleanTable("leases_list");
		OBJ("reserv_host").value="";
		OBJ("reserv_ipaddr").value="";
		OBJ("reserv_macaddr").value="";
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) {},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.InitLAN()) return false;
		if (!this.InitDHCPS()) return false;
		return true;
	},
	PreSubmit: function()
	{
		if (!this.PreLAN()) return null;
		if (!this.PreDHCPS()) return null;
		PXML.IgnoreModule("RUNTIME.INF.LAN-1");
		PXML.IgnoreModule("RUNTIME.INF.LAN-2");
		return PXML.doc;
	},
	IsDirty: function()
	{
		var table = OBJ("reserves_list");
		var rows = table.getElementsByTagName("tr");
		var i;

		for(i=1; i < rows.length; i++)
		{
			this.reserv_new[i] = {
				host:	OBJ("en_dhcp_host"+i).innerHTML,
				hostid:	COMM_IPv4HOST(OBJ("en_dhcp_ipaddr"+i).innerHTML, this.mask),
				macaddr:OBJ("en_dhcp_macaddr"+i).innerHTML
			};
		}

		if(this.reserv_old.length != this.reserv_new.length) return true;

		for(i=1; i<=this.reserv_new.length; i++)
		{
			if(this.reserv_old[i]!=null && this.reserv_new[i]!=null)
			{
				if(this.reserv_old[i].host!= this.reserv_new[i].host ||
					this.reserv_old[i].hostid != this.reserv_new[i].hostid ||
					this.reserv_old[i].macaddr != this.reserv_new[i].macaddr
				)
					return true;
			}
			else if(this.reserv_old[i]!=null || this.reserv_new[i]!=null)
			{
				return true;
			}
		}
		return false;
	},
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	lanip: null,
	inetp: null,
	dhcps4: null,
	leasep: null,
	leasep2: null,
	mask: null,
	ipdirty: false,
	reserv_old: null,
	reserv_new: null,
	cfg: null,
	g_edit: 0,
	g_table_index: 1,
	InitLAN: function()
	{
		var lan	= PXML.FindModule("INET.LAN-1");
		var inetuid = XG(lan+"/inf/inet");
		this.inetp = GPBT(lan+"/inet", "entry", "uid", inetuid, false);
		if (!this.inetp)
		{
			BODY.ShowAlert("InitLAN() ERROR!!!");
			return false;
		}

		if (XG(this.inetp+"/addrtype") == "ipv4")
		{
			var b = this.inetp+"/ipv4";
			this.lanip = XG(b+"/ipaddr");
			this.mask = XG(b+"/mask");
		}

		return true;
	},
	PreLAN: function()
	{
		PXML.IgnoreModule("INET.LAN-1");
		return true;
	},
	InitDHCPS: function()
	{
		var svc = PXML.FindModule("DHCPS4.LAN-1");
		var svc_gz = PXML.FindModule("DHCPS4.LAN-2");
		var inf1p = PXML.FindModule("RUNTIME.INF.LAN-1");
		var inf2p = PXML.FindModule("RUNTIME.INF.LAN-2");
		if (!svc || !inf1p || !svc_gz || !inf2p)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.dhcps4 = GPBT(svc+"/dhcps4", "entry", "uid", "DHCPS4-1", false);
		this.leasep = GPBT(inf1p+"/runtime", "inf", "uid", "LAN-1", false);
		var router_ip = XG(this.leasep+"/dhcps4/pool/router");//For WD ITR 43414
		this.leasep2 = GPBT(inf2p+"/runtime", "inf", "uid", "LAN-2", false);
		if (!this.dhcps4)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.leasep += "/dhcps4/leases";
		this.leasep2 += "/dhcps4/leases";

		if (!this.leasep)	return true;	// in bridge mode, the value of this.leasep is null.

		if(XG(svc+"/inf/dhcps4")!="")
		{
			OBJ("reserv_ipaddr").disabled = false;
			OBJ("reserv_macaddr").disabled = false;
			OBJ("reserv_host").disabled = false;
			OBJ("b_add").disabled = false;
			OBJ("b_save").disabled = false;	
		}
		else
		{
			OBJ("reserv_ipaddr").value = "";
			OBJ("reserv_macaddr").value = "";
			OBJ("reserv_host").value = "";
			OBJ("reserv_ipaddr").disabled = true;
			OBJ("reserv_macaddr").disabled = true;
			OBJ("reserv_host").disabled = true;
			OBJ("b_add").disabled = true;	
			OBJ("b_save").disabled = true;
		}

		var lease_i = 0;
		entry = this.leasep+"/entry";
		cnt = XG(entry+"#");
		if (XG(svc+"/inf/dhcps4")!="")		// when the dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt; i++)
			{
				var uid		= "DUMMY_"+i;
				var host	= XG(entry+":"+i+"/hostname");
				var ipaddr	= XG(entry+":"+i+"/ipaddr");
				var mac		= XG(entry+":"+i+"/macaddr");
				var lease_t = parseInt(XG(entry+":"+i+"/expire"), 10);
				var lease_t_sec = lease_t % 60;
				var lease_t_min = ((lease_t - lease_t_sec) / 60) % 60;
				var lease_hr = (lease_t - (lease_t_min * 60) - lease_t_sec) / 3600;
				var lease_t_hr = lease_hr % 24;
				var lease_t_d = (lease_hr - lease_t_hr) / 24;
				var lt_string = lease_t_d + " Day(s) " + lease_t_hr + " Hr(s) " + lease_t_min + " Min(s) " + lease_t_sec + " Sec(s) ";
				lease_i++;

				var data = [lease_i, ipaddr, mac, host, lt_string];
				var type = ["text", "text", "text", "text", "text"];
				if(router_ip!=ipaddr && mac!="00:00:00:00:00:00")//For WD ITR 43414
					BODY.InjectTable("leases_list", uid, data, type);
			}
		}

		var entry_gz = this.leasep2+"/entry";
		var cnt_gz = XG(entry_gz+"#");
		if (XG(svc_gz+"/inf/dhcps4")!="")		// when the guest zone dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt_gz; i++)
			{
				lease_i++;
				var uid		= "DUMMY_GZ_"+i;
				var host	= XG(entry_gz+":"+i+"/hostname");
				var ipaddr	= XG(entry_gz+":"+i+"/ipaddr");
				var mac		= XG(entry_gz+":"+i+"/macaddr");
				//var lease_t = XG(entry+":"+i+"/expire");
				var lease_t = parseInt(XG(entry_gz+":"+i+"/expire"), 10);
				var lease_t_sec = lease_t % 60;
				var lease_t_min = ((lease_t - lease_t_sec) / 60) % 60;
				var lease_hr = (lease_t - (lease_t_min * 60) - lease_t_sec) / 3600;
				var lease_t_hr = lease_hr % 24;
				var lease_t_d = (lease_hr - lease_t_hr) / 24;
				var lt_string = lease_t_d + " day " + lease_t_hr + " hr " + lease_t_min + " min " + lease_t_sec + " sec ";
				var data	= [lease_i, ipaddr, mac, host, lt_string];
				var type	= ["text", "text", "text", "text", "text"];
				BODY.InjectTable("leases_list", uid, data, type);
			}
		}

		if (this.reserv_old) delete this.reserv_old;
		if (this.reserv_new) delete this.reserv_new;
		this.reserv_old = new Array();
		this.reserv_new = new Array();

		cnt = XG(this.dhcps4+"/staticleases/entry#");
		for(var i=1; i <= cnt; i++)
		{
			var data = [	i,
				'<span id="en_dhcp_ipaddr'+i+'"></span>',
				'<span id="en_dhcp_macaddr'+i+'"></span>',
				'<span id="en_dhcp_host'+i+'"></span>',
				'<a href="javascript:PAGE.OnEdit('+i+');"><img src="pic/img_edit.gif"></a>',
				'<a href="javascript:PAGE.OnDelete('+i+');"><img src="pic/img_delete.gif"></a>'
				];
			var type	= ["text","", "","",""];

			BODY.InjectTable("reserves_list", i, data, type);
			OBJ("en_dhcp_ipaddr"+i).innerHTML = COMM_IPv4IPADDR(this.lanip, this.mask, XG(this.dhcps4+"/staticleases/entry:"+i+"/hostid"));
			OBJ("en_dhcp_macaddr"+i).innerHTML = XG(this.dhcps4+"/staticleases/entry:"+i+"/macaddr");
			OBJ("en_dhcp_host"+i).innerHTML = XG(this.dhcps4+"/staticleases/entry:"+i+"/hostname");

			this.reserv_old[i] = {
				hostid:	 XG(this.dhcps4+"/staticleases/entry:"+i+"/hostid"),
				macaddr: XG(this.dhcps4+"/staticleases/entry:"+i+"/macaddr"),
				host: XG(this.dhcps4+"/staticleases/entry:"+i+"/hostname")
				};
		}
		this.g_table_index=i;

		return true;
	},
	PreDHCPS: function()
	{
		var lan = PXML.FindModule("DHCPS4.LAN-1");

		/*clear static leases entry*/
		var cnt = XG(this.dhcps4+"/staticleases/count");
		for (var i=1; i<= cnt; i++)
		{
			XD(this.dhcps4+"/staticleases/entry");
		}
		XS(this.dhcps4+"/staticleases/count", "0");

		/*+++set values to xml*/
		var table = OBJ("reserves_list");
		var rows = table.getElementsByTagName("tr");
		var rowslen = rows.length;


		for (var i=1; i < rowslen; i++)
		{
			var path = COMM_AddEntry(PXML.doc, this.dhcps4+"/staticleases", "STIP-");
			XS(path+"/enable",		"1");
			XS(path+"/hostname",	OBJ("en_dhcp_host"+i).innerHTML);
			XS(path+"/macaddr",		OBJ("en_dhcp_macaddr"+i).innerHTML);
			XS(path+"/hostid",		COMM_IPv4HOST(OBJ("en_dhcp_ipaddr"+i).innerHTML, this.mask));
		}
		/*---set values to xml*/

		PXML.ActiveModule("DHCPS4.LAN-1");
		return true;
	},
	ip_verify: function(name)
	{

		if(OBJ(name).value == "")
		{
			BODY.ShowAlert("<?echo I18N("j", "Please enter IP address");?>");
			OBJ(name).focus();
			return false;
		}

		if(!IsIPv4(OBJ(name).value))
		{
			BODY.ShowAlert("<?echo I18N("j", "Invalid IP address");?>");
			OBJ(name).focus();
			return false;
		}

		if(OBJ(name).value === this.lanip)
		{
			alert("<?echo I18N("j", "The IP Address can not be the same as LAN IP Address.");?>");
			OBJ(name).focus();
			return false;
		}
		if(!TEMP_CheckNetworkAddr(OBJ(name).value, this.lanip, this.mask))
		{
			alert("<?echo I18N("j", "IP address should be in LAN subnet.");?>");
			OBJ(name).focus();
			return false;
		}

		return true;
	},
	AddDHCPReserv: function()
	{
		var i=0;
		var myMac="";
		if(!this.ip_verify("reserv_ipaddr")) return false;	
		myMac=CheckMAC(OBJ("reserv_macaddr").value);
		
		if (myMac=="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Invalid MAC address value");?>");
			OBJ("reserv_macaddr").focus();
			return false;
		}
		if (OBJ("reserv_host").value=="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Invalid Computer Name");?>");
			OBJ("reserv_host").focus();
			return false;
		}

		if(this.g_edit!=0)
		{
			i=this.g_edit;
		}
		else
		{
			i=this.g_table_index;
		}
		if (i > <?=$DHCP_MAX_COUNT?>)
		{
			BODY.ShowAlert("<?echo I18N("j", "The maximum number of permitted DHCP reservations has been exceeded.");?>");
			return false;
		}

		var data = [	i,
						'<span id="en_dhcp_ipaddr'+i+'"></span>',
						'<span id="en_dhcp_macaddr'+i+'"></span>',
						'<span id="en_dhcp_host'+i+'"></span>',
						'<a href="javascript:PAGE.OnEdit('+i+');"><img src="pic/img_edit.gif"></a>',
						'<a href="javascript:PAGE.OnDelete('+i+');"><img src="pic/img_delete.gif"></a>'
						];
		var type = ["text","", "","",""];
		
		OBJ("reserv_macaddr").value = myMac;
		BODY.InjectTable("reserves_list", i, data, type);

		OBJ("en_dhcp_host"+i).innerHTML = OBJ("reserv_host").value;
		OBJ("en_dhcp_ipaddr"+i).innerHTML = OBJ("reserv_ipaddr").value;
		OBJ("en_dhcp_macaddr"+i).innerHTML = OBJ("reserv_macaddr").value;

		if(this.g_edit!=0)
		{
			this.g_edit=0;
		}
		else
		{
			this.g_table_index++;
		}
		this.ClearDHCPReserv();
		OBJ("reserv_ipaddr").focus();
	},
	ClearDHCPReserv: function()
	{
		OBJ("reserv_host").value = "";
		OBJ("reserv_ipaddr").value = "";
		OBJ("reserv_macaddr").value = "";
		OBJ("mainform").setAttribute("modified", "true");
	},
	OnEdit: function(i)
	{
		OBJ("reserv_host").value=OBJ("en_dhcp_host"+i).innerHTML;
		OBJ("reserv_ipaddr").value=OBJ("en_dhcp_ipaddr"+i).innerHTML;
		OBJ("reserv_macaddr").value=OBJ("en_dhcp_macaddr"+i).innerHTML;
		this.g_edit=i;
	},
	OnDelete: function(i)
	{
		var z,x;
		var table = OBJ("reserves_list");
		var rows = table.getElementsByTagName("tr");

		for (z=1; z<=rows.length; z++)
		{
			if(rows[z]!=null)
			{
				if (rows[z].id==i)
				{
					var tail_obj=rows.length-1;					
					for (x=i+1; x<rows.length; x++)
					{
						var y=x-1;
						OBJ("en_dhcp_host"+y).innerHTML    = OBJ("en_dhcp_host"+x).innerHTML;
						OBJ("en_dhcp_ipaddr"+y).innerHTML  = OBJ("en_dhcp_ipaddr"+x).innerHTML;
						OBJ("en_dhcp_macaddr"+y).innerHTML = OBJ("en_dhcp_macaddr"+x).innerHTML;
					}
					table.deleteRow(tail_obj);/* always delete last obj to avort obj is null. 2011.12.22 Daniel Chen */
					break;
				}
			}
		}		
		this.g_table_index--;
	}
}

function FocusObj(result)
{
	var found = true;
	var node = result.Get("/hedwig/node");
	var nArray = node.split("/");
	var len = nArray.length;
	var name = nArray[len-1];
	if (node.match("dhcps4"))
	{
		switch (name)
		{
		case "hostid":
			OBJ("reserv_ipaddr").focus();
			break;
		case "macaddr":
			OBJ("reserv_macaddr").focus();
			break;
		default:
			found = false;
			break;
		}
	}
	else
	{
		found = false;
	}

	return found;
}

function SetDelayTime(millis)
{
	var date = new Date();
	var curDate = null;
	curDate = new Date();
	do { curDate = new Date(); }
	while(curDate-date < millis);
}
function CheckMAC(m)
{
	var myMAC="";
	if (m.search(":") != -1)	var tmp=m.split(":");
	else if (m.search("-") != -1)		var tmp=m.split("-");
	else		var tmp="non_segment";
	if (m == "" || tmp.length != 6 ){	
		if (m.length != 12)		return "";
	}
	if (tmp!="non_segment"){
		for (var i=0; i<tmp.length; i++)
		{
			if (tmp[i].length==1)	tmp[i]="0"+tmp[i];
			else if (tmp[i].length==0||tmp[i].length>2)	return "";
			tmp[i]=tmp[i].toLowerCase();
			for (var j=0; j<tmp[i].length; j++)
			{
				var c = "0123456789abcdef";
				var str_hex=0;
				for(var k=0; k<c.length; k++)	if(tmp[i].substr(j,1)===c.substr(k,1))	{str_hex=1;break;}
				if(str_hex===0) return "";
			}
		}
		myMAC = tmp[0];
		for (var i=1; i<tmp.length; i++){	
			myMAC = myMAC + ':' + tmp[i];
		}
	}
	else {
		tmp=m;
		tmp=tmp.toLowerCase();
		for (var j=0; j<tmp.length; j++){
			var c = "0123456789abcdef";
			var str_hex=0;
			for (var k=0; k<c.length; k++)	if(tmp.substr(j,1)===c.substr(k,1))	{str_hex=1;break;}
			if (str_hex===0) return "";
		}
		myMAC = tmp[0]+tmp[1];
		for (var i=2; i<tmp.length-1; i++){	
			myMAC = myMAC + ':' + tmp[i]+tmp[i+1];
			i++;
		}
	}


	if(myMAC==="ff:ff:ff:ff:ff:ff" || myMAC==="01:11:11:11:11:11" || myMAC=="00:00:00:00:00:00")	return "";
	return myMAC;
}
function IsIPv4(ipv4)
{
	var vals = ipv4.split(".");
	if (vals.length!==4)	return false;
	for (var i=0; i<4; i++)	if (!TEMP_IsDigit(vals[i]) || vals[i]>255)	return false;
	return true;
}
</script>
