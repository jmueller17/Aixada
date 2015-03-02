	<table class="tblForms">
						<tr>
							<td><label for="login"><?php echo $Text['login'];?></label></td>
							<td>
								<p class="textAlignLeft ui-corner-all">{login}</p>
							</td>
							<td><label for="member_id"><?php echo $Text['member_id']; ?></label></td>
							<td><p class="textAlignLeft ui-corner-all">{id}</p></td>
							
						</tr>
						<tr>
							<td colspan="2"></td>
							<td><label for="custom_member_ref"><?php echo $Text['custom_member_ref']; ?></label></td>
							<td><input type="text" name="custom_member_ref" value="{custom_member_ref}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="name"><?php echo $Text['name_person'];?></label></td>
							<td><input type="text" name="name"  value="{name}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="nif"><?php echo $Text['nif'];?></label></td>
							<td><input type="text" name="nif" value="{nif}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="address"><?php echo $Text['address'];?></label></td>
							<td colspan="5"><input type="text" name="address" value="{address}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="city"><?php echo $Text['city'];?></label></td>
							<td><input type="text" name="city" value="{city}" class="ui-widget-content ui-corner-all" /></td>
							<td><label for="zip"><?php echo $Text['zip'];?></label></td>
							<td><input type="text" name="zip"  value="{zip}" class=" ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="phone1"><?php echo $Text['phone1'];?></label></td>
							<td><input type="text" name="phone1" value="{phone1}" class="ui-widget-content ui-corner-all" /></td>
						
							<td><label for="phone2"><?php echo $Text['phone2'];?></label></td>
							<td><input type="text" name="phone2" value="{phone2}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="email"><?php echo $Text['email'];?></label></td>
							<td colspan="5"><input type="text" name="email" value="{email}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="web"><?php echo $Text['web'];?></label></td>
							<td colspan="5"><input type="text" name="web" value="{web}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="notes"><?php echo $Text['notes'];?></label></td>
							<td colspan="5"><input type="text" name="notes" value="{notes}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="member_active"><?php echo $Text['active'];?></label></td>
							<td><input type="checkbox" name="member_active" value="{active}" class="floatLeft" /></td>
							<td><label for="participant"><?php echo $Text['participant'];?></label></td>
							<td><input type="checkbox" name="participant" value="{participant}" class="floatLeft" /></td>
						</tr>
						<tr>
							<td>&nbsp;
							</td>
						</tr>
						<tr>
							<td><label for="last_seen"><?php echo $Text['last_logon']; ?>:</label></td>
							<td colspan="2"><p class="textAlignLeft ui-corner-all">{last_successful_login}</p></td>
						</tr>
						<tr>
							<td><label for="default_theme"><?php echo $Text['theme']; ?>:</label></td>
							<td colspan="2">
								<p class="textAlignLeft ui-corner-all hidden">{gui_theme}</p>
								<div class="memberThemeSelect"></div>
							</td>
						</tr>
						<tr>
							<td><label for="languageSelect"><?php echo $Text['lang']; ?>:</label></td>
							<td>
								<p class="textAlignLeft ui-corner-all hidden">{language}</p>
								<div class="memberLanguageSelect"></div>
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>
								<button class="btn_reset_pwd hidden" userId="{user_id}"><?php echo $Text['btn_reset_pwd']; ?></button>
							
							</td>
							<td><p class="floatRight">
									<button class="btn_save_edit_member" memberid="{id}"><?php echo $Text['btn_save'];?></button>
								</p>
							</td>
						</tr>
					</table>
					</form>
					<p>&nbsp;</p>
					<table class="tblForms">
						<tr>
							<td><?php echo $Text['active_roles'];?></td>
							<td><p class="textAlignLeft">{roles}</p></td>
						</tr>
						<tr>
							<td><?php echo $Text['providers_cared_for'];?>:</td>
							<td><p class="textAlignLeft">{providers}</p></td>
							
						</tr>
						<tr>
							<td><?php echo $Text['products_cared_for'];?>:</td>
							<td><p class="textAlignLeft">{products}</p></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
					</table>
		

