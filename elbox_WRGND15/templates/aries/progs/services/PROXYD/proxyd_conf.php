<?
if($config_file=="") { $config_file="/var/etc/proxyd.conf"; }

$http_ip= query("/runtime/services/http/server/ipaddr");
if($http_ip == "")
{
	$http_ip= query("/inet/entry:1/ipv4/ipaddr");
}

$timeout_connect = query("/proxyd/timeout_connect");
if($timeout_connect=="") {$timeout_connect="10";}
$timeout_read = query("/proxyd/timeout_read");
if($timeout_read=="") {$timeout_read="10";}
$timeout_write = query("/proxyd/timeout_write");
if($timeout_write=="") {$timeout_write="10";}
$max_client = query("/proxyd/max_client");
if($max_client=="") {$max_client="512";}

fwrite("w", $config_file, "CONTROL\n{\n");
fwrite("a", $config_file, "\tTIMEOUT_CONNECT\t".$timeout_connect."\n");
fwrite("a", $config_file, "\tTIMEOUT_READ\t".$timeout_read."\n");
fwrite("a", $config_file, "\tTIMEOUT_WRITE\t".$timeout_write."\n");
fwrite("a", $config_file, "\tMAX_CLIENT\t".$max_client."\n");
fwrite("a", $config_file, "}\n\n");

fwrite("a", $config_file, "HTTP\n{\n");
fwrite("a", $config_file, "\tINTERFACE\t"."br0"."\n");
fwrite("a", $config_file, "\tPORT\t5449\n");
fwrite("a", $config_file, "\tALLOW_TYPE\t{ gif jpg css png }\n");
fwrite("a", $config_file, "\tERROR_PAGE\n\t{\n");
fwrite("a", $config_file, "\t\tdefault\thttp://".$http_ip."/parent_ctrl_block.php\n");
fwrite("a", $config_file, "\t\t403\thttp://".$http_ip."/parent_ctrl_block.php\n");
fwrite("a", $config_file, "\t\t404\thttp://".$http_ip."/parent_ctrl_block.php\n");
fwrite("a", $config_file, "\t}\n}\n\n");
?>
