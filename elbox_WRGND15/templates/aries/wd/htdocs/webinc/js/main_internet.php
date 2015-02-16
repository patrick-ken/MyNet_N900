<?include "/htdocs/phplib/inet.php";?>

<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div p.wiz_strong
{
	margin-left:46px;
	color: #13376B;
	font-weight: bold;
}
div span.wiz_input
{
	width: 35%;
	font-weight: bold;
	margin-left:50px;
	margin-top: 4px;
}
div span.wiz_input_script
{
	width: 61%;
	margin-left:5px;
	margin-top: 4px;
}

div.main_internet_next_button
{
	margin-left:600px;
}
span.main_internet_next_button
{
	margin-left:730px;
}

div.main_internet_process_bar
{
	text-align:center;
	margin-top:50px;
	margin-bottom:50px;
}

.name_ex
{
    color: white;
	font-weight: bold;
    text-align: left;
    font-size: 15px;
    position: absolute;
	bottom: 5px;
	left: 70px;
}

.value_ex
{
    color: white;
    margin-top: 4px;
    position: absolute;
    left: 300px;
}

div.textinput_ex
{
    clear: both;
	color: white;
    position: relative;
    height: 35px;
    line-height: 35px;
    *line-height: 17px;/*For IE 7*/
}

.internet_list_item{height:354px;margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;}
.internet_list_item .item1{position:absolute;z-index:1;left:70px;top:15px;color:grey;font-weight: bold;font-size:17px}
.internet_list_item .item2{position:absolute;left:0px;}
.internet_list_item .item3{position:absolute;left:660px;bottom:-259px;}

.internet_status_item{margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;text-align:left;}
.internet_status_item .item1{position:absolute;top:15px;}
.internet_status_item .item2{position:absolute;text-align:left;left:50px;}
.internet_status_item .item3{position:absolute;text-align:left;left:630px;top:40px;}	

.final_success_item{margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;text-align:left;}
.final_success_item .item1{position:absolute;top:15px;}
.final_success_item .item2{position:absolute;text-align:left;left:50px;top:25px;}
.final_success_item .item3{position:absolute;text-align:left;top:60px;}
.final_success_item .item4{position:absolute;text-align:left;left:630px;top:160px;}

.internet_status_item_ex{margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;text-align:left;}
.internet_status_item_ex .item1{position:absolute;}
.internet_status_item_ex .item2{position:absolute;text-align:left;left:60px;top:5px;width:670px;}
.internet_status_item_ex .item3{position:absolute;text-align:left;left:630px;top:50px;}

.internet_status_item_ex_ex{height:34px;margin:0px 0px 0px 0px;-moz-border-radius:0px;-webkit-border-radius:0px;border:solid transparent 0px;position:relative;text-align:left;}
.internet_status_item_ex_ex .item1{position:absolute;top:0px;}
.internet_status_item_ex_ex .item2{position:absolute;text-align:left;left:60px;top:15px;}
.internet_status_item_ex_ex .item3{position:absolute;text-align:left;left:630px;top:45px;}

