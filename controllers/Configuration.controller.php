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

class DpdGeopostConfigurationController extends DpdGeopostController
{
	public $available_services_ids = array();
	public $countries = array();

	const SETTINGS_SAVE_ACTION 	= 'saveModuleSettings';
	const FILENAME 				= 'Configuration.controller';

	public function getSettingsPage()
	{
		$configuration = new DpdGeopostConfiguration();

		if (empty($this->countries))
			$this->setAvailableCountries();

		$this->context->smarty->assign(array(
			'saveAction' => $this->module_instance->module_url,
			'available_countries' => $this->countries,
			'settings' => $configuration
		));

		$template_filename = version_compare(_PS_VERSION_, '1.6', '>=') ? 'configuration_16' : 'configuration';

		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/'.$template_filename.'.tpl');
	}

	public function setAvailableCountries()
	{
		$this->countries = array(
			'EE' => array(
				'title' 		=> $this->l('Estonia'),
				'ws_uri_prod' 	=> 'https://integration.dpd.ee:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> 'https://integrationtest.dpd.ee:8183/IT4EMWebServices/eshop/',
				'currency'		=> 'EUR'
			),
			'LV' => array(
				'title' 		=> $this->l('Latvia'),
				'ws_uri_prod' 	=> 'https://integration.dpd.lv:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'EUR'
			),
			'LT' => array(
				'title' 		=> $this->l('Lithuania'),
				'ws_uri_prod' 	=> 'https://integration.dpd.lt:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'EUR'
			),
			'CZ' => array(
				'title' 		=> $this->l('Czech Respublic'),
				'ws_uri_prod' 	=> 'https://it4em.dpd.cz/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'CZK'
			),
			'SK' => array(
				'title' 		=> $this->l('Slovakia'),
				'ws_uri_prod' 	=> 'https://it4em.dpd.sk/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'EUR'
			),
			'HU' => array(
				'title' 		=> $this->l('Hungary'),
				'ws_uri_prod' 	=> 'http://it4em.dpd.hu/IT4EMWebServices/eshop',
				'ws_uri_test'	=> '',
				'currency'		=> 'HUF'
			),
			'SI' => array(
				'title' 		=> $this->l('Slovenia'),
				'ws_uri_prod' 	=> 'https://it4em.dpd.si/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'EUR'
			),
			'HR' => array(
				'title' 		=> $this->l('Croatia'),
				'ws_uri_prod' 	=> 'https://it4em.dpd.hr/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'HRK'
			),
			'RO' => array(
				'title' 		=> $this->l('Romania'),
				'ws_uri_prod' 	=> 'https://it4em.dpd.ro/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> '',
				'currency'		=> 'RON'
			)
		);
	}

	public static function init()
	{
		$controller = new DpdGeopostConfigurationController;

		if (Tools::isSubmit(self::SETTINGS_SAVE_ACTION))
		{
			$controller->validateSettings();

			if (!self::$errors)
				$controller->createDeleteCarriers();

			if (!self::$errors)
				$controller->saveSettings();
			else
				$controller->module_instance->outputHTML($controller->module_instance->displayError(implode('<br />', self::$errors)));
		}
	}

