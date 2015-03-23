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

require_once(_DPDGEOPOST_CLASSES_DIR_.'Address.php');
require_once(_DPDGEOPOST_CLASSES_DIR_.'Search.php');

class DpdGeopostPostcodeSearch
{
	const MIN_POSTCODE_LENGTH = 4;

	private static $instance = null;

	private static function getInstance()
	{
		if (!self::$instance instanceof DpdGeopostSearch)
			self::$instance = new DpdGeopostSearch();

		return self::$instance;
	}

	/**
	 * process a search on postcode table store it to address entity
	 * at the end return it for the api call
	 *
	 * @param $address_object
	 *
	 * @return string
	 * @throws PrestaShopException
	 */
	public function extractPostCodeForShippingRequest(Address $address_object)
	{
		$id_state = $address_object->id_state;
		$country_name = pSQL($address_object->country);
		$region_name = State::getNameById($id_state);

		$address = array(
			'country'  => $country_name,
			'region'   => $region_name,
			'city'     => $address_object->city,
			'address'  => $address_object->address1.(($address_object->address2) ? ' '.$address_object->address2 : ''),
			'postcode' => $address_object->postcode
		);

		if ($this->isEnabledAutocompleteForPostcode($country_name))
		{
			$dpd_postcode_address = new DpdGeopostDpdPostcodeAddress();
			$dpd_postcode_address->loadDpdAddressByAddressId($address_object->id);
			$current_hash = $this->generateAddressHash($address);

			if ($dpd_postcode_address->id_address && $current_hash == $dpd_postcode_address->hash)
				return $dpd_postcode_address->auto_postcode;

			if (!$dpd_postcode_address->id_address || $current_hash != $dpd_postcode_address->hash)
			{
				$postcode_relevance = new stdClass();
				$post_code = $this->search($address, $postcode_relevance);
				$dpd_postcode_address->auto_postcode = $post_code;
				$dpd_postcode_address->id_address = $address_object->id;
				$dpd_postcode_address->hash = $current_hash;

				if ($this->isValid($post_code, $postcode_relevance))
				{
					$dpd_postcode_address->relevance = 1;
					$address_object->postcode = $post_code;
					$address_object->save();
				}
				else
					$dpd_postcode_address->relevance = 0;

				if (!empty($dpd_postcode_address->dpd_postcode_id))
					$dpd_postcode_address->id = $dpd_postcode_address->dpd_postcode_id;

				$dpd_postcode_address->save();
			}
			else
				return $dpd_postcode_address->auto_postcode;
		}
		else
			$post_code = $address_object->postcode;

		return $post_code;
	}

	/**
	 * this hash will be used for trigger the postcode expiration
	 *
	 * @param $address
	 *
	 * @return string
	 */
	private function generateAddressHash($address)
	{
		if (!is_array($address))
			return '';

		unset($address['postcode']);
		$hash = implode('', $address);

		return md5($hash);
	}

	/**
	 * it is used to create a list of relevant addresses for given address.
	 * used in admin panel to validate the postcode
	 *
	 * @param array $address The content will be the edit form for address from admin
	 *                       $address contain next keys
	 *                       MANDATORY
	 *                       country
	 *                       city
	 *
	 * OPTIONAL
	 *      region
	 *      address
	 *      street
	 * @return bool|string
	 */
	public function findAllSimilarAddressesForAddress($address)
	{
		$address['region'] = '';
		$country_name = '';

		if (!empty($address['country_id']))
		{
			$country_object = new Country();
			$country_name = $country_object->getNameById($address['lang_id'], $address['country_id']);
			$address['country'] = $country_name;
		}

		if ($this->isEnabledAutocompleteForPostcode($country_name))
		{
			if ($address['region_id'])
			{
				$region_name = State::getNameById($address['region_id']);
				$address['region'] = $region_name;
			}

			if (empty($address['region']))
			{
				$regions = DpdGeopostDpdPostcodeMysql::identifyRegionByCity($address['city']);

				if ($regions && count($regions) == 1)
					$address['region'] = array_pop($regions);
			}

			$found_addresses = self::getInstance()->searchSimilarAddresses($address);

			return $found_addresses;
		}

		return false;
	}

	public function search($address, $postcode_relevance = null)
	{
		$found_post_code = self::getInstance()->search($address, $postcode_relevance);

		if (isset($address['postcode']) && Tools::strlen($address['postcode']) > self::MIN_POSTCODE_LENGTH)
		{
			if ($found_post_code == $address['postcode'])
				return $found_post_code;
			elseif (!empty($found_post_code))
				return $found_post_code; //mark the response as not exactly the same

			return $address['postcode'];
		}

		return $found_post_code;
	}

	/**
	 * test if found postcode relevance is enough for considering the postcode useful in the rest of checkout process
	 *
	 * @param          $post_code
	 * @param stdClass $relevance
	 *
	 * @return bool
	 */
	private function isValid($post_code, stdClass $relevance = null)
	{
		if (!Validate::isPostCode($post_code) || empty($relevance))
			return false;

		if (!empty($relevance->percent) && $relevance->percent > DpdGeopostSearch::SEARCH_RESULT_RELEVANCE_THRESHOLD_FOR_VALIDATION)
			return true;

		return false;
	}

	private function isEnabledAutocompleteForPostcode($country_name)
	{
		return DpdGeopostDpdPostcodeMysql::applyFiltersForAddress($country_name) == DpdGeopostSearch::ENABLED_COUNTRY;
	}
}