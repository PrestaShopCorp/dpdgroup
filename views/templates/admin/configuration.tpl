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
<form id="configuration_form" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=configuration" method="post" enctype="multipart/form-data">
	<fieldset id="general">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='General' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='Debug Mode:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="debug_mode_yes" type="radio" name="{DpdGeopostConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::DEBUG_MODE, $settings->debug_mode) == 1}checked="checked"{/if} value="1" />
			<label class="t" for="debug_mode_yes">
				{l s='Yes' mod='dpdgeopost'}
			</label>
			<input id="debug_mode_no" type="radio" name="{DpdGeopostConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::DEBUG_MODE, $settings->debug_mode) == 0}checked="checked"{/if} value="0" />
			<label class="t" for="debug_mode_no">
				{l s='No' mod='dpdgeopost'}
			</label>
		</div>
		<div class="clear"></div>

		<label>
			{l s='Production Mode:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="production_mode_yes" type="radio" name="{DpdGeopostConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_MODE, $settings->production_mode) == 1}checked="checked"{/if} value="1" />
			<label class="t" for="production_mode_yes">
				{l s='Yes' mod='dpdgeopost'}
			</label>
			<input id="production_mode_no" type="radio" name="{DpdGeopostConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_MODE, $settings->production_mode) == 0}checked="checked"{/if} value="0" />
			<label class="t" for="production_mode_no">
				{l s='No' mod='dpdgeopost'}
			</label>
			<p class="preference_description">
				{l s='Select "No" if you want to test module and select "Yes" when you want to start using it in production.' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
	</fieldset>

	<br />

	<fieldset id="price_calculation">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Price calculation' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='Sipping price calculation method:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="price_calculation_webservices" type="radio" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_WEB_SERVICES}checked="checked"{/if} value="{DpdGeopostConfiguration::PRICE_CALCULATION_WEB_SERVICES}" />
			<label class="t" for="price_calculation_webservices">
				{l s='Web Services + CSV shipping price percentage and CSV COD rules' mod='dpdgeopost'}
			</label>
			<br />
			<input id="price_calculation_prestashop" type="radio" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_PRESTASHOP}checked="checked"{/if} value="{DpdGeopostConfiguration::PRICE_CALCULATION_PRESTASHOP}" />
			<label class="t" for="price_calculation_prestashop">
				{l s='Standard PrestaShop rules + CSV COD rules' mod='dpdgeopost'}
			</label>
			<br />
			<input id="price_calculation_csv" type="radio" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_CSV}checked="checked"{/if} value="{DpdGeopostConfiguration::PRICE_CALCULATION_CSV}" />
			<label class="t" for="price_calculation_csv">
				{l s='CSV rules' mod='dpdgeopost'}
			</label>
		</div>
		<div class="clear"></div>

		<div id="address_validation_block">
			<div class="separation"></div>

			<label>
				{l s='Enable Destination Address validation using Web Services:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input id="address_validation_yes" type="radio" name="{DpdGeopostConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 1}checked="checked"{/if} value="1" />
				<label class="t" for="address_validation_yes">
					{l s='Yes' mod='dpdgeopost'}
				</label>
				<input id="address_validation_no" type="radio" name="{DpdGeopostConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 0}checked="checked"{/if} value="0" />
				<label class="t" for="address_validation_no">
					{l s='No' mod='dpdgeopost'}
				</label>
				<p class="preference_description">
					{l s='If PrestaShop shipping locations rules for carriers are used for price calculation then it\'s possible to use Web Services for destination address validation. If this option is turned off then carrier will be available even if it\'s not possible make shipment to  destination address.' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>
		</div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
	</fieldset>

	<br />

	<fieldset id="cod_settings_container">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='COD settings' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='COD surcharge percentage calculation uses:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="cod_shopping_cart" type="radio" name="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART}checked="checked"{/if} value="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART|escape:'htmlall':'UTF-8'}" />
			<label class="t" for="cod_shopping_cart">
				{l s='Shopping cart price' mod='dpdgeopost'}
			</label>
			<p class="preference_description">
				{l s='COD surcharge percentage will be calculated using shopping cart' mod='dpdgeopost'}
			</p>
			<br />
			<input id="cod_shopping_cart_shipping" type="radio" name="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING}checked="checked"{/if} value="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING|escape:'htmlall':'UTF-8'}" />
			<label class="t" for="cod_shopping_cart_shipping">
				{l s='Shopping cart price + Shipping price' mod='dpdgeopost'}
			</label>
			<p class="preference_description">
				{l s='COD surcharge percentage will be calculated using shopping cart + shipping price' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<div class="toggle_cod_info_link_container">
				<a id="toggle_cod_info_link">{l s='What is included in shipping price? →' mod='dpdgeopost'}</a>
			</div>
			<div id="toggle_cod_info">
				<div class="cod_info_container">
					<span>
						{l s='Shipping price includes:' mod='dpdgeopost'}
					</span>
					<span>
						<span>
							{l s='* Shipping price defined in carrier rules or CSV rules' mod='dpdgeopost'}
						</span>
						<span>
							{l s='* Shipping "' mod='dpdgeopost'}<b>{l s='Handling charges' mod='dpdgeopost'}</b>{l s='" - can be set in PrestaShop shipping settings page' mod='dpdgeopost'}
						</span>
						<span>
							{l s='* "' mod='dpdgeopost'}<b>{l s='Additional shipping cost (per quantity)' mod='dpdgeopost'}</b>{l s='" for a product - cat be set in product settings page' mod='dpdgeopost'}
						</span>
					</span>
				</div>
			</div>
		</div>

		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>

	</fieldset>

	<br />

	<fieldset id="cod_payment_methods_container">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='COD payment method' mod='dpdgeopost'}
		</legend>


		{foreach from=DpdGeopost::getPaymentModules() item=module}
			<label for="payment_method_{$module.name|escape:'htmlall':'UTF-8'}">
				{$module.name|escape:'htmlall':'UTF-8'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{$module.name|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="payment_method_{$module.name|escape:'htmlall':'UTF-8'}" type="checkbox" name="{$module.name|escape:'htmlall':'UTF-8'}"
					{if Tools::isSubmit(DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION) && Tools::isSubmit($module.name)}
						checked="checked"
					{elseif Tools::isSubmit(DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION) && !Tools::isSubmit($module.name)}

					{elseif Configuration::get(DpdGeopostConfiguration::COD_MODULE) && $module.name == Configuration::get(DpdGeopostConfiguration::COD_MODULE)}
						checked="checked"
					{/if}
					   value="1"
				/>
			</div>
			<div class="clear"></div>
		{/foreach}

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
	</fieldset>

	<br />

	<a name="cod_selection_warning"></a>
	<fieldset id="active_services">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Active Services' mod='dpdgeopost'}
		</legend>

		<p class="{if Configuration::get('PS_ALLOW_MULTISHIPPING')}warning warn{else}clear list info{/if}">
			{l s='If multishipping (Preferences > Orders > Allow multishipig) is enabled then customers can select COD and NON COD carriers for the same order and payment method for both carriers can be selected only one so there may be situations when COD payment will be selected for NON COD carriers. Merchant is responsible for configuring multishiping correctly.' mod='dpdgeopost'}
		</p>

		<p class="warning warn cod_selection_warning">
			{l s='You do not have selected COD payment method' mod='dpdgeopost'}
		</p>

		<div class="carriers_block">
			<label for="active_services_classic">
				{l s='DPD Classic:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_classic" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC, $settings->active_services_classic) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD classic" service.' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_10">
				{l s='DPD 10:00:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_10" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_10, $settings->active_services_10) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD 10:00" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_12">
				{l s='DPD 12:00:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_12" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_12, $settings->active_services_12) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD 12:00" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_same_day">
				{l s='DPD Same Day:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_same_day" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_SAME_DAY, $settings->active_services_same_day) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD Same Day" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_b2c">
				{l s='DPD B2C:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_b2c" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_B2C, $settings->active_services_b2c) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD B2C" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_international">
				{l s='DPD International:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_international" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_INTERNATIONAL, $settings->active_services_international) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD International" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="active_services_bulgaria">
				{l s='DPD Bulgaria:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="active_services_bulgaria" type="checkbox" name="{DpdGeopostConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_BULGARIA, $settings->active_services_bulgaria) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable "DPD Bulgaria" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>
		</div>

		<div class="carriers_cod_block">
			<label for="is_cod_carrier_classic">
				{l s='DPD Classic + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_classic" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC, $settings->is_cod_carrier_classic) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD classic" service.' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_10">
				{l s='DPD 10:00 + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_10" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_10, $settings->is_cod_carrier_10) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD 10:00" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_12">
				{l s='DPD 12:00 + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_12" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_12, $settings->is_cod_carrier_12) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD 12:00" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_same_day">
				{l s='DPD Same Day + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_same_day" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY, $settings->is_cod_carrier_same_day) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD Same Day" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_b2c">
				{l s='DPD B2C + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_b2c" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_B2C, $settings->is_cod_carrier_b2c) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD B2C" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_international">
				{l s='DPD International + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_international" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL, $settings->is_cod_carrier_international) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD International" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label for="is_cod_carrier_bulgaria">
				{l s='DPD Bulgaria + COD:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
				<input id="is_cod_carrier_bulgaria" type="checkbox" name="{DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA, $settings->is_cod_carrier_bulgaria) == 1}checked="checked"{/if} value="1" />
				<p class="preference_description">
					{l s='Enable COD shipment method for "DPD Bulgaria" service' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>
		</div>

		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
		<p class="clear list info">
			{l s='Please note that after installation carriers will be created for each service. You can manage these carriers using standar PrestaShop configuration tools.' mod='dpdgeopost'}
		</p>
	</fieldset>

	<br />

	<fieldset id="default_packaging_method">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Default packaging method' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='Packaging method:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">

			<input id="packaging_method_one_product" type="radio" name="{DpdGeopostConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGeopostConfiguration::PACKAGE_METHOD_ONE_PRODUCT}checked="checked"{/if} value="{DpdGeopostConfiguration::PACKAGE_METHOD_ONE_PRODUCT}" />
			<label class="t" for="packaging_method_one_product">
				{l s='One parcel for one product' mod='dpdgeopost'}
			</label>
			<br />
			<input id="packaging_method_all_products" type="radio" name="{DpdGeopostConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGeopostConfiguration::PACKAGE_METHOD_ALL_PRODUCTS}checked="checked"{/if} value="{DpdGeopostConfiguration::PACKAGE_METHOD_ALL_PRODUCTS}" />
			<label class="t" for="packaging_method_all_products">
				{l s='One parcel for all products' mod='dpdgeopost'}
			</label>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
	</fieldset>

	<br />

	<fieldset id="country_selection">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Country' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='DPD Country Select:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<select id="dpd_country_select" name="{DpdGeopostConfiguration::COUNTRY|escape:'htmlall':'UTF-8'}">
				<option value="">{l s='-' mod='dpdgeopost'}</option>
				{foreach from=$available_countries key=iso item=country}
					<option value="{$iso|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COUNTRY, $settings->dpd_country_select) == $iso}selected="selected"{/if}>{$country.title|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				<option value="{DpdGeopostConfiguration::OTHER_COUNTRY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COUNTRY, $settings->dpd_country_select) == DpdGeopostConfiguration::OTHER_COUNTRY}selected="selected"{/if}>{l s='Other - enter your web services URL' mod='dpdgeopost'}</option>
			</select>
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			<p class="preference_description">
				{l s='Select your country from the list, or choose "custom" to enter custom web services URL' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>
	</fieldset>

	<br />

	<fieldset id="web_services">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Web Services' mod='dpdgeopost'}
		</legend>

		<div id="custom_web_service_container">
			<h3>
				{l s='You have chosen custom country. Please enter web services URLs.' mod='dpdgeopost'}<sup>{l s='*' mod='dpdgeopost'}</sup>
			</h3>

			<br />

			<label>
				{l s='Production WS URL:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input id="production_ws_url" type="text" name="{DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8', $settings->ws_production_url|escape:'htmlall':'UTF-8')}" />
				<p class="preference_description">
					{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<label>
				{l s='Test WS URL:' mod='dpdgeopost'}
			</label>
			<div class="margin-form">
				<input id="test_ws_url" type="text" name="{DpdGeopostConfiguration::TEST_URL|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::TEST_URL|escape:'htmlall':'UTF-8', $settings->ws_test_url|escape:'htmlall':'UTF-8')}" />
				<p class="preference_description">
					{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgeopost'}
				</p>
			</div>
			<div class="clear"></div>

			<div class="separation"></div>
		</div>


		<label>
			{l s='Web Service Username:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input type="text" name="{DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8', $settings->ws_username|escape:'htmlall':'UTF-8')}" />
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			<p class="preference_description">
				{l s='Enter your web services username. If you forgot your username please contact DPD GeoPost.' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<label>
			{l s='Web Service Password:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input type="text" name="{DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8', $settings->ws_password|escape:'htmlall':'UTF-8')}" />
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			<p class="preference_description">
				{l s='Enter WebServices passoword. If you forgot your password please contact DPD GeoPost.' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<label>
			{l s='Web Service Connection Timeout:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input type="text" name="{DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8', $settings->ws_timeout|escape:'htmlall':'UTF-8')}" />
			<p class="preference_description">
				{l s='Set a timeout for connecting to the DPD web service in seconds. Default is 10s. 0 - no limitl' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="separation"></div>

		<div class="margin-form">
			<p class="connection_message conf">
				{l s='Connected successfuly!' mod='dpdgeopost'}
			</p>
			<p class="connection_message error">
				{l s='Could not connect to a web service server. Error:' mod='dpdgeopost'} <span class="error_message"></span>
			</p>
		</div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
			<input id="test_connection" type="button" class="button" value="{l s='Test Connection' mod='dpdgeopost'}" />
		</div>

		<div class="small">
			<sup>{l s='*' mod='dpdgeopost'}</sup> {l s='Required field' mod='dpdgeopost'}
		</div>
	</fieldset>

	<br />

	<fieldset id="sender_payer">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Sender & Payer' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='Sender Address Id:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="sender_address_id" type="text" name="{DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8', $settings->sender_id|escape:'htmlall':'UTF-8')}" />
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			<p class="preference_description">
				{l s='You should find your sender address id in contract with DPD GeoPost.' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<label>
			{l s='Payer Id:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="payer_id" type="text" name="{DpdGeopostConfiguration::PAYER_ID|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PAYER_ID|escape:'htmlall':'UTF-8', $settings->payer_id|escape:'htmlall':'UTF-8')}" />
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			<p class="preference_description">
				{l s='You should find your payer id in contract with DPD GeoPost' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>

		<div class="small">
			<sup>{l s='*' mod='dpdgeopost'}</sup> {l s='Required field' mod='dpdgeopost'}
		</div>
	</fieldset>

	<br />

	<fieldset id="weight_measurement">
		<legend>
			<img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png" alt="{l s='Settings' mod='dpdgeopost'}" />
			{l s='Weight measurement units conversion' mod='dpdgeopost'}
		</legend>

		<label>
			{l s='System default weight units:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			{Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
		</div>
		<div class="clear"></div>

		<label>
			{l s='DPD weight units:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			{$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
		</div>
		<div class="clear"></div>

		<label>
			{l s='Conversion rate:' mod='dpdgeopost'}
		</label>
		<div class="margin-form">
			<input id="weight_conversion_input" type="text" name="{DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}" />
			<sup>{l s='*' mod='dpdgeopost'}</sup>
			1 {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span id="dpd_weight_unit">{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span> {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
			<p class="preference_description">
				{l s='Conversion rate from system to DPD weight units. If your system uses the same weight units as DPD then leave this field blank.' mod='dpdgeopost'}
			</p>
		</div>
		<div class="clear"></div>

		<div class="margin-form">
			<input type="submit" class="button" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='dpdgeopost'}" />
		</div>

		<div class="small">
			<sup>{l s='*' mod='dpdgeopost'}</sup> {l s='Required field' mod='dpdgeopost'}
		</div>
	</fieldset>
</form>