<? 
include "/htdocs/webinc/body/draw_elements.php";
include "/htdocs/phplib/xnode.php";
?>
<form id="mainform" onsubmit="return false;">
	<div>
		<div>
			<p class="text_title"><?echo i18n("FasTrack Plus Quality of Service(QoS) SETTINGS");?></p>
		</div>				
		<p class="text"><?
			echo i18n("Use this section to configure QoS powered by WD's FasTrack Plus&#8482; Technology. FasTrack Plus analyzes network traffic for video, audio, voice/video calls, and data traffic in real-time and reallocates network bandwidth and resources intelligently to heighten your entertainment experience.");
		?></p>		
		<hr>
		
		<div>
			<p class="text_title"><?echo i18n("Enable FasTrack Plus QoS");?></p>
		</div>					
		<br>
		<div class="textinput_ex">
			<span class="name"><?echo i18n("Enable FasTrack Plus QoS");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_qos" onClick="PAGE.OnClickQOSEnable();" /></span>
		</div>
		<div class="textinput_ex" style="display:none;"><!--Hide for WD demo. Joseph-->
			<span class="name"><?echo i18n("Dynamic Fragmentation");?></span>
			<span class="value"><input type="checkbox" class="styled" id="dynamic_fraq" /></span>
		</div>							
		<hr>

		<div>
			<p class="text_title"><?echo i18n("WAN Uplink Speed");?></p>
		</div>
		<br>
		<div class="textinput_ex" style="display:none;"><!--Hide for WD demo. Joseph-->
			<span class="name"><?echo i18n("Enable Traffic Shaping");?></span>
			<span class="value"><input type="checkbox" class="styled" id="en_shaping" /></span>
		</div>						
		<div id="select_speed" class="textinput_ex">
			<span class="name"><?echo i18n("Set Uplink Speed at:");?></span>
			<span class="value">
				<select id="select_upstream" onchange="PAGE.OnChangeQOSUpstream();" class="<? if ($lang == "hu" || $lang == "ru" || $lang == "pl" || $lang == "de") {echo "styled4";} else if ($lang == "ptbr" || $lang == "es" || $lang == "fr") {echo "styled3";} else if ($lang == "nl" || $lang == "it" || $lang == "no" || $lang == "sv" || $lang == "tr" || $lang == "cs" || $lang == "ja") {echo "styled2";}else {echo "styled1";}?>">
					<? if(query("/runtime/devdata/countrycode") == "RU") echo "<!--"; ?>
					<option value="0" selected><?echo i18n("Auto");?></option>
					<? if(query("/runtime/devdata/countrycode") == "RU") echo "-->"; ?>
					<option value="1"><?echo i18n("User define");?></option>
					<option value="128"><?echo i18n("128K");?></option>
					<option value="256"><?echo i18n("256K");?></option>
					<option value="384"><?echo i18n("384K");?></option>
					<option value="512"><?echo i18n("512K");?></option>
					<option value="1024"><?echo i18n("1M");?></option>
				</select>
				<span id="uplink_user" style="color:white;">
					<input id="uplink_user_define" type="text" size=7 maxlength=7>&nbsp;Kbps
				</span>
			</span>
		</div>
		<hr>
		
		<div>
			<p class="text_title"><?echo i18n("Prioritization Rules");?></p>
		</div>
        <div class="textinput_ex">
            <span class="name"><?echo i18n("Automatic Prioritization");?></span>
            <span class="value"><input type="checkbox" class="styled" id="auto_classfy"></span>
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
				while ($INDEX <= $QOS_MAX_COUNT)	{dophp("load", "/htdocs/webinc/body/adv_qos_list.php");	$INDEX++;}
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

