<form>
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Registration");?></p>
		</div>
		<div>
			<p class="text"><? echo I18N("h", "Register your My Net router.");?></p>
		</div>
		<br>
			
		<div class="textinput">
			<span class="name"><? echo I18N("h", "First Name");?></span>
			<span class="value"><input id="first_name" type="text" size="30" maxlength="20" /></span>
		</div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Last Name");?></span>
			<span class="value"><input id="last_name" type="text" size="30" maxlength="20" /></span>
		</div>
		<div class="textinput">
			<span class="name"><? echo I18N("h", "Email Address");?></span>
			<span class="value"><input id="email" type="text" size="30" maxlength="80" /></span>
		</div>		
		<br>
		<div>
			<p id="register_result" class="text"></p>
		</div>
		<br>
		
		<div class="bottom_cancel_save">
			<input type="button" id="b_register" class="button_blue" onclick="PAGE.Register();" value="<? echo I18N('h', 'Register');?>" />
		</div>
	</div>
</form>
