<style>
div p.text_ra
{
    color: white;
    font-size: 14px;
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

.button_blue_ra
{
    color: white;
    background-image: url(/pic/<? if($lang=="es"){echo "bg_button_blueX2";} else{echo "bg_button_blueX3_ex";} ?>.png);
	background-repeat: no-repeat;
    font-size:14px;
    height:32px;
    width:<? if ($lang == "es") {echo "180px";} else {echo "253px";} ?>;
    padding:0 10px 0 10px;
    border:0px;
    margin:0px;
    -moz-border-radius: 5px; /*FireFox*/
    -webkit-border-radius: 5px; /*Chrome Safari*/
    border-radius: 5px; /*Opera*/
}

.button_black_ra
{
	cursor: pointer;
    color: gray;
    background-image: url(/pic/<? if ($lang == "es") {echo "bg_button_blackX2";} else {echo "bg_button_blackX3_ex";} ?>.png);
	background-repeat: no-repeat;
    font-size:14px;
    height:32px;
    width:<? if ($lang == "es") {echo "180px";} else {echo "253px";} ?>;
    padding:0 10px 0 10px;
    border:0px;
    margin:0px;
    -moz-border-radius: 5px; /*FireFox*/
    -webkit-border-radius: 5px; /*Chrome Safari*/
    border-radius: 5px; /*Opera*/
}

div.leftline
{
    text-align: left;
}

div.r_textinput
{
	color:white;
    clear: both;
    position: relative;
    height: 35px;
	text-align: left;
    line-height: 35px;
    *line-height: 17px;/*For IE 7*/
}
div.r_textinput span.name
{
    color: white;
    text-align: left;
	margin-top: 4px;
    font-size: 14px;
}

span div.help_box_top_ex
{
    cursor: pointer;
    background: url(/pic/help_box_top_ex.png);
    height: 47px;
    width: 318px;
    padding: 0px;
    border: 0px;
    margin: 0px;
}

</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "AUFOURA83",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback_Storage: function (code, result)
	{
		return true;
	},
	InitValue: function(xml)
	{
		BODY.NewWDStyle_init();
		OBJ("mobile_access_1").style.display = "block";
		PXML.doc = xml;
		var aufoura = PXML.FindModule("AUFOURA83");
		var aufourauid = XG(aufoura+"/text");
		PAGE.pwd = aufourauid;
		get_remote_access_status();
		return true;
	},
	PreSubmit: function()
	{		
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	ra_status: 0,
    alexObj: null,
    rslt: 0,
    tmrs: 0,
	pwd: null,
	checkRemoteAccess: function()
	{
		if (PAGE.ra_status == 0)
		{
			disabledRemoteAccess(0);
		}
		else
		{
			disabledRemoteAccess(1);
		}
	},
	reBuild: function()
	{
		ajax_reBuild();
	},
    ra_message: function(type, title, message)
	{	
		var str = '<div style="height:1px;"></div>';
		if (title!="")
		{
			str += '<h1>'+title+'</h1>';
			str += '<div class="emptyline"></div>';
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
			str += '<table width="635px">';
			str += '<tr>';
			str += '<td align=left><input type="button" class="button_blue" value="<?echo i18n('NO');?>" /></td>';
			str += '<td align=right><input type="button" class="button_blue" value="<?echo i18n('YES');?>" /></td>';
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
    abortAjaxRequest: function()
    {
		if (PAGE.alexObj!=null)
		{
			PAGE.alexObj.abortAction();
		}
    },
	fwlOption: 0,
	setFirewall: function(option)
	{
		PAGE.fwlOption = option;
		save_firewall_option();		
	},
    help_display_ex: function(node)
    {
        var element = node.nextSibling;//For IE
        if(element.nodeType!=1) element = element.nextSibling;// For FF & Chrome
        element.style.display='';
    }
}

function get_remote_access_status()
{
	if (PAGE.alexObj == null && PAGE.rslt == 0)
	{
		clearTimeout(PAGE.tmrs);
		PAGE.rslt = 99;
		PAGE.alexObj = GetAjaxObj("rAccess");
    	PAGE.alexObj.createRequest();
		PAGE.alexObj.isWDOrion = true;
    	PAGE.alexObj.onError = function(msg)
    	{
			if (PAGE.alexObj != null)
			{
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
			PAGE.ra_status = 0;
			OBJ("remote_access_checkbox").checked = false;
			OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
			OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
			BODY.NewWDStyle_refresh();
        	var fwl = new String();
 			fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","Automatic");?>' />\n";
        	fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","XP Compatibility");?>' />\n";
        	OBJ("mobile_firewll_settings").innerHTML = fwl;
			if (PAGE.rslt == 99)
			{
				PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);	
			}  
			PAGE.rslt = 0;
    	}
    	PAGE.alexObj.onCallback = function (xml)
    	{
			clearTimeout(PAGE.tmrs);
			if (PAGE.alexObj != null)
			{
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
        	if (xml.Get("/device/remote_access") == "true")
        	{
				PAGE.ra_status = 1;
				OBJ("remote_access_checkbox").checked = true;
				if (xml.Get("/device/communication_status") == "portforwarded")
				{
					OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connected");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Port forwarded");?>";
				}
        		else if (xml.Get("/device/communication_status") == "relayed")
        		{
            		OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connected");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Relayed");?>";
        		}
        		else if (xml.Get("/device/communication_status") == "disabled")
        		{
            		OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Disabled");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Undetected");?>";
        		}
        		else if (xml.Get("/device/communication_status") == "connecting")
        		{
            		OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connecting");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
        		}
				else if (xml.Get("/device/communication_status") == "failed")
				{
                	var str_ra ="<span style=\"cursor:pointer;color:white;\" onclick=\"PAGE.help_display_ex(this);\"><?echo I18N("h","Failed");?><img src=\"pic/warning_mark_small.png\"></span>";
                	str_ra +="<span style=\"position:relative;z-index:100;display:none;\">";
                	str_ra +="<div class=\"help_box\">";
                	str_ra +="<div class=\"help_box_top_ex\" onclick=\"this.parentNode.parentNode.style.display='none'\"></div>";
                	str_ra +="<div class=\"help_box_middle\">";
                	str_ra +="<div class=\"help_box_middle_text\"><?echo I18N("h","Remote connection failed. Please check your network connection; then make sure the latest firmware is installed on your router.");?></div>";
                	str_ra +="</div>";
                	str_ra +="<div class=\"help_box_bottom\"></div>";
                	str_ra +="</div>";
                	str_ra +="</span>";
					OBJ("remote_access_status").innerHTML= str_ra;
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
				}
				else
				{
					OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
				}
			}	
			else
			{
				PAGE.ra_status = 0;
				OBJ("remote_access_checkbox").checked = false;
				if (xml.Get("/device/communication_status") == "disabled")
				{
					OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Disabled");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Ready");?>";
				}
				else
				{
					OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
				}
			}

			BODY.NewWDStyle_refresh();
			var fwl = new String();
	
			if (xml.Get("/device/default_ports_only") == "true")
			{
            	fwl += "<input type='button' class='button_black_ra' onclick='PAGE.setFirewall(1);' value='<?echo I18N("h","Automatic");?>' />\n";
            	fwl += "<input type='button' class='button_blue_ra' value='<?echo I18N("h","XP Compatibility");?>' />\n";
			}
			else
			{
            	fwl += "<input type='button' class='button_blue_ra' value='<?echo I18N("h","Automatic");?>' />\n";
            	fwl += "<input type='button' class='button_black_ra' onclick='PAGE.setFirewall(2);' value='<?echo I18N("h","XP Compatibility");?>' />\n";
			}

			OBJ("mobile_firewll_settings").innerHTML = fwl;
			BODY.ShowContent();
			if (PAGE.rslt == 99)
			{
				PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
			}
			PAGE.rslt = 0;
    	}
		PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    	PAGE.alexObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd);
		PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 10000);
	}
}

