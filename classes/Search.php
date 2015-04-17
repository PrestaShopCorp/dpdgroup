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

require_once(_DPDGROUP_CLASSES_DIR_.'Mysql.php');

class DpdGroupSearch
{
	const ADDRESS_FIELD_CITY = 'city';
	const ADDRESS_FIELD_COUNTRY = 'country';
	const ADDRESS_FIELD_REGION = 'region';
	const ADDRESS_FIELD_ADDRESS = 'address';
	const ADDRESS_FIELD_POSTCODE = 'postcode';
	const STRICT_SEARCH_LIMIT = 400;
	const SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD = 100;
	const SEARCH_CAN_RETURN_RANDOM_VALUES = 1;
	const SEARCH_RESULT_RELEVANCE_THRESHOLD_FOR_VALIDATION = 84;
	const SEARCH_APPLY_SIMILARITY_MIN_THRESHOLD = 2;
	const SEARCH_APPLY_SIMILARITY_CITY_PERCENTAGE_THRESHOLD = 60;
	const SEARCH_HOUSE_NUMBER_IDENTIFIER_CAN_SKIP_WORDS = 1;
	const SEARCH_HOUSE_NUMBER_CONSTANT1 = 2;/*used to calibrate house number mapping in address - define the comparison threshold*/
	const SEARCH_HOUSE_NUMBER_CONSTANT2 = 5;/*used to calibrate house number mapping in address - increase the results mapping house numbers*/
	const ENABLED_COUNTRY = 'romania';

	private $mysql_model;

	public function __construct()
	{
		$this->mysql_model = new DpdGroupDpdPostcodeMysql();
	}

	private function filterAddressInput($array)
	{
		if (is_array($array))
			foreach ($array as &$value)
				$value = DpdGroupDpdPostcodeMysql::applyFiltersForAddress($value);

		return $array;
	}

	public function search(array $address, stdClass $relevance = null)
	{
		$address = $this->filterAddressInput($address);
		$postcode = $this->mysql_model->search($address, $relevance);

		return empty($postcode) ? null : $postcode;
	}

	public function searchSimilarAddresses(array $address)
	{
		$address = $this->filterAddressInput($address);
		$address = $this->mysql_model->searchSimilarAddresses($address);

		return $address;
	}
}