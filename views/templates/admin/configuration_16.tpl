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
<form id="configuration_form" class="form-horizontal" enctype="multipart/form-data" method="post" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=configuration">
	<div id="configuration_fieldset_general" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='General' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<div>
					<label class="control-label col-lg-3">
						{l s='Debug Mode:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="debug_mode_yes" name="{DpdGeopostConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DEBUG_MODE, $settings->debug_mode) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="debug_mode_yes">
								{l s='Yes' mod='dpdgeopost'}
							</label>
							<input type="radio" value="0" id="debug_mode_no" name="{DpdGeopostConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DEBUG_MODE, $settings->debug_mode) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="debug_mode_no">
								{l s='No' mod='dpdgeopost'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Select "No" if you want to test module and select "Yes" when you want to start using it in production.' mod='dpdgeopost'}
						</div>
					</div>
				</div>

				<div>
					<label class="control-label col-lg-3">
						{l s='Production Mode:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="production_mode_yes" name="{DpdGeopostConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_MODE, $settings->production_mode) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="production_mode_yes">
								{l s='Yes' mod='dpdgeopost'}
							</label>
							<input type="radio" value="0" id="production_mode_no" name="{DpdGeopostConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_MODE, $settings->production_mode) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="production_mode_no">
								{l s='No' mod='dpdgeopost'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Select "No" if you want to test module and select "Yes" when you want to start using it in production.' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="configuration_fieldset_price_calculation" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Price calculation' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Sipping price calculation method:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="price_calculation_webservices">
							<input id="price_calculation_webservices" type="radio" value="{DpdGeopostConfiguration::PRICE_CALCULATION_WEB_SERVICES|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_WEB_SERVICES} checked="checked"{/if} />
							{l s='Web Services + CSV shipping price percentage and CSV COD rules' mod='dpdgeopost'}
						</label>
					</p>
					<p class="radio">
						<label for="price_calculation_prestashop">
							<input id="price_calculation_prestashop" type="radio" value="{DpdGeopostConfiguration::PRICE_CALCULATION_PRESTASHOP|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_PRESTASHOP} checked="checked"{/if} />
							{l s='Standard PrestaShop rules + CSV COD rules' mod='dpdgeopost'}
						</label>
					</p>
					<p class="radio">
						<label for="price_calculation_csv">
							<input id="price_calculation_csv" type="radio" value="{DpdGeopostConfiguration::PRICE_CALCULATION_CSV|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGeopostConfiguration::PRICE_CALCULATION_CSV} checked="checked"{/if} />
							{l s='CSV rules' mod='dpdgeopost'}
						</label>
					</p>
				</div>
			</div>

			<div id="address_validation_block">
				<hr />
				<div class="form-group">
					<label class="control-label col-lg-3">
						{l s='Enable Destination Address validation using Web Services:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="address_validation_yes" name="{DpdGeopostConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="address_validation_yes">
								{l s='Yes' mod='dpdgeopost'}
							</label>
							<input type="radio" value="0" id="address_validation_no" name="{DpdGeopostConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="address_validation_no">
								{l s='No' mod='dpdgeopost'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='If PrestaShop shipping locations rules for carriers are used for price calculation then it\'s possible to use Web Services for destination address validation. If this option is turned off then carrier will be available even if it\'s not possible make shipment to  destination address.' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="cod_settings_container" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='COD settings' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='COD surcharge percentage calculation uses:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="cod_shopping_cart">
							<input id="cod_shopping_cart" type="radio" value="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART} checked="checked"{/if} />
							{l s='Shopping cart price' mod='dpdgeopost'}
						</label>
					</p>
					<div class="col-lg-12">
						<div class="help-block">
							{l s='COD surcharge percentage will be calculated using shopping cart' mod='dpdgeopost'}
						</div>
					</div>
					<p class="radio">
						<label for="cod_shopping_cart_shipping">
							<input id="cod_shopping_cart_shipping" type="radio" value="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING}" name="{DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING} checked="checked"{/if} />
							{l s='Shopping cart price + Shipping price' mod='dpdgeopost'}
						</label>
					</p>
					<div class="col-lg-12">
						<div class="help-block">
							{l s='COD surcharge percentage will be calculated using shopping cart + shipping price' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
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
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="cod_payment_methods_container" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='COD payment method' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			{foreach from=DpdGeopost::getPaymentModules() item=module}
				<div class="form-group">
					<label class="control-label col-lg-3">
						{$module.name|escape:'htmlall':'UTF-8'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label for="payment_method_{$module.name|escape:'htmlall':'UTF-8'}">
								<input type="hidden" name="{$module.name|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="payment_method_{$module.name|escape:'htmlall':'UTF-8'}" type="checkbox" value="1" name="{$module.name|escape:'htmlall':'UTF-8'}"
									{if Tools::isSubmit(DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION) && Tools::getValue($module.name) ||
										Configuration::get(DpdGeopostConfiguration::COD_MODULE) && $module.name == Configuration::get(DpdGeopostConfiguration::COD_MODULE)}
										checked="checked"
									{/if}>
							</label>
						</div>
					</div>
				</div>
			{/foreach}
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<a name="cod_selection_warning"></a>
	<div id="active_services" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Active Services' mod='dpdgeopost'}
		</div>

		<p class="alert {if Configuration::get('PS_ALLOW_MULTISHIPPING')}alert-warning{else}alert-info{/if}">
			{l s='If multishipping (Preferences > Orders > Allow multishipig) is enabled then customers can select COD and NON COD carriers for the same order and payment method for both carriers can be selected only one so there may be situations when COD payment will be selected for NON COD carriers. Merchant is responsible for configuring multishiping correctly.' mod='dpdgeopost'}
		</p>

		<p class="alert warn alert-warning cod_selection_warning">
			{l s='You do not have selected COD payment method' mod='dpdgeopost'}
		</p>

		<div class="form-wrapper">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="active_services_classic" class="control-label col-lg-3">
						{l s='DPD Classic:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_classic" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC, $settings->active_services_classic) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD classic" service.' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_10" class="control-label col-lg-3">
						{l s='DPD 10:00:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label for="active_services_10">
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_10" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_10, $settings->active_services_10) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD 10:00" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_12" class="control-label col-lg-3">
						{l s='DPD 12:00:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_12" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_12, $settings->active_services_12) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD 12:00" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_same_day" class="control-label col-lg-3">
						{l s='DPD Same Day:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_same_day" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_SAME_DAY, $settings->active_services_same_day) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD Same Day" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_b2c" class="control-label col-lg-3">
						{l s='DPD B2C:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_b2c" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_B2C, $settings->active_services_b2c) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD B2C" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_international" class="control-label col-lg-3">
						{l s='DPD International:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_international" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_INTERNATIONAL, $settings->active_services_international) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD International" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_bulgaria" class="control-label col-lg-3">
						{l s='DPD Bulgaria:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_bulgaria" type="checkbox" value="1" name="{DpdGeopostConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_BULGARIA, $settings->active_services_bulgaria) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD Bulgaria" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 carriers_cod_block">
				<div class="form-group">
					<label for="is_cod_carrier_classic" class="control-label col-lg-3">
						{l s='DPD Classic + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_classic" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC, $settings->is_cod_carrier_classic) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD classic" service.' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_10" class="control-label col-lg-3">
						{l s='DPD 10:00 + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_10" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_10, $settings->is_cod_carrier_10) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD 10:00" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_12" class="control-label col-lg-3">
						{l s='DPD 12:00 + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_12" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_12, $settings->is_cod_carrier_12) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD 12:00" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_same_day" class="control-label col-lg-3">
						{l s='DPD Same Day + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_same_day" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY, $settings->is_cod_carrier_same_day) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD Same Day" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_b2c" class="control-label col-lg-3">
						{l s='DPD B2C + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_b2c" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_B2C, $settings->is_cod_carrier_b2c) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD B2C" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_international" class="control-label col-lg-3">
						{l s='DPD International + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_international" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL, $settings->is_cod_carrier_international) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD International" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_bulgaria" class="control-label col-lg-3">
						{l s='DPD Bulgaria + COD:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_bulgaria" type="checkbox" value="1" name="{DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA, $settings->is_cod_carrier_bulgaria) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD Bulgaria" service' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			<br />

			<p class="alert alert-info">
				{l s='Please note that after installation carriers will be created for each service. You can manage these carriers using standar PrestaShop configuration tools.' mod='dpdgeopost'}
			</p>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="default_packaging_method" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Default packaging method' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Packaging method:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="packaging_method_one_product">
							<input id="packaging_method_one_product" type="radio" value="{DpdGeopostConfiguration::PACKAGE_METHOD_ONE_PRODUCT|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGeopostConfiguration::PACKAGE_METHOD_ONE_PRODUCT} checked="checked"{/if} />
							{l s='One parcel for one product' mod='dpdgeopost'}
						</label>
					</p>
					<p class="radio">
						<label for="packaging_method_all_products">
							<input id="packaging_method_all_products" type="radio" value="{DpdGeopostConfiguration::PACKAGE_METHOD_ALL_PRODUCTS|escape:'htmlall':'UTF-8'}" name="{DpdGeopostConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}"{if DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGeopostConfiguration::PACKAGE_METHOD_ALL_PRODUCTS} checked="checked"{/if} />
							{l s='One parcel for all products' mod='dpdgeopost'}
						</label>
					</p>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="country_selection" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Country' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="dpd_country_select" class="control-label col-lg-3 required">
					{l s='DPD Country Select:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<select id="dpd_country_select" class="form-control fixed-width-xxl " name="{DpdGeopostConfiguration::COUNTRY|escape:'htmlall':'UTF-8'}">
						<option value="">{l s='-' mod='dpdgeopost'}</option>
						{foreach from=$available_countries key=iso item=country}
							<option value="{$iso|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COUNTRY, $settings->dpd_country_select) == $iso}selected="selected"{/if}>{$country.title|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
						<option value="{DpdGeopostConfiguration::OTHER_COUNTRY|escape:'htmlall':'UTF-8'}" {if DPDGeopost::getInputValue(DpdGeopostConfiguration::COUNTRY, $settings->dpd_country_select) == DpdGeopostConfiguration::OTHER_COUNTRY}selected="selected"{/if}>{l s='Other - enter your web services URL' mod='dpdgeopost'}</option>
					</select>
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Select your country from the list, or choose "custom" to enter custom web services URL' mod='dpdgeopost'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="web_services" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Web Services' mod='dpdgeopost'}
		</div>

		<div id="custom_web_service_container">
			<label class="required">
				{l s='You have chosen custom country. Please enter web services URLs.' mod='dpdgeopost'}
			</label>

			<div class="form-wrapper">
				<div class="form-group">
					<label for="production_ws_url" class="control-label col-lg-3">
						{l s='Production WS URL:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<input id="production_ws_url" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8', $settings->ws_production_url|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8'}" size="5">
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgeopost'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="test_ws_url" class="control-label col-lg-3">
						{l s='Test WS URL:' mod='dpdgeopost'}
					</label>
					<div class="col-lg-9">
						<input id="test_ws_url" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::TEST_URL|escape:'htmlall':'UTF-8', $settings->ws_test_url|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::TEST_URL|escape:'htmlall':'UTF-8'}" size="5">
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgeopost'}
						</div>
					</div>
				</div>
			</div>

			<hr />
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="ws_username" class="control-label col-lg-3 required">
					{l s='Web Service Username:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<input id="ws_username" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8', $settings->ws_username|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Enter your web services username. If you forgot your username please contact DPD GeoPost.' mod='dpdgeopost'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ws_password" class="control-label col-lg-3 required">
					{l s='Web Service Password:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<input id="ws_password" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8', $settings->ws_password|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Enter WebServices passoword. If you forgot your password please contact DPD GeoPost.' mod='dpdgeopost'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ws_timeout" class="control-label col-lg-3">
					{l s='Web Service Connection Timeout:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<input id="ws_timeout" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8', $settings->ws_timeout|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Set a timeout for connecting to the DPD web service in seconds. Default is 10s. 0 - no limitl' mod='dpdgeopost'}
					</div>
				</div>

				<p class="connection_message conf alert alert-success">
					{l s='Connected successfuly!' mod='dpdgeopost'}
				</p>
				<p class="connection_message error alert alert-danger">
					{l s='Could not connect to a web service server. Error:' mod='dpdgeopost'} <span class="error_message"></span>
				</p>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
			<button id="test_connection" class="btn btn-default pull-right" type="button">
				<i class="process-icon-refresh"></i>
				{l s='Test Connection' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="sender_payer" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Sender & Payer' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3 required">
					{l s='Sender Address Id:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<input id="sender_address" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8', $settings->sender_id|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='You should find your sender address id in contract with DPD GeoPost.' mod='dpdgeopost'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="payer_id" class="control-label col-lg-3 required">
					{l s='Payer Id:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<input id="payer_id" class="form-control fixed-width-xxl" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PAYER_ID|escape:'htmlall':'UTF-8', $settings->payer_id|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::PAYER_ID|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='You should find your payer id in contract with DPD GeoPost' mod='dpdgeopost'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>

	<div id="weight_measurement" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Weight measurement units conversion' mod='dpdgeopost'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3">
					{l s='System default weight units:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					{Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
				</div>
			</div>
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3">
					{l s='DPD weight units:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					{$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
				</div>
			</div>
			<div class="form-group">
				<label for="weight_conversion_input" class="control-label col-lg-3 required">
					{l s='Conversion rate:' mod='dpdgeopost'}
				</label>
				<div class="col-lg-9">
					<div class="col-sm-3">
						<div class="input-group">
							<input id="weight_conversion_input" class="form-control" type="text" value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}" name="{DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}" size="5">
							<span class="input-group-addon">
								1 {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span id="dpd_weight_unit">{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span> {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
							</span>
						</div>
					</div>
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Conversion rate from system to DPD weight units. If your system uses the same weight units as DPD then leave this field blank.' mod='dpdgeopost'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgeopost'}
			</button>
		</div>
	</div>
</form>