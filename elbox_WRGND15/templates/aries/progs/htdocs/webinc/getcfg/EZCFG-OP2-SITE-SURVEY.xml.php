<?include "/htdocs/phplib/xnode.php";?>
<module>
<service><?=$GETCFG_SVC?></service>
<ezcfg>
<?
echo "<sitesurvey>\n";
echo "<enable_24g>".dump(0, "/ezcfg/sitesurvey/enable_24g")."</enable_24g>\n";
echo "<enable_5g>".dump(0, "/ezcfg/sitesurvey/enable_5g")."</enable_5g>\n";
echo "<ssid></ssid>\n";
echo "<network_key></network_key>\n";
echo "<security></security>\n";
echo "<type></type>\n";
echo "<wps></wps>\n";
echo "</sitesurvey>\n";
echo "<sitedata>\n";
echo dump(0, "/runtime/site_survey");
echo "</sitedata>";
?>
</ezcfg>
</module>
