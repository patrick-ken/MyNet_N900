
<html>
	<head>
		<link rel="stylesheet" href="/css/general.css" type="text/css">
		<meta http-equiv="Pragma" content="no-cache"> <!--for HTTP 1.1-->
		<meta http-equiv="Cache-Control" content="no-cache"> <!--for HTTP 1.0--> 
		<meta http-equiv="Expires" content="0"> <!--prevents caching at the proxy server-->
		<script type="text/javascript">
			function Page() {}
			Page.prototype =
			{
				Apply_PWD: function()
				{
					var pwd = document.getElementById('pwd').value;
					var url_host= "<? echo $_GET['url_host'];?>";
					var override_type=document.getElementById('override_type').value;
					var override_time=document.getElementById('override_time').value;
					
					/*
					var ajaxObj = GetAjaxObj("parent_ctrl");
					ajaxObj.createRequest();
					ajaxObj.onCallback = function(xml)
					{
						ajaxObj.release();
					}
					ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
					ajaxObj.sendRequest("parent_ctrl.php", "pwd="+pwd+"&url_host="+url_host+"&override_type="+override_type+"&override_time="+override_time); // parent_ctrl.php should run event, set config(url, override time, uptime)
					*/
					
					document.getElementById('message').style.display = "none";
					document.getElementById('count_down').style.display = "block";
					PAGE.Wait_Connect();
				
				},
				Count: 5,
				Wait_Connect: function()
				{
					document.getElementById('count_down_second').innerHTML = PAGE.Count;
					if(PAGE.Count > 0)
					{
						PAGE.Count--;
						setTimeout('PAGE.Wait_Connect()',1000);
					}
					else self.location.href = "http://"+"<? echo $_GET['url_all'];?>";
				}	
			};
			var PAGE = new Page();	
		</script>
	</head>
	<body style="background-color:black;color:white;">
		<div id="mbox" class="msg_body">
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
							<div>URL:<? echo $_GET["url_host"];?></div>
							<div><? echo I18N("h", "Category");?>:<? echo $_GET["category"];?></div>
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
										<option value="category"><? echo i18n("Category");?></option>							
									</select>
								</span>
							</div>
							<div class="emptyline"></div>													
							<div>
								<span><? echo I18N("h", "Override Time");?>:</span>
								<span>
									<select id="override_time">
										<option value="1"><? echo i18n("One time");?></option>
										<option value="1hour"><? echo i18n("1 hour");?></option>
										<option value="2hour"><? echo i18n("2 hours");?></option>
										<option value="1day"><? echo i18n("1 day");?></option>
										<option value="permanent"><? echo i18n("Permanent (essentially moves the URL to a Safe List)");?></option>							
									</select>
								</span>
							</div>							
							<div class="emptyline"></div>
							<div><? echo I18N("h", "Or contact your administrator to ask for access to the URL.");?></div>													
							<div class="emptyline"></div>
							<div style="text-align:right;">
								<input type="button" class="button_blue" onclick="PAGE.Apply_PWD()" value="<? echo i18n('Apply');?>">
								<input type="button" class="button_blue" onclick="window.history.go(-2);" value="<? echo i18n('Go Back');?>">
							</div>						
						</span>
					</div>
					<div id="count_down" style="width:620px;display:none;">
						<span style="text-align:center;">
							<div>
								<span><? echo I18N("h", "Please wait");?></span>
								<span id="count_down_second"></span>
								<span><? echo I18N("h", "seconds");?></span>
							</div>
						</span>						
					</div>
					<div class="emptyline"></div>		
				</div>
				<div class="msg_box_bottom"></div>
			</div>
		</div>
	</body>
</html>