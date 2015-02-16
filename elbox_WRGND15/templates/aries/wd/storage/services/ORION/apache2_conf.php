<?
fwrite('w', $APACHE2_CONF, '# Apache2 configurations\n');
fwrite('a', $APACHE2_CONF, '
ServerName "Linux, HTTP/1.1, '.query('/runtime/device/modelname').' Ver '.query('/runtime/device/firmwareversion').'"
ServerRoot "/var"
ServerSignature Off
ServerTokens Prod
HostnameLookups Off
TraceEnable Off
Timeout 300
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 10
PidFile "/var/run/apache2.pid"
LockFile "/var/log/accept.lock"
LimitRequestFields 100
LimitRequestLine 16000

<IfModule mpm_prefork_module>
	StartServers          5
	MinSpareServers       5
	MaxSpareServers       5
	MaxClients            5
	MaxRequestsPerChild   0
</IfModule>
<IfModule mpm_worker_module>
	StartServers          2
	MaxClients          150
	MinSpareThreads      25
	MaxSpareThreads      75
	ThreadsPerChild      25
	MaxRequestsPerChild   0
</IfModule>

User root
Group root

DefaultType text/plain
# update header version to 1.3.1
Header add X-Orion-Version "1.3.1"
## Hide apache server version and PHP version number in server response.
ServerTokens ProductOnly
ServerSignature Off

LoadModule php5_module /usr/modules/libphp5.so
LoadModule xsendfile_module /usr/modules/mod_xsendfile.so

<IfModule dir_module>
	DirectoryIndex index.php
</IfModule>

AccessFileName .htaccess
<FilesMatch "^\.ht">
	Order allow,deny
	Deny from all
	Satisfy All
</FilesMatch>

LogLevel error
ErrorLog  "|/internalhd/root/usr/bin/rotatelogs /var/log/error.%Y-%m-%d-%H-%M.log 1M"
#CustomLog "|/internalhd/root/usr/bin/rotatelogs /var/log/access.%Y-%m-%d-%H-%M.log 1M" combined
<IfModule log_config_module>
	LogFormat "%h %l %u %t \\\"%r\\\" %>s %b \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"" combined
	LogFormat "%h %l %u %t \\\"%r\\\" %>s %b" common
	<IfModule logio_module>
	# You need to enable mod_logio.c to use %I and %O
		LogFormat "%h %l %u %t \\\"%r\\\" %>s %b \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\" %I %O" combinedio
	</IfModule>
</IfModule>

<IfModule mime_module>
	TypesConfig '.$APACHE2_CONF_DIR.'/mime.types
	AddType application/x-compress .Z
	AddType application/x-gzip .gz .tgz
	AddType application/x-httpd-php .php .phtml
</IfModule>
');

function vhost($cfgfile, $ipaddr, $port)
{
	fwrite('a', $cfgfile, '
#Listen '.$ipaddr.':'.$port.'
Listen '.$port.'
<VirtualHost '.$ipaddr.':'.$port.'>
	ServerAdmin admin@localhost
	ServerName "Linux, HTTP/1.1, '.query('/runtime/device/modelname').' Ver '.query('/runtime/device/firmwareversion').'"
	SetEnv __ADMIN_API_ROOT /var/www/Admin
	DocumentRoot /var/www/
	RewriteEngine On
	RewriteRule ^/api/1.0/rest/(.*) /Admin/webapp/htdocs/api/1.0/rest/index.php [L]
	RewriteRule ^/LandingPage /Admin/webapp/htdocs/securityCheck.php [L,R]
	XSendFile on
	<Directory "/shares/Public">
		XSendFile on
		XSendFilePath /shares/Public
	</Directory>
	<Directory "/">
		Options FollowSymLinks
		XSendFile on
		XSendFilePath /internalhd/etc/orion
	</Directory>
	<Directory /var/www/Admin/webapp/config/>
		Order deny,allow
		Deny from all
	</Directory>
	<Directory /var/www/Admin/webapp/classes/>
		Order deny,allow
		Deny from all
	</Directory>
	<Directory /var/www/Admin/webapp/includes/>
		Order deny,allow
		Deny from all
	</Directory>
	<Directory "/var/www/Admin/webapp/htdocs/protected">
		AllowOverride None
		deny from all
	</Directory>
	<Directory /var/www/Admin/webapp/lib/>
		Order deny,allow
		Deny from all
	</Directory>
	<Directory /var/www/Admin/webapp/locale/>
		Order deny,allow
		Deny from all
	</Directory>
</VirtualHost>
');
}

foreach('/runtime/inf')
{
	$uid = query('uid');

	$islan = cut($uid, 0, '-');
	if ($islan != 'LAN') continue;
	$addrtype = query('inet/addrtype');
	if ($addrtype == 'ipv4')      {$ipaddr = query('inet/ipv4/ipaddr');}
	else if ($addrtype == 'ipv6') {$ipaddr = query('inet/ipv6/ipaddr');}

	if ($uid != 'LAN-1') {continue;}
	else                 {$ipaddr = query('inet/ipv4/ipaddr');}
	//vhost($APACHE2_CONF, $ipaddr, '1280');
	vhost($APACHE2_CONF, '*', '1280');
}

fwrite('a', $APACHE2_CONF, '
Include '.$APACHE2_CONF_DIR.'/autoindex.conf
Include /internalhd/etc/apache2/webdav.conf
');
?>
