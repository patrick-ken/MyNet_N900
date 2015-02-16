<form id="mainform"  onSubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Routing");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'The Routing option allows you to define static routes to specific destinations.');
		?></p>		
		<hr>		
		
		<div>
			<p class="text_title"><?echo I18N("h", "ROUTE LIST");?></p>
		</div>				
		<p class="text"><?echo I18N("h", "Remaining number of rules that can be created");?>: <span id="rmd" style="color:red;"></span></p>		
		<br>
			
	    <div>
			<div class="centerline" align="center">
				<table id="" class="general" width=525>
				<tr class="centerline">
					<td width="20px" class=c_tb><strong><?echo I18N("h", "Active");?></strong></td>
					<td class=c_tb><strong><?echo I18N("h", "Name");?></strong></td>
					<td class=c_tb><strong><?echo I18N("h", "Interface");?></strong></td>
					<td class=c_tb><strong><?echo I18N("h", "Destination IP");?></strong></td>
					<td class=c_tb><strong><?echo I18N("h", "Netmask");?></strong></td>
					<td class=c_tb><strong><?echo I18N("h", "Gateway");?></strong></td>
				</tr>
	
	<?
		$ROUTING_INDEX = 1;
		while ($ROUTING_INDEX <= $ROUTING_MAX_COUNT) {	dophp("load", "/htdocs/webinc/body/adv_routing_list.php");	$ROUTING_INDEX++; }
	?>
				</table>
			</div>
		</div>
		<hr>
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
