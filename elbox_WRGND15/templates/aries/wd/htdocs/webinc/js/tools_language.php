<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "",
	OnLoad: function()
	{
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result) { return false; },
	InitValue: function(xml)
	{
		<?
			include "/htdocs/phplib/langpack.php";
			$count = cut_count($_SERVER["HTTP_ACCEPT_LANGUAGE"], ',');
			$i = 0;
			while ($i < $count)
			{
				$tag = cut($_SERVER["HTTP_ACCEPT_LANGUAGE"], $i, ',');
				$pri = cut($tag, 0, '-');
				$sub = cut($tag, 1, '-');
				$lcode = convert_lcode($pri, $sub);
				if (check_lcode($lcode) == 1) break;
				$i++;
			}
		?>
		/*var browser_lcode = "<? echo $lcode;?>";
		for(var j=0; j < this.LanguageName.length; j++)
		{
			if(browser_lcode == this.LanguageName[j].name)
			{
				OBJ("browser_language").innerHTML = "("+this.LanguageName[j].value+")";
				break;
			}
		}*/
		for(var i=0; i < OBJ("lang_select").options.length; i++)
		{
			var option_language = OBJ("lang_select").options[i].value;
			for(var j=0; j < this.LanguageName.length; j++)
			{
				if(option_language == this.LanguageName[j].name)
				{
					OBJ("lang_select").options[i].text = this.LanguageName[j].value;
				}
			}	
		}
		this.RouterLanguage = "<? echo query('/device/features/language');?>";
		if(this.RouterLanguage!="") COMM_SetSelectValue(OBJ("lang_select"), this.RouterLanguage);
		return true;
	},
	PreSubmit: function()
	{
		return null;
	},
	OnClickChangeLang: function()
	{
		var lang_str = OBJ("lang_select").value;
		var ajaxObj = GetAjaxObj("Lang");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function ()
		{
			ajaxObj.release();
			self.location = "./<?=$TEMP_MYNAME?>.php";
		}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("language.php", "multilanguage="+lang_str);
	},
	isDirty: null,
	RouterLanguage: null,
	LanguageName: [{name:"id", 	value:"Bahasa Indonesia"},
					{name:"ms", 	value:"Bahasa Melayu"},
					{name:"ca", 	value:"Català"},
					{name:"cs", 	value:"Czech"},//Fixed WD ITR 50823
					{name:"da", 	value:"Dansk"},
					{name:"de", 	value:"Deutsch"},
					{name:"et", 	value:"Eesti keel"},
					{name:"engb", 	value:"English (UK)"},
					//{name:"en", 	value:"English (US)"},
					{name:"en", 	value:"English"},//Fixed WD ITR 45108
					{name:"es", 	value:"Español"},
					{name:"eu", 	value:"Euskara"},
					{name:"tl", 	value:"Filipino"},
					{name:"freu", 	value:"Français (UK)"},
					//{name:"fr", 	value:"Français (US)"},//Fixed WD ITR 45108
					{name:"fr", 	value:"Français"},
					{name:"hr", 	value:"Hrvatski"},
					{name:"it", 	value:"Italiano"},
					{name:"is", 	value:"Íslenska"},
					{name:"sw", 	value:"Kiswahili"},
					{name:"lv", 	value:"Latviešu"},
					{name:"lt", 	value:"Lietuvių"},
					{name:"hu", 	value:"Magyar"},
					{name:"nl", 	value:"Nederlands"},
					{name:"no", 	value:"Norsk (Bokmål)"},
					{name:"pl", 	value:"Polski"},
					{name:"pt", 	value:"Português"},
					{name:"ro", 	value:"Română"},
					{name:"sk", 	value:"Slovenský"},
					{name:"sl", 	value:"Slovenščina"},
					{name:"fi", 	value:"Suomi"},
					{name:"sv", 	value:"Svenska"},
					{name:"vi", 	value:"Tiếng Việt"},
					{name:"tr", 	value:"Türkçe"},
					{name:"el", 	value:"Ελληνικά"},
					{name:"ru", 	value:"Русский"},
					{name:"sr", 	value:"Српски"},
					{name:"uk", 	value:"Українська"},
					{name:"bg", 	value:"Български"},
					{name:"iw", 	value:"Hebrew"},
					{name:"ar", 	value:"Arabic"},
					{name:"ur", 	value:"Urdu"},
					{name:"mr", 	value:"मराठी"},
					{name:"hi", 	value:"हिन्दी"},
					{name:"bn", 	value:"বাংলা"},
					{name:"gu", 	value:"ગુજરાતી"},
					{name:"or", 	value:"ଓଡିଆ (Oriya)"},
					{name:"ta", 	value:"தமிழ்"},
					{name:"te", 	value:"తెలుగు"},
					{name:"kn", 	value:"ಕನ್ನಡ"},
					{name:"ml", 	value:"മലയാളം"},
					{name:"th", 	value:"ภาษาไทย"},
					{name:"am", 	value:"አማርኛ (Amharic)"},
					{name:"zhtw", 	value:"中文（繁體）"},
					{name:"zhcn", 	value:"中文（简体）"},
					{name:"ja", 	value:"日本語"},
					{name:"ko", 	value:"한국어"},
					{name:"ptbr", 	value:"Portuguese (Brazilian)"}],//Fixed WD ITR 43549
	Synchronize: function() {}
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
		
};
</script>
		
	
