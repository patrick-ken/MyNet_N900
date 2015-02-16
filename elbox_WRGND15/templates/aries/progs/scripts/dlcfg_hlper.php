<?
if ($ACTION=="STARTTODOWNLOADFILE")
{
	/* hendry,fix :I want a node that is very UNIQUE !! */
	set("/runtime/hendry_user_setting_tmp","");
	mov("/device/account","/runtime/hendry_user_setting_tmp");
	set("/runtime/hendry_user_setting_tmp_2","");
	mov("/inf:6/web","/runtime/hendry_user_setting_tmp_2");
	set("/runtime/hendry_user_setting_tmp_3","");
	mov("/inf:6/https_rport","/runtime/hendry_user_setting_tmp_3");
}
else if ($ACTION=="ENDTODOWNLOADFILE")
{
	mov("/runtime/hendry_user_setting_tmp/account","/device");
	del("/runtime/hendry_user_setting_tmp");
	mov("/runtime/hendry_user_setting_tmp_2/web","/inf:6");
	del("/runtime/hendry_user_setting_tmp_2");
	mov("/runtime/hendry_user_setting_tmp_3/https_rport","/inf:6");
	del("/runtime/hendry_user_setting_tmp_3");
}
?>
