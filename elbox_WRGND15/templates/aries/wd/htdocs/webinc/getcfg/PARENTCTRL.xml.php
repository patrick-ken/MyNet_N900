<module>
<service><?=$GETCFG_SVC?></service>
<security>
	<active><?echo dump(0, "/security/active");?></active>
	<netstar>
		<enable><?echo dump(0, "/security/netstar/enable");?></enable>
		<agent><?echo dump(0, "/security/netstar/agent");?></agent>
		<location_name_server><?echo dump(0, "/security/netstar/location_name_server");?></location_name_server>
		<location><?echo dump(0, "/security/netstar/location");?></location>
		<registed><?echo dump(0, "/security/netstar/registed");?></registed>
		<register_url><?echo dump(0, "/security/netstar/register_url");?></register_url>
		<config_url><?echo dump(0, "/security/netstar/config_url");?></config_url>
		<email><?echo dump(0, "/security/netstar/email");?></email>
		<password><?echo dump(0, "/security/netstar/password");?></password>
		<device_limit><?echo dump(0, "/security/netstar/device_limit");?></device_limit>
	</netstar>
</security>
<runtime>
	<devdata>
		<wanmac><?echo dump(0, "/runtime/devdata/wanmac");?></wanmac>
	</devdata>
	<netstar><?echo dump(0, "/runtime/netstar");?></netstar>
</runtime>
<device>
	<hostname><?echo dump(0, "/device/hostname");?></hostname>
</device>
</module>
