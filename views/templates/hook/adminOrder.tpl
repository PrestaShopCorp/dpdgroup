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
	var dpd_geopost_ajax_uri = '{$dpd_geopost_ajax_uri|escape:'javascript':'UTF-8'}';
	var dpd_geopost_token = '{$dpd_geopost_token|escape:'javascript':'UTF-8'}';
	var dpd_geopost_id_shop = '{$dpd_geopost_id_shop|intval}';
	var dpd_geopost_id_lang = '{$dpd_geopost_id_lang|intval}';
	var dpd_geopost_weight_unit = '{$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_|escape:'javascript':'UTF-8'}';
	var id_order = '{Tools::getValue('id_order')|intval}';
</script>
{if isset($ps14) && $ps14}
	{assign var="total_shipping_tax_incl" value=$order->total_shipping}
{else}
	{assign var="total_shipping_tax_incl" value=$order->total_shipping_tax_incl}
{/if}
<br />

<a name="dpdgroup_fieldset_identifier"></a>
<fieldset id="dpdgroup"{if isset($ps14) && $ps14} class="fixed-width"{/if}>

	{if isset($ps14) && $ps14}
		<div id="ajax_running">
			{l s='Loading...' mod='dpdgroup'}
		</div>
	{/if}

	<legend>
		<img src="{$smarty.const._DPDGROUP_MODULE_URI_}logo.gif" width="16" height="16"> {l s='DPD Group shipping information' mod='dpdgroup'}
	</legend>

	{if $settings->checkRequiredFields()}
		{if $settings->debug_mode}
			<div class="col-lg-12">
				<p class="warn">
					<img src="../img/admin/warn2.png" />
					{l s='Module is in debug mode.' mod='dpdgroup'}
					<a target="_blank" href="{$smarty.const._DPDGROUP_MODULE_URI_|escape:'htmlall':'UTF-8'}{DpdGroupWS::createDebugFileIfNotExists()|escape:'htmlall':'UTF-8'}">
						{l s='Debug file →' mod='dpdgroup'}
					</a>
				</p>
			</div>
		{/if}
		<div id="dpdgroup_notice_container"{if isset($ps14) && $ps14} class="order_ps14"{/if}>
			{if isset($errors) && $errors}
				<p class="error">
					{foreach $errors as $error}
						{$error|escape:'htmlall':'UTF-8'}<br />
					{/foreach}
				</p>
			{/if}
			{if isset($warnings) && $warnings}
				<p class="warn">
					<img src="../img/admin/warn2.png" />
					{foreach $warnings as $warning}
						{$warning|escape:'htmlall':'UTF-8'}<br />
					{/foreach}
				</p>
			{/if}
		</div>
		<b>{l s='Shipping address' mod='dpdgroup'}</b><br /><br />
		<select id="dpdgroup_id_address" autocomplete="off"{if $shipment->id_shipment && $shipment->id_manifest} disabled="disabled"{/if}>
			{foreach from=$customer_addresses item=address}
			<option value="{$address['id_address']|escape:'htmlall':'UTF-8'}"{if $address['id_address'] == $order->id_address_delivery} selected="selected"{/if}>{$address['alias']|escape:'htmlall':'UTF-8'} - {$address['address1']|escape:'htmlall':'UTF-8'} {$address['postcode']|escape:'htmlall':'UTF-8'} {$address['city']|escape:'htmlall':'UTF-8'}{if !empty($address['state'])} {$address['state']|escape:'htmlall':'UTF-8'}{/if}, {$address['country']|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
		<br /><br />
		<b>{l s='Parcels information' mod='dpdgroup'}</b><br /><br />
		<table width="100%" cellspacing="0" cellpadding="0" class="table{if isset($ps14) && $ps14} order_ps14{/if}">
			<colgroup>
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="">
			</colgroup>
			<thead>
				<tr>
					<th>{l s='Weight' mod='dpdgroup'}</th>
					<th>{l s='Parcels' mod='dpdgroup'}</th>
					<th>{l s='Payed for Shipping' mod='dpdgroup'}</th>
					<th>{l s='DPD Shipping Price' mod='dpdgroup'}</th>
					<th>{l s='Shipping Method' mod='dpdgroup'}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$total_weight|string_format:"%.3f"} {$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_}</td>
					<td>{if $shipment->parcels}{$shipment->parcels|count|escape:'htmlall':'UTF-8'}{elseif $settings->packaging_method == DpdGroupConfiguration::PACKAGE_METHOD_ONE_PRODUCT}{$products|count}{else}1{/if}</td>
					<td><span id="dpdgroup_paid_price"{if $ws_shippingPrice > 0 && $ws_shippingPrice > $total_shipping_tax_incl} style="color:red"{/if}>{displayPrice price=$total_shipping_tax_incl currency=$order->id_currency|intval}</span></td>
					<td><span id="dpdgroup_service_price"{if $ws_shippingPrice > 0 && $ws_shippingPrice > $total_shipping_tax_incl} style="color:red"{/if}>{$ws_shippingPrice|escape:'htmlall':'UTF-8'}</span></td>
					<td>
						<select id="dpd_shipping_method" autocomplete="off"{if $shipment->id_shipment && $shipment->id_manifest} disabled="disabled"{/if}>
							<option value="">-</option>
						{if $settings->active_services_10}
							<option value="{$smarty.const._DPDGROUP_10_ID_}"{if $shipment->mainServiceCode == 10 || $selected_shipping_method_id == 10} selected="selected"{/if}>{l s='DPD 10:00' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_12}
							<option value="{$smarty.const._DPDGROUP_12_ID_}"{if $shipment->mainServiceCode == 9 || $selected_shipping_method_id == 9} selected="selected"{/if}>{l s='DPD 12:00' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_classic}
							<option value="{$smarty.const._DPDGROUP_CLASSIC_ID_}"{if $shipment->mainServiceCode == 1 || $selected_shipping_method_id == 1} selected="selected"{/if}>{l s='DPD Classic' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_same_day}
							<option value="{$smarty.const._DPDGROUP_SAME_DAY_ID_}"{if $shipment->mainServiceCode == 27 || $selected_shipping_method_id == 27} selected="selected"{/if}>{l s='DPD Same Day' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_b2c}
							<option value="{$smarty.const._DPDGROUP_B2C_ID_}"{if $shipment->mainServiceCode == 109 || $selected_shipping_method_id == 109} selected="selected"{/if}>{l s='DPD B2C' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_international}
							<option value="{$smarty.const._DPDGROUP_INTERNATIONAL_ID_}"{if $shipment->mainServiceCode == 40033 || $selected_shipping_method_id == 40033} selected="selected"{/if}>{l s='DPD International' mod='dpdgroup'}</option>
						{/if}
						{if $settings->active_services_bulgaria}
							<option value="{$smarty.const._DPDGROUP_BULGARIA_ID_}"{if $shipment->mainServiceCode == 40107 || $selected_shipping_method_id == 40107} selected="selected"{/if}>{l s='DPD Bulgaria' mod='dpdgroup'}</option>
						{/if}
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		{if $shipment->id_shipment}
			<div class="dpdgroup_shipment_action_buttons">
				<a href="{$smarty.const._DPDGROUP_PDF_URI_|escape:'htmlall':'UTF-8'}?printLabels=true&token={$dpd_geopost_token|escape:'htmlall':'UTF-8'}&id_shop={$dpd_geopost_id_shop|escape:'htmlall':'UTF-8'}&id_lang={$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}" id="dpdgroup_print_labels" class="button">
					<img title="{l s='Print labels' mod='dpdgroup'}" alt="{l s='Print labels' mod='dpdgroup'}" src="../img/admin/printer.gif"> {l s='Print labels' mod='dpdgroup'}
				</a>
				{if !$shipment->id_manifest}
					<input type="button" id="dpdgroup_delete_shipment" onclick="if (confirm('{l s='Are You sure?' mod='dpdgroup'}')) deleteShipment();" class="button" value="{l s='Delete' mod='dpdgroup'}" />
					<input type="button" id="dpdgroup_edit_shipment" class="button" value="{l s='Edit' mod='dpdgroup'}" />
				{/if}
				<input type="button" id="dpdgroup_preview_shipment" class="button" value="{l s='Preview' mod='dpdgroup'}" />
			</div>
		{else}
			<input type="button" class="button"{if !$force_enable_button && (!$selected_shipping_method_id || $ws_shippingPrice == '---')} disabled="disabled"{/if} id="dpdgroup_create_shipment" value="{l s='Create shipment' mod='dpdgroup'}" autocomplete="off" />
		{/if}
		<div class="clear"></div>

		{if $shipment->id_shipment}
			{if isset($ps14) && $ps14}
				<hr />
			{else}
				<div class="separation"></div>
			{/if}
			<b>{l s='Status' mod='dpdgroup'}</b><br /><br />
			<table id="dpdgroup_shipment_actions" width="100%" cellspacing="0" cellpadding="0" class="table">
				<colgroup>
					<col width="">
					<col width="20%">
				</colgroup>
				<thead>
					<tr>
						<th>{l s='Action' mod='dpdgroup'}</th>
						<th>{l s='Status' mod='dpdgroup'}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{l s='DPD pickup arranged' mod='dpdgroup'}</td>
						<td>{if $shipment->isPickupArranged()}{l s='Yes' mod='dpdgroup'}{else}{l s='No' mod='dpdgroup'}{/if}</td>
					</tr>
					<tr>
						<td>{l s='Manifest closed' mod='dpdgroup'}</td>
						<td>{if $shipment->id_manifest}{l s='Yes' mod='dpdgroup'}{else}{l s='No' mod='dpdgroup'}{/if}</td>
					</tr>
				</tbody>
			</table>
		{/if}

		<div id="dpdgroup_shipment_creation">
			<h2>{l s='Shipment creation' mod='dpdgroup'}</h2>
			{if $display_product_weight_warning}
				<p class="warn">
					<img src="../img/admin/warn2.png" />
					{l s='Order has at least one product without weight' mod='dpdgroup'}
				</p>
			{/if}
			<div class="message_container"></div>
			<b>{l s='Group the products in your shipment into parcels' mod='dpdgroup'}</b><br />
			{l s='This module lets you organize your products into parcels using the table below. Select parcel number.' mod='dpdgroup'}
			<br /><br />
			<table width="100%" cellspacing="0" cellpadding="0" class="table" id="parcel_selection_table">
				<colgroup>
					<col width="10%">
					<col width="">
					<col width="20%">
					<col width="20%">
					<col width="5%">
				</colgroup>
				<thead>
					<tr>
						<th>{l s='ID' mod='dpdgroup'}</th>
						<th>{l s='Product' mod='dpdgroup'}</th>
						<th>{l s='Weight' mod='dpdgroup'}</th>
						<th>{l s='Total selected parcel weight' mod='dpdgroup'}</th>
						<th>{l s='Parcel' mod='dpdgroup'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$products item=product name=products}
						{if isset($product.parcel_weight)}
							{assign var="parcel_total_weight" value=$product.parcel_weight}
						{else}
							{assign var="parcel_total_weight" value=$product.product_weight}
						{/if}
						<tr>
							<td class="product_id">{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}</td>
							<td>{$product.product_name|escape:'htmlall':'UTF-8'}</td>
							<td class="parcel_weight" rel="{$product.product_weight|string_format:"%.3f"}">
								<input type="text" value="{$product.product_weight|string_format:"%.3f"}" /> {$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_}
							</td>
							<td class="parcel_total_weight" rel="{$parcel_total_weight|escape:'htmlall':'UTF-8'}">
								<input type="text" value="{$parcel_total_weight|string_format:"%.3f"}" /> {$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_}
							</td>
							<td>
								<select class="parcel_selection" autocomplete="off">
									{if isset($product.parcel_reference_number)}
										{foreach from=$shipment->parcels key=parcel_no item=parcel}
											{if $parcel.parcelReferenceNumber == $product.parcel_reference_number}
												{assign var="selected_parcel" value=$parcel_no+1}
											{/if}
										{/foreach}
									{/if}

									{section start=1 loop=$products|count+1 name=parcel}
									{if !isset($selected_parcel)}
										{if $settings->packaging_method == DpdGroupConfiguration::PACKAGE_METHOD_ONE_PRODUCT}
											{assign var="parcelNo" value=$smarty.foreach.products.index+1}
										{else}
											{assign var="parcelNo" value=1}
										{/if}
										<option value="{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}"{if $smarty.section.parcel.index == $parcelNo} selected="selected"{/if}>{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}</option>
									{else}
										<option value="{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}"{if $smarty.section.parcel.index == $selected_parcel} selected="selected"{/if}>{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}</option>
									{/if}
									{/section}
								</select>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			<br />

			<b>{l s='Enter description for each parcel' mod='dpdgroup'}</b><br />
			{l s='You can enter description of each parcel, for communication with courier services, in the fields below.' mod='dpdgroup'}
			<br /><br />
			<table width="100%" cellspacing="0" cellpadding="0" class="table" id="parcel_descriptions_table">
				<colgroup>
					<col width="10%">
					<col width="">
				</colgroup>
				<thead>
					<tr>
						<th>{l s='Parcel' mod='dpdgroup'}</th>
						<th>{l s='Description' mod='dpdgroup'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$products item=product name=products}
					<tr>
						<td class="parcel_id_{$smarty.foreach.products.iteration|escape:'htmlall':'UTF-8'}">{$smarty.foreach.products.iteration|escape:'htmlall':'UTF-8'}</td>
						<td class="parcel_description">
							{if $shipment->parcels && isset($shipment->parcels[$smarty.foreach.products.iteration-1])}
								{assign var="description" value=$shipment->parcels[$smarty.foreach.products.iteration-1].description}
							{elseif $shipment->parcels}
								{assign var="description" value=""}
							{elseif $settings->packaging_method == DpdGroupConfiguration::PACKAGE_METHOD_ONE_PRODUCT}
								{assign var="description" value=$product.description}
							{elseif $settings->packaging_method == DpdGroupConfiguration::PACKAGE_METHOD_ALL_PRODUCTS && $smarty.foreach.products.iteration == 1}
								{assign var="description" value=$product.description}
							{else}
								{assign var="description" value=""}
							{/if}
							<input type="hidden" value="{$description|escape:'htmlall':'UTF-8'}" autocomplete="off" />
							<input type="text" value="{$description|escape:'htmlall':'UTF-8'}"{if !$description} disabled="disabled"{/if} autocomplete="off" />
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<br />
			<div class="buttons_container">
				<input type="button" class="button" id="dpdgroup_shipment_creation_save" value="{l s='Save' mod='dpdgroup'}" />
				<input type="button" class="button" id="dpdgroup_shipment_creation_cancel" value="{l s='Cancel' mod='dpdgroup'}" />
			</div>
			<input type="button" class="button" id="dpdgroup_shipment_creation_close" value="{l s='Close' mod='dpdgroup'}" />
		</div>
	{else}
		<p class="warn">
			<img src="../img/admin/warn2.png" />
			{if $settings->debug_mode}
				{l s='Module is in debug mode.' mod='dpdgroup'}
				<a target="_blank" href="{$smarty.const._DPDGROUP_MODULE_URI_|escape:'htmlall':'UTF-8'}{DpdGroupWS::createDebugFileIfNotExists()|escape:'htmlall':'UTF-8'}">
					{l s='Debug file →' mod='dpdgroup'}
				</a>
				<br />
			{/if}
			{l s='Please provide required information in module settings page' mod='dpdgroup'}
			<a href="{$module_link|escape:'htmlall':'UTF-8'}&menu=configuration">{l s='here' mod='dpdgroup'}</a>
		</p>
	{/if}

	{if $order->shipping_number && $carrier_url}
		{if isset($ps14) && $ps14}
			<hr />
		{else}
			<div class="separation"></div>
		{/if}
		<a target="_blank" href="{$carrier_url|replace:'@':$order->shipping_number|escape:'htmlall':'UTF-8'}">
			<button id="track_shipment_button" class="button">
				{l s='Track Shipment' mod='dpdgroup'}
			</button>
		</a>
	{/if}
</fieldset>