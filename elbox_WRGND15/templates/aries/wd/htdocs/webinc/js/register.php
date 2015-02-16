<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: null,
	OnLoad: function()
	{
		if(this.register_config == "1") OBJ("register_result").innerHTML = "<? echo i18n('Your router has already been registered. You can re-register by clicking on Register button after entering name and email address.');?>";
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return true; },
	InitValue: function(xml) { return true; },
	PreSubmit: function() { return null; },
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	register_config: "<? echo query('/runtime/devdata/register');?>",
	Register: function()
	{
		if(!this.ErrorCheck()) return;
			
		var self = this;
		var payload = "fn="+OBJ("first_name").value+"&ln="+OBJ("last_name").value+"&e="+OBJ("email").value+"&optin=0";		
		var ajaxObj = GetAjaxObj("register_send");
		
		OBJ("b_register").disabled = true;

		ajaxObj.release();
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			var register_result = xml.Get("/string");
			switch(register_result)
			{
			case "success":
				OBJ("register_result").innerHTML = "<? echo I18N('h', 'Thank you. Your registration has been submitted.');?>";
				PAGE.Register_set();
				break;
			case "registered":
				OBJ("register_result").innerHTML = "<? echo i18n('The product has already been registered. You may log into your account at <a href=\'http://register.wdc.com/\' target=\'_blank\'>http://register.wdc.com</a> to view or update your registration profile.');?>";
				break;
			case "pending":
				OBJ("register_result").innerHTML = "<? echo I18N('h', 'Thank you for your registration submission. Please check your email to complete the registration.');?>";
				break;				
			case "nosuccess":
				OBJ("register_result").innerHTML = "<? echo i18n('Registration failed. Please go to <a href=\'http://register.wdc.com/\' target=\'_blank\'>http://register.wdc.com</a> to register your product.');?>";
				break;				
			default:
				OBJ("register_result").innerHTML = "<? echo I18N('h', 'Registration Failed.');?>";
				break;
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("register_send.php", payload);
		setTimeout('OBJ("b_register").disabled = false',5000);
	},
	Register_set: function()
	{
		var self = this;
		var payload = "result=success";	
		var ajaxObj = GetAjaxObj("register_set");
		ajaxObj.release();
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml){}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("register_set.php", payload);
	},	
	ErrorCheck: function()
	{
		if(OBJ("first_name").value=="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Please enter the first name.");?>");
			OBJ("first_name").focus();
			return false;
		}
		if(OBJ("last_name").value=="")
		{
			BODY.ShowAlert("<?echo I18N("j", "Please enter the last name.");?>");
			OBJ("last_name").focus();
			return false;
		}		
		/*
			There are four checks for email address.
			1. Only one '@' 
			2. At least one '.' 
			3. At least one character before the '@'. 
			4. At least two characters after the '@'.
		*/
		var email = OBJ("email").value;
		if(email.match("@")!="@" || (email.lastIndexOf("@")!=email.indexOf("@")))
		{
			BODY.ShowAlert("<?echo I18N("j", "The email address is invalid.");?>");
			OBJ("email").focus();
			return false;
		}
		if(email.indexOf(".")==-1 || email.indexOf("@") < 1 || (email.indexOf("@")+3) > email.length)
		{
			BODY.ShowAlert("<?echo I18N("j", "The email address is invalid.");?>");
			OBJ("email").focus();
			return false;
		}		
		
		return true;
	}	
}
</script>
