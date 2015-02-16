<module>
	<service><?=$GETCFG_SVC?></service>
	<SETCFG></SETCFG>
	<FATLADY></FATLADY>
	<ACTIVATE></ACTIVATE>
	<device>
		<hostname><?echo get("x", "/device/hostname");?></hostname>
	</device>
	<wd>
		<storage>
			<master><?echo get("x", "/wd/storage/master");?></master>
		</storage>
	</wd>
</module>
