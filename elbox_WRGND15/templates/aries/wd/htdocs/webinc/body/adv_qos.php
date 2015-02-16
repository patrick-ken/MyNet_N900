<? 
include "/htdocs/webinc/body/draw_elements.php";
include "/htdocs/phplib/xnode.php";
?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Quality of Service(QoS) SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo I18N("h", "Use this section to configure the QoS Engine to improve your media applications and online game experience by ensuring that your most critical traffic is prioritized over other network traffic, such as web surfing. For best performance, use the QoS rules to set the priority for your applications.");
		?></p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Enable QoS");?></p>
		</div>					
		<br>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Enable QoS");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_qos" onClick="PAGE.OnClickQOSEnable();" /></span>
		</div>			
		<hr>

		<div>
			<p class="text_title"><?echo I18N("h", "QoS Setup");?></p>
		</div>
		<br>				
		<div id="select_speed" class="textinput">
			<span class="name"><?echo I18N("h", "Uplink Speed");?></span>
			<span class="value">
				<select id="select_upstream" onchange="PAGE.OnChangeQOSUpstream();" class="styled3">
					<? if(query("/runtime/devdata/countrycode") == "RU") echo "<!--"; ?>
					<option value="0" selected><?echo I18N("h", "Auto");?></option>
					<? if(query("/runtime/devdata/countrycode") == "RU") echo "-->"; ?>
					<option value="1"><?echo I18N("h", "User define");?></option>
					<option value="128">128k</option>	
					<option value="256">256k</option>
					<option value="384">384k</option>
					<option value="512">512k</option>
					<option value="1024">1M</option>
					<option value="2048">2M</option>
					<option value="3072">3M</option>
					<option value="5120">5M</option>
					<option value="10240">10M</option>
					<option value="20480">20M</option>
				</select>
				<span id="uplink_user" style="color:white;">
					<input id="uplink_user_define" type="text" size=7 maxlength=7>&nbsp;Kbps
				</span>
			</span>
		</div>
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Classification Rules");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Remaining number of rules that can be created");?>: <span id="rmd" style="color:red;"></span></span>
			<span class="value" style="padding-left: 160px;"><input id="restore_button" type="button" class="button_blueX3" onclick="PAGE.OnRestore();" value="<?echo I18N('h', 'Restore Default');?>" /></span>
		</div>
		<br>
		<div>
			<table id="qos_table" class="general">
				<tr>
					<th width="140px"><?echo I18N("h", "Name");?></th>
					<th width="150px"><?echo I18N("h", "Priority");?></th>
					<th width="140px"><?echo I18N("h", "Type");?></th>
					<th width="340px"><?echo I18N("h", "Detail");?></th>
					<th width="20px"></th>
				</tr>				
			</table>
		</div>
		<br>
		<div>			
			<input id="add_rule_button" type="button" class="button_blueX2" onclick="PAGE.OnAddRule();" value="<?echo I18N('h', 'Add Rule');?>" />
		</div>		
		<hr>
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" id="b_save" class="button_blue" onclick="PAGE.Submit_and_Reboot();" value="<?echo I18N('h', 'Save');?>" />
		</div>
	</div>
</form>