function get_remote_access_status_repeat()
{
	if (PAGE.alexObj == null && PAGE.rslt == 0)
    {
		PAGE.rslt = 98;
		PAGE.alexObj = GetAjaxObj("rAccess");
		PAGE.alexObj.createRequest();
		PAGE.alexObj.onError = function(msg)
		{
			clearTimeout(PAGE.tmrs);
            if (PAGE.alexObj != null)
			{
                PAGE.alexObj.release();
			}
            PAGE.alexObj = null;
            if (PAGE.rslt == 98)
            {
                PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
            }
			PAGE.rslt = 0;
    	}
		PAGE.alexObj.onCallback = function (xml)
    	{
			clearTimeout(PAGE.tmrs);
            if (PAGE.alexObj != null)
            {
                PAGE.alexObj.release();
    		}
            PAGE.alexObj = null;

			if (xml.Get("/device/remote_access") == "true")
			{
				PAGE.ra_status = 1;
				if (xml.Get("/device/communication_status") == "portforwarded")
    			{
            		OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connected");?>";
                	OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Port forwarded");?>";
    			}
            	else if (xml.Get("/device/communication_status") == "relayed")
    			{
                	OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connected");?>";
                	OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Relayed");?>";
    			}		
            	else if (xml.Get("/device/communication_status") == "disabled")
            	{
            		OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Disabled");?>";
                	OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Ready");?>";
				}
            	else if (xml.Get("/device/communication_status") == "connecting")
				{
                	OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Connecting");?>";
                	OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
            	}
            	else if (xml.Get("/device/communication_status") == "failed")
    			{
                    var str_ra ="<span style=\"cursor:pointer;color:white;\" onclick=\"PAGE.help_display_ex(this);\"><?echo I18N("h","Failed");?><img src=\"pic/warning_mark_small.png\"></span>";
                    str_ra +="<span style=\"position:relative;z-index:100;display:none;\">";
                    str_ra +="<div class=\"help_box\">";
                    str_ra +="<div class=\"help_box_top_ex\" onclick=\"this.parentNode.parentNode.style.display='none'\"></div>";
                    str_ra +="<div class=\"help_box_middle\">";
                    str_ra +="<div class=\"help_box_middle_text\"><?echo I18N("h","Remote connection failed. Please check your network connection; then make sure the latest firmware is installed on your router.");?></div>";
                    str_ra +="</div>";
                    str_ra +="<div class=\"help_box_bottom\"></div>";
                    str_ra +="</div>";
                    str_ra +="</span>";
                    OBJ("remote_access_status").innerHTML= str_ra;
                    OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
				}
            	else
				{
					OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
					OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
				}
			}
			else
			{
				PAGE.ra_status = 0;
                if (xml.Get("/device/communication_status") == "disabled")
                {
                    OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Disabled");?>";
                    OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Ready");?>";
                }
                else
                {
                    OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
                    OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
                }
			}
            if (PAGE.rslt == 98)
            {
                PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
            }
			PAGE.rslt = 0;
    	}
		PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    	PAGE.alexObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd);
		PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 10000);
    }
}

