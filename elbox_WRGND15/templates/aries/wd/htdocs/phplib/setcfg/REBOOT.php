<?
/* setcfg is used to move the validated session data to the configuration database.
 * The variable, 'SETCFG_prefix',  will indicate the path of the session data. */

if(isfile("/var/stop_SE_RATE_ESTIMATION")==0)
{
	fwrite("w+", "/var/stop_SE_RATE_ESTIMATION", "1");
}
?>
