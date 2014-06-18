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
include_once(dirname(__FILE__).'/dpdgeopost.php'); /*module core*/

$module_instance = new DpdGeopost;

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) exit;

if (Tools::isSubmit('printLabels'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));
	
	$pdf_file_contents = $shipment->getLabelsPdf();
	
	if ($pdf_file_contents)
	{
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="shipment_labels_'.(int)Tools::getValue('id_order').'.pdf"');
		echo $pdf_file_contents;
	}
	else
	{
		echo reset(DpdGeopostShipment::$errors);
		exit;
	}
}