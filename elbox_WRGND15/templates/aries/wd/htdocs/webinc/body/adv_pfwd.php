<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Port Forwarding");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'This option is used to open multiple ports or a range of ports in your router and redirect data through those ports to a single computer on your network. This feature allows you to enter external start port, external end port, and internal start port in the fields. The internal end port will be generated automatically. This option is only applicable to the Internet session.');
		?></p>
		<hr>
		<div>
		<p class="text_title"><?echo I18N("h", "Ports Used by System");?></p>
		</div>
		<p class="text"><?echo I18N('h', 'These external ports are currently being used by router system or UPnP applications.');?></p>
		<br>
		<div>
			<table id="usedPORT_table" class="general">
				<tr>
					<th width="250px"><?echo I18N("h", "Application");?></th>
					<th width="50px"><?echo I18N("h", "UPnP");?></th>
					<th width="120px"><?echo I18N("h", "IP Address");?></th>
					<th width="280px"><?echo I18N("h", "External Port");?></th>
				</tr>				
			</table>
		</div>
		<br>
		<hr>
		<div>
		<p class="text_title"><?echo I18N("h", "Port Forwarding Rules");?></p>
		</div>
		<p class="text"><?echo I18N('h', 'Remaining number of rules that can be created');?>: <span id="rmd" style="color:red;"></span></p>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
		<br>
		<div class="centerline" align="center">		
	<?
	$INDEX = 1;
	while ($INDEX <= $PFWD_MAX_COUNT) {	dophp("load", "/htdocs/webinc/body/adv_pfwd_list.php");	$INDEX++; }
	?>
		</div>
		<div class="gap"></div>
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
