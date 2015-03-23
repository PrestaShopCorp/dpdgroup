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


class DpdGeopostService
{
	const IMG_DIR = 'DPD_services';
	const IMG_EXTENTION = 'jpg';

	const CONTINENT_EUROPE = 1;
	const CONTINENT_NORTH_AMERICA = 0;
	const CONTINENT_ASIA = 0;
	const CONTINENT_AFRICA = 0;
	const CONTINENT_OCEANIA = 0;
	const CONTINENT_SOUTH_AMERICA = 0;
	const CONTINENT_EUROPE_EU = 1;
	const CONTINENT_CENTRAL_AMERICA = 0;

	protected $module_instance;
	protected $continents;

	public function __construct()
	{
		$this->module_instance = Module::getInstanceByName('dpdgeopost');
		$this->continents = array(
			'1' => self::CONTINENT_EUROPE,
			'2' => self::CONTINENT_NORTH_AMERICA,
			'3' => self::CONTINENT_ASIA,
			'4' => self::CONTINENT_AFRICA,
			'5' => self::CONTINENT_OCEANIA,
			'6' => self::CONTINENT_SOUTH_AMERICA,
			'7' => self::CONTINENT_EUROPE_EU,
			'8' => self::CONTINENT_CENTRAL_AMERICA,
		);
	}

	public static function install($carrier_type, $carrier_name)
	{
		$id_carrier = (int)Configuration::get($carrier_type);

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

		$service = new DpdGeopostService();

		$carrier = new Carrier();
		$carrier->name = $carrier_name;
		$carrier->active = 1;
		$carrier->is_free = 0;
		$carrier->shipping_handling = 1;
		$carrier->shipping_external = 1;
		$carrier->shipping_method = 1;
		$carrier->max_width = 0;
		$carrier->max_height = 0;
		$carrier->max_weight = 0;
		$carrier->grade = 0;
		$carrier->is_module = 1;
		$carrier->need_range = 1;
		$carrier->range_behavior = 1;
		$carrier->external_module_name = $service->module_instance->name;
		$carrier->url = _DPDGEOPOST_TRACKING_URL_;

		$delay = array();

		foreach (Language::getLanguages(false) as $language)
			$delay[$language['id_lang']] = $carrier_name;

		$carrier->delay = $delay;

		if (!$carrier->save())
			return false;

		$dpdgeopost_carrier = new DpdGeopostCarrier();
		$dpdgeopost_carrier->id_carrier = (int)$carrier->id;
		$dpdgeopost_carrier->id_reference = (int)$carrier->id;

		if (!$dpdgeopost_carrier->save())
			return false;

		foreach ($service->continents as $continent => $value)
			if ($value && !$carrier->addZone($continent))
				return false;

		$groups = array();

		foreach (Group::getGroups((int)Context::getContext()->language->id) as $group)
			$groups[] = $group['id_group'];

		if (version_compare(_PS_VERSION_, '1.5.5', '<'))
		{
			if (!self::setGroups14((int)$carrier->id, $groups))
				return false;
		}
		else
			if (!$carrier->setGroups($groups))
				return false;

		if (!Configuration::updateValue($carrier_type, (int)$carrier->id))
			return false;

		return true;
	}

	public static function deleteCarrier($carrier_type)
	{
		$id_carrier = (int)Configuration::get($carrier_type);

		if (!$id_carrier)
			return true;

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$id_carrier);
			$carrier = new Carrier((int)$id_carrier);
		}
		else
			$carrier = Carrier::getCarrierByReference($id_carrier);

		if (!Validate::isLoadedObject($carrier))
			return true;

		if ($carrier->deleted)
			return true;

		$carrier->deleted = 1;

		return (bool)$carrier->save();
	}

	protected static function setGroups14($id_carrier, $groups)
	{
		foreach ($groups as $id_group)
			if (!Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'carrier_group`
					(`id_carrier`, `id_group`)
				VALUES
					("'.(int)$id_carrier.'", "'.(int)$id_group.'")
			'))
				return false;

		return true;
	}
}