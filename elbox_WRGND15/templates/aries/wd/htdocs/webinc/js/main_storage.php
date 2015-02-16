
<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
div.main_storage_bottom_button
{
	margin-left:490px;
}	
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "STORAGE",
	OnLoad: function(){},
	OnUnload: function() {},
	OLD_SAMBA: false,
	OLD_DLNA: false,
	OLD_ITUNE: false,
	OLD_FTP: false,
	OLD_NAME: "",
	OLD_PW: "",
	OLD_PUBLIC_SHARE: "",
	OLD_WorkName: "",
	OnSubmitCallback_Storage: function (code, result)
	{
		switch (code)
		{
			case "OK":
				this.seconds = 5;
				OBJ("unlock_pwd_input").style.display = "none";
				OBJ("unlock_countdown").style.display = "block";
				OBJ("next_button").style.display="none";
				OBJ("unlock_fail").style.display = "none";				
				this.UnLockCountDown();
				return true;
				break;
			default :
				BODY.ShowAlert("<?echo I18N("j", "The call back function has failed. Please check the connection to the router.");?>");
				return false;
		}
	},
	InitValue: function(xml)
	{	
		PXML.doc = xml;
		this.USB_1 = new Array();
		this.USB_2 = new Array();

		this.ShowCurrentStage();
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
		var p = PXML.FindModule("STORAGE");
		if(this.checkSpace(OBJ("workgroup_name").value)==1)
        {
           	BODY.ShowAlert("<?echo I18N("j", "Workgroup name can not have space.");?>");
           	OBJ("workgroup_name").focus();
           	return false;
        }
        if(this.WorkNameCheck(OBJ("workgroup_name").value)==false)
        {
        	BODY.ShowAlert("<?echo I18N("j", "Invalid workgroup name.");?>");
           	OBJ("workgroup_name").focus();
           	return false;
        }
        
        if(COMM_GetRadioValue("public_share")==="1")
		{
            XS(p+"/wd/storage/public_share", "1");
		}
        else
		{
			XS(p+"/wd/storage/public_share", "0");
        	if(OBJ("username").value=="")
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Please enter the user name.");?>");
            	OBJ("username").focus();
            	return false;
        	}
			if(this.WorkNameCheck(OBJ("username").value)==false)
			{
            	BODY.ShowAlert("<?echo I18N("j", "Invalid user name.");?>");
            	OBJ("username").focus();
            	return false;
			}
        	if(OBJ("password").value=="")
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Please enter the password.");?>");
            	OBJ("password").focus();
            	return false;
        	}
        	if(this.checkSpace(OBJ("password").value)==1)
        	{
            	BODY.ShowAlert("<?echo I18N("j", "Password can not have space.");?>");
            	OBJ("password").focus();
            	return false;
        	}
        	XS(p+"/wd/storage/username", OBJ("username").value);
        	XS(p+"/wd/storage/password", OBJ("password").value);
		}
	
		if(COMM_GetRadioValue("storage_device")==="usb1_check") var USB="USB1";
		else var USB="USB2";
		XS(p+"/wd/storage/samba/"+USB,	OBJ("en_samba").checked?"1":"0");
		XS(p+"/wd/storage/dlna/"+USB,	OBJ("en_dlna").checked?"1":"0");
		XS(p+"/wd/storage/itune/"+USB,	OBJ("en_itune").checked?"1":"0");
		XS(p+"/wd/storage/ftp/"+USB,	OBJ("en_ftp").checked?"1":"0");
		XS(p+"/wd/storage/workgroup",	OBJ("workgroup_name").value);
		
		if (PAGE.OLD_SAMBA!=OBJ("en_samba").checked)
			XS(p+"/wd/storage/samba/configured","1");
		else
			XS(p+"/wd/storage/samba/configured","0");
		
		if (PAGE.OLD_DLNA!=OBJ("en_dlna").checked)
			XS(p+"/wd/storage/dlna/configured","1");
		else
			XS(p+"/wd/storage/dlna/configured","0");
		
		if (PAGE.OLD_ITUNE!=OBJ("en_itune").checked)
			XS(p+"/wd/storage/itune/configured","1");
		else
			XS(p+"/wd/storage/itune/configured","0");
		
		if (PAGE.OLD_FTP!=OBJ("en_ftp").checked)
			XS(p+"/wd/storage/ftp/configured","1");
		else
			XS(p+"/wd/storage/ftp/configured","0");
			
		if(COMM_GetRadioValue("public_share")!=PAGE.OLD_PUBLIC_SHARE || PAGE.OLD_NAME!=OBJ("username").value || PAGE.OLD_PW!=OBJ("password").value)
			XS(p+"/wd/storage/configured","1");
		else
			XS(p+"/wd/storage/configured","0");

		if(PAGE.OLD_WorkName!=OBJ("workgroup_name").value)
			XS(p+"/wd/storage/samba/configured","1");//no need to consider 0
			
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	stages: new Array ("start_setup", "list_storage", "unlock_stage", "unlock_fail_3", "share_setup", "finish"),
	currentStage: 0,	// 0 ~ this.stages.length
	NextStage: null,
	USB: null,
	USB_1: null,
	USB_2: null,
	USB_selected: null,
	seconds: null,
	username:null,
	password:null,	
	tid:null,
	DetectInput: function()
	{
		OBJ("next_button").style.display ="none";
		clearTimeout(PAGE.tid);
		if(PAGE.stages[PAGE.currentStage]==="unlock_stage")
		{
			
			if(OBJ("unlock_pwd").value=="")
			{
				if(document.getElementById("next_button")) OBJ("next_button").style.display ="none";
			}
			else
			{
				if(document.getElementById("next_button")) OBJ("next_button").style.display ="";
			}
			PAGE.tid = setTimeout("PAGE.DetectInput();", 500);
		}
	},
	ShowCurrentStage: function()
	{
		for (i=0; i<this.stages.length; i++)
		{
			if (i==this.currentStage) OBJ(this.stages[i]).style.display = "block";
			else OBJ(this.stages[i]).style.display = "none";
		}
		if(this.stages[this.currentStage]==="list_storage") this.ShowStorage();
		if(this.stages[this.currentStage]==="unlock_stage")
		{
			PAGE.DetectInput();
			OBJ("unlock_pwd_input").style.display = "block";
			OBJ("unlock_countdown").style.display = "none";
			OBJ("unlock_fail").style.display = "none";
		}
		if(this.stages[this.currentStage]==="share_setup")
		{
			if(COMM_GetRadioValue("storage_device")==="usb2_check")
			{
				if("<? echo $FEATURE_MODEL_NAME;?>" == "storage") OBJ("hard_drive").innerHTML = '<? echo I18N("h", "Internal HDD: ");?>';
				else OBJ("hard_drive").innerHTML = "USB2";
				var USB = "USB2";
			}
			else
			{
				OBJ("hard_drive").innerHTML = "USB1";
				var USB = "USB1";
			}
			
			var p = PXML.FindModule("STORAGE");
			if(XG(p+"/wd/storage/username")=="") OBJ("username").value = this.username = "wd_user";
			else OBJ("username").value = this.username = XG(p+"/wd/storage/username");
			OBJ("password").value = this.password =XG(p+"/wd/storage/password");
			OBJ("en_samba").checked	= COMM_ToBOOL(XG(p+"/wd/storage/samba/"+USB));
			OBJ("en_dlna").checked	= COMM_ToBOOL(XG(p+"/wd/storage/dlna/"+USB));
			OBJ("en_itune").checked	= COMM_ToBOOL(XG(p+"/wd/storage/itune/"+USB));
			OBJ("en_ftp").checked	= COMM_ToBOOL(XG(p+"/wd/storage/ftp/"+USB));
			OBJ("workgroup_name").value = XG(p+"/wd/storage/workgroup");
			PAGE.OLD_SAMBA=OBJ("en_samba").checked;
			PAGE.OLD_DLNA=OBJ("en_dlna").checked;
			PAGE.OLD_FTP=OBJ("en_ftp").checked;
			PAGE.OLD_ITUNE=OBJ("en_itune").checked;
			PAGE.OLD_NAME=OBJ("username").value;
			PAGE.OLD_PW=OBJ("password").value;
			PAGE.OLD_WorkName=OBJ("workgroup_name").value;

        	if(XG(p+"/wd/storage/public_share")=="1")
        	{
            	COMM_SetRadioValue("public_share", "1");
            	PAGE.OLD_PUBLIC_SHARE = "1";
        	}
        	else
        	{
            	COMM_SetRadioValue("public_share", "0");
            	PAGE.OLD_PUBLIC_SHARE = "0";
            }
            this.PublicShareCheck();	
		}
		BODY.NewWDStyle_refresh();
	},
	SetStage: function(offset)
	{
		var length = this.stages.length;
		switch (offset)
		{
		case 3:
			if (this.currentStage < length-1)
				this.currentStage += 3;
			break;			
		case 2:
			if (this.currentStage < length-1)
				this.currentStage += 2;
			break;			
		case 1:
			if (this.currentStage < length-1)
				this.currentStage += 1;
			break;
		case -1:
			if (this.currentStage > 0)
				this.currentStage -= 1;
			break;
		case -2:
			if (this.currentStage > 1)
				this.currentStage -= 2;
			break;
		case -3:
			if (this.currentStage > 1)
				this.currentStage -= 3;
			break;					
		}
	},
	OnClickPre: function()
	{
		var stage = this.stages[this.currentStage];
		if(stage=="share_setup") this.SetStage(-3);
		else this.SetStage(-1);
		this.ShowCurrentStage();
	},	
	OnClickRefresh: function()
	{
		COMM_GetCFG(false, "STORAGE", function(xml) {PAGE.GetStorage(xml);});
		this.ShowStorage();
	},
	OnClickNext: function()
	{
		var stage = this.stages[this.currentStage];
		if (stage == "finish")
		{
			self.location.href='/main_dashboard.php';//For WD ITR 53931
			return;
		}
		else if(stage == "start_setup") 
		{
			this.NextStage = true;
			COMM_GetCFG(false, "STORAGE", function(xml) {PAGE.GetStorage(xml);});
		}
		else if(stage == "list_storage")
		{
			this.StorageSelected();
			if(this.USB_selected===null)
			{
				BODY.ShowAlert("<?echo I18N("j", "Please insert the hard drive.");?>");			
				this.SetStage(-1);
			}
			else if(this.USB_selected.lock_status==="LOCK") this.SetStage(1);
			else this.SetStage(3);
			this.ShowCurrentStage();	
		}	
		else if(stage == "unlock_stage") this.StorageUnLock();
		else if(stage == "share_setup")
		{
			var xml = this.PreSubmit();
			if(xml===false) return false;
			PXML.UpdatePostXML(xml);
			PXML.Post();
			this.SetStage(1);
			this.ShowCurrentStage();
		}
		else
		{
			this.SetStage(1);
			this.ShowCurrentStage();
		}
	},
	GetStorage: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("STORAGE");
		this.USB_1 = new Array();
		this.USB_2 = new Array();
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
						Device:			XG(s+"/entry:"+i+"/Device"),
						SD_DEVICE:		XG(s+"/entry:"+i+"/SD_DEVICE"),
						unlock_fail_time:XG(s+"/entry:"+i+"/unlock_fail_time")			
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
					var Device			=XG(s+"/entry:"+i+"/Device");
					var SD_DEVICE		=XG(s+"/entry:"+i+"/SD_DEVICE");
					var unlock_fail_time=XG(s+"/entry:"+i+"/unlock_fail_time");
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
								this.USB[j].Device = Device;
								this.USB[j].model = model;
								this.USB[j].unlock_fail_time = unlock_fail_time;						
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
								Device:			XG(s+"/entry:"+i+"/Device"),
								SD_DEVICE:		XG(s+"/entry:"+i+"/SD_DEVICE"),
								unlock_fail_time:XG(s+"/entry:"+i+"/unlock_fail_time")					
								};
							new_usb=true;
						}	
					}
					if(new_usb) usb_n++;
				}
			}
			if(usb===1) for(var k=0; k < this.USB.length; k++) this.USB_1[k] = this.USB[k];
			else if(usb===2) for(var k=0; k < this.USB.length; k++) this.USB_2[k] = this.USB[k];						
				
		}
		if(this.NextStage)
		{
			this.SetStage(1);
			this.ShowCurrentStage();
			this.NextStage = false;
		}
	},
	ShowStorage: function()
	{
		var usb_name = null;
		var usb_id = null;
		/* If the object is exist, remove it. 2011.12.26 Daniel Chen */
		
		var element_1 = document.getElementById("main_text_id_1");
		if(element_1) 
		{
			element_1.innerHTML='';
			element_1.parentNode.removeChild(element_1);//fix element_1 jerry_lai
		}
		var element_2 = document.getElementById("main_text_id_2");
		if(element_2) 
		{
			element_2.innerHTML='';
			element_2.parentNode.removeChild(element_2);//fix element_2 jerry_lai
		}
		
		for(var usb=1; usb <= 2; usb++)
		{			
			this.USB = new Array();
			if(usb===1)
			{
				for(var k=0; k < this.USB_1.length; k++) this.USB[k] = this.USB_1[k];
				usb_name = '<? echo I18N("h", "USB Port 1: ");?>';
				usb_id = "USB_1";
				var USB_span = document.createElement("span");
				USB_span.className="main_text";
				USB_span.id="main_text_id_1";
			}	
			else if(usb===2)
			{
				for(var k=0; k < this.USB_2.length; k++) this.USB[k] = this.USB_2[k];
				if("<? echo $FEATURE_MODEL_NAME;?>" == "storage") usb_name = '<? echo I18N("h", "Internal HDD: ");?>';
				else usb_name = '<? echo I18N("h", "USB Port 2: ");?>';
				usb_id = "USB_2";
				var USB_span = document.createElement("span");
				USB_span.className="main_text";
				USB_span.id="main_text_id_2";
			}
			if (this.USB.length===1)
			{
				var USB_span_inner = usb_name+"&nbsp;"+this.USB[0].name+"&nbsp;"+this.USB[0].capacity;
				USB_span.innerHTML=USB_span_inner;
				OBJ(usb_id).appendChild(USB_span);
				OBJ(usb_id).style.display = "block";
			}	
			else if (this.USB.length > 1)
			{
				var USB_span_inner = '<span>';			
				USB_span_inner = USB_span_inner + '<span>' + usb_name + '&nbsp;</span>';
				/* add style to select object to fix the length. 2011.12.27 Daniel Chen */
				USB_span_inner = USB_span_inner + '<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="usb'+usb+'_device_selected" style="width:390px" >';
				for(var i=0; i < this.USB.length; i++)
				{
					USB_span_inner = USB_span_inner + '<option value='+i+'>'+this.USB[i].name+'&nbsp;['+this.USB[i].capacity+']</option>';
				}
									
				USB_span_inner = USB_span_inner + '&nbsp;&nbsp;</select></span>';
				USB_span.innerHTML=USB_span_inner;
				OBJ(usb_id).appendChild(USB_span);				
				OBJ(usb_id).style.display = "block";
			}
			else
			{
				OBJ(usb_id).style.display = "none";
			}
		}
		
		if(OBJ("USB_1").style.display==="none" && OBJ("USB_2").style.display==="none")
		{
			OBJ("list_storage_hasusb").style.display="none";
			OBJ("list_storage_nousb").style.display="block";
			OBJ("list_storage_next").disabled=true;
		}	
		else
		{
			OBJ("list_storage_hasusb").style.display="block";
			OBJ("list_storage_nousb").style.display="none";
			OBJ("list_storage_next").disabled=false;	
		}	
		if(OBJ("USB_1").style.display!=="none") COMM_SetRadioValue("storage_device", "usb1_check");
		else COMM_SetRadioValue("storage_device", "usb2_check");
	},
	StorageSelected: function()
	{
		if(COMM_GetRadioValue("storage_device")==="usb1_check")
		{
			if(this.USB_1.length===1) this.USB_selected = this.USB_1[0];
			else if(this.USB_1.length > 1) this.USB_selected = this.USB_1[OBJ("usb1_device_selected").value];
		}
		else if(COMM_GetRadioValue("storage_device")==="usb2_check")
		{
			if(this.USB_2.length===1) this.USB_selected = this.USB_2[0];
			else if(this.USB_2.length > 1) this.USB_selected = this.USB_2[OBJ("usb2_device_selected").value];			
		}
	},
	StorageUnLock: function()
	{
		var p = PXML.FindModule("STORAGE");
		XS(p+"/wd/unlock_device", this.USB_selected.Device);
		
		var cnt = S2I(XG(p+"/wd/entry#"));
		var max_index=XG(p+"/wd/max");
		if(cnt === 0)
		{
			XS(p+"/wd/count",			cnt);
			XS(p+"/wd/entry:1/name",			this.USB_selected.name);
			XS(p+"/wd/entry:1/Vendor",			this.USB_selected.vendor);
			XS(p+"/wd/entry:1/Model",			this.USB_selected.model);
			XS(p+"/wd/entry:1/serial_number",	this.USB_selected.serial_number);
			XS(p+"/wd/entry:1/capacity",		this.USB_selected.capacity);
			XS(p+"/wd/entry:1/Device",			this.USB_selected.Device);
			XS(p+"/wd/entry:1/SD_DEVICE",		this.USB_selected.SD_DEVICE);
			XS(p+"/wd/entry:1/PWD",				OBJ("unlock_pwd").value);			
		}
		else
		{		
			for(var i=1; i <= cnt; i++)
			{
				var serial_num = XG(p+"/wd/entry:"+i+"/serial_number");
				if(serial_num!=="" && serial_num===this.USB_selected.serial_number)
				{
					XS(p+"/wd/entry:"+i+"/name",		this.USB_selected.name);
					XS(p+"/wd/entry:"+i+"/Vendor",		this.USB_selected.vendor);
					XS(p+"/wd/entry:"+i+"/Model",		this.USB_selected.model);
					XS(p+"/wd/entry:"+i+"/capacity",	this.USB_selected.capacity);
					XS(p+"/wd/entry:"+i+"/Device",		this.USB_selected.Device);
					XS(p+"/wd/entry:"+i+"/SD_DEVICE",	this.USB_selected.SD_DEVICE);
					XS(p+"/wd/entry:"+i+"/PWD",			OBJ("unlock_pwd").value);								
				}
				else if(i===cnt)
				{
					var new_entry_n = i+1;
					XS(p+"/wd/entry:"+new_entry_n+"/name",			this.USB_selected.name);
					XS(p+"/wd/entry:"+new_entry_n+"/Vendor",		this.USB_selected.vendor);
					XS(p+"/wd/entry:"+new_entry_n+"/Model",			this.USB_selected.model);
					XS(p+"/wd/entry:"+new_entry_n+"/serial_number",	this.USB_selected.serial_number);
					XS(p+"/wd/entry:"+new_entry_n+"/capacity",		this.USB_selected.capacity);
					XS(p+"/wd/entry:"+new_entry_n+"/Device",		this.USB_selected.Device);
					XS(p+"/wd/entry:"+new_entry_n+"/SD_DEVICE",		this.USB_selected.SD_DEVICE);
					XS(p+"/wd/entry:"+new_entry_n+"/PWD",			OBJ("unlock_pwd").value);
					if(cnt > max_index)
					{
						XD(p+"/wd/entry:1");
					}	
				}		
			}
		}
		//If the storage is unplug, clear the relative nodes.
		var wd_cnt = S2I(XG(p+"/wd/entry#"));
		var usb1_cnt = S2I(XG(p+"/runtime/wd/USB1/entry#"));
		var usb2_cnt = S2I(XG(p+"/runtime/wd/USB2/entry#"));
		for(var i=wd_cnt; i >= 1; i--)
		{
			var storage_plug = false;
			var wd_name = XG(p+"/wd/entry:"+i+"/name");
			var wd_serial_number = XG(p+"/wd/entry:"+i+"/serial_number");
			for(var j=1; j <= usb1_cnt; j++)
			{
				if(wd_name==XG(p+"/runtime/wd/USB1/entry:"+j+"/name") && wd_serial_number==XG(p+"/runtime/wd/USB1/entry:"+j+"/serial_number"))
				{	
					storage_plug = true;
					break;
				}
			}
			if(!storage_plug)
			{
				for(var j=1; j <= usb2_cnt; j++)
				{
					if(wd_name==XG(p+"/runtime/wd/USB2/entry:"+j+"/name") && wd_serial_number==XG(p+"/runtime/wd/USB2/entry:"+j+"/serial_number"))
					{	
						storage_plug = true;
						break;
					}
				}
			}	
			if(!storage_plug) XD(p+"/wd/entry:"+i);
		}
		PXML.ActiveModule("STORAGE");
		var xml = PXML.doc;
		PXML.UpdatePostXML(xml);
		PXML.Post(function(code, result){PAGE.OnSubmitCallback_Storage(code,result);});		
	},
	UnLockCountDown: function()
	{
		if(PAGE.seconds > 0)
		{
			OBJ("count_down_second").innerHTML = PAGE.seconds;
			PAGE.seconds--;		
			setTimeout('PAGE.UnLockCountDown()', 1000);
		}
		else COMM_GetCFG(false, 'STORAGE', PAGE.StorageUnLockResult);
	},
	StorageUnLockResult: function(xml)
	{
		PAGE.GetStorage(xml);
		PAGE.StorageSelected();
		if(PAGE.USB_selected.lock_status==="LOCK")
		{
			if(PAGE.USB_selected.unlock_fail_time < 5)
			{
				OBJ("unlock_pwd_input").style.display = "block";
				OBJ("unlock_countdown").style.display = "none";
				OBJ("next_button").style.display = "";
				OBJ("unlock_fail").style.display = "block";
			}
			else
			{
				PAGE.SetStage(1);
				PAGE.ShowCurrentStage();	
			}		
		}	
		else
		{		
			PAGE.SetStage(2);
			PAGE.ShowCurrentStage();
		}
	},
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
	}			
}

function SetButtonDisabled(name, disable)
{
	var button = document.getElementsByName(name);
	for (i=0; i<button.length; i++)	button[i].disabled = disable;
}
</script>
