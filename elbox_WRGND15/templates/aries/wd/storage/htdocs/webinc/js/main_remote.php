<style>
div p.text_ra
{
    color: white;
    font-size: 16px;
    text-align: left;
    padding:0px;
    border:0px;
    margin:0px;
}

div span.text_ra
{
    color: white;
    font-size: 14px;
    text-align: left;
    padding:0px;
    border:0px;
    margin:0px;
}

div input.button_blue_ra
{
    color: black;
    background-image: url(/pic/bg_button_blueX2.png);
    font-size:19px;
	font-weight:bold;
    height:28px;
    padding:0 10px 0 10px;
    border:0px;
    margin:0px;
    -moz-border-radius: 5px; /*FireFox*/
    -webkit-border-radius: 5px; /*Chrome Safari*/
    border-radius: 5px; /*Opera*/
}

hr{width:90%;margin:0;border:0;height:1px;background-color:#999}
.mobileaccount_list_item{height:50px;margin:5px 5px 2px 5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:solid transparent 1px;position:relative}
.mobileaccount_list_item .mobileaccount{margin-left:30px;margin-top:2px;color:#616264;font-size:10px}
.mobileaccount_list_item .device_name{font-weight:bold;position:absolute;width:160px;overflow:hidden;white-space:nowrap}
.mobileaccount_list_item .account_name{position:absolute;left:170px;width:116px;overflow:hidden}
.mobileaccount_list_item .app_icon{position:absolute;bottom:4px}
.mobileaccount_list_item .username{position:absolute;margin-bottom:3px;left:130px;bottom:6px}
.mobileaccount_list_item .username_web{position:absolute;margin-bottom:3px;left:100px;}
.mobileaccount_list_item .status{position:absolute;margin-bottom:0px;right:75px}
.mobileaccount_list_item .dac{position:absolute;bottom:15px}
.mobileaccount_list_item .dac_box1,.mobileaccount_list_item .dac_box2,.mobileaccount_list_item .dac_box3{width:40px;text-align:center;border:1px solid gray;font-size:10px}
.mobileaccount_list_item .dac_expiration{position:absolute;margin-bottom:3px;left:0px;bottom:0px}
.mobileaccount_list_item .appname{position:absolute;margin-bottom:3px;left:36px;bottom:6px}
.mobileaccount_list_item .mobile_icon{position:absolute;top:-5px;left:-35px}
.mobileaccount_list_item .newDevice{position:absolute;top:-1px;left:-26px}
.mobileaccount_list_item .trashaccount{cursor:pointer;position:absolute;right:25px;margin-bottom:3px;top:3px;opacity:.5;filter:alpha(opacity=50)}	
.mobileaccount_list_item .resubmit_email{cursor:pointer;position:absolute;right:75px;margin-bottom:3px;top:3px;opacity:.5;filter:alpha(opacity=50)}
.helpLink img{border:0;height:14px;width:14px;margin-top:0px;margin-left:0px}
#scrollable_access_list{position:relative;height:200px;}
#scrollable_web_list{position:relative;height:200px;}
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "INET.WAN-1,RUNTIME.INF.WAN-1,RUNTIME.PHYINF,AUFOURA83",
	OnLoad: function()
	{
		this.ShowCurrentStage();
		//get_remote_access_status();
	},
	OnUnload: function() {},
	OnSubmitCallback_Storage: function (code, result)
	{
		switch (code)
		{
			case "OK":
				return true;
				break;
			default :
				BODY.ShowAlert("<?echo I18N("h","The call back function is fail. Please check the connection to the router.");?>");
				return false;
		}
	},
	InitValue: function(xml)
	{
        PXML.doc = xml;
        var wan = PXML.FindModule("INET.WAN-1");
        var rwan = PXML.FindModule("RUNTIME.INF.WAN-1");
        var rphy = PXML.FindModule("RUNTIME.PHYINF");
        var waninetuid = XG(wan+"/inf/inet");
        var wanphyuid = XG(wan+"/inf/phyinf");
        var waninetp = GPBT(wan+"/inet", "entry", "uid", waninetuid, false);
        var rwanphyp = GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);
        var rwaninetp = GPBT(rwan+"/runtime/inf", "inet", "uid", waninetuid, false);
        var aufoura = PXML.FindModule("AUFOURA83");
        var aufourauid = XG(aufoura+"/text");
        PAGE.pwd = aufourauid;
        if((XG(rwanphyp+"/linkstatus")!="0") && (XG(rwanphyp+"/linkstatus")!=""))
        {
            wancable_status=1;
        }
        if (XG(waninetp+"/addrtype") == "ipv4")
        {
            if (XG(waninetp+"/ipv4/static")== "1")
            {
                this.wan_network_status=wancable_status;
            }
            else
            {
                if ((XG(rwaninetp+"/ipv4/valid")== "1")&& (wancable_status==1))
                {
                    this.wan_network_status=1;
                }
                else if (XG(rwaninetp+"/ipv4/conflict")== "1")
                {
                    this.wan_network_status=0;
                }

            }
        }
        else if (XG(waninetp+"/addrtype") == "ppp4" || XG(waninetp+"/addrtype") == "ppp10")
        {
            var connStat = XG(rwan+"/runtime/inf/pppd/status");
            if ((XG  (rwaninetp+"/ppp4/valid")== "1")&& (wancable_status==1))
            {
                this.wan_network_status=1;
            }
            switch (connStat)
            {
                case "":
                case "disconnected":
                    this.wan_network_status=0;
                    break;
                case "on demand":
                    this.wan_network_status=0;
                    break;
                default:
                    break;
            }
        }
		OBJ("ra_button").disabled = false;
		OBJ("rw_button").disabled = false;
		get_remote_access_status();
        return true;
	},
	PreSubmit: function()
	{		
		//return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	stages: new Array ("remote_access_1", "remote_access_2","remote_access_3","remote_access_5","remote_access_6", "remote_access_7", "main_dashboard"),
	currentStage: 0,	// 0 ~ this.stages.length
    wan_network_status: 0,
    remote_access: 0,
    communication_status: 0,
    mobile_id: new String(),
    mobile_code: new String(),
    action_type: 0,
    dac_code: new String(),
	remote_access_mobile: 0,
	remote_access_web: 0,
	webxml: null,
	bflag: 0,
	rflag: 0,
	pwd: null,
	tmrs: 0,
	GoWhere: 0,
    ShowCurrentStage: function()
    {
        var found = 0;
        for (i=0; i<this.stages.length-1; i++)
        {
            if (i==this.currentStage)
            {
                found = 1;
                OBJ(this.stages[i]).style.display = "block";
            }
            else
            {
                OBJ(this.stages[i]).style.display = "none";
            }
        }
        if (!found)
        {
            OBJ(this.stages[0]).style.display = "block";
        }
	},
	SetStage: function(offset)
	{
		this.currentStage = offset;
	},
	OnClickNext: function(s)
	{
		PAGE.GoWhere = s;
		var mge = "";
		if(s == 0) 
		{
			mge = "";
            this.SetStage(s);
            this.ShowCurrentStage();
            createMobileAccessLists_access();
            PAGE.dac_code="";
		}
		else if(s == 1)
		{
			mge = "/ <?echo I18N("h","Mobile Access");?>";
			if (PAGE.remote_access_mobile >= 64)
			{
				PAGE.ra_message("OK", "<?echo I18N("h","Maximum Limitation");?>", "<?echo I18N("h","The maximum number of Mobile Access are 64.");?>");
			}
            else if (PAGE.remote_access == 0)
            {
                this.action_type = 1;
                PAGE.ra_message("YES_NO", "<?echo I18N("h","Remote Access Off");?>", "<?echo I18N("h","Remote Access is off and must be turned on to add mobile access.Do you want to turn on Remote Access?");?>");
            }
            else if (PAGE.wan_network_status == 0)
            {
                PAGE.ra_message("OK", "<?echo I18N("h","No Network Connection");?>", "<?echo I18N("h","No network connection detected.Please set up an Internet connection from Advanced Settings > WAN > Internet Setup.");?>");
            }
            else if (PAGE.communication_status == 0)
            {
                PAGE.ra_message("OK", "<?echo I18N("h","Remote Connection Failed");?>", "<?echo I18N("h","Please check your network connection; then make sure the latest firmware is installed on your router.");?>");
            }
            else
            {
                this.SetStage(s);
                this.ShowCurrentStage();
            }
		}	
		else if(s == 2)
		{
			mge = "/ <?echo I18N("h","Mobile Access");?>";
            if (PAGE.dac_code.length == 0)
            {
                get_activation_code(s);
            }
			else
			{
            	this.SetStage(s);
            	this.ShowCurrentStage();
			}
		}
		else if(s == 3)
		{
			mge = "/ <?echo I18N("h","Web Access");?>";
			if (PAGE.remote_access_web >= 64)
			{
				PAGE.ra_message("OK", "<?echo I18N("h","Maximum Limitation");?>", "<?echo I18N("h","The maximum number of Web Access are 64.");?>");
			}
            else if (PAGE.remote_access == 0)
            {
                this.action_type = 1;
                PAGE.ra_message("YES_NO", "<?echo I18N("h","Remote Access Off");?>", "<?echo I18N("h","Remote Access is off and must be turned on to add mobile access.Do you want to turn on Remote Access?");?>");
            }
            else if (PAGE.wan_network_status == 0)
            {
                PAGE.ra_message("OK", "<?echo I18N("h","No Network Connection");?>", "<?echo I18N("h","No network connection detected.Please set up an Internet connection from Advanced Settings > WAN > Internet Setup.");?>");
            }
            else if (PAGE.communication_status == 0)
            {
                PAGE.ra_message("OK", "<?echo I18N("h","Remote Connection Failed");?>", "<?echo I18N("h","Please check your network connection; then make sure the latest firmware is installed on your router.");?>");
            }
            else
            {
                this.SetStage(s);
                this.ShowCurrentStage();
            }
		}
		else if(s == 4)
		{
			mge = "/ <?echo I18N("h","Web Access");?>";
			send_email(s);
		}
		else if (s == 6)
		{
			self.location.href='/' + this.stages[s] + '.php';	
		}
		else if (s == 5)
		{
			mge = "/ <?echo I18N("h","Web Access");?>";
			resend_email(4);
		}
			
		var d = OBJ("wb_storage_remote_access");
		if (d)
		{
			d.innerHTML= mge;
		}
	},
    delete_account: function(type, id, code)
    {
        this.mobile_id = id;
        this.mobile_code = code;
        this.action_type = 2;
		if (type == 2)
		{
			PAGE.ra_message("YES_NO", "<?echo I18N("h","Remove Mobile Access");?>", "<?echo I18N("h","Are you sure you want to remove mobile access for this device?");?>");
		}
		else if (type == 3)
		{
        	PAGE.ra_message("YES_NO", "<?echo I18N("h","Remove Web Access");?>", "<?echo I18N("h","Are you sure you want to remove web access for this device?");?>");
		}
    },
    ra_add_delete: function()
    {
        if (this.action_type == 1)
        {
            this.action_type = 0;
            PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
			PAGE.bflag = 1;
            var ajaxObj = GetAjaxObj("raccess");
            ajaxObj.createRequest();
            ajaxObj.isWDOrion = true;
            ajaxObj.onError = function(msg)
            {
                ajaxObj.release();
				if (msg != "")
				{
                	PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
				}
				else
				{
					PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
				}
				PAGE.bflag = 0;
            }
            ajaxObj.onCallback = function (xml)
            {
                ajaxObj.release();
				set_remote_access();
                //PAGE.remote_access = 1;
                //PAGE.OnClickNext(1);
				//BODY.ShowContent();
				//PAGE.bflag = 0;
            }

			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
            ajaxObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd+"&remote_access=true&rest_method=PUT");

        }
        else if (this.action_type == 2)
        {
            this.action_type = 0;
            PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
			PAGE.bflag = 1;
            var ajaxObj = GetAjaxObj("raccess");
            ajaxObj.createRequest();
            ajaxObj.isWDOrion = true;
            ajaxObj.onError = function(msg)
            {
                ajaxObj.release();
				if (msg != "")
				{
					PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
				}
				else
				{
					PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
				}
				PAGE.bflag = 0;
            }
            ajaxObj.onCallback = function (xml)
            {
                ajaxObj.release();
                createMobileAccessLists_access();
                BODY.ShowContent();
				PAGE.bflag = 0;
            }

            var option_context_1 = new String();
			var option_context_2 = new String();
            option_context_1="/api/1.0/rest/device_user/" + this.mobile_id;
			option_context_2="owner=admin&pw="+PAGE.pwd+"&device_user_auth_code=" + this.mobile_code + "&rest_method=DELETE";
			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
            ajaxObj.sendRequest(option_context_1, option_context_2);
        }
    },
    ra_message: function(type, title, message)
    {
		PAGE.bflag = 1;
        var str = '<div style="height:1px;"></div>';
        if (title!="")
        {
            str += '<h1>'+title+'</h1>';
            str += '<div class="leftline">'+message+'</div>';
        }
        else
        {
            str += '<div class="emptyline"></div>';
            str += '<div class="emptyline"></div>';
            str += '<div class="emptyline"></div>';
            str += '<div class="emptyline"></div>';
            str += '<div class="emptyline"></div>';
            str += '<div class="centerline">'+message+'</div>';
        }
        str += '<div class="emptyline"></div>';
        str += '<div class="emptyline"></div>';
        str += '<div class="emptyline"></div>';
        str += '<div class="emptyline"></div>';
        str += '<div class="emptyline"></div>';
        if (type=="OK")
        {
            str += '<div align=right><input type="button" class="button_blue" value="<?echo i18n('OK');?>" onclick="PAGE.bflag=0;BODY.ShowContent();"></div>';
        }
        else if (type=="YES_NO")
        {
            str += '<div>';
            str += '<table width="100%">';
            str += '<tr>';
            str += '<td align=left><input type="button" class="button_blue" value="<?echo i18n('NO');?>" onclick="PAGE.bflag=0;BODY.ShowContent();"></td>';
            str += '<td align=right><input type="button" class="button_blue" value="<?echo i18n('YES');?>" onclick="PAGE.bflag=0;PAGE.ra_add_delete();"></td>';
            str += '</tr>';
            str += '</table>';
            str += '</div>';
        }
        OBJ("message").innerHTML = str;
        OBJ("login").style.display  = "none";
        OBJ("menu").style.display   = "none";
        OBJ("content").style.display= "none";
        OBJ("mbox").style.display   = "block";
        OBJ("mbox2").style.display   = "none";
        BODY.NewWDStyle_init();
    },
	resend_email: function(id, code, email)
	{
        var d = OBJ("wb_storage_remote_access");
        if (d)
        {
            d.innerHTML= "/ <?echo I18N("h","Web Access");?>";
		}
		OBJ("sender_address_remail").value = email;
		OBJ("email_string").value = email;
		this.mobile_id = id;
		this.mobile_code = code;
		this.SetStage(5);
		this.ShowCurrentStage();
	},
    refreashPage: function()
    {
        if (PAGE.bflag == 0 && PAGE.currentStage == 0)
        {
            PAGE.rflag = 1;
            createMobileAccessLists_access();
			PAGE.tmrs = setTimeout("PAGE.refreashPage()", 15000);
        }
        else
        {
            clearTimeout(PAGE.tmrs);
            PAGE.tmrs = setTimeout("PAGE.refreashPage()", 10000);
        }
    }
}

