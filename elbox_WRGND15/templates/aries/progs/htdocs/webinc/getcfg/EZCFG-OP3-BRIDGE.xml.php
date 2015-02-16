<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<ezcfg>
<?
echo "<ipaddr>".dump(0, "/ezcfg/ipaddr")."</ipaddr>\n";
?></ezcfg>
</module>
