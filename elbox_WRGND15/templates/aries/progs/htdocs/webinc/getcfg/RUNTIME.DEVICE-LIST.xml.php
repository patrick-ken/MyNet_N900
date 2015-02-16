<module>
	<service><?=$GETCFG_SVC?></service>
	<ACTIVATE>ignore</ACTIVATE>
	<FATLADY>ignore</FATLADY>
	<SETCFG>ignore</SETCFG>
	<runtime>
		<devlist>
			<userlist><? echo dump(0, "/runtime/devlist/userlist/"); ?></userlist>
		</devlist>
	</runtime>
</module>