function set_remote_access()
{
        var ajaxObj = GetAjaxObj("Orion_Remote_Access");
        ajaxObj.createRequest();
        ajaxObj.onError = function(msg)
        {
            ajaxObj.release();
            PAGE.remote_access = 1;
            PAGE.OnClickNext(0);//go to this page's main page. JERRY_LAI
            BODY.ShowContent();
            PAGE.bflag = 0;
        }
        ajaxObj.onCallback = function (xml)
        {
            ajaxObj.release();
            PAGE.remote_access = 1;
            PAGE.OnClickNext(PAGE.GoWhere);
            BODY.ShowContent();
            PAGE.bflag = 0;
        }

        ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest("adv_set_remote_status.php", "status=1");
}

function get_remote_access_status()
{
    var ajaxObj = GetAjaxObj("raccess");
	PAGE.bflag = 1;
    ajaxObj.createRequest();
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
        PAGE.communication_status = 0;
        PAGE.remote_access = 0;
		PAGE.bflag = 0;
        createMobileAccessLists_access();
		clearTimeout(PAGE.tmrs);
		PAGE.tmrs = setTimeout("PAGE.refreashPage()", 10000);
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();

        if (xml.Get("/device/remote_access") == "true")
        {
            PAGE.remote_access = 1;
        }
        else
        {
            PAGE.remote_access = 0;
        }

        if (xml.Get("/device/communication_status") == "failed")
        {
            PAGE.communication_status = 0;
        }
        else
        {
            PAGE.communication_status = 1;
        }

		PAGE.bflag = 0;
        createMobileAccessLists_access();
		clearTimeout(PAGE.tmrs);
		PAGE.tmrs = setTimeout("PAGE.refreashPage()", 10000);
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd);
}

