
<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div.main_storage_bottom_button
{
	margin-left:550px;
}
span.text
{
	color: white;
  font-size: 12px;
	text-align: left;
	padding:0px;
	border:0px;
	margin:0px;	
}
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "STORAGE",
	OnLoad: function(){lanugage_StyleSet('<? echo $lang;?>' , "<?echo $TEMP_MYNAME; ?>");},
	OnUnload: function() {},
	OnSubmitCallback: function() {},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.storage = PXML.FindModule("STORAGE");
		this.USB_1 = new Array();
		this.USB_2 = new Array();
		this.GetStorage();
		this.BuildStorageTable();
		if(XG(this.storage+"/runtime/wd/USB1/entry#")!=0) this.USB1_used = true;
		else this.USB1_used = false;
		if(XG(this.storage+"/runtime/wd/USB2/entry#")!=0) this.USB2_used = true;
		else this.USB2_used = false;

		if(this.USB1_used)
		{
			OBJ("USB1").style.display="";
			OBJ("en_samba1").checked=(XG(this.storage+"/wd/storage/samba/USB1")=="1")?true:false;
			OBJ("en_dlna1").checked=(XG(this.storage+"/wd/storage/dlna/USB1")=="1")?true:false;
			OBJ("en_itune1").checked=(XG(this.storage+"/wd/storage/itune/USB1")=="1")?true:false;
			OBJ("en_ftp1").checked=(XG(this.storage+"/wd/storage/ftp/USB1")=="1")?true:false;
		}
		if(this.USB2_used)
		{
			OBJ("USB2").style.display="";
			OBJ("en_samba2").checked=(XG(this.storage+"/wd/storage/samba/USB2")=="1")?true:false;
			OBJ("en_dlna2").checked=(XG(this.storage+"/wd/storage/dlna/USB2")=="1")?true:false;
			OBJ("en_itune2").checked=(XG(this.storage+"/wd/storage/itune/USB2")=="1")?true:false;
			OBJ("en_ftp2").checked=(XG(this.storage+"/wd/storage/ftp/USB2")=="1")?true:false;
		}

		PAGE.OLD_WorkName = OBJ("workgroup_name").value = XG(this.storage+"/wd/storage/workgroup");
		PAGE.OLD_NAME = OBJ("username").value = this.username = XG(this.storage+"/wd/storage/username");
		PAGE.OLD_PW = OBJ("password").value = this.password = XG(this.storage+"/wd/storage/password");

        if(XG(this.storage+"/wd/storage/public_share")=="1")
		{
            COMM_SetRadioValue("public_share", "1");
            PAGE.OLD_PUBLIC_SHARE = "1";
		}
        else
        {
            COMM_SetRadioValue("public_share", "0");
            PAGE.OLD_PUBLIC_SHARE = "0";
        }

		this.Bt_value=0;
		this.OnClickInfo();
		this.PublicShareCheck();
		return true;
	},
	checkSpace: function(str)
	{
		var i=0;
		var result=0;

		for(i=0;i<str.length;i++) 
		{ 
			if(str.charAt(i)==' ')
			{ 
				result=1; 
				break;
			}
		}
		return result;
	},
	WorkNameCheck: function(str)
	{
		var illegal =new Array('/','|','\\','*','+','?','.',':',';','=','`','^',',','~');
		for (var i = 0; i < illegal.length; i+=1)
		{
			for (var j = 0; j < str.length; j+=1)
			{
				if(str.charAt(j)==illegal[i])
					return false;
			}		
		}
		return true;
	},	
	PreSubmit: function()
	{
		//if (!this.PreDLNA()) return null;
		if(this.checkSpace(OBJ("workgroup_name").value)==1)
        {
           	BODY.ShowAlert("<?echo I18N("j", "Workgroup name can not have space.");?>");
           	OBJ("workgroup_name").focus();
           	return null;
        }
        if(this.WorkNameCheck(OBJ("workgroup_name").value)==false)
        {
        	BODY.ShowAlert("<?echo I18N("j", "Invalid workgroup name.");?>");
           	OBJ("workgroup_name").focus();
           	return null;
        }
		if(COMM_GetRadioValue("public_share")==="1") 
		{
			XS(this.storage+"/wd/storage/public_share", "1");
		}
        else 
		{
			XS(this.storage+"/wd/storage/public_share", "0");
        	if(OBJ("username").value=="")
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Please enter the user name.");?>");
            	OBJ("username").focus();
            	return null;
        	}
			if(this.WorkNameCheck(OBJ("username").value)==false)
			{
            	BODY.ShowAlert("<?echo I18N("j", "Invalid user name.");?>");
            	OBJ("username").focus();
            	return null;
			}
        	if(OBJ("password").value=="")
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Please enter the password.");?>");
            	OBJ("password").focus();
            	return null;
        	}
        	if(this.checkSpace(OBJ("password").value)==1)
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Password can not have space.");?>");
            	OBJ("password").focus();
            	return null;
        	}
        	XS(this.storage+"/wd/storage/username", OBJ("username").value);
        	XS(this.storage+"/wd/storage/password", OBJ("password").value);
		}

		if(this.USB1_used)
		{
			XS(this.storage+"/wd/storage/samba/USB1",	OBJ("en_samba1").checked?"1":"0");
			XS(this.storage+"/wd/storage/dlna/USB1",	OBJ("en_dlna1").checked?"1":"0");
			XS(this.storage+"/wd/storage/itune/USB1",	OBJ("en_itune1").checked?"1":"0");
			XS(this.storage+"/wd/storage/ftp/USB1",		OBJ("en_ftp1").checked?"1":"0");
		}
		if(this.USB2_used)
		{
			XS(this.storage+"/wd/storage/samba/USB2",	OBJ("en_samba2").checked?"1":"0");
			XS(this.storage+"/wd/storage/dlna/USB2",	OBJ("en_dlna2").checked?"1":"0");
			XS(this.storage+"/wd/storage/itune/USB2",	OBJ("en_itune2").checked?"1":"0");
			XS(this.storage+"/wd/storage/ftp/USB2",		OBJ("en_ftp2").checked?"1":"0");
		}
		XS(this.storage+"/wd/storage/workgroup",	OBJ("workgroup_name").value);
		
		if (COMM_EqBOOL(OBJ("en_samba1").getAttribute("modified"),true)||
		COMM_EqBOOL(OBJ("en_samba2").getAttribute("modified"),true))
			XS(this.storage+"/wd/storage/samba/configured","1");
		else
			XS(this.storage+"/wd/storage/samba/configured","0");
		
		if (COMM_EqBOOL(OBJ("en_dlna1").getAttribute("modified"),true)||
		COMM_EqBOOL(OBJ("en_dlna2").getAttribute("modified"),true))
			XS(this.storage+"/wd/storage/dlna/configured","1");
		else
			XS(this.storage+"/wd/storage/dlna/configured","0");
		
		if (COMM_EqBOOL(OBJ("en_itune1").getAttribute("modified"),true)||
		COMM_EqBOOL(OBJ("en_itune2").getAttribute("modified"),true))
			XS(this.storage+"/wd/storage/itune/configured","1");
		else
			XS(this.storage+"/wd/storage/itune/configured","0");
		
		if (COMM_EqBOOL(OBJ("en_ftp1").getAttribute("modified"),true)||
		COMM_EqBOOL(OBJ("en_ftp2").getAttribute("modified"),true))
			XS(this.storage+"/wd/storage/ftp/configured","1");
		else
			XS(this.storage+"/wd/storage/ftp/configured","0");
			
		if(COMM_GetRadioValue("public_share")!=PAGE.OLD_PUBLIC_SHARE || PAGE.OLD_NAME!=OBJ("username").value || PAGE.OLD_PW!=OBJ("password").value)
			XS(this.storage+"/wd/storage/configured","1");
		else
			XS(this.storage+"/wd/storage/configured","0");
		
		if (PAGE.OLD_WorkName != OBJ("workgroup_name").value)
			XS(this.storage+"/wd/storage/samba/configured","1");//no need to consider 0
					
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	storage: null,
	USB: null,
	USB_1: null,
	USB_2: null,
	USB1_used: false,
	USB2_used: false,
	EjectTdID: null,
	EjectCallback_n: 0,
	username:null,
	password:null,
	OLD_NAME: "",
	OLD_PW: "",
	OLD_PUBLIC_SHARE:"",
	OLD_WorkName:"",
	Bt_value: 0,
	OnClickInfo:function()
	{
		if(this.Bt_value==0)
		{
			OBJ("info_bt").value="<?echo I18N("h", "Show");?>";
			this.Bt_value=1;
			OBJ("usb_info_list").style.display ="none";
		}
		else
		{
			OBJ("info_bt").value="<?echo I18N("h", "Hide");?>";
			this.Bt_value=0;
			OBJ("usb_info_list").style.display ="block";
		}
	},
	GetStorage: function()
	{
		var p = PXML.FindModule("STORAGE");
		for(var usb=1; usb <= 2; usb++)
		{
			this.USB = new Array();
			if(usb===1) s = p + "/runtime/wd/USB1";
			else if(usb===2) s = p + "/runtime/wd/USB2";
			var cnt = S2I(XG(s+"/entry#"));
			var usb_n = 1;
			//Only the newest ten HD would be listed in WD project.
			//The newest HD would be listed first.
			//For WD HD, it has three entry nodes with the same serial number to descript one HD properties.
			for(var i=cnt; i >= 1 ;i--)
			{
				if(i===cnt)
				{
					this.USB[0] = {
						name:			XG(s+"/entry:"+i+"/name"),
						vendor:			XG(s+"/entry:"+i+"/vendor"),
						model:			XG(s+"/entry:"+i+"/model"),
						serial_number:	XG(s+"/entry:"+i+"/serial_number"),
						lock_status:	XG(s+"/entry:"+i+"/lock_status"),
						capacity:		XG(s+"/entry:"+i+"/capacity"),
						SD_DEVICE:		XG(s+"/entry:"+i+"/SD_DEVICE")
						};
				}
				else
				{
					var name			=XG(s+"/entry:"+i+"/name");
					var vendor			=XG(s+"/entry:"+i+"/vendor");
					var model			=XG(s+"/entry:"+i+"/model");
					var serial_number	=XG(s+"/entry:"+i+"/serial_number");
					var lock_status		=XG(s+"/entry:"+i+"/lock_status");
					var capacity		=XG(s+"/entry:"+i+"/capacity");
					var SD_DEVICE		=XG(s+"/entry:"+i+"/SD_DEVICE");
					var new_usb = false;
					for(var j=0; j < usb_n; j++)
					{
						//Make sure it has different entry nodes for one HD or not.
						if(serial_number!=="" && serial_number===this.USB[j].serial_number)
						{
							if(capacity.length > this.USB[j].capacity.length)
							{
								this.USB[j].name	= name;
								this.USB[j].capacity= capacity;
							}
							else if(lock_status!=="")
							{
								this.USB[j].lock_status= lock_status;
							}
							break;
						}
						else if(j===usb_n-1 && usb_n < 10)
						{
							this.USB[usb_n] = {
								name:			XG(s+"/entry:"+i+"/name"),
								vendor:			XG(s+"/entry:"+i+"/vendor"),
								model:			XG(s+"/entry:"+i+"/model"),
								serial_number:	XG(s+"/entry:"+i+"/serial_number"),
								lock_status:	XG(s+"/entry:"+i+"/lock_status"),
								capacity:		XG(s+"/entry:"+i+"/capacity"),
								SD_DEVICE:		XG(s+"/entry:"+i+"/SD_DEVICE")
								};
							new_usb = true;
						}
					}
					if(new_usb)	usb_n++;
				}
			}
			if(usb===1) for(var k=0; k < this.USB.length; k++) this.USB_1[k] = this.USB[k];
			else if(usb===2) for(var k=0; k < this.USB.length; k++) this.USB_2[k] = this.USB[k];
		}
	},
	BuildStorageTable: function()
	{
		var usb_n = 0;
		for(var usb=1; usb <= 2; usb++)
		{
			this.USB = new Array();
			if(usb===1) for(var k=0; k < this.USB_1.length; k++) this.USB[k] = this.USB_1[k];
			else if(usb===2) for(var k=0; k < this.USB_2.length; k++) this.USB[k] = this.USB_2[k];
			if(this.USB.length!==0)
			{
				for (var i=0; i < this.USB.length; i++)
				{
					usb_n++;
					var uid	= "USB_"+usb_n;
					var usb_port = "";
					if(i==0)
					{
						if(usb==1) usb_port = "USB1";
						else if(usb==2) usb_port = "<? if ($FEATURE_MODEL_NAME=='storage'){echo I18N('h', 'Internal HDD');} else {echo 'USB2';}?>";
					}
					var usb_name= this.USB[i].name;
					var vendor = this.USB[i].vendor;
					var model = this.USB[i].model;
					var serial_number = this.USB[i].serial_number;

					var data	= [usb_port, usb_name];
					var type	= ["text", "text"];
					BODY.InjectTable("storage_list", uid, data, type);
				}
			}
		}
	},	
	/*
	PreDLNA: function()
	{
		if (COMM_Equal(OBJ("en_dlna1").getAttribute("modified"), "true") ||
		    COMM_Equal(OBJ("en_dlna2").getAttribute("modified"), "true") )
		//if (OBJ("en_dlna1").checked || OBJ("en_dlna2").checked)
		{
			//alert("PreDLNA ActiveModule DLNASCAN");
			PXML.ActiveModule("DLNASCAN");
			//PXML.ActiveModule("DLNA");		// Active: service restart.
			//PXML.DelayActiveModule("DLNASCAN", "3");
		}
		else
		{
			PXML.IgnoreModule("DLNA");		// Ignore: do nothing.
			PXML.IgnoreModule("DLNASCAN");
		}

		return true;
	},
	*/
	PublicShareCheck: function()
	{
		if(COMM_GetRadioValue("public_share")==="1")
		{
			OBJ("username").disabled = true;
			OBJ("password").disabled = true;
			OBJ("username").value = this.username;
			OBJ("password").value = this.password;
		}
		else
		{
			OBJ("username").disabled = false;
			OBJ("password").disabled = false;			
		}			
	},
	OnClickReb: function()
	{
		Service("DLNA.REBUILD");
	}
}
function Service(svc)
{	
	var ajaxObj = GetAjaxObj("SERVICE");
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+svc);
	var msgArray =
	[
		"<div style='text-align:center;'><img src='pic/process_bar.gif' /></div>",
		"<?echo I18N('h', 'The settings are being saved and taking effect.');?>",
		"<?echo I18N('h', 'Please wait');?> ..."
	];
	BODY.ShowCountdown("<?echo I18N('h', 'Rebuilding');?>", msgArray, 10, "http://<?echo $_SERVER['HTTP_HOST'];?>/storage.php");
}
</script>
