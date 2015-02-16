
<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "STORAGE",
	OnLoad: function(){},
	OnUnload: function() {},
	OnSubmitCallback: function() {},
	InitValue: function(xml)
	{		
		PXML.doc = xml;
		this.USB_1 = new Array();
		this.USB_2 = new Array();
		this.GetStorage();
		this.BuildStorageTable();
				
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
	USB: null,
	USB_1: null,
	USB_2: null,
	USB1_used: false,
	USB2_used: false,
	EjectTdID: null,
	EjectCallback_n: 0,
	WD_model: "<? echo $FEATURE_MODEL_NAME;?>",
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
			if(this.WD_model=="storage" && usb==2) continue; //For storage model, USB2 is internal HDD which could not be removed.
			if(this.USB.length!==0)
			{
				for (var i=0; i < this.USB.length; i++)
				{
					if(this.USB[i].lock_status == "LOCK") continue; //If the WD storage is locked, it is already unmount.
					usb_n++;
					var uid	= "USB_"+usb_n;
					var usb_port = "";
					if(i==0)
					{
						if(usb==1) usb_port = "USB1";
						else if(usb==2) usb_port = "USB2";
					}
					var usb_name= this.USB[i].name;
					var vendor = this.USB[i].vendor;
					var model = this.USB[i].model;
					var serial_number = this.USB[i].serial_number;
					var td_id = uid+"_2";
					var td_button_id = td_id+"_b";
					var eject = '<input type="button" class="button_blueX2" id="'+td_button_id+'" value="<? echo I18N("h", "Eject"); ?>" onclick="PAGE.EjectUSB(\''+td_id+'\', \''+vendor+'\', \''+model+'\', \''+serial_number+'\');" />';
					
					var data	= [usb_port, usb_name, eject];
					var type	= ["text", "text", ""];
					BODY.InjectTable("storage_list", uid, data, type);
				}				
			}		
		}
	},
	EjectUSB: function(td_id, vendor, model, serial_number)
	{
		if (!confirm("<?echo I18N('j', 'Please make sure you want to eject this storage. Some router services (DLNA, SAMBA ,iTunes and FTP) would be stopped temporarily.');?>")) return;
		
		this.EjectTdID = td_id;
		var	table = OBJ("storage_list");
		var rows = table.getElementsByTagName("tr");
		for(var i=1; i < rows.length; i++) //i=0 is for th,  i>0 is for td 
		{
			var td_button_id = rows[i].id + "_2_b"
			if(OBJ(td_button_id)!==null) OBJ(td_button_id).disabled = true;
		}
		BODY.NewWDStyle_refresh();
		
		var self = this;
		var payload = "act=SET&vendor="+escape(vendor)+"&model="+escape(model)+"&serial_number="+escape(serial_number);		
		var ajaxObj = GetAjaxObj("EjectUSB");
		ajaxObj.release();
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			if (xml.Get("/safe_remove/report")==="OK")
			{
				setTimeout('PAGE.EjectUSBCallback()',2000);
			}
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("safe_remove.php", payload);		
	},
	EjectUSBCallback: function()
	{
		PAGE.EjectCallback_n++;
		
		var self = this;
		var payload = "act=GET";
		var ajaxObj = GetAjaxObj("EjectUSBCallback");
		ajaxObj.release();
		ajaxObj.createRequest();
		ajaxObj.onCallback = function(xml)
		{
			if (xml.Get("/safe_remove/unmount")!=="1")
			{
				if(PAGE.EjectCallback_n < 20) setTimeout('PAGE.EjectUSBCallback()',500);
				else PAGE.EjectUSBResult(false);
			}
			else PAGE.EjectUSBResult(true);	
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("safe_remove.php", payload);		
	},
	EjectUSBResult: function(result)
	{
		var	table = OBJ("storage_list");
		var rows = table.getElementsByTagName("tr");
		for(var i=1; i < rows.length; i++) //i=0 is for th,  i>0 is for td 
		{
			var td_button_id = rows[i].id + "_2_b"
			if(OBJ(td_button_id)!==null) OBJ(td_button_id).disabled = false;
		}
		BODY.NewWDStyle_refresh();		
		
		if(result)
		{
			OBJ(this.EjectTdID).innerHTML = "<? echo I18N("h", "The USB device is safe to remove.");?>";
		}			
	}	
}
</script>
