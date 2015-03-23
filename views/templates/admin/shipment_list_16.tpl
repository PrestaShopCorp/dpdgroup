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
<script type="text/javascript">
	var dpdgeopost_error_no_shipment_selected = '{l s='Select at least one shipment' mod='dpdgeopost' js=1}';
	var dpdgeopost_error_puckup_not_available = '{l s='To arrange pickup, manifest or label must be printed' mod='dpdgeopost' js=1}';
	var dpd_geopost_id_lang = '{$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}';
	var ps14 = 0;

	$(document).ready(function(){
		$('table#shipment_list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonShipments');
		})
	});

	$(function() {
		$('table.shipment .filter').keypress(function(e){
			var key = (e.keyCode ? e.keyCode : e.which);
			if (key == 13)
			{
				e.preventDefault();
				formSubmit(e, 'submitFilterButtonShipments');
			}
		});

		$('#submitFilterButtonShipments').click(function() {
			$('#submitFilterShipments').val(1);
		});

		if ($("table .datepicker").length > 0) {
			$("table .datepicker").datepicker({
				prevText: '',
				nextText: '',
				altFormat: 'yy-mm-dd'
			});
		}

		$("#dpdgeopost_pickup_datetime").datepicker({
			dateFormat:"yy-mm-dd"
		});

		$('#dpdgeopost_pickup_fromtime, #dpdgeopost_pickup_totime').datetimepicker({
			currentText: '{l s='Now' mod='dpdgeopost'}',
			closeText: '{l s='Done' mod='dpdgeopost'}',
			timeOnly: true,
			ampm: false,
			timeFormat: 'hh:mm:ss',
			timeSuffix: '',
			timeOnlyTitle: '{l s='Choose Time' mod='dpdgeopost'}',
			timeText: '{l s='Time' mod='dpdgeopost'}',
			hourText: '{l s='Hour' mod='dpdgeopost'}',
			minuteText: '{l s='Minute' mod='dpdgeopost'}'
		});
	});
</script>

