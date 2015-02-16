<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<entry>
<uid>SSHD-1</uid>
<active><?echo dump(0, "/runtime/sshd/active");?></active>
</entry>
</module>
