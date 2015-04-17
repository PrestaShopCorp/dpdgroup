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
	var dpdgroup_error_no_shipment_selected = '{l s='Select at least one shipment' mod='dpdgroup' js=1}';
	var dpdgroup_error_puckup_not_available = '{l s='To arrange pickup, manifest or label must be printed' mod='dpdgroup' js=1}';
	var dpd_geopost_id_lang = '{$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}';
	var ps14 = {if isset($ps14) && $ps14}1{else}0{/if};

	$(document).ready(function(){
		if (!ps14){
			$('#dpdgroup_pickup_fromtime, #dpdgroup_pickup_totime').datetimepicker({
				currentText: '{l s='Now' mod='dpdgroup'}',
				closeText: '{l s='Done' mod='dpdgroup'}',
				timeOnly: true,
				ampm: false,
				timeFormat: 'hh:mm:ss',
				timeSuffix: '',
				timeOnlyTitle: '{l s='Choose Time' mod='dpdgroup'}',
				timeText: '{l s='Time' mod='dpdgroup'}',
				hourText: '{l s='Hour' mod='dpdgroup'}',
				minuteText: '{l s='Minute' mod='dpdgroup'}'
			});
		}

		$("#dpdgroup_pickup_datetime").datepicker({
			dateFormat:"yy-mm-dd"
		});

		$("table.Shipments .datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		$('table#shipment_list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonShipments');
		})
	});
</script>

