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

function upgrade_module_1_0_0($module)
{
	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "road_type"') == true)
	{
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `road_type`'))
			return false;

		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP INDEX `postcode_2`'))
			return false;

		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
		    ADD CONSTRAINT `postcode_2` UNIQUE (`postcode`, `region`, `city`, `address`)'))
			return false;
	}

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "d_depo"') == true)
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `d_depo`'))
			return false;

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "d_sort"') == true)
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `d_sort`'))
			return false;

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "zone"') == true)
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `zone`'))
			return false;

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "saturday"') == true)
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `saturday`'))
			return false;

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` LIKE "route"') == true)
		if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'` DROP COLUMN `route`'))
			return false;

	return $module;
}
