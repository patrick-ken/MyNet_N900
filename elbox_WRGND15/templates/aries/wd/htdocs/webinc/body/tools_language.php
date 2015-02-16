<form id="mainform" >
	<div>
		<div>
			<p class="text_title"><?echo I18N("h", "Language");?></p>
		</div>
		<div class="textinput">
			<span class="name"><?echo I18N("h", "Select language ");?></span>
			<span class="value">
				<select id="lang_select" class="styled3">
					<option value="auto"><? echo I18N("h", "Auto");?></option>
        	<?				
				if(isfile("/etc/sealpac/id.slp")=="1") echo '\t\t\t<option value="id"></option>\n';
				if(isfile("/etc/sealpac/ms.slp")=="1") echo '\t\t\t<option value="ms"></option>\n';
				if(isfile("/etc/sealpac/ca.slp")=="1") echo '\t\t\t<option value="ca"></option>\n';
				if(isfile("/etc/sealpac/da.slp")=="1") echo '\t\t\t<option value="da"></option>\n';
				if(isfile("/etc/sealpac/et.slp")=="1") echo '\t\t\t<option value="et"></option>\n';
				if(isfile("/etc/sealpac/engb.slp")=="1") echo '\t\t\t<option value="engb"></option>\n';
				if(isfile("/etc/sealpac/eu.slp")=="1") echo '\t\t\t<option value="eu"></option>\n';
				if(isfile("/etc/sealpac/tl.slp")=="1") echo '\t\t\t<option value="tl"></option>\n';
				if(isfile("/etc/sealpac/freu.slp")=="1") echo '\t\t\t<option value="freu"></option>\n';
				if(isfile("/etc/sealpac/hr.slp")=="1") echo '\t\t\t<option value="hr"></option>\n';
				if(isfile("/etc/sealpac/is.slp")=="1") echo '\t\t\t<option value="is"></option>\n';
				if(isfile("/etc/sealpac/sw.slp")=="1") echo '\t\t\t<option value="sw"></option>\n';
				if(isfile("/etc/sealpac/lv.slp")=="1") echo '\t\t\t<option value="lv"></option>\n';
				if(isfile("/etc/sealpac/lt.slp")=="1") echo '\t\t\t<option value="lt"></option>\n';
				if(isfile("/etc/sealpac/ro.slp")=="1") echo '\t\t\t<option value="ro"></option>\n';
				if(isfile("/etc/sealpac/sk.slp")=="1") echo '\t\t\t<option value="sk"></option>\n';
				if(isfile("/etc/sealpac/sl.slp")=="1") echo '\t\t\t<option value="sl"></option>\n';
				if(isfile("/etc/sealpac/fi.slp")=="1") echo '\t\t\t<option value="fi"></option>\n';
				if(isfile("/etc/sealpac/vi.slp")=="1") echo '\t\t\t<option value="vi"></option>\n';
				if(isfile("/etc/sealpac/el.slp")=="1") echo '\t\t\t<option value="el"></option>\n';
				if(isfile("/etc/sealpac/sr.slp")=="1") echo '\t\t\t<option value="sr"></option>\n';
				if(isfile("/etc/sealpac/uk.slp")=="1") echo '\t\t\t<option value="uk"></option>\n';
				if(isfile("/etc/sealpac/bg.slp")=="1") echo '\t\t\t<option value="bg"></option>\n';
				if(isfile("/etc/sealpac/iw.slp")=="1") echo '\t\t\t<option value="iw"></option>\n';
				if(isfile("/etc/sealpac/ar.slp")=="1") echo '\t\t\t<option value="ar"></option>\n';
				if(isfile("/etc/sealpac/ur.slp")=="1") echo '\t\t\t<option value="ur"></option>\n';
				if(isfile("/etc/sealpac/mr.slp")=="1") echo '\t\t\t<option value="mr"></option>\n';
				if(isfile("/etc/sealpac/hi.slp")=="1") echo '\t\t\t<option value="hi"></option>\n';
				if(isfile("/etc/sealpac/bn.slp")=="1") echo '\t\t\t<option value="bn"></option>\n';
				if(isfile("/etc/sealpac/gu.slp")=="1") echo '\t\t\t<option value="gu"></option>\n';
				if(isfile("/etc/sealpac/or.slp")=="1") echo '\t\t\t<option value="or"></option>\n';
				if(isfile("/etc/sealpac/ta.slp")=="1") echo '\t\t\t<option value="ta"></option>\n';
				if(isfile("/etc/sealpac/te.slp")=="1") echo '\t\t\t<option value="te"></option>\n';
				if(isfile("/etc/sealpac/kn.slp")=="1") echo '\t\t\t<option value="kn"></option>\n';
				if(isfile("/etc/sealpac/ml.slp")=="1") echo '\t\t\t<option value="ml"></option>\n';
				if(isfile("/etc/sealpac/th.slp")=="1") echo '\t\t\t<option value="th"></option>\n';
				if(isfile("/etc/sealpac/am.slp")=="1") echo '\t\t\t<option value="am"></option>\n';
				if(isfile("/etc/sealpac/en.slp")=="1") echo '\t\t\t<option value="en">'.I18N("h", "English").'</option>\n';				
				if(isfile("/etc/sealpac/nl.slp")=="1") echo '\t\t\t<option value="nl">'.I18N("h", "Dutch").'</option>\n';				
				if(isfile("/etc/sealpac/fr.slp")=="1") echo '\t\t\t<option value="fr">'.I18N("h", "French").'</option>\n';				
				if(isfile("/etc/sealpac/it.slp")=="1") echo '\t\t\t<option value="it">'.I18N("h", "Italian").'</option>\n';				
				if(isfile("/etc/sealpac/de.slp")=="1") echo '\t\t\t<option value="de">'.I18N("h", "German").'</option>\n';				
				if(isfile("/etc/sealpac/es.slp")=="1") echo '\t\t\t<option value="es">'.I18N("h", "Spanish").'</option>\n';				
				if(isfile("/etc/sealpac/ptbr.slp")=="1") echo '\t\t\t<option value="ptbr">'.I18N("h", "Portuguese (Brazilian)").'</option>\n';				
				if(isfile("/etc/sealpac/pl.slp")=="1") echo '\t\t\t<option value="pl">'.I18N("h", "Polish").'</option>\n';				
				if(isfile("/etc/sealpac/ru.slp")=="1") echo '\t\t\t<option value="ru">'.I18N("h", "Russian").'</option>\n';				
				if(isfile("/etc/sealpac/no.slp")=="1") echo '\t\t\t<option value="no">'.I18N("h", "Norwegian").'</option>\n';				
				if(isfile("/etc/sealpac/sv.slp")=="1") echo '\t\t\t<option value="sv">'.I18N("h", "Swedish").'</option>\n';				
				if(isfile("/etc/sealpac/tr.slp")=="1") echo '\t\t\t<option value="tr">'.I18N("h", "Turkish").'</option>\n';				
				if(isfile("/etc/sealpac/cs.slp")=="1") echo '\t\t\t<option value="cs">'.I18N("h", "Czech").'</option>\n';				
				if(isfile("/etc/sealpac/hu.slp")=="1") echo '\t\t\t<option value="hu">'.I18N("h", "Hungarian").'</option>\n';				
				if(isfile("/etc/sealpac/zhcn.slp")=="1") echo '\t\t\t<option value="zhcn">'.I18N("h", "Simplified Chinese").'</option>\n';				
				if(isfile("/etc/sealpac/zhtw.slp")=="1") echo '\t\t\t<option value="zhtw">'.I18N("h", "Traditional Chinese").'</option>\n';
				if(isfile("/etc/sealpac/ko.slp")=="1") echo '\t\t\t<option value="ko">'.I18N("h", "Korean").'</option>\n';
				if(isfile("/etc/sealpac/ja.slp")=="1") echo '\t\t\t<option value="ja">'.I18N("h", "Japanese").'</option>\n';
			?>
       			</select>
			</span>
		</div>
		<hr>
	</div>
	<div class="bottom_cancel_save">
		<input type="button" class="button_black" id="reload" onclick="BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;
		<input type="button" class="button_blue" id="onsumit" onclick="PAGE.OnClickChangeLang();" value="<?echo I18N('h', 'Change');?>">
	</div>	
</form>

