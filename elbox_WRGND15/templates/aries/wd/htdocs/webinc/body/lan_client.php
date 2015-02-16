<form id="mainform">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "DHCP Client Table");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Wired/Wireless Devices");?></span>
		</div>
		<div>
			<table id="leases_list" width="700px" class="general">
				<tr>
					<th width="30px">#</th>
					<th width="100px"><?echo I18N("h", "IP Address");?></th>
					<th width="150px"><?echo I18N("h", "MAC Address");?></th>
					<th width="200px"><?echo I18N("h", "Device Name");?></th>
					<th width="220px"><?echo I18N("h", "Lease Time");?></th>
				</tr>
			</table>
		</div>
		<br>
		<br>
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Manually Added Devices");?></p>
		</div>
		<br>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "IP Address");?></span>
			<span class="value"><input id="reserv_ipaddr" type="text" /></span>
		</div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "MAC Address");?></span>
			<span class="value"><input id="reserv_macaddr" type="text" /></span>
		</div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Device Name");?></span>
			<span class="value"><input id="reserv_host" type="text" /></span>
		</div>
		<div class="textinput">
			<span class="name"></span>
			<span class="value"><input type="button" id="b_add" class="button_blueX2" onclick="PAGE.AddDHCPReserv();" value="<?echo I18N('h', 'Add/Update');?>" /></span>
		</div>
		<br>
		<div>
			<table id="reserves_list" width="600px" class="general">
				<tr>
					<th width="30px">#</th>
					<th width="150px"><?echo I18N("h", "IP Address");?></th>
					<th width="150px"><?echo I18N("h", "MAC Address");?></th>
					<th width="200px"><?echo I18N("h", "Device Name");?></th>
					<th width="30px"></th>
					<th width="30px"></th>			
				</tr>
			</table>
		</div>
		<br>				
		<hr>
					
		<div class="bottom_cancel_save">
			<input type="button" id="b_cancel" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" id="b_save" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
