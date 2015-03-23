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

if (!defined('_DPDGEOPOST_MODULE_URI_'))
	define('_DPDGEOPOST_MODULE_URI_', _MODULE_DIR_.'dpdgeopost/');

if (!defined('_DPDGEOPOST_CSS_URI_'))
	define('_DPDGEOPOST_CSS_URI_', _DPDGEOPOST_MODULE_URI_.'views/css/');

if (!defined('_DPDGEOPOST_JS_URI_'))
	define('_DPDGEOPOST_JS_URI_', _DPDGEOPOST_MODULE_URI_.'views/js/');

if (!defined('_DPDGEOPOST_IMG_URI_'))
	define('_DPDGEOPOST_IMG_URI_', _DPDGEOPOST_MODULE_URI_.'views/img/');

if (!defined('_DPDGEOPOST_AJAX_URI_'))
	define('_DPDGEOPOST_AJAX_URI_', _DPDGEOPOST_MODULE_URI_.'dpdgeopost.ajax.php');

if (!defined('_DPDGEOPOST_PDF_URI_'))
	define('_DPDGEOPOST_PDF_URI_', _DPDGEOPOST_MODULE_URI_.'dpdgeopost.pdf.php');

/* Directories paths constants */

if (!defined('_DPDGEOPOST_MODULE_DIR_'))
	define('_DPDGEOPOST_MODULE_DIR_', _PS_MODULE_DIR_.'dpdgeopost/');

if (!defined('_DPDGEOPOST_SQL_DIR_'))
	define('_DPDGEOPOST_SQL_DIR_', _DPDGEOPOST_MODULE_DIR_.'sql/');

if (!defined('_DPDGEOPOST_CLASSES_DIR_'))
	define('_DPDGEOPOST_CLASSES_DIR_', _DPDGEOPOST_MODULE_DIR_.'classes/');

if (!defined('_DPDGEOPOST_TPL_DIR_'))
	define('_DPDGEOPOST_TPL_DIR_', _DPDGEOPOST_MODULE_DIR_.'views/templates/');

if (!defined('_DPDGEOPOST_CONTROLLERS_DIR_'))
	define('_DPDGEOPOST_CONTROLLERS_DIR_', _DPDGEOPOST_MODULE_DIR_.'controllers/');

if (!defined('_DPDGEOPOST_IMG_DIR_'))
	define('_DPDGEOPOST_IMG_DIR_', _DPDGEOPOST_MODULE_DIR_.'views/img/');

/* Database tables constants */

if (!defined('_DPDGEOPOST_CSV_DB_'))
	define('_DPDGEOPOST_CSV_DB_', 'dpdgeopost_price_rules');

if (!defined('_DPDGEOPOST_CARRIER_DB_'))
	define('_DPDGEOPOST_CARRIER_DB_', 'dpdgeopost_carrier');

if (!defined('_DPDGEOPOST_SHIPMENT_DB_'))
	define('_DPDGEOPOST_SHIPMENT_DB_', 'dpdgeopost_shipment');

if (!defined('_DPDGEOPOST_PARCEL_DB_'))
	define('_DPDGEOPOST_PARCEL_DB_', 'dpdgeopost_parcel');

if (!defined('_DPDGEOPOST_REFERENCE_DB_'))
	define('_DPDGEOPOST_REFERENCE_DB_', 'dpdgeopost_reference');

if (!defined('_DPDGEOPOST_POSTCODE_DB_'))
	define('_DPDGEOPOST_POSTCODE_DB_', 'dpdgeopost_postcode');

if (!defined('_DPDGEOPOST_ADDRESS_DB_'))
	define('_DPDGEOPOST_ADDRESS_DB_', 'dpdgeopost_address');

/* DPD carriers IDs */

if (!defined('_DPDGEOPOST_CLASSIC_ID_'))
	define('_DPDGEOPOST_CLASSIC_ID_', 1);

if (!defined('_DPDGEOPOST_10_ID_'))
	define('_DPDGEOPOST_10_ID_', 10);

if (!defined('_DPDGEOPOST_12_ID_'))
	define('_DPDGEOPOST_12_ID_', 9);

if (!defined('_DPDGEOPOST_SAME_DAY_ID_'))
	define('_DPDGEOPOST_SAME_DAY_ID_', 27);

if (!defined('_DPDGEOPOST_B2C_ID_'))
	define('_DPDGEOPOST_B2C_ID_', 109);

if (!defined('_DPDGEOPOST_INTERNATIONAL_ID_'))
	define('_DPDGEOPOST_INTERNATIONAL_ID_', 40033);

if (!defined('_DPDGEOPOST_BULGARIA_ID_'))
	define('_DPDGEOPOST_BULGARIA_ID_', 40107);

/* ************************ */

if (!defined('_DPDGEOPOST_CSV_DELIMITER_'))
	define('_DPDGEOPOST_CSV_DELIMITER_', ';');

if (!defined('_DPDGEOPOST_CSV_FILENAME_'))
	define('_DPDGEOPOST_CSV_FILENAME_', 'dpdgeopost');

if (!defined('_DPDGEOPOST_DEFAULT_WEIGHT_UNIT_'))
	define('_DPDGEOPOST_DEFAULT_WEIGHT_UNIT_', 'kg');

if (!defined('_DPDGEOPOST_TRACKING_URL_'))
	define('_DPDGEOPOST_TRACKING_URL_', 'http://tracking.dpd.de/cgi-bin/delistrack?pknr=@');

if (!defined('_DPDGEOPOST_COOKIE_'))
	define('_DPDGEOPOST_COOKIE_', 'dpdgeopost_cookie');

if (!defined('_DPDGEOPOST_MAX_ADDRESS_LENGTH_'))
	define('_DPDGEOPOST_MAX_ADDRESS_LENGTH_', 70);