function get_activation_code(s)
{
    PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
	PAGE.bflag = 1;
    var ajaxObj = GetAjaxObj("raccess");
    ajaxObj.createRequest();
    ajaxObj.isWDOrion = true;
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
		if (msg != "")
		{
			PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
		}
		else
		{
			PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
		}
		PAGE.bflag = 0;
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();

        var dac= new String(xml.Get("/device_user/dac"));
        if (dac.length == 12 && xml.Get("/device_user/status") == "success")
        {
            PAGE.dac_code = dac;
            OBJ("dac_part1").value = dac.substring(0,4);
            OBJ("dac_part2").value = dac.substring(4,8);
            OBJ("dac_part3").value = dac.substring(8,12);
			PAGE.SetStage(s);
			PAGE.ShowCurrentStage();
        }
        BODY.ShowContent();
		PAGE.bflag = 0;
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device_user", "owner=admin&pw="+PAGE.pwd+"&rest_method=POST");
}

function verifyEmail()
{
    var emailRegEx = /^([\w!.%+\-])+@([\w\-])+(?:\.[\w\-]+)+$/;
    if (OBJ("sender_address").value.search(emailRegEx) == -1)
    {
        return false;
    }
    return true;
}

function send_email(s)
{
    if (OBJ("sender_name").value.length ==0)
    {
        PAGE.ra_message("OK", "<?echo I18N("h","Full Name Required");?>", "<?echo I18N("h","Full Name is required to identify the sender in email messages.");?>");
    }
    else if (OBJ("sender_address").value.length == 0)
    {
        PAGE.ra_message("OK", "<?echo I18N("h","Email Address Required");?>", "<?echo I18N("h","Email Address is required in email messages.");?>");
    }
    else if (verifyEmail()==false)
    {
        PAGE.ra_message("OK", "<?echo I18N("h","Email Address Required");?>", "<?echo I18N("h","Please enter a valid email address.");?>");
    }
    else
    {
        var found = 0;
        var cnt = PAGE.webxml.Get("/device_users/device_user#");
        for (var i = 1; i <= cnt; i++)
        {
            var dac = new String(PAGE.webxml.Get("/device_users/device_user:"+i+"/dac"));
            var email = new String(PAGE.webxml.Get("/device_users/device_user:"+i+"/email"));
            if (dac.length == 0 && email.length != 0)
            {
                if (email == OBJ("sender_address").value)
                {
                    found = 1;
                    break;
                }
            }
        }

        if (found)
        {
            PAGE.ra_message("OK", "<?echo I18N("h","Email Address Conflict");?>", "<?echo I18N("h","Email Address already exists.");?>");
        }
        else
        {
        	PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
        	var ajaxObj = GetAjaxObj("raccess");
			PAGE.bflag = 1;
        	ajaxObj.createRequest();
        	ajaxObj.isWDOrion = true;
        	ajaxObj.onError = function(msg)
        	{
            	ajaxObj.release();
				if (msg != "")
				{
            		PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
				}
				else
				{
					PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
				}
				PAGE.bflag = 0;
        	}
        	ajaxObj.onCallback = function (xml)
        	{
            	ajaxObj.release();
            	if (xml.Get("/device_user/status") == "success")
            	{
                	PAGE.SetStage(s);
                	PAGE.ShowCurrentStage();
            	}
            	BODY.ShowContent();
				PAGE.bflag = 0;
        	}

        	var email_context = new String();
        	email_context = "owner=admin&pw="+PAGE.pwd+"&rest_method=POST&sender=" + OBJ("sender_name").value + "&email=" + OBJ("sender_address").value;

			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        	ajaxObj.sendRequest("/api/1.0/rest/device_user", email_context);
		}
    }
}

