<? include "/htdocs/webinc/body/draw_elements.php"; ?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Firewall");?></p>
		</div>				
		<p class="text"><?
			echo I18N('h', 'You can setup the firewall of this device in this page. The firewall rules allow you to block specific incoming or outgoing traffic by IP address or ports.');
		?></p>	
		<br><br>
		<div class="textinput">
			<span class="name" style="position: absolute; margin-top:3px;"><?echo I18N('h','IPv4 SPI Firewall');?></span>
			<span class="value" style="left:200px; margin-top:0px;"><input type="checkbox" class="styled" id="EN_SPI" /></span>
		</div>
		<div class="smallemptyline">
		</div>
		<div class="textinput">
			<span class="name" style="position: absolute; left:50px;  margin-top:0px;"><?echo I18N('h','Allow incoming WAN ping request');?></span>
			<span class="value" style="left:0px; margin-top:2px;"><input type="checkbox" class="styled2" id="EN_WANping" /></span>
		</div>
		<hr>
		
		<div>
			<p class="text_title"><?echo I18N("h", "Create Firewall Rules");?></p>
		</div>
		<div>
		<p class="text"><?echo I18N('h', 'If WAN is selected for Source/Destination, asterisk characters(*), also known as wild card, can be used to represent any IP address or port range.');?></span></p>
		</div>
		<br>
		<p class="text"><?echo I18N('h', 'Remaining number of rules that can be created');?>: <span id="rmd" style="color:red;"></span></p>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>
		<br>
	    <div class="centerline" align="center">
	<?
	$INDEX = 1;
	while ($INDEX <= $FW_MAX_COUNT) {dophp("load", "/htdocs/webinc/body/adv_firewall_list.php");    $INDEX++;}
	?>
	    </div>
	    <br>
	    
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
		</div>		
	</div>
</form>

