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

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

$module_instance = Module::getInstanceByName('dpdgroup');
$filename = 'dpdgroup.ajax';

if (Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name) || !$module_instance instanceof DpdGroup)
	exit;

if (Tools::isSubmit('testConnectivity'))
{
	require_once(_DPDGROUP_CONTROLLERS_DIR_.'Configuration.controller.php');
	$configuration_controller = new DpdGroupConfigurationController();

	if ((int)Tools::getValue('other_country'))
	{
		$error_message = $configuration_controller->testConnection(
			(int)Tools::getValue('production_mode'),
			Tools::getValue('production_ws_url'),
			Tools::getValue('test_ws_url')
		);
	}
	else
		$error_message = $configuration_controller->validateCountryConfiguration(Tools::getValue('ws_country'));

	die($error_message ? $error_message : true);
}

if (Tools::isSubmit('calculatePrice'))
{
	DpdGroupWS::$parcel_weight_warning_message = false;
	$id_order = (int)Tools::getValue('id_order');
	$shipment = new DpdGroupShipment((int)$id_order);
	$price = $shipment->calculatePriceForOrder((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'));
	$errors = array();

	if (DpdGroupShipment::$errors)
		foreach (DpdGroupShipment::$errors as $error)
			$errors[] = $error;

	$price = $price !== false ? $price : '---';
	$notices = DpdGroupShipment::$notices;
	$shipment = new DpdGroupShipment((int)$id_order);
	$order = new Order((int)$id_order);
	$total_shipping = version_compare(_PS_VERSION_, '1.5', '>=') ? $order->total_shipping_tax_incl : $order->total_shipping;

	if (DpdGroupShipment::$errors)
		foreach (DpdGroupShipment::$errors as $error)
			$errors[] = $error;

	if (!$shipment->id_shipment && !(int)Tools::getValue('method_id'))
		$notices[] = $module_instance->l('Client did not selected DPD shipment, but you can use this shipment method.', $filename);

	if ($price > 0 && $price > $total_shipping)
		$notices[] = $module_instance->l('Shipping costs more than client paid.', $filename);

	die(Tools::jsonEncode(array(
		'price'  => $price,
		'error'  => array_unique($errors),
		'notice' => array_unique($notices),
		'force_enable_button' => (int)DpdGroupWS::$parcel_weight_warning_message
	)));
}

if (Tools::isSubmit('saveShipment'))
{
	$shipment = new DpdGroupShipment((int)Tools::getValue('id_order'));
	$message = $shipment->save((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'), Tools::getValue('parcels'));

	if ($message && !DpdGroupShipment::$errors)
		$module_instance->addFlashMessage($message);

	$error_messages = '';

	if (DpdGroupShipment::$errors)
		foreach (DpdGroupShipment::$errors as $error)
			$error_messages .= $error.'<br />';

	if (DpdGroupShipment::$notices)
		foreach (DpdGroupShipment::$notices as $error)
			$error_messages .= $error.'<br />';

	die(Tools::jsonEncode(array(
		'error' => $message && !$error_messages ? null : $error_messages
	)));
}

if (Tools::isSubmit('deleteShipment'))
{
	$shipment = new DpdGroupShipment((int)Tools::getValue('id_order'));

	if ($result = $shipment->delete())
		$module_instance->addFlashMessage($module_instance->l('Shipment successfully deleted', $filename));

	die(Tools::jsonEncode(array(
		'error' => $result ? null : reset(DpdGroupShipment::$errors)
	)));
}

if (Tools::isSubmit('arrangePickup'))
{
	$pickup_data = Tools::getValue('dpdgroup_pickup_data');
	$pickup = new DpdGroupPickup;
	$pickup->id_shipment = Tools::getValue('shipmentIds');
	$pickup->date = isset($pickup_data['date']) ? $pickup_data['date'] : null;
	$pickup->from_time = isset($pickup_data['from_time']) ? $pickup_data['from_time'] : null;
	$pickup->to_time = isset($pickup_data['to_time']) ? $pickup_data['to_time'] : null;
	$pickup->contact_email = isset($pickup_data['contact_email']) ? $pickup_data['contact_email'] : null;
	$pickup->contact_name = isset($pickup_data['contact_name']) ? $pickup_data['contact_name'] : null;
	$pickup->contact_phone = isset($pickup_data['contact_phone']) ? $pickup_data['contact_phone'] : null;
	$pickup->special_instruction = isset($pickup_data['special_instruction']) ? $pickup_data['special_instruction'] : null;
	$pickup->reference_number = Tools::passwdGen();

	if ($result = $pickup->arrange())
		$module_instance->addFlashMessage(
			sprintf($module_instance->l('Pickup successfully arranged at %s %s - %s', $filename), $pickup->date, $pickup->from_time, $pickup->to_time)
		);

	$shipment_errors = '';

	foreach (DpdGroupShipment::$errors as $error_message)
		$shipment_errors .= $error_message.'<br />';

	foreach (DpdGroupShipment::$notices as $notice)
		$shipment_errors .= $notice.'<br />';

	die(Tools::jsonEncode(array(
		'error' => $result ? null : str_replace('
', '<br />', $shipment_errors)
	)));
}

if (Tools::isSubmit('downloadModuleCSVSettings'))
{
	include_once(dirname(__FILE__).'/controllers/Csv.controller.php');
	$controller = new DpdGroupCSVController;
	$controller->generateCSV();
}

if (Tools::getValue('action') == 'postcode-recommendation')
{
	require_once(_DPDGROUP_CLASSES_DIR_.'PostcodeSearch.php');
	require_once(_DPDGROUP_CLASSES_DIR_.'Address.php');
	require_once(_DPDGROUP_CLASSES_DIR_.'Mysql.php');
	require_once(_DPDGROUP_CLASSES_DIR_.'CachedData.php');
	require_once(_DPDGROUP_CLASSES_DIR_.'PostcodeSearch.php');

	$data = array();
	$address = array(
		'city' => Tools::getValue('city'),
		'country_id' => Tools::getValue('id_country'),
		'region_id' => Tools::getValue('id_state'),
		'lang_id' => (int)Context::getContext()->language->id,
		'address' => Tools::getValue('address1').' '.Tools::getValue('address2')
	);

	$postcode_search = new DpdGroupPostcodeSearch();
	$results = $postcode_search->findAllSimilarAddressesForAddress($address);

	if (!$results)
		die(Tools::jsonEncode($data));

	foreach ((array)$results as $address)
	{
		$data[] = array(
			'label' => ($address['address'] ? $address['address'].', ': '' ).$address['city'].', '.$address['region'],
			'postcode' => $address['postcode']
		);
	}

	die(Tools::jsonEncode($data));
}

if (Tools::getValue('action') == 'validate_postcode')
{
	$psotcode = Tools::getValue('dpdpostcode');
	$result = true;

	require_once(_DPDGROUP_CLASSES_DIR_.'Mysql.php');

	$model = new DpdGroupDpdPostcodeMysql();

	if (!DpdGroupDpdPostcodeMysql::postcodeExistsInDB($psotcode))
		$result = false;

	die(Tools::jsonEncode(array('is_valid' => $result)));
}