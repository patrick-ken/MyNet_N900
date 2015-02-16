<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/phyinf.php";
//$sshd = XNODE_getpathbytarget("sshd", "entry", "uid", "SSHD-1", 0);
//$enable = query($sshd."/active");
$enable = query("/runtime/sshd/active");
fwrite("w",$START, "#!/bin/sh\n");
if ($enable=="1")
{
	fwrite("a",$START, "if [ -f /var/run/sshd.pid ]; then\n");
	fwrite("a",$START, "		PID=`cat /var/run/sshd.pid`\n");
	fwrite("a",$START, "		if [ \"$PID\" != \"0\" ]; then\n");
	fwrite("a",$START, "				kill $PID\n");
	fwrite("a",$START, "				rm -f /var/run/sshd.pid\n");
	fwrite("a",$START, "				rm -f /var/etc/sshd_config\n");
	fwrite("a",$START, "		fi\n");
	fwrite("a",$START, "fi\n");
	fwrite("a",$START, "deluser orion\n");
	fwrite("a",$START, "sleep 2\n");
	fwrite("a",$START, "adduser -H -D -G root orion > /var/etc/sshd_config\n");
	fwrite("a",$START, "echo \"orion:orion\" | chpasswd > /var/etc/sshd_config\n");
	fwrite("a",$START, "xmldbc -P /etc/services/SSH/sshdconf4.php > /var/etc/sshd_config\n");
	fwrite("a",$START, "sleep 1\n");
	fwrite("a",$START, "/usr/sbin/sshd & > /dev/console\n");
}
else
{
	fwrite("a",$START, "if [ -f /var/run/sshd.pid ]; then\n");
	fwrite("a",$START, "        PID=`cat /var/run/sshd.pid`\n");
	fwrite("a",$START, "        if [ \"$PID\" != \"0\" ]; then\n");
	fwrite("a",$START, "                kill $PID\n");
	fwrite("a",$START, "                rm -f /var/run/sshd.pid\n");
	fwrite("a",$START, "                rm -f /var/etc/sshd_config\n");
	fwrite("a",$START, "        fi\n");
	fwrite("a",$START, "fi\n");
	fwrite("a",$START, "deluser orion\n");
	fwrite("a",$START, "killall sshd\n");
}
fwrite("a",$START, "exit 0\n");
?>