<form class="form" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">

	{if isset($ps14) && $ps14}
		<div id="ajax_loader_container">
			<div id="ajax_running">
				{l s='Loading...' mod='dpdgroup'}
			</div>
		</div>
	{/if}

	<input type="hidden" value="0" name="submitFilterShipments" id="submitFilterShipments">
	<table id="shipment_list" name="list_table" class="table_grid">
		<tbody>
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">
						{if $page > 1}
							<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilterShipments').value=1"/>&nbsp;
							<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilterShipments').value={$page|intval - 1}"/>
						{/if}
						{l s='Page' mod='dpdgroup'} <b>{$page|intval}</b> / {$total_pages|intval}
						{if $page < $total_pages}
							<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilterShipments').value={$page|intval + 1}"/>&nbsp;
							<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilterShipments').value={$total_pages|intval}"/>
						{/if}
						| {l s='Display' mod='dpdgroup'}
						<select name="pagination" onchange="submit()">
							{foreach from=$pagination item=value}
								<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
							{/foreach}
						</select>
						/ {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdgroup'}
					</span>
					<span style="float: right;">
						<input type="submit" class="button" value="{l s='Filter' mod='dpdgroup'}" name="submitFilterButtonShipments" id="submitFilterButtonShipments">
						<input type="submit" class="button" value="{l s='Reset' mod='dpdgroup'}" name="submitResetShipments">
					</span>
					<span class="clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table Shipments">
						<colgroup>
							<col width="10px">
							<col width="100px">
							<col width="160px">
							<col width="100px">
							<col width="160px">
							<col width="150px">
							<col>
							<col width="70px">
							<col width="140px">
							<col width="140px">
							<col width="160px">
							<col width="30px">
						</colgroup>
						<thead>
							<tr style="height: 40px" class="nodrag nodrop">
								<th class="center">
									<input type="checkbox" onclick="checkDelBoxes(this.form, 'ShipmentsBox[]', this.checked)" class="noborder" name="checkme">
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Shipment ID' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=desc">
										{if $order_by == 'id_shipment' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=asc">
										{if $order_by == 'id_shipment' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Date shipped' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=desc">
										{if $order_by == 'date_shipped' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=asc">
										{if $order_by == 'date_shipped' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Order' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=desc">
										{if $order_by == 'id_order' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=asc">
										{if $order_by == 'id_order' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Order Date' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=desc">
										{if $order_by == 'date_add' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=asc">
										{if $order_by == 'date_add' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Carrier' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=desc">
										{if $order_by == 'carrier' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=asc">
										{if $order_by == 'carrier' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Customer' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=desc">
										{if $order_by == 'customer' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=asc">
										{if $order_by == 'customer' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Total Qty' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=desc">
										{if $order_by == 'quantity' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=asc">
										{if $order_by == 'quantity' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Manifest Closed' mod='dpdgroup'}<br>&nbsp;
									</span>
									<br>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Label Printed' mod='dpdgroup'}<br>&nbsp;
									</span>
									<br>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='DPD pickup' mod='dpdgroup'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=desc">
										{if $order_by == 'date_pickup' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/down.gif">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=asc">
										{if $order_by == 'date_pickup' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/up.gif">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Actions' mod='dpdgroup'}<br>&nbsp;
									</span>
									<br>
								</th>
							</tr>
							<tr style="height: 35px;" class="nodrag nodrop filter row_hover">
								<td class="center">
									--
								</td>
								<td class="center">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_id_shipment}{Context::getContext()->cookie->ShipmentsFilter_id_shipment|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_id_shipment" class="filter">
								</td>
								<td class="right">
									{l s='From' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_shipped[0]" id="ShipmentsFilter_date_shipped_0" class="filter datepicker">
									<br>
									{l s='To' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_shipped[1]" id="ShipmentsFilter_date_shipped_1" class="filter datepicker">
								</td>
								<td class="center">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_id_order}{Context::getContext()->cookie->ShipmentsFilter_id_order|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_id_order" class="filter">
								</td>
								<td class="right">
									{l s='From' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_add[0]" id="ShipmentsFilter_date_add_0" class="filter datepicker">
									<br>
									{l s='To' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_add[1]" id="ShipmentsFilter_date_add_1" class="filter datepicker">
								</td>
								<td class="right">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_carrier}{Context::getContext()->cookie->ShipmentsFilter_carrier|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_carrier" class="filter">
								</td>
								<td class="right">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_customer}{Context::getContext()->cookie->ShipmentsFilter_customer|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_customer" class="filter">
								</td>
								<td class="right">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_quantity}{Context::getContext()->cookie->ShipmentsFilter_quantity|escape:'htmlall':'UTF-8'}{/if}" name="ShipmentsFilter_quantity" class="filter">
								</td>
								<td class="center">
									<select name="ShipmentsFilter_manifest" onchange="$('input#submitFilterButtonShipments').click();">
										<option value="">--</option>
										<option {if Context::getContext()->cookie->ShipmentsFilter_manifest === '1'}selected="selected" {/if}value="1">{l s='Yes' mod='dpdgroup'}</option>
										<option {if Context::getContext()->cookie->ShipmentsFilter_manifest === '0'}selected="selected" {/if}value="0">{l s='No' mod='dpdgroup'}</option>
									</select>
								</td>
								<td class="center">
									<select name="ShipmentsFilter_label" onchange="$('input#submitFilterButtonShipments').click();">
										<option value="">--</option>
										<option {if Context::getContext()->cookie->ShipmentsFilter_label === '1'}selected="selected" {/if}value="1">{l s='Yes' mod='dpdgroup'}</option>
										<option {if Context::getContext()->cookie->ShipmentsFilter_label === '0'}selected="selected" {/if}value="0">{l s='No' mod='dpdgroup'}</option>
									</select>
								</td>
								<td class="right">
									{l s='From' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_pickup[0]" id="ShipmentsFilter_date_pickup_0" class="filter datepicker">
									<br>
									{l s='To' mod='dpdgroup'} <input type="text" style="width:70px" value="" name="ShipmentsFilter_date_pickup[1]" id="ShipmentsFilter_date_pickup_1" class="filter datepicker">
								</td>
								<td class="center">
									--
								</td>
							</tr>
						</thead>
						<tbody>
							{if isset($shipments) && $shipments}
								{section name=ii loop=$shipments}
									<tr class="row_hover" id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}_0">
										<td class="center">
											<input type="checkbox" class="noborder" value="{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}" name="ShipmentsBox[]"{if isset($smarty.post.ShipmentsBox) && in_array($shipments[ii].id_shipment, $smarty.post.ShipmentsBox)} checked="checked"{/if}>
											<input type="hidden" name="pickup_available" value="{if $shipments[ii].manifest && $shipments[ii].manifest != '0000-00-00 00:00:00' || $shipments[ii].label}1{else}0{/if}" />
										</td>
										<td class="center">
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
												<img alt="{l s='Yes' mod='dpdgroup'}" src="../img/admin/enabled.gif">
											{else}
												<img alt="{l s='No' mod='dpdgroup'}" src="../img/admin/disabled.gif">
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].label}
												<img alt="{l s='Yes' mod='dpdgroup'}" src="../img/admin/enabled.gif">
											{else}
												<img alt="{l s='No' mod='dpdgroup'}" src="../img/admin/disabled.gif">
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].date_pickup && $shipments[ii].date_pickup != '0000-00-00 00:00:00'}
												{$shipments[ii].date_pickup|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td style="white-space: nowrap;" class="center">
											<a title="{l s='View' mod='dpdgroup'}" href="{$order_link|escape:'htmlall':'UTF-8'}&id_order={$shipments[ii].id_order|escape:'htmlall':'UTF-8'}#dpdgroup_fieldset_identifier">
												<img alt="{l s='View' mod='dpdgroup'}" src="../img/admin/details.gif">
											</a>
											{if $shipments[ii].shipping_number && isset($shipments[ii].carrier_url)}
												&nbsp;
												<a target="_blank" title="{l s='Track Shipment' mod='dpdgroup'}" href="{$shipments[ii].carrier_url|replace:'@':$shipments[ii].shipping_number}">
													<img alt="{l s='Track Shipment' mod='dpdgroup'}" src="../img/admin/delivery.gif">
												</a>
											{/if}
										</td>
									</tr>
								{/section}
							{else}
								<tr>
									<td colspan="11" class="center">
										{l s='No shipments' mod='dpdgroup'}
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
					<p>
						<input class="button" type="submit" onclick="return confirm('{l s='Print selected manifest(s)?' mod='dpdgroup'}');" value="{l s='Print manifest(s)' mod='dpdgroup'}" name="printManifest" />
						<input class="button" type="submit" value="{l s='Print label(s)' mod='dpdgroup'}" name="printLabels" />
						<input class="button" type="submit" value="{l s='Change order status to shipped' mod='dpdgroup'}" name="changeOrderStatus" />
						<input class="button" type="button" value="{l s='Arrange DPD pickup' mod='dpdgroup'}" id="displayPickupDialog" />
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</form>

<div id="dpdgroup_pickup_dialog">
	<h2>{l s='Arrange DPD Group pickup' mod='dpdgroup'}</h2>
	<div id="dpdgroup_pickup_dialog_mssg"></div>
	<label>
		<sup>*</sup>{l s='Pickup date' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" id="dpdgroup_pickup_datetime" name="dpdgroup_pickup_data[date]" />
		<p>yy-mm-dd</p>
	</div>
	<div class="clear"></div>

	<label>
		<sup>*</sup>{l s='Pickup start time' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" id="dpdgroup_pickup_fromtime" name="dpdgroup_pickup_data[from_time]" />
		<p>hh:mm:ss</p>
	</div>
	<div class="clear"></div>

	<label>
		<sup>*</sup>{l s='Pickup end time' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" id="dpdgroup_pickup_totime" name="dpdgroup_pickup_data[to_time]" />
		<p>hh:mm:ss</p>
	</div>
	<div class="clear"></div>

	<label>
		<sup>*</sup>{l s='Contact name' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" value="{$employee->firstname|escape:'htmlall':'UTF-8'}" name="dpdgroup_pickup_data[contact_name]" />
		<p>{l s='Sender name' mod='dpdgroup'}</p>
	</div>
	<div class="clear"></div>

	<label>
		<sup>*</sup>{l s='Contact e-mail' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" value="{$employee->email|escape:'htmlall':'UTF-8'}" name="dpdgroup_pickup_data[contact_email]" />
		<p>{l s='Sender email' mod='dpdgroup'}</p>
	</div>
	<div class="clear"></div>

	<label>
		<sup>*</sup>{l s='Contact phone no.' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<input type="text" name="dpdgroup_pickup_data[contact_phone]" />
		<p>{l s='Sender phone number' mod='dpdgroup'}</p>
	</div>
	<div class="clear"></div>

	<label>
		{l s='Message to driver' mod='dpdgroup'}
	</label>
	<div class="margin-form">
		<textarea name="dpdgroup_pickup_data[special_instruction]"></textarea>
		<p>{l s='Additional information' mod='dpdgroup'}</p>
	</div>
	<div class="clear"></div>

	<label class="required"><sup>*</sup> {l s='Required fields' mod='dpdgroup'}</label>
	<div class="margin-form">
		<input type="button" class="button" value="{l s='Submit' mod='dpdgroup'}" id="submit_dpdgroup_pickup_dialog" />
		<input type="button" class="button" value="{l s='Cancel' mod='dpdgroup'}" id="close_dpdgroup_pickup_dialog" />
	</div>
	<div class="clear"></div>

</div>