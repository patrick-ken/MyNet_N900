<? include "/htdocs/webinc/body/draw_elements.php"; ?>
		<tr align="center">
			<td align="center">
				<!-- the uid of MAC_FILTER -->
				<input type="hidden" id="uid_<?=$INDEX?>" value="">
				<input type="hidden" id="description_<?=$INDEX?>" value="">
				<input type="checkbox"id="en_<?=$INDEX?>" class="styled2" />
			</td>
			<td><input type=text id="mac_<?=$INDEX?>" maxlength=17>
			</td>
			<td >
				<input type="button" id="arrow_<?=$INDEX?>" value="<<" class="button_blackX05" onclick="PAGE.OnClickArrowKey(<?=$INDEX?>);" modified="ignore" />
			</td>
			
			<td>			
			<select id="client_list_<?=$INDEX?>" modified="ignore" class="styled3">
				<option value=""><?echo I18N("h", "Device Name");?></option>
				<?
				$lanp = XNODE_getpathbytarget("/runtime", "inf", "uid", "LAN-1", false);
				if ($lanp != "")
				{
					foreach ($lanp."/dhcps4/leases/entry")
					echo '<option value="'.query("macaddr").'">'.query("hostname").'</option>';
				}
				?>
			</select>
			</td>
			<?
			if ($FEATURE_NOSCH!="1")
			{
				echo '<td>\n';
				DRAW_select_sch("sch_".$INDEX, I18N("h", "Always"), "-1", "", "0", "narrow");
				echo '&nbsp;'.
					 '<input type="button"  id="schedule_'.$INDEX.'_btn" value="'.I18N("h", "New Schedule").'"'.
					 		' onclick="javascript:self.location.href=\'tools_sch.php\'">\n';
				echo '</td>\n';
			}
			?>
		</tr>
