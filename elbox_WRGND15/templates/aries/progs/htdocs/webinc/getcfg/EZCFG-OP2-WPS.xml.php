<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<ezcfg>
<?
echo "<wps>\n";
echo "<enable_24g>".dump(0, "/ezcfg/wps/enable_24g")."</enable_24g>\n";
echo "<enable_5g>".dump(0, "/ezcfg/wps/enable_5g")."</enable_5g>\n";
echo "<status>".dump(0, "/ezcfg/wps/status")."</status>\n";
echo "</wps>\n";
?>
</ezcfg>
</module>
