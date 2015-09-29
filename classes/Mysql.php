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

require_once(_DPDGROUP_CLASSES_DIR_.'CachedData.php');

class DpdGroupDpdPostcodeMysql
{
	const MIN_ADDRESS_WORD_LENGTH = 4;

	public function searchSimilarAddresses($address)
	{
		if (empty($address[DpdGroupSearch::ADDRESS_FIELD_CITY]))
			return false;

		$this->processRegionAndCity($address);
		$strict_search = $this->strictSearch($address);

		if ($strict_search > 0)
			return $this->strictSearch($address, false);

		$region = $address[DpdGroupSearch::ADDRESS_FIELD_REGION];
		$city = $address[DpdGroupSearch::ADDRESS_FIELD_CITY];

		return DB::getInstance()->executeS('
			SELECT `id_postcode`, `postcode`, `region`, `city`, `address`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `region` LIKE "%'.pSQL($region).'%"
				AND `city` LIKE "%'.pSQL($city).'%"
			LIMIT '.(DpdGroupSearch::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD + 1)
		);
	}

	public static function identifyRegionByCity($city)
	{
		$regions = DB::getInstance()->executeS('
			SELECT DISTINCT `region`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `city` = "'.pSQL($city).'"
		');

		if (!$regions)
			return false;

		$results = array();

		foreach ($regions as $row)
			$results[] = $row['region'];

		return $results;
	}

	public function search($address, stdClass $relevance = null)
	{
		if (empty($relevance))
			$relevance = new stdClass();

		$result = $this->tryStrictSearchAndReturn($address);

		if ($result !== false)
		{
			$relevance->percent = 100;

			return $result;
		}

		if ($this->processRegionAndCity($address))
		{
			if ($result !== false)
			{
				$relevance->percent = 95;

				return $result;
			}
		}

		// the city and region is already checked by processRegionAndCity
		$result = $this->tryLocateAtLeastTheCity($address, $relevance);

		if ($result !== false)
			return $this->returnPostalCode($result);

		return null;
	}

	/**
	 * @param $address
	 *
	 * if nothing found for city and address we have to check the city
	 * if we have only one entry for this city then this one will be returned
	 * else the similarity algorithm will be applied on all addresses
	 *
	 * @param stdClass $relevance
	 * @return array
	 */
	private function tryLocateAtLeastTheCity($address, stdClass $relevance = null)
	{
		if (empty($address[DpdGroupSearch::ADDRESS_FIELD_CITY]) || empty($address[DpdGroupSearch::ADDRESS_FIELD_REGION]))
			return false;

		$results = DB::getInstance()->executeS('
			SELECT `id_postcode`, `postcode`, `region`, `city`, `address`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `region` LIKE "%'.pSQL($address[DpdGroupSearch::ADDRESS_FIELD_REGION]).'%"
				AND `city` LIKE "%'.pSQL($address[DpdGroupSearch::ADDRESS_FIELD_CITY]).'%"
			LIMIT '.((int)DpdGroupSearch::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD + 1));

		if (count($results) > DpdGroupSearch::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD)
		{
			if (DpdGroupSearch::SEARCH_CAN_RETURN_RANDOM_VALUES)
			{
				$relevance->percent = 60;

				return array(array_pop($results));
			}
			else
			{
				$relevance->percent = 0;

				return false;
			}
		}

		if (is_array($results) && count($results) == 1)
		{
			$relevance->percent = 95;

			return $results;
		}
		elseif (is_array($results) && count($results) > 1)
		{
			$relevance->percent = 85;

			return $this->processSimilarity($address, $results);
		}

		return null;
	}

	/**
	 *   try the strict search meaning the search on city region and words of address
	 *   calling the strictSearch
	 *    if one item is returned then the postal code was found
	 *    else the similarity algorithm will be applied on all results
	 *
	 * @param $address
	 *
	 * @return bool|string
	 */
	private function tryStrictSearchAndReturn($address)
	{
		$results = false;
		$strict_search = $this->strictSearch($address);

		if ($strict_search == 1)
		{
			$results = $this->strictSearch($address, false);

			return $this->returnPostalCode($results);
		}
		elseif ($strict_search < DpdGroupSearch::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD
			&&	$strict_search > DpdGroupSearch::SEARCH_APPLY_SIMILARITY_MIN_THRESHOLD)
		{
			$strict_search = $this->strictSearch($address, false);
			$results = $this->processSimilarity($address, $strict_search);
		}

		if (is_array($results) && count($results) == 1)
			return $this->returnPostalCode($results);

		return false;
	}

	private function processRegionAndCity(array &$address)
	{
		$old_city = $address[DpdGroupSearch::ADDRESS_FIELD_CITY];
		$old_region = $address[DpdGroupSearch::ADDRESS_FIELD_REGION];
		$valid_city = $this->isCityValid($address[DpdGroupSearch::ADDRESS_FIELD_CITY]);

		if ($valid_city == 0 && Tools::strlen($old_city) > 2)
			$address[DpdGroupSearch::ADDRESS_FIELD_CITY] = $this->processCitySimilarity($address[DpdGroupSearch::ADDRESS_FIELD_CITY]);

		$valid_region = $this->isRegionValid($address[DpdGroupSearch::ADDRESS_FIELD_REGION]);

		if ($valid_region == 0 && Tools::strlen($old_region) > 1)
			$address[DpdGroupSearch::ADDRESS_FIELD_REGION] = $this->processRegionSimilarity($address[DpdGroupSearch::ADDRESS_FIELD_REGION]);

		if ($old_city == $address[DpdGroupSearch::ADDRESS_FIELD_CITY]
			&& $old_region == $address[DpdGroupSearch::ADDRESS_FIELD_REGION])
			return false;

		return true;
	}

	/**
	 * process the result and return the found entry
	 *
	 * @param $results
	 *
	 * @return bool|string
	 */
	private function returnPostalCode($results)
	{
		if (is_array($results) && count($results) == 1)
		{
			$tmp = array_pop($results);

			return !empty($tmp[DpdGroupSearch::ADDRESS_FIELD_POSTCODE]) ? (string)$tmp[DpdGroupSearch::ADDRESS_FIELD_POSTCODE] : 'false';
		}

		if (is_array($results) && count($results) > 1)
		{
			$tmp = $results;

			return !empty($tmp[DpdGroupSearch::ADDRESS_FIELD_POSTCODE]) ? (string)$tmp[DpdGroupSearch::ADDRESS_FIELD_POSTCODE] : 'false';
		}

		return false;
	}

	private function processSimilarity($address, $results)
	{
		$house_number = $this->findHouseNumber($address['address']);

		$found_house_numbers = array();

		if (!is_array($results) || count($results) == 0)
			return false;
		elseif (is_array($results) && count($results) == 1)
			return $results;

		$similarity_array   = array();
		$address_identifier = $address['address'];

		foreach ($results as $key => $temp_address)
		{
			$temp_identifier = $temp_address['address'];
			$percent = 0;
			similar_text($address_identifier, $temp_identifier, $percent);
			$similarity_array[$key] = $percent;

			if (!empty($house_number))
			{
				$db_address_numbers = $this->extractHouseNumbersFromDatabaseAddress($temp_address['address']);
				$check_result = $this->checkIfNumberInInterval($house_number, $db_address_numbers);
				if ($check_result)
					$found_house_numbers[] = $key;
			}
		}

		if (count($found_house_numbers) == 1)
		{
			$key = array_pop($found_house_numbers);

			return array($results[$key]);
		}
		elseif (count($found_house_numbers) > 1)
		{
			// we have to make the similarity count higher for $found_house_numbers
			$similarity_average = array_sum($similarity_array) / count($similarity_array);
			//we want to not add too much noise in our decision
			//that is why we have to increase only the results if are up then a threshold

			$delta = (max($similarity_array) - $similarity_average);

			foreach ($found_house_numbers as $key)
			{
				// we have to ensure that the result is not false positive case
				if ($similarity_array[$key] > $similarity_average - $delta * DpdGroupSearch::SEARCH_HOUSE_NUMBER_CONSTANT1)
				{
					//we have to be sure that this result will be increased enough
					$similarity_array[$key] += $delta * DpdGroupSearch::SEARCH_HOUSE_NUMBER_CONSTANT2;
				}
			}
		}

		$maxs = array_keys($similarity_array, max($similarity_array));
		$max = $maxs[0];

		return array($results[$max]);
	}

	private function checkIfNumberInInterval($nr, $interval)
	{
		if (count($interval) == 1)
			$interval = array_pop($interval);

		if (!is_array($interval) && (int)$interval && $nr == $interval)
			return true;
		elseif (!is_array($interval))
			return false;

		$last_value = null;

		foreach ($interval as $value)
		{
			$value = (int)$value;

			if (!empty($last_value))
			{
				if ($nr <= $value && $nr >= $last_value)
					return true;
			}

			$last_value = $value;
		}

		return false;
	}

	/**
	 * extract all numbers from database address field
	 *
	 * @param $address
	 * @return array
	 */
	private function extractHouseNumbersFromDatabaseAddress($address)
	{
		$address = str_replace(array('.', '/', '\\', '-'), array(' ', ' ', ' ', ' '), $address);
		$words   = explode(' ', $address);
		$numbers = array();

		foreach ($words as $word)
		{
			if (Tools::strlen($word) == 0)
				continue;

			if ((int)$word)
				$numbers[] = (int)$word;
			elseif ($word == 't')
				$numbers[] = 9999999;
		}

		return $numbers;

	}

	/**
	 * find the address house number - for given address string
	 *
	 * @param $address
	 * @return mixed|null
	 */
	public function findHouseNumber($address)
	{
		$can_skip_words = DpdGroupSearch::SEARCH_HOUSE_NUMBER_IDENTIFIER_CAN_SKIP_WORDS;
		$numbers = array();
		$house_number_identifiers = DpdGroupSearchModelCachedData::getHouseNumberIdentifier();
		$house_number = null;
		$address = str_replace(array('.', '/', '\\', '-'), array(' ', ' ', ' ', ' '), $address);
		$words = explode(' ', $address);
		$last_word_was_the_identifier = false;
		$skip_count = 0;

		foreach ($words as $word)
		{
			if (Tools::strlen($word) == 0)
				continue;

			if ((int)$word)
				$numbers[] = (int)$word;

			if ($last_word_was_the_identifier == true && (int)$word)
			{
				$house_number = $word;

				return $house_number;
			}
			elseif ($last_word_was_the_identifier == true)
			{
				$skip_count++;

				if ($skip_count > $can_skip_words)
					$last_word_was_the_identifier = false;
			}

			if (in_array($word, $house_number_identifiers))
				$last_word_was_the_identifier = true;
		}

		if (count($numbers) == 1)
			return array_pop($numbers);

		return null;
	}

	/**
	 * check in database if there is a valid city name
	 *
	 * @param $city_name
	 * @return int
	 * @internal param $string
	 */
	private function isCityValid($city_name)
	{
		return (int)DB::getInstance()->getValue('
			SELECT  COUNT(*) AS count
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `city` = "'.pSQL($city_name).'"
		');
	}

	/**
	 * check in database if there is a valid region (state) name
	 *
	 * @param $region_name
	 * @return int
	 * @internal param $string
	 */
	private function isRegionValid($region_name)
	{
		return (int)DB::getInstance()->getValue('
			SELECT  COUNT(*) AS count
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `region` = "'.pSQL($region_name).'"
		');
	}

	/**
	 * check for miss typing on the input for the city
	 *
	 * is using an similarity algorithm on an array
	 * available cities are cached in php
	 *
	 * @param $city_input
	 *
	 * @return mixed
	 */
	private function processCitySimilarity($city_input)
	{
		//use cached database
		$cities = DpdGroupSearchModelCachedData::getCities();

		if (!empty($cities))
		{
			$results = array();
			$similarity_array = array();
			$i = 0;

			foreach ($cities as $city)
			{
				$results[$i] = $city;
				similar_text($city_input, $city, $percent);
				$similarity_array[$i] = $percent;
				$i++;
			}

			$maxs = array_keys($similarity_array, max($similarity_array));
			$max  = $maxs[0];

			if (!empty($results[$max]) && max($similarity_array) >= DpdGroupSearch::SEARCH_APPLY_SIMILARITY_CITY_PERCENTAGE_THRESHOLD)
				return $results[$max];
		}

		$cities = DB::getInstance()->executeS('
			SELECT DISTINCT `city`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
		');

		$results = array();
		$similarity_array = array();
		$i = 0;

		foreach ($cities as $row)
		{
			$results[$i] = $row['city'];
			similar_text($city_input, $row['city'], $percent);
			$similarity_array[$i] = $percent;
			$i++;
		}

		$maxs = array_keys($similarity_array, max($similarity_array));
		$max  = $maxs[0];

		if (!empty($results[$max]))
			return $results[$max];

		return $city_input;
	}

	/**
	 * check for miss typing on the input for the region
	 *
	 * is using an similarity algorithm on an array
	 * available cities are cached in php
	 *
	 * @param $region_input
	 *
	 * @return mixed
	 */
	private function processRegionSimilarity($region_input)
	{
		//use cached database
		$regions = DpdGroupSearchModelCachedData::getRegions();

		if (!empty($regions))
		{
			$results = array();
			$similarity_array = array();
			$i = 0;

			foreach ($regions as $region)
			{
				$results[$i] = $region;
				similar_text($region_input, $region, $percent);
				$similarity_array[$i] = $percent;
				$i++;
			}

			$maxs = array_keys($similarity_array, max($similarity_array));
			$max  = $maxs[0];

			if (!empty($results[$max]) && max($similarity_array) >= DpdGroupSearch::SEARCH_APPLY_SIMILARITY_CITY_PERCENTAGE_THRESHOLD)
				return $results[$max];
		}

		$regions = DB::getInstance()->executeS('
			SELECT DISTINCT `region`
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
		');

		$similarity_array = array();
		$results = array();
		$i = 0;

		foreach ($regions as $row)
		{
			$results[$i] = $row['region'];
			similar_text($region_input, $row['region'], $percent);
			$similarity_array[$i] = $percent;
			$i++;
		}

		$maxs = array_keys($similarity_array, max($similarity_array));
		$max  = $maxs[0];

		if (!empty($results[$max]))
			return $results[$max];

		return $region_input;
	}

	/**
	 * perform a strict search in database by looking at city region and address
	 * address field is searched using LIKE statment
	 *
	 * @param      $address
	 * @param bool $count
	 *
	 * @return array
	 */
	private function strictSearch($address, $count = true)
	{
		$street = $address[DpdGroupSearch::ADDRESS_FIELD_ADDRESS];
		$words = explode(' ', $street);

		$sql = 'SELECT '.($count === true ? ' COUNT(*) AS count ' : ' * ').'
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `region` LIKE "%'.pSQL($address[DpdGroupSearch::ADDRESS_FIELD_REGION]).'%"
				AND `city` LIKE "%'.pSQL($address[DpdGroupSearch::ADDRESS_FIELD_CITY]).'%"';

		$words_new = array();

		foreach ($words as $word)
		{
			$filtered_value = preg_replace('/[^a-zA-Z]+/', '', $word);

			if (Tools::strlen($filtered_value) > self::MIN_ADDRESS_WORD_LENGTH)
				$words_new[] = $filtered_value;
		}

		$words_new = array_unique($words_new);
		$i = 0;

		if (count($words_new))
			$sql .= ' AND (';

		foreach ($words_new as $word)
		{
			if ($i != 0)
				$sql .= ' OR ';

			$sql .= ' address LIKE "%'.pSQL($word).'%" ';
			$i++;
		}

		if (count($words_new))
			$sql .= ' ) ';

		$sql = $sql.' LIMIT '.(int)DpdGroupSearch::STRICT_SEARCH_LIMIT;
		$results = array();
		$postcode_data = DB::getInstance()->executeS($sql);

		foreach ($postcode_data as $row)
			$results[] = $row;

		if ($count == true)
			return $results[0]['count'];

		return $results;
	}

	public static function applyFiltersForAddress(&$data)
	{
		if (!is_array($data))
			$data = self::applyTextFilter($data);
		else
			foreach ($data as &$value)
				$value = self::applyTextFilter($value);

		return $data;
	}

	private static function applyTextFilter($string)
	{
		$empty = ''; //used to avoid PrestaShop validator error of double quotes
		$search = array('Ă', 'ă', 'Â', 'â', 'Î', 'î', 'Ş', 'ş', 'Ţ', 'ţ',	'Ş', 'ş', 'Ţ', 'ţ',"\s$empty", "\t$empty","\r\n$empty");
		$replace = array('A', 'a', 'A', 'a', 'I', 'i', 'S', 's', 'T', 't', 'S', 's', 'T', 't', ' ', ' ', ' ');
		$string = str_replace($search, $replace, $string);
		$temp = iconv('utf-8', 'ascii//TRANSLIT', $string);

		if (!empty($temp))
			$string = $temp;

		$string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$string = preg_replace('/[^a-zA-Z0-9.\\\ \/-]+/', '', $string);

		return Tools::strtolower(trim($string));
	}

	public static function postcodeExistsInDB($postcode)
	{
		return DB::getInstance()->getValue('
			SELECT COUNT(`postcode`)
			FROM `'._DB_PREFIX_._DPDGROUP_POSTCODE_DB_.'`
			WHERE `postcode` = "'.pSQL($postcode).'"
		');
	}
}