function resend_email(s)
{
    if (OBJ("sender_name_remail").value.length ==0)
    {
        PAGE.ra_message("OK", "<?echo I18N("h","Full Name Required");?>", "<?echo I18N("h","Full Name is required to identify the sender in email messages.");?>");
    }
    else
    {
        PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
		PAGE.bflag = 1;
        var ajaxObj = GetAjaxObj("raccess");
        ajaxObj.createRequest();
        ajaxObj.isWDOrion = true;
        ajaxObj.onError = function(msg)
        {
            ajaxObj.release();
			if (msg != "")
			{
            	PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
			}
			else
			{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
			}
			PAGE.bflag = 0;
        }
        ajaxObj.onCallback = function (xml)
        {
            ajaxObj.release();
            if (xml.Get("/device_user/status") == "success")
            {
                PAGE.SetStage(s);
                PAGE.ShowCurrentStage();
            }
            BODY.ShowContent();
			PAGE.bflag = 0;
        }

        var email_context_1 = new String();
		var email_context_2 = new String();
		email_context_1 = "/api/1.0/rest/device_user/" + PAGE.mobile_id;
		email_context_2 = "type=webuser&name=admin&device_user_auth_code=" + PAGE.mobile_code;
		email_context_2 += "&resend_email=true&owner=admin&pw="+PAGE.pwd+"&rest_method=PUT";
		email_context_2 += "&sender=" + OBJ("sender_name_remail").value + "&email=" + OBJ("sender_address_remail").value;
	
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");	
		ajaxObj.sendRequest(email_context_1, email_context_2);
    }
}

