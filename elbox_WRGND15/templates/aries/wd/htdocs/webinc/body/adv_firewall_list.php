<?
if($lang=="pl")	$multi_language="styled3";
else	$multi_language="styled1a";
?>
<table id="" class="wd_table">
	<tr>
		<td width="50px" rowspan="2" align="center">
			<span class="table_words"><?echo I18N("h", "Enabled");?></span><br>
			<input id="en_<?=$INDEX?>" type="checkbox" class="styled2" />
		</td>
		<td width="73px">
			<span class="table_words"><?echo I18N("h", "Name");?></span><br>
			<input id="dsc_<?=$INDEX?>" type="text" size="8" maxlength="31" />
		</td>
		<td width="126px">
			<span class="table_words"><?echo I18N("h", "Source");?></span><br>
			<select id="src_inf_<?=$INDEX?>" class="styled1a">
				<option value=""><?echo I18N("h", "Source");?></option>
				<option value="LAN-1">LAN</option>
				<option value="WAN-1">WAN</option>
			</select>
		</td>
		<td width="150px">
			<span class="table_words"><?echo I18N("h", "Source IP Range");?></span><br>
			<input id="src_startip_<?=$INDEX?>" type="text" maxlength="15" size="12" /><br>
			<input id="src_endip_<?=$INDEX?>" type="text" maxlength="15" size="12" />
		</td>
		<td width="90px">
			
			<span class="table_words"><?echo I18N("h", "Protocol");?></span><br>
			<select id="pro_<?=$INDEX?>" onchange="PAGE.OnChangeProt(<?=$INDEX?>)" class="styled1">
				<option value="TCP+UDP">TCP+UDP</option>
				<option value="TCP">TCP</option>
				<option value="UDP">UDP</option>
				<option value="ICMP">ICMP</option>
			</select>
		</td>
		<?
		if ($FEATURE_NOSCH!="1")
		{
			echo '<td width="113px" rowspan="2">\n';
			echo '<span class="table_words">'.I18N("h", "Schedule").'</span><br>\n';
			DRAW_select_sch("sch_".$INDEX, I18N("h", "Always"), "-1", "", 0, "styled1");
			echo '<br>\n';
			echo '<input type="button" class="button_black" id=sch_'.$INDEX.'_btn value="'.I18N("h", "New Schedule").'"'.
				 ' onclick="javascript:self.location.href=\'tools_sch.php\'">\n';
			echo '</td>\n';
		}
		?>
		<td width="30px" rowspan="2" align="center">
			<span class="table_words"><?echo I18N("h", "Delete");?></span><br>
			<a href="javascript:PAGE.OnDelete('<?=$INDEX?>');"><img src="pic/img_delete.gif" title="<?echo I18N("h", "Delete");?>"></a>
		</td>
	</tr>
	<tr>
		<td>
			<span class="table_words"><?echo I18N("h", "Action");?></span><br>
			<select id="action_<?=$INDEX?>" class="styled1">
				<option value="ACCEPT"><?echo I18N("h", "Allow");?></option>
				<option value="DROP"><?echo I18N("h", "Deny");?></option>
			</select>
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "Destination");?></span><br>
			<select id="dst_inf_<?=$INDEX?>" class="<?=$multi_language?>">
				<option value=""><?echo I18N("h", "Destination");?></option>
				<option value="LAN-1">LAN</option>
				<option value="WAN-1">WAN</option>
			</select>
		</td>
		<td>
			<span class="table_words"><?echo I18N("h", "Destination IP Range");?></span><br>
			<input id="dst_startip_<?=$INDEX?>" type="text" maxlength="15" size="12" /><br>
			<input id="dst_endip_<?=$INDEX?>" type="text" maxlength="15" size="12" />
		</td>
		<td>
			<?echo I18N("h", "Port Range");?><br>
			<input id="dst_startport_<?=$INDEX?>" type="text"  maxlength="5" size="6"/><br>
			<input id="dst_endport_<?=$INDEX?>" type="text" maxlength="5" size="6" />
		</td>
	</tr>
</table>
