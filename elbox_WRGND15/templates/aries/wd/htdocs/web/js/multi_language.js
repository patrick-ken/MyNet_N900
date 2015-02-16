/*  
This file indicates different CSS styles under different Languages.
You could generated different CSS styles in the return variable.
We also consider cross browsers issues and generated appropriate CSS styles.
*/
function lanugage_StyleGet(lang , name)
{
	lang = lang.toUpperCase();
	switch (lang)
	{
	case 'CS': 
	return CS_StyleGet(name);
	case 'DE': 
	return DE_StyleGet(name);
	case 'EN': 
	return EN_StyleGet(name);
	case 'ES': 
	return ES_StyleGet(name);
	case 'FR': 
	return FR_StyleGet(name);
	case 'HU': 
	return HU_StyleGet(name);
	case 'IT': 
	return IT_StyleGet(name);
	case 'JA': 
	return JA_StyleGet(name);
	case 'KO': 
	return KO_StyleGet(name);
	case 'NL': 
	return NL_StyleGet(name);
	case 'NO': 
	return NO_StyleGet(name);
	case 'PL': 
	return PL_StyleGet(name);
	case 'PTBR': 
	return PTBR_StyleGet(name);
	case 'RU': 
	return RU_StyleGet(name);
	case 'SV': 
	return SV_StyleGet(name);
	case 'TR': 
	return TR_StyleGet(name);
	case 'ZHCN': 
	return ZHCN_StyleGet(name);
	case 'ZHTW': 
	return ZHTW_StyleGet(name);
	}	
}
function CS_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:45px;";
	case 'qos_end_port': 
	return "margin-left:55px;";
	case 'qos_lan_ip': 
	return "margin-left:15px;";
	case 'qos_remote_ip': 
	return "margin-left:9px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function DE_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:11px;";
	case 'qos_end_port': 
	return "margin-left:33px;";
	case 'qos_lan_ip': 
	return "margin-left:44px;";
	case 'qos_remote_ip': 
	return "margin-left:20px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function EN_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:22px;";
	case 'qos_end_port': 
	return "margin-left:28px;";
	case 'qos_lan_ip': 
	return "margin-left:39px;";
	case 'qos_remote_ip': 
	return "margin-left:14px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
}
function ES_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:8px;";
	case 'qos_end_port': 
	return "margin-left:20px;";
	case 'qos_lan_ip': 
	return "margin-left:27px;";
	case 'qos_remote_ip': 
	return "margin-left:31px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
}
function FR_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:9px;";
	case 'qos_end_port': 
	return "margin-left:48px;";
	case 'qos_lan_ip': 
	return "margin-left:59px;";
	case 'qos_remote_ip': 
	return "margin-left:45px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function HU_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:13px;";
	case 'qos_end_port': 
	return "margin-left:14px;";
	case 'qos_lan_ip': 
	return "margin-left:40px;";
	case 'qos_remote_ip': 
	return "margin-left:30px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function IT_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:11px;";
	case 'qos_end_port': 
	return "margin-left:22px;";
	case 'qos_lan_ip': 
	return "margin-left:52px;";
	case 'qos_remote_ip': 
	return "margin-left:34px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function JA_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:20px;";
	case 'qos_end_port': 
	return "margin-left:20px;";
	case 'qos_lan_ip': 
		if(navigator.appName.search("Opera")!=-1 || navigator.appName.search("Microsoft")!=-1 || navigator.userAgent.search("Chrome")!=-1)
			return "margin-left:38px;";
		else
			return "margin-left:51px;";
	case 'qos_remote_ip': 
	return "margin-left:22px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function KO_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:17px;";
	case 'qos_end_port': 
	return "margin-left:33px;";
	case 'qos_lan_ip': 
	return "margin-left:36px;";
	case 'qos_remote_ip': 
	return "margin-left:35px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function NL_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:16px;";
	case 'qos_end_port': 
	return "margin-left:17px;";
	case 'qos_lan_ip': 
	return "margin-left:36px;";
	case 'qos_remote_ip': 
	return "margin-left:23px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function NO_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:27px;";
	case 'qos_end_port': 
	return "margin-left:30px;";
	case 'qos_lan_ip': 
	return "margin-left:39px;";
	case 'qos_remote_ip': 
	return "margin-left:17px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function PL_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:3px;";
	case 'qos_end_port': 
	return "margin-left:24px;";
	case 'qos_lan_ip': 
	return "margin-left:68px;";
	case 'qos_remote_ip': 
	return "margin-left:10px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function PTBR_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:2px;";
	case 'qos_end_port': 
	return "margin-left:32px;";
	case 'qos_lan_ip': 
	return "margin-left:31px;";
	case 'qos_remote_ip': 
	return "margin-left:35px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function RU_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:16px;";
	case 'qos_end_port': 
	return "margin-left:28px;";
	case 'qos_lan_ip': 
	return "margin-left:24px;";
	case 'qos_remote_ip': 
	return "margin-left:4px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function SV_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:28px;";
	case 'qos_end_port': 
	return "margin-left:35px;";
	case 'qos_lan_ip': 
	return "margin-left:39px;";
	case 'qos_remote_ip': 
	return "margin-left:37px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function TR_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:2px;";
	case 'qos_end_port': 
	return "margin-left:39px;";
	case 'qos_lan_ip': 
	return "margin-left:35px;";
	case 'qos_remote_ip': 
	return "margin-left:31px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function ZHCN_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:9px;";
	case 'qos_end_port': 
	return "margin-left:9px;";
	case 'qos_lan_ip': 
	return "margin-left:24px;";
	case 'qos_remote_ip': 
	return "margin-left:23px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function ZHTW_StyleGet(name)
{
	switch (name)
	{
	case 'qos_start_port': 
	return "margin-left:18px;";
	case 'qos_end_port': 
	return "margin-left:18px;";
	case 'qos_lan_ip': 
	return "margin-left:49px;";
	case 'qos_remote_ip': 
	return "margin-left:48px;";
	case 'qos_protocol': 
	return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}	
}
function lanugage_StyleSet(lang , location)
{
	lang = lang.toUpperCase();
	switch (lang)
	{
	case 'CS': 
	return CS_StyleSet(location);
	case 'DE': 
	return DE_StyleSet(location);
	case 'EN': 
	return EN_StyleSet(location);
	case 'ES': 
	return ES_StyleSet(location);
	case 'FR': 
	return FR_StyleSet(location);
	case 'HU': 
	return HU_StyleSet(location);
	case 'IT': 
	return IT_StyleSet(location);
	case 'JA': 
	return JA_StyleSet(location);
	case 'KO': 
	return KO_StyleSet(location);
	case 'NL': 
	return NL_StyleSet(location);
	case 'NO': 
	return NO_StyleSet(location);
	case 'PL': 
	return PL_StyleSet(location);
	case 'PTBR': 
	return PTBR_StyleSet(location);
	case 'RU': 
	return RU_StyleSet(location);
	case 'SV': 
	return SV_StyleSet(location);
	case 'TR': 
	return TR_StyleSet(location);
	case 'ZHCN': 
	return ZHCN_StyleSet(location);
	case 'ZHTW': 
	return ZHTW_StyleSet(location);
	}	
}
function CS_StyleSet(location)
{
	switch (location)
	{
		case 'storage':
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "180px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "180px";
			break;
		case 'wlan_wps':
			if(document.getElementById("gen_pin")) document.getElementById("gen_pin").className = "button_blueX3";
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className = "button_blueX3";
			break;
		default:
	}	
}
function DE_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("add_ntp_span")) document.getElementById("add_ntp_span").className = "value_left380";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left380";
			if(document.getElementById("select_span")) document.getElementById("select_span").className = "value_left380";
			if(document.getElementById("update_span")) document.getElementById("update_span").className = "value_left380";
			if(document.getElementById("ntpadd")) document.getElementById("ntpadd").className = "button_blueX1p5";
			if(document.getElementById("ntpdel")) document.getElementById("ntpdel").className = "button_blueX1p5";
			if(document.getElementById("ntp_sync")) document.getElementById("ntp_sync").className = "button_blueX1p5";
			break;
		case 'storage':
			if(document.getElementById("WG_name")) document.getElementById("WG_name").style.left = "170px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className = "button_blueX3";
			break;
		case 'main_internet':
			if(document.getElementById("btn_skip")) document.getElementById("btn_skip").style.fontSize = "13px";
			break;
		default:
	}	
}
function EN_StyleSet(location)
{
	switch (location)
	{
		default:
	}
}
function ES_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className = "value_left200";
			if(document.getElementById("TZ")) document.getElementById("TZ").className = "value_left200";
			if(document.getElementById("EDS")) document.getElementById("EDS").className = "value_left200";
			if(document.getElementById("DSO")) document.getElementById("DSO").className = "value_left200";
			if(document.getElementById("DSD")) document.getElementById("DSD").className = "value_left200";
			if(document.getElementById("add_ntp_span")) document.getElementById("add_ntp_span").className = "value_left320";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left320";
			if(document.getElementById("select_span")) document.getElementById("select_span").className = "value_left320";
			if(document.getElementById("update_span")) document.getElementById("update_span").className = "value_left320";
			break;
		case 'storage':
			if(document.getElementById("WG_name")) document.getElementById("WG_name").style.left = "190px";
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "190px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "190px";
			break;
		case 'main_internet':
			if(document.getElementById("DG")) document.getElementById("DG").style.fontSize = "13px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}
}
function FR_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className= "value_left175";
			if(document.getElementById("TZ")) document.getElementById("TZ").className= "value_left175";
			if(document.getElementById("EDS")) document.getElementById("EDS").className= "value_left175";
			if(document.getElementById("DSO")) document.getElementById("DSO").className= "value_left175";
			if(document.getElementById("DSD")) document.getElementById("DSD").className= "value_left175";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left350";
			break;
		case 'storage':
			if(document.getElementById("WG_name")) document.getElementById("WG_name").style.left = "170px";
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "170px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "170px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className = "button_blueX4";
			break;
		default:
	}		
}
function HU_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("add_ntp_span")) document.getElementById("add_ntp_span").className = "value_left380";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left380";
			if(document.getElementById("select_span")) document.getElementById("select_span").className = "value_left380";
			if(document.getElementById("update_span")) document.getElementById("update_span").className = "value_left380";
			if(document.getElementById("ntpadd")) document.getElementById("ntpadd").className = "button_blueX1p5";
			if(document.getElementById("ntpdel")) document.getElementById("ntpdel").className = "button_blueX1p5";
			if(document.getElementById("ntp_sync")) document.getElementById("ntp_sync").className = "button_blueX1p5";
			break;
		case 'storage':
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "170px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "170px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX4";
			break;
		default:
	}		
}
function IT_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className= "value_left150";
			if(document.getElementById("TZ")) document.getElementById("TZ").className= "value_left150";
			if(document.getElementById("EDS")) document.getElementById("EDS").className= "value_left150";
			if(document.getElementById("DSO")) document.getElementById("DSO").className= "value_left150";
			if(document.getElementById("DSD")) document.getElementById("DSD").className= "value_left150";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}		
}
function JA_StyleSet(location)
{
	switch (location)
	{
		case 'tools_fwup':
			if(document.getElementById("CFV")) document.getElementById("CFV").className= "value_left470";
			if(document.getElementById("CFD")) document.getElementById("CFD").className= "value_left470";
			if(document.getElementById("CON")) document.getElementById("CON").className= "value_left470";
			if(document.getElementById("UPLOAD")) document.getElementById("UPLOAD").className= "button_blueX1p5";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}	
}
function KO_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
function NL_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("add_ntp_span")) document.getElementById("add_ntp_span").className = "value_left380";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left380";
			if(document.getElementById("select_span")) document.getElementById("select_span").className = "value_left380";
			if(document.getElementById("update_span")) document.getElementById("update_span").className = "value_left380";
			if(document.getElementById("ntpadd")) document.getElementById("ntpadd").className = "button_blueX1p5";
			if(document.getElementById("ntpdel")) document.getElementById("ntpdel").className = "button_blueX1p5";
			if(document.getElementById("ntp_sync")) document.getElementById("ntp_sync").className = "button_blueX1p5";
			break;
		case 'storage':
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "170px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "170px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}		
}
function NO_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
function PL_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className= "value_c";
			if(document.getElementById("TZ")) document.getElementById("TZ").className= "value_c";
			if(document.getElementById("EDS")) document.getElementById("EDS").className= "value_c";
			if(document.getElementById("DSO")) document.getElementById("DSO").className= "value_c";
			if(document.getElementById("DSD")) document.getElementById("DSD").className= "value_c";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left350";
			break;
		case 'tools_fwup':
			if(document.getElementById("CFV")) document.getElementById("CFV").className= "value_left470";
			if(document.getElementById("CFD")) document.getElementById("CFD").className= "value_left470";
			if(document.getElementById("CON")) document.getElementById("CON").className= "value_left470";
			break;
		case 'storage':
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "190px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "190px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}		
}
function PTBR_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className= "value_left200";
			if(document.getElementById("TZ")) document.getElementById("TZ").className= "value_left200";
			if(document.getElementById("EDS")) document.getElementById("EDS").className= "value_left200";
			if(document.getElementById("DSO")) document.getElementById("DSO").className= "value_left200";
			if(document.getElementById("DSD")) document.getElementById("DSD").className= "value_left200";
			if(document.getElementById("add_ntp_span")) document.getElementById("add_ntp_span").className = "value_left350";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left350";
			if(document.getElementById("select_span")) document.getElementById("select_span").className = "value_left350";
			if(document.getElementById("update_span")) document.getElementById("update_span").className = "value_left350";
			break;
		case 'storage':
			if(document.getElementById("WG_name")) document.getElementById("WG_name").style.left = "180px";
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "180px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "180px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").style.fontSize = "13px";
			break;
		default:
	}	
}
function RU_StyleSet(location)
{
	switch (location)
	{
		case 'tools_time':
			if(document.getElementById("st_time")) document.getElementById("st_time").className= "value_left200";
			if(document.getElementById("TZ")) document.getElementById("TZ").className= "value_left200";
			if(document.getElementById("EDS")) document.getElementById("EDS").className= "value_left200";
			if(document.getElementById("DSO")) document.getElementById("DSO").className= "value_left200";
			if(document.getElementById("DSD")) document.getElementById("DSD").className= "value_left200";
			if(document.getElementById("checkbox_span")) document.getElementById("checkbox_span").className = "value_left470";
			break;
		case 'storage':
			if(document.getElementById("WD_user")) document.getElementById("WD_user").style.left = "190px";
			if(document.getElementById("WD_pw")) document.getElementById("WD_pw").style.left = "190px";
			break;
		case 'wlan_wps':
			if(document.getElementById("reset_pin")) document.getElementById("reset_pin").className= "button_blueX3";
			break;
		default:
	}	
}
function SV_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
function TR_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
function ZHCN_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
function ZHTW_StyleSet(location)
{
	switch (location)
	{
		default:
	}	
}