<style>
	@media (max-width: 992px) {
		.table-responsive-row td:nth-of-type(2):before
		{
			content: "{l s='Shipment ID' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(3):before
		{
			content: "{l s='Date shipped' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(4):before
		{
			content: "{l s='Order' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(5):before
		{
			content: "{l s='Order Date' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(6):before
		{
			content: "{l s='Carrier' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(7):before
		{
			content: "{l s='Customer' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(8):before
		{
			content: "{l s='Total Qty' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(9):before
		{
			content: "{l s='Manifest Closed' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(10):before
		{
			content: "{l s='Label Printed' mod='dpdgeopost'}";
		}
		.table-responsive-row td:nth-of-type(11):before
		{
			content: "{l s='DPD pickup' mod='dpdgeopost'}";
		}
	}
</style>

<form id="form-shipment" class="form-horizontal clearfix" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">
	<input type="hidden" value="0" name="submitFilterShipments" id="submitFilterShipments" />
	<div class="panel col-lg-12">
		<div class="panel-heading">
			{l s='Shipments' mod='dpdgeopost'}
			<span class="badge">{$list_total|escape:'htmlall':'UTF-8'}</span>
		</div>
		<div class="table-responsive-row clearfix">
			<table id="shipment_list" class="table shipment">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center fixed-width-xs">
							&nbsp;
						</th>
						<th class="fixed-width-xs center">
							<span class="title_box{if $order_by == 'id_shipment'} active{/if}">
								{l s='Shipment ID' mod='dpdgeopost'}
								<a{if $order_by == 'id_shipment' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'id_shipment' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'date_shipped'} active{/if}">
								{l s='Date shipped' mod='dpdgeopost'}
								<a{if $order_by == 'date_shipped' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'date_shipped' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'id_order'} active{/if}">
								{l s='Order' mod='dpdgeopost'}
								<a{if $order_by == 'id_order' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'id_order' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'date_add'} active{/if}">
								{l s='Order Date' mod='dpdgeopost'}
								<a{if $order_by == 'date_add' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'date_add' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'carrier'} active{/if}">
								{l s='Carrier' mod='dpdgeopost'}
								<a{if $order_by == 'carrier' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'carrier' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'customer'} active{/if}">
								{l s='Customer' mod='dpdgeopost'}
								<a{if $order_by == 'customer' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'customer' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'quantity'} active{/if}">
								{l s='Total Qty' mod='dpdgeopost'}
								<a{if $order_by == 'quantity' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'quantity' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							<span class="title_box">
								{l s='Manifest Closed' mod='dpdgeopost'}
							</span>
						</th>
						<th>
							<span class="title_box">
								{l s='Label Printed' mod='dpdgeopost'}
							</span>
						</th>
						<th>
							<span class="title_box{if $order_by == 'date_pickup'} active{/if}">
								{l s='DPD pickup' mod='dpdgeopost'}
								<a{if $order_by == 'date_pickup' && $order_way == 'desc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=desc">
									<i class="icon-caret-down"></i>
								</a>
								<a{if $order_by == 'date_pickup' && $order_way == 'asc'} class="active"{/if} href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=asc">
									<i class="icon-caret-up"></i>
								</a>
							</span>
						</th>
						<th>
							&nbsp;
						</th>
					</tr>
					<tr class="nodrag nodrop filter row_hover">
						<th class="text-center">
							--
						</th>
						<th>
							<input class="filter" type="text" value="{if Context::getContext()->cookie->ShipmentsFilter_id_shipment !== false}{Context::getContext()->cookie->ShipmentsFilter_id_shipment|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_id_shipment" />
						</th>
						<th class="text-right">
							<div class="date_range row">
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='From' mod='dpdgeopost'}"
									       name="local_ShipmentsFilter_date_shipped_0"
									       id="local_ShipmentsFilter_date_shipped_0"
									       class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_shipped !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_shipped_0|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_shipped[0]" id="ShipmentsFilter_date_shipped_0">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='To' mod='dpdgeopost'}" name="local_ShipmentsFilter_date_shipped_1" id="local_ShipmentsFilter_date_shipped_1" class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_shipped !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_shipped_1|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_shipped[1]" id="ShipmentsFilter_date_shipped_1">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<script>
									$(function() {
										var dateStart = parseDate($('#ShipmentsFilter_date_shipped_0').val());
										var dateEnd = parseDate($('#ShipmentsFilter_date_shipped_1').val());
										$('#local_ShipmentsFilter_date_shipped_0').datepicker('option', 'altField', '#ShipmentsFilter_date_shipped_0');
										$('#local_ShipmentsFilter_date_shipped_1').datepicker('option', 'altField', '#ShipmentsFilter_date_shipped_1');
										if (dateStart !== null){
											$('#local_ShipmentsFilter_date_shipped_0').datepicker('setDate', dateStart);
										}
										if (dateEnd !== null){
											$('#local_ShipmentsFilter_date_shipped_1').datepicker('setDate', dateEnd);
										}
									});
								</script>
							</div>
						</th>
						<th>
							<input class="filter" type="text" value="{if Context::getContext()->cookie->ShipmentsFilter_id_order !== false}{Context::getContext()->cookie->ShipmentsFilter_id_order|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_id_order" />
						</th>
						<th class="text-right">
							<div class="date_range row">
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='From' mod='dpdgeopost'}" name="local_ShipmentsFilter_date_add_0" id="local_ShipmentsFilter_date_add_0" class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_add !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_add_0|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_add[0]" id="ShipmentsFilter_date_add_0">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='To' mod='dpdgeopost'}" name="local_ShipmentsFilter_date_add_1" id="local_ShipmentsFilter_date_add_1" class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_add !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_add_1|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_add[1]" id="ShipmentsFilter_date_add_1">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<script>
									$(function() {
										var dateStart = parseDate($('#ShipmentsFilter_date_add_0').val());
										var dateEnd = parseDate($('#ShipmentsFilter_date_add_1').val());
										$('#local_ShipmentsFilter_date_add_0').datepicker('option', 'altField', '#ShipmentsFilter_date_add_0');
										$('#local_ShipmentsFilter_date_add_1').datepicker('option', 'altField', '#ShipmentsFilter_date_add_1');
										if (dateStart !== null){
											$('#local_ShipmentsFilter_date_add_0').datepicker('setDate', dateStart);
										}
										if (dateEnd !== null){
											$('#local_ShipmentsFilter_date_add_1').datepicker('setDate', dateEnd);
										}
									});
								</script>
							</div>
						</th>
						<th>
							<input class="filter" type="text" value="{if Context::getContext()->cookie->ShipmentsFilter_carrier !== false}{Context::getContext()->cookie->ShipmentsFilter_carrier|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_carrier" />
						</th>
						<th>
							<input class="filter" type="text" value="{if Context::getContext()->cookie->ShipmentsFilter_customer !== false}{Context::getContext()->cookie->ShipmentsFilter_customer|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_customer" />
						</th>
						<th>
							<input class="filter" type="text" value="{if Context::getContext()->cookie->ShipmentsFilter_quantity !== false}{Context::getContext()->cookie->ShipmentsFilter_quantity|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_quantity" />
						</th>
						<th class="text-center">
							<select class="filter fixed-width-sm" name="ShipmentsFilter_manifest" onchange="$('input#submitFilterButtonShipments').click();">
								<option value="">-</option>
								<option {if Context::getContext()->cookie->ShipmentsFilter_manifest === '1'}selected="selected" {/if}value="1">{l s='Yes' mod='dpdgeopost'}</option>
								<option {if Context::getContext()->cookie->ShipmentsFilter_manifest === '0'}selected="selected" {/if}value="0">{l s='No' mod='dpdgeopost'}</option>
							</select>
						</th>
						<th class="text-center">
							<select class="filter fixed-width-sm" name="ShipmentsFilter_label" onchange="$('input#submitFilterButtonShipments').click();">
								<option value="">-</option>
								<option {if Context::getContext()->cookie->ShipmentsFilter_label === '1'}selected="selected" {/if}value="1">{l s='Yes' mod='dpdgeopost'}</option>
								<option {if Context::getContext()->cookie->ShipmentsFilter_label === '0'}selected="selected" {/if}value="0">{l s='No' mod='dpdgeopost'}</option>
							</select>
						</th>
						<th class="text-right">
							<div class="date_range row">
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='From' mod='dpdgeopost'}" name="local_ShipmentsFilter_date_pickup_0" id="local_ShipmentsFilter_date_pickup_0" class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_pickup !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_pickup_0|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_pickup[0]" id="ShipmentsFilter_date_pickup_0">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<div class="input-group fixed-width-md">
									<input type="text" placeholder="{l s='To' mod='dpdgeopost'}" name="local_ShipmentsFilter_date_pickup_1" id="local_ShipmentsFilter_date_pickup_1" class="filter datepicker date-input form-control"
									       value="{if Context::getContext()->cookie->ShipmentsFilter_date_pickup !== false}{Context::getContext()->cookie->local_ShipmentsFilter_date_pickup_1|escape:'htmlall':'UTF-8'}{/if}" />
									<input type="hidden" value="" name="ShipmentsFilter_date_pickup[1]" id="ShipmentsFilter_date_pickup_1">
									<span class="input-group-addon">
										<i class="icon-calendar"></i>
									</span>
								</div>
								<script>
									$(function() {
										var dateStart = parseDate($('#ShipmentsFilter_date_pickup_0').val());
										var dateEnd = parseDate($('#ShipmentsFilter_date_pickup_1').val());
										$('#local_ShipmentsFilter_date_pickup_0').datepicker('option', 'altField', '#ShipmentsFilter_date_pickup_0');
										$('#local_ShipmentsFilter_date_pickup_1').datepicker('option', 'altField', '#ShipmentsFilter_date_pickup_1');
										if (dateStart !== null){
											$('#local_ShipmentsFilter_date_pickup_0').datepicker('setDate', dateStart);
										}
										if (dateEnd !== null){
											$('#local_ShipmentsFilter_date_pickup_1').datepicker('setDate', dateEnd);
										}
									});
								</script>
							</div>
						</th>
						<th class="actions">
							<span class="pull-right">
								<button id="submitFilterButtonShipments" class="btn btn-default" data-list-id="Shipments" name="submitFilterButtonShipments" type="submit">
									<i class="icon-search"></i>
									{l s='Search' mod='dpdgeopost'}
								</button>
								{if Context::getContext()->cookie->ShipmentsFilter_id_shipment !== false ||
									Context::getContext()->cookie->ShipmentsFilter_date_shipped !== false ||
									Context::getContext()->cookie->ShipmentsFilter_id_order !== false ||
									Context::getContext()->cookie->ShipmentsFilter_date_add !== false ||
									Context::getContext()->cookie->ShipmentsFilter_carrier !== false ||
									Context::getContext()->cookie->ShipmentsFilter_customer !== false ||
									Context::getContext()->cookie->ShipmentsFilter_quantity !== false ||
									Context::getContext()->cookie->ShipmentsFilter_manifest !== false ||
									Context::getContext()->cookie->ShipmentsFilter_label !== false ||
									Context::getContext()->cookie->ShipmentsFilter_date_pickup !== false}
									<button type="submit" name="submitResetShipments" class="btn btn-warning">
										<i class="icon-eraser"></i>
										{l s='Reset' mod='dpdgeopost'}
									</button>
								{/if}
							</span>
						</th>
					</tr>
				</thead>
				<tbody>
					{if isset($shipments) && $shipments}
						{section name=ii loop=$shipments}
							<tr id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}_0">
								<td class="row-selector text-center">
									<input class="noborder" type="checkbox" value="{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}" name="ShipmentsBox[]"{if isset($smarty.post.ShipmentsBox) && in_array($shipments[ii].id_shipment, $smarty.post.ShipmentsBox)} checked="checked"{/if} />
								</td>
								<td>
									{if $shipments[ii].id_shipment}
										{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].date_shipped && $shipments[ii].date_shipped != '0000-00-00 00:00:00'}
										{$shipments[ii].date_shipped|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].id_order}
										{$shipments[ii].id_order|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].date_add && $shipments[ii].date_add != '0000-00-00 00:00:00'}
										{$shipments[ii].date_add|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].carrier}
										{$shipments[ii].carrier|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].customer}
										{$shipments[ii].customer|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].quantity}
										{$shipments[ii].quantity|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].manifest && $shipments[ii].manifest != '0000-00-00 00:00:00'}
										<a class="list-action-enable action-enabled" title="{l s='Yes' mod='dpdgeopost'}">
											<i class="icon-check"></i>
										</a>
									{else}
										<a class="list-action-enable action-disabled" title="{l s='No' mod='dpdgeopost'}">
											<i class="icon-remove"></i>
										</a>
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].label}
										<a class="list-action-enable action-enabled" title="{l s='Yes' mod='dpdgeopost'}">
											<i class="icon-check"></i>
										</a>
									{else}
										<a class="list-action-enable action-disabled" title="{l s='No' mod='dpdgeopost'}">
											<i class="icon-remove"></i>
										</a>
									{/if}
								</td>
								<td class="center">
									{if $shipments[ii].date_pickup && $shipments[ii].date_pickup != '0000-00-00 00:00:00'}
										{$shipments[ii].date_pickup|escape:'htmlall':'UTF-8'}
									{else}
										--
									{/if}
								</td>
								<td class="text-right">
									<div class="btn-group-action">
										<div class="btn-group pull-right">
											<a class="edit btn btn-default" title="{l s='View' mod='dpdgeopost'}" href="{$order_link|escape:'htmlall':'UTF-8'}&id_order={$shipments[ii].id_order|escape:'htmlall':'UTF-8'}#dpdgeopost_fieldset_identifier">
												<i class="icon-preview"></i>
												{l s='View' mod='dpdgeopost'}
											</a>
											{if $shipments[ii].shipping_number && isset($shipments[ii].carrier_url)}
												<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>
												</button>
												<ul class="dropdown-menu">
													<li>
														<a target="_blank" title="{l s='Track Shipment' mod='dpdgeopost'}" href="{$shipments[ii].carrier_url|replace:'@':$shipments[ii].shipping_number}">
															<i class="icon-truck"></i>
															{l s='Track Shipment' mod='dpdgeopost'}
														</a>
													</li>
												</ul>
											{/if}
										</div>
									</div>
								</td>
							</tr>
						{/section}
					{else}
						<tr>
							<td colspan="12" class="center">
								{l s='No shipments' mod='dpdgeopost'}
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
		<div class="row">
			{if isset($shipments) && $shipments}
				<div class="col-lg-6">
					<div class="btn-group bulk-actions dropup">
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
							{l s='Bulk actions' mod='dpdgeopost'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li>
								<a onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'ShipmentsBox[]', true);return false;" href="#">
									<i class="icon-check-sign"></i>
									{l s='Select all' mod='dpdgeopost'}
								</a>
							</li>
							<li>
								<a onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'ShipmentsBox[]', false);return false;" href="#">
									<i class="icon-check-empty"></i>
									{l s='Unselect all' mod='dpdgeopost'}
								</a>
							</li>
							<li class="divider"></li>
							<li>
								<a onclick="sendBulkAction($(this).closest('form').get(0), 'printManifest');" href="#">
									<i class="icon-print"></i>
									{l s='Print manifest(s)' mod='dpdgeopost'}
								</a>
							</li>
							<li>
								<a onclick="sendBulkAction($(this).closest('form').get(0), 'printLabels');" href="#">
									<i class="icon-print"></i>
									{l s='Print label(s)' mod='dpdgeopost'}
								</a>
							</li>
							<li>
								<a onclick="sendBulkAction($(this).closest('form').get(0), 'changeOrderStatus');" href="#">
									<i class="icon-truck"></i>
									{l s='Change order status to shipped' mod='dpdgeopost'}
								</a>
							</li>
							<li>
								<a id="displayPickupDialog" href="#">
									<i class="icon-credit-card"></i>
									{l s='Arrange DPD pickup' mod='dpdgeopost'}
								</a>
							</li>
						</ul>
					</div>
				</div>
			{/if}
			{if $list_total > $pagination[0]}
				<div class="row">
					<div class="col-lg-6">
						<div class="pagination">
							{l s='Display' mod='dpdgeopost'}
							<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
								{$selected_pagination|intval}
								<i class="icon-caret-down"></i>
							</button>
							<ul class="dropdown-menu">
								{section name=pagination_page loop=$pagination}
									<li>
										<a class="pagination-items-page" data-list-id="csv" data-items="{$pagination[pagination_page]|intval}" href="javascript:void(0);">{$pagination[pagination_page]|intval}</a>
									</li>
								{/section}
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
								$('#submitFilterShipments').val($(this).data('page')).closest('form').submit();
							});
						</script>
					</div>
				</div>
			{/if}
		</div>
	</div>
