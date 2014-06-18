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
	
class DpdGeopostConfigurationController extends DpdGeopostController
{
	public  $available_services_ids = array();
	
	const SETTINGS_SAVE_ACTION 	= 'saveModuleSettings';
	const FILENAME 				= 'configuration.controller';
	
	public function getSettingsPage()
	{
		$configuration = new DpdGeopostConfiguration();
		
		$this->context->smarty->assign(array(
			'saveAction' => $this->module_instance->module_url,
			'available_countries' => $configuration->countries,
			'settings' => $configuration
		));
		
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/configuration.tpl');
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
				$controller->module_instance->outputHTML($controller->module_instance->displayErrors(self::$errors));
		}
		
		$configuration = new DpdGeopostConfiguration();

		if (!$configuration->checkRequiredFields())
			$controller->module_instance->outputHTML($controller->module_instance->displayWarnings(array($controller->l('Module is not fully configured yet.'))));
	}
	
	private function createDeleteCarriers()
	{
		require_once(_DPDGEOPOST_CLASSES_DIR_.'service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_classic.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_10.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_12.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_same_day.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_classic_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_10_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_12_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_same_day_cod.service.php');
		
		
		if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC))
		{
			if (!DpdGeopostCarrierClassicService::install())
				self::$errors[] = $this->l('Could not save DPD Classic service');
		}
		else
			if (!DpdGeopostCarrierClassicService::delete())
				self::$errors[] = $this->l('Could not delete DPD Classic service');

		if (Tools::getValue(DpdGeopostConfiguration::SERVICE_10))
		{
			if (!DpdGeopostCarrier10Service::install())
				self::$errors[] = $this->l('Could not save DPD 10:00 service');
		}
		else
			if (!DpdGeopostCarrier10Service::delete())
				self::$errors[] = $this->l('Could not delete DPD 10:00 service');
		
		if (Tools::getValue(DpdGeopostConfiguration::SERVICE_12))
		{
			if (!DpdGeopostCarrier12Service::install())
				self::$errors[] = $this->l('Could not save DPD 12:00 service');
		}
		else
			if (!DpdGeopostCarrier12Service::delete())
				self::$errors[] = $this->l('Could not delete DPD 12:00 service');
		
		if (Tools::getValue(DpdGeopostConfiguration::SERVICE_SAME_DAY))
		{
			if (!DpdGeopostCarrierSameDayService::install())
				self::$errors[] = $this->l('Could not save DPD Same Day service');
		}
		else
			if (!DpdGeopostCarrierSameDayService::delete())
				self::$errors[] = $this->l('Could not delete DPD Same Day service');
		
		if (Tools::getValue(DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC))
		{
			require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_classic_cod.service.php');
			if (!DpdGeopostCarrierClassicCODService::install())
				self::$errors[] = $this->l('Could not save DPD Classic + COD service');
		}
		else
			if (!DpdGeopostCarrierClassicCODService::delete())
				self::$errors[] = $this->l('Could not delete DPD Classic + COD service');

		if (Tools::getValue(DpdGeopostConfiguration::IS_COD_CARRIER_10))
		{
			require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_10_cod.service.php');
			if (!DpdGeopostCarrier10CODService::install())
				self::$errors[] = $this->l('Could not save DPD 10:00 + COD service');
		}
		else
			if (!DpdGeopostCarrier10CODService::delete())
				self::$errors[] = $this->l('Could not delete DPD 10:00 + COD service');
		
		if (Tools::getValue(DpdGeopostConfiguration::IS_COD_CARRIER_12))
		{
			require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_12_cod.service.php');
			if (!DpdGeopostCarrier12CODService::install())
				self::$errors[] = $this->l('Could not save DPD 12:00 + COD service');
		}
		else
			if (!DpdGeopostCarrier12CODService::delete())
				self::$errors[] = $this->l('Could not delete DPD 12:00 + COD service');
		
		if (Tools::getValue(DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY))
		{
			require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_same_day_cod.service.php');
			if (!DpdGeopostCarrierSameDayCODService::install())
				self::$errors[] = $this->l('Could not save DPD Same Day + COD service');
		}
		else
			if (!DpdGeopostCarrierSameDayCODService::delete())
				self::$errors[] = $this->l('Could not delete DPD Same Day + COD service');
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
			
		if (Tools::getValue(DpdGeopostConfiguration::COUNTRY) == DpdGeopostConfiguration::OTHER &&
			!Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL) &&
			!Tools::getValue(DpdGeopostConfiguration::TEST_URL)
		)
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
		elseif (!Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) || Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) && Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE) < 0)
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
				Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_SAME_DAY)
			)
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
	
	public function testConnectivity()
	{
		$ws_country		 = Tools::getValue('ws_country');
		$ws_production_url  = Tools::getValue('production_ws_url');
		$ws_test_url		= Tools::getValue('test_ws_url');
		$other_country	  = Tools::getValue('other_country');
		$production_mode	= Tools::getValue('production_mode');
		$error_message		= '';
		
		if ($other_country)
		{
			if ($production_mode)
			{
				if (!$ws_production_url || $ws_production_url && !Validate::isUrl($ws_production_url))
					$error_message = $this->module_instance->l('Production URL is not valid', self::FILENAME);
			}
			else
			{
				if (!$ws_test_url || $ws_test_url && !Validate::isUrl($ws_test_url))
					$error_message = $this->module_instance->l('Test URL is not valid', self::FILENAME);
			}
		}
		else
		{
			$configuration = new DpdGeopostConfiguration();
			if (!isset($configuration->countries[$ws_country]))
				$error_message = $this->module_instance->l('Country does not exist', self::FILENAME);
			elseif (!isset($configuration->countries[$ws_country]['ws_uri_prod']) || !isset($configuration->countries[$ws_country]['ws_uri_test']))
				$error_message = $this->module_instance->l('Country does not have production / Test URL(s)', self::FILENAME);
			elseif (!$configuration->countries[$ws_country]['ws_uri_prod'] || !$configuration->countries[$ws_country]['ws_uri_test'])
				$error_message = $this->module_instance->l('Country production / Test URL(s) is(are) empty', self::FILENAME);
			elseif (!Validate::isUrl($configuration->countries[$ws_country]['ws_uri_prod']) || !Validate::isUrl($configuration->countries[$ws_country]['ws_uri_test']))
				$error_message = $this->module_instance->l('Country production / Test URL(s) is(are) empty', self::FILENAME);
		}
		
		return $error_message;
	}
}