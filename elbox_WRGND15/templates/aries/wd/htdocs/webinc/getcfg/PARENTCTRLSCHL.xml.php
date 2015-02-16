<module>
	<service><?=$GETCFG_SVC?></service>
	<SETCFG></SETCFG>
	<FATLADY></FATLADY>
	<ACTIVATE></ACTIVATE>
	<security>
		<active><?echo dump(0, "/security/active");?></active>		
		<parental>
			<?echo dump(0, "/security/parental");?>
		</parental>
	</security>
</module>