<?
include "/htdocs/phplib/xnode.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w",$STOP,  "#!/bin/sh\n");

fwrite("a",$START, "TELNET_DISABLE=`xmldbc -g /runtime/device/telnet_disable`\n");
fwrite("a",$START, "if [ \"$TELNET_DISABLE\" != \"1\" ]; then\n");
fwrite("a",$START, "	mfc telnet_disable 0\n");
fwrite("a",$START, "	if [ -f \"/usr/sbin/login\" ]; then\n");
fwrite("a",$START, "        image_sign=`cat /etc/config/image_sign`\n");
fwrite("a",$START, "		telnetd -l /usr/sbin/login -u Alphanetworks:$image_sign -i br0 &\n");
fwrite("a",$START, "	else\n");
fwrite("a",$START, "		telnetd &\n");
fwrite("a",$START, "	fi\n");
fwrite("a",$START, "else\n");
fwrite("a",$START, "	mfc telnet_disable 1\n");
fwrite("a",$START, "	killall telnetd\n");
fwrite("a",$START, "fi\n");
fwrite("a",$START, "exit 0\n");
fwrite("a",$STOP,  "exit 0\n");
?>