function set_remote_access()
{
        var ajaxObj = GetAjaxObj("Orion_Remote_Access");
        ajaxObj.createRequest();
		ajaxObj.onError = function(msg)
		{
            ajaxObj.release()
            BODY.ShowContent();
            PAGE.rslt = 0;
            get_remote_access_status();
		}
        ajaxObj.onCallback = function (xml)
        {
			ajaxObj.release()
            BODY.ShowContent();
            PAGE.rslt = 0;
            get_remote_access_status();
        }

        ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxObj.sendRequest("adv_set_remote_status.php", "status="+ (PAGE.ra_status == 0 ? 1: 0));
}

function disabledRemoteAccess(s)
{
	clearTimeout(PAGE.tmrs);
	PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
	if (PAGE.alexObj == null && PAGE.rslt == 0)
	{
		PAGE.rslt = 97;
		PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
    	PAGE.alexObj = GetAjaxObj("rAccess");
    	PAGE.alexObj.createRequest();
		PAGE.alexObj.isWDOrion = true;
    	PAGE.alexObj.onError = function(msg)
    	{
			clearTimeout(PAGE.tmrs);
			if (msg != "")
			{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
			}
			else
			{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
			}
			if (PAGE.alexObj != null)
			{
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
			PAGE.ra_status = 0;
            OBJ("remote_access_checkbox").checked = false;
            OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
            OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
			BODY.NewWDStyle_refresh();
            var fwl = new String();
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","Automatic");?>' />\n";
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","XP Compatibility");?>' />\n";
            OBJ("mobile_firewll_settings").innerHTML = fwl;
			PAGE.rslt = 0;
			PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
    	}
    	PAGE.alexObj.onCallback = function (xml)
    	{
			clearTimeout(PAGE.tmrs);
			if (PAGE.alexObj != null)
			{
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
			set_remote_access();
			//BODY.ShowContent();
			//PAGE.rslt = 0;
			//get_remote_access_status();
    	}

		if (s)
    	{
			PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			PAGE.alexObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd+"&rest_method=PUT&remote_access=false");
			PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 40000);
    	}
    	else
    	{
			PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			PAGE.alexObj.sendRequest("/api/1.0/rest/device", "owner=admin&pw="+PAGE.pwd+"&rest_method=PUT&remote_access=true");
			PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 10000);
    	}
	}
	else 
	{
		PAGE.abortAjaxRequest();
		PAGE.tmrs = setTimeout("PAGE.checkRemoteAccess()", 2000);		
	}
}

