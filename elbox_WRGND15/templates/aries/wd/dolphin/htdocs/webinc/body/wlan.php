<?
include "/htdocs/webinc/body/draw_elements.php";
include "/htdocs/phplib/wifi.php";
?>
<form>
	<div>
		<table>
			<tbody>
				<tr>
					<td style="width: 390px;vertical-align: top;">
						<div class="textinput">
							<span class="title"><?echo I18N("h", "Wireless 2.4Ghz");?></span>
							<span class="value_d">
								<input id="en_wifi" type="checkbox" class="styled" onClick="PAGE.OnClickEnWLAN('');" />
							</span>
						</div>
						<br>
						<?
                            if ($FEATURE_NOSCH!="1")
                            {
                            	echo '<div class="textinput" style="margin-bottom: 15px;">';
                            	echo '<span class="name_c">'.I18N("h", "Schedule").'</span>';
                            	echo '<span class="value_d">';
                                DRAW_select_sch("sch", I18N("h", "Always"), "", "", "0", "styled3");
                                echo '<input id="go2sch"  type="button" style="margin-top: 5px;" value="'.I18N("h", "New Schedule").'" onClick="javascript:self.location.href=\'./tools_sch.php\';" />\n';
                            	echo '</span>';
                            	echo '</div>';
                            }
                        ?>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Network Name (SSID)");?></span>
							<span class="value_d">
								<input id="ssid" class="same_width" type="text" maxlength="32">
							</span>
						</div>


						<!-- Security start -->
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Security Mode");?></span>
							<span class="value_d">
								 <select class="styled3" id="security_type" onChange="PAGE.OnChangeSecurityType('');">
                                        <option value=""><?echo I18N("h", "None");?></option>
                                 </select>
							</span>
						</div>

						<div id="wep">
							<div class="textinput">
								<span class="name_c"><?echo I18N("h", "Authentication");?></span>
								<span class="value_d">
									 <select class="styled3" id="auth_type" onChange="PAGE.OnChangeWEPAuth('');">
	                                    <option value="WEPAUTO"><?echo I18N("h", "Both");?></option>
	                                    <option value="SHARED"><?echo I18N("h", "Shared Key");?></option>
	                                </select>
								</span>
							</div>
							<div class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Encryption");?></span>
								<span class="value_d">
									<select class="styled3" id="wep_key_len" onChange="PAGE.OnChangeWEPKey('');">
	                                    <option value="64"><?echo I18N("h", "64Bit");?></option>
	                                    <option value="128"><?echo I18N("h", "128Bit");?></option>
	                                </select>
	                            </span>
	                        </div>
	                        <div class="textinput" style="display:none;">
	                            <span class="name_c"> </span>
	                            <span class="value_d">
	                                <select class="styled3" id="wep_def_key" style="display:none" onChange="PAGE.OnChangeWEPKey('');">
	                                        <option value="1"><?echo I18N("h", "WEP Key 1");?></option>
	                                </select>
								</span>
							</div>
							<div id="wep_64" class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
								<span class="value_d"><input class="same_width" id="wep_64_1" name="wepkey_64" type="text" maxlength="10" /></span>
							</div>
							<div id="wep_64_text" class="textinput2">
								
								<span class="value_k">(5 ASCII or 10 HEX)</span>
							</div>
							<div id="wep_128" class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
								<span class="value_d"><input class="same_width" id="wep_128_1" name="wepkey_128" type="text" maxlength="26" /></span>
							</div>
							<div id="wep_128_text" class="textinput2">
								
								<span class="value_k">(13 ASCII or 26 HEX)</span>
							</div>
						</div>

						<div id="wpa">
							<div class="textinput" style="display:none;">
								<span class="name_c"><?echo I18N("h", "Cipher Type");?></span>
								<span class="value_d">
									<select class="styled3" id="cipher_type">
	                                    <option value="TKIP">TKIP</option>
	                                    <option value="AES">AES</option>
	                                    <option value="TKIP+AES">TKIP and AES</option>
	                                </select>
								</span>
							</div>
							<div id="network_key" class="textinput" >
								<span class="name_c"><?echo I18N("h", "Password");?></span>
								<span class="value_d">
									<input class="same_width" id="wpapsk" type="text" maxlength="64">
								</span>
							</div>
							<div name="eap" class="textinput">
								<span class="name_d"><?echo I18N("h", "RADIUS Server IP Address");?></span>
								<span class="value_d"><input class="same_width" id="srv_ip" type="text" maxlength="15" /></span>
							</div>
							<div name="eap" class="textinput">
								<span class="name_c"><?echo I18N("h", "Port");?></span>
								<span class="value_d"><input class="same_width" id="srv_port" type="text" maxlength="5" /></span>
							</div>
							<div name="eap" class="textinput">
								<span class="name_c"><?echo I18N("h", "Shared Secret");?></span>
								<span class="value_d"><input class="same_width" id="srv_sec" type="text" maxlength="64" /></span>
							</div>
						</div>
						<!-- Security end -->

						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Network Mode");?></span>
							<span class="value_d">
								<select class="styled3" id="wlan_mode" onChange="PAGE.OnChangeWLMode('');">
                                    <option value="b"><?echo I18N("h", "802.11b only");?></option>
                                    <option value="g"><?echo I18N("h", "802.11g only");?></option>
                                    <option value="n"><?echo I18N("h", "802.11n only");?></option>
                                    <option value="bg"><?echo I18N("h", "Mixed 802.11 b+g");?></option>
                                    <option value="gn"><?echo I18N("h", "Mixed 802.11 g+n");?></option>
                                    <option value="bgn"><?echo I18N("h", "Mixed 802.11 b+g+n");?></option>
                                </select>
							</span>
						</div>

						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Channel Width");?></span>
							<span class="value_d">
								<select class="styled3" id="bw">
	                                <option value="20">20 MHz</option>
	                                <option value="20+40">20/40 MHz(<?echo I18N("h", "Auto");?>)</option>
	                            </select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Channel");?></span>
							<span class="value_d">
								<select class="styled3" id="channel" onchange="PAGE.OnChangeChannel('');">
									<option value="auto"><?echo I18N("h", "Auto");?></option>
								<?
									$clist = WIFI_getchannellist();
									$count = cut_count($clist, ",");

									$i = 0;
									while($i < $count)
									{
										$ch = cut($clist, $i, ',');
										$str = $ch;
										//for 2.4 Ghz
										if		($ch=="1")	 { $str = "2.412 GHz - CH 1";  }
										else if ($ch=="2")   { $str = "2.417 GHz - CH 2";  }
										else if ($ch=="3")   { $str = "2.422 GHz - CH 3";  }
										else if ($ch=="4")   { $str = "2.427 GHz - CH 4";  }
										else if ($ch=="5")   { $str = "2.432 GHz - CH 5";  }
										else if ($ch=="6")   { $str = "2.437 GHz - CH 6";  }
										else if ($ch=="7")   { $str = "2.442 GHz - CH 7";  }
										else if ($ch=="8")   { $str = "2.447 GHz - CH 8";  }
										else if ($ch=="9")   { $str = "2.452 GHz - CH 9";  }
										else if ($ch=="10")  { $str = "2.457 GHz - CH 10"; }
										else if ($ch=="11")  { $str = "2.462 GHz - CH 11"; }
										else if ($ch=="12")  { $str = "2.467 GHz - CH 12"; }
										else if ($ch=="13")  { $str = "2.472 GHz - CH 13"; }
										else if ($ch=="14")  { $str = "2.484 GHz - CH 14"; }

										//for 5 Ghz
										else if	($ch=="34")	   { $str = "5.170 GHz - CH 34";   }
										else if	($ch=="38")	   { $str = "5.190 GHz - CH 38";   }
										else if	($ch=="42")	   { $str = "5.210 GHz - CH 42";   }
										else if	($ch=="46")	   { $str = "5.230 GHz - CH 46";   }

										else if	($ch=="36")	   { $str = "5.180 GHz - CH 36";   }
										else if ($ch=="40")    { $str = "5.200 GHz - CH 40";   }
										else if ($ch=="44")    { $str = "5.220 GHz - CH 44";   }
										else if ($ch=="48")    { $str = "5.240 GHz - CH 48";   }
										else if ($ch=="52")    { $str = "5.260 GHz - CH 52";   }
										else if ($ch=="56")    { $str = "5.280 GHz - CH 56";   }
										else if ($ch=="60")    { $str = "5.300 GHz - CH 60";   }
										else if ($ch=="64")    { $str = "5.320 GHz - CH 64";   }
										else if ($ch=="100")   { $str = "5.500 GHz - CH 100";  }
										else if ($ch=="104")   { $str = "5.520 GHz - CH 104";  }
										else if ($ch=="108")   { $str = "5.540 GHz - CH 108";  }
										else if ($ch=="112")   { $str = "5.560 GHz - CH 112";  }
										else if ($ch=="116")   { $str = "5.580 GHz - CH 116";  }
										else if ($ch=="120")   { $str = "5.600 GHz - CH 120";  }
										else if ($ch=="124")   { $str = "5.620 GHz - CH 124";  }
										else if ($ch=="128")   { $str = "5.640 GHz - CH 128";  }
										else if ($ch=="132")   { $str = "5.660 GHz - CH 132";  }
										else if ($ch=="136")   { $str = "5.680 GHz - CH 136";  }
										else if ($ch=="140")   { $str = "5.700 GHz - CH 140";  }

										else if ($ch=="149")   { $str = "5.745 GHz - CH 149";  }
										else if ($ch=="153")   { $str = "5.765 GHz - CH 153";  }
										else if ($ch=="157")   { $str = "5.785 GHz - CH 157";  }
										else if ($ch=="161")   { $str = "5.805 GHz - CH 161";  }
										else if ($ch=="165")   { $str = "5.825 GHz - CH 165";  }
										else if ($ch=="184")   { $str = "4.920 GHz - CH 184";  }
										else if ($ch=="188")   { $str = "4.940 GHz - CH 188";  }
										else if ($ch=="192")   { $str = "4.960 GHz - CH 192";  }
										else if ($ch=="196")   { $str = "4.980 GHz - CH 196";  }
										else { $str = $ch ; }

										echo '\t\t\t\t<option value="'.$ch.'">'.$str.'</option>\n';
										$i++;
									}
								?>
								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "SSID Broadcast");?></span>
							<span class="value_d">
								<input id="ssid_visible" type="checkbox" class="styled" />
							</span>
						</div>
						<br>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "WMM QoS");?></span>
							<span class="value_d">
								<input id="en_wmm" type="checkbox" class="styled" />
							</span>
						</div>
					</td>
					<!--<td width="1" bgcolor="#939393" style="border-left: 1px; padding: 0px; border-right-width: 0px; height: 515px; margin-left: 0pt;"><br></td>-->
					<td id="div_5G" style="width: 390px;vertical-align: top;">
						<div class="textinput">
							<span class="title"><?echo I18N("h", "Wireless 5Ghz");?></span>
							<span class="value_d">
								<input id="en_wifi_Aband" type="checkbox" class="styled" onClick="PAGE.OnClickEnWLAN('_Aband');"/>
							</span>
						</div>
						<br>
						<?
                            if ($FEATURE_NOSCH!="1")
                            {
                            	echo '<div class="textinput" style="margin-bottom: 15px;">';
                            	echo '<span class="name_c">'.I18N("h", "Schedule").'</span>';
                            	echo '<span class="value_d">';
								DRAW_select_sch("sch_Aband", I18N("h", "Always"), "", "", "0", "styled3");
								echo '<input id="go2sch_Aband" type="button" style="margin-top: 5px;" value="'.I18N("h", "New Schedule").'" onClick="javascript:self.location.href=\'./tools_sch.php\';" />\n';
                            	echo '</span>';
                            	echo '</div>';
                            }
                        ?>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Network Name (SSID)");?></span>
							<span class="value_d">
								<input class="same_width" id="ssid_Aband" type="text" maxlength="32">
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Security Mode");?></span>
							<span class="value_d">
								 <select class="styled3" id="security_type_Aband" onChange="PAGE.OnChangeSecurityType('_Aband');">
										<option value=""><?echo I18N("h", "None");?></option>
									</select>
							</span>
						</div>

						<div id="wep_Aband">
							<div class="textinput">
								<span class="name_c"><?echo I18N("h", "Authentication");?></span>
								<span class="value_d">
									 <select class="styled3" id="auth_type_Aband" onChange="PAGE.OnChangeWEPAuth('_Aband');">
										<!--<option value="OPEN">Open</option>-->
										<option value="WEPAUTO"><?echo I18N("h", "Both");?></option>
										<option value="SHARED"><?echo I18N("h", "Shared Key");?></option>
									</select>
								</span>
							</div>
							<div class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Encryption");?></span>
								<span class="value_d">
									<select class="styled3" id="wep_key_len_Aband" onChange="PAGE.OnChangeWEPKey('_Aband');">
	                                    <option value="64"><?echo I18N("h", "64Bit");?></option>
	                                    <option value="128"><?echo I18N("h", "128Bit");?></option>
	                                </select>
	                            </span>
	                        </div>
	                        <div class="textinput" style="display:none;">
	                            <span class="name_c"> </span>
	                            <span class="value_d">
	                                <select class="styled3" id="wep_def_key_Aband" style="display:none" onChange="PAGE.OnChangeWEPKey('_Aband');">
	                                        <option value="1"><?echo I18N("h", "WEP Key 1");?></option>
	                                </select>
								</span>
							</div>
							<div id="wep_64_Aband" class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
								<span class="value_d"><input class="same_width" id="wep_64_1_Aband" name="wepkey_64_Aband" type="text" maxlength="10" /></span>
							</div>
							<div id="wep_64_Aband_text" class="textinput2">
								
								<span class="value_k">(5 ASCII or 10 HEX)</span>
							</div>
							<div id="wep_128_Aband" class="textinput">
								<span class="name_c"><?echo I18N("h", "WEP Key");?></span>
								<span class="value_d"><input class="same_width" id="wep_128_1_Aband" name="wepkey_128_Aband" type="text" maxlength="26" /></span>
							</div>
							<div id="wep_128_Aband_text" class="textinput2">
								
								<span class="value_k">(13 ASCII or 26 HEX)</span>
							</div>
						</div>
						<div id="wpa_Aband">
							<div class="textinput" style="display:none;">
								<span class="name_c"><?echo I18N("h", "Cipher Type");?></span>
								<span class="value_d">
									<select class="styled3" id="cipher_type_Aband">
										<option value="TKIP">TKIP</option>
										<option value="AES">AES</option>
										<option value="TKIP+AES">TKIP and AES</option>
									</select>
								</span>
							</div>

							<div id="network_key_Aband" class="textinput">
								<span class="name_c"><?echo I18N("h", "Password");?></span>
								<span class="value_d">
									<input class="same_width" id="wpapsk_Aband" type="text" maxlength="64">
								</span>
							</div>

							<div name="eap_Aband" class="textinput">
								<span class="name_d"><?echo I18N("h", "RADIUS Server IP Address");?></span>
								<span class="value_d"><input class="same_width" id="srv_ip_Aband" type="text" maxlength="15" /></span>
							</div>
							<div name="eap_Aband" class="textinput">
								<span class="name_c"><?echo I18N("h", "Port");?></span>
								<span class="value_d"><input class="same_width" id="srv_port_Aband" type="text" maxlength="5" /></span>
							</div>
							<div name="eap_Aband" class="textinput">
								<span class="name_c"><?echo I18N("h", "Shared Secret");?></span>
								<span class="value_d"><input class="same_width" id="srv_sec_Aband" type="text" maxlength="64" /></span>
							</div>
						</div>

						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Network Mode");?></span>
							<span class="value_d">
								<select class="styled3" id="wlan_mode_Aband" onChange="PAGE.OnChangeWLMode('_Aband');">
									<option value="a"><?echo I18N("h", "802.11a only");?></option>
					                <option value="n"><?echo I18N("h", "802.11n only");?></option>
									<option value="an"><?echo I18N("h", "Mixed 802.11 a+n");?></option>
									<option value="ac"><?echo I18N("h", "802.11 ac");?></option>
								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Channel Width");?></span>
							<span class="value_d">
								<select class="styled3" id="bw_Aband">
									<option value="20">20 MHz</option>
									<option value="20+40">20/40 MHz(<?echo I18N("h", "Auto");?>)</option>
								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "Channel");?></span>
							<span class="value_d">
								<select class="styled3" id="channel_Aband" onchange="PAGE.OnChangeChannel('_Aband');">
									<option value="auto"><?echo I18N("h", "Auto");?></option>
