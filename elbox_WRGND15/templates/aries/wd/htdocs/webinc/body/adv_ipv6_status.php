<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "IPv6 Network Information");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'All of your Internet and network connection details are displayed on this page.');
		?></p>	
		<hr>
		
		<div id="ll_ipv6" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "IPv6 Connection Information");?></p>
			</div>		    
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "IPv6 Connection Type");?>&nbsp;:</span>
			    <span class="value" id="ll_type"></span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "IPv6 Default Gateway");?>&nbsp;:</span>
				<span class="value" id="ll_gateway"></span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "LAN IPv6 Link-Local Address");?>&nbsp;:</span>
			<span class="value">
		        <span id="ll_lan_ll_address"></span>
				<span id="ll_lan_ll_pl"></span>
			</span>
		    </div>
			<div class="gap"></div>
		</div>
		
		<div id="ipv6" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "IPv6 Connection Information");?></p>
			</div>		    
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "IPv6 Connection Type");?>&nbsp;:</span>
			    <span class="value" id="type"></span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "Network Status");?>&nbsp;:</span>
		        <span class="value" id="status"></span>
		    </div>
		    <div class="textinput_ipv6_st" id="st_wan_ppp_action" style="display:none;">
		        <span class="name"></span>
		        <span class="value">
		            <input type="button" class="button_blue" id="st_wan_ppp_connect" value="<?echo I18N("h", "Connect");?>" onClick="PAGE.PPP_Connect();"/>&nbsp;&nbsp;
		            <input type="button" class="button_blue" id="st_wan_ppp_disconnect" value="<?echo I18N("h", "Disconnect");?>" onClick="PAGE.PPP_Disconnect();"/>  
		        </span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "WAN IPv6 Address");?>&nbsp;:</span>
				<span class="value">
		        	<span id="wan_address"></span>
					<span id="wan_address_pl"></span>
				</span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "IPv6 Default Gateway");?>&nbsp;:</span>
				<span class="value" id="gateway"></span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "Primary IPv6 DNS Server");?>&nbsp;:</span>
		        <span class="value" id="br_dns1"></span>
		    </div>
		    <div class="textinput_ipv6_st" >
		        <span class="name"><?echo I18N("h", "Secondary IPv6 DNS Server");?>&nbsp;:</span>
		        <span class="value" id="br_dns2"></span>
		    </div>    
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "LAN IPv6 Link-Local Address");?>&nbsp;:</span>
				<span class="value">
			        	<span id="lan_ll_address"></span>
						<span id="lan_ll_pl"></span>
				</span>
		    </div>
		    <div class="textinput_ipv6_st" id="ipv6_pd" style="display:none">
		        <span class="name"><?echo I18N("h", "DHCP-PD");?>&nbsp;:</span>
				<span class="value">
			        	<span id="enable_pd"></span>
				</span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "IPv6 Network assigned by DHCP-PD");?>&nbsp;:</span>
				<span class="value">
		        	<span id="pd_prefix"></span>
					<span id="pd_pl"></span>
				</span>
		    </div>
		    <div class="textinput_ipv6_st">
		        <span class="name"><?echo I18N("h", "LAN IPv6 Address");?>&nbsp;:</span>
				<span class="value">
		        	<span id="lan_address"></span>
					<span id="lan_pl"></span>
				</span>
		    </div>
			<div class="gap"></div>
		</div>
		<hr>
		
		<div id="ipv6_client" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "LAN IPv6 Computers");?></p>
			</div>			
			<table id="client6_list" class="general">
		                <tr>
		                        <th><?echo I18N("h", "IPv6 Address");?></th>
		                        <th><?echo I18N("h", "Name(if any)");?></th>
		                </tr>
		     </table>
		</div>
			
		<div  id="ipv6_bridge" style="display:none">
			<div>
				<p class="text_title"><?echo I18N("h", "IPv6 Connection Information");?></p>
			</div>		    
		    <table height="100px" align="center">
		    	<tr><td><?echo I18N("h", "The router is now in Access Point Mode.");?></td></tr>
		    </table>
		</div>		
	</div>
</form>