	private function createDeleteCarriers()
	{
		require_once(_DPDGEOPOST_CONTROLLERS_DIR_.'Service.php');

		$services = array(
			DpdGeopostConfiguration::SERVICE_CLASSIC => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_CLASSIC_ID,
				'install_message' => $this->l('Could not save DPD Classic service'),
				'delete_message' => $this->l('Could not delete DPD Classic service'),
				'service_name' => $this->l('DPD classic')
			),
			DpdGeopostConfiguration::SERVICE_10 => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_10_ID,
				'install_message' => $this->l('Could not save DPD 10:00 service'),
				'delete_message' => $this->l('Could not delete DPD 10:00 service'),
				'service_name' => $this->l('DPD 10:00')
			),
			DpdGeopostConfiguration::SERVICE_12 => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_12_ID,
				'install_message' => $this->l('Could not save DPD 12:00 service'),
				'delete_message' => $this->l('Could not delete DPD 12:00 service'),
				'service_name' => $this->l('DPD 12:00')
			),
			DpdGeopostConfiguration::SERVICE_SAME_DAY => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_SAME_DAY_ID,
				'install_message' => $this->l('Could not save DPD Same Day service'),
				'delete_message' => $this->l('Could not delete DPD Same Day service'),
				'service_name' => $this->l('DPD Same Day')
			),
			DpdGeopostConfiguration::SERVICE_B2C => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_B2C_ID,
				'install_message' => $this->l('Could not save DPD B2C service'),
				'delete_message' => $this->l('Could not delete DPD B2C service'),
				'service_name' => $this->l('DPD B2C')
			),
			DpdGeopostConfiguration::SERVICE_INTERNATIONAL => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_INTERNATIONAL_ID,
				'install_message' => $this->l('Could not save DPD International service'),
				'delete_message' => $this->l('Could not delete DPD International service'),
				'service_name' => $this->l('DPD International')
			),
			DpdGeopostConfiguration::SERVICE_BULGARIA => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_BULGARIA_ID,
				'install_message' => $this->l('Could not save DPD Bulgaria service'),
				'delete_message' => $this->l('Could not delete DPD Bulgaria service'),
				'service_name' => $this->l('DPD Bulgaria')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_CLASSIC_COD_ID,
				'install_message' => $this->l('Could not save DPD Classic + COD service'),
				'delete_message' => $this->l('Could not save DPD Classic + COD service'),
				'service_name' => $this->l('DPD classic + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_10 => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_10_COD_ID,
				'install_message' => $this->l('Could not save DPD 10:00 + COD service'),
				'delete_message' => $this->l('Could not save DPD 10:00 + COD service'),
				'service_name' => $this->l('DPD 10:00 + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_12 => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_12_COD_ID,
				'install_message' => $this->l('Could not save DPD 12:00 + COD service'),
				'delete_message' => $this->l('Could not delete DPD 12:00 + COD service'),
				'service_name' => $this->l('DPD 12:00 + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_SAME_DAY_COD_ID,
				'install_message' => $this->l('Could not save DPD Same Day + COD service'),
				'delete_message' => $this->l('Could not delete DPD Same Day + COD service'),
				'service_name' => $this->l('DPD Same Day + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_B2C => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_B2C_COD_ID,
				'install_message' => $this->l('Could not save DPD B2C + COD service'),
				'delete_message' => $this->l('Could not delete DPD B2C + COD service'),
				'service_name' => $this->l('DPD B2C + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_INTERNATIONAL_COD_ID,
				'install_message' => $this->l('Could not save DPD International + COD service'),
				'delete_message' => $this->l('Could not delete DPD International + COD service'),
				'service_name' => $this->l('DPD International + COD')
			),
			DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA => array(
				'id_service' => DpdGeopostConfiguration::CARRIER_BULGARIA_COD_ID,
				'install_message' => $this->l('Could not save DPD Bulgaria + COD service'),
				'delete_message' => $this->l('Could not delete DPD Bulgaria + COD service'),
				'service_name' => $this->l('DPD Bulgaria + COD')
			)
		);

		foreach ($services as $type => $data)
		{
			if (Tools::getValue($type))
			{
				if (!DpdGeopostService::install($data['id_service'], $data['service_name']))
					self::$errors[] = $data['install_message'];
			}
			else
				if (!DpdGeopostService::deleteCarrier($data['id_service']))
					self::$errors[] = $data['delete_message'];
		}
	}

	private function validateSettings()
	{
		if (!Tools::getValue(DpdGeopostConfiguration::COUNTRY))
			self::$errors[] = $this->l('DPD Country can not be empty');

		if (!Tools::getValue(DpdGeopostConfiguration::SENDER_ID))
			self::$errors[] = $this->l('Sender Address Id can not be empty');

		if (!Tools::getValue(DpdGeopostConfiguration::PAYER_ID))
			self::$errors[] = $this->l('Payer Id can not be empty');

		if (!Tools::getValue(DpdGeopostConfiguration::USERNAME))
			self::$errors[] = $this->l('Web Service Username can not be empty');

		if (!Tools::getValue(DpdGeopostConfiguration::PASSWORD))
			self::$errors[] = $this->l('Web Service Password can not be empty');

		if (Tools::getValue(DpdGeopostConfiguration::COUNTRY) == DpdGeopostConfiguration::OTHER_COUNTRY &&
			!Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL) &&
			!Tools::getValue(DpdGeopostConfiguration::TEST_URL))
			self::$errors[] = $this->l('At least one WS URL must be entered');

		if (Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL) !== '' && !Validate::isUrl(Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL)))
			self::$errors[] = $this->l('Production WS URL is not valid');

		if (Tools::getValue(DpdGeopostConfiguration::TEST_URL) !== '' && !Validate::isUrl(Tools::getValue(DpdGeopostConfiguration::TEST_URL)))
			self::$errors[] = $this->l('Test WS URL is not valid');

		if (Tools::getValue(DpdGeopostConfiguration::PASSWORD) !== '' && !Validate::isPasswd(Tools::getValue(DpdGeopostConfiguration::PASSWORD)))
			self::$errors[] = $this->l('Web Service Password is not valid');

		if (Tools::getValue(DpdGeopostConfiguration::TIMEOUT) !== '' && !Validate::isUnsignedInt(Tools::getValue(DpdGeopostConfiguration::TIMEOUT)))
			self::$errors[] = $this->l('Web Service Connection Timeout is not valid');

		if (!Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE))
			self::$errors[] = $this->l('Weight conversation rate can not be empty');
		elseif (!Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) ||
			Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) &&
			Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE) < 0)
			self::$errors[] = $this->l('Weight conversation rate is not valid');

		$this->validateCODMethods();
	}

	private function validateCODMethods()
	{
		$payment_module_selected = false;
		foreach (DpdGeopost::getPaymentModules() as $payment_module)
		{
			if (Tools::isSubmit($payment_module['name']))
			{
				$payment_module_selected = true;
				break;
			}
		}

		if (!$payment_module_selected)
		{
			if (Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_10) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_12) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_B2C) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL) ||
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_BULGARIA))
				self::$errors[] = $this->l('COD payment method must be selected to enable COD services');
		}
	}

	private function saveSettings()
	{
		if (DpdGeopostConfiguration::saveConfiguration())
		{
			DpdGeopost::addFlashMessage($this->l('Settings saved successfully'));
			Tools::redirectAdmin($this->module_instance->module_url.'&menu=configuration');
		}
		else
			DpdGeopost::addFlashError($this->l('Could not save settings'));
	}

	public function testConnection($production_mode, $ws_production_url, $ws_test_url)
	{
		$wsdl_url = $production_mode ? $ws_production_url : $ws_test_url;

		if (!$wsdl_url || !Validate::isUrl($wsdl_url))
			return $this->l('Webservice URL is not valid');
		try
		{
			new SoapClient($wsdl_url.'PickupOrderServiceImpl?wsdl', array('trace' => true));
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}

		return null;
	}

	public function validateCountryConfiguration($ws_country)
	{
		if (empty($this->countries))
			$this->setAvailableCountries();

		if (!isset($this->countries[$ws_country]))
			return $this->l('Country does not exist');
		elseif (!isset($this->countries[$ws_country]['ws_uri_prod']) || !isset($this->countries[$ws_country]['ws_uri_test']))
			return $this->l('Country does not have production / Test URL(s)');
		elseif (!$this->countries[$ws_country]['ws_uri_prod'] || !$this->countries[$ws_country]['ws_uri_test'])
			return $this->l('Country production / Test URL(s) is(are) empty');
		elseif (!Validate::isUrl($this->countries[$ws_country]['ws_uri_prod']) ||
			!Validate::isUrl($this->countries[$ws_country]['ws_uri_test']))
			return $this->l('Country production / Test URL(s) is(are) empty');

		return null;
	}
}