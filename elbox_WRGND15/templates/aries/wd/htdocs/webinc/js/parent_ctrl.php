
<?
	$mac=query("/runtime/devdata/wanmac");
	$newMac="";
	$num=0;
	while ($num < 6)
	{
	    $tmpMac = cut($mac, $num, ":");
	    $newMac = $newMac.$tmpMac;
	    $num++;
	}
	$newMac = toupper($newMac);
?>
<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "PARENTCTRL,DEVICE.TIME,DHCPS4.LAN-1,DHCPS4.LAN-2,RUNTIME.INF.LAN-1,RUNTIME.INF.LAN-2",
	OnLoad: function() 
	{
		//add delay for event updatelease finished.
		//Hope it can prevent sometimes can't get DHCP's mac and hostname.
		this.SetDelayTime(800);
		BODY.CleanTable("DHCP_info");
		this.devicecount = 0;
	},
	OnUnload: function() {},
	OnSubmitCallback: function () {},
	parent_ctrl: null,
	enable: null,
	registed: null,
	located: null,
	xml_location: null,
	email: null,
	pw: null,
	mac: null,
	tid: null,
	tid2: null,
	device_limit: null,
	honame: null,
	IsSynchronized: null,
	devicecount: null,
	dhcps4: null,
	leasep: null,
	leasep2: null,
	valid_mail: null,	
	
	InitValue: function(xml)
	{
		PXML.doc = xml;
		PXML.IgnoreModule("DEVICE.TIME");
		PXML.IgnoreModule("DHCPS4.LAN-1");
		PXML.IgnoreModule("DHCPS4.LAN-2");
		PXML.IgnoreModule("RUNTIME.INF.LAN-1");
		PXML.IgnoreModule("RUNTIME.INF.LAN-2");
		PXML.IgnoreModule("PARENTCTRL");
		
		this.IsSynchronized = "";
		this.parent_ctrl = PXML.FindModule("PARENTCTRL");
		this.devtime_p = PXML.FindModule("DEVICE.TIME");

		if (!this.parent_ctrl || !this.devtime_p)
		{
			alert("InitValue ERROR!"); 
			return false; 
		}
		this.enable		= XG(this.parent_ctrl+"/security/netstar/enable");
		this.registed	= XG(this.parent_ctrl+"/security/netstar/registed");
		this.xml_location = XG(this.parent_ctrl+"/security/netstar/location");
		PAGE.device_limit = S2I(XG(this.parent_ctrl+"/security/netstar/device_limit"));
		
		if(XG(PAGE.parent_ctrl+"/security/active")=="1")
		{
		if(this.enable=="1" && this.xml_location!="")
		{
			OBJ("en_parent_ctrl").checked= true; 
			OBJ("device_location").value=this.xml_location;
			this.OpenUI();
			this.ShowTable();
		}
		else if(this.enable=="1")
		{
			OBJ("en_parent_ctrl").checked= true;
			this.CloseUI();
			OBJ("device_span").style.display="";
			OBJ("device_span").disabled	= false;
			OBJ("location_text").style.display="";
			OBJ("location_text").disabled	= false;
			this.SetLocZone("");
		}
		else
		{
			OBJ("en_parent_ctrl").checked= false;
			this.CloseUI();
		}
		}
		else
		{
			OBJ("en_parent_ctrl").checked= false;
			this.CloseUI();
			this.enable = "0";
		}
		return true;
	},
	ShowTable: function()
	{
		BODY.CleanTable("DHCP_info");
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
		var router_ip = XG(this.leasep+"/dhcps4/pool/router");//For WD ITR 43414 same with lan_client.php
		this.leasep2 = GPBT(inf2p+"/runtime", "inf", "uid", "LAN-2", false);
		if (!this.dhcps4)
		{
			BODY.ShowAlert("InitDHCPS() ERROR !");
			return false;
		}
		this.leasep += "/dhcps4/leases";
		this.leasep2 += "/dhcps4/leases";

		if (!this.leasep)	return true;	// in bridge mode, the value of this.leasep is null.

		var lease_i = 0;
		entry = this.leasep+"/entry";
		cnt = XG(entry+"#");
		if (XG(svc+"/inf/dhcps4")!="")		// when the dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt; i++)
			{
				var host	= XG(entry+":"+i+"/hostname");
				var mac		= XG(entry+":"+i+"/macaddr");
				var ipaddr	= XG(entry+":"+i+"/ipaddr");//For WD ITR 43414.same with lan_client.php
				mac = this.getMAC(mac);
				lease_i++;

				var data = [	'<div align="center"><input type="checkbox" id="'+lease_i+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+lease_i+'_check_0\');" ></div>',
						'<div align="center" id="name_'+lease_i+'">'+host+'</div>',
						'<div align="center" id="mac_'+lease_i+'">'+mac+'</div>'
						];
				var type = ["","",""];
												
				if(router_ip!=ipaddr && mac!="00:00:00:00:00:00")//For WD ITR 43414.same with lan_client.php
				{
					BODY.InjectTable("DHCP_info", lease_i, data, type);
	
					OBJ(lease_i+"_check_0").checked = false;
					OBJ(lease_i+"_check_0").disabled = false;
					OBJ("mac_"+lease_i).value = mac;
					OBJ("name_"+lease_i).value = host;
				}
			}
		}

	/*	var entry_gz = this.leasep2+"/entry";
		var cnt_gz = XG(entry_gz+"#");
		if (XG(svc_gz+"/inf/dhcps4")!="")		// when the guest zone dhcp server is enabled show the dynamic dhcp clients list
		{
			for (var i=1; i<=cnt_gz; i++)
			{
				var uid		= "DUMMY_GZ_"+i;
				var host	= XG(entry_gz+":"+i+"/hostname");
				var mac		= XG(entry_gz+":"+i+"/macaddr");
				mac = this.getMAC(mac);
				lease_i++;
				
				var data = [	'<div align="center"><input type="checkbox" id="'+lease_i+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+lease_i+'_check_0\');" ></div>',
						'<div align="center" id="name_'+lease_i+'">'+host+'</div>',
						'<div align="center" id="mac_'+lease_i+'">'+mac+'</div>'
						];
				var type = ["","",""];
				
				BODY.InjectTable("DHCP_info", lease_i, data, type);
				
				OBJ(lease_i+"_check_0").checked = false;
				OBJ(lease_i+"_check_0").disabled = false;
				OBJ("mac_"+lease_i).value = mac;
				OBJ("name_"+lease_i).value = host;
			}
		}*/
		this.devicecount = lease_i;	
		this.AddRegisteredData();

		NewCheckRadio.init(); //Generate the new styles of checkbox and radio for WD.
		
	},
	OpenUI: function()
	{
		OBJ("email_count").style.display="";
		OBJ("email_count").disabled	= false;
		OBJ("private_password").style.display="";
		OBJ("private_password").disabled = false;
		OBJ("regist_device").style.display="";
		OBJ("regist_device").disabled = false;
		OBJ("button_apply").style.display="";
		OBJ("button_apply").disabled	= false;
		OBJ("policy_management").style.display="";
		OBJ("policy_management").disabled	= false;
		OBJ("hr1").style.display="";
		OBJ("hr2").style.display="";
		
		OBJ("regist_router").style.display="";
		OBJ("regist_router").disabled = false;
		OBJ("DHCP_text").style.display="";
		OBJ("DHCP_text").disabled = false;
		OBJ("DHCP_info").style.display="";
		OBJ("DHCP_info").disabled = false;
		OBJ("Dscription").style.display="";
		OBJ("password_text").style.display="";
		OBJ("password_text").disabled	= false;
		OBJ("Router_text").style.display="";
		OBJ("Router_text").disabled	= false;
		OBJ("email_text").style.display="";
		OBJ("email_text").disabled	= false;
		OBJ("device_span").style.display="";
		OBJ("device_span").disabled	= false;
		OBJ("location_text").style.display="";
		OBJ("location_text").disabled	= false;
		OBJ("register_result").style.display="";
		OBJ("register_result").disabled	= false;	
		OBJ("register_result").innerHTML = "";	
		OBJ("register_result2").style.display="";
		OBJ("register_result2").disabled	= false;	
		OBJ("register_result2").innerHTML = "";
		/*Disable button to save or cancel*/
		this.SetFieldData();
		this.SetPolicyLink();
	},
	CloseUI: function()
	{
		OBJ("email_count").style.display="none";
		OBJ("email_count").disabled	= true;
		OBJ("private_password").style.display="none";
		OBJ("private_password").disabled = true;
		OBJ("regist_device").style.display="none";
		OBJ("regist_device").disabled = true;
		OBJ("policy_management").style.display="none";
		OBJ("policy_management").disabled	= true;	
		OBJ("hr1").style.display="none";
		OBJ("hr2").style.display="none";
		
		OBJ("regist_router").style.display="none";
		OBJ("regist_router").disabled = true;
		OBJ("DHCP_text").style.display="none";
		OBJ("DHCP_text").disabled = true;
		OBJ("DHCP_info").style.display="none";
		OBJ("DHCP_info").disabled = true;
		OBJ("Dscription").style.display="none";
		OBJ("password_text").style.display="none";
		OBJ("password_text").disabled	= true;
		OBJ("Router_text").style.display="none";
		OBJ("Router_text").disabled	= true;
		OBJ("email_text").style.display="none";
		OBJ("email_text").disabled	= true;
		OBJ("device_span").style.display="none";
		OBJ("device_span").disabled	= true;
		OBJ("location_text").style.display="none";
		OBJ("location_text").disabled	= true;
		OBJ("register_result").style.display="none";
		OBJ("register_result").disabled	= true;	
		OBJ("register_result").innerHTML = "";	
		OBJ("register_result2").style.display="none";
		OBJ("register_result2").disabled	= true;	
		OBJ("register_result2").innerHTML = "";
		//OBJ("hide_button_info").style.display="none";
		//OBJ("hide_button_info").disabled	= true;	
		//OBJ("hide_button_info").innerHTML = "";
		OBJ("div_restore").style.display="none";
		/*Enable button to save or cancel*/
	},
	checkPW: function(str)
	{
		var i=0;
		var result=0;
		if(str.length<6 || str.length>20)
		{
			result=-1;
		}
		else
		{
			for(i=0;i<str.length;i++) 
			{ 
				if(str.charAt(i)<'0'||(str.charAt(i)>'9' && str.charAt(i)<'A')||(str.charAt(i)>'Z' && str.charAt(i)<'a')||str.charAt(i)>'z')
				{ 
					result=-1; 
					break;
				}
			}
		}
		return result;
	},
	OnClickButtonRegister: function(mode)
	{
		var email = OBJ("email_count").value;
		var locate = OBJ("device_location").value;
		var argumentMAC="";
		var str;
		var chPW=0;
		if(mode=="RegisterRouter")
		{	
			var pass = OBJ("private_password").value;
			var strReg=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			pass=this.RmSpace(pass);
			chPW=this.checkPW(pass);
			
			if(this.xml_location!=locate)
			{
					BODY.ShowAlert("<?echo I18N("j", "Please click on Apply button to save the new router location before registering the router.");?>");
					OBJ("button_apply").focus();
			}
			else if(email == "" || email.match("@")==null || (email.lastIndexOf("@")!=email.indexOf("@")) || email.search(strReg)==-1 || pass == "" || chPW != 0 )
			{
				/* Email rule:
				* 1. No @ sign
				* 2. No . (dot) anywhere after @ sign
				* 3. No character before @ sign
				* 4. No character between @ and . (dot)
				* 5. No character after . (dot)
				*/
				if(email=="")
				{
					BODY.ShowAlert("<?echo I18N("j", "The email address is empty.");?>");
					OBJ("email_count").focus();
				}
				else if(pass == "")
				{
					BODY.ShowAlert("<?echo I18N("j", "The password is empty.");?>");
					OBJ("private_password").focus();
				}
				else if(chPW!=0)
				{
					BODY.ShowAlert("<?echo I18N("j", "Invalid password. Please enter a password between 6 and 20 alphanumeric characters.");?>");
					OBJ("private_password").focus();
				}
				else
				{
					BODY.ShowAlert("<?echo I18N("j", "Invalid email format. Please re-enter.");?>");
					OBJ("email_count").focus();
				}
			}
			else
			{
				this.mac = XG(this.parent_ctrl+"/runtime/devdata/wanmac");
				this.mac = this.getMAC(this.mac);
				//argumentMAC = this.ChangeMAC(this.mac);
				argumentMAC = this.mac;
				var ajaxObj = GetAjaxObj("RegisterRouter");
				ajaxObj.release();
				ajaxObj.createRequest();
				this.bindUI();
				OBJ("register_result").innerHTML = "<? echo I18N("h", "Registering router");?>";
				ajaxObj.onCallback = function(xml)
				{
					var register_result = xml.Get("/result/result1");
					switch(register_result)
					{
						case "Correct":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Registration Successful! You will receive a registration confirmation email.");echo "<br>";echo I18N("h", "Please follow the URL link in the email to confirm your registration of Parental Controls.");echo "<br>";echo I18N("h", "Parental Controls will be activated 5 minutes after you confirm your registration.");?>";
							break;
						case "Request fail":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Request fail");?>";
							break;
						case "Invalid HEADER part":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Invalid header part");?>";
							break;				
						case "Invalid BODY part":
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Invalid body part");?>";
							break;
						case "Duplicated E-Mail and Password":
							OBJ("register_result").innerHTML = "<img src='pic/warning_mark_small.png' style='float:left;margin-right:10px'>"+"<? echo I18N("h", "Your selected email address/password combination is already registered for another router product. Please select another email address or password to register.");?>";
							break;				
						default:
							OBJ("register_result").innerHTML = "<? echo I18N("h", "Registration failed");?>";
							break;
					}
					PAGE.showUI();
					if(register_result=="Correct"||register_result=="Duplicated E-Mail and Password")
					{
						OBJ("regist_router").style.display="none";
						OBJ("regist_router").disabled = true;
						OBJ("email_count").disabled = true;
						OBJ("password_text").style.display="none";
						OBJ("private_password").style.display="none";
						OBJ("div_restore").style.display="";
						OBJ("regist_device").style.display="";
						if(register_result=="Duplicated E-Mail and Password")
							PAGE.UpdateDeviceData();
					}
				}
				ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
				ajaxObj.sendRequest("parent_ctrl_register.php", "act=RegisterRouter&email="+email+"&password="+pass+"&location="+locate+"&mac="+argumentMAC);
			}
		}
		else if(mode=="RegisterDevice")
		{
			var i=1;
			var a1="{\"MAC\":\"";
			var a2="\"}";
			var del="";
			var add="";	
			var alreadyregister=0;
	
			for(i=1; i<=this.devicecount; i++)
			{
				argumentMAC="";
				argumentMAC=OBJ("mac_"+i).value;
				//argumentMAC=this.ChangeMAC(OBJ("mac_"+i).value);
				if(OBJ(i+"_check_0").checked==false)//del
				{
					if(COMM_EqBOOL(OBJ(i+"_check_0").getAttribute("modified"),true))
					{
						if(del=="")
							del = "'" + a1 + argumentMAC + a2;
						else
							del = del +"," + a1 + argumentMAC + a2;
					}
				}
				else//add
				{
					alreadyregister++;
					if(COMM_EqBOOL(OBJ(i+"_check_0").getAttribute("modified"),true))
					{
						if(add=="")
							add="'"+a1+argumentMAC+"\",\"Name\":\""+OBJ("name_"+i).value+a2;
						else
							add=add+","+a1+argumentMAC+"\",\"Name\":\""+OBJ("name_"+i).value+a2;
					}
				}
				OBJ(i+"_check_0").setAttribute("modified", "false");//set to default
			}
			if((add!="" || del!="")&& alreadyregister<=PAGE.device_limit)
			{
				if(add!="")
					add=add+"'";
				if(del!="")
					del=del+"'";

				var ajaxObj = GetAjaxObj("RegisterDevice");
				ajaxObj.release();
				ajaxObj.createRequest();
				this.bindUI();
				OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registering device");?>";
				ajaxObj.onCallback = function(xml)
				{
					var register_result = xml.Get("/result/result1");
					switch(register_result)
					{
						case "Correct":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registration success");?>";
							break;
						case "Request fail":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Request fail");?>";
							break;
						case "Invalid HEADER part":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid header part");?>";
							break;				
						case "Invalid BODY part":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid body part");?>";
							break;
						case "Duplicated E-Mail and Password":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Duplicated email and password");?>";
							break;				
						case "Invalid authentication":
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Invalid authentication");?>";
							break;				
						default:
							OBJ("register_result2").innerHTML = "<? echo I18N("h", "Registration failed");?>";
							break;
					}
					PAGE.showUI();
				}
				ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
				ajaxObj.sendRequest("parent_ctrl_register.php", "act=RegisterDevice&add="+add+"&del="+del+"&email="+email+"&location="+locate);	
			}
			else if(alreadyregister > PAGE.device_limit)
			{
				BODY.ShowAlert("<? echo I18N("j", "The max number of devices registered is ");?>" + PAGE.device_limit);
				PAGE.MantainDeviceData();
			}
			else
			{
				BODY.ShowAlert("<? echo I18N("h", "Device settings have not changed.");?>");
			}
		}
		else if(mode=="RestoreUI")
		{
			if(this.xml_location!=locate)
			{
					BODY.ShowAlert("<?echo I18N("j", "Please click on Apply button to save the new router location before registering the router.");?>");
					OBJ("button_apply").focus();
			}
			else
			{
				//recovery email field,password text,password field, and register router button.
				//hide "hide_button_info" message.
				//OBJ("hide_button_info").style.display="none";
				//OBJ("hide_button_info").innerHTML = '';
				OBJ("email_count").disabled = false;
				OBJ("password_text").style.display="";
				OBJ("private_password").style.display="";
				OBJ("div_restore").style.display="none";
				OBJ("register_result").innerHTML = '';
				OBJ("regist_router").style.display="";
				OBJ("regist_router").disabled = false;
				OBJ("regist_router").style.color="white";
			}
		}
	},
	SetDelayTime: function(millis)
	{
		var date = new Date();
		var curDate = null;
		curDate = new Date();
		do 
		{
			curDate = new Date(); 
		}while(curDate-date < millis);
	},
	SetPolicyLink: function()
	{
		var ajaxObj = GetAjaxObj("GetLink");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			var cp_server = xml.Get("/result/result1");
			var email;
			var locate;//20120419 add
			locate = OBJ("device_location").value;//20120419 add
			if(OBJ("email_count").value=="")
			{	
				email = xml.Get("/result/result2");
			}
			else
			{
				email = OBJ("email_count").value;
			}
			if(cp_server==="undefined" ||cp_server==="" || cp_server===null)
			{
				OBJ("policy_management").style.display="none";
				OBJ("policy_management").disabled	= true;
				PAGE.tid = setTimeout("PAGE.SetPolicyLink();", 3000);
			}
			else
			{
				cp_server = "www.wdinternetsecurity.com";//this is new link!!!! In old link method,no need to do tis line.
				if(email==="" || email===null || email==="undefined")
				{
					//document.getElementById("linking").href="https://"+cp_server+"/wdpcs/login.php"; this is old link,at 20120419 WD want us use new link
					document.getElementById("linking").href="https://"+cp_server+"/?location="+locate;//this is new link! In old link method,no need to do tis line.
					//document.getElementById("linking").style.color = "#1E90FF";no need to do this. template.php has been modify.

				}
				else
				{
					//document.getElementById("linking").href="https://"+cp_server+"/wdpcs/login.php?email="+email; this is old link,at 20120419 WD want us use new link
					document.getElementById("linking").href="https://"+cp_server+"/?email="+email+"&location="+locate;//this is new link! In old link method,no need to do tis line.
					//document.getElementById("linking").style.color = "#1E90FF";no need to do this. template.php has been modify.
				}
				clearTimeout(PAGE.tid);
				OBJ("policy_management").style.display="";
				OBJ("policy_management").disabled	= false;
				OBJ("linking").innerHTML = "<?echo I18N("h","WD Internet parental controls policy management");?>";
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("parent_ctrl_register.php", "act=GetLink");
	},
	AddRegisteredData: function()
	{
		var list;
		var listname;
		var honame;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		
		list = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/name");//i start at 1
		while( list != null && list != "" )
		{
			//list=list.toLowerCase();
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" onclick="PAGE.OnClickDHCP(\''+this.devicecount+'_check_0\');" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
						];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(this.parent_ctrl+"/runtime/netstar/user:"+i+"/name");
		}
	},
	HideTable: function()
	{
	},
	bindUI: function()
	{	
		OBJ("en_parent_ctrl").disabled = true;	
		OBJ("device_span").disabled = true;	
		OBJ("button_apply").disabled = true;
		OBJ("email_count").disabled = true;
		OBJ("private_password").disabled = true;
		OBJ("regist_router").disabled = true;
		OBJ("regist_device").disabled = true;
		OBJ("policy_management").style.display="none";
		OBJ("policy_management").disabled	= true;
	},
	showUI: function()
	{
		OBJ("en_parent_ctrl").disabled = false;	
		OBJ("device_span").disabled = false;	
		OBJ("button_apply").disabled = false;
		if(OBJ("div_restore").style.display=="none")
			OBJ("email_count").disabled = false;
		OBJ("private_password").disabled = false;
		OBJ("regist_router").disabled = false;
		OBJ("regist_device").disabled = false;
		OBJ("policy_management").style.display="";
		OBJ("policy_management").disabled	= false;
		this.SetPolicyLink();
	},
	getMAC: function(m)
	{
		var myMAC="";
		if (m.search(":") != -1)	var tmp=m.split(":");
		else				var tmp=m.split("-");
		if (m == "" || tmp.length != 6)
			return "";
	
		for (var i=0; i<tmp.length; i++)
		{
			if (tmp[i].length==1)
				tmp[i]="0"+tmp[i];
			else if (tmp[i].length==0||tmp[i].length>2)
				return "";
		}
		myMAC = tmp[0];
		for (var i=1; i<tmp.length; i++)
		{
			//myMAC = myMAC + ':' + tmp[i];
			myMAC = myMAC + tmp[i];
		}
		return myMAC.toUpperCase();
	},
	/*
	getMAC: function(m)
	{
		var myMAC="";
		if (m.search(":") != -1)	var tmp=m.split(":");
		else				var tmp=m.split("-");
		if (m == "" || tmp.length != 6)
			return "";

		for (var i=0; i<tmp.length; i++)
		{
			if (tmp[i].length==1)
				tmp[i]="0"+tmp[i];
			else if (tmp[i].length==0||tmp[i].length>2)
				return "";
		}
		myMAC = tmp[0];
		for (var i=1; i<tmp.length; i++)
		{
			myMAC = myMAC + ':' + tmp[i];
		}
		return myMAC;
	},
	ChangeMAC: function(m)
	{
		var returnMAC="";
		if(m.search(":") != -1)
		{
			var tmp=m.split(":");
		}
		returnMAC = tmp[0];
		for (var i=1; i<tmp.length; i++)
		{
			returnMAC = returnMAC + tmp[i];
		}
		return returnMAC.toUpperCase();
	},
	*/
	RmPunc: function(str)
	{
		var i,j;
		var result="";
		var before="";
		var after="";
		while(str.length>0)
		{
			j=-1;
			j=str.search(":");
			if(j>=0)
			{
				before="";
				after="";
				before=str.substr(0,j);
				after=str.substr(j+1,str.length-j-1);
				str=after;
				result = result.concat(before);
			}
			else
			{
				result = result.concat(str);
			}
		}
		return result;
	},
	UpdateDeviceData: function()
	{
		OBJ("DHCP_updating_text").style.display="";
		OBJ("regist_device").disabled	= true;
		COMM_GetCFG(false, "PARENTCTRL", function(xml) {PAGE.AddData(xml);});
	},
	AddData: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("PARENTCTRL");
		var list;
		var listname;
		var honame;
		var routername;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		var alreadyrun=0;
		routername = XG(p+"/runtime/netstar/name");
		list = XG(p+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(p+"/runtime/netstar/user:"+i+"/name");//i start at 1
		while( list != null && list != "" )
		{
			//list=list.toLowerCase();
			alreadyrun=1;
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
							];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(p+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(p+"/runtime/netstar/user:"+i+"/name");
		}
		if(alreadyrun==0 && routername=="")
		{
			PAGE.tid2 = setTimeout("PAGE.UpdateDeviceData();", 2000);
		}
		else
		{
			OBJ("DHCP_updating_text").style.display="none";
			OBJ("regist_device").disabled	= false;
			clearTimeout(PAGE.tid2);
			NewCheckRadio.init();
		}
	},
	MantainDeviceData: function()
	{
		OBJ("DHCP_updating_text").style.display="";
		OBJ("regist_device").disabled	= true;
		COMM_GetCFG(false, "PARENTCTRL", function(xml) {PAGE.Mantaindata(xml);});
	},
	Mantaindata: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("PARENTCTRL");
		var list;
		var listname;
		var honame;
		var routername;
		var i=1;
		var j=1;
		var k = this.devicecount;
		var check=0;
		var alreadyrun=0;
		routername = XG(p+"/runtime/netstar/name");
		list = XG(p+"/runtime/netstar/user:"+i+"/mac");//i start at 1
		listname = XG(p+"/runtime/netstar/user:"+i+"/name");//i start at 1
		
		for(j=1;j<=k;j++)
			OBJ(j+"_check_0").checked = false;
			
		while( list != null && list != "" )
		{
			alreadyrun=1;
			if(listname!="" &&listname!=null)
			{
				honame=listname;
			}
			else
			{
				honame="Unknown";
			}
			check=0;
			for(j=1;j<=k;j++)
			{
				if(OBJ("mac_"+j).value==list)
				{	
					check=1;
					break;
				}
			}
			if(check==0)
			{
				this.devicecount++;
				var data = [	'<div align="center"><input type="checkbox" id="'+this.devicecount+'_check_0" class="styled2" ></div>',
							'<div align="center" id="name_'+this.devicecount+'">'+honame+'</div>',
							'<div align="center" id="mac_'+this.devicecount+'">'+list+'</div>'
							];
				var type = ["","",""];

				BODY.InjectTable("DHCP_info", this.devicecount, data, type);
		
				OBJ(this.devicecount+"_check_0").checked = true;
				OBJ(this.devicecount+"_check_0").disabled = false;
				OBJ("mac_"+this.devicecount).value = list;
				OBJ("name_"+this.devicecount).value = honame;
			}
			else
			{
				OBJ(j+"_check_0").checked = true;
				if(honame!="Unknown")
					//OBJ("name_"+this.devicecount).value = honame;
					OBJ("name_"+j).value = honame;
			}
			i++;
			list = XG(p+"/runtime/netstar/user:"+i+"/mac");
			listname = XG(p+"/runtime/netstar/user:"+i+"/name");
		}
		if(alreadyrun==0 && routername=="")
		{
			PAGE.tid2 = setTimeout("PAGE.UpdateDeviceData();", 2000);
		}
		else
		{
			OBJ("DHCP_updating_text").style.display="none";
			OBJ("regist_device").disabled	= false;
			clearTimeout(PAGE.tid2);
			NewCheckRadio.init();
		}
	},
	SetLocZone: function(val)
	{
		var zone=0;
		if(val==null|| val=="")
		{
			val = XG(this.devtime_p+"/device/time/timezone");
			zone = parseInt(val,10);
			if( zone>0 && zone<25 )
			{
				OBJ("device_location").value = "USA";
				this.located = "USA";
			}
			else if( zone>24 && zone<49 )
			{
				OBJ("device_location").value = "EMEA";
				this.located = "EMEA";
			}
			else if( zone>48 && zone<76 )
			{
				OBJ("device_location").value = "APAC";
				this.located = "APAC";
			}
			else
			{
				OBJ("device_location").value = "USA";//default is USA
				this.located = "USA";//default is USA
			}
		}
		else
		{
			OBJ("device_location").value = val;
			this.located = val;
		}
	},
	SetFieldData: function()
	{
		var ajaxObj = GetAjaxObj("GetFieldData");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			PAGE.located = xml.Get("/result/result1");//location
			PAGE.SetLocZone(PAGE.located);
			if(PAGE.email=="" ||PAGE.email==null)
			{
				PAGE.email = xml.Get("/result/result2");//email
				PAGE.valid_mail = PAGE.email;
				OBJ("email_count").value = PAGE.email;
				OBJ("private_password").value = xml.Get("/result/result3");//password
			}			
			if(OBJ("email_count").value!="" && OBJ("email_count").value!=null)
			{
				//OBJ("hide_button_info").style.display="";
				//OBJ("hide_button_info").innerHTML = "<img src='pic/warning_mark_small.png...
				OBJ("regist_router").style.display="none";
				OBJ("regist_router").disabled = true;
				OBJ("email_count").disabled = true;
				OBJ("password_text").style.display="none";
				OBJ("private_password").style.display="none";
				OBJ("div_restore").style.display="";
				OBJ("regist_device").style.display="";
			}
			else
			{
				//OBJ("hide_button_info").style.display="none";
				//OBJ("hide_button_info").innerHTML = '';
				OBJ("div_restore").style.display="none";
				OBJ("private_password").value = "";
				OBJ("regist_device").style.display="none";
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("parent_ctrl_register.php", "act=GetFieldData");
	},
	RmSpace: function(str)
	{
		ptntrim = /(^\s*)|(\s*$)/g;
		return str.replace(ptntrim,"");
	},
	PreSubmit: function()
	{	
		this.located = OBJ("device_location").value;
		var ajaxObj = GetAjaxObj("Apply");
		ajaxObj.release();
		ajaxObj.createRequest();
		
		ajaxObj.onCallback = function(xml)
		{/*do nothing*/}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("parent_ctrl_register.php", "act=Apply&location="+this.located+"&en="+this.enable);
		return PXML.doc;
	},
	OnClickChange: function()
	{
		if(OBJ("en_parent_ctrl").checked && this.xml_location!="")
		{
			OBJ("device_location").value=this.xml_location;
			this.OpenUI();
			this.ShowTable();
			this.enable = "1";
		}
		else if(OBJ("en_parent_ctrl").checked)
		{
			this.CloseUI();
			OBJ("device_span").style.display="";
			OBJ("device_span").disabled	= false;
			OBJ("location_text").style.display="";
			OBJ("location_text").disabled	= false;
			this.enable = "1";
			this.SetLocZone("");
		}
		else
		{
			this.CloseUI();
			this.enable = "0";
		}
	},	
	OnClickDHCP: function(check)
	{
		if(COMM_EqBOOL(OBJ(check).getAttribute("modified"),true))
		{
			OBJ(check).setAttribute("modified", "false");
		}
		else
		{
			OBJ(check).setAttribute("modified", "true");
		}
	},
	isDirty: null,
	Synchronize: function() {}
}
</script>
