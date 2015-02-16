<? include "/htdocs/webinc/body/draw_elements.php";?>
<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Internet Status");?></p>
		</div>
		<div>
			<p class="text"><?echo I18N("h", "All of your Internet connection details are displayed on this page.");?></p>
		</div>
		<hr>
		<br>

	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection Type");?></span>
	        <span class="value" id="st_wantype"></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Physical Link to WAN");?></span>
	        <span class="value" id="st_wancable"></span>
	    </div>
	    <div class="textinput" id="wan_failover_block" style="display:none;">
	        <span class="name"><?echo I18N("h", "WAN Failover Status");?></span>
	        <span class="value" id="st_wan_failover"></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection to Internet");?></span>
	        <span class="value" id="st_networkstatus"></span>
	    </div>
	    <div class="textinput" id="st_wan_dhcp_action" style="display:none;">
			<span class="name"><?echo I18N("h", "DHCP IP Address");?></span>
	        <span class="value">
	            <input type="button" id="st_wan_dhcp_renew" class="button_blue" value="<?echo I18N("h", "Renew");?>" onClick="PAGE.DHCP_Renew();"/>&nbsp;&nbsp;
	            <input type="button" id="st_wan_dhcp_release" class="button_blue" value="<?echo I18N("h", "Release");?>" onClick="PAGE.DHCP_Release();"/>
	        </span>
	    </div>
	    <div class="textinput" id="st_wan_ppp_action" style="display:none;">
	        <span class="name"></span>
	        <span class="value">
	            <input type="button" id="st_wan_ppp_connect" class="button_blueX1p5" value="<?echo I18N("h", "Connect");?>" onClick="PAGE.PPP_Connect();"/>&nbsp;&nbsp;
	            <input type="button" id="st_wan_ppp_disconnect" class="button_blueX1p5" value="<?echo I18N("h", "Disconnect");?>" onClick="PAGE.PPP_Disconnect();"/>
	        </span>
	    </div>
		<div class="textinput">
	        <span class="name"><?echo I18N("h", "Connection Up Time");?></span>
	        <span class="value" id="st_connection_uptime"></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "MAC Address");?></span>
		    <span class="value" id="st_wan_mac"></span>
	    </div>
	    <div class="textinput">
	        <span class="name" id= "name_wanipaddr"></span>
	        <span class="value" id="st_wanipaddr"></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Subnet Mask");?></span>
	        <span class="value" id="st_wannetmask"></span>
	    </div>
	    <div class="textinput">
	        <span class="name" id= "name_wangateway"></span>
	        <span class="value" id="st_wangateway"></span>
	    </div>
	    <div class="textinput">
	        <span class="name"><?echo I18N("h", "Primary DNS Server");?></span>
	        <span class="value" id="st_wanDNSserver"></span>
	    </div>
	    <div class="textinput" >
	        <span class="name"><?echo I18N("h", "Secondary DNS Server");?></span>
	        <span class="value" id="st_wanDNSserver2"></span>
	    </div>
	    <div class="gap"></div>
	</div>
</form>
