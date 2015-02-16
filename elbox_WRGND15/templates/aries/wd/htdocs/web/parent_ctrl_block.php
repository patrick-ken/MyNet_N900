
<html>
	<head>
		<link rel="stylesheet" href="/css/general.css" type="text/css">
		<meta http-equiv="Pragma" content="no-cache"> <!--for HTTP 1.1-->
		<meta http-equiv="Cache-Control" content="no-cache"> <!--for HTTP 1.0--> 
		<meta http-equiv="Expires" content="0"> <!--prevents caching at the proxy server-->
		<script type="text/javascript" charset="utf-8" src="./js/libajax.js"></script>
		<script type="text/javascript">			
			var category="";
			var url_all="";
			var url_host="";
			var user_mac="";
			var str_array = self.location.href.split("?");
			var str1_array = str_array[1].split("&");
			for(i=0;i<str1_array.length;i++)
			{
				if(str1_array[i].search("category=") != -1)
				{
					var str2_array = str1_array[i].split("=");
					category = unescape(str2_array[1]);
				}
				if(str1_array[i].search("mac=") != -1)
				{
					var str4_array = str1_array[i].split("=");
					user_mac = unescape(str4_array[1]);
				}							
			}
			var url_array = self.location.href.split("&url=");
			url_all = url_array[1];
			var url_all_array = url_all.split("/");
			url_host = url_all_array[2];	

			//if(category == "Block list")
			//	url_host = url_all.substring(7,url_all.length);

			function Page() {}
			Page.prototype =
			{
				OnLoad: function()
				{
					document.getElementById('url_desc').innerHTML = "URL: " + "http://"+url_host;
					document.getElementById('category_desc').innerHTML = "<? echo I18N('h', 'Category');?>: " + category;					
				},
				Apply_PWD: function()
				{
					PAGE.Count = 10;
					if(PAGE.checkPW()!=0)
					{
						alert("<?echo I18N("j", "Invalid password. Please enter a password between 6 and 20 alphanumeric characters.");?>");
						document.getElementById('pwd').focus();
						return;
					}	
					
					var pwd = document.getElementById('pwd').value;
					var override_type=document.getElementById('override_type').value;
					var override_time=document.getElementById('override_time').value;
					
					var ajaxObj = GetAjaxObj("parent_ctrl");
					ajaxObj.createRequest();
					ajaxObj.onCallback = function(xml)
					{
						ajaxObj.release();
						if(xml.Get("/parent_ctrl_override/report")=="OK")
						{
							self.location.href = url_all;
						}	
						else
						{
							clearTimeout(PAGE.Count_down);
							document.getElementById('message').style.display = "";
							document.getElementById('count_down').style.display = "none";
							document.getElementById('netstar_result').innerHTML = xml.Get("/parent_ctrl_override/report");
						}	
					}
					ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
					ajaxObj.sendRequest("parent_ctrl_override.php", "pwd="+pwd+"&url_host="+url_host+"&category="+category+"&override_type="+override_type+"&override_time="+override_time+"&user_mac="+user_mac);
					document.getElementById('message').style.display = "none";
					document.getElementById('count_down').style.display = "";
					PAGE.Wait_Connect();	
				},
				Count: 10,
				Count_down: null,
				Wait_Connect: function()
				{
					document.getElementById('count_down_second').innerHTML = PAGE.Count;
					if(PAGE.Count > 0)
					{
						PAGE.Count--;
						PAGE.Count_down = setTimeout('PAGE.Wait_Connect()',1000);
					}
				},
				checkPW: function()
				{
					var i=0;
					var result=0;
					var str = document.getElementById('pwd').value;
					if(str.length<6 || str.length>20) result=-1;
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
				}					
			};
			var PAGE = new Page();
		</script>
	</head>
	<body onload="PAGE.OnLoad();" style="background-color:black;color:white;">
		<div id="mbox" class="msg_body">
			<div>
				<div class="msg_box">
					<div class="msg_box_top"></div>
					<div class="msg_box_middle">
						<div class="emptyline"></div>
						<div><img src="/pic/mark.png"></div>
						<div class="emptyline"></div>	
						<div id="message" style="width:620px;">
							<span style="text-align:left;">
								<div><? echo I18N("h", "This content is blocked by parental control policy set by your administrator because it belongs to following category.");?></div>
								<div class="emptyline"></div>
								<div id="url_desc"></div>
								<div id="category_desc"></div>
								<div class="emptyline"></div>
								<div><? echo I18N("h", "Enter the password to override the policy temporarily and gain access to the URL.");?></div>
								<div class="emptyline"></div>
								<div>
									<span><? echo I18N("h", "Password");?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
									<span>
										<input type="password" id="pwd"/>
									</span>
								</div>
								<div class="emptyline"></div>
								<div>
									<span><? echo I18N("h", "Override Type");?>:</span>
									<span>
										<select id="override_type">
											<option value="url">URL</option>
											<option value="category"><? echo I18N("h", "Category");?></option>							
										</select>
									</span>
								</div>
								<div class="emptyline"></div>													
								<div>
									<span><? echo I18N("h", "Override Time");?>:</span>
									<span>
										<select id="override_time">
											<option value="1"><? echo I18N("h", "One time");?></option>
											<option value="1hour"><? echo I18N("h", "1 hour");?></option>
											<option value="2hour"><? echo I18N("h", "2 hours");?></option>
											<option value="1day"><? echo I18N("h", "1 day");?></option>
											<option value="permanent"><? echo I18N("h", "Permanent (essentially moves the URL to a Safe List)");?></option>							
										</select>
									</span>
								</div>							
								<div class="emptyline"></div>
								<div><? echo I18N("h", "Or contact your administrator to ask for access to the URL.");?></div>													
								<div class="emptyline"></div>
								<div id="netstar_result" style="color:red;"></div>
								<div class="emptyline"></div>
								<div style="text-align:right;">
									<input type="button" class="button_blue" onclick="PAGE.Apply_PWD()" value='<? echo I18N("h", "Apply");?>'>
									<input type="button" class="button_blueX2" onclick="window.history.go(-1);" value='<? echo I18N("h", "Go Back");?>'>
								</div>						
							</span>
						</div>
						<div id="count_down" style="width:620px;display:none;">
							<span style="text-align:center;">
								<div>
									<span><? echo I18N("h", "Please wait");?></span>
									<span id="count_down_second"></span>
									<span>&nbsp;<? echo I18N("h", "seconds");?></span>
								</div>
							</span>						
						</div>
						<div class="emptyline"></div>		
					</div>
					<div class="msg_box_bottom"></div>
				</div>
			</div>
			<br>
			<br>
			<br>
		</div>
	</body>
</html>
