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
	
class DpdGeopostParcel extends DpdGeopostObjectModel
{
	public $id_parcel;
	
	public $id_order;
	
	public $parcelReferenceNumber;
	
	public $id_product;
	
	public $id_product_attribute;
	
	public $date_add;
	
	public $date_upd;
	
	public static $definition = array(
		'table' => _DPDGEOPOST_PARCEL_DB_,
		'primary' => 'id_parcel',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_order'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'parcelReferenceNumber'	=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'id_product'			=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_product_attribute'	=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add'				=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd'				=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);
	
	public static function getOrderParcels($id_order)
	{
		return Db::getInstance()->executeS('
			SELECT `parcelReferenceNumber`, `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.pSQL(self::$definition['table']).'`
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
					if ($product_in_parcel['id_product'] == $product['id_product'] && $product_in_parcel['id_product_attribute'] == $product['id_product_attribute'])
					{
						$product['parcelReferenceNumber'] = $product_in_parcel['parcelReferenceNumber'];
						unset($products_in_parcels[$key]);
						break;
					}
				}
			}
		}
		
		/* adds total parcel weights */
		foreach ($products as $key => &$product)
		{
			$product['parcel_weight'] = $product['product_weight'];
			foreach ($products as $key_ => $product_)
				if ($key != $key_ && $product['parcelReferenceNumber'] == $product_['parcelReferenceNumber'])
					$product['parcel_weight'] = $product['parcel_weight'] + $product_['product_weight'];
		}
	}
	
	public static function clearOrderParcels($id_order)
	{
		return 	Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.pSQL(self::$definition['table']).'`
			WHERE `id_order`='.(int)$id_order
		);
	}
}