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


class DpdGeopostCarrier10CODService extends DpdGeopostService
{
	const FILENAME = 'dpd_10_cod.service';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public static function install()
	{
		$id_carrier = (int)Configuration::get(DpdGeopostConfiguration::CARRIER_10_COD_ID);
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$id_carrier);
			$carrier = new Carrier((int)$id_carrier);
		}
		else
			$carrier = Carrier::getCarrierByReference($id_carrier);
		
		if ($id_carrier && Validate::isLoadedObject($carrier))
			if (!$carrier->deleted)
				return true;
			else
			{
				$carrier->deleted = 0;
				return (bool)$carrier->save();
			}
		
		$carrier_10_cod_service = new DpdGeopostCarrier10CODService();
		
		$carrier = new Carrier();
		$carrier->name = $carrier_10_cod_service->module_instance->l('DPD 10:00 + COD', self::FILENAME);
		$carrier->active = 1;
		$carrier->is_free = 0;
		$carrier->shipping_handling = 1;
		$carrier->shipping_external = 1;
		$carrier->shipping_method = 1;
		$carrier->max_width = 0;
		$carrier->max_height = 0;
		$carrier->max_height = 0;
		$carrier->max_weight = 0;
		$carrier->grade = 0;
		$carrier->is_module = 1;
		$carrier->need_range = 1;
		$carrier->range_behavior = 1;
		$carrier->external_module_name = $carrier_10_cod_service->module_instance->name;
		$carrier->url = _DPDGEOPOST_TRACKING_URL_;
		
		$delay = array();
		foreach (Language::getLanguages(false) as $language)
			$delay[$language['id_lang']] = $carrier_10_cod_service->module_instance->l('DPD 10:00 + COD', self::FILENAME);
		$carrier->delay = $delay;
		
		if (!$carrier->save())
			return false;
		
		$dpdgeopost_carrier = new DpdGeopostCarrier();
		$dpdgeopost_carrier->id_carrier = (int)$carrier->id;
		$dpdgeopost_carrier->id_reference = (int)$carrier->id;
		
		if (!$dpdgeopost_carrier->save())
			return false;
		
		if (!copy(_DPDGEOPOST_IMG_DIR_.DpdGeopostCarrier10CODService::IMG_DIR.'/'._DPDGEOPOST_10_ID_.'.'.DpdGeopostCarrier10CODService::IMG_EXTENTION, _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
			return false;
		
		foreach ($carrier_10_cod_service->continents as $continent => $value)
			if ($value && !$carrier->addZone($continent))
				return false;
		
		$groups = array();
		foreach (Group::getGroups((int)Context::getContext()->language->id) as $group)
			$groups[] = $group['id_group'];
		
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			if (!self::setGroups14((int)$carrier->id, $groups))
				return false;
		}
		else
			if (!$carrier->setGroups($groups))
				return false;
		
		if (!Configuration::updateValue(DpdGeopostConfiguration::CARRIER_10_COD_ID, (int)$carrier->id))
			return false;
		
		return true;
	}
	
	public static function delete()
	{
		return (bool)self::deleteCarrier((int)Configuration::get(DpdGeopostConfiguration::CARRIER_10_COD_ID));
	}
}