<?
$myname = $APACHE2_CONF_DIR.'/apache2_https.conf';
fwrite('w', $myname, '# Apache2 https only configurations\n');
fwrite('a', $myname, '
ServerName "Linux, HTTP/1.1, SSL, '.query('/runtime/device/modelname').' Ver '.query('/runtime/device/firmwareversion').'"
ServerRoot "/var"
ServerSignature Off
ServerTokens Prod
HostnameLookups Off
TraceEnable Off
Timeout 300
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 10
PidFile "/var/run/apache2_ssl.pid"
LockFile "/var/log/accept_ssl.lock"
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
ErrorLog  "|/internalhd/root/usr/bin/rotatelogs /var/log/error_ssl.%Y-%m-%d-%H-%M.log 1M"
#CustomLog "|/internalhd/root/usr/bin/rotatelogs /var/log/access_ssl.%Y-%m-%d-%H-%M.log 1M" combined
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

#	RewriteEngine On
#	RewriteCond %{SERVER_PORT} !^443$
#	RewriteRule ^/$ https://%{SERVER_NAME}${REQUEST_URI} [R]
<Directory />
	Options FollowSymLinks
	AllowOverride None
	Order deny,allow
	Deny from all
</Directory>
<Directory "/var/www/">
#	Options Indexes FollowSymLinks
	AllowOverride None
	Order allow,deny
	Allow from all
</Directory>

SSLRandomSeed startup file:/dev/urandom 1024
SSLRandomSeed connect file:/dev/urandom 1024

AddType application/x-x509-ca-cert .crt
AddType application/x-pkcs7-crl    .crl

SSLPassPhraseDialog  builtin

SSLSessionCache        "shmcb:/var/log/ssl_scache(512000)"
SSLSessionCacheTimeout  600

SSLMutex  "file:/var/log/ssl_mutex"
');

function vhost_ssl($cfgdir, $cfgfile, $ipaddr, $port)
{
	fwrite('a', $cfgfile, '
Listen '.$ipaddr.':'.$port.'
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

	SSLEngine on
	SSLCipherSuite ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP:+eNULL
	SSLCertificateFile "/internalhd/etc/apache2/server.crt"
	SSLCertificateKeyFile "/internalhd/etc/apache2/server.key"
	#SSLCertificateChainFile "/var/etc/apache2/server-ca.crt"
	#SSLCACertificatePath "/var/etc/apache2/ssl.crt"
	#SSLCACertificateFile "/var/etc/apache2/ssl.crt/ca-bundle.crt"
	#SSLCARevocationPath "/var/etc/apache2/ssl.crl"
	#SSLCARevocationFile "/var/etc/apache2/ssl.crl/ca-bundle.crl"
	#SSLVerifyClient require
	#SSLVerifyDepth  10
	#<Location />
	#SSLRequire (    %{SSL_CIPHER} !~ m/^(EXP|NULL)/ \
	#            and %{SSL_CLIENT_S_DN_O} eq "Snake Oil, Ltd." \
	#            and %{SSL_CLIENT_S_DN_OU} in {"Staff", "CA", "Dev"} \
	#            and %{TIME_WDAY} >= 1 and %{TIME_WDAY} <= 5 \
	#            and %{TIME_HOUR} >= 8 and %{TIME_HOUR} <= 20       ) \
	#           or %{REMOTE_ADDR} =~ m/^192\.76\.162\.[0-9]+$/
	#</Location>
	#SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire
	<FilesMatch "\.(cgi|shtml|phtml|php)$">
		SSLOptions +StdEnvVars
	</FilesMatch>
	<Directory "/usr/cgi-bin">
		SSLOptions +StdEnvVars
	</Directory>
	BrowserMatch ".*MSIE.*" \\
		nokeepalive ssl-unclean-shutdown \\
		downgrade-1.0 force-response-1.0
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
	//vhost_ssl($APACHE2_CONF_DIR, $myname, $ipaddr, '1643');
	vhost_ssl($APACHE2_CONF_DIR, $myname, '*', '1643');
}

fwrite('a', $myname, '
Include '.$APACHE2_CONF_DIR.'/autoindex.conf
Include /internalhd/etc/apache2/webdav.conf
');
?>

