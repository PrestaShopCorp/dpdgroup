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

require_once(dirname(__FILE__).'/config.api.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Controller.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Webservice.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Messages.controller.php');

require_once(_DPDGROUP_CLASSES_DIR_.'ObjectModel.php');
require_once(_DPDGROUP_CLASSES_DIR_.'CSV.php');
require_once(_DPDGROUP_CLASSES_DIR_.'Configuration.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Shipment.webservice.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Manifest.webservice.php');
require_once(_DPDGROUP_CLASSES_DIR_.'Parcel.php');
require_once(_DPDGROUP_CONTROLLERS_DIR_.'Pickup.webservice.php');
require_once(_DPDGROUP_CLASSES_DIR_.'Carrier.php');

if (version_compare(_PS_VERSION_, '1.5', '<'))
	require_once(_DPDGROUP_MODULE_DIR_.'backward_compatibility/backward.php');

class DpdGroup extends CarrierModule
{
	private $html = '';
	public $module_url;
	private $ps_14;

	public $id_carrier; /* mandatory field for carrier recognision in front office */
	private static $parcels = array(); /* used to cache parcel setup for price calculation in front office */
	private static $carriers = array(); /* DPD carriers prices cache, used in front office */

	const CURRENT_INDEX = 'index.php?tab=AdminModules&token=';
	const ROMANIA_COUNTRY_ISO_CODE = 'RO';

	public function __construct()
	{
		$this->name = 'dpdgroup';
		$this->tab = 'shipping_logistics';
		$this->version = '0.1.0';
		$this->author = 'Invertus';
		$this->module_key = '8f6c90d2a004cd27f552fc11d1152846';

		parent::__construct();

		$this->displayName = $this->l('DPD Group');
		$this->description = $this->l('DPD shipping module');

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->context = new Context;
			$this->smarty = $this->context->smarty;
			$this->context->smarty->assign('ps14', true);
		}

		if (defined('_PS_ADMIN_DIR_'))
			$this->module_url = self::CURRENT_INDEX.Tools::getValue('token').'&configure='.$this->name;

