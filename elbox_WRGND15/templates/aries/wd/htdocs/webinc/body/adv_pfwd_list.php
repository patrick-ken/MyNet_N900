<? include "/htdocs/webinc/body/draw_elements.php"; ?>
<table id="" class="wd_table_pf">
	<tr>
		<td rowspan="2" align="center">
			<!-- the uid of PFWD -->
			<input type="hidden" id="uid_<?=$INDEX?>" value="">
			<span class="table_words"><?echo I18N("h", "Enabled");?></span>
			<input id="en_<?=$INDEX?>" type="checkbox" class="styled2" onClick="OnClickEnable('<?=$INDEX?>');" />
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "Name");?></span><br />
			<input id="dsc_<?=$INDEX?>" type="text" size="15" maxlength="15"/>
		</td>
		<td class="bottom">
			<br />
			<input type="button" value="<<" class="button_blackX05" onClick="OnClickAppArrow('<?=$INDEX?>');" />
			<span id="span_app_<?=$INDEX?>"></span>
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "External Port Range");?></span><br />
			<table id="" class="wd_inner_table">
				<tr>
					<td><FONT SIZE=2><?echo I18N("h", "From:");?></FONT></td>
					<td><input id="pub_start_<?=$INDEX?>" type="text" size="5" maxlength="5"></td>
				</tr>
				<tr>
					<td><FONT SIZE=2><?echo I18N("h", "To:");?></FONT></td>
					<td><input id="pub_end_<?=$INDEX?>" type="text" size="5" maxlength="5"></td>
				</tr>
			</table>
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "Protocol");?></span><br />
			<select id="pro_<?=$INDEX?>" class="styled1">
				<option value="TCP+UDP"><?echo I18N("h", "All");?></option>				    
				<option value="TCP">TCP</option>
				<option value="UDP">UDP</option>
			</select>
		</td>
		<?
		if ($FEATURE_NOSCH != "1")
		{
			echo '<td width="120px">\n';
			echo '	<span class="table_words">'.I18N("h", "Schedule").'</span><br />\n';
			DRAW_select_sch("sch_".$INDEX, I18N("h", "Always"), "-1", "", "0", "styled1");
			echo '</td>\n';
		}
		?>
	</tr>
	<tr>
		<td>
			<span class="table_words"><?echo I18N("h", "IP Address");?></span><br />
			<input id="ip_<?=$INDEX?>" type="text" size="15" maxlength="15" />
		</td>
		<td class="bottom">
			<br />
			<input type="button" value="<<" class="button_blackX05" onClick="OnClickPCArrow('<?=$INDEX?>');" />
			<? DRAW_select_dhcpclist("LAN-1","pc_".$INDEX, I18N("h", "Computer Name"), "",  "", "1", "styled3"); ?>
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "Internal Port Range");?></span>
			<table id=""  class="wd_inner_table">
				<tr>
					<td><FONT SIZE=2><?echo I18N("h", "From:");?></FONT></td>
					<td><input id="pri_start_<?=$INDEX?>" type="text" size="5" maxlength="5" ></td>
				</tr>
				<tr>
					<td><FONT SIZE=2><?echo I18N("h", "To:");?></FONT></td>
					<td><input id="pri_end_<?=$INDEX?>" type="text" size="5" maxlength="5" disabled ></td>
				</tr>
			</table>
		</td>
		<td>
			<span class="table_words"> </span><br />		 
		</td>
		<?
		if ($FEATURE_INBOUNDFILTER == "1")
		{
			echo '<td>\n';
			echo '	<span class="table_words">'.I18N("h", "Inbound Filter").'</span><br />\n';
			DRAW_select_inbfilter("inbfilter_".$INDEX, I18N("h", "Allow All"), "-1", I18N("h", "Deny All"), "denyall", "", 0, "styled1");
			echo '</td>\n';
		}
		else if ($FEATURE_NOSCH != "1")
		{
			echo '<td>&nbsp;</td>\n';
		}			
		?>
	</tr>
</table>
