<?
if ($_GET["RESULT"]=="SUCCESS")
{
	$f_style = ' style="display:none;"';
}
else
{
	$f_style = ' style="display:block;"';
}
?>
	<div class="msg_body" <?=$f_style?>>
		<div>
			<div class="msg_box">
				<div class="msg_box_top"></div>
				<div class="msg_box_middle">
					<div class="emptyline"></div>
					<h1><?echo I18N("h", "Restore Invalid");?></h1>
					<div style="width:620px;">
						<span style="text-align:left;">
							<div><? echo I18N("h", "The restored configuration file is not correct.");?></div>
							<div class="emptyline"></div>
							<div><? echo I18N("h", "You may have restored a file that is not intended for this device, is incompatible with this version of the product, or is corrupted.");?></div>
							<div class="emptyline"></div>
							<div><? echo I18N("h", "Try the restore again with a valid restore configuration file.");?></div>
							<div class="emptyline"></div>
							<div><? echo I18N("h", "Please press the button below to continue configuring the router.");?></div>											
							<div class="emptyline"></div>
							<div style="text-align:right;">
								<input type="button" class="button_blueX2" onclick="history.back();" value="<? echo I18N('h', 'Continue');?>">
							</div>						
						</span>
					</div>
					<div class="emptyline"></div>		
				</div>
				<div class="msg_box_bottom"></div>
			</div>
		</div>
		<br>
		<br>
		<br>
	</div>