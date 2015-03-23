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

class DpdGeopostDpdPostcodeAddress extends ObjectModel
{
	/* @var integer address id which postcode cached data belongs to */
	public $dpd_postcode_id = null;

	public $id_address = null;

	/** @var string * */
	public $hash;

	/** @var string Company (optional) */
	public $auto_postcode;

	/** @var string is postcode relevant or not for the address */
	public $relevance;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table'   => _DPDGEOPOST_ADDRESS_DB_,
		'primary' => 'dpd_postcode_id',
		'fields'  => array(
			'dpd_postcode_id'	=> array('type' => self::TYPE_INT),
			'id_address'    	=> array('type' => self::TYPE_INT),
			'hash'          	=> array('type' => self::TYPE_STRING),
			'auto_postcode' 	=> array('type' => self::TYPE_STRING),
			'relevance'     	=> array('type' => self::TYPE_STRING),
			'date_add'      	=> array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
			'date_upd'      	=> array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
		),
	);

	public function loadDpdAddressByAddressId($id_address)
	{
		$sql = 'SELECT `dpd_postcode_id`, `id_address`, `hash`, `auto_postcode`, `relevance`, `date_add`, `date_upd`
			FROM `'._DB_PREFIX_._DPDGEOPOST_ADDRESS_DB_.'`
			WHERE `id_address` = "'.(int)$id_address.'"';

		if ($object_datas_lang = Db::getInstance()->executeS($sql))
			foreach ($object_datas_lang as $row)
				foreach ($row as $key => $value)
					if (array_key_exists($key, $this))
						$this->{$key} = $value;

		return $this;
	}
}