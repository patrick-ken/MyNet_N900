<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";

fwrite('w', $START, '#!/bin/sh\n');

$enable = query('/orion/enable');
if ($enable<=0)
{
	fwrite('a', $START, 'echo "Orion is inactive."\n');
	return 8;
}

$etc_root = '/var/etc';
$apache2_conf_dir = $etc_root.'/apache2';
$apache2_conf = $apache2_conf_dir.'/apache2.conf';
$apache2_https_conf = $apache2_conf_dir.'/apache2_https.conf';
$php_ini_dir = $etc_root.'/php';
$php_ini = $php_ini_dir.'/php.ini';
$extra_conf_dir = '/internalhd/etc';

fwrite('a', $START,
	'export PATH=$PATH:/internalhd/root/usr/bin:/internalhd/root/usr/sbin\n'.
	'/etc/scripts/check_orion_env.sh '.$apache2_conf_dir.' '.$php_ini_dir.'\n'.
	'xmldbc -P /etc/services/ORION/orioncfg.php -V PHP_INI='.$php_ini.' -V APACHE2_CONF_DIR='.$apache2_conf_dir.' -V APACHE2_CONF='.$apache2_conf.' -V EXTRA_CONF_DIR='.$extra_conf_dir.'\n'.
	'echo 32768 > /proc/sys/fs/inotify/max_user_watches\n'.
	'next=0\n'.
	'if [ -f '.$apache2_conf.' ]; then\n'.
	'	apache2 &\n'.
	'	next=1\n'.
	'else\n'.
	'   echo "[$0]: no config file!"\n'.
	'   exit 9\n'.
	'fi\n'.
	'if [ -f '.$apache2_https_conf.' ]; then\n'.
	'	apache2 -f '.$apache2_https_conf.' &\n'.
	'	next=1\n'.
	'else\n'.
	'   echo "[$0]: no config file with ssl!"\n'.
	'   exit 9\n'.
	'fi\n'.
	'if [ "$next" = "1" ]; then\n'.
	'	/usr/local/mediacrawler/mediacrawlerd start &\n'.
	'	/usr/local/orion/miocrawler/miocrawlerd start &\n'.
	'	/usr/local/orion/communicationmanager/communicationmanagerd start &\n'.
	'	xmldbc -t rotatelog:60:"/internalhd/root/rotatelog.sh"\n'.
	'fi\n'.
	'exit 0\n'
	);

/* stop the apache2 */
fwrite('w', $STOP,
	'#!/bin/sh\n'.
	'/usr/local/orion/communicationmanager/communicationmanagerd stop &\n'.
	'/usr/local/mediacrawler/mediacrawlerd stop &\n'.
	'/usr/local/orion/miocrawler/miocrawlerd stop &\n'.
	'killall -9 apache2\n'.
	'echo 8192 > /proc/sys/fs/inotify/max_user_watches\n'.
	'rm -rf '.$apache2_conf_dir.'\n'.
	'rm -rf '.$php_ini_dir.'\n'.
	'rm -rf /var/www\n'.
	'exit 0\n'
	);
?>
