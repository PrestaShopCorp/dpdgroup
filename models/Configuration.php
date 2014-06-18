<?php
/**
* 2014 Apple Inc.
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
*  @copyright 2014 DPD Polska sp. z o.o. 
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska sp. z o.o. 
*/

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostConfiguration extends DpdGeopostObjectModel
{
	const PRODUCTION_MODE 							= 'DPD_GEOPOST_PRODUCTION_MODE';
	const ADDRESS_VALIDATION 						= 'DPD_GEOPOST_ADDRESS_VALIDATION';
	const SERVICE_CLASSIC 							= 'DPD_GEOPOST_SERVICE_CLASSIC';
	const SERVICE_10 								= 'DPD_GEOPOST_SERVICE_10';
	const SERVICE_12 								= 'DPD_GEOPOST_SERVICE_12';
	const SERVICE_SAME_DAY 							= 'DPD_GEOPOST_SERVICE_SAME_DAY';
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
	
	const CARRIER_CLASSIC_COD_ID					= 'DPD_GEOPOST_COD_CLASSIC_ID';
	const CARRIER_10_COD_ID							= 'DPD_GEOPOST_COD_10_ID';
	const CARRIER_12_COD_ID							= 'DPD_GEOPOST_COD_12_ID';
	const CARRIER_SAME_DAY_COD_ID					= 'DPD_GEOPOST_COD_SAME_DAY_ID';
	
	const IS_COD_CARRIER_CLASSIC					= 'DPD_GEOPOST_IS_COD_CLASSIC';
	const IS_COD_CARRIER_10							= 'DPD_GEOPOST_IS_COD_10';
	const IS_COD_CARRIER_12							= 'DPD_GEOPOST_IS_COD_12';
	const IS_COD_CARRIER_SAME_DAY					= 'DPD_GEOPOST_IS_COD_SAME_DAY';

	const OTHER 									= 'other';
	const ONE_PRODUCT								= 'one_product';
	const ALL_PRODUCTS								= 'all_products';
	
	const WEB_SERVICES								= 'webservices';
	const PRESTASHOP								= 'prestashop';
	const CSV										= 'csv';
	
	const FILE_NAME 								= 'Configuration';
	const MEASUREMENT_ROUND_VALUE					= 6;
	const COD_MODULE								= 'DPD_GEOPOST_COD_MODULE';
	
	const COD_PERCENTAGE_CALCULATION				= 'DPD_GEOPOST_COD_CALCULATION';
	const COD_PERCENTAGE_CALCULATION_CART			= 'DPD_GEOPOST_CALCULATION_CART';
	const COD_PERCENTAGE_CALCULATION_CART_SHIPPING	= 'DPD_GEOPOST_CALCULATION_SHIPPING';
	
	public $production_mode					= 0;
	public $address_validation				= 0;
	public $active_services_classic			= 0;
	public $active_services_10				= 0;
	public $active_services_12				= 0;
	public $active_services_same_day 		= 0;
	public $packaging_method				= self::ONE_PRODUCT;
	public $dpd_country_select				= '';
	public $ws_production_url				= '';
	public $ws_test_url						= '';
	public $ws_username						= '';
	public $ws_password						= '';
	public $ws_timeout						= 10;
	public $sender_id						= '';
	public $payer_id						= '';
	public $weight_conversation_rate		= 1;
	public $price_calculation_method		= self::PRESTASHOP;
	
	public $is_cod_carrier_classic			= 0;
	public $is_cod_carrier_10				= 0;
	public $is_cod_carrier_12				= 0;
	public $is_cod_carrier_same_day			= 0;
	
	public $cod_percentage_calculation		= self::COD_PERCENTAGE_CALCULATION_CART;
	
	public $countries 						= array();

	public $module_instance;
	
	public function __construct()
	{
		$this->module_instance = Module::getInstanceByName('dpdgeopost');
		$this->getSettings();
		$this->setAvailableCountries();
	}

	public static function saveConfiguration()
	{
		$success = true;
		
		$success &= Configuration::updateValue(self::PRODUCTION_MODE, 						(int)Tools::getValue(self::PRODUCTION_MODE));
		$success &= Configuration::updateValue(self::ADDRESS_VALIDATION, 					(int)Tools::getValue(self::ADDRESS_VALIDATION));
		$success &= Configuration::updateValue(self::SERVICE_CLASSIC, 						(int)Tools::getValue(self::SERVICE_CLASSIC));
		$success &= Configuration::updateValue(self::SERVICE_10, 							(int)Tools::getValue(self::SERVICE_10));
		$success &= Configuration::updateValue(self::SERVICE_12, 							(int)Tools::getValue(self::SERVICE_12));
		$success &= Configuration::updateValue(self::SERVICE_SAME_DAY, 						(int)Tools::getValue(self::SERVICE_SAME_DAY));
		$success &= Configuration::updateValue(self::PACKING_METHOD, 						Tools::getValue(self::PACKING_METHOD));
		$success &= Configuration::updateValue(self::COUNTRY, 								Tools::getValue(self::COUNTRY));
		$success &= Configuration::updateValue(self::USERNAME, 								Tools::getValue(self::USERNAME));
		$success &= Configuration::updateValue(self::PASSWORD, 								Tools::getValue(self::PASSWORD));
		$success &= Configuration::updateValue(self::TIMEOUT, 								(int)Tools::getValue(self::TIMEOUT));
		$success &= Configuration::updateValue(self::SENDER_ID, 							Tools::getValue(self::SENDER_ID));
		$success &= Configuration::updateValue(self::PAYER_ID, 								Tools::getValue(self::PAYER_ID));
		$success &= Configuration::updateValue(self::WEIGHT_CONVERSATION_RATE,				(float)Tools::getValue(self::WEIGHT_CONVERSATION_RATE));
		$success &= Configuration::updateValue(self::PRICE_CALCULATION,						Tools::getValue(self::PRICE_CALCULATION));
		
		$success &= Configuration::updateValue(self::COD_PERCENTAGE_CALCULATION,			Tools::getValue(self::COD_PERCENTAGE_CALCULATION));
		
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_CLASSIC,				(int)Tools::isSubmit(self::IS_COD_CARRIER_CLASSIC));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_10,						(int)Tools::isSubmit(self::IS_COD_CARRIER_10));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_12,						(int)Tools::isSubmit(self::IS_COD_CARRIER_12));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_SAME_DAY,				(int)Tools::isSubmit(self::IS_COD_CARRIER_SAME_DAY));
		
		if (Tools::getValue(self::COUNTRY) == self::OTHER)
		{
			$success &= Configuration::updateValue(self::PRODUCTION_URL, 					Tools::getValue(self::PRODUCTION_URL));
			$success &= Configuration::updateValue(self::TEST_URL, 							Tools::getValue(self::TEST_URL));
		}
		
		$payment_module_selected = '';
		foreach (DpdGeopost::getPaymentModules() as $payment_module)
		{
			if (Tools::isSubmit($payment_module['name']))
				$payment_module_selected = pSQL($payment_module['name']);
		}
		
		$success &= Configuration::updateValue(self::COD_MODULE,				$payment_module_selected);
		
		return $success;
	}
	
	private function getSettings()
	{
		$this->production_mode					= $this->getSetting(self::PRODUCTION_MODE, 						$this->production_mode);
		$this->address_validation				= $this->getSetting(self::ADDRESS_VALIDATION, 					$this->address_validation);
		$this->active_services_classic			= $this->getSetting(self::SERVICE_CLASSIC, 						$this->active_services_classic);
		$this->active_services_10				= $this->getSetting(self::SERVICE_10, 							$this->active_services_10);
		$this->active_services_12				= $this->getSetting(self::SERVICE_12, 							$this->active_services_12);
		$this->active_services_same_day			= $this->getSetting(self::SERVICE_SAME_DAY, 					$this->active_services_same_day);
		$this->packaging_method					= $this->getSetting(self::PACKING_METHOD, 						$this->packaging_method);
		$this->dpd_country_select				= $this->getSetting(self::COUNTRY, 								$this->dpd_country_select);
		$this->ws_production_url				= $this->getSetting(self::PRODUCTION_URL, 						$this->ws_production_url);
		$this->ws_test_url						= $this->getSetting(self::TEST_URL, 							$this->ws_test_url);
		$this->ws_username						= $this->getSetting(self::USERNAME, 							$this->ws_username);
		$this->ws_password						= $this->getSetting(self::PASSWORD, 							$this->ws_password);
		$this->ws_timeout						= $this->getSetting(self::TIMEOUT, 								$this->ws_timeout);
		$this->sender_id						= $this->getSetting(self::SENDER_ID, 							$this->sender_id);
		$this->payer_id							= $this->getSetting(self::PAYER_ID, 							$this->payer_id);
		$this->weight_conversation_rate			= $this->getSetting(self::WEIGHT_CONVERSATION_RATE,				$this->weight_conversation_rate);
		$this->price_calculation_method			= $this->getSetting(self::PRICE_CALCULATION, 					$this->price_calculation_method);
		$this->is_cod_carrier_classic			= $this->getSetting(self::IS_COD_CARRIER_CLASSIC, 				$this->is_cod_carrier_classic);
		$this->is_cod_carrier_10				= $this->getSetting(self::IS_COD_CARRIER_10, 					$this->is_cod_carrier_10);
		$this->is_cod_carrier_12				= $this->getSetting(self::IS_COD_CARRIER_12, 					$this->is_cod_carrier_12);
		$this->is_cod_carrier_same_day			= $this->getSetting(self::IS_COD_CARRIER_SAME_DAY, 				$this->is_cod_carrier_same_day);
		
		$this->cod_percentage_calculation		= $this->getSetting(self::COD_PERCENTAGE_CALCULATION, 			$this->cod_percentage_calculation);
		
		$carrier_10_id 							= (int)Configuration::get(self::CARRIER_10_ID);
		$carrier_12_id 							= (int)Configuration::get(self::CARRIER_12_ID);
		$carrier_classic_id 					= (int)Configuration::get(self::CARRIER_CLASSIC_ID);
		$carrier_same_day_id 					= (int)Configuration::get(self::CARRIER_SAME_DAY_ID);
		
		$carrier_10_cod_id						= (int)Configuration::get(self::CARRIER_10_COD_ID);
		$carrier_12_cod_id						= (int)Configuration::get(self::CARRIER_12_COD_ID);
		$carrier_classic_cod_id					= (int)Configuration::get(self::CARRIER_CLASSIC_COD_ID);
		$carrier_same_day_cod_id				= (int)Configuration::get(self::CARRIER_SAME_DAY_COD_ID);
		
		if ($carrier_10_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_10_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_10_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->active_services_10 = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->active_services_10 = 0;
		}
		
		if ($carrier_12_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_12_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_12_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->active_services_12 = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->active_services_12 = 0;
		}
		
		if ($carrier_classic_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_classic_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->active_services_classic = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->active_services_classic = 0;
		}
		
		if ($carrier_same_day_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_same_day_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_same_day_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->active_services_same_day = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->active_services_same_day = 0;
		}
		
		if ($carrier_10_cod_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_10_cod_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_10_cod_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->is_cod_carrier_10 = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->is_cod_carrier_10 = 0;
		}
		
		if ($carrier_12_cod_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_12_cod_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_12_cod_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->is_cod_carrier_12 = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->is_cod_carrier_12 = 0;
		}
		
		if ($carrier_classic_cod_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_cod_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_classic_cod_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->is_cod_carrier_classic = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->is_cod_carrier_classic = 0;
		}
		
		if ($carrier_same_day_cod_id)
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_same_day_cod_id);
				$carrier = new Carrier((int)$id_carrier);
			}
			else
				$carrier = Carrier::getCarrierByReference((int)$carrier_same_day_cod_id);
			
			if (Validate::isLoadedObject($carrier))
				$this->is_cod_carrier_same_day = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			else
				$this->is_cod_carrier_same_day = 0;
		}
	}
	
	private function getSetting($name, $default_value)
	{
		return Configuration::get($name) !== false ? Configuration::get($name) : $default_value;
	}

	private function setAvailableCountries()
	{
		$this->countries = array(
			'EE' => array(
				'title' 		=> $this->module_instance->l('Estonia', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.ee:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> 'https://integrationtest.dpd.ee:8183/IT4EMWebServices/eshop/'
			),
			'LV' => array(
				'title' 		=> $this->module_instance->l('Latvia', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.lv:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> ''
			),
			'LT' => array(
				'title' 		=> $this->module_instance->l('Lithuania', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.lt:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> ''
			),
			'PL' => array(
				'title' 		=> $this->module_instance->l('Poland', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'CS' => array(
				'title' 		=> $this->module_instance->l('Czech Respublic', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'SK' => array(
				'title' 		=> $this->module_instance->l('Slovakia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'HU' => array(
				'title' 		=> $this->module_instance->l('Hungary', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'SI' => array(
				'title' 		=> $this->module_instance->l('Slovenia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'HR' => array(
				'title' 		=> $this->module_instance->l('Croatia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'RO' => array(
				'title' 		=> $this->module_instance->l('Romania', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'BG' => array(
				'title' 		=> $this->module_instance->l('Bulgaria', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			)
		);
	}
	
	public static function deleteConfiguration()
	{
		Configuration::deleteByName(self::PRODUCTION_MODE);
		Configuration::deleteByName(self::ADDRESS_VALIDATION);
		Configuration::deleteByName(self::SERVICE_CLASSIC);
		Configuration::deleteByName(self::SERVICE_10);
		Configuration::deleteByName(self::SERVICE_12);
		Configuration::deleteByName(self::SERVICE_SAME_DAY);
		Configuration::deleteByName(self::PACKING_METHOD);
		Configuration::deleteByName(self::COUNTRY);
		Configuration::deleteByName(self::USERNAME);
		Configuration::deleteByName(self::PASSWORD);
		Configuration::deleteByName(self::TIMEOUT);
		Configuration::deleteByName(self::SENDER_ID);
		Configuration::deleteByName(self::PAYER_ID);
		Configuration::deleteByName(self::PRODUCTION_URL);
		Configuration::deleteByName(self::TEST_URL);
		Configuration::deleteByName(self::WEIGHT_CONVERSATION_RATE);
		Configuration::deleteByName(self::PRICE_CALCULATION);
		Configuration::deleteByName(self::CARRIER_CLASSIC_ID);
		Configuration::deleteByName(self::CARRIER_10_ID);
		Configuration::deleteByName(self::CARRIER_12_ID);
		Configuration::deleteByName(self::CARRIER_SAME_DAY_ID);
		Configuration::deleteByName(self::IS_COD_CARRIER_CLASSIC);
		Configuration::deleteByName(self::IS_COD_CARRIER_10);
		Configuration::deleteByName(self::IS_COD_CARRIER_12);
		Configuration::deleteByName(self::IS_COD_CARRIER_SAME_DAY);
		Configuration::deleteByName(self::CARRIER_CLASSIC_COD_ID);
		Configuration::deleteByName(self::CARRIER_10_COD_ID);
		Configuration::deleteByName(self::CARRIER_12_COD_ID);
		Configuration::deleteByName(self::CARRIER_SAME_DAY_COD_ID);
		Configuration::deleteByName(self::COD_MODULE);

		return true;
	}
	
	public function checkRequiredFields()
	{
		if (!$this->dpd_country_select ||
			!$this->sender_id ||
			!$this->payer_id ||
			!$this->ws_username ||
			!$this->ws_password ||
			($this->dpd_country_select == self::OTHER && !$this->ws_production_url && !$this->ws_test_url)
		)
			return false;
		
		return true;	
	}
}