</form>

<script>
	$('.pagination-items-page').on('click',function(e){
		e.preventDefault();
		$('#'+$(this).data('list-id')+'-pagination-items-page').val($(this).data('items')).closest('form').submit();
	});
</script>

<div id="dpdgeopost_pickup_dialog">

	<div class="bootstrap">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-plus-circle"></i>
				{l s='Arrange DPD Geopost pickup' mod='dpdgeopost'}
			</div>

			<div class="form-wrapper">
				<div id="dpdgeopost_pickup_dialog_mssg"></div>
				<div class="clearfix"></div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_datetime" class="control-label col-lg-12 required ps16">
						{l s='Pickup date' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input type="text" id="dpdgeopost_pickup_datetime" name="dpdgeopost_pickup_data[date]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							yy-mm-dd
						</div>
					</div>
				</div>


				<div class="form-group">
					<label for="dpdgeopost_pickup_fromtime" class="control-label col-lg-12 required ps16">
						{l s='Pickup start time' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input type="text" id="dpdgeopost_pickup_fromtime" name="dpdgeopost_pickup_data[from_time]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							hh:mm:ss
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_totime" class="control-label col-lg-12 required ps16">
						{l s='Pickup end time' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input type="text" id="dpdgeopost_pickup_totime" name="dpdgeopost_pickup_data[to_time]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							hh:mm:ss
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_contact_name" class="control-label col-lg-12 required ps16">
						{l s='Contact name' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input id="dpdgeopost_pickup_contact_name" type="text" value="{$employee->firstname|escape:'htmlall':'UTF-8'}" name="dpdgeopost_pickup_data[contact_name]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							{l s='Sender name' mod='dpdgeopost'}
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_contact_email" class="control-label col-lg-12 required ps16">
						{l s='Contact e-mail' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input id="dpdgeopost_pickup_contact_email" type="text" value="{$employee->email|escape:'htmlall':'UTF-8'}" name="dpdgeopost_pickup_data[contact_email]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							{l s='Sender email' mod='dpdgeopost'}
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_contact_phone" class="control-label col-lg-12 required ps16">
						{l s='Contact phone no.' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<input id="dpdgeopost_pickup_contact_phone" type="text" name="dpdgeopost_pickup_data[contact_phone]" />
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							{l s='Sender phone number' mod='dpdgeopost'}
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="dpdgeopost_pickup_special_instruction" class="control-label col-lg-12">
						{l s='Message to driver' mod='dpdgeopost'}
					</label>
					<div class="col-lg-12">
						<textarea id="dpdgeopost_pickup_special_instruction" name="dpdgeopost_pickup_data[special_instruction]"></textarea>
					</div>
					<div class="col-lg-9">
						<div class="help-block">
							{l s='Additional information' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>

			<div class="panel-footer clearfix">
				<div class="buttons_container">
					<button id="submit_dpdgeopost_pickup_dialog" class="btn btn-default pull-right" type="button">
						<i class="icon-save"></i>
						{l s='Submit' mod='dpdgeopost'}
					</button>
					<button id="close_dpdgeopost_pickup_dialog" class="btn btn-default pull-right" type="button">
						<i class="icon-remove"></i>
						{l s='Cancel' mod='dpdgeopost'}
					</button>
				</div>
			</div>
		</div>
	</div>
</div>