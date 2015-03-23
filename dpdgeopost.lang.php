<?php
/**
* 2014 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
*  @copyright 2014 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

class DpdGeopostLanguage
{
	const FILENAME = 'dpdgeopost.lang';

	private $translations = array();
	private $module_instance;

	public function __construct()
	{
		$this->module_instance = Module::getInstanceByName('dpdgeopost');

		$this->translations = array(
			'50052' => $this->module_instance->l('50052 - All shipments\' address must be same to close manifest', self::FILENAME),
			'50053' => $this->module_instance->l('50053 - Manifest couldn\'t be found.', self::FILENAME),
			'50054' => $this->module_instance->l('50054 - shipmentManifestList cannot have same reference', self::FILENAME),
			'50055' => $this->module_instance->l('50055 - Your shipment is created successfully. However, You don\'t have an available pickup order for this shipment in today.', self::FILENAME),
			'50056' => $this->module_instance->l('50056 - Your shipment is created successfully. However, You don\'t have an available pickup order for this shipment and your pickup order time exceeded for today.', self::FILENAME),
			'50057' => $this->module_instance->l('50057 - You cannot print manifest only. You have unprinted parcel labels in your shipments.', self::FILENAME),
			'50020' => $this->module_instance->l('50020 - Sender Address is invalid, or not active', self::FILENAME),
			'50021' => $this->module_instance->l('50021 - DPD product is not part of the customer contract', self::FILENAME),
			'50022' => $this->module_instance->l('50022 - Payer default legal address couldn\'t be found or not active', self::FILENAME),
			'50023' => $this->module_instance->l('50023 - Receiver Country couldn\'t be found in IT4EM with  param ERROR_WITH', self::FILENAME),
			'50024' => $this->module_instance->l('50024 - Multi parcel shipment(Mps) is not allowed to receiver country!', self::FILENAME),
			'50025' => $this->module_instance->l('50025 - Your additional param ERROR_FIELD service(s) is/are not belong to Main Service', self::FILENAME),
			'50026' => $this->module_instance->l('50026 - Additional param ERROR_FIELD service is mandatory or part of main service, but your request doesn\'t have a param ERROR_FIELD service.', self::FILENAME),
			'50044' => $this->module_instance->l('50044 - Pickup order date is holiday', self::FILENAME),
			'50045' => $this->module_instance->l('50045 - Pickup order working unit not found', self::FILENAME),
			'50046' => $this->module_instance->l('50046 - Pickup order destination country param COUNTRY_CODE not found', self::FILENAME),
			'50047' => $this->module_instance->l('50047 - Pickup order product param PROD_ID not found', self::FILENAME),
			'50100' => $this->module_instance->l('50100 - Param CLASS_NAME - param FIELD_NAME cannot be empty.', self::FILENAME),
			'50101' => $this->module_instance->l('50101 - Param CLASS_NAME - param FIELD_NAME length must be between param MIN and param MAX.', self::FILENAME),
			'50102' => $this->module_instance->l('50102 - Param CLASS_NAME - param FIELD_NAME minimum value can be param VALUE.', self::FILENAME),
			'50103' => $this->module_instance->l('50103 - Param CLASS_NAME - param FIELD_NAME maximum value can be param VALUE.', self::FILENAME),
			'50104' => $this->module_instance->l('50104 - Param CLASS_NAME - param FIELD_NAME cannot be equal to param VALUE.', self::FILENAME),
			'50105' => $this->module_instance->l('50105 - Param CLASS_NAME - param FIELD_NAME cannot be less than or equal to param VALUE.', self::FILENAME),
			'50106' => $this->module_instance->l('50106 - Param CLASS_NAME - param FIELD_NAME is not a valid(YYYYMMDD) date. your value is param VALUE.', self::FILENAME),
			'50108' => $this->module_instance->l('50108 - Param CLASS_NAME - param FIELD_NAME is not a valid(HHMMSS) email. your value is param VALUE.', self::FILENAME),
			'50107' => $this->module_instance->l('50107 - Param CLASS_NAME - param FIELD_NAME is not a valid(HHMMSS) time. your value is param VALUE.', self::FILENAME),
			'50050' => $this->module_instance->l('50050 - Manifest is closed before.', self::FILENAME),
			'50051' => $this->module_instance->l('50051 - shipmentReferenceList param ORDER_ID, shipment is used another manifest before. shipmentReferenceNumber: param SHIPMENT_REFERENCE', self::FILENAME),
			'50027' => $this->module_instance->l('50027 - Destination depot couldn\'t be found for Receiver Country: param COUNTRY_CODE_ALPHA - param COUNTRY_NAME, ZipCode: param ZIP_CODE, Departure Depot: param DEPOT, Service Code: param SERVICE_CODE', self::FILENAME),
			'50028' => $this->module_instance->l('50028 - Shipment for param PROD_ID : param PROD_NAME is not allowed To Zone: param TO_ZONE_ID, To Zip code: param TO_ZIP_CODE, To Country Id: param TO_COUNTRY_ID!', self::FILENAME),
			'50029' => $this->module_instance->l('50029 - Param PROD_NAME with param DESI_KG Kg is not allowed for selected receiver customer. Allowed Max Kg: param MAX_KG, Allowed Min Kg: param MIN_KG. To Country ID: param TO_COUNTRY_ID, To Zip Code: param TO_ZIP_CODE.', self::FILENAME),
			'50030' => $this->module_instance->l('50030 - Param REFERENCE param REFERENCE_NUMBER is used before!', self::FILENAME),
			'50031' => $this->module_instance->l('50031 - Mandatory fields cannot be empty. Fields: param ERROR_FIELDS', self::FILENAME),
			'50032' => $this->module_instance->l('50032 - Param ERROR_FIELD cannot be empty!', self::FILENAME),
			'50001' => $this->module_instance->l('50001 - Shipment id and shipment reference number both empty', self::FILENAME),
			'50002' => $this->module_instance->l('50002 - Shipment not found', self::FILENAME),
			'50003' => $this->module_instance->l('50003 - Shipment has scan(s)', self::FILENAME),
			'50004' => $this->module_instance->l('50004 - Shipment (shipment id : param SHP_ID, shipment reference number : param SHIPMENT_REF_NUMBER is already closed in a manifest (manifest id : param MANIFEST_ID.', self::FILENAME),
			'50005' => $this->module_instance->l('50005 - Shipment delete failed', self::FILENAME),
			'50006' => $this->module_instance->l('50006 - Parcel id and parcel reference number both empty', self::FILENAME),
			'50007' => $this->module_instance->l('50007 - Parcel (parcel id : param SHP_PARCEL_ID , parcel reference number : param PARCEL_REF_NUMBER not found', self::FILENAME),
			'50008' => $this->module_instance->l('50008 - Parcel (parcel id : param SHP_PARCEL_ID , parcel reference number : param PARCEL_REF_NUMBER not printed', self::FILENAME),
			'50009' => $this->module_instance->l('50009 - Pickup order id and reference number both empty', self::FILENAME),
			'50010' => $this->module_instance->l('50010 - Pickup order not found', self::FILENAME),
			'50011' => $this->module_instance->l('50011 - Desired pickup date and time empty', self::FILENAME),
			'50012' => $this->module_instance->l('50012 - Pickup start date time exceeded', self::FILENAME),
			'50013' => $this->module_instance->l('50013 - Pickup date time exceeded to cancel', self::FILENAME),
			'50033' => $this->module_instance->l('50033 - senderAddressId is not belong to sender\'s pickup address', self::FILENAME),
			'50034' => $this->module_instance->l('50034 - Param ERROR_FIELD is/are invalid', self::FILENAME),
			'50035' => $this->module_instance->l('50035 - startDate cannot be greater than endDate', self::FILENAME),
			'50014' => $this->module_instance->l('50014 - Pickup order not allowed to param DESTINATION_COUNTRY country with service code param PROD_ID', self::FILENAME),
			'50015' => $this->module_instance->l('50015 - Pickup order from time must be at least 15 minutes later from now and in defined time range. Please contact your sales representative if you are unsure.', self::FILENAME),
			'50016' => $this->module_instance->l('50016 - Pickup order product param PROD_ID to time exceeded', self::FILENAME),
			'50017' => $this->module_instance->l('50017 - Pickup order from time and to time must be at least param INTERVAL minutes interval', self::FILENAME),
			'50018' => $this->module_instance->l('50018 - Pickup order from time wrong for Product param PROD_ID', self::FILENAME),
			'50019' => $this->module_instance->l('50019 - Pickup order to time wrong for Product param PROD_ID', self::FILENAME),
			'50040' => $this->module_instance->l('50040 - Pickup order for work area, parcel weight cannot be bigger than param MAX_KG kg', self::FILENAME),
			'50041' => $this->module_instance->l('50041 - Pickup order not allowed for work area at this date', self::FILENAME),
			'50042' => $this->module_instance->l('50042 - Pickup order from time wrong for work area', self::FILENAME),
			'50043' => $this->module_instance->l('50043 - Pickup order to time wrong for work area', self::FILENAME),
			'16001' => $this->module_instance->l('16001 - Price is not defined for used service in customer\'s contract', self::FILENAME)
		);
	}

	public function getTranslation($id_translation)
	{
		return isset($this->translations[(int)$id_translation]) ? $this->translations[(int)$id_translation] : '';
	}
}