function createMobileAccessLists_access()
{
    var ajaxObj = GetAjaxObj("raccess");
	PAGE.bflag = 1;
    ajaxObj.createRequest();
    ajaxObj.isWDOrion = true;
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
        if (PAGE.rflag)
        {
            clearTimeout(PAGE.tmrs);
            PAGE.tmrs = setTimeout("PAGE.refreashPage()", 10000);
            PAGE.rflag = 0;
        }
		PAGE.bflag = 0;
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();
        var cnt = xml.Get("/device_users/device_user#");
        var mal = new String();
		PAGE.remote_access_mobile = 0;
        for (var i = 1; i <= cnt; i++)
        {
            var dac = new String(xml.Get("/device_users/device_user:"+i+"/dac"));
            if (dac.length != 0)
            {
				PAGE.remote_access_mobile++;
                mal += '<div class="mobileaccount_list_item" id="' + xml.Get("/device_users/device_user:"+i+"/device_user_id") +'" style="display:block">\n';
                if (xml.Get("/device_users/device_user:"+i+"/active") == "1")
                {

                    if (xml.Get("/device_users/device_user:"+i+"/type") == "1")
                    {
						mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconiPhone.png" alt=""></div>\n';
					}
                    else if (xml.Get("/device_users/device_user:"+i+"/type") == "2")
                    {
						mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconiPhone.png" alt=""></div>\n';
                    }
                    else if (xml.Get("/device_users/device_user:"+i+"/type") == "3")
                    {
                        mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconiPad.png" alt=""></div>\n';
                    }
                    else if (xml.Get("/device_users/device_user:"+i+"/type") == "4")
                    {
                        mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconAndroidPhone.png" alt=""></div>\n';
                    }
                    else if (xml.Get("/device_users/device_user:"+i+"/type") == "5")
                    {
                        mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconAndroidTablet.png" alt=""></div>\n';
                    }
                    else if (xml.Get("/device_users/device_user:"+i+"/type") == "6")
                    {
                        mal += '<div class="mobileaccount mobile_icon"><img src="pic/WDSync_30x30.png" alt=""></div>\n';
                    }
                    else
                    {
						mal += '<div class="mobileaccount mobile_icon"><img src="pic/BaliIconUnknownPhone.png" alt=""></div>\n';
                    }

                    mal += '<div class="mobileaccount device_name">' + xml.Get("/device_users/device_user:"+i+"/name") + '</div>\n';
                    mal += '<div class="mobileaccount status"><?echo I18N("h","Connected");?></div>\n';

					var app = xml.Get("/device_users/device_user:"+i+"/application");
                    if (app.indexOf("WD 2go") > -1)
                    {
                        mal += '<div class="mobileaccount app_icon"><img src="pic/BaliIconWD2go.png" alt="" /></div>\n';
                        mal += '<div class="mobileaccount appname"><?echo I18N("h","WD 2go");?></div>\n';
                    }
                    else if (app.indexOf("WD Photos") > -1)
                    {
                        mal += '<div class="mobileaccount app_icon"><img src="pic/BaliIconWDPhotos.png" alt="" /></div>\n';
						mal += '<div class="mobileaccount appname"><?echo I18N("h","WD Photos");?></div>\n';
                    }
                    else
                    {
                        mal += '<div class="mobileaccount app_icon"></div>\n';
                        mal += '<div class="mobileaccount appname"></div>\n';
                    }

                    mal += '<div class="mobileaccount username">admin</div>\n';
                    mal += "<div style=\"display:block\"><img src=\"pic/img_delete.gif\" class=\"trashaccount\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Delete Account");?>\" alt=\"\" onclick=\"PAGE.delete_account(2, \'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\');\"></div>\n";
                }
                else
                {
                    mal += '<div class="newDevice mobileaccount"><img src="pic/qm_icon_16x16.png" alt=""></div>\n';
                    mal += '<div class="device_name mobileaccount"><?echo I18N("h","New Device");?></div>\n';
                    var exp_t = xml.Get("/device_users/device_user:"+i+"/dac_expiration");
                    exp_t = parseInt(exp_t) * 1000;
                    var dac_exp = new Date();
                    dac_exp.setTime(exp_t);
                    var now_t = new Date();
                    if (now_t.getTime() - exp_t > 0)
                    {
                        mal += "<div class=\"status mobileaccount\"><?echo I18N("h","Code expired");?><a href=\"#\" class=\"helpLink\" title=\"<?echo I18N("h","The activation code generated has expired. Please click Add Access to generate a new code.");?>\"><img src=\"pic/help_hover.png\" alt=\"\"></a></div>\n";
                        mal += "<div style=\"display:block\"><img src=\"pic/img_delete.gif\" class=\"trashaccount\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Delete Account");?>\" alt=\"\" onclick=\"PAGE.delete_account(2, \'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\');\"></div>\n";
                    }
                    else
                    {
                        mal += "<div class=\"status mobileaccount\"><?echo I18N("h","Waiting");?><a href=\"#\" class=\"helpLink\" title=\"<?echo I18N("h","Indicates that an activation code has been generated but has not yet been entered on your mobile or tablet device.");?>\"><img src=\"pic/help_hover.png\" alt=\"\"></a></div>\n";
                        mal += "<div style=\"display:block\"><img src=\"pic/img_delete.gif\" class=\"trashaccount\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Delete Account");?>\" alt=\"\" onclick=\"PAGE.delete_account(2, \'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\');\"></div>\n";

                        mal += '<div class="dac mobileaccount">\n';
                        mal += '<table cellpadding="0" cellspacing="0">\n';
                        mal += '<tr valign="middle">\n';
                        mal += '<td class="dac_box1">' + dac.substring(0,4) + '</td>\n';
                        mal += '<td style="padding:3px;"></td>\n';
                        mal += '<td class="dac_box2">' + dac.substring(4,8) + '</td>\n';
                        mal += '<td style="padding:3px;"></td>\n';
                        mal += '<td class="dac_box3">' + dac.substring(8,12) + '</td>\n';
                        mal += '</tr>\n';
                        mal += '</table>\n';
                        mal += '</div>\n';

                        mal += '<div class="mobileaccount dac_expiration">\n';
                        mal += '<table cellpadding="0" cellspacing="0">\n';
                        mal += '<tr>\n';
            			mal += '<td nowrap><?echo I18N("h","Code expires");?>&nbsp;<span id="dac_expiration_date">' + dac_exp.toGMTString() + '</span></td>\n';
                        mal += '</tr>\n';
                        mal += '</table>\n';
                        mal += '</div>\n';
                    }
                }

                mal += '<hr style="width:100%;opacity:0.5;position:absolute;bottom:-1px">\n';
                mal += '</div>\n';
            }
        }
        OBJ("scrollable_access_list").innerHTML = mal;
		PAGE.bflag = 0;
        if (PAGE.rflag)
        {
            clearTimeout(PAGE.tmrs);
            PAGE.tmrs = setTimeout("PAGE.refreashPage()", 10000);
            PAGE.rflag = 0;
        }
		else
		{
			createMobileAccessLists_web();
		}
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device_user", "owner=admin&pw="+PAGE.pwd);
}

