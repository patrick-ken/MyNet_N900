<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<ezcfg>
<?
	echo "<modelname>".dump(0, "/runtime/device/modelname")."</modelname>\n";
	echo "<lanmac>".dump(0, "/runtime/devdata/lanmac")."</lanmac>\n";
	echo "<wanmac>".dump(0, "/runtime/devdata/wanmac")."</wanmac>\n";
?></ezcfg>
</module>
