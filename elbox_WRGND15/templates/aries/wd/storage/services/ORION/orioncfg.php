<? /* vi: set sw=4 ts=4: */
include '/htdocs/phplib/trace.php';
include '/htdocs/phplib/xnode.php';
include '/htdocs/phplib/phyinf.php';

if (isfile('/etc/services/ORION/apache2_conf.php')==1)
	include '/etc/services/ORION/apache2_conf.php';

if (isfile('/etc/services/ORION/mime_types.php')==1)
	include '/etc/services/ORION/mime_types.php';

if (isfile('/etc/services/ORION/apache2_https_conf.php')==1)
	include '/etc/services/ORION/apache2_https_conf.php';

if (isfile('/etc/services/ORION/autoindex_conf.php')==1)
	include '/etc/services/ORION/autoindex_conf.php';

if (isfile('/etc/services/ORION/webdav_conf.php')==1)
	include '/etc/services/ORION/webdav_conf.php';

if (isfile('/etc/services/ORION/server_crt.php')==1)
	include '/etc/services/ORION/server_crt.php';

if (isfile('/etc/services/ORION/server_key.php')==1)
	include '/etc/services/ORION/server_key.php';

if (isfile('/etc/services/ORION/php_ini.php')==1)
	include '/etc/services/ORION/php_ini.php';
?>
