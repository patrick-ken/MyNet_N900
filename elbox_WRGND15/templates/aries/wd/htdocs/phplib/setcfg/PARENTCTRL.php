<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */
set("/security/netstar/enable", query($SETCFG_prefix."/security/netstar/enable"));
set("/security/netstar/registed", query($SETCFG_prefix."/security/netstar/registed"));
set("/security/netstar/location", query($SETCFG_prefix."/security/netstar/location"));
set("/security/netstar/email", query($SETCFG_prefix."/security/netstar/email"));
set("/security/netstar/password", query($SETCFG_prefix."/security/netstar/password"));
?>