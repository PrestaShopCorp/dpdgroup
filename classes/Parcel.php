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

class DpdGroupParcel extends DpdGroupObjectModel
{
	public $id_parcel;

	public $id_order;

	public $parcel_reference_number;

	public $id_product;

	public $id_product_attribute;

	public $date_add;

	public $date_upd;

	public static $definition = array(
		'table' => _DPDGROUP_PARCEL_DB_,
		'primary' => 'id_parcel',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_order'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'parcel_reference_number'	=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'id_product'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_product_attribute'	=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add'				=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd'				=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

	public static function getOrderParcels($id_order)
	{
		return Db::getInstance()->executeS('
			SELECT `parcel_reference_number`, `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_._DPDGROUP_PARCEL_DB_.'`
			WHERE `id_order`='.(int)$id_order
		);
	}

	public static function addParcelDataToProducts(&$products, $id_order)
	{
		/* adds parcel references */
		if ($products_in_parcels = self::getOrderParcels($id_order))
		{
			foreach ($products as &$product)
			{
				foreach ($products_in_parcels as $key => $product_in_parcel)
				{
					if (($product_in_parcel['id_product'] == $product['id_product']) &&
						($product_in_parcel['id_product_attribute'] == $product['id_product_attribute']))
					{
						$product['parcel_reference_number'] = $product_in_parcel['parcel_reference_number'];
						unset($products_in_parcels[$key]);
						break;
					}
					else
						$product['parcel_reference_number'] = '';
				}
			}
		}

		/* adds total parcel weights */
		foreach ($products as $key => &$product)
		{
			$product['parcel_weight'] = $product['product_weight'];

			foreach ($products as $key2 => $product2)
				if ($key != $key2 && $product['parcel_reference_number'] == $product2['parcel_reference_number'])
					$product['parcel_weight'] = $product['parcel_weight'] + $product2['product_weight'];
		}
	}

	public static function clearOrderParcels($id_order)
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_._DPDGROUP_PARCEL_DB_.'`
			WHERE `id_order`='.(int)$id_order
		);
	}
}