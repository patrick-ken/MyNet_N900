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
    color: white;
    background-image: url(/pic/bg_button_blueX2.png);
    font-size:15px;
    height:28px;
    padding:0 10px 0 10px;
    border:0px;
    margin:0px;
    -moz-border-radius: 5px; /*FireFox*/
    -webkit-border-radius: 5px; /*Chrome Safari*/
    border-radius: 5px; /*Opera*/
}

div input.button_black_ra
{
	cursor: pointer;
    color: gray;
    background-image: url(/pic/bg_button_blackX2.png);
    font-size:15px;
    height:28px;
    padding:0 10px 0 10px;
    border:0px;
    margin:0px;
    -moz-border-radius: 5px; /*FireFox*/
    -webkit-border-radius: 5px; /*Chrome Safari*/
    border-radius: 5px; /*Opera*/
}

hr{width:90%;margin:0;border:0;height:1px;background-color:#999}
.mobileaccount_list_item{height:38px;margin:5px 5px 2px 5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:solid transparent 1px;position:relative}
.mobileaccount_list_item .mobileaccount{margin-left:30px;margin-top:2px;color:#616264;font-size:12px}
.mobileaccount_list_item .device_name{font-weight:bold;position:absolute;width:160px;overflow:hidden;white-space:nowrap}
.mobileaccount_list_item .username{position:absolute;margin-bottom:3px;left:120px;}
.mobileaccount_list_item .mobile_icon{position:absolute;top:-5px;left:-35px}
.mobileaccount_list_item .trashaccount{cursor:pointer;position:absolute;right:25px;margin-bottom:3px;top:3px;opacity:.5;filter:alpha(opacity=50)}
.mobileaccount_list_item .resubmit_email{cursor:pointer;position:absolute;right:75px;margin-bottom:3px;top:3px;opacity:.5;filter:alpha(opacity=50)}
#scrollable_mobileaccount_list{position:relative;height:283px}
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
	stages: new Array ("mobile_access_1", "mobile_access_2", "mobile_access_3", "remote_access_4", "mobile_access_1"),
	currentStage: 0,	// 0 ~ this.stages.length
	wan_network_status: 0,
	remote_access: 0,
	communication_status: 0,
	mobile_id: new String(),
	mobile_code: new String(),
	action_type: 0,
	remote_access_web: 0,
	webxml: null,
	pwd: null,
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
		
		if (s==0)
		{
			this.SetStage(s);
			this.ShowCurrentStage();
		}
		else if (s==1)
		{
			if (PAGE.remote_access_web >=64)
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
		else if (s==2)
		{
			send_email(s);	
		}
        else if (s == 3)
        {
            resend_email(2);
        }
		else if (s==4)
		{
			this.SetStage(s);
			this.ShowCurrentStage();	
		}
	},
	delete_account: function(id, code)
	{
		this.mobile_id = id;
		this.mobile_code = code;
        this.action_type = 2;
		PAGE.ra_message("YES_NO", "<?echo I18N("h","Remove Web Access");?>", "<?echo I18N("h","Are you sure you want to remove web access for this device?");?>");
	},
	ra_add_delete: function()
	{
		if (this.action_type == 1)
		{
			this.action_type = 0;
    		PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
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
    		}
    		ajaxObj.onCallback = function (xml)
    		{
        		ajaxObj.release();
				set_remote_access();
				//PAGE.remote_access = 1;
				//PAGE.OnClickNext(1);
				//BODY.ShowContent();
    		}
			ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    		ajaxObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd+"&remote_access=true&rest_method=PUT");

		}
		else if (this.action_type == 2)
		{
			this.action_type = 0;
			PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
        	var ajaxObj = GetAjaxObj("raccess");
        	ajaxObj.createRequest();
			ajaxObj.isWDOrion = true;
        	ajaxObj.onError = function(msg)
        	{
            	ajaxObj.release();
				BODY.ShowContent();
        	}
        	ajaxObj.onCallback = function (xml)
        	{
            	ajaxObj.release();
				createMobileAccessLists();
				BODY.ShowContent();
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
            str += '<div align=right><input type="button" class="button_blue" value="<?echo i18n('OK');?>" onclick="BODY.ShowContent();"></div>';
        }
        else if (type=="YES_NO")
        {
            str += '<div>';
            str += '<table width="100%">';
            str += '<tr>';
            str += '<td align=left><input type="button" class="button_blue" value="<?echo i18n('NO');?>" onclick="BODY.ShowContent();"></td>';
            str += '<td align=right><input type="button" class="button_blue" value="<?echo i18n('YES');?>" onclick="PAGE.ra_add_delete();"></td>';
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
        OBJ("sender_address_remail").value = email;
        OBJ("email_string").value = email;
        this.mobile_id = id;
        this.mobile_code = code;
        this.SetStage(3);
        this.ShowCurrentStage();
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
            PAGE.OnClickNext(1);
            BODY.ShowContent();
        }
        ajaxObj.onCallback = function (xml)
        {
            ajaxObj.release();
            PAGE.remote_access = 1;
            PAGE.OnClickNext(1);
            BODY.ShowContent();
        }

        ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest("adv_set_remote_status.php", "status=1");
}

function get_remote_access_status()
{
    var ajaxObj = GetAjaxObj("raccess");
    ajaxObj.createRequest();
    ajaxObj.onError = function(msg)
    {
		ajaxObj.release();
		PAGE.communication_status = 0;
		PAGE.remote_access = 0;
		createMobileAccessLists();
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

		createMobileAccessLists();
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd);
}

function createMobileAccessLists()
{
    var ajaxObj = GetAjaxObj("raccess");
    ajaxObj.createRequest();
	ajaxObj.isWDOrion = true;
    ajaxObj.onError = function(msg)
    {
        ajaxObj.release();
		BODY.ShowContent();
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
    			mal += '<div class="mobileaccount username">' + email + '</div>\n';
                mal += "<div style=\"display:block\"><img src=\"pic/sending_icon_16x16.png\" class=\"resubmit_email\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Resends the registration email.");?>\" alt=\"\"  onclick=\"PAGE.resend_email(\'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\',\'" + xml.Get("/device_users/device_user:"+i+"/email") + "\');\"></div>\n";
				mal += "<div style=\"display:block\"><img src=\"pic/img_delete.gif\" class=\"trashaccount\" rel=\"" + xml.Get("/device_users/device_user:"+i+"/device_user_id") + "\" title=\"<?echo I18N("h","Delete Account");?>\" alt=\"\" onclick=\"PAGE.delete_account(\'"+xml.Get("/device_users/device_user:"+i+"/device_user_id")+"\',\'"+xml.Get("/device_users/device_user:"+i+"/device_user_auth_code")+"\');\"></div>\n";
    			
    			mal += '<hr style="width:100%;opacity:0.5;position:absolute;bottom:-1px">\n';
				mal += '</div>\n';
			}
		}
		OBJ("scrollable_mobileaccount_list").innerHTML = mal;
		BODY.ShowContent();
    }

	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxObj.sendRequest("/api/1.0/rest/device_user", "owner=admin&pw="+PAGE.pwd);
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
    		}
    		ajaxObj.onCallback = function (xml)
    		{
        		ajaxObj.release();
        		if (xml.Get("/device_user/status") == "success")
        		{
            		PAGE.SetStage(s);
            		PAGE.ShowCurrentStage();
        		}
				createMobileAccessLists();
				OBJ("sender_name").value = "";
				OBJ("sender_address").value = "";
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
        }

        var email_context_1 = new String();
		var email_context_2 = new String();
        email_context_1 = "/api/1.0/rest/device_user/" + PAGE.mobile_id;
		email_context_2 ="type=webuser&name=admin&device_user_auth_code=" + PAGE.mobile_code;
        email_context_2 += "&resend_email=true&owner=admin&pw="+PAGE.pwd+"&rest_method=PUT";
        email_context_2 += "&sender=" + OBJ("sender_name_remail").value + "&email=" + OBJ("sender_address_remail").value;

		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest(email_context_1, email_context_2);
    }
}

</script>
