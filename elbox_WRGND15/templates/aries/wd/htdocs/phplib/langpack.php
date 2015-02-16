<?
//include "/htdocs/phplib/trace.php";

function convert_lcode($primary, $subtag)
{
	$pri = tolower($primary);
	if ($pri=="zh")
	{
		$sub = tolower($subtag);
		if ($sub=="cn")	return "zhcn";
		else			return "zhtw";
	}
	else if ($pri=="pt")
	{
		$sub = tolower($subtag);
		if ($sub=="br")	return "ptbr";
	}		
	else if($pri=="se" && tolower($subtag)=="no")
	{
		return "no";
	}
	else if($pri=="sma" && tolower($subtag)=="no")
	{
		return "no";
	}
	else if($pri=="nn" ||$pri=="nb")
	{
		return "no";
	}
	return $pri;
}
function load_slp($lcode)
{
	$slp = "/etc/sealpac/".$lcode.".slp";
	if (isfile($slp)!="1") return 0;
	sealpac($slp);
	return 1;
}

function LANGPACK_setsealpac()
{
	$lcode = query("/device/features/language");
	if ($lcode=="auto" || $lcode=="")
	{
		$count = cut_count($_SERVER["HTTP_ACCEPT_LANGUAGE"], ',');
		$i = 0;
		while ($i < $count)
		{
			$tag = cut($_SERVER["HTTP_ACCEPT_LANGUAGE"], $i, ',');
			$pri = cut($tag, 0, '-');
			$sub = cut($tag, 1, '-');
			$lcode = convert_lcode($pri, $sub);
			if (load_slp($lcode) > 0) 
			{
				set("/runtime/device/auto_language",$lcode);
				return;
			}
			$i++;
		}
	}
	else if (load_slp($lcode) > 0) return;
	//TRACE_debug("load_slp default");
	sealpac("/etc/sealpac/en.slp");	// Use system default language, en.
}
function check_lcode($lcode)
{
	if	($lcode=="id")			return 1;
	else if	($lcode=="ms")		return 1;
	else if	($lcode=="ca")		return 1;
	else if	($lcode=="cs")		return 1;
	else if	($lcode=="da")		return 1;
	else if	($lcode=="de")		return 1;
	else if	($lcode=="et")		return 1;
	else if	($lcode=="engb")	return 1;
	else if	($lcode=="en")		return 1;
	else if	($lcode=="es")		return 1;
	else if	($lcode=="eu")		return 1;
	else if	($lcode=="tl")		return 1;
	else if	($lcode=="freu")	return 1;
	else if	($lcode=="fr")		return 1;
	else if	($lcode=="hr")		return 1;
	else if	($lcode=="it")		return 1;
	else if	($lcode=="is")		return 1;
	else if	($lcode=="sw")		return 1;
	else if	($lcode=="lv")		return 1;
	else if	($lcode=="lt")		return 1;
	else if	($lcode=="hu")		return 1;
	else if	($lcode=="nl")		return 1;
	else if	($lcode=="no")		return 1;
	else if	($lcode=="pl")		return 1;
	else if	($lcode=="pt")		return 1;
	else if	($lcode=="ro")		return 1;
	else if	($lcode=="sk")		return 1;
	else if	($lcode=="sl")		return 1;
	else if	($lcode=="fi")		return 1;
	else if	($lcode=="sv")		return 1;
	else if	($lcode=="vi")		return 1;
	else if	($lcode=="tr")		return 1;
	else if	($lcode=="el")		return 1;
	else if	($lcode=="ru")		return 1;
	else if	($lcode=="sr")		return 1;
	else if	($lcode=="uk")		return 1;
	else if	($lcode=="bg")		return 1;
	else if	($lcode=="iw")		return 1;
	else if	($lcode=="ar")		return 1;
	else if	($lcode=="ur")		return 1;
	else if	($lcode=="mr")		return 1;
	else if	($lcode=="hi")		return 1;
	else if	($lcode=="bn")		return 1;
	else if	($lcode=="gu")		return 1;
	else if	($lcode=="or")		return 1;
	else if	($lcode=="ta")		return 1;
	else if	($lcode=="te")		return 1;
	else if	($lcode=="kn")		return 1;
	else if	($lcode=="ml")		return 1;
	else if	($lcode=="th")		return 1;
	else if	($lcode=="am")		return 1;
	else if	($lcode=="zhtw")	return 1;
	else if	($lcode=="zhcn")	return 1;
	else if	($lcode=="ja")		return 1;
	else if	($lcode=="ko")		return 1;
	return 0;
}
?>
