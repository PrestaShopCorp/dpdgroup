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
			content: "{l s='Country' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(2):before
		{
			content: "{l s='Region / State' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(3):before
		{
			content: "{l s='Zip / Postal Code ' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(4):before
		{
			content: "{l s='Weight / Price (From)' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(5):before
		{
			content: "{l s='Weight / Price (To)' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(6):before
		{
			content: "{l s='Shipping Price' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(7):before
		{
			content: "{l s='Shipping Price Percentage' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(8):before
		{
			content: "{l s='Currency' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(9):before
		{
			content: "{l s='Method ID' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(10):before
		{
			content: "{l s='COD Surcharge' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(11):before
		{
			content: "{l s='COD Surcharge Percentage' mod='dpdgroup'}";
		}
		.table-responsive-row td:nth-of-type(12):before
		{
			content: "{l s='COD Min. Surcharge' mod='dpdgroup'}";
		}
	}
</style>

<script>
	$(document).ready(function(){
		$('table#table-postcode-list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonpostcode');
		})
	});
</script>

<form id="configuration_csv_form" class="form-horizontal" enctype="multipart/form-data" method="post" action="{$saveAction|escape:'htmlall':'UTF-8'}">
	<input id="current_page" type="hidden" value="0" name="current_page">
	<div id="configuration_csv_options" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Postcodes management' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Upload postcodes CSV:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="{DpdGroupPostcode::CSV_POSTCODE_FILE|escape:'htmlall':'UTF-8'}" id="csv_file_select">
							<div class="dummyfile input-group">
								<span class="input-group-addon">
									<i class="icon-file"></i>
								</span>
								<input type="text" readonly="" name="filename" id="csv_file_select_name" />
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="csv_file_select_button">
										<i class="icon-folder-open"></i>{l s='Select postcodes CSV' mod='dpdgroup'}
									</button>
								</span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="dummyfile input-group">
								<button id="generate-friendly-url" name="{DpdGroupPostcodeController::SETTINGS_SAVE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" class="btn btn-default" type="submit">
									<i class="icon-plus-sign"></i>
									{l s='Upload' mod='dpdgroup'}
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Download postcodes CSV:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-sm-6">
							<button id="generate-friendly-url" name="{DpdGroupPostcodeController::SETTINGS_DOWNLOAD_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" class="btn btn-default" type="submit">
								<i class="icon-arrow-down"></i>
								{l s='Download' mod='dpdgroup'}
							</button>
							<p class="help-block">
								{l s='This proces can require a lot of memory. If it will be unsuccessful, please increase memory limit' mod='dpdgroup'}
							</p>
						</div>
					</div>
				</div>
			</div>

			<hr />

			<div class="toggle_postcode_info_link_container">
				<a id="toggle_postcode_info_link">{l s='Instructions how to import postcodes →' mod='dpdgroup'}</a>
			</div>

			<div id="postcode_info_container">
				{include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdgroup/views/templates/admin/postcode_info.tpl'}
			</div>

			<hr />

			<div class="form-group">
				<div class="panel col-lg-12">
					<div class="panel-heading">
						{l s='Postcodes' mod='dpdgroup'} <span class="badge">{$list_total|intval}</span>
					</div>
					<div class="table-responsive-row clearfix">
						<table id="table-postcode-list" class="table postcode">
							<thead>
								<tr class="nodrag nodrop">
									<th class="center fixed-width-xs">

									</th>
									<th class="center fixed-width-xs">
										<span class="title_box">
											{l s='ID' mod='dpdgroup'}
											<a{if $order_by == 'id_postcode' && $order_way == 'desc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=id_postcode&postcodeOrderWay=desc">
												<i class="icon-caret-down"></i>
											</a>
											<a{if $order_by == 'id_postcode' && $order_way == 'asc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=id_postcode&postcodeOrderWay=asc">
												<i class="icon-caret-up"></i>
											</a>
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Postcode' mod='dpdgroup'}
											<a{if $order_by == 'postcode' && $order_way == 'desc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=postcode&postcodeOrderWay=desc">
												<i class="icon-caret-down"></i>
											</a>
											<a{if $order_by == 'postcode' && $order_way == 'asc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=postcode&postcodeOrderWay=asc">
												<i class="icon-caret-up"></i>
											</a>
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Region' mod='dpdgroup'}
											<a{if $order_by == 'region' && $order_way == 'desc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=region&postcodeOrderWay=desc">
												<i class="icon-caret-down"></i>
											</a>
											<a{if $order_by == 'region' && $order_way == 'asc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=region&postcodeOrderWay=asc">
												<i class="icon-caret-up"></i>
											</a>
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='City' mod='dpdgroup'}
											<a{if $order_by == 'city' && $order_way == 'desc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=city&postcodeOrderWay=desc">
												<i class="icon-caret-down"></i>
											</a>
											<a{if $order_by == 'city' && $order_way == 'asc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=city&postcodeOrderWay=asc">
												<i class="icon-caret-up"></i>
											</a>
										</span>
									</th>
									<th class="center">
										<span class="title_box">
											{l s='Address' mod='dpdgroup'}
											<a{if $order_by == 'address' && $order_way == 'desc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=address&postcodeOrderWay=desc">
												<i class="icon-caret-down"></i>
											</a>
											<a{if $order_by == 'address' && $order_way == 'asc'} class="active"{/if} href="{$saveAction|escape:'htmlall':'UTF-8'}&postcodeOrderBy=address&postcodeOrderWay=asc">
												<i class="icon-caret-up"></i>
											</a>
										</span>
									</th>
									<th class="actions">

									</th>
								</tr>
								<tr class="nodrag nodrop filter row_hover">
									<th class="text-center">
										--
									</th>

									<th class="center">
										<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_id_postcode !== false}{Context::getContext()->cookie->postcodeFilter_id_postcode}{/if}" name="postcodeFilter_id_postcode" class="filter" />
									</th>
									<th class="center">
										<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_postcode !== false}{Context::getContext()->cookie->postcodeFilter_postcode}{/if}" name="postcodeFilter_postcode" class="filter" />
									</th>
									<th class="center">
										<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_region !== false}{Context::getContext()->cookie->postcodeFilter_region}{/if}" name="postcodeFilter_region" class="filter" />
									</th>
									<th>
										<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_city !== false}{Context::getContext()->cookie->postcodeFilter_city}{/if}" name="postcodeFilter_city" class="filter" />
									</th>
									<th>
										<input type="text" value="{if Context::getContext()->cookie->postcodeFilter_address !== false}{Context::getContext()->cookie->postcodeFilter_address}{/if}" name="postcodeFilter_address" class="filter" />
									</th>

									<th class="actions">
										<span class="pull-right">
											<button data-list-id="postcode" class="btn btn-default" name="submitFilterButtonpostcode" id="submitFilterButtonpostcode" type="submit">
												<i class="icon-search"></i>
												{l s='Search' mod='dpdgroup'}
											</button>
											{if Context::getContext()->cookie->postcodeFilter_id_postcode !== false ||
												Context::getContext()->cookie->postcodeFilter_postcode !== false ||
												Context::getContext()->cookie->postcodeFilter_region !== false ||
												Context::getContext()->cookie->postcodeFilter_city !== false ||
												Context::getContext()->cookie->postcodeFilter_address !== false}
												<button class="btn btn-warning" name="submitResetpostcode" type="submit">
													<i class="icon-eraser"></i>
													{l s='Reset' mod='dpdgroup'}
												</button>
											{/if}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if isset($postcode_data) && !empty($postcode_data)}
									{section name=ii loop=$postcode_data}
										<tr class="odd">
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
											<td class="text-right">
												<div class="btn-group-action">
													<div class="btn-group pull-right">
														<a class="edit btn btn-default" title="{l s='Delete' mod='dpdgroup'}" href="{$saveAction|escape:'htmlall':'UTF-8'}&delete_postcode&id_postcode={$postcode_data[ii].id_postcode|escape:'htmlall':'UTF-8'}">
															<i class="icon-trash"></i>
															{l s='Delete' mod='dpdgroup'}
														</a>
													</div>
												</div>
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
					</div>
					<div class="col-lg-6">
						<div class="btn-group bulk-actions dropup">
							<button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
								{l s='Bulk actions'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li>
									<a onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'postcodeBox[]', true);return false;" href="#">
										<i class="icon-check-sign"></i>
										{l s='Select all' mod='dpdgroup'}
									</a>
								</li>
								<li>
									<a onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'postcodeBox[]', false);return false;" href="#">
										<i class="icon-check-empty"></i>
										{l s='Unselect all' mod='dpdgroup'}
									</a>
								</li>
								<li class="divider"></li>
								<li>
									<a onclick="if (confirm('Delete selected postcodes?'))sendBulkAction($(this).closest('form').get(0), 'submitBulkdeletepostcode');" href="#">
										<i class="icon-trash"></i>
										{l s='Delete selected' mod='dpdgroup'}
									</a>
								</li>
							</ul>
						</div>
					</div>
					{if $list_total > 20}
						<div class="row">
							<div class="col-lg-6">
								<div class="pagination">
									{l s='Display' mod='dpdgroup'}
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
									/ {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdgroup'}
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
											{assign p $page - 3}
										{else if $p > $page + 2}
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
			<button onclick="if (!confirm('Delete all postcodes?')) return false;"{if !isset($postcode_data) || isset($postcode_data) && empty($postcode_data)} disabled="disabled"{/if} class="btn btn-default pull-right" name="{DpdGroupPostcodeController::SETTINGS_DELETE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-delete"></i>
				{l s='Delete all postcodes' mod='dpdgroup'}
			</button>
			<button onclick="if (!confirm('Restore default postcodes?')) return false;" class="btn btn-default pull-right" name="{DpdGroupPostcodeController::SETTINGS_RESTORE_POSTCODE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-refresh"></i>
				{l s='Restore default postcodes' mod='dpdgroup'}
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