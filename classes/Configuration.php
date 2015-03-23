<?php
/**
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
 */

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostConfiguration extends DpdGeopostObjectModel
{
	const DEBUG_MODE	 							= 'DPD_GEOPOST_DEBUG_MODE';
	const PRODUCTION_MODE 							= 'DPD_GEOPOST_PRODUCTION_MODE';
	const ADDRESS_VALIDATION 						= 'DPD_GEOPOST_ADDRESS_VALIDATION';
	const SERVICE_CLASSIC 							= 'DPD_GEOPOST_SERVICE_CLASSIC';
	const SERVICE_10 								= 'DPD_GEOPOST_SERVICE_10';
	const SERVICE_12 								= 'DPD_GEOPOST_SERVICE_12';
	const SERVICE_SAME_DAY 							= 'DPD_GEOPOST_SERVICE_SAME_DAY';
	const SERVICE_B2C 								= 'DPD_GEOPOST_SERVICE_B2C';
	const SERVICE_INTERNATIONAL						= 'DPD_GEOPOST_SERVICE_INTERNATION';
	const SERVICE_BULGARIA							= 'DPD_GEOPOST_SERVICE_BULGARIA';
	const PACKING_METHOD 							= 'DPD_GEOPOST_PACKING_METHOD';
	const COUNTRY 									= 'DPD_GEOPOST_COUNTRY';
	const PRODUCTION_URL 							= 'DPD_GEOPOST_PRODUCTION_URL';
	const TEST_URL 									= 'DPD_GEOPOST_TEST_URL';
	const USERNAME 									= 'DPD_GEOPOST_USERNAME';
	const PASSWORD 									= 'DPD_GEOPOST_PASSWORD';
	const TIMEOUT 									= 'DPD_GEOPOST_TIMEOUT';
	const SENDER_ID 								= 'DPD_GEOPOST_SENDER_ID';
	const PAYER_ID 									= 'DPD_GEOPOST_PAYER_ID';
	const WEIGHT_CONVERSATION_RATE					= 'DPD_GEOPOST_WEIGHT_RATE';
	const PRICE_CALCULATION							= 'DPD_GEOPOST_PRICE_CALCULATION';

	const CARRIER_CLASSIC_ID						= 'DPD_GEOPOST_CARRIER_CLASSIC_ID';
	const CARRIER_10_ID								= 'DPD_GEOPOST_CARRIER_10_ID';
	const CARRIER_12_ID								= 'DPD_GEOPOST_CARRIER_12_ID';
	const CARRIER_SAME_DAY_ID						= 'DPD_GEOPOST_CARRIER_SAME_DAY_ID';
	const CARRIER_B2C_ID							= 'DPD_GEOPOST_CARRIER_B2C_ID';
	const CARRIER_INTERNATIONAL_ID					= 'DPD_GEOPOST_CARRIER_INTERNAT_ID';
	const CARRIER_BULGARIA_ID						= 'DPD_GEOPOST_CARRIER_BULGARIA_ID';

	const CARRIER_CLASSIC_COD_ID					= 'DPD_GEOPOST_COD_CLASSIC_ID';
	const CARRIER_10_COD_ID							= 'DPD_GEOPOST_COD_10_ID';
	const CARRIER_12_COD_ID							= 'DPD_GEOPOST_COD_12_ID';
	const CARRIER_SAME_DAY_COD_ID					= 'DPD_GEOPOST_COD_SAME_DAY_ID';
	const CARRIER_B2C_COD_ID						= 'DPD_GEOPOST_COD_B2C_ID';
	const CARRIER_INTERNATIONAL_COD_ID				= 'DPD_GEOPOST_COD_INTERNATIONAL_ID';
	const CARRIER_BULGARIA_COD_ID					= 'DPD_GEOPOST_COD_BULGARIA_ID';

	const IS_COD_CARRIER_CLASSIC					= 'DPD_GEOPOST_IS_COD_CLASSIC';
	const IS_COD_CARRIER_10							= 'DPD_GEOPOST_IS_COD_10';
	const IS_COD_CARRIER_12							= 'DPD_GEOPOST_IS_COD_12';
	const IS_COD_CARRIER_SAME_DAY					= 'DPD_GEOPOST_IS_COD_SAME_DAY';
	const IS_COD_CARRIER_B2C						= 'DPD_GEOPOST_IS_COD_B2C';
	const IS_COD_CARRIER_INTERNATIONAL				= 'DPD_GEOPOST_IS_COD_INTERNATIONAL';
	const IS_COD_CARRIER_BULGARIA					= 'DPD_GEOPOST_IS_COD_BULGARIA';

	const OTHER_COUNTRY 							= 'other';
	const PACKAGE_METHOD_ONE_PRODUCT				= 'one_product';
	const PACKAGE_METHOD_ALL_PRODUCTS				= 'all_products';

	const PRICE_CALCULATION_WEB_SERVICES			= 'webservices';
	const PRICE_CALCULATION_PRESTASHOP				= 'prestashop';
	const PRICE_CALCULATION_CSV						= 'csv';

	const MEASUREMENT_ROUND_VALUE					= 6;
	const COD_MODULE								= 'DPD_GEOPOST_COD_MODULE';

	const COD_PERCENTAGE_CALCULATION				= 'DPD_GEOPOST_COD_CALCULATION';
	const COD_PERCENTAGE_CALCULATION_CART			= 'DPD_GEOPOST_CALCULATION_CART';
	const COD_PERCENTAGE_CALCULATION_CART_SHIPPING	= 'DPD_GEOPOST_CALCULATION_SHIPPING';

	public $debug_mode						= 0;
	public $production_mode					= 0;
	public $address_validation				= 0;
	public $active_services_classic			= 0;
	public $active_services_10				= 0;
	public $active_services_12				= 0;
	public $active_services_same_day 		= 0;
	public $active_services_b2c 			= 0;
	public $active_services_international	= 0;
	public $active_services_bulgaria 		= 0;
	public $packaging_method				= self::PACKAGE_METHOD_ONE_PRODUCT;
	public $dpd_country_select				= '';
	public $ws_production_url				= '';
	public $ws_test_url						= '';
	public $ws_username						= '';
	public $ws_password						= '';
	public $ws_timeout						= 10;
	public $sender_id						= '';
	public $payer_id						= '';
	public $weight_conversation_rate		= 1;
	public $price_calculation_method		= self::PRICE_CALCULATION_PRESTASHOP;

	public $is_cod_carrier_classic			= 0;
	public $is_cod_carrier_10				= 0;
	public $is_cod_carrier_12				= 0;
	public $is_cod_carrier_same_day			= 0;
	public $is_cod_carrier_b2c				= 0;
	public $is_cod_carrier_international	= 0;
	public $is_cod_carrier_bulgaria			= 0;

	public $cod_percentage_calculation		= self::COD_PERCENTAGE_CALCULATION_CART;

	public $settings_data = array();

	public function __construct()
	{
		$this->settings_data = self::getSettingsData();
		$this->getSettings();
	}

	public static function saveConfiguration()
	{
		$settings_data = self::getSettingsData();
		$success = true;

		foreach (array_keys($settings_data) as $name)
			$success &= Configuration::updateValue($name, Tools::getValue($name));

		$payment_module_selected = '';

		foreach (DpdGeopost::getPaymentModules() as $payment_module)
		{
			if (Tools::getValue($payment_module['name']))
				$payment_module_selected = $payment_module['name'];
		}

		$success &= Configuration::updateValue(self::COD_MODULE, $payment_module_selected);

		return $success;
	}

	private static function getSettingsData()
	{
		return array(
			self::DEBUG_MODE => 'debug_mode',
			self::PRODUCTION_MODE => 'production_mode',
			self::ADDRESS_VALIDATION => 'address_validation',
			self::SERVICE_CLASSIC => 'active_services_classic',
			self::SERVICE_10 => 'active_services_10',
			self::SERVICE_12 => 'active_services_12',
			self::SERVICE_SAME_DAY => 'active_services_same_day',
			self::SERVICE_B2C => 'active_services_b2c',
			self::SERVICE_INTERNATIONAL => 'active_services_international',
			self::SERVICE_BULGARIA => 'active_services_bulgaria',
			self::PACKING_METHOD => 'packaging_method',
			self::COUNTRY => 'dpd_country_select',
			self::PRODUCTION_URL => 'ws_production_url',
			self::TEST_URL => 'ws_test_url',
			self::USERNAME => 'ws_username',
			self::PASSWORD =>'ws_password',
			self::TIMEOUT => 'ws_timeout',
			self::SENDER_ID => 'sender_id',
			self::PAYER_ID => 'payer_id',
			self::WEIGHT_CONVERSATION_RATE => 'weight_conversation_rate',
			self::PRICE_CALCULATION => 'price_calculation_method',
			self::IS_COD_CARRIER_CLASSIC => 'is_cod_carrier_classic',
			self::IS_COD_CARRIER_10 => 'is_cod_carrier_10',
			self::IS_COD_CARRIER_12 => 'is_cod_carrier_12',
			self::IS_COD_CARRIER_SAME_DAY => 'is_cod_carrier_same_day',
			self::IS_COD_CARRIER_B2C => 'is_cod_carrier_b2c',
			self::IS_COD_CARRIER_INTERNATIONAL => 'is_cod_carrier_international',
			self::IS_COD_CARRIER_BULGARIA => 'is_cod_carrier_bulgaria',
			self::COD_PERCENTAGE_CALCULATION => 'cod_percentage_calculation'
		);
	}

	private function getSettings()
	{
		$settings_data = self::getSettingsData();

		foreach ($settings_data as $name => $setting)
			$this->$setting = $this->getSetting($name, $this->$setting);

		$carriers_ids = Configuration::getMultiple(array(
			self::CARRIER_10_ID,
			self::CARRIER_12_ID,
			self::CARRIER_CLASSIC_ID,
			self::CARRIER_SAME_DAY_ID,
			self::CARRIER_B2C_ID,
			self::CARRIER_INTERNATIONAL_ID,
			self::CARRIER_BULGARIA_ID,
			self::CARRIER_10_COD_ID,
			self::CARRIER_12_COD_ID,
			self::CARRIER_CLASSIC_COD_ID,
			self::CARRIER_SAME_DAY_COD_ID,
			self::CARRIER_B2C_COD_ID,
			self::CARRIER_INTERNATIONAL_COD_ID,
			self::CARRIER_BULGARIA_COD_ID
		));

		$ps_14 = version_compare(_PS_VERSION_, '1.5', '<');

		$all_dpd_carriers = array(
			array('id_carrier' => isset($carriers_ids[self::CARRIER_10_ID]) ? $carriers_ids[self::CARRIER_10_ID] : '',
				'id_service' => 'active_services_10'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_12_ID]) ? $carriers_ids[self::CARRIER_12_ID] : '',
				'id_service' => 'active_services_12'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_CLASSIC_ID]) ? $carriers_ids[self::CARRIER_CLASSIC_ID] : '',
				'id_service' => 'active_services_classic'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_SAME_DAY_ID]) ? $carriers_ids[self::CARRIER_SAME_DAY_ID] : '',
				'id_service' => 'active_services_same_day'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_B2C_ID]) ? $carriers_ids[self::CARRIER_B2C_ID] : '',
				'id_service' => 'active_services_b2c'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_INTERNATIONAL_ID]) ? $carriers_ids[self::CARRIER_INTERNATIONAL_ID] : '',
				'id_service' => 'active_services_international'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_BULGARIA_ID]) ? $carriers_ids[self::CARRIER_BULGARIA_ID] : '',
				'id_service' => 'active_services_bulgaria'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_10_COD_ID]) ? $carriers_ids[self::CARRIER_10_COD_ID] : '',
				'id_service' => 'is_cod_carrier_10'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_12_COD_ID]) ? $carriers_ids[self::CARRIER_12_COD_ID] : '',
				'id_service' => 'is_cod_carrier_12'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_CLASSIC_COD_ID]) ? $carriers_ids[self::CARRIER_CLASSIC_COD_ID] : '',
				'id_service' => 'is_cod_carrier_classic'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_SAME_DAY_COD_ID]) ? $carriers_ids[self::CARRIER_SAME_DAY_COD_ID] : '',
				'id_service' => 'is_cod_carrier_same_day'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_B2C_COD_ID]) ? $carriers_ids[self::CARRIER_B2C_COD_ID] : '',
				'id_service' => 'is_cod_b2c'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_INTERNATIONAL_COD_ID]) ? $carriers_ids[self::CARRIER_INTERNATIONAL_COD_ID] : '',
				'id_service' => 'is_cod_international'),
			array('id_carrier' => isset($carriers_ids[self::CARRIER_BULGARIA_COD_ID]) ? $carriers_ids[self::CARRIER_BULGARIA_COD_ID] : '',
				'id_service' => 'is_cod_bulgaria')
		);

		foreach ($all_dpd_carriers as $dpd_carrier)
		{
			if ($dpd_carrier['id_carrier'])
			{
				if ($ps_14)
				{
					$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$dpd_carrier['id_carrier']);
					$carrier = new Carrier((int)$id_carrier);
				}
				else
					$carrier = Carrier::getCarrierByReference((int)$dpd_carrier['id_carrier']);

				if (Validate::isLoadedObject($carrier))
					$this->$dpd_carrier['id_service'] = ($carrier->active && !$carrier->deleted) ? 1 : 0;
				else
					$this->$dpd_carrier['id_service'] = 0;
			}
		}
	}

	private function getSetting($name, $default_value)
	{
		return Configuration::get($name) !== false ? Configuration::get($name) : $default_value;
	}

	public static function deleteConfiguration()
	{
		$settings_data = self::getSettingsData();

		foreach (array_keys($settings_data) as $name)
			Configuration::deleteByName($name);

		$settings_data = array(
			self::CARRIER_CLASSIC_ID,
			self::CARRIER_10_ID,
			self::CARRIER_12_ID,
			self::CARRIER_SAME_DAY_ID,
			self::CARRIER_B2C_ID,
			self::CARRIER_INTERNATIONAL_ID,
			self::CARRIER_BULGARIA_ID,
			self::CARRIER_CLASSIC_COD_ID,
			self::CARRIER_10_COD_ID,
			self::CARRIER_12_COD_ID,
			self::CARRIER_SAME_DAY_COD_ID,
			self::CARRIER_B2C_COD_ID,
			self::CARRIER_INTERNATIONAL_COD_ID,
			self::CARRIER_BULGARIA_COD_ID,
			self::COD_MODULE
		);

		foreach ($settings_data as $name)
			Configuration::deleteByName($name);

		return true;
	}

	public function checkRequiredFields()
	{
		if (!$this->dpd_country_select ||
			!$this->sender_id ||
			!$this->payer_id ||
			!$this->ws_username ||
			!$this->ws_password ||
			($this->dpd_country_select == self::OTHER_COUNTRY && !$this->ws_production_url && !$this->ws_test_url))
			return false;

		return true;
	}
}