function ajax_reBuild()
{
	clearTimeout(PAGE.tmrs);
	PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
    if (PAGE.alexObj == null && PAGE.rslt == 0)
    {
		PAGE.rslt = 96;
        PAGE.alexObj = GetAjaxObj("rAccess");
        PAGE.alexObj.createRequest();
		PAGE.alexObj.isWDOrion = true;
        PAGE.alexObj.onError = function(msg)
        {
			clearTimeout(PAGE.tmrs);
			if (msg != "")
			{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
			}
			else
			{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
			}
            if (PAGE.alexObj != null)
            {
                PAGE.alexObj.release();
            }
            PAGE.alexObj = null;
			PAGE.ra_status = 0;
            OBJ("remote_access_checkbox").checked = false;
            OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
            OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
            NewCheckRadio.refresh();
            var fwl = new String();
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","Automatic");?>' />\n";
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","XP Compatibility");?>' />\n";
            OBJ("mobile_firewll_settings").innerHTML = fwl;
            PAGE.rslt = 0;
            PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
        }
        PAGE.alexObj.onCallback = function (xml)
        {
			clearTimeout(PAGE.tmrs);
            if (PAGE.alexObj != null)
            {
                PAGE.alexObj.release();
            }
            PAGE.alexObj = null;
			BODY.ShowContent();
            PAGE.rslt = 0;
			get_remote_access_status();
    	}
		PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
    	PAGE.alexObj.sendRequest("/api/1.0/rest/miocrawler", "action=reset&owner=admin&pw="+PAGE.pwd+"&rest_method=PUT");
		PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 10000);
	}
	else
	{
        PAGE.abortAjaxRequest();
        PAGE.tmrs = setTimeout("ajax_reBuild()", 2000);
	}
}

function save_firewall_option()
{
	clearTimeout(PAGE.tmrs);
	PAGE.ra_message("", "", "<?echo I18N("h","Please Wait...");?>");
	if (PAGE.fwlOption && PAGE.alexObj == null && PAGE.rslt == 0)
	{
		PAGE.rslt = 95;
    	PAGE.alexObj = GetAjaxObj("rAccess");
		PAGE.alexObj.isWDOrion = true;
    	PAGE.alexObj.createRequest();
		
    	PAGE.alexObj.onError = function(msg)
        {
			clearTimeout(PAGE.tmrs);
			if (msg != "")
            {
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", msg);
            }
			else
           	{
				PAGE.ra_message("OK", "<?echo I18N("h","ERROR");?>", "<?echo I18N("h","500 Internal Server Error");?>");
            }
			if (PAGE.alexObj != null)
            {
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
			PAGE.ra_status = 0;
            OBJ("remote_access_checkbox").checked = false;
            OBJ("remote_access_status").innerHTML= "<?echo I18N("h","Unknown");?>";
            OBJ("remote_access_type").innerHTML= "<?echo I18N("h","Unknown");?>";
            NewCheckRadio.refresh();
            var fwl = new String();
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","Automatic");?>' />\n";
            fwl += "<input type='button' class='button_black_ra' value='<?echo I18N("h","XP Compatibility");?>' />\n";
            OBJ("mobile_firewll_settings").innerHTML = fwl;
            PAGE.rslt = 0;
            PAGE.tmrs = setTimeout("get_remote_access_status_repeat()", 10000);
        }
    	PAGE.alexObj.onCallback = function (xml)
    	{
			clearTimeout(PAGE.tmrs);
			if (PAGE.alexObj != null)
           	{
        		PAGE.alexObj.release();
			}
			PAGE.alexObj = null;
			PAGE.rslt = 0;
			get_remote_access_status();
        }
    	if (PAGE.fwlOption==1)
        {
			PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			PAGE.alexObj.sendRequest("api/1.0/rest/device", "default_ports_only=false&owner=admin&pw="+PAGE.pwd+"&rest_method=PUT");
        }
    	else if (PAGE.fwlOption==2)
        {
			PAGE.alexObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
			PAGE.alexObj.sendRequest("api/1.0/rest/device", "default_ports_only=true&owner=admin&pw="+PAGE.pwd+"&rest_method=PUT");
        }
		PAGE.tmrs = setTimeout("PAGE.abortAjaxRequest()", 10000);
	}
	else
	{
        PAGE.abortAjaxRequest();
        PAGE.tmrs = setTimeout("save_firewall_option()", 2000);
    }
}
</script>