<?
	$clist = WIFI_getchannellist("a");
	$count = cut_count($clist, ",");

	$i = 0;
	while($i < $count)
	{
		$ch = cut($clist, $i, ',');
		$str = $ch;
		//for 2.4 Ghz
		if		($ch=="1")	 { $str = "2.412 GHz - CH 1";  }
		else if ($ch=="2")   { $str = "2.417 GHz - CH 2";  }
		else if ($ch=="3")   { $str = "2.422 GHz - CH 3";  }
		else if ($ch=="4")   { $str = "2.427 GHz - CH 4";  }
		else if ($ch=="5")   { $str = "2.432 GHz - CH 5";  }
		else if ($ch=="6")   { $str = "2.437 GHz - CH 6";  }
		else if ($ch=="7")   { $str = "2.442 GHz - CH 7";  }
		else if ($ch=="8")   { $str = "2.447 GHz - CH 8";  }
		else if ($ch=="9")   { $str = "2.452 GHz - CH 9";  }
		else if ($ch=="10")  { $str = "2.457 GHz - CH 10"; }
		else if ($ch=="11")  { $str = "2.462 GHz - CH 11"; }
		else if ($ch=="12")  { $str = "2.467 GHz - CH 12"; }
		else if ($ch=="13")  { $str = "2.472 GHz - CH 13"; }
		else if ($ch=="14")  { $str = "2.484 GHz - CH 14"; }

		//for 5 Ghz
		else if	($ch=="34")	   { $str = "5.170 GHz - CH 34";   }
		else if	($ch=="38")	   { $str = "5.190 GHz - CH 38";   }
		else if	($ch=="42")	   { $str = "5.210 GHz - CH 42";   }
		else if	($ch=="46")	   { $str = "5.230 GHz - CH 46";   }

		else if	($ch=="36")	   { $str = "5.180 GHz - CH 36";   }
		else if ($ch=="40")    { $str = "5.200 GHz - CH 40";   }
		else if ($ch=="44")    { $str = "5.220 GHz - CH 44";   }
		else if ($ch=="48")    { $str = "5.240 GHz - CH 48";   }
		else if ($ch=="52")    { $str = "5.260 GHz - CH 52";   }
		else if ($ch=="56")    { $str = "5.280 GHz - CH 56";   }
		else if ($ch=="60")    { $str = "5.300 GHz - CH 60";   }
		else if ($ch=="64")    { $str = "5.320 GHz - CH 64";   }
		else if ($ch=="100")   { $str = "5.500 GHz - CH 100";  }
		else if ($ch=="104")   { $str = "5.520 GHz - CH 104";  }
		else if ($ch=="108")   { $str = "5.540 GHz - CH 108";  }
		else if ($ch=="112")   { $str = "5.560 GHz - CH 112";  }
		else if ($ch=="116")   { $str = "5.580 GHz - CH 116";  }
		else if ($ch=="120")   { $str = "5.600 GHz - CH 120";  }
		else if ($ch=="124")   { $str = "5.620 GHz - CH 124";  }
		else if ($ch=="128")   { $str = "5.640 GHz - CH 128";  }
		else if ($ch=="132")   { $str = "5.660 GHz - CH 132";  }
		else if ($ch=="136")   { $str = "5.680 GHz - CH 136";  }
		else if ($ch=="140")   { $str = "5.700 GHz - CH 140";  }

		else if ($ch=="149")   { $str = "5.745 GHz - CH 149";  }
		else if ($ch=="153")   { $str = "5.765 GHz - CH 153";  }
		else if ($ch=="157")   { $str = "5.785 GHz - CH 157";  }
		else if ($ch=="161")   { $str = "5.805 GHz - CH 161";  }
		else if ($ch=="165")   { $str = "5.825 GHz - CH 165";  }
		else if ($ch=="184")   { $str = "4.920 GHz - CH 184";  }
		else if ($ch=="188")   { $str = "4.940 GHz - CH 188";  }
		else if ($ch=="192")   { $str = "4.960 GHz - CH 192";  }
		else if ($ch=="196")   { $str = "4.980 GHz - CH 196";  }
		else { $str = $ch ; }

		echo '\t\t\t\t<option value="'.$ch.'">'.$str.'</option>\n';
		$i++;
	}

?>								</select>
							</span>
						</div>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "SSID Broadcast");?></span>
							<span class="value_d">
								<input id="ssid_visible_Aband" type="checkbox" class="styled" />
							</span>
						</div>
						<br>
						<div class="textinput">
							<span class="name_c"><?echo I18N("h", "WMM QoS");?></span>
							<span class="value_d">
								<input id="en_wmm_Aband" type="checkbox" class="styled" />
							</span>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div style="height:30px;"></div>
		<hr>
		<div class="bottom_cancel_save">
			<input type="button" class="button_black" id="reload" onclick="PAGE.InitLocalVariables(); BODY.OnReload();" value="<?echo I18N('h', 'Cancel');?>">&nbsp;&nbsp;
			<input type="button" class="button_blue" id="onsumit" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>">
		</div>
	</div>
</form>
