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

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

$module_instance = Module::getInstanceByName('dpdgeopost');
$filename = 'dpdgeopost.ajax';

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) exit;

if (Tools::isSubmit('testConnectivity'))
{
	require_once(_DPDGEOPOST_CLASSES_DIR_.'configuration.controller.php');
	$configuration_controller = new DpdGeopostConfigurationController();
	$error_message = $configuration_controller->testConnectivity();
	die($error_message ? $error_message : true);
}

if (Tools::isSubmit('calculatePrice'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));
	$price = $shipment->calculatePriceForOrder((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'));

	die(Tools::jsonEncode(array(
		'price'  => $price ? (float)$price : '---',
		'error'  => reset(DpdGeopostShipment::$errors),
		'notice' => reset(DpdGeopostShipment::$notices)
	)));
}

if (Tools::isSubmit('saveShipment'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

	$message = $shipment->save((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'), Tools::getValue('parcels'));
	
	if ($message && !DpdGeopostShipment::$errors)
		$module_instance->addFlashMessage($message);

	$error_messages = '';
	if (DpdGeopostShipment::$errors)
		foreach (DpdGeopostShipment::$errors as $error)
			$error_messages .= $error.'<br />';

	die(Tools::jsonEncode(array(
		'error' => $message && !$error_messages ? null : $error_messages
	)));
}

if (Tools::isSubmit('deleteShipment'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

	if ($result = $shipment->delete())
		$module_instance->addFlashMessage($module_instance->l('Shipment successfully deleted', $filename));

	die(Tools::jsonEncode(array(
		'error' => $result ? null : reset(DpdGeopostShipment::$errors)
	)));
}

if (Tools::isSubmit('arrangePickup'))
{
	$pickup_data = Tools::getValue('dpdgeopost_pickup_data');

	$pickup = new DpdGeopostPickup;
	$pickup->id_shipment = Tools::getValue('shipmentIds');
	$pickup->date = isset($pickup_data['date']) ? pSQL($pickup_data['date']) : null;
	$pickup->fromTime = isset($pickup_data['fromTime']) ? pSQL($pickup_data['fromTime']) : null;
	$pickup->toTime = isset($pickup_data['toTime']) ? pSQL($pickup_data['toTime']) : null;
	$pickup->contactEmail = isset($pickup_data['contactEmail']) ? pSQL($pickup_data['contactEmail']) : null;
	$pickup->contactName = isset($pickup_data['contactName']) ? pSQL($pickup_data['contactName']) : null;
	$pickup->contactPhone = isset($pickup_data['contactPhone']) ? pSQL($pickup_data['contactPhone']) : null;
	$pickup->specialInstruction = isset($pickup_data['specialInstruction']) ? pSQL($pickup_data['specialInstruction']) : null;
	$pickup->referenceNumber = Tools::passwdGen();

	if ($result = $pickup->arrange())
		$module_instance->addFlashMessage(
			sprintf($module_instance->l('Pickup successfully arranged at %s %s - %s', $filename), $pickup->date, $pickup->fromTime, $pickup->toTime)
		);

	die(Tools::jsonEncode(array(
		'error' => $result ? null : reset(DpdGeopostShipment::$errors)
	)));
}

if (Tools::isSubmit('downloadModuleCSVSettings'))
{
	include_once(dirname(__FILE__).'/classes/csv.controller.php');
	$controller = new DpdGeopostCSVController;
	$controller->generateCSV();
}