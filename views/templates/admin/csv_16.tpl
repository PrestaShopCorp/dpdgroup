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
<style>
	@media (max-width: 992px) {
		.table-responsive-row td:nth-of-type(1):before
		{
			content: "{l s='Country' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(2):before
		{
			content: "{l s='Region / State' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(3):before
		{
			content: "{l s='Zip / Postal Code ' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(4):before
		{
			content: "{l s='Weight / Price (From)' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(5):before
		{
			content: "{l s='Weight / Price (To)' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(6):before
		{
			content: "{l s='Shipping Price' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(7):before
		{
			content: "{l s='Shipping Price Percentage' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(8):before
		{
			content: "{l s='Currency' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(9):before
		{
			content: "{l s='Method ID' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(10):before
		{
			content: "{l s='COD Surcharge' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(11):before
		{
			content: "{l s='COD Surcharge Percentage' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(12):before
		{
			content: "{l s='COD Min. Surcharge' mod='dpdgeopost'}";
		}
	}
</style>

<form id="configuration_csv_form" class="form-horizontal" enctype="multipart/form-data" method="post" action="{$saveAction|escape:'htmlall':'UTF-8'}">
	<input id="current_page" type="hidden" value="0" name="current_page">
	<div id="configuration_csv_options" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Price rules import' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Upload CSV:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="{DpdGeopostCSV::CSV_FILE|escape:'htmlall':'UTF-8'}" id="csv_file_select">
							<div class="dummyfile input-group">
								<span class="input-group-addon">
									<i class="icon-file"></i>
								</span>
								<input type="text" readonly="" name="filename" id="csv_file_select_name" />
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="csv_file_select_button">
										<i class="icon-folder-open"></i>{l s='Select CSV' mod='dpdgeopost'}
									</button>
								</span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="dummyfile input-group">
								<button id="generate-friendly-url" name="{DpdGeopostCSVController::SETTINGS_SAVE_CSV_ACTION|escape:'htmlall':'UTF-8'}" class="btn btn-default" type="submit">
									<i class="icon-plus-sign"></i>
									{l s='Upload' mod='dpdgeopost'}
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Download CSV:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-sm-6">
							<button id="generate-friendly-url" name="{DpdGeopostCSVController::SETTINGS_DOWNLOAD_CSV_ACTION|escape:'htmlall':'UTF-8'}" class="btn btn-default" type="submit">
								<i class="icon-arrow-down"></i>
								{l s='Download' mod='dpdgeopost'}
							</button>
						</div>
					</div>
				</div>
			</div>

			<hr />

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Preview imported prices:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<p class="preference_description">
						{l s='Available shiping methods and their IDs:' mod='dpdgeopost'}
					</p>
					<p class="preference_description">
						{l s='* DPD Classic: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_CLASSIC_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD 10:00: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_10_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD 12:00: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_12_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD Same Day: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_SAME_DAY_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD B2C: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_B2C_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD International: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_INTERNATIONAL_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<p class="preference_description">
						{l s='* DPD Bulgaria: ID -' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_BULGARIA_ID_|escape:'htmlall':'UTF-8'}
					</p>
					<br />

					<p class="preference_description">
						{l s='Decimal separator symbol is: "."' mod='dpdgeopost'}
					</p>

					<p class="preference_description">
						{l s='Maximum decimal numbers: 6' mod='dpdgeopost'}
					</p>

					<p class="preference_description">
						{l s='Please also check module settings page in order to be sure that correct price calculation method is selected.' mod='dpdgeopost'}
					</p>

					<div class="toggle_csv_info_link_container">
						<a id="toggle_csv_info_link">{l s='Instructions how to import CSV price rules →' mod='dpdgeopost'}</a>
					</div>
					<div id="toggle_csv_info">
						{include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdgeopost/views/templates/admin/csv_info.tpl'}
					</div>

					<div class="alert alert-info">
						{l s='The first matching rule will be used for price calculation. Make sure your CSV rules arre in correct order!' mod='dpdgeopost'}
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="panel col-lg-12">
					<div class="panel-heading">
						{l s='Price rules' mod='dpdgeopost'} <span class="badge">{$list_total|intval}</span>
					</div>
					<div class="table-responsive-row clearfix">
						<table id="table-combinations-list" class="table configuration">
							<thead>
								<tr class="nodrag nodrop">
									<th class="center">
										<span class="title_box">
											{l s='Country' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Region / State' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Zip / Postal Code' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Weight / Price (From)' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Weight / Price (To)' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Shipping Price' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Shipping Price Percentage' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Currency' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Method ID' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='COD Surcharge' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='COD Surcharge Percentage' mod='dpdgeopost'}
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='COD Min. Surcharge' mod='dpdgeopost'}
										</span>
									</th>
									<th>

									</th>
								</tr>
							</thead>
							<tbody>
								{if isset($csv_data) && !empty($csv_data)}
									{section name=ii loop=$csv_data}
										<tr class="odd">
											<td class="center">
												{if $csv_data[ii].country !== ''}
													{$csv_data[ii].country|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].region !== ''}
													{$csv_data[ii].region|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].zip !== ''}
													{$csv_data[ii].zip|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].weight_from !== ''}
													{$csv_data[ii].weight_from|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].weight_to !== ''}
													{$csv_data[ii].weight_to|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].shipping_price !== ''}
													{$csv_data[ii].shipping_price|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].shipping_price_percentage !== ''}
													{$csv_data[ii].shipping_price_percentage|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].currency !== ''}
													{$csv_data[ii].currency|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].method_id !== ''}
													{$csv_data[ii].method_id|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].cod_surcharge !== ''}
													{$csv_data[ii].cod_surcharge|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].cod_surcharge_percentage !== ''}
													{$csv_data[ii].cod_surcharge_percentage|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td class="center">
												{if $csv_data[ii].cod_min_surcharge !== ''}
													{$csv_data[ii].cod_min_surcharge|escape:'htmlall':'UTF-8'}
												{else}
													&nbsp;
												{/if}
											</td>
											<td>

											</td>
										</tr>
									{/section}
								{else}
									<tr>
										<td colspan="13" class="center">
											{l s='No prices' mod='dpdgeopost'}
										</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
					{if $list_total > 20}
						<div class="row">
							<div class="col-lg-12">
								<div class="pagination">
									{l s='Display' mod='dpdgeopost'}
									<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
										{$selected_pagination|intval}
										<i class="icon-caret-down"></i>
									</button>
									<ul class="dropdown-menu">
										<li>
											<a class="pagination-items-page" data-list-id="csv" data-items="20" href="javascript:void(0);">20</a>
										</li>
										<li>
											<a class="pagination-items-page" data-list-id="csv" data-items="50" href="javascript:void(0);">50</a>
										</li>
										<li>
											<a class="pagination-items-page" data-list-id="csv" data-items="100" href="javascript:void(0);">100</a>
										</li>
										<li>
											<a class="pagination-items-page" data-list-id="csv" data-items="300" href="javascript:void(0);">300</a>
										</li>
										<li>
											<a class="pagination-items-page" data-list-id="csv" data-items="1000" href="javascript:void(0);">1000</a>
										</li>
									</ul>
									/ {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdgeopost'}
									<input id="csv-pagination-items-page" type="hidden" value="{$selected_pagination|intval}" name="pagination" />
								</div>
								<ul class="pagination pull-right">
									<li {if $page <= 1}class="disabled"{/if}>
										<a href="javascript:void(0);" class="pagination-link" data-page="1">
											<i class="icon-double-angle-left"></i>
										</a>
									</li>
									<li {if $page <= 1}class="disabled"{/if}>
										<a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval - 1}">
											<i class="icon-angle-left"></i>
										</a>
									</li>
									{assign p 0}
									{while $p++ < $total_pages}
										{if $p < $page-2}
											<li class="disabled">
												<a href="javascript:void(0);">&hellip;</a>
											</li>
											{assign p $page-3}
										{else if $p > $page+2}
											<li class="disabled">
												<a href="javascript:void(0);">&hellip;</a>
											</li>
											{assign p $total_pages}
										{else}
											<li {if $p == $page}class="active"{/if}>
												<a href="javascript:void(0);" class="pagination-link" data-page="{$p|intval}">{$p|intval}</a>
											</li>
										{/if}
									{/while}
									<li {if $page >= $total_pages}class="disabled"{/if}>
										<a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval + 1}">
											<i class="icon-angle-right"></i>
										</a>
									</li>
									<li {if $page >= $total_pages}class="disabled"{/if}>
										<a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages|intval}">
											<i class="icon-double-angle-right"></i>
										</a>
									</li>
								</ul>
								<script type="text/javascript">
									$('li:not(.disabled):not(.active) .pagination-link').on('click',function(e){
										e.preventDefault();
										$('#current_page').val($(this).data('page')).closest('form').submit();
									});
								</script>
							</div>
						</div>
					{/if}
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button {if !isset($csv_data) || isset($csv_data) && empty($csv_data)}disabled="disabled"{/if} class="btn btn-default pull-right" name="{DpdGeopostCSVController::SETTINGS_DELETE_CSV_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-delete"></i>
				{l s='Delete all prices' mod='dpdgeopost'}
			</button>
		</div>
	</div>
</form>

<script>
	$(document).ready(function(){
		$('#csv_file_select_button').click(function(e){
			$('#csv_file_select').trigger('click');
		});
		$('#csv_file_select').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#csv_file_select_name').val(file[file.length-1]);
		});
	});

	$('.pagination-items-page').on('click',function(e){
		e.preventDefault();
		$('#'+$(this).data('list-id')+'-pagination-items-page').val($(this).data('items')).closest('form').submit();
	});
</script>