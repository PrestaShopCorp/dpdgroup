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

$sql = array();

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_CSV_DB_.'` (
		`id_csv` int(11) NOT NULL AUTO_INCREMENT,
		`id_shop` int(11) NOT NULL,
		`date_add` datetime DEFAULT NULL,
		`date_upd` datetime DEFAULT NULL,
		`country` varchar(255) NOT NULL,
		`region` varchar(255) NOT NULL,
		`zip` varchar(255) NOT NULL,
		`weight_from` decimal(20,2) NOT NULL DEFAULT "0",
  		`weight_to` decimal(20,2) NOT NULL DEFAULT "0",
		`shipping_price` varchar(255) NOT NULL,
		`shipping_price_percentage` varchar(255) NOT NULL,
		`currency` varchar(255) NOT NULL,
		`method_id` varchar(11) NOT NULL,
		`cod_surcharge` varchar(255) NOT NULL,
		`cod_surcharge_percentage` varchar(255) NOT NULL,
		`cod_min_surcharge` varchar(255) NOT NULL,
	PRIMARY KEY (`id_csv`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_PARCEL_DB_.'` (
		`id_parcel` int(10) NOT NULL AUTO_INCREMENT,
		`id_order` int(10) NOT NULL,
		`parcel_reference_number` varchar(30) NOT NULL,
		`id_product` int(10) NOT NULL,
		`id_product_attribute` int(10) NOT NULL,
		`date_add` datetime DEFAULT NULL,
		`date_upd` datetime DEFAULT NULL,
	PRIMARY KEY (`id_parcel`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_CARRIER_DB_.'` (
		`id_dpd_geopost_carrier` int(10) NOT NULL AUTO_INCREMENT,
		`id_carrier` int(10) NOT NULL,
		`id_reference` int(10) NOT NULL,
		`date_add` datetime NOT NULL,
		`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_dpd_geopost_carrier`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_SHIPMENT_DB_.'` (
		`id_shipment` int(10) NOT NULL,
		`id_order` int(10) NOT NULL,
		`shipment_reference` VARCHAR(45) NULL DEFAULT NULL,
		`id_manifest` int(10) NOT NULL DEFAULT "0",
		`reference` varchar('.DpdGroupManifest::REFERENCE_LENGTH.') NOT NULL,
		`label_printed` int(1) NOT NULL DEFAULT "0",
		`date_pickup` datetime DEFAULT NULL,
	PRIMARY KEY (`id_order`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_REFERENCE_DB_.'` (
		`id_order` int(10) NOT NULL,
		`reference` varchar('.DpdGroupManifest::REFERENCE_LENGTH.') NOT NULL,
	PRIMARY KEY (`id_order`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] =
	'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` (
		`id_postcode` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`postcode` varchar(10) NOT NULL,
		`region` varchar(50) NULL,
		`city` varchar(20) NOT NULL,
		`address` varchar(100) NULL,
	PRIMARY KEY (`id_postcode`),
	UNIQUE
	KEY `postcode_2` (`postcode`, `region`, `city`, `address`),
	KEY `postcode` (`postcode`),
	KEY `city` (`city`),
	KEY `region` (`region`),
	KEY `region_2` (`region`, `city`),
	KEY `address` (`address`),
	KEY `region_3` (`region`, `city`, `address`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$postcodes = Tools::file_get_contents(_DPDGROUP_SQL_DIR_.'data.sql');
$postcodes = str_replace('zitec_dpd_postcodes', _DB_PREFIX_._DPDGROUP_POSTCODE_DB_, $postcodes);
$postcodes = explode(';', $postcodes);

foreach ($postcodes as $query)
	if ($query)
		$sql[] = $query.';';

$database_table_install_error = false; /* must be defined to avoid PrestaShop validator error */
$price_rules_data_intall_error = false; /* must be defined to avoid PrestaShop validator error */

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'` (
		`dpd_postcode_id` int(11) DEFAULT NULL AUTO_INCREMENT ,
		`id_address` int(11) NOT NULL,
		`hash` varchar(100) NULL,
		`auto_postcode` varchar(6) NULL,
		`relevance` int(2) NULL,
		`date_add` datetime DEFAULT NULL,
		`date_upd` datetime DEFAULT NULL,
	PRIMARY KEY (`dpd_postcode_id`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TRIGGER dpd_trigger_update_address AFTER UPDATE ON '._DB_PREFIX_.'address
	FOR EACH ROW  UPDATE '._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'
	SET
		'._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'.auto_postcode = NEW.postcode,
		'._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'.relevance = 1
	WHERE '._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'.id_address = NEW.id_address
		AND '._DB_PREFIX_._DPDGROUP_ADDRESS_DB_.'.dpd_postcode_id > 0';

foreach ($sql as $query)
	if (!$database_table_install_error)
		if (Db::getInstance()->execute($query) == false)
			$database_table_install_error = true;

if (!$database_table_install_error)
{
	$shops = version_compare(_PS_VERSION_, '1.5', '<') ? array('1' => 1) : Shop::getShops();
	$current_date = date('Y-m-d H:i:s');
	$currency = Currency::getDefaultCurrency();

	foreach (array_keys($shops) as $id_shop)
	{
		if (!$price_rules_data_intall_error)
		{
			$sql = '
			INSERT INTO `'._DB_PREFIX_._DPDGROUP_CSV_DB_.'`
				(`id_shop`, `date_add`, `date_upd`, `country`, `region`, `zip`, `weight_from`, `weight_to`, `shipping_price`,
				`currency`, `method_id`)
			VALUES
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_CLASSIC_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_12_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_10_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_SAME_DAY_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_B2C_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_INTERNATIONAL_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "'.(int)_DPDGROUP_BULGARIA_ID_.'"),
				("'.(int)$id_shop.'", "'.pSQL($current_date).'", "'.pSQL($current_date).'", "*", "*", "*", "0", "0.5", "0",
					"'.pSQL($currency->iso_code).'", "*")
			';

			if (!Db::getInstance()->execute($sql))
				$price_rules_data_intall_error = true;
		}
	}
}