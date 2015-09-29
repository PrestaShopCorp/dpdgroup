{**
* 2015 XXXXX.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to telco.csee@geopost.pl so we can send you a copy immediately.
*
*  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
*  @copyright 2015 DPD Polska sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska sp. z o.o.
*}
<script>
	$(document).ready(function(){
		$('table#table-postcode-list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonpostcode');
		})
	});
</script>
<form id="configuration_csv_form" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset id="postcode_fieldset">
		<legend>
			<img src="{$smarty.const._DPDGROUP_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings |' mod='dpdgroup'}" />
			{l s='Postcodes management' mod='dpdgroup'}
		</legend>

		<label>
			{l s='Upload postcodes CSV:' mod='dpdgroup'}
		</label>
		<div class="margin-form">
			<input type="file" name="{DpdGroupPostcode::CSV_POSTCODE_FILE|escape:'htmlall':'UTF-8'}" value="" />
			<input type="submit" class="button" name="{DpdGroupPostcodeController::SETTINGS_SAVE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Upload' mod='dpdgroup'}" />
		</div>

		<div class="clear"></div>

		<label>
			{l s='Download postcodes CSV:' mod='dpdgroup'}
		</label>
		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGroupPostcodeController::SETTINGS_DOWNLOAD_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Download' mod='dpdgroup'}" />
		</div>

		<div class="separation"></div>

		<div class="toggle_postcode_info_link_container">
			<a id="toggle_postcode_info_link">{l s='Instructions how to import postcodes →' mod='dpdgroup'}</a>
		</div>

		<div id="postcode_info_container">
			{include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdgroup/views/templates/admin/postcode_info.tpl'}
		</div>

		<div class="postcodes_table_container">
			<table class="table_grid" style="width: 100%;">
				<tbody>
				<tr>
					<td style="vertical-align: bottom;">
						<span style="float: left;">
							{l s='Page' mod='dpdgroup'}
							<b>{$page|intval}</b>
							/ {$total_pages|intval} | {l s='Display' mod='dpdgroup'}
							<select onchange="submit()" name="pagination">
								<option value="20"{if $selected_pagination == 20} selected="selected"{/if}>20</option>
								<option value="50"{if $selected_pagination == 50} selected="selected"{/if}>50</option>
								<option value="100"{if $selected_pagination == 100} selected="selected"{/if}>100</option>
								<option value="300"{if $selected_pagination == 300} selected="selected"{/if}>300</option>
								<option value="1000"{if $selected_pagination == 1000} selected="selected"{/if}>1000</option>
							</select>
							/ {$list_total|intval} {l s='result(s)' mod='dpdgroup'}
						</span>
						<span style="float: right;">
							<input id="submitFilterButtonpostcode" class="button" type="submit" value="{l s='Filter' mod='dpdgroup'}" name="submitFilterButtonpostcode">
							<input class="button" type="submit" value="{l s='Reset' mod='dpdgroup'}" name="submitResetpostcode">
						</span>
						<span class="clear"></span>
					</td>
				</tr>
				<tr>
					<table cellspacing="0" cellpadding="0" id="table-postcode-list" class="table table_grid" width="100%">
						<colgroup>
							<col width="10px">
							<col width="40px">
							<col width="70px">
							<col>
							<col>
							<col>
							<col width="40px">
						</colgroup>
						<thead>
						<tr style="height: 40px" class="nodrag nodrop">
							<th class="center">
								<input type="checkbox" onclick="checkDelBoxes(this.form, 'postcodeBox[]', this.checked)" class="noborder" name="checkme" />
							</th>
							<th class="center">
								<span class="title_box">
									{l s='ID' mod='dpdgroup'}
								</span>
								<br>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=id_postcode&postcodeOrderWay=desc">
									<img border="0" src="../img/admin/down{if $order_by == 'id_postcode' && $order_way == 'desc'}_d{/if}.gif">
								</a>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=id_postcode&postcodeOrderWay=asc">
									<img border="0" src="../img/admin/up{if $order_by == 'id_postcode' && $order_way == 'asc'}_d{/if}.gif">
								</a>
							</th>
							<th class="center">
								<span class="title_box">
									{l s='Postcode' mod='dpdgroup'}
								</span>
								<br>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=postcode&postcodeOrderWay=desc">
									<img border="0" src="../img/admin/down{if $order_by == 'postcode' && $order_way == 'desc'}_d{/if}.gif">
								</a>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=postcode&postcodeOrderWay=asc">
									<img border="0" src="../img/admin/up{if $order_by == 'postcode' && $order_way == 'asc'}_d{/if}.gif">
								</a>
							</th>
							<th class="center">
								<span class="title_box">
									{l s='Region' mod='dpdgroup'}
								</span>
								<br>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=region&postcodeOrderWay=desc">
									<img border="0" src="../img/admin/down{if $order_by == 'region' && $order_way == 'desc'}_d{/if}.gif">
								</a>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=region&postcodeOrderWay=asc">
									<img border="0" src="../img/admin/up{if $order_by == 'region' && $order_way == 'asc'}_d{/if}.gif">
								</a>
							</th>
							<th class="center">
								<span class="title_box">
									{l s='City' mod='dpdgroup'}
								</span>
								<br>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=city&postcodeOrderWay=desc">
									<img border="0" src="../img/admin/down{if $order_by == 'city' && $order_way == 'desc'}_d{/if}.gif">
								</a>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=city&postcodeOrderWay=asc">
									<img border="0" src="../img/admin/up{if $order_by == 'city' && $order_way == 'asc'}_d{/if}.gif">
								</a>
							</th>
							<th class="center">
								<span class="title_box">
									{l s='Address' mod='dpdgroup'}
								</span>
								<br>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=address&postcodeOrderWay=desc">
									<img border="0" src="../img/admin/down{if $order_by == 'address' && $order_way == 'desc'}_d{/if}.gif">
								</a>
								<a href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=address&postcodeOrderWay=asc">
									<img border="0" src="../img/admin/up{if $order_by == 'address' && $order_way == 'asc'}_d{/if}.gif">
								</a>
							</th>
							<th class="center">
								{l s='Actions' mod='dpdgroup'}
								<br />
								&nbsp;
							</th>
						</tr>
						<tr style="height: 35px;" class="nodrag nodrop filter row_hover">
							<td class="center">
								--
							</td>
							<td class="center">
								<input type="text" style="width:40px" value="{if Context::getContext()->cookie->postcodeFilter_id_postcode !== false}{Context::getContext()->cookie->postcodeFilter_id_postcode}{/if}" name="postcodeFilter_id_postcode" class="filter" style="width: 90%;" />
							</td>
							<td class="center">
								<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_postcode !== false}{Context::getContext()->cookie->postcodeFilter_postcode}{/if}" name="postcodeFilter_postcode" class="filter" style="width: 90%;" />
							</td>
							<td class="center">
								<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_region !== false}{Context::getContext()->cookie->postcodeFilter_region}{/if}" name="postcodeFilter_region" class="filter" style="width: 90%;" />
							</td>
							<td class="center">
								<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_city !== false}{Context::getContext()->cookie->postcodeFilter_city}{/if}" name="postcodeFilter_city" class="filter" style="width: 90%;" />
							</td>
							<td class="center">
								<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_address !== false}{Context::getContext()->cookie->postcodeFilter_address}{/if}" name="postcodeFilter_address" class="filter" style="width: 90%;" />
							</td>

							<td class="center">
								--
							</td>
						</tr>
						</thead>
						<tbody>
						{if isset($postcode_data) && !empty($postcode_data)}
							{section name=ii loop=$postcode_data}
								<tr class="row_hover{if $smarty.section.ii.index % 2 == 0} alt_row{/if}">
									<td class="center">
										<input class="noborder" type="checkbox" value="{$postcode_data[ii].id_postcode|escape:'htmlall':'UTF-8'}" name="postcodeBox[]">
									</td>
									<td class="center">
										{if $postcode_data[ii].id_postcode !== ''}
											{$postcode_data[ii].id_postcode|escape:'htmlall':'UTF-8'}
										{else}
											--
										{/if}
									</td>
									<td class="center">
										{if $postcode_data[ii].postcode !== ''}
											{$postcode_data[ii].postcode|escape:'htmlall':'UTF-8'}
										{else}
											--
										{/if}
									</td>
									<td class="center">
										{if $postcode_data[ii].region !== ''}
											{$postcode_data[ii].region|escape:'htmlall':'UTF-8'}
										{else}
											--
										{/if}
									</td>
									<td class="center">
										{if $postcode_data[ii].city !== ''}
											{$postcode_data[ii].city|escape:'htmlall':'UTF-8'}
										{else}
											--
										{/if}
									</td>
									<td class="center">
										{if $postcode_data[ii].address !== ''}
											{$postcode_data[ii].address|escape:'htmlall':'UTF-8'}
										{else}
											--
										{/if}
									</td>
									<td class="center" style="white-space: nowrap;">
										<a class="delete" title="{l s='Delete' mod='dpdgroup'}" onclick="return confirm('{l s='Delete selected postcode?' mod='dpdgroup'}');" href="{$saveAction|escape:'htmlall':'UTF-8'}&delete_postcode&id_postcode={$postcode_data[ii].id_postcode|escape:'htmlall':'UTF-8'}">
											<img alt="{l s='Delete' mod='dpdgroup'}" src="../img/admin/delete.gif">
										</a>
									</td>
								</tr>
							{/section}
						{else}
							<tr>
								<td colspan="7" class="center">
									{l s='No postcodes' mod='dpdgroup'}
								</td>
							</tr>
						{/if}
						</tbody>
					</table>
					<p>
						<input class="button" type="submit" onclick="return confirm('Delete selected postcodes?');" value="{l s='Delete selected postcodes' mod='dpdgroup'}" name="submitBulkdeletepostcode">
						<span style="float: right;">
							<input onclick="if (!confirm('Restore default postcodes?')) return false;" class="button" type="submit" value="{l s='Restore default postcodes' mod='dpdgroup'}" name="{DpdGroupPostcodeController::SETTINGS_RESTORE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}">
							<input onclick="if (!confirm('Delete all postcodes?')) return false;"{if !isset($postcode_data) || isset($postcode_data) && empty($postcode_data)} disabled="disabled"{/if} class="button" type="submit" value="{l s='Delete all postcodes' mod='dpdgroup'}" name="{DpdGroupPostcodeController::SETTINGS_DELETE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}">
						</span>
					</p>
				</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
</form>