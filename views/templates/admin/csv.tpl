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
<form id="configuration_csv_form" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset id="sender_payer">
		<legend>
			<img src="{$smarty.const._DPDGROUP_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings |' mod='dpdgroup'}" />
			{l s='Price rules import' mod='dpdgroup'}
		</legend>

		<label>
			{l s='Upload CSV:' mod='dpdgroup'}
		</label>
		<div class="margin-form">
			<input type="file" name="{DpdGroupCSV::CSV_FILE|escape:'htmlall':'UTF-8'}" value="" />
			<input type="submit" class="button" name="{DpdGroupCSVController::SETTINGS_SAVE_CSV_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Upload' mod='dpdgroup'}" />
		</div>
		<div class="clear"></div>

		<label>
			{l s='Download CSV:' mod='dpdgroup'}
		</label>
		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGroupCSVController::SETTINGS_DOWNLOAD_CSV_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Download' mod='dpdgroup'}" />
		</div>

		<div class="separation"></div>

		<h3>
			{l s='Preview imported prices:' mod='dpdgroup'}
		</h3>

		<div class="csv_information_block">
			<p class="preference_description">
				{l s='Available shiping methods and their IDs:' mod='dpdgroup'}
			</p>
			<p class="preference_description">
				{l s='* DPD Classic: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_CLASSIC_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD 10:00: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_10_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD 12:00: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_12_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD Same Day: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_SAME_DAY_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD B2C: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_B2C_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD International: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_INTERNATIONAL_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<p class="preference_description">
				{l s='* DPD Bulgaria: ID -' mod='dpdgroup'} {$smarty.const._DPDGROUP_BULGARIA_ID_|escape:'htmlall':'UTF-8'}
			</p>
			<br />

			<p class="preference_description">
				{l s='Decimal separator symbol is: "."' mod='dpdgroup'}
			</p>

			<p class="preference_description">
				{l s='Maximum decimal numbers: 6' mod='dpdgroup'}
			</p>

			<p class="preference_description">
				{l s='Please also check module settings page in order to be sure that correct price calculation method is selected.' mod='dpdgroup'}
			</p>

			<div class="toggle_csv_info_link_container">
				<a id="toggle_csv_info_link">{l s='Instructions how to import CSV price rules →' mod='dpdgroup'}</a>
			</div>
			<div id="toggle_csv_info">
				{include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdgroup/views/templates/admin/csv_info.tpl'}
			</div>

			<p class="clear list info">
				{l s='The first matching rule will be used for price calculation. Make sure your CSV rules arre in correct order!' mod='dpdgroup'}
			</p>

			<table name="list_table" class="table_grid">
				<tbody>
					<tr>
						<td style="vertical-align: bottom;">
							<span style="float: left;">
								{if $page > 1}
									<a href="{$saveAction|escape:'htmlall':'UTF-8'}&current_page=1&pagination={$selected_pagination|escape:'htmlall':'UTF-8'}">
										<img class="pagination_image" src="../img/admin/list-prev2.gif" alt="{l s='First page' mod='dpdgroup'}" />
									</a>
									<a href="{$saveAction|escape:'htmlall':'UTF-8'}&current_page={$page|escape:'htmlall':'UTF-8' - 1}&pagination={$selected_pagination|escape:'htmlall':'UTF-8'}">
										<img class="pagination_image" src="../img/admin/list-prev.gif" alt="{l s='Previous page' mod='dpdgroup'}" />
									</a>
								{/if}
								{l s='Page' mod='dpdgroup'} <b>{$page|escape:'htmlall':'UTF-8'}</b> / {$total_pages|escape:'htmlall':'UTF-8'}
								{if $page < $total_pages}
									<a href="{$saveAction|escape:'htmlall':'UTF-8'}&current_page={$page|escape:'htmlall':'UTF-8' + 1}&pagination={$selected_pagination|escape:'htmlall':'UTF-8'}">
										<img class="pagination_image" src="../img/admin/list-next.gif" alt="{l s='Next page' mod='dpdgroup'}" />
									</a>
									<a href="{$saveAction|escape:'htmlall':'UTF-8'}&current_page={$total_pages|escape:'htmlall':'UTF-8'}&pagination={$selected_pagination|escape:'htmlall':'UTF-8'}">
										<img class="pagination_image" src="../img/admin/list-next2.gif" alt="{l s='Last page' mod='dpdgroup'}" />
									</a>
								{/if}
								| {l s='Display' mod='dpdgroup'}
								<select name="pagination" onchange="submit()">
									{foreach $pagination AS $value}
										<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
									{/foreach}
								</select>
								/ {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdgroup'}
							</span>
							<span class="clear"></span>
						</td>
					</tr>
					<tr>
						<td style="border:none;">
							<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table document">
								<colgroup>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
									<col>
								</colgroup>
								<thead>
									<tr style="height: 40px" class="nodrag nodrop">
										<th class="center">
											<span class="title_box">{l s='Country' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Region / State' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Zip / Postal Code' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Weight / Price (From)' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Weight / Price (To)' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Shipping Price' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Shipping Price Percentage' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Currency' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='Method ID' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='COD Surcharge' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='COD Surcharge Percentage' mod='dpdgroup'}</span>
										</th>
										<th class="center">
											<span class="title_box">{l s='COD Min. Surcharge' mod='dpdgroup'}</span>
										</th>
									</tr>
								</thead>

								<tbody>
									{if isset($csv_data) && !empty($csv_data)}
										{section name=ii loop=$csv_data}
											<tr>
												<td class="center">
													{$csv_data[ii].country|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].region|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].zip|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].weight_from|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].weight_to|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].shipping_price|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].shipping_price_percentage|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].currency|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].method_id|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].cod_surcharge|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].cod_surcharge_percentage|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													{$csv_data[ii].cod_min_surcharge|escape:'htmlall':'UTF-8'}
												</td>
											</tr>
										{/section}
									{else}
										<tr>
											<td colspan="12" class="center">
												{l s='No prices' mod='dpdgroup'}
											</td>
										</tr>
									{/if}
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" {if !isset($csv_data) || isset($csv_data) && empty($csv_data)}disabled="disabled"{/if} class="button" name="{DpdGroupCSVController::SETTINGS_DELETE_CSV_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Delete all prices' mod='dpdgroup'}" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
</form>