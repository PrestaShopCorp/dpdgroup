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

require_once(_PS_MODULE_DIR_.'dpdgeopost/config.api.php');
require_once(_DPDGEOPOST_CLASSES_DIR_.'controller.php');
require_once(_DPDGEOPOST_MODULE_DIR_.'dpdgeopost.ws.php');
require_once(_DPDGEOPOST_CLASSES_DIR_.'messages.controller.php');

require_once(_DPDGEOPOST_MODELS_DIR_.'ObjectModel.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'CSV.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Configuration.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Shipment.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Manifest.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Parcel.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Pickup.php');
require_once(_DPDGEOPOST_MODELS_DIR_.'Carrier.php');

if (version_compare(_PS_VERSION_, '1.5', '<'))
	require_once(_DPDGEOPOST_MODULE_DIR_.'backward_compatibility/backward.php');

class DpdGeopost extends Module
{
	private $_html = '';
	public  $module_url;
	
	public  $id_carrier; // mandatory field for carrier recognision in front office
	private static $parcels = array(); // used to cache parcel setup for price calculation in front office
	private static $carriers = array(); // DPD carriers prices cache, used in front office
	
	private static $addresses = array();
	
	const CURRENT_INDEX = 'index.php?tab=AdminModules&token=';
	
	public function __construct()
	{
		$this->name = 'dpdgeopost';
		$this->tab = 'shipping_logistics';
		$this->version = '0.1';
		$this->author = 'Invertus';

		parent::__construct();
		
		$this->displayName = $this->l('DPD GeoPost');
		$this->description = $this->l('DPD GeoPost shipping module');

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->context = new Context;
			$this->smarty = $this->context->smarty;
			$this->context->smarty->assign('ps14', true);
		}
		
		if (defined('_PS_ADMIN_DIR_'))
			$this->module_url = self::CURRENT_INDEX.Tools::getValue('token').'&configure='.$this->name;
	}
	
	public function install()
	{
		if (!function_exists('curl_init'))
			return false;
		
		if (!extension_loaded('soap'))
			return false;
		
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'` (
				`id_csv` int(11) NOT NULL AUTO_INCREMENT,
				`id_shop` int(11) NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				`country` varchar(255) NOT NULL,
				`region` varchar(255) NOT NULL,
				`zip` varchar(255) NOT NULL,
				`weight_from` varchar(255) NOT NULL,
				`weight_to` varchar(255) NOT NULL,
				`shipping_price` varchar(255) NOT NULL,
				`shipping_price_percentage` varchar(255) NOT NULL,
				`currency` varchar(255) NOT NULL,
				`method_id` varchar(11) NOT NULL,
				`cod_surcharge` varchar(255) NOT NULL,
				`cod_surcharge_percentage` varchar(255) NOT NULL,
				`cod_min_surcharge` varchar(255) NOT NULL,
				PRIMARY KEY (`id_csv`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
		
		if (!Db::getInstance()->execute($sql)) return false;
		
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGEOPOST_PARCEL_DB_.'` (
				`id_parcel` int(10) NOT NULL AUTO_INCREMENT,
				`id_order` int(10) NOT NULL,
				`parcelReferenceNumber` varchar(30) NOT NULL,
				`id_product` int(10) NOT NULL,
				`id_product_attribute` int(10) NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				PRIMARY KEY (`id_parcel`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
		
		if (!Db::getInstance()->execute($sql)) return false;
		
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGEOPOST_CARRIER_DB_.'` (
				`id_dpd_geopost_carrier` int(10) NOT NULL AUTO_INCREMENT,
				`id_carrier` int(10) NOT NULL,
				`id_reference` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_dpd_geopost_carrier`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		
		if (!Db::getInstance()->execute($sql)) return false;
		
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'` (
				`id_shipment` int(10) NOT NULL,
				`id_order` int(10) NOT NULL,
				`id_manifest` int(10) NOT NULL DEFAULT "0",
				`label_printed` int(1) NOT NULL DEFAULT "0",
				`date_pickup` datetime DEFAULT NULL,
				PRIMARY KEY (`id_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		
		if (!Db::getInstance()->execute($sql)) return false;
		
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGEOPOST_REFERENCE_DB_.'` (
				`id_order` int(10) NOT NULL,
				`reference` varchar(9) NOT NULL,
				PRIMARY KEY (`id_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		
		if (!Db::getInstance()->execute($sql)) return false;
		
		$current_date = date('Y-m-d H:i:s');
		$currency = Currency::getDefaultCurrency();
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			$shops = array('1' => 1);
		else
			$shops = Shop::getShops();
		
		foreach (array_keys($shops) as $id_shop)
		{
			$sql = "
				INSERT INTO `"._DB_PREFIX_._DPDGEOPOST_CSV_DB_."`
					(`id_shop`, `date_add`, `date_upd`, `country`, `region`, `zip`, `weight_from`, `weight_to`, `shipping_price`, `currency`, `method_id`)
				VALUES
					('".(int)$id_shop."', '".pSQL($current_date)."', '".pSQL($current_date)."', '*', '*', '*', '0', '0.5', 0, '".pSQL($currency->iso_code)."', '".(int)_DPDGEOPOST_CLASSIC_ID_."'),
					('".(int)$id_shop."', '".pSQL($current_date)."', '".pSQL($current_date)."', '*', '*', '*', '0', '0.5', 0, '".pSQL($currency->iso_code)."', '".(int)_DPDGEOPOST_12_ID_."'),
					('".(int)$id_shop."', '".pSQL($current_date)."', '".pSQL($current_date)."', '*', '*', '*', '0', '0.5', 0, '".pSQL($currency->iso_code)."', '".(int)_DPDGEOPOST_10_ID_."'),
					('".(int)$id_shop."', '".pSQL($current_date)."', '".pSQL($current_date)."', '*', '*', '*', '0', '0.5', 0, '".pSQL($currency->iso_code)."', '".(int)_DPDGEOPOST_SAME_DAY_ID_."'),
					('".(int)$id_shop."', '".pSQL($current_date)."', '".pSQL($current_date)."', '*', '*', '*', '0', '0.5', 0, '".pSQL($currency->iso_code)."', '*')
				";
			
			if (!Db::getInstance()->execute($sql)) return false;
		}
		
		if (!parent::install())
			return false;
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			if (!$this->registerHook('paymentTop'))
				return false;
			
			if (!$this->registerHook('updateCarrier'))
				return false;
		}
		else
			if (!$this->registerHook('paymentTop'))
				return false;
		
		return (bool)$this->registerHook('adminOrder');
	}
	
	public function uninstall()
	{
		require_once(_DPDGEOPOST_CLASSES_DIR_.'service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_10.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_10_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_12.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_12_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_classic.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_classic_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_same_day.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_.'dpd_same_day_cod.service.php');

		return
			parent::uninstall() &&
			DpdGeopostCarrier10Service::delete() &&
			DpdGeopostCarrier10CODService::delete() &&
			DpdGeopostCarrier12Service::delete() &&
			DpdGeopostCarrier12CODService::delete() &&
			DpdGeopostCarrierClassicService::delete() &&
			DpdGeopostCarrierClassicCODService::delete() &&
			DpdGeopostCarrierSameDayService::delete() &&
			DpdGeopostCarrierSameDayCODService::delete() &&
			$this->dropTables() &&
			DpdGeopostConfiguration::deleteConfiguration();
	}
	
	private function dropTables()
	{
		return DB::getInstance()->Execute('
			DROP TABLE IF EXISTS
				`'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'`,
				`'._DB_PREFIX_._DPDGEOPOST_PARCEL_DB_.'`,
				`'._DB_PREFIX_._DPDGEOPOST_CARRIER_DB_.'`,
				`'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
		');
	}
	
	/**
	* module configuration page
	* @return page HTML code
	*/
	
	private function setGlobalVariablesForAjax()
	{
		require_once(_DPDGEOPOST_CLASSES_DIR_.'csv.controller.php');
		$this->context->smarty->assign(array(
			'download_csv_action'	=> DpdGeopostCSVController::SETTINGS_DOWNLOAD_CSV_ACTION,
			'dpd_geopost_ajax_uri' 	=> _DPDGEOPOST_AJAX_URI_,
			'dpd_geopost_token'		=> sha1(_COOKIE_KEY_.$this->name),
			'dpd_geopost_id_shop' 	=> (int)$this->context->shop->id,
			'dpd_geopost_id_lang' 	=> (int)$this->context->language->id
		));
	}
	
	public function getContent()
	{		
		$this->displayFlashMessagesIfIsset();
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->addJS(_DPDGEOPOST_JS_URI_.'backoffice.js');
			$this->addCSS(_DPDGEOPOST_CSS_URI_.'backoffice.css');
			$this->addCSS(_DPDGEOPOST_CSS_URI_.'toolbar.css');
		}
		else
		{
			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_.'backoffice.js');
			$this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_.'backoffice.css');
		}
		
		$this->setGlobalVariablesForAjax();
		$this->context->smarty->assign('dpd_geopost_other', DpdGeopostConfiguration::OTHER);
		$this->_html .= $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/prepare.tpl');

		switch(Tools::getValue('menu'))
		{
			case 'configuration':
				require_once(_DPDGEOPOST_CLASSES_DIR_.'configuration.controller.php');
				DpdGeopostConfigurationController::init();

				$this->context->smarty->assign('path', array($this->displayName, $this->l('Settings')));
				$this->displayNavigation();
				if (!version_compare(_PS_VERSION_, '1.5', '<'))
					$this->displayShopRestrictionWarning();
				
				$configuration_controller = new DpdGeopostConfigurationController();
				$this->_html .= $configuration_controller->getSettingsPage();
				break;
			case 'csv':
				require_once(_DPDGEOPOST_CLASSES_DIR_.'csv.controller.php');
				DpdGeopostCSVController::init();
				
				$this->context->smarty->assign('path', array($this->displayName, $this->l('Price rules')));
				$this->displayNavigation();
				
				if (!version_compare(_PS_VERSION_, '1.5', '<'))
					if (Shop::getContext() != Shop::CONTEXT_SHOP)
					{
						$this->_html .= $this->displayWarnings(array($this->l('CSV management is disabled when all shops or group of shops are selected')));
						break;
					}
				$csv_controller = new DpdGeopostCSVController();
				$this->_html .= $csv_controller->getCSVPage();
				break;
			case 'help':
				$this->context->smarty->assign('path', array($this->displayName, $this->l('Help')));
				$this->displayNavigation();
				break;
			case 'shipment_list':
			default:
				if (version_compare(_PS_VERSION_, '1.5', '<'))
				{
					includeDatepicker(null);
					$this->addJS(_DPDGEOPOST_JS_URI_.'jquery.bpopup.min.js');
				}
				else
				{
					$this->context->controller->addJqueryUI(array(
						'ui.slider', // for datetimepicker
						'ui.datepicker' // for datetimepicker
					));
				
					$this->context->controller->addJS(array(
						_DPDGEOPOST_JS_URI_.'jquery.bpopup.min.js',
						_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js' // for datetimepicker
					));
	
					$this->addCSS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'); // for datetimepicker
				}
				
				$this->context->smarty->assign('path', array($this->displayName, $this->l('Shipments')));
				$this->displayNavigation();
				
				require_once(_DPDGEOPOST_CLASSES_DIR_.'shipmentsList.controller.php');
				if (!version_compare(_PS_VERSION_, '1.5', '<') &&
					Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') &&
					count(Shop::getShops(0)) > 1 &&
					Shop::getContext() != Shop::CONTEXT_SHOP)
				{
					$this->_html .= $this->displayWarnings(array($this->l('Shipments functionality is disabled when all shops or group of shops are chosen')));
					break;
				}
				$shipment_controller = new DpdGeopostShipmentController();
				$this->_html .= $shipment_controller->getShipmentList();
				break;
		}
		
		return $this->_html;
	}
	
	private function displayShopRestrictionWarning()
	{
		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_GROUP)
			$this->_html .= $this->displayWarnings(array($this->l('You have chosen a group of shops, all the changes will be set for all shops in this group')));
		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_ALL)
			$this->_html .= $this->displayWarnings(array($this->l('You have chosen all shops, all the changes will be set for all shops')));
	}
	
	public function outputHTML($html)
	{
		$this->_html .= $html;
	}
	
	public static function addCSS($css_uri)
	{
		echo '<link href="'.$css_uri.'" rel="stylesheet" type="text/css">';
	}
	
	public static function addJS($js_uri)
	{
		echo '<script src="'.$js_uri.'" type="text/javascript"></script>';
	}
	
	private function displayNavigation()
	{
		$this->context->smarty->assign('module_link', $this->module_url);
		$this->_html .= $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/navigation.tpl');
	}
	
	/* adds success message into session */
	public static function addFlashMessage($msg)
	{
		$messages_controller = new DpdGeopostMessagesController();
		$messages_controller->setSuccessMessage($msg);
	}
	
	public static function addFlashError($msg)
	{
		$messages_controller = new DpdGeopostMessagesController();
		
		if (is_array($msg))
		{
			foreach ($msg as $message)
				$messages_controller->setErrorMessage($message);
		}
		else
			$messages_controller->setErrorMessage($msg);
	}
	
	/* displays success message only untill page reload */
	private function displayFlashMessagesIfIsset()
	{
		$messages_controller = new DpdGeopostMessagesController();
		
		if ($success_message = $messages_controller->getSuccessMessage())
			$this->_html .= $this->displayConfirmation($success_message);
		
		if ($error_message = $messages_controller->getErrorMessage())
			$this->_html .= $this->displayErrors($error_message);
	}
	
	public function displayErrors($errors)
	{
		$this->context->smarty->assign('errors', $errors);
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/errors.tpl');
	}
	
	public function displayWarnings($warnings)
	{
		$this->context->smarty->assign('warnings', $warnings);
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/warnings.tpl');
	}
	
	public static function getInputValue($name, $default_value = null)
	{
		return (Tools::isSubmit($name)) ? Tools::getValue($name) : $default_value;
	}
	
	public static function getMethodIdByCarrierId($id_carrier)
	{
		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
			return false;
		
		switch($id_reference)
		{
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_ID):
				return _DPDGEOPOST_CLASSIC_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_10_ID):
				return _DPDGEOPOST_10_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_12_ID):
				return _DPDGEOPOST_12_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_SAME_DAY_ID):
				return _DPDGEOPOST_SAME_DAY_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_COD_ID):
				return _DPDGEOPOST_CLASSIC_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_10_COD_ID):
				return _DPDGEOPOST_10_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_12_COD_ID):
				return _DPDGEOPOST_12_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_SAME_DAY_COD_ID):
				return _DPDGEOPOST_SAME_DAY_ID_;
			default:
				return false;
		}
	}
	
	public static function isCODCarrier($id_carrier)
	{
		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
			return false;
		
		switch($id_reference)
		{
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_COD_ID):
				return true;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_10_COD_ID):
				return true;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_12_COD_ID):
				return true;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_SAME_DAY_COD_ID):
				return true;
			default:
				return false;
		}
	}
	
	private static function getReferenceIdByCarrierId($id_carrier)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return DpdGeopostCarrier::getReferenceByIdCarrier($id_carrier);
		
		return Db::getInstance()->getValue('
			SELECT `id_reference`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE `id_carrier`='.(int)$id_carrier
		);
	}
	
	private function getModuleLink($tab)
	{
		# the ps15 way
		if (method_exists($this->context->link, 'getAdminLink'))
			return $this->context->link->getAdminLink($tab).'&configure='.$this->name;

		# the ps14 way
		return 'index.php?tab='.$tab.'&configure='.$this->name.'&token='.Tools::getAdminToken($tab.(int)(Tab::getIdFromClassName($tab)).(int)$this->context->cookie->id_employee);
	}
	
	public static function getPaymentModules()
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
				FROM `'._DB_PREFIX_.'module_country` mc
				LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
				INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module`)
				LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
				LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
				WHERE h.`name` = \'payment\'
				AND m.`active` = 1
				ORDER BY hm.`position`, m.`name` DESC
			');
		return Module::getPaymentModules();
	}
	
	public function hookAdminOrder($params)
	{
		$this->displayFlashMessagesIfIsset();
		$order = new Order((int)$params['id_order']);

		$customer = new Customer($order->id_customer);
		$shipment = new DpdGeopostShipment((int)$params['id_order']);
		$products = $shipment->getParcelsSetUp($order->getProductsDetail());
		
		if ($shipment->parcels)
			DpdGeopostParcel::addParcelDataToProducts($products, $order->id);
			
		$id_method = self::getMethodIdByCarrierId($order->id_carrier);
		$price = $shipment->calculatePriceForOrder((int)$id_method, $order->id_address_delivery, $products);
		
		$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
		
		$this->context->smarty->assign(array(
			'order' => $order,
			'module_link' => $this->getModuleLink('AdminModules'),
			'settings' => new DpdGeopostConfiguration,
			'total_weight' => DpdGeopostShipment::convertWeight($order->getTotalWeight()),
			'shipment' => $shipment,
			'selected_shipping_method_id' => $id_method,
			'ws_shippingPrice' => $price > 0 ? $price : '---',
			'products' => $products,
			'customer_addresses' => $customer->getAddresses($this->context->language->id),
			'error_message' => reset(DpdGeopostShipment::$errors),
			'carrier_url' => $carrier->url
		));
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->addJS(_DPDGEOPOST_JS_URI_.'jquery.bpopup.min.js');
			$this->addJS(_DPDGEOPOST_JS_URI_.'adminOrder.js');
			$this->addCSS(_DPDGEOPOST_CSS_URI_.'adminOrder.css');
		}
		else
		{
			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_.'jquery.bpopup.min.js');
			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_.'adminOrder.js');
			$this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_.'adminOrder.css');
		}
		
		$this->setGlobalVariablesForAjax();
		
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'hook/adminOrder.tpl');
	}
	
	public function hookPaymentTop($params)
	{
		if (!$this->getMethodIdByCarrierId((int)$this->context->cart->id_carrier)) //Check if DPD carrier is chosen
			return;
		
		if (Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return;
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return $this->disablePaymentMethods();
		
		if (!Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier)
			return;
		
		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);
		
		$cache_id = 'exceptionsCache';
		$exceptionsCache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array(); // existing cache
		$controller = (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
		$id_hook = Hook::getIdByName('displayPayment'); // ID of hook we are going to manipulate
		
		if ($paymentModules = self::getPaymentModules())
		{
			foreach ($paymentModules as $module)
			{
				if ($module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier
				)
				{
					$module_instance = Module::getInstanceByName($module['name']);
					
					if (Validate::isLoadedObject($module_instance))
					{
						$key = (int)$id_hook.'-'.(int)$module_instance->id;
						$exceptionsCache[$key][$this->context->shop->id][] = $controller;
					}
				}
			}
			
			Cache::store($cache_id, $exceptionsCache);
		}
	}
	
	private function disablePaymentMethods()
	{
		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);
		
		if ($paymentModules = self::getPaymentModules())
		{
			foreach ($paymentModules as $module)
			{
				if ($module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier
				)
				{
					$module_instance = Module::getInstanceByName($module['name']);
					if (Validate::isLoadedObject($module_instance))
					{
						$module_instance->active = 0;
						$module_instance->currencies = array();
					}
				}
			}
		}
	}
	
	public function hookUpdateCarrier($params)
	{
		$id_reference = (int)DpdGeopostCarrier::getReferenceByIdCarrier((int)$params['id_carrier']);
		$id_carrier = (int)$params['carrier']->id;
		
		$dpdgeopost_carrier = new DpdGeopostCarrier();
		$dpdgeopost_carrier->id_carrier = (int)$id_carrier;
		$dpdgeopost_carrier->id_reference = (int)$id_reference;
		$dpdgeopost_carrier->save();
	}
	
	public function getOrderShippingCost($cart, $price)
	{
		return $this->getOrderShippingCostExternal($cart);
	}

	public function getPackageShippingCost($cart, $shipping_cost, $products)
	{
		if (!Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return $this->getOrderShippingCostExternal($cart);

		return $this->getOrderShippingCostExternal($cart, $products);
	}

	public function getOrderShippingCostExternal($cart, $products = array())
	{
		if (!$this->id_carrier)
			return false;

		$cache_key = $this->getCacheKey($cart, $products);

		if (isset(self::$carriers[$this->id_carrier][$cache_key]))
			return self::$carriers[$this->id_carrier][$cache_key];

		$id_address_delivery = empty($products) ? (int)$cart->id_address_delivery : (int)$this->getIdAddressDeliveryByProducts($products);
		$id_country = (int)Tools::getValue('id_country');

		if ($id_country)
			$zone = Country::getIdZone($id_country);
		else
			$zone = Address::getZoneById($id_address_delivery);

		if (!$id_method = self::getMethodIdByCarrierId($this->id_carrier))
		{
			self::$carriers[$this->id_carrier][$cache_key] = false;
			return false;
		}

		$carrier = new Carrier((int)$this->id_carrier);

		if (!Validate::isLoadedObject($carrier))
			return false;

		$is_cod_method = $this->isCODCarrier($this->id_carrier);
		$carrier_shipping_method = $carrier->getShippingMethod();
		$order_total_price = empty($products) ? $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) : $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $products, $this->id_carrier);
		$total_weight = empty($products) ? $cart->getTotalWeight() : $cart->getTotalWeight($products);
		$cart_total = $carrier_shipping_method == Carrier::SHIPPING_METHOD_WEIGHT ? DpdGeopostShipment::convertWeight($total_weight) : $order_total_price;

		$configuration = new DpdGeopostConfiguration();
		$price_rule = DpdGeopostShipment::getPriceRule($cart_total, $id_method, $id_address_delivery, $is_cod_method);
		$additional_shipping_cost = $this->calculateAdditionalShippingCost($cart, $products);
		$additional_shipping_cost = Tools::convertPrice($additional_shipping_cost);

		$handling_charges = $carrier->shipping_handling ? Configuration::get('PS_SHIPPING_HANDLING') : 0;
		$handling_charges = Tools::convertPrice($handling_charges);

		if ($configuration->price_calculation_method == DpdGeopostConfiguration::PRESTASHOP)
		{
			if ($carrier_shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
				$carrier_price = $carrier->getDeliveryPriceByWeight($cart_total, $zone);
			else
				$carrier_price = $carrier->getDeliveryPriceByPrice($cart_total, $zone);

			$default_currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT', null, null, 0));
			$carrier_price = $this->convertPriceByCurrency($carrier_price, $default_currency->iso_code);
			
			$shipping_price_with_charges = $carrier_price + $additional_shipping_cost + $handling_charges;

			$cod_price = 0;
			if (!empty($price_rule) && $is_cod_method)
			{
				if ($price_rule['cod_surcharge'] !== '')
					$cod_price = $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($price_rule['cod_surcharge_percentage'] !== '')
				{
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $shipping_price_with_charges;
					$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}
			
			$price = $shipping_price_with_charges + $cod_price;

			self::$carriers[$this->id_carrier][$cache_key] = $price;
			return self::$carriers[$this->id_carrier][$cache_key];
		}
		elseif ($configuration->price_calculation_method == DpdGeopostConfiguration::WEB_SERVICES)
		{
			$shipment = new DpdGeopostShipment;

			if (!self::$parcels)
			{
				$cart_products = empty($products) ? $cart->getProducts() : $products;
				self::$parcels = $shipment->putProductsToParcels($cart_products);
			}

			$extra_params = $is_cod_method ? array(
				'total_paid' => $order_total_price,
				'currency_iso_code' => $this->context->currency->iso_code,
				'reference' => (int)$this->context->cart->id
			) : array();

			$result = $shipment->calculate($id_method, $id_address_delivery, self::$parcels, null, $extra_params);

			if ($result === false || !isset($result['price']) || !isset($result['id_currency']))
			{
				self::$carriers[$this->id_carrier][$cache_key] = false;
				return false;
			}

			$result_price_in_default_currency = Tools::convertPrice($result['price'], new Currency((int)$result['id_currency']), false);
			$result['price'] = Tools::convertPrice($result_price_in_default_currency);

			$result += $additional_shipping_cost + $handling_charges;
			
			if (!empty($price_rule))
			{
				if ($price_rule['shipping_price_percentage'] !== '')
				{
					$surcharge = $result['price'] * $price_rule['shipping_price_percentage'] / 100;
					$result['price'] += $surcharge;
				}

				if ($is_cod_method && $price_rule['cod_surcharge'] !== '')
					$result['price'] += $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($is_cod_method && $price_rule['cod_surcharge_percentage'] !== '')
				{
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $result['price'];
					$result['price'] += $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}

			self::$carriers[$this->id_carrier][$cache_key] = $result['price'];
			return self::$carriers[$this->id_carrier][$cache_key];
		}
		elseif ($configuration->price_calculation_method == DpdGeopostConfiguration::CSV)
		{
			if (empty($price_rule))
				return false;

			if ($price_rule['shipping_price'] !== '')
				$carrier_price = $this->convertPriceByCurrency($price_rule['shipping_price'], $price_rule['currency']);
			elseif ($price_rule['shipping_price_percentage'] !== '')
				$carrier_price = $order_total_price * $price_rule['shipping_price_percentage'] / 100;
			else
				return false;

			$shipping_price_with_charges = $carrier_price + $additional_shipping_cost + $handling_charges;

			$cod_price = 0;
			if ($is_cod_method)
			{
				if ($price_rule['cod_surcharge'] !== '')
					$cod_price = $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($price_rule['cod_surcharge_percentage'] !== '')
				{
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $shipping_price_with_charges;
					$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}

			$price = $shipping_price_with_charges + $cod_price;

			self::$carriers[$this->id_carrier][$cache_key] = $price;
			return self::$carriers[$this->id_carrier][$cache_key];
		}

		return false;
	}
	
	private function getCacheKey($cart, $products)
	{
		if (empty($products))
			$products = $cart->getProducts();

		$cache_key = '';

		foreach ($products as $product)
			for ($i = 0; $i < $product['cart_quantity']; $i++)
				$cache_key .= $product['id_product'].'_'.$product['id_product_attribute'].';';

		return $cache_key;
	}
	
	private function getIdAddressDeliveryByProducts($products)
	{
		foreach ($products as $product)
			return $product['id_address_delivery'];
	}

	private function calculateAdditionalShippingCost($cart, $products)
	{
		$additional_shipping_price = 0;
		$cart_products = empty($products) ? $cart->getProducts() : $products;

		foreach ($cart_products as $product)
			$additional_shipping_price += (int)$product['cart_quantity'] * (float)$product['additional_shipping_cost'];

		return $additional_shipping_price;
	}

	/**
	 * Convert price from given currency to current context currency
	 *
	 * @param (float) $price - price which will be converted from given currency
	 * @param (string) $iso_code_currency - iso code of given currency
	 * 
	 * @return (float) converted price without currency sign
	 */
	private function convertPriceByCurrency($price, $iso_code_currency)
	{
		$currency = new Currency(Currency::getIdByIsoCode($iso_code_currency));

		if (!Validate::isLoadedObject($currency))
			return 0;

		$price_in_default_currency = Tools::convertPrice($price, $currency, false);

		return Tools::convertPrice($price_in_default_currency);
	}

	/**
	 * Calculate percentage value of given price. If minimum value is higher than percentage
	 * value than it is returned minimum value converted into current context currency
	 *
	 * @param (float) $order_total_price - price which will be used to calculate percentage value
	 * @param (float) $cod_surcharge_percentage - percentage factor
	 * @param (float) $cod_min_surcharge - price in given currency which will be used as minimum value
	 * @param (string) $iso_code_currency - iso code of given currency
	 * 
	 * @return (float) calculated price without currency sign
	 */
	private function calculateCODSurchargePercentage($order_total_price, $cod_surcharge_percentage, $cod_min_surcharge, $iso_code_currency)
	{
		$surcharge_percentage = $order_total_price * $cod_surcharge_percentage / 100;
		if ($cod_min_surcharge !== '' && ($min_surcharge = $this->convertPriceByCurrency($cod_min_surcharge, $iso_code_currency))  > $surcharge_percentage)
			$surcharge_percentage = $min_surcharge;

		return $surcharge_percentage;
	}
}