</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "AUFOURA83",
	OnLoad: function(){lanugage_StyleSet('<? echo $lang;?>' , "<?echo $TEMP_MYNAME; ?>");},
	OnUnload: function() {},
    OnSubmitCallback: function (code, result)
    {
        switch (code)
        {
        case "OK":
			BODY.ShowContent();
			PAGE.currentStage=10;
			PAGE.OnClickNext();
            break;
        case "BUSY":
            BODY.ShowContent();
            BODY.ShowAlert("<?echo I18N("j", "Someone is configuring the device; please try again later.");?>");
            break;
        case "HEDWIG":
            BODY.ShowContent();
            BODY.ShowAlert(result.Get("/hedwig/message"));
            break;
        case "PIGWIDGEON":
            BODY.ShowContent();
            if (result.Get("/pigwidgeon/message")==="no power")
            {
                BODY.NoPower();
            }
            else
            {
                BODY.ShowAlert(result.Get("/pigwidgeon/message"));
            }
            break;
        default:
            BODY.ShowContent();
            BODY.ShowAlert("<?echo I18N("j", "Please try again later.");?>");
            break;
        }
        return true;
    },
	InitValue: function(xml)
	{		
		this.ShowCurrentStage();
		PXML.doc = xml;
		var aufoura = PXML.FindModule("AUFOURA83");
		var aufourauid = XG(aufoura+"/text");
		PAGE.pwd = aufourauid;
		return true;
	},
	base : null,
    InitValue_ex: function(xml)
    {
        this.defaultCFGXML = xml;
        PXML.doc = xml;

		var b = PXML.FindModule("EZCFG-WAN");
		PAGE.base = b+"/wan";
		if (!b) { alert("InitValue ERROR!"); return false; }

		PAGE.base = b+"/wan";
		var wan1addrtype = XG(PAGE.base+"/mode");
        if (wan1addrtype === "STATIC")
        {
            COMM_SetSelectValue(OBJ("wan_ip_mode"), "static");
        }
        else if (wan1addrtype === "DHCP")
        {
            COMM_SetSelectValue(OBJ("wan_ip_mode"), "dhcp");
        }
        else if (wan1addrtype === "PPPOE")
            {
            var over = XG(PAGE.base+"/russia");
            if (over === "1")
                    COMM_SetSelectValue(OBJ("wan_ip_mode"), "r_pppoe");
                else
                    COMM_SetSelectValue(OBJ("wan_ip_mode"), "pppoe");
            }
        else if (wan1addrtype === "PPTP")
            {
            var over = XG(PAGE.base+"/russia");
            if (over === "1")
                    COMM_SetSelectValue(OBJ("wan_ip_mode"), "r_pptp");
                else
                    COMM_SetSelectValue(OBJ("wan_ip_mode"), "pptp");
            }
        else if (wan1addrtype === "L2TP")
            {
            var over = XG(PAGE.base+"/russia");
            if (over === "1")
                    COMM_SetSelectValue(OBJ("wan_ip_mode"), "r_l2tp");
                else
                COMM_SetSelectValue(OBJ("wan_ip_mode"), "l2tp");
            }

        /* init ip setting */
        if (!this.InitIpv4Value()) return false;
        if (!this.InitPpp4Value()) return false;

        this.OnChangeWanIpMode();
        return true;
    },
	PreSubmit: function()
	{
        switch(OBJ("wan_ip_mode").value)
        {
        case "static":
            if (!this.PreStatic()) return null;
            break;
        case "dhcp":
        case "dhcpplus":
            if (!this.PreDhcp()) return null;
            break;
        case "r_pppoe":
            if (!this.PreRPppoe()) return null;
        case "pppoe":
            if (!this.PrePppoe()) return null;
            break;
        case "r_pptp":
            if (!this.PrePptp("russia")) return null;
            break;
        case "pptp":
            if (!this.PrePptp()) return null;
            break;
        case "l2tp":
            if (!this.PreL2tp("")) return null;
            break;
        case "r_l2tp":
            if (!this.PreL2tp("russia")) return null;
            break;
        case "dslite":
            if (!this.PreDSLite()) return null;
            break;
        }
        return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	stages: new Array ("cable_check", "internet_check", "modem_unplug", "modem_unplug_after", "modem_plug", "modem_plug_after", "internet_check_again", "final_success", "cable_check_again","internet_type_select","internet_check_again_again", "final_fail_again_again"),
	connect_to_router: false,
	connect_to_modem: 0,
	connect_to_internet: false,
	countDownTime: null,
	countDownTimer: null,
	currentStage: 0,	// 0 ~ this.stages.length
	val: null,
    // The above are MUST HAVE methods ...
    ///////////////////////////////////////////////////////////////////
    defaultCFGXML: null,
    wizard_config: "<? echo query('/device/wizardconfig');?>",
	ShowCurrentStage: function()
	{
		for (var i=0; i<this.stages.length; i++)
		{
			OBJ(this.stages[i]).style.display = "none";
		}
		OBJ(this.stages[this.currentStage]).style.display = "block";
				
		if (this.stages[this.currentStage]=="internet_check")
		{
			OBJ("pc_rt_internet").style.display = "none";
			OBJ("pc_rt_fail").style.display = "none";
			OBJ("pc_rt_ok").style.display = "block";
			OBJ("rt_internet_fail").style.display = "none";
			OBJ("pc_internet_ok").style.display = "none";
			OBJ("connect_detect_info").style.display = "block";
			OBJ("connect_internet_fail").style.display = "none";
			OBJ("connect_internet_ok").style.display ="none";
			PAGE.connect_to_router = true;
			PAGE.connect_to_modem = 0;
			PAGE.connect_to_internet = false;		
			PAGE.countDownTime = 60;
			PAGE.val="";
			PAGE.COMM_GetCFG_ex();
			PAGE.Internet_check();
		}
        else if (this.stages[this.currentStage]=="internet_check_again")
        {
            OBJ("pc_rt_internet_ex").style.display = "none";
            OBJ("pc_rt_fail_ex").style.display = "none";
            OBJ("pc_rt_ok_ex").style.display = "block";
            OBJ("rt_internet_fail_ex").style.display = "none";
            OBJ("pc_internet_ok_ex").style.display = "none";
            OBJ("connect_detect_info_ex").style.display = "block";
            OBJ("connect_internet_fail_ex").style.display = "none";
            OBJ("connect_internet_ok_ex").style.display ="none";
            PAGE.connect_to_router = true;
            PAGE.connect_to_modem = 0;
            PAGE.connect_to_internet = false;
            PAGE.countDownTime = 60;
            PAGE.val="_ex";
            PAGE.Login_Hash_ex();
            PAGE.Internet_check();
        }
        else if (this.stages[this.currentStage]=="internet_check_again_again")
        {
            OBJ("pc_rt_internet_ex_ex").style.display = "none";
            OBJ("pc_rt_fail_ex_ex").style.display = "none";
            OBJ("pc_rt_ok_ex_ex").style.display = "block";
            OBJ("rt_internet_fail_ex_ex").style.display = "none";
            OBJ("pc_internet_ok_ex_ex").style.display = "none";
            OBJ("connect_detect_info_ex_ex").style.display = "block";
            OBJ("connect_internet_fail_ex_ex").style.display = "none";
            OBJ("connect_internet_ok_ex_ex").style.display ="none";
            PAGE.connect_to_router = true;
            PAGE.connect_to_modem = 0;
            PAGE.connect_to_internet = false;
            PAGE.countDownTime = 60;
            PAGE.val="_ex_ex";
			PAGE.COMM_GetCFG_ex();
            PAGE.Internet_check();
        }
		else if (this.stages[this.currentStage]=="modem_unplug_after")
		{
			this.countDownTime = 20;
			this.modem_unplug_after();
		}
        else if (this.stages[this.currentStage]=="modem_plug_after")
        {
            this.countDownTime = 20;
            this.modem_plug_after();
        }
        else if (this.stages[this.currentStage]=="final_success")
        {
    		if(PAGE.wizard_config!="1")
    		{
    			OBJ("final_success_btn_1").style.display="block";
    			OBJ("final_success_btn_2").style.display="none";
    		}
    		else
    		{
    			OBJ("final_success_btn_1").style.display="none";
    			OBJ("final_success_btn_2").style.display="block";
    		}
        }
		else 
		{
			BODY.NewWDStyle_refresh();
		}
	},
	COMM_GetCFG_for_setup_wizard_countdown : 60,
    COMM_GetCFG_for_setup_wizard: function()
    {
        var ajaxObj = GetAjaxObj("getData");

        ajaxObj.createRequest();
        ajaxObj.onCallback = function (xml)
        {
            ajaxObj.release();
            PAGE.InitValue_ex(xml);
        }
        ajaxObj.onError = function(msg)
        {
            ajaxObj.release();
			PAGE.COMM_GetCFG_for_setup_wizard_countdown--;
			if (PAGE.COMM_GetCFG_for_setup_wizard_countdown > 0)
			{
				PAGE.COMM_GetCFG_for_setup_wizard();
			}
        }
        ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest("getcfg.php", "SERVICES=EZCFG-WAN");
    },
	COMM_GetCFG_ex: function()
	{
    	var ajaxObj = GetAjaxObj("getData");

    	ajaxObj.createRequest();
    	ajaxObj.onCallback = function (xml)
    	{
        	ajaxObj.release();
			PAGE.ShowWANLink(xml);
        	BODY.NewWDStyle_refresh();
    	}
    	ajaxObj.onError = function(msg)
    	{
			ajaxObj.release();
			if (PAGE.countDownTime > 0)
			{
				PAGE.COMM_GetCFG_ex();
			}
    	}
    	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    	ajaxObj.sendRequest("getcfg.php", "SERVICES=INET.WAN-1,RUNTIME.PHYINF");
	},
	ShowWANLink: function(xml)
	{
		PXML.doc = xml;
		PAGE.connect_to_router = true;
		var wan	= PXML.FindModule("INET.WAN-1");
		var rphy = PXML.FindModule("RUNTIME.PHYINF");
		var wanphyuid = XG(wan+"/inf/phyinf");      
		var rwanphyp = GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);
        if((XG(rwanphyp+"/linkstatus")!="0") && (XG(rwanphyp+"/linkstatus")!=""))
        {
			PAGE.connect_to_modem = 1;
			Service("INTERNET_PING","RESTART");
		}
		else
		{
			PAGE.connect_to_modem = 2;
		}
		return;
	},	
	OnClickNext: function()
	{
		clearTimeout(this.countDownTimer);
		var stage = this.stages[this.currentStage];
		this.ShowCurrentStage();	
	},	
	Internet_check: function()
	{
        if (PAGE.connect_to_modem == 2)
        {
            OBJ("pc_rt_ok"+PAGE.val).style.display = "none";
            OBJ("rt_internet_fail"+PAGE.val).style.display = "block";
            OBJ("connect_detect_info"+PAGE.val).style.display = "none";
            OBJ("connect_internet_fail"+PAGE.val).style.display = "block";
            if(PAGE.val=="")
            {
            	PAGE.Logout_ex();
            }
            else if(PAGE.val=="_ex")
            {
            	PAGE.COMM_GetCFG_for_setup_wizard_countdown = 60;
            	PAGE.COMM_GetCFG_for_setup_wizard();
            }
            return;
        }
        else if(PAGE.connect_to_internet == true)
        {
            OBJ("pc_rt_ok"+PAGE.val).style.display = "none";
            OBJ("pc_internet_ok"+PAGE.val).style.display = "block";
            OBJ("connect_detect_info"+PAGE.val).style.display = "none";
            OBJ("connect_internet_ok"+PAGE.val).style.display = "block";
            return;
        }
        else if(PAGE.countDownTime == 0)
        {
            OBJ("pc_rt_ok"+PAGE.val).style.display = "none";
            OBJ("rt_internet_fail"+PAGE.val).style.display = "block";
            OBJ("connect_detect_info"+PAGE.val).style.display = "none";
            OBJ("connect_internet_fail"+PAGE.val).style.display = "block";
            if(PAGE.val=="")
            {
            	PAGE.Logout_ex();
            }
            else if(PAGE.val=="_ex")
            {
            	PAGE.COMM_GetCFG_for_setup_wizard_countdown = 60;
            	PAGE.COMM_GetCFG_for_setup_wizard();
            }
            return;
        }
		var ajaxObj = GetAjaxObj("ReadPingServiceResult");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			ajaxObj.release();
			if (xml.Get("/diagnostic/report")=="1")
			{
				PAGE.connect_to_internet = true;
				PAGE.Internet_check();//run this again to show correct webpage
			}
			else
			{
				if(PAGE.countDownTime > 0)
				{
					PAGE.countDownTime--;
					PAGE.countDownTimer = setTimeout('PAGE.Internet_check()', 1000);
				}		
			}
		}
        ajaxObj.onError = function(msg)
        {
			if(PAGE.countDownTime > 0) PAGE.Internet_check();
        }
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("diagnostic.php", "act=ReadPingServiceResult");
	},
	modem_unplug_after: function()
	{
		OBJ("modem_unplug_after_info").innerHTML = "<?echo I18N("h", "Count down");?>"+" "+this.countDownTime.toString()+" "+"<?echo I18N("h", "seconds");?>";
		if (this.countDownTime > 0)
		{
			this.countDownTime--;
			PAGE.countDownTimer = setTimeout('PAGE.modem_unplug_after()', 1000);
		}
		else 
		{
			PAGE.currentStage=4;
			PAGE.OnClickNext();		
		}
	},
	modem_plug_after: function()
	{
		OBJ("modem_plug_after_info").innerHTML = "<?echo I18N("h", "Count down");?>"+" "+this.countDownTime.toString()+" "+"<?echo I18N("h", "seconds");?>";
		if (this.countDownTime > 0)
		{
			this.countDownTime--;
			PAGE.countDownTimer = setTimeout('PAGE.modem_plug_after()', 1000);
		}
		else
		{
			PAGE.currentStage=6; 
			PAGE.OnClickNext();		
		}
	},
	check_result: function(result)
	{
		var JsonData = eval('(' + result + ')');
		if(JsonData.RESULT=="OK")
		{
			if(typeof(JsonData.AUTHORIZED_GROUP)=="undefined") AUTH.AuthorizedGroup = 0;
			else AUTH.AuthorizedGroup = parseInt(JsonData.AUTHORIZED_GROUP, 10);
			AUTH.UpdateTimeout();
			PAGE.COMM_GetCFG_ex();
		}
	},
    Logout_ex: function()
    {
        var payload = "REPORT_METHOD=xml&ACTION=logout";
        var AJAX = GetAjaxObj("login");
        AJAX.createRequest();
        AJAX.onCallback = function(xml)
		{
			AUTH.AuthorizedGroup=-1; 
			AJAX.release();
		}
        AJAX.setHeader("Content-Type", "application/x-www-form-urlencoded");
        AJAX.sendRequest("session.cgi", payload);
    },
	pwd: null,
    Login_Hash_ex: function()
    {
		var user="a"+"d"+"m"+"i"+"n";
		var pass=PAGE.pwd;

        var dummy = new Date().getTime(); //Solve the problem of Ajax GET omitted due to IE cache.
        var AJAX = GetAjaxObj("login_hash");
        AJAX.createRequest();
        AJAX.onCallback = function(json)
        {
			AJAX.release();
            AUTH.Login_Send_Digest(PAGE.check_result, user, pass, json);
        }
		AJAX.onError = function(msg)
		{
			AJAX.release();
			if (PAGE.countDownTime > 0)
			{
				PAGE.Login_Hash_ex();
			}
		}
        AJAX.setHeader("Content-Type", "application/x-www-form-urlencoded");
        AJAX.requestMethod = "GET";
        AJAX.returnXml = false;//return JSON data
        AJAX.sendRequest("authentication.cgi?"+dummy, "");
    },
    check_ip_validity: function(ipstr)
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
    },
    OnChangeWanIpMode: function()
    {
        OBJ("box_wan_static_body").style.display= "none";
        OBJ("box_wan_ipv4_common_body").style.display = "none";

        OBJ("box_wan_pppoe_body").style.display = "none";
        OBJ("box_wan_pptp_body").style.display  = "none";
        OBJ("box_wan_l2tp_body").style.display  = "none";

        switch(OBJ("wan_ip_mode").value)
        {
        case "static":
            OBJ("box_wan_static_body").style.display        = "block";
            OBJ("box_wan_ipv4_common_body").style.display   = "block";
            break;
        case "dhcpplus":
        case "dhcp":
            break;
        case "r_pppoe":
        case "pppoe":
            OBJ("box_wan_pppoe_body").style.display         = "block";
            break;
        case "r_pptp":
            OBJ("box_wan_pptp_body").style.display          = "block";
            break;
        case "pptp":
            OBJ("box_wan_pptp_body").style.display          = "block";
            break;
        case "l2tp":
            OBJ("box_wan_l2tp_body").style.display          = "block";
            break;
        case "r_l2tp":
            OBJ("box_wan_l2tp_body").style.display          = "block";
            break;
        default:
			OBJ("box_wan_pppoe_body").style.display         = "block";
            break;
        }
    },
    PreL2tp: function(type)
    {
        var cnt;
        XS(PAGE.base+"/mode","L2TP");
        if(type == "russia")    //normal l2tp
        {
            XS(PAGE.base+"/russia","1");
        }
        else
        {
            XS(PAGE.base+"/russia","0");
        }

        XS(PAGE.base+"/l2tp/username", OBJ("l2tp_username").value);
        XS(PAGE.base+"/l2tp/password", OBJ("l2tp_password").value);
        XS(PAGE.base+"/l2tp/ipaddr", OBJ("l2tp_server").value);

        return true;
    },
    PrePptp: function(type)
    {
        XS(PAGE.base+"/mode","PPTP");
        if(type == "russia")    //normal pptp
        {
            XS(PAGE.base+"/russia","1");
        }
        else
        {
            XS(PAGE.base+"/russia","0");
        }

        XS(PAGE.base+"/pptp/username", OBJ("pptp_username").value);
        XS(PAGE.base+"/pptp/password", OBJ("pptp_password").value);
        XS(PAGE.base+"/pptp/ipaddr", OBJ("pptp_server").value);

        return true;
    },
    PrePppoe: function()
    {
        XS(PAGE.base+"/mode","PPPOE");
        XS(PAGE.base+"/pppoe/username", OBJ("pppoe_username").value);
        XS(PAGE.base+"/pppoe/password", OBJ("pppoe_password").value);

        return true;
    },
    PreStatic: function()
    {
        XS(PAGE.base+"/mode","STATIC");
        XS(PAGE.base+"/static/ipaddr",  OBJ("st_ipaddr").value);
        XS(PAGE.base+"/static/subnet",  COMM_IPv4MASK2INT(OBJ("st_mask").value));
        XS(PAGE.base+"/static/gateway", OBJ("st_gw").value);

        XS(PAGE.base+"/static/dns1", OBJ("ipv4_dns1").value);
        if (OBJ("ipv4_dns2").value !== "")
        {
            XS(PAGE.base+"/static/dns2", OBJ("ipv4_dns2").value);
        }

        return true;
    },
    DecideToGoWhere: function()
    {
    	if(PAGE.wizard_config!="1") self.location.href ='main_wireless.php';
    	else self.location.href ='/';
    },
    PreDhcp: function()
    {
        XS(PAGE.base+"/mode","DHCP");
        return true;
    },
    InitIpv4Value: function()
    {
        /* static ip */
        OBJ("st_ipaddr").value  = XG(PAGE.base+"/static/ipaddr");
        OBJ("st_mask").value    = COMM_IPv4INT2MASK(XG(PAGE.base+"/static/subnet"));
        OBJ("st_gw").value      = XG(PAGE.base+"/static/gateway");
        /* dns server */

        OBJ("ipv4_dns1").value  = XG(PAGE.base+"/static/dns1") != "" ? XG(PAGE.base+"/static/dns1"): "";
        OBJ("ipv4_dns2").value  = XG(PAGE.base+"/static/dns2") != "" ? XG(PAGE.base+"/static/dns2"): "";

        return true;
    },
    InitPpp4Value: function()
    {
        /* set/clear to default */
        /* pppoe */
        OBJ("pppoe_username").value         = "";
        OBJ("pppoe_password").value         = "";
        /* pptp */
        OBJ("pptp_username").value          = "";
        OBJ("pptp_password").value          = "";
        /* l2tp */
        OBJ("l2tp_server").value            = "";
        OBJ("l2tp_username").value          = "";
        OBJ("l2tp_password").value          = "";

        /* init */
        var over = XG(PAGE.base+"/mode");
        switch (over)
        {
        case "PPPOE":
            OBJ("pppoe_username").value     = XG(PAGE.base+"/pppoe/username");
            OBJ("pppoe_password").value     = XG(PAGE.base+"/pppoe/password");
            break;
        case "PPTP":
            OBJ("pptp_server").value    = XG(PAGE.base+"/pptp/ipaddr");
            OBJ("pptp_username").value  = XG(PAGE.base+"/pptp/username");
            OBJ("pptp_password").value  = XG(PAGE.base+"/pptp/password");
            break;
        case "L2TP":
            OBJ("l2tp_server").value    = XG(PAGE.base+"/l2tp/ipaddr");
            OBJ("l2tp_username").value  = XG(PAGE.base+"/l2tp/username");
            OBJ("l2tp_password").value  = XG(PAGE.base+"/l2tp/password");
            break;
        }

        return true;
    }
}
function Service(servicename,act)
{	
	var ajaxObj = GetAjaxObj("SERVICE");
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		if (xml.Get("/report/result")!="OK") BODY.ShowAlert("Internal ERROR!\nCan not "+act+" "+servicename+" service\n"+xml.Get("/report/message"));
		else PAGE.Internet_check();
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "SERVICE="+servicename+"&ACTION="+act);
}
</script>
