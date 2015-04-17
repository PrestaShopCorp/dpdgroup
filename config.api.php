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

/* URI constants */

if (!defined('_DPDGROUP_MODULE_URI_'))
	define('_DPDGROUP_MODULE_URI_', _MODULE_DIR_.'dpdgroup/');

if (!defined('_DPDGROUP_CSS_URI_'))
	define('_DPDGROUP_CSS_URI_', _DPDGROUP_MODULE_URI_.'views/css/');

if (!defined('_DPDGROUP_JS_URI_'))
	define('_DPDGROUP_JS_URI_', _DPDGROUP_MODULE_URI_.'views/js/');

if (!defined('_DPDGROUP_IMG_URI_'))
	define('_DPDGROUP_IMG_URI_', _DPDGROUP_MODULE_URI_.'views/img/');

if (!defined('_DPDGROUP_AJAX_URI_'))
	define('_DPDGROUP_AJAX_URI_', _DPDGROUP_MODULE_URI_.'dpdgroup.ajax.php');

if (!defined('_DPDGROUP_PDF_URI_'))
	define('_DPDGROUP_PDF_URI_', _DPDGROUP_MODULE_URI_.'dpdgroup.pdf.php');

/* Directories paths constants */

if (!defined('_DPDGROUP_MODULE_DIR_'))
	define('_DPDGROUP_MODULE_DIR_', _PS_MODULE_DIR_.'dpdgroup/');

if (!defined('_DPDGROUP_SQL_DIR_'))
	define('_DPDGROUP_SQL_DIR_', _DPDGROUP_MODULE_DIR_.'sql/');

if (!defined('_DPDGROUP_CLASSES_DIR_'))
	define('_DPDGROUP_CLASSES_DIR_', _DPDGROUP_MODULE_DIR_.'classes/');

if (!defined('_DPDGROUP_TPL_DIR_'))
	define('_DPDGROUP_TPL_DIR_', _DPDGROUP_MODULE_DIR_.'views/templates/');

if (!defined('_DPDGROUP_CONTROLLERS_DIR_'))
	define('_DPDGROUP_CONTROLLERS_DIR_', _DPDGROUP_MODULE_DIR_.'controllers/');

if (!defined('_DPDGROUP_IMG_DIR_'))
	define('_DPDGROUP_IMG_DIR_', _DPDGROUP_MODULE_DIR_.'views/img/');

/* Database tables constants */

if (!defined('_DPDGROUP_CSV_DB_'))
	define('_DPDGROUP_CSV_DB_', 'dpdgroup_price_rules');

if (!defined('_DPDGROUP_CARRIER_DB_'))
	define('_DPDGROUP_CARRIER_DB_', 'dpdgroup_carrier');

if (!defined('_DPDGROUP_SHIPMENT_DB_'))
	define('_DPDGROUP_SHIPMENT_DB_', 'dpdgroup_shipment');

if (!defined('_DPDGROUP_PARCEL_DB_'))
	define('_DPDGROUP_PARCEL_DB_', 'dpdgroup_parcel');

if (!defined('_DPDGROUP_REFERENCE_DB_'))
	define('_DPDGROUP_REFERENCE_DB_', 'dpdgroup_reference');

if (!defined('_DPDGROUP_POSTCODE_DB_'))
	define('_DPDGROUP_POSTCODE_DB_', 'dpdgroup_postcode');

if (!defined('_DPDGROUP_ADDRESS_DB_'))
	define('_DPDGROUP_ADDRESS_DB_', 'dpdgroup_address');

/* DPD carriers IDs */

if (!defined('_DPDGROUP_CLASSIC_ID_'))
	define('_DPDGROUP_CLASSIC_ID_', 1);

if (!defined('_DPDGROUP_10_ID_'))
	define('_DPDGROUP_10_ID_', 10);

if (!defined('_DPDGROUP_12_ID_'))
	define('_DPDGROUP_12_ID_', 9);

if (!defined('_DPDGROUP_SAME_DAY_ID_'))
	define('_DPDGROUP_SAME_DAY_ID_', 27);

if (!defined('_DPDGROUP_B2C_ID_'))
	define('_DPDGROUP_B2C_ID_', 109);

if (!defined('_DPDGROUP_INTERNATIONAL_ID_'))
	define('_DPDGROUP_INTERNATIONAL_ID_', 40033);

if (!defined('_DPDGROUP_BULGARIA_ID_'))
	define('_DPDGROUP_BULGARIA_ID_', 40107);

/* ************************ */

if (!defined('_DPDGROUP_CSV_DELIMITER_'))
	define('_DPDGROUP_CSV_DELIMITER_', ';');

if (!defined('_DPDGROUP_CSV_FILENAME_'))
	define('_DPDGROUP_CSV_FILENAME_', 'dpdgroup');

if (!defined('_DPDGROUP_DEFAULT_WEIGHT_UNIT_'))
	define('_DPDGROUP_DEFAULT_WEIGHT_UNIT_', 'kg');

if (!defined('_DPDGROUP_TRACKING_URL_'))
	define('_DPDGROUP_TRACKING_URL_', 'http://tracking.dpd.de/cgi-bin/delistrack?pknr=@');

if (!defined('_DPDGROUP_COOKIE_'))
	define('_DPDGROUP_COOKIE_', 'dpdgroup_cookie');

if (!defined('_DPDGROUP_MAX_ADDRESS_LENGTH_'))
	define('_DPDGROUP_MAX_ADDRESS_LENGTH_', 70);