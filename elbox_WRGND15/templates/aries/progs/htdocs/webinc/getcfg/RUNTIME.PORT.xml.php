<module>
	<?
	include "/htdocs/phplib/xnode.php";
	include "/htdocs/phplib/trace.php";
	$nat = XNODE_getpathbytarget("/nat", "entry", "uid", "NAT-1");
	$infp = XNODE_getpathbytarget("", "inf", "uid", "WAN-1", 0);
	$inf = XNODE_getpathbytarget("", "inf", "uid", "LAN-1", 0);
	?>
	<service><?=$GETCFG_SVC?></service>
	<runtime>
		<upnpigd><?echo dump(3, "/runtime/upnpigd");?></upnpigd>
		<orion><?
		if(query("/runtime/orion")!="")
			echo dump(3, "/runtime/orion");
		?></orion>
	</runtime>
	<inf>
		<?
		if($infp!="")
		{
			echo "<web>".query($infp."/web")."</web>\n";
			echo "\t\t<https_rport>".query($infp."/https_rport")."</https_rport>";
		}
		?>
		<upnp>
			<count><? if ($inf!="") echo query($inf."/upnp/count"); ?></count>
		</upnp>
	</inf>
	<nat>
	<?
	if ($nat!="")
	{
		TRACE_debug($nat."/portforward");
		echo "<entry>\n";
		echo "\t\t<portforward>\n";
		echo dump(3, $nat."/portforward");
		echo "\t\t</portforward>\n";
		echo "\t</entry>";
	}
	?>
	</nat>
	<ACTIVATE>ignore</ACTIVATE>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>	
</module>
