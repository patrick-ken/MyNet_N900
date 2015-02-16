<? 
include "/htdocs/webinc/body/draw_elements.php";
include "/htdocs/phplib/xnode.php";
?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo i18n("Enhanced WMM");?></p>
		</div>				
		<p class="text"><?echo i18n("Enhanced WMM prioritizes the traffic of various wireless applications.");?></p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo i18n("Enhanced WMM Setup");?></p>
		</div>					
		<br>
		<div class="textinput">
			<span class="name"><?echo i18n("Enable Enhanced WMM");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_qos" onClick="PAGE.OnClickQOSEnable();" /></span>
		</div>					
		<hr>	
		
		<div style="display:none;"><!--Hide for WD demo.-->		
			<div>
				<p class="text_title"><?echo i18n("Enhanced WMM Setup");?></p>
			</div>
			<br>		
			<div class="textinput">
				<span class="name">HTTP</span>
				<span class="value"><input type="checkbox" class="styled" id="wish_http"/></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo i18n("Windows Media Center");?></span>
				<span class="value"><input type="checkbox" class="styled" id="wish_wmc" /></span>
			</div>
			<div class="textinput">
				<span class="name"><?echo i18n("Automatic");?></span>
				<span class="value"><input type="checkbox" class="styled" id="wish_auto" /></span>
			</div>
			<br>
			<hr>
		</div>	
		
		<div>
			<p class="text_title"><?echo i18n("Classification Rules");?></p>
		</div>
		<p class="text"><?echo i18n("Remaining number of rules that can be created");?>: <span id="rmd" style="color:red;"></span></p>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo i18n('Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo i18n('Save');?>" />
		</div>		
		<br>
		<div>
			<table id="qos_table" class="general">
				<col width="10px"></col>
				<col width="250px"></col>
				<col width="200px"></col>
				<col width="180px"></col>
				<?
				$INDEX = 1;
				while ($INDEX <= $QOS_MAX_COUNT)	{dophp("load", "/htdocs/webinc/body/adv_qos_wish_list.php");	$INDEX++;}
				?>
			</table>
		</div>
		<br>	
		<hr>
		
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" onclick="BODY.OnReload();" value="<?echo i18n('Cancel');?>" />&nbsp;&nbsp;			
			<input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo i18n('Save');?>" />
		</div>
	</div>
</form>

