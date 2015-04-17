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
			{l s='General' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<div>
					<label class="control-label col-lg-3">
						{l s='Debug Mode:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="debug_mode_yes" name="{DpdGroupConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::DEBUG_MODE, $settings->debug_mode) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="debug_mode_yes">
								{l s='Yes' mod='dpdgroup'}
							</label>
							<input type="radio" value="0" id="debug_mode_no" name="{DpdGroupConfiguration::DEBUG_MODE|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::DEBUG_MODE, $settings->debug_mode) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="debug_mode_no">
								{l s='No' mod='dpdgroup'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Select "No" if you want to test module and select "Yes" when you want to start using it in production.' mod='dpdgroup'}
						</div>
					</div>
				</div>

				<div>
					<label class="control-label col-lg-3">
						{l s='Production Mode:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="production_mode_yes" name="{DpdGroupConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PRODUCTION_MODE, $settings->production_mode) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="production_mode_yes">
								{l s='Yes' mod='dpdgroup'}
							</label>
							<input type="radio" value="0" id="production_mode_no" name="{DpdGroupConfiguration::PRODUCTION_MODE|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PRODUCTION_MODE, $settings->production_mode) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="production_mode_no">
								{l s='No' mod='dpdgroup'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Select "No" if you want to test module and select "Yes" when you want to start using it in production.' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="configuration_fieldset_price_calculation" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Price calculation' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Sipping price calculation method:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="price_calculation_webservices">
							<input id="price_calculation_webservices" type="radio" value="{DpdGroupConfiguration::PRICE_CALCULATION_WEB_SERVICES|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGroupConfiguration::PRICE_CALCULATION_WEB_SERVICES} checked="checked"{/if} />
							{l s='Web Services + CSV shipping price percentage and CSV COD rules' mod='dpdgroup'}
						</label>
					</p>
					<p class="radio">
						<label for="price_calculation_prestashop">
							<input id="price_calculation_prestashop" type="radio" value="{DpdGroupConfiguration::PRICE_CALCULATION_PRESTASHOP|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGroupConfiguration::PRICE_CALCULATION_PRESTASHOP} checked="checked"{/if} />
							{l s='Standard PrestaShop rules + CSV COD rules' mod='dpdgroup'}
						</label>
					</p>
					<p class="radio">
						<label for="price_calculation_csv">
							<input id="price_calculation_csv" type="radio" value="{DpdGroupConfiguration::PRICE_CALCULATION_CSV|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::PRICE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PRICE_CALCULATION, $settings->price_calculation_method) == DpdGroupConfiguration::PRICE_CALCULATION_CSV} checked="checked"{/if} />
							{l s='CSV rules' mod='dpdgroup'}
						</label>
					</p>
				</div>
			</div>

			<div id="address_validation_block">
				<hr />
				<div class="form-group">
					<label class="control-label col-lg-3">
						{l s='Enable Destination Address validation using Web Services:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" value="1" id="address_validation_yes" name="{DpdGroupConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 1} checked="checked"{/if} />
							<label class="radioCheck" for="address_validation_yes">
								{l s='Yes' mod='dpdgroup'}
							</label>
							<input type="radio" value="0" id="address_validation_no" name="{DpdGroupConfiguration::ADDRESS_VALIDATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::ADDRESS_VALIDATION, $settings->address_validation) == 0} checked="checked"{/if} />
							<label class="radioCheck" for="address_validation_no">
								{l s='No' mod='dpdgroup'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='If PrestaShop shipping locations rules for carriers are used for price calculation then it\'s possible to use Web Services for destination address validation. If this option is turned off then carrier will be available even if it\'s not possible make shipment to  destination address.' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="cod_settings_container" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='COD settings' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='COD surcharge percentage calculation uses:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="cod_shopping_cart">
							<input id="cod_shopping_cart" type="radio" value="{DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART} checked="checked"{/if} />
							{l s='Shopping cart price' mod='dpdgroup'}
						</label>
					</p>
					<div class="col-lg-12">
						<div class="help-block">
							{l s='COD surcharge percentage will be calculated using shopping cart' mod='dpdgroup'}
						</div>
					</div>
					<p class="radio">
						<label for="cod_shopping_cart_shipping">
							<input id="cod_shopping_cart_shipping" type="radio" value="{DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING}" name="{DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION}"{if DPDGroup::getInputValue(DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION, $settings->cod_percentage_calculation) == DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART_SHIPPING} checked="checked"{/if} />
							{l s='Shopping cart price + Shipping price' mod='dpdgroup'}
						</label>
					</p>
					<div class="col-lg-12">
						<div class="help-block">
							{l s='COD surcharge percentage will be calculated using shopping cart + shipping price' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<div class="toggle_cod_info_link_container">
						<a id="toggle_cod_info_link">{l s='What is included in shipping price? →' mod='dpdgroup'}</a>
					</div>
					<div id="toggle_cod_info">
						<div class="cod_info_container">
							<span>
								{l s='Shipping price includes:' mod='dpdgroup'}
							</span>
							<span>
								<span>
									{l s='* Shipping price defined in carrier rules or CSV rules' mod='dpdgroup'}
								</span>
								<span>
									{l s='* Shipping "' mod='dpdgroup'}<b>{l s='Handling charges' mod='dpdgroup'}</b>{l s='" - can be set in PrestaShop shipping settings page' mod='dpdgroup'}
								</span>
								<span>
									{l s='* "' mod='dpdgroup'}<b>{l s='Additional shipping cost (per quantity)' mod='dpdgroup'}</b>{l s='" for a product - cat be set in product settings page' mod='dpdgroup'}
								</span>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="cod_payment_methods_container" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='COD payment method' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			{foreach from=DpdGroup::getPaymentModules() item=module}
				<div class="form-group">
					<label class="control-label col-lg-3">
						{$module.name|escape:'htmlall':'UTF-8'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label for="payment_method_{$module.name|escape:'htmlall':'UTF-8'}">
								<input type="hidden" name="{$module.name|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="payment_method_{$module.name|escape:'htmlall':'UTF-8'}" type="checkbox" value="1" name="{$module.name|escape:'htmlall':'UTF-8'}"
									{if Tools::isSubmit(DpdGroupConfigurationController::SETTINGS_SAVE_ACTION) && Tools::getValue($module.name) ||
										Configuration::get(DpdGroupConfiguration::COD_MODULE) && $module.name == Configuration::get(DpdGroupConfiguration::COD_MODULE)}
										checked="checked"
									{/if}>
							</label>
						</div>
					</div>
				</div>
			{/foreach}
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<a name="cod_selection_warning"></a>
	<div id="active_services" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Active Services' mod='dpdgroup'}
		</div>

		<p class="alert {if Configuration::get('PS_ALLOW_MULTISHIPPING')}alert-warning{else}alert-info{/if}">
			{l s='If multishipping (Preferences > Orders > Allow multishipig) is enabled then customers can select COD and NON COD carriers for the same order and payment method for both carriers can be selected only one so there may be situations when COD payment will be selected for NON COD carriers. Merchant is responsible for configuring multishiping correctly.' mod='dpdgroup'}
		</p>

		<p class="alert warn alert-warning cod_selection_warning">
			{l s='You do not have selected COD payment method' mod='dpdgroup'}
		</p>

		<div class="form-wrapper">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="active_services_classic" class="control-label col-lg-3">
						{l s='DPD Classic:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_classic" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_CLASSIC, $settings->active_services_classic) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD classic" service.' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_10" class="control-label col-lg-3">
						{l s='DPD 10:00:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label for="active_services_10">
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_10" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_10|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_10, $settings->active_services_10) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD 10:00" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_12" class="control-label col-lg-3">
						{l s='DPD 12:00:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_12" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_12|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_12, $settings->active_services_12) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD 12:00" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_same_day" class="control-label col-lg-3">
						{l s='DPD Same Day:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_same_day" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_SAME_DAY, $settings->active_services_same_day) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD Same Day" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_b2c" class="control-label col-lg-3">
						{l s='DPD B2C:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_b2c" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_B2C|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_B2C, $settings->active_services_b2c) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD B2C" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_international" class="control-label col-lg-3">
						{l s='DPD International:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_international" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_INTERNATIONAL, $settings->active_services_international) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD International" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="active_services_bulgaria" class="control-label col-lg-3">
						{l s='DPD Bulgaria:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="active_services_bulgaria" type="checkbox" value="1" name="{DpdGroupConfiguration::SERVICE_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::SERVICE_BULGARIA, $settings->active_services_bulgaria) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable "DPD Bulgaria" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 carriers_cod_block">
				<div class="form-group">
					<label for="is_cod_carrier_classic" class="control-label col-lg-3">
						{l s='DPD Classic + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_classic" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_CLASSIC|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_CLASSIC, $settings->is_cod_carrier_classic) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD classic" service.' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_10" class="control-label col-lg-3">
						{l s='DPD 10:00 + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_10" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_10|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_10, $settings->is_cod_carrier_10) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD 10:00" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_12" class="control-label col-lg-3">
						{l s='DPD 12:00 + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_12" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_12|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_12, $settings->is_cod_carrier_12) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD 12:00" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_same_day" class="control-label col-lg-3">
						{l s='DPD Same Day + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_same_day" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_SAME_DAY|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_SAME_DAY, $settings->is_cod_carrier_same_day) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD Same Day" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_b2c" class="control-label col-lg-3">
						{l s='DPD B2C + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_b2c" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_B2C|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_B2C, $settings->is_cod_carrier_b2c) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD B2C" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_international" class="control-label col-lg-3">
						{l s='DPD International + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_international" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_INTERNATIONAL|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_INTERNATIONAL, $settings->is_cod_carrier_international) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD International" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="is_cod_carrier_bulgaria" class="control-label col-lg-3">
						{l s='DPD Bulgaria + COD:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<div class="checkbox">
							<label>
								<input type="hidden" name="{DpdGroupConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" value="0" />
								<input id="is_cod_carrier_bulgaria" type="checkbox" value="1" name="{DpdGroupConfiguration::IS_COD_CARRIER_BULGARIA|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::IS_COD_CARRIER_BULGARIA, $settings->is_cod_carrier_bulgaria) == 1}checked="checked"{/if} />
							</label>
						</div>
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='Enable COD shipment method for "DPD Bulgaria" service' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			<br />

			<p class="alert alert-info">
				{l s='Please note that after installation carriers will be created for each service. You can manage these carriers using standar PrestaShop configuration tools.' mod='dpdgroup'}
			</p>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="default_packaging_method" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Default packaging method' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Packaging method:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="packaging_method_one_product">
							<input id="packaging_method_one_product" type="radio" value="{DpdGroupConfiguration::PACKAGE_METHOD_ONE_PRODUCT|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGroupConfiguration::PACKAGE_METHOD_ONE_PRODUCT} checked="checked"{/if} />
							{l s='One parcel for one product' mod='dpdgroup'}
						</label>
					</p>
					<p class="radio">
						<label for="packaging_method_all_products">
							<input id="packaging_method_all_products" type="radio" value="{DpdGroupConfiguration::PACKAGE_METHOD_ALL_PRODUCTS|escape:'htmlall':'UTF-8'}" name="{DpdGroupConfiguration::PACKING_METHOD|escape:'htmlall':'UTF-8'}"{if DPDGroup::getInputValue(DpdGroupConfiguration::PACKING_METHOD, $settings->packaging_method) == DpdGroupConfiguration::PACKAGE_METHOD_ALL_PRODUCTS} checked="checked"{/if} />
							{l s='One parcel for all products' mod='dpdgroup'}
						</label>
					</p>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="country_selection" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Country' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="dpd_country_select" class="control-label col-lg-3 required">
					{l s='DPD Country Select:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<select id="dpd_country_select" class="form-control fixed-width-xxl " name="{DpdGroupConfiguration::COUNTRY|escape:'htmlall':'UTF-8'}">
						<option value="">{l s='-' mod='dpdgroup'}</option>
						{foreach from=$available_countries key=iso item=country}
							<option value="{$iso|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::COUNTRY, $settings->dpd_country_select) == $iso}selected="selected"{/if}>{$country.title|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
						<option value="{DpdGroupConfiguration::OTHER_COUNTRY|escape:'htmlall':'UTF-8'}" {if DPDGroup::getInputValue(DpdGroupConfiguration::COUNTRY, $settings->dpd_country_select) == DpdGroupConfiguration::OTHER_COUNTRY}selected="selected"{/if}>{l s='Other - enter your web services URL' mod='dpdgroup'}</option>
					</select>
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Select your country from the list, or choose "Other" to enter custom web services URL' mod='dpdgroup'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="web_services" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Web Services' mod='dpdgroup'}
		</div>

		<div id="custom_web_service_container">
			<label class="required">
				{l s='You have chosen custom country. Please enter web services URLs.' mod='dpdgroup'}
			</label>

			<div class="form-wrapper">
				<div class="form-group">
					<label for="production_ws_url" class="control-label col-lg-3">
						{l s='Production WS URL:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<input id="production_ws_url" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8', $settings->ws_production_url|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8'}" size="5">
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgroup'}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="test_ws_url" class="control-label col-lg-3">
						{l s='Test WS URL:' mod='dpdgroup'}
					</label>
					<div class="col-lg-9">
						<input id="test_ws_url" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::TEST_URL|escape:'htmlall':'UTF-8', $settings->ws_test_url|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::TEST_URL|escape:'htmlall':'UTF-8'}" size="5">
					</div>
					<div class="col-lg-9 col-lg-offset-3">
						<div class="help-block">
							{l s='E.g. http://egproduction.dpd.com/IT4EMWebServices/eshop/' mod='dpdgroup'}
						</div>
					</div>
				</div>
			</div>

			<hr />
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="ws_username" class="control-label col-lg-3 required">
					{l s='Web Service Username:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<input id="ws_username" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::USERNAME|escape:'htmlall':'UTF-8', $settings->ws_username|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::USERNAME|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Enter your web services username. If you forgot your username please contact DPD Group.' mod='dpdgroup'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ws_password" class="control-label col-lg-3 required">
					{l s='Web Service Password:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<input id="ws_password" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::PASSWORD|escape:'htmlall':'UTF-8', $settings->ws_password|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Enter WebServices passoword. If you forgot your password please contact DPD Group.' mod='dpdgroup'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ws_timeout" class="control-label col-lg-3">
					{l s='Web Service Connection Timeout:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<input id="ws_timeout" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::TIMEOUT|escape:'htmlall':'UTF-8', $settings->ws_timeout|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::TIMEOUT|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Set a timeout for connecting to the DPD web service in seconds. Default is 10s. 0 - no limitl' mod='dpdgroup'}
					</div>
				</div>

				<p class="connection_message conf alert alert-success">
					{l s='Connected successfuly!' mod='dpdgroup'}
				</p>
				<p class="connection_message error alert alert-danger">
					{l s='Could not connect to a web service server. Error:' mod='dpdgroup'} <span class="error_message"></span>
				</p>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
			<button id="test_connection" class="btn btn-default pull-right" type="button">
				<i class="process-icon-refresh"></i>
				{l s='Test Connection' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="sender_payer" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Sender & Payer' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3 required">
					{l s='Sender Address Id:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<input id="sender_address" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::SENDER_ID|escape:'htmlall':'UTF-8', $settings->sender_id|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::SENDER_ID|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='You should find your sender address id in contract with DPD Group.' mod='dpdgroup'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="payer_id" class="control-label col-lg-3 required">
					{l s='Payer Id:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<input id="payer_id" class="form-control fixed-width-xxl" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::PAYER_ID|escape:'htmlall':'UTF-8', $settings->payer_id|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::PAYER_ID|escape:'htmlall':'UTF-8'}" size="5">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='You should find your payer id in contract with DPD Group' mod='dpdgroup'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>

	<div id="weight_measurement" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>
			{l s='Weight measurement units conversion' mod='dpdgroup'}
		</div>

		<div class="form-wrapper">
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3">
					{l s='System default weight units:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					{Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
				</div>
			</div>
			<div class="form-group">
				<label for="sender_address" class="control-label col-lg-3">
					{l s='DPD weight units:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					{$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
				</div>
			</div>
			<div class="form-group">
				<label for="weight_conversion_input" class="control-label col-lg-3 required">
					{l s='Conversion rate:' mod='dpdgroup'}
				</label>
				<div class="col-lg-9">
					<div class="col-sm-3">
						<div class="input-group">
							<input id="weight_conversion_input" class="form-control" type="text" value="{DPDGroup::getInputValue(DpdGroupConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}" name="{DpdGroupConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}" size="5">
							<span class="input-group-addon">
								1 {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span id="dpd_weight_unit">{DPDGroup::getInputValue(DpdGroupConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span> {$smarty.const._DPDGROUP_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
							</span>
						</div>
					</div>
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Conversion rate from system to DPD weight units. If your system uses the same weight units as DPD then leave this field blank.' mod='dpdgroup'}
					</div>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="{DpdGroupConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}" type="submit">
				<i class="process-icon-save"></i>
				{l s='Save' mod='dpdgroup'}
			</button>
		</div>
	</div>
</form>