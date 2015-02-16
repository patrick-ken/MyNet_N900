<?
$webdav_conf = $EXTRA_CONF_DIR.'/apache2/webdav.conf';
//TRACE_debug("webdav_conf=".$webdav_conf);
if (isfile($webdav_conf)!=1)
{
fwrite('w', $webdav_conf, '# webdav configurations\n');
fwrite('a', $webdav_conf, '
DAVLockDB /internalhd/etc/apache2/DAVLock
Alias /Public /shares/Public

<Directory /shares/Public/>
        Dav on
        Allow from all
        AuthName DeviceUser
        AuthType Digest
        AuthDigestProvider file
        AuthDigestDomain /shares/ /shares/Public/ /Public/
        AuthUserFile /internalhd/etc/apache2/htpasswd
        <Limit GET PROPFIND COPY>
            Require valid-user
        </Limit>
        <Limit POST PUT DELETE MOVE MKCOL PROPPATCH LOCK>
        </Limit>
</Directory>
');
}
?>
