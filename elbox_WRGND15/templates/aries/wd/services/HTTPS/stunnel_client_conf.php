<?
if($config_file=="") { $config_file="/var/etc/stunnel_client_temp.conf"; }

fwrite("w", $config_file, "client = yes\n");
fwrite("a", $config_file, "cert = /etc/stunnel_cert.pem\n");
fwrite("a", $config_file, "key =/etc/stunnel.key\n");
fwrite("a", $config_file, "pid = /var/run/stunnel_client.pid\n");
fwrite("a", $config_file, "setuid = 0\n");
fwrite("a", $config_file, "setgid = 0\n");

fwrite("a", $config_file, "debug = 7\n");
fwrite("a", $config_file, "output = /var/log/stunnel.log\n");
fwrite("a", $config_file, "\n");
fwrite("a", $config_file, "[https]\n");
fwrite("a", $config_file, "accept = 127.0.0.1:".$port."\n");
fwrite("a", $config_file, "connect = ".$host.":443\n");

?>
