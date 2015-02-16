<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<telnet_disable>
<?echo dump(0, "/runtime/device/telnet_disable");?>
</telnet_disable>
</module>
