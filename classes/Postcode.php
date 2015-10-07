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

class DpdGroupPostcode extends DpdGroupObjectModel
{
	public $id_postcode;
	public $postcode;
	public $region;
	public $city;
	public $address;

	public static $available_order_by = array(
		'postcode', 'region', 'city', 'address'
	);

	public static $available_order_way = array(
		'asc', 'desc'
	);

	const COLUMN_ID_POSTCODE	= 0;
	const COLUMN_POSTCODE 		= 1;
	const COLUMN_REGION 		= 2;
	const COLUMN_CITY 			= 3;
	const COLUMN_ROAD_TYPE		= 4;
	const COLUMN_ADDRESS 		= 5;
	const COLUMN_D_DEPO			= 6;
	const COLUMN_D_SORT			= 7;
	const COLUMN_ZONE 			= 8;
	const COLUMN_SATURDAY 		= 9;
	const COLUMN_ROUTE			= 10;

	const CSV_POSTCODE_FILE		= 'DPD_GROUP_POSTCODE_FILE';
	const DEFAULT_ORDER_BY 		= 'id_postcode';
	const DEFAULT_ORDER_WAY 	= 'asc';

	public static $definition = array(
		'table' => _DPDGROUP_POSTCODE_DB_,
		'primary' => 'id_postcode',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_postcode'	=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'postcode'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'region'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'city'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'address'		=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
		)
	);

	public static function getAllData($filter = '', $limit = '', $order_by = '', $order_way = '')
	{
		if (!in_array($order_by, self::$available_order_by))
			$order_by = self::DEFAULT_ORDER_BY;

		if (!in_array($order_way, self::$available_order_way))
			$order_way = self::DEFAULT_ORDER_WAY;

		return DB::getInstance()->executeS('
			SELECT `id_postcode`, `postcode`, `region`, `city`, `address`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			'.$filter
			.'ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way)
			.$limit
		);
	}

	public static function deleteAllData()
	{
		return DB::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
		');
	}

	public static function getCSVData()
	{
		return DB::getInstance()->executeS('
			SELECT `id_postcode`, `postcode`, `region`, `city`, `address`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
		');
	}
}