function createMobileAccessLists_web()
{
    var ajaxObj = GetAjaxObj("raccess");
	PAGE.bflag = 1;
    ajaxObj.createRequest();
    ajaxObj.isWDOrion = true;
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
		PAGE.bflag = 0;
    }
    ajaxObj.onCallback = function (xml)
    {
        ajaxObj.release();
		PAGE.webxml = xml;
        var cnt = xml.Get("/device_users/device_user#");
        var mal = new String();
		PAGE.remote_access_web = 0;
        for (var i = 1; i <= cnt; i++)
        {
			var dac = new String(xml.Get("/device_users/device_user:"+i+"/dac"));
            var email = new String(xml.Get("/device_users/device_user:"+i+"/email"));
            if (dac.length == 0 && email.length != 0)
            {
				PAGE.remote_access_web++;
                mal += '<div class="mobileaccount_list_item" id="' + xml.Get("/device_users/device_user:"+i+"/device_user_id") +'" style="display:block">\n';
                mal += '<div class="mobileaccount mobile_icon"><img src="pic/email_icon_24x24.png" alt=""></div>\n';
                mal += '<div class="mobileaccount device_name"><?echo I18N("h","Email");?></div>\n';
                mal += '<div class="mobileaccount username_web">' + email + '</div>\n';
                mal += "<div style=\"display:block\"><img src=\"pic/sending_icon_16x16.png\" class=\"resubmit_email\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Resends the registration email.");?>\" alt=\"\"  onclick=\"PAGE.resend_email(\'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\',\'" + xml.Get("/device_users/device_user:"+i+"/email") + "\');\"></div>\n";
                mal += "<div style=\"display:block\"><img src=\"pic/img_delete.gif\" class=\"trashaccount\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Delete Account");?>\" alt=\"\" onclick=\"PAGE.delete_account(3, \'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\');\"></div>\n";

                mal += '<hr style="width:100%;opacity:0.5;position:absolute;bottom:-1px">\n';
                mal += '</div>\n';
            }
        }
        OBJ("scrollable_web_list").innerHTML = mal;
		PAGE.bflag = 0;
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device_user", "owner=admin&pw="+PAGE.pwd);
}

</script>