		$this->bootstrap = (bool)version_compare(_PS_VERSION_, '1.6', '>=');
		$this->ps_14 = (bool)version_compare(_PS_VERSION_, '1.5', '<');
	}

	public function install()
	{
		$hooks = array('paymentTop', 'adminOrder', 'backOfficeHeader');
		$database_table_install_error = false;
		$price_rules_data_intall_error = false;

		if (!extension_loaded('soap'))
		{
			$this->_errors[] = $this->l('Soap Client library is not installed');
			return false;
		}

		if (!parent::install())
			return false;

		if ($this->ps_14)
			$hooks[] = 'updateCarrier';

		if (!$this->registerHooks($hooks))
		{
			parent::uninstall();
			return false;
		}

		require_once(_DPDGROUP_SQL_DIR_.'install.php');

		if ($database_table_install_error)
		{
			$this->_errors[] = $this->l('Could not install database tables');
			return false;
		}

		if ($price_rules_data_intall_error)
		{
			$this->dropTables();
			$this->_errors[] = $this->l('Could not add default price rules data');
			parent::uninstall();
			return false;
		}

		return true;
	}

	private function registerHooks(array $hooks)
	{
		foreach ($hooks as $hook)
			if (!$this->registerHook($hook))
			{

				return false;
			}

		return true;
	}

	public function uninstall()
	{
		require_once(_DPDGROUP_CONTROLLERS_DIR_.'Service.php');

		$services = array(
			DpdGroupConfiguration::CARRIER_CLASSIC_ID,
			DpdGroupConfiguration::CARRIER_10_ID,
			DpdGroupConfiguration::CARRIER_12_ID,
			DpdGroupConfiguration::CARRIER_SAME_DAY_ID,
			DpdGroupConfiguration::CARRIER_B2C_ID,
			DpdGroupConfiguration::CARRIER_INTERNATIONAL_ID,
			DpdGroupConfiguration::CARRIER_BULGARIA_ID,
			DpdGroupConfiguration::CARRIER_CLASSIC_COD_ID,
			DpdGroupConfiguration::CARRIER_10_COD_ID,
			DpdGroupConfiguration::CARRIER_12_COD_ID,
			DpdGroupConfiguration::CARRIER_SAME_DAY_COD_ID,
			DpdGroupConfiguration::CARRIER_B2C_COD_ID,
			DpdGroupConfiguration::CARRIER_INTERNATIONAL_COD_ID,
			DpdGroupConfiguration::CARRIER_BULGARIA_COD_ID
		);

		foreach ($services as $id_service)
			if (!DpdGroupService::deleteCarrier($id_service))
			{
				$this->_errors[] = $this->l('Could not delete DPD carrier');
				return false;
			}

		return
			parent::uninstall() &&
			$this->dropTables() &&
			$this->dropTriggers() &&
			DpdGroupConfiguration::deleteConfiguration();
	}

	private function dropTables()
	{
		return DB::getInstance()->Execute('
			DROP TABLE IF EXISTS
				`'._DB_PREFIX_._DPDGROUP_CSV_DB_.'`,
				`'._DB_PREFIX_._DPDGROUP_PARCEL_DB_.'`,
				`'._DB_PREFIX_._DPDGROUP_CARRIER_DB_.'`,
				`'._DB_PREFIX_._DPDGROUP_SHIPMENT_DB_.'`,
				`'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`,
				`'._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'`
		');
	}

	private function dropTriggers()
	{
		return DB::getInstance()->Execute('
			DROP TRIGGER IF EXISTS `dpd_trigger_update_address`
		');
	}

	private function setGlobalVariablesForAjax()
	{
		require_once(_DPDGROUP_CONTROLLERS_DIR_.'Csv.controller.php');

		$this->context->smarty->assign(array(
			'dpd_geopost_ajax_uri' => _DPDGROUP_AJAX_URI_,
			'dpd_geopost_token' => sha1(_COOKIE_KEY_.$this->name),
			'dpd_geopost_id_shop' => (int)$this->context->shop->id,
			'dpd_geopost_id_lang' => (int)$this->context->language->id
		));
	}

	/**
	 * module configuration page
	 * @return page HTML code
	 */
	public function getContent()
	{
		$this->displayFlashMessagesIfIsset();

		if ($this->ps_14)
		{
			$this->addJS(_DPDGROUP_JS_URI_.'backoffice.js');
			$this->addCSS(_DPDGROUP_CSS_URI_.'backoffice.css');
			$this->addCSS(_DPDGROUP_CSS_URI_.'toolbar.css');
		}
		else
		{
			$this->context->controller->addJS(_DPDGROUP_JS_URI_.'backoffice.js');

			if (!$this->bootstrap || Tools::getValue('menu') && Tools::getValue('menu') != 'shipment_list')
				$this->context->controller->addCSS(_DPDGROUP_CSS_URI_.'backoffice.css');
		}

		$this->setGlobalVariablesForAjax();
		$this->context->smarty->assign('dpd_geopost_other_country', DpdGroupConfiguration::OTHER_COUNTRY);
		$this->html .= $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/global_variables.tpl');

		if ($this->bootstrap)
			$this->html .= $this->getBootstrapMenu();

		$this->displayModuleWarnings();

		switch (Tools::getValue('menu'))
		{
			case 'configuration':
				require_once(_DPDGROUP_CONTROLLERS_DIR_.'Configuration.controller.php');

				DpdGroupConfigurationController::init();

				if (!$this->bootstrap)
				{
					$this->context->smarty->assign('path', array($this->displayName, $this->l('Settings')));
					$this->displayNavigation();
				}

				if (!$this->ps_14)
					$this->displayShopRestrictionWarning();

				$configuration_controller = new DpdGroupConfigurationController();
				$this->html .= $configuration_controller->getSettingsPage();
				break;
			case 'csv':
				require_once(_DPDGROUP_CONTROLLERS_DIR_.'Csv.controller.php');

				DpdGroupCSVController::init();

				if (!$this->bootstrap)
				{
					$this->context->smarty->assign('path', array($this->displayName, $this->l('Price rules')));
					$this->displayNavigation();
				}

				if (!$this->ps_14)
					if (Shop::getContext() != Shop::CONTEXT_SHOP)
					{
						$this->displayAdminWarning($this->l('CSV management is disabled when all shops or group of shops are selected'));
						break;
					}
				$csv_controller = new DpdGroupCSVController();
				$this->html .= $csv_controller->getCSVPage();
				break;
			case 'help':
				if (!$this->bootstrap)
				{
					$this->context->smarty->assign('path', array($this->displayName, $this->l('Help')));
					$this->displayNavigation();
				}

				if (Tools::isSubmit('print_pdf'))
				{
					$filename = 'dpdgroup_eng.pdf';

					ob_end_clean();
					header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$this->l('manual').'.pdf"');
					readfile(_PS_MODULE_DIR_.$this->name.'/manual/'.$filename);
					exit;
				}

				$this->context->smarty->assign('module_link', $this->module_url);
				$this->html .= $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/help.tpl');

				break;
			case 'shipment_list':
			default:
				if ($this->ps_14)
				{
					includeDatepicker(null);
					$this->addJS(_DPDGROUP_JS_URI_.'jquery.bpopup.min.js');
				}
				else
				{
					$this->context->controller->addJqueryUI(array(
						'ui.slider', // for datetimepicker
						'ui.datepicker' // for datetimepicker
					));

					$this->context->controller->addJS(array(
						_DPDGROUP_JS_URI_.'jquery.bpopup.min.js',
						_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js' // for datetimepicker
					));

					$this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'); // for datetimepicker
				}

				if (!$this->bootstrap)
				{
					$this->context->smarty->assign('path', array($this->displayName, $this->l('Shipments')));
					$this->displayNavigation();
				}
				else
					$this->context->controller->addCSS(_DPDGROUP_CSS_URI_.'backoffice_16.css');

				require_once(_DPDGROUP_CONTROLLERS_DIR_.'ShipmentsList.controller.php');

				if (!$this->ps_14 &&
					Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') &&
					count(Shop::getShops(0)) > 1 && Shop::getContext() != Shop::CONTEXT_SHOP)
				{
					$this->displayAdminWarning($this->l('Shipments functionality is disabled when all shops or group of shops are chosen'));
					break;
				}
				$shipment_controller = new DpdGroupShipmentController();
				$this->html .= $shipment_controller->getShipmentList();
				break;
		}

		return $this->html;
	}

	private function displayModuleWarnings()
	{
		$configuration = new DpdGroupConfiguration();

		if (!$configuration->checkRequiredFields())
		{
			$warning_message = $this->l('Module is not fully configured yet.');

			if (Tools::getValue('menu') != 'configuration' && !$this->ps_14)//on PS 1.4 html tags are encoded
				$warning_message .= ' <a href="'.$this->module_url.'&menu=configuration">'.$this->l('Configuration page →').'</a>';

			$this->displayAdminWarning($warning_message);
		}

		if ($configuration->debug_mode)
		{
			$warning_message = $this->l('Module is in debug mode.');

			if (!$this->ps_14)//on PS 1.4 html tags are encoded
			{
				$debug_filename = DpdGroupWS::createDebugFileIfNotExists();
				$warning_message .= ' <a target="_blank" href="'._DPDGROUP_MODULE_URI_.$debug_filename.'">'.$this->l('Debug file →').'</a>';
			}

			$this->displayAdminWarning($warning_message);
		}
	}

	private function displayAdminWarning($message)
	{
		if ($this->ps_14)
		{
			$this->context->smarty->assign('warning_message', $message);
			$this->html .= $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/warning.tpl');
		}
		else
			$this->adminDisplayWarning($message);
	}

	private function getBootstrapMenu()
	{
		$this->context->smarty->assign('menutabs', $this->getModuleTabs());

		return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/navigation.tpl');
	}

	private function getModuleTabs()
	{
		$menu_tabs = array(
			'shipment_list' => array(
				'short' => 'Shipment_list',
				'desc' => $this->l('Shipment list'),
				'href' => $this->module_url.'&menu=shipment_list',
				'active' => false,
				'imgclass' => 'icon-bars'
			),
			'csv' => array(
				'short' => 'Price_rules',
				'desc' => $this->l('Price rules'),
				'href' => $this->module_url.'&menu=csv',
				'active' => false,
				'imgclass' => 'icon-money'
			),
			'configuration' => array(
				'short' => 'Settings',
				'desc' => $this->l('Settings'),
				'href' => $this->module_url.'&menu=configuration',
				'active' => false,
				'imgclass' => 'icon-cogs'
			),
			'help' => array(
				'short' => 'Help',
				'desc' => $this->l('Help'),
				'href' => $this->module_url.'&menu=help',
				'active' => false,
				'imgclass' => 'icon-info-circle'
			)
		);

		$selected_tab = Tools::getValue('menu', 'shipment_list');
		$menu_tabs[$selected_tab]['active'] = true;

		return $menu_tabs;
	}

	private function displayShopRestrictionWarning()
	{
		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_GROUP)
			$this->displayAdminWarning($this->l('You have chosen a group of shops, all the changes will be set for all shops in this group'));

		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_ALL)
			$this->displayAdminWarning($this->l('You have chosen all shops, all the changes will be set for all shops'));
	}

	public function outputHTML($html)
	{
		$this->html .= $html;
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
		$this->html .= $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/navigation.tpl');
	}

	/* adds success message into session */
	public static function addFlashMessage($msg)
	{
		$messages_controller = new DpdGroupMessagesController();
		$messages_controller->setSuccessMessage($msg);
	}

	public static function addFlashError($msg)
	{
		$messages_controller = new DpdGroupMessagesController();

		if (is_array($msg))
		{
			foreach ($msg as $message)
				$messages_controller->setErrorMessage($message);
		}
		else
			$messages_controller->setErrorMessage($msg);
	}

	/* displays success message only until page reload */
	private function displayFlashMessagesIfIsset()
	{
		$messages_controller = new DpdGroupMessagesController();

		if ($success_message = $messages_controller->getSuccessMessage())
			$this->html .= $this->displayConfirmation($success_message);

		if ($error_message = $messages_controller->getErrorMessage())
			$this->html .= $this->displayError(implode('<br />', $error_message));
	}

	public static function getInputValue($name, $default_value = null)
	{
		return (Tools::isSubmit($name)) ? Tools::getValue($name) : $default_value;
	}

	public static function getMethodIdByCarrierId($id_carrier)
	{
		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
			return false;

		switch ($id_reference)
		{
			case Configuration::get(DpdGroupConfiguration::CARRIER_CLASSIC_ID):
				return _DPDGROUP_CLASSIC_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_10_ID):
				return _DPDGROUP_10_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_12_ID):
				return _DPDGROUP_12_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_SAME_DAY_ID):
				return _DPDGROUP_SAME_DAY_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_B2C_ID):
				return _DPDGROUP_B2C_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_INTERNATIONAL_ID):
				return _DPDGROUP_INTERNATIONAL_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_BULGARIA_ID):
				return _DPDGROUP_BULGARIA_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_CLASSIC_COD_ID):
				return _DPDGROUP_CLASSIC_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_10_COD_ID):
				return _DPDGROUP_10_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_12_COD_ID):
				return _DPDGROUP_12_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_SAME_DAY_COD_ID):
				return _DPDGROUP_SAME_DAY_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_B2C_COD_ID):
				return _DPDGROUP_B2C_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_INTERNATIONAL_COD_ID):
				return _DPDGROUP_INTERNATIONAL_ID_;
			case Configuration::get(DpdGroupConfiguration::CARRIER_BULGARIA_COD_ID):
				return _DPDGROUP_BULGARIA_ID_;
			default:
				return false;
		}
	}

	public static function isCODCarrier($id_carrier)
	{
		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
			return false;

		switch ($id_reference)
		{
			case Configuration::get(DpdGroupConfiguration::CARRIER_CLASSIC_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_10_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_12_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_SAME_DAY_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_B2C_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_INTERNATIONAL_COD_ID):
				return true;
			case Configuration::get(DpdGroupConfiguration::CARRIER_BULGARIA_COD_ID):
				return true;
			default:
				return false;
		}
	}

	private static function getReferenceIdByCarrierId($id_carrier)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return DpdGroupCarrier::getReferenceByIdCarrier($id_carrier);

		return Db::getInstance()->getValue('
			SELECT `id_reference`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE `id_carrier`='.(int)$id_carrier
		);
	}

	private function getModuleLink($tab)
	{
		// the ps15 way
		if (method_exists($this->context->link, 'getAdminLink'))
			return $this->context->link->getAdminLink($tab).'&configure='.$this->name;

		// the ps14 way
		return 'index.php?tab='.$tab.'&configure='.$this->name.'&token='.Tools::getAdminToken($tab.
			(int)Tab::getIdFromClassName($tab).(int)$this->context->cookie->id_employee);
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

	public function hookBackOfficeHeader()
	{
		if ((Tools::getValue('controller') == 'AdminAddresses' || Tools::getValue('tab') == 'AdminAddresses') &&
			(Tools::isSubmit('updateaddress') || Tools::isSubmit('addaddress') || Tools::isSubmit('submitAddaddress')))
		{
			$this->context->smarty->assign('dpdgroup_token', sha1(_COOKIE_KEY_.$this->name));

			if (version_compare(_PS_VERSION_, '1.5', '>='))
			{
				$this->context->controller->addCSS(_DPDGROUP_CSS_URI_.'address_autocomplete.css');
				$this->context->controller->addJS(_DPDGROUP_JS_URI_.'address_autocomplete.js');
				$this->context->controller->addJS(_DPDGROUP_JS_URI_.'jquery-ui.min.js');
			}
			else
				$this->addCSS(_DPDGROUP_CSS_URI_.'address_autocomplete.css');

			return $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/address_header.tpl');
		}

		return '';
	}

	public function hookAdminOrder($params)
	{
		$shipment = new DpdGroupShipment((int)$params['id_order']);

		if (Tools::isSubmit('printLabels'))
		{
			$pdf_file_contents = $shipment->getLabelsPdf();

			if ($pdf_file_contents)
			{
				ob_end_clean();
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename="shipment_labels_'.(int)Tools::getValue('id_order').'.pdf"');
				echo $pdf_file_contents;
			}
			else
			{
				$this->addFlashError(reset(DpdGroupShipment::$errors));
				Tools::redirectAdmin(self::getAdminOrderLink().'&id_order='.(int)$params['id_order']);
			}
		}

		$this->displayFlashMessagesIfIsset();
		$order = new Order((int)$params['id_order']);
		$customer = new Customer($order->id_customer);

		$products = $shipment->getParcelsSetUp($order->getProductsDetail());

		if ($shipment->parcels)
			DpdGroupParcel::addParcelDataToProducts($products, $order->id);

		$id_method = self::getMethodIdByCarrierId($order->id_carrier);

		DpdGroupWS::$parcel_weight_warning_message = false;
		$price = $shipment->calculatePriceForOrder((int)$id_method, $order->id_address_delivery, $products);
		$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
		$ws_shipping_price = $price !== false ? $price : '---';
		$total_shipping = version_compare(_PS_VERSION_, '1.5', '>=') ? $order->total_shipping_tax_incl : $order->total_shipping;

		$this->context->smarty->assign(array(
			'order' => $order,
			'module_link' => $this->getModuleLink('AdminModules'),
			'settings' => new DpdGroupConfiguration,
			'total_weight' => DpdGroupShipment::convertWeight($order->getTotalWeight()),
			'shipment' => $shipment,
			'selected_shipping_method_id' => $id_method,
			'ws_shippingPrice' => $price !== false ? $price : '---',
			'products' => $products,
			'customer_addresses' => $customer->getAddresses($this->context->language->id),
			'carrier_url' => $carrier->url,
			'order_link' => self::getAdminOrderLink().'&id_order='.(int)Tools::getValue('id_order'),
			'errors' => $this->getErrorMessagesForOrderPage(),
			'warnings' => $this->getWarningMessagesForOrderPage($shipment->id_shipment, $id_method,
				$ws_shipping_price, $total_shipping),
			'force_enable_button' => DpdGroupWS::$parcel_weight_warning_message,
			'display_product_weight_warning' => $this->orderProductsWithoutWeight($products)
		));

		if ($this->ps_14)
		{
			$this->addJS(_DPDGROUP_JS_URI_.'jquery.bpopup.min.js');
			$this->addJS(_DPDGROUP_JS_URI_.'adminOrder.js');
			$this->addCSS(_DPDGROUP_CSS_URI_.'adminOrder.css');
		}
		else
		{
			$this->context->controller->addJS(_DPDGROUP_JS_URI_.'jquery.bpopup.min.js');
			$this->context->controller->addJS(_DPDGROUP_JS_URI_.'adminOrder.js');

			$css_filename = $this->bootstrap ? 'adminOrder_16' : 'adminOrder';
			$this->context->controller->addCSS(_DPDGROUP_CSS_URI_.$css_filename.'.css');
		}

		$this->setGlobalVariablesForAjax();
		$template_filename = $this->bootstrap ? 'adminOrder_16' : 'adminOrder';

		return $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'hook/'.$template_filename.'.tpl');
	}

	private function orderProductsWithoutWeight($prodcuts)
	{
		foreach ($prodcuts as $product)
			if ($product['product_weight'] <= 0)
				return true;

		return false;
	}

	public static function getAdminOrderLink()
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return 'index.php?tab=AdminOrders&vieworder&token='.Tools::getAdminTokenLite('AdminOrders');

		return 'index.php?controller=AdminOrders&vieworder&token='.Tools::getAdminTokenLite('AdminOrders');
	}

	private function getErrorMessagesForOrderPage()
	{
		$errors = array();

		if ($messages = DpdGroupShipment::$errors)
			foreach ($messages as $error)
				$errors[] = $error;

		$messages_controller = new DpdGroupMessagesController();

		if ($messages = $messages_controller->getErrorMessage())
			foreach ($messages as $error)
				$errors[] = $error;

		$errors = array_unique($errors);

		return $errors;
	}

	private function getWarningMessagesForOrderPage($id_shipment, $id_selected_shipment_method, $ws_shipping_price, $total_shipping_tax_incl)
	{
		$warnings = array();

		if ($messages = DpdGroupShipment::$notices)
			foreach ($messages as $notice)
				$warnings[] = $notice;

		if (!$id_shipment && !$id_selected_shipment_method)
			$warnings[] = $this->l('Client did not selected DPD shipment, but you can use this shipment method.');

		if ($ws_shipping_price > 0 && $ws_shipping_price > $total_shipping_tax_incl)
			$warnings[] = $this->l('Shipping costs more than client paid.');

		return $warnings;
	}

	public function hookPaymentTop()
	{
		if (!$this->getMethodIdByCarrierId((int)$this->context->cart->id_carrier)) //Check if DPD carrier is chosen
			return null;

		if (Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return null;

		if ($this->ps_14)
		{
			$this->disablePaymentMethods();
			return null;
		}

		if (!Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier)
			return null;

		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGroupConfiguration::COD_MODULE);

		$cache_id = 'exceptionsCache';
		$exceptions_cache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array(); // existing cache
		$controller = (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
		$id_hook = Hook::getIdByName('displayPayment'); // ID of hook we are going to manipulate

		if ($payment_modules = self::getPaymentModules())
		{
			foreach ($payment_modules as $module)
			{
				if ($module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier)
				{
					$module_instance = Module::getInstanceByName($module['name']);

					if (Validate::isLoadedObject($module_instance))
					{
						$key = (int)$id_hook.'-'.(int)$module_instance->id;
						$exceptions_cache[$key][$this->context->shop->id][] = $controller;
					}
				}
			}

			Cache::store($cache_id, $exceptions_cache);
		}
	}

	private function disablePaymentMethods()
	{
		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGroupConfiguration::COD_MODULE);

		if ($payment_modules = self::getPaymentModules())
		{
			foreach ($payment_modules as $module)
			{
				if ($module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier)
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
		$id_reference = (int)DpdGroupCarrier::getReferenceByIdCarrier((int)$params['id_carrier']);
		$id_carrier = (int)$params['carrier']->id;

		$dpdgroup_carrier = new DpdGroupCarrier();
		$dpdgroup_carrier->id_carrier = (int)$id_carrier;
		$dpdgroup_carrier->id_reference = (int)$id_reference;
		$dpdgroup_carrier->save();
	}

	public function getOrderShippingCost($cart, $shipping_cost)
	{
		return $this->getOrderShippingCostExternal($cart, array(), $shipping_cost);
	}

	public function getPackageShippingCost($cart, $shipping_cost, $products)
	{
		if (!Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return $this->getOrderShippingCostExternal($cart);

		return $this->getOrderShippingCostExternal($cart, $products, $shipping_cost);
	}

	public function getOrderShippingCostExternal($cart, $products = array())
	{
		if (!$this->id_carrier || !$cart instanceof Cart)
			return false;

		$cache_key = $this->getCacheKey($cart, $products);

		if (isset(self::$carriers[$this->id_carrier][$cache_key]))
			return self::$carriers[$this->id_carrier][$cache_key];

		$id_address_delivery = empty($products) ? (int)$cart->id_address_delivery : (int)$this->getIdAddressDeliveryByProducts($products);
		$id_customer_country = (int)Tools::getValue('id_country');

		if (!$id_customer_country)
		{
			$customer_country = Address::getCountryAndState((int)$id_address_delivery);
			$id_customer_country = (int)$customer_country['id_country'];
		}

		$zone = $id_customer_country ? Country::getIdZone((int)$id_customer_country) : Address::getZoneById((int)$id_address_delivery);

		if (!$id_method = self::getMethodIdByCarrierId($this->id_carrier))
		{
			self::$carriers[$this->id_carrier][$cache_key] = false;
			return false;
		}

		$carrier = new Carrier((int)$this->id_carrier);

		if (!Validate::isLoadedObject($carrier))
			return false;

		$configuration = new DpdGroupConfiguration();
		$is_cod_method = $this->isCODCarrier((int)$this->id_carrier);

		if ($is_cod_method && !$this->isCODCarrierAvailable($cart, $configuration, (int)$id_customer_country))
		{
			self::$carriers[$this->id_carrier][$cache_key] = false;
			return false;
		}

		$carrier_shipping_method = $carrier->getShippingMethod();
		$order_total_price = empty($products) ? $cart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING) :
			$cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $products, $this->id_carrier);
		$total_weight = empty($products) ? $cart->getTotalWeight() : $cart->getTotalWeight($products);
		$cart_total = $carrier_shipping_method == Carrier::SHIPPING_METHOD_WEIGHT ? DpdGroupShipment::convertWeight($total_weight) :
			$order_total_price;
		$price_rule = DpdGroupShipment::getPriceRule($cart_total, $id_method, $id_address_delivery, $is_cod_method);
		$additional_shipping_cost = $this->calculateAdditionalShippingCost($cart, $products);
		$additional_shipping_cost = Tools::convertPrice($additional_shipping_cost);
		$handling_charges = $carrier->shipping_handling ? Configuration::get('PS_SHIPPING_HANDLING') : 0;
		$handling_charges = Tools::convertPrice($handling_charges);
		$price = false;

		switch ($configuration->price_calculation_method)
		{
			case DpdGroupConfiguration::PRICE_CALCULATION_PRESTASHOP:
				$price = $this->getPriceByPrestaShopCalculationType($carrier_shipping_method, $carrier, $total_weight, $zone, $additional_shipping_cost,
					$handling_charges, $is_cod_method, $order_total_price, $configuration, $price_rule);
				break;
			case DpdGroupConfiguration::PRICE_CALCULATION_WEB_SERVICES:
				$price = $this->getPriceByWebServicesCalculationType($cart, $is_cod_method, $order_total_price, $id_method, $id_address_delivery,
					$additional_shipping_cost, $handling_charges, $configuration, $price_rule, $products);
				break;
			case DpdGroupConfiguration::PRICE_CALCULATION_CSV:
				$price = $this->getPriceByCSVCalculationType($price_rule, $order_total_price, $additional_shipping_cost, $handling_charges,
					$is_cod_method, $configuration);
				break;
		}

		self::$carriers[$this->id_carrier][$cache_key] = $price;
		return self::$carriers[$this->id_carrier][$cache_key];
	}

	private function getPriceByPrestaShopCalculationType($carrier_shipping_method, Carrier $carrier, $cart_total, $zone, $additional_shipping_cost,
		$handling_charges, $is_cod_method, $order_total_price, DpdGroupConfiguration $configuration, $price_rule)
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
				$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART ?
					$order_total_price : $order_total_price + $shipping_price_with_charges;
				$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'],
					$price_rule['cod_min_surcharge'], $price_rule['currency']);
			}
		}

		return $shipping_price_with_charges + $cod_price;
	}

	private function getPriceByWebServicesCalculationType(Cart $cart, $is_cod_method, $order_total_price, $id_method, $id_address_delivery,
		$additional_shipping_cost, $handling_charges, DpdGroupConfiguration $configuration, $price_rule, $products)
	{
		$shipment = new DpdGroupShipment;
		$cart_products = empty($products) ? $cart->getProducts() : $products;

		if (!self::$parcels)
			self::$parcels = $shipment->putProductsToParcels($cart_products);

		$extra_params = array();

		if ($is_cod_method)
			$extra_params['cod'] = array(
				'total_paid'        => $order_total_price,
				'currency_iso_code' => $this->context->currency->iso_code,
				'reference'         => (int)$this->context->cart->id
			);

		$product_names = '';

		if (count($cart_products))
			foreach ($cart_products as $product)
				$product_names .= '|'.$product['name'];

		$extra_params['highInsurance'] = array(
			'total_paid'        => $order_total_price,
			'currency_iso_code' => $this->context->currency->iso_code,
			'content'         	=> $product_names
		);

		$result = $shipment->calculate($id_method, $id_address_delivery, self::$parcels, null, $extra_params);

		if ($result === false || !isset($result['price']) || !isset($result['id_currency']))
			return false;

		$result_price_in_default_currency = Tools::convertPrice($result['price'], new Currency((int)$result['id_currency']), false);
		$result['price'] = Tools::convertPrice($result_price_in_default_currency);
		$result['price'] += $additional_shipping_cost + $handling_charges;

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
				$percentage_starting_price = $configuration->cod_percentage_calculation ==
				DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $result['price'];
				$result['price'] += $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'],
					$price_rule['cod_min_surcharge'], $price_rule['currency']);
			}
		}

		return $result['price'];
	}

	private function getPriceByCSVCalculationType($price_rule, $order_total_price, $additional_shipping_cost, $handling_charges, $is_cod_method,
		$configuration)
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
				$percentage_starting_price = $configuration->cod_percentage_calculation ==
				DpdGroupConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $shipping_price_with_charges;
				$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'],
					$price_rule['cod_min_surcharge'], $price_rule['currency']);
			}
		}

		return $shipping_price_with_charges + $cod_price;
	}

	/**
	 * Check if COD carrier can be visible in checkout pages
	 * depending on customer delivery address country and selected currency
	 *
	 * @param Cart $cart
	 * @param DpdGroupConfiguration $configuration
	 * @param $id_customer_country
	 * @return bool
	 */
	private function isCODCarrierAvailable(Cart $cart, DpdGroupConfiguration $configuration, $id_customer_country)
	{
		if ($configuration->dpd_country_select == DpdGroupConfiguration::OTHER_COUNTRY)
			return true;

		$customer_country_iso_code = Country::getIsoById((int)$id_customer_country);

		if ($configuration->dpd_country_select != $customer_country_iso_code)
			return false;

		require_once(_DPDGROUP_CONTROLLERS_DIR_.'Configuration.controller.php');

		$configuration_controller = new DpdGroupConfigurationController();
		$configuration_controller->setAvailableCountries();
		$sender_currency = '';

		if (isset($configuration_controller->countries[$configuration->dpd_country_select]['currency']))
			$sender_currency = $configuration_controller->countries[$configuration->dpd_country_select]['currency'];

		if (!$sender_currency)
			return false;

		$id_cart_currency = (int)$cart->id_currency;
		$cart_currency = Currency::getCurrency((int)$id_cart_currency);

		if ($sender_currency != $cart_currency['iso_code'])
			return false;

		return true;
	}

	private function getCacheKey(Cart $cart, $products)
	{
		if (empty($products))
			$products = $cart->getProducts();

		$cache_key = '';

		foreach ($products as $product)
			for ($i = 0; $i < $product['cart_quantity']; $i++)
				$cache_key .= $product['id_product'].'_'.$product['id_product_attribute'].';';

		return $cache_key;
	}

	/**
	 * Get order delivery address ID by cart product
	 *
	 * @param array $products
	 * @return int
	 */
	private function getIdAddressDeliveryByProducts(array $products)
	{
		$return = 0;

		foreach ($products as $product)
		{
			if (isset($product['id_address_delivery']))
			{
				$return = (int)$product['id_address_delivery'];
				break;
			}
		}

		return (int)$return;
	}

	/**
	 * Calculate additional shipping price sum of each product in order cart
	 *
	 * @param Cart $cart
	 * @param $products
	 * @return float
	 */
	private function calculateAdditionalShippingCost(Cart $cart, $products)
	{
		$additional_shipping_price = 0;
		$cart_products = empty($products) ? $cart->getProducts() : $products;

		foreach ($cart_products as $product)
			$additional_shipping_price += (int)$product['cart_quantity'] * (float)$product['additional_shipping_cost'];

		return (float)$additional_shipping_price;
	}

	/**
	 * Convert price from given currency to current context currency
	 *
	 * @param $price
	 * @param $iso_code_currency
	 * @return float|int converted price without currency sign
	 * @internal param float $price - price which will be converted from given currency
	 * @internal param string $iso_code_currency - iso code of given currency
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
	 * @param $order_total_price
	 * @param $cod_surcharge_percentage
	 * @param $cod_min_surcharge
	 * @param $iso_code_currency
	 * @return float|int (float) calculated price without currency sign
	 * @internal param float $order_total_price - price which will be used to calculate percentage value
	 * @internal param float $cod_surcharge_percentage - percentage factor
	 * @internal param float $cod_min_surcharge - price in given currency which will be used as minimum value
	 * @internal param string $iso_code_currency - iso code of given currency
	 */
	private function calculateCODSurchargePercentage($order_total_price, $cod_surcharge_percentage, $cod_min_surcharge, $iso_code_currency)
	{
		$surcharge_percentage = $order_total_price * $cod_surcharge_percentage / 100;
		if ($cod_min_surcharge !== '' &&
			($min_surcharge = $this->convertPriceByCurrency($cod_min_surcharge, $iso_code_currency)) > $surcharge_percentage)
			$surcharge_percentage = $min_surcharge;

		return $surcharge_percentage;
	}
}