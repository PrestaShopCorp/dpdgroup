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

class DpdGeopostShipment extends DpdGeopostWs
{
	const FILENAME = 'Shipment';
	const PAYMENT_TYPE = 'Cash';

	protected $targetNamespace = 'http://it4em.yurticikargo.com.tr/eshop/shipment';

	protected $serviceName = 'ShipmentServiceImpl';

	public 	  $id_order;
	public 	  $id_shipment;
	public	  $id_manifest;
	public	  $label_printed;
	public	  $date_pickup;
	private	  $data = array();

	public function __construct($id_order = null)
	{
		parent::__construct();

		if ($this->id_order = (int)$id_order)
		{
			if ($shipmentDate = self::getShipmentData($id_order))
				foreach ($shipmentDate as $element => $value)
					$this->$element = $value;

			if ($this->id_shipment)
			{
				$result = $this->getShipment('shipmentReferenceList', array('id' => $this->id_shipment));
				if (isset($result['shipmentResultList']) && isset($result['shipmentResultList']['shipment']))
					$this->data = $result['shipmentResultList']['shipment'];

				/* parcels array must be indexed and sorted by parcelReferenceNumber, code below makes sure of that */
				if ($this->parcels)
				{
					$parcels = (array_values($this->parcels) === $this->parcels) ? $this->parcels : array($this->parcels);

					if (count($this->parcels) > 1)
						usort($parcels, array("DpdGeopostShipment", "sortParcelsByReferenceNumber"));

					$this->parcels = $parcels;
				}
			}
		}
	}

	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	public function calculatePriceForOrder($id_method, $id_address, $products = array())
	{
		/* method ID is not defined, nothing to do here... */
		if (!$id_method)
			return false;

		$order = new Order($this->id_order);

		if (!Validate::isLoadedObject($order))
		{
			self::$errors[] = $this->l('Order does not exists');
			return false;
		}

		if ($this->parcels)
			$parcels = $this->parcels;
		else
		{
			if (!$products)
				$products = $order->getProductsDetail();

			$parcels = $this->putProductsToParcels($products);
		}

		if ($result = $this->calculate($id_method, $id_address, $parcels, $order))
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
				$order_price = $order->total_paid;
			else
				$order_price = $order->total_shipping_tax_incl;

			if ($result['price'] > Tools::convertPrice($order_price, $order->id_currency, false))
				self::$notices[] = $this->l('Shipping costs more than client paid.');

			return Tools::displayPrice($result['price'], $result['id_currency']);
		}

		return false;
	}

	public function calculate($id_method, $id_address, $parcels, Order $order = null, $extra_params = array())
	{
		$address = new Address($id_address);
		$country = new Country($address->id_country);

		$params = array(
			'receiverName' => pSQL($address->firstname).' '.pSQL($address->lastname),
			'receiverFirmName' => pSQL($address->company),
			'receiverCountryCode' => pSQL($country->iso_code),
			'receiverZipCode' => pSQL($address->postcode),
			'receiverCity' => pSQL($address->city),
			'receiverStreet' => pSQL($address->address1) . (($address->address2) ? ' ' . pSQL($address->address2) : ''),
			'receiverHouseNo' => '',
			'receiverPhoneNo' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
			'mainServiceCode' => (int)$id_method
		);

		$params[] = array(
			'name' => 'parcels',
			'data' => $parcels
		);

		if ($order !== null)
		{
			$cod_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

			if ($cod_method !== null && $order->module == $cod_method)
			{
				$currency = new Currency((int)$order->id_currency);

				$params['additionalServices'] = array(
					'cod' => array(
						'amount' => version_compare(_PS_VERSION_, '1.5', '<') ? (float)$order->total_paid : (float)$order->total_paid_tax_incl,
						'currency' => pSQL($currency->iso_code),
						'paymentType' => self::PAYMENT_TYPE,
						'referenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id))
					)
				);
			}
		}

		if (!empty($extra_params))
		{
			$params['additionalServices'] = array(
				'cod' => array(
					'amount' => (float)$extra_params['total_paid'],
					'currency' => pSQL($extra_params['currency_iso_code']),
					'paymentType' => self::PAYMENT_TYPE,
					'referenceNumber' => pSQL($extra_params['reference'])
				)
			);
		}

		$result = $this->calculatePrice('shipmentList', $params);

		if (!reset(self::$errors))
		{
			if($id_currency = Currency::getIdByIsoCode($result['priceList']['price']['currency'], $this->context->shop->id))
			{
				return array(
					'price' => (float)$result['priceList']['price']['totalAmount'],
					'id_currency' => (int)$id_currency
				);
			}
			else
			{
				self::$errors[] = sprintf($this->l('Currency %s is not installed, price cannot be calculated'), $result['priceList']['price']['currency']);
				return false;
			}
		}

		return false;
	}

	public function save($id_method, $id_address, $parcels)
	{
		if (!$this->validateParcelsWeights($parcels))
			return false;

		$order = new Order($this->id_order);

		if (!Validate::isLoadedObject($order))
		{
			self::$errors[] = $this->l('Order does not exists');
			return false;
		}

		$address = new Address($id_address);

		if (!Validate::isLoadedObject($address))
		{
			self::$errors[] = $this->l('Address does not exists');
			return false;
		}

		$country = new Country($address->id_country);

		$params = array(
			'receiverName' => pSQL($address->firstname).' '.pSQL($address->lastname),
			'receiverFirmName' => pSQL($address->company),
			'receiverCountryCode' => pSQL($country->iso_code),
			'receiverZipCode' => pSQL($address->postcode),
			'receiverCity' => pSQL($address->city),
			'receiverStreet' => pSQL($address->address1) . (($address->address2) ? ' ' . pSQL($address->address2) : ''),
			'receiverHouseNo' => '',
			'receiverPhoneNo' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
			'mainServiceCode' => (int)$id_method,
			'shipmentReferenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id))
		);

		if ($order !== null)
		{
			$cod_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

			if ($cod_method !== null && $order->module == $cod_method)
			{
				$currency = new Currency((int)$order->id_currency);

				$params['additionalServices'] = array(
					'cod' => array(
						'amount' => version_compare(_PS_VERSION_, '1.5', '<') ? (float)$order->total_paid : (float)$order->total_paid_tax_incl,
						'currency' => pSQL($currency->iso_code),
						'paymentType' => self::PAYMENT_TYPE,
						'referenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id))
					)
				);
			}
		}

		$params[] = $this->prepareParcelsDataForWS($order, $parcels);

		if ($this->id_shipment)
			$result = $this->update($params, $order);
		else
			$result = $this->create($params, $order);

		if (!$result)
			return false;

		if (!$this->updateOrder($id_address, $id_method, $order))
			return false;

		return $result;
	}
	
	private static function getOrderReference($id_order)
	{
		$reference = DB::getInstance()->getValue('
			SELECT `reference`
			FROM `'._DB_PREFIX_._DPDGEOPOST_REFERENCE_DB_.'`
			WHERE `id_order` = "'.(int)$id_order.'"
		');
		
		return $reference ? $reference : self::createOrderReference((int)$id_order);
	}
	
	private static function createOrderReference($id_order)
	{
		$reference = strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
		
		DB::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_._DPDGEOPOST_REFERENCE_DB_.'`
				(`id_order`, `reference`)
			VALUES
				("'.(int)$id_order.'", "'.pSQL($reference).'")
		');
		
		return $reference;
	}

	private function validateParcelsWeights($parcels)
	{
		$are_parcels_valid = true;
		foreach ($parcels as $parcel)
			if (!Validate::isUnsignedFloat($parcel['weight']))
			{
				self::$errors[] = sprintf($this->l('Parcel total weight "%s" is not valid'), $parcel['weight']);
				$are_parcels_valid = false;
			}
		return $are_parcels_valid;
	}

	public function create($params, $order)
	{
		$result = $this->createShipment('shipmentList', $params, array('priceOption' => 'WithoutPrice'));

		if (!reset(self::$errors))
		{
			if (!isset($result['resultList']) || !isset($result['resultList']['shipmentReference']))
			{
				self::$errors[] = $this->l('Could not receive response from web services.');
				return false;
			}

			$this->id_shipment = $result['resultList']['shipmentReference']['id'];

			if (!Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
					(`id_order`, `id_shipment`)
				VALUES
					('.(int)$this->id_order.', '.(int)$this->id_shipment.')
			'))
			{
				self::$errors[] = $this->l('Shipment could not be created locally');
				return false;
			}
			elseif (!$this->saveParcelsLocally($params[0]['data'], $order->id))
			{
				self::$errors[] = $this->l('Parcels could not be saved locally');
				return false;
			}
		}

		return $result['resultList']['message'] ? $result['resultList']['message'] : $this->l('Your shipment is successfully saved');
	}

	public function update($params, $order)
	{
		$result = $this->updateShipment('shipmentList', $params, array('priceOption' => 'WithoutPrice'));

		if (!reset(self::$errors))
		{
			if (!isset($result['resultList']) || !isset($result['resultList']['shipmentReference']))
			{
				self::$errors[] = $this->l('Could not receive response from web services.');
				return false;
			}

			$this->id_shipment = $result['resultList']['shipmentReference']['id'];

			if (!$this->saveParcelsLocally($params[0]['data'], $order->id))
			{
				self::$errors[] = $this->l('Parcels could not be saved locally');
				return false;
			}
		}

		return $result['resultList']['message'] ? $result['resultList']['message'] : $this->l('Your shipment is successfully saved');
	}

	public function delete()
	{
		if (!$this->id_order)
		{
			self::$errors[] = $this->l('Order does not exists');
			return false;
		}

		$params = array(
			'id' => $this->id_shipment
		);

		$this->deleteShipment('shipmentReferenceList', $params);

		if (!reset(self::$errors))
		{
			if (!Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				WHERE `id_order`='.(int)$this->id_order) || !DpdGeopostParcel::clearOrderParcels((int)$this->id_order)
			)
			{
				self::$errors[] = $this->l('Shipment could not be deleted locally');
				return false;
			}
		}

		return true;
	}

	public function getStatus()
	{
		$this->getShipmentStatus('shipmentReferenceList', array('id' => $this->id_shipment));
	}


	public function search($date_from = '20080909', $date_to = '20150909')
	{
		$result = $this->searchShipment('searchParams', array('startDate' => $date_from, 'endDate' => $date_to));
		return isset($result['shipmentInfoList']) ? $result['shipmentInfoList'] : array();
	}

	public function getLabelsPdf($shipment_ids = null)
	{
		if (!$shipment_ids && !$this->id_order)
			return array('error' => $this->l('Order does not exists'));

		$data = array();

		if (!$shipment_ids)
			$shipment_ids = array($this->id_shipment);

		$cod_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

		foreach ($shipment_ids as $id_shipment)
		{
			$shipment = new DpdGeopostShipment((int)$id_shipment);
			$order = new Order($shipment->id_order);

			if ($cod_method !== null && $order->module == $cod_method)
			{
				$currency = new Currency((int)$order->id_currency);

				$data[] = array(
					'id' => (int)$id_shipment,
					'additionalServices' => array(
						'cod' => array(
							'amount' => (float)$order->total_paid_tax_incl,
							'currency' => pSQL($currency->iso_code),
							'paymentType' => pSQL($order->payment),
							'referenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id))
						)
					)
				);
			}
			else
				$data[] = array('id' => (int)$id_shipment);
		}

		$params = array(
			'name' => 'shipmentReferenceList',
			'data' => $data
		);

		$result = $this->getShipmentLabel(null, array($params), array('printOption' => 'Pdf'));

		if (!reset(self::$errors))
		{
			if (isset($result['pdfFile']))
			{
				foreach ($shipment_ids as $id_shipment)
					if (!$this->setLabelPrinted((int)$id_shipment))
						return false;

				return base64_decode($result['pdfFile']);
			}
			else
			{
				self::$errors[] = $this->l('PDF file cannot be generated');
				return false;
			}
		}
		else
			return false;

		return false;
	}

	private function updateOrder($id_address, $id_method, &$order)
	{
		$id_carrier = $this->getIdcarrierFromIdMethod($id_method);

		if ($id_carrier && Validate::isLoadedObject(new Carrier($id_carrier)))
		{
			if ($order->id_address_delivery != $id_address || $order->id_carrier != $id_carrier || $order->shipping_number != $this->id_shipment)
			{
				$order->id_address_delivery = (int)$id_address;
				$order->id_carrier = (int)$id_carrier;

				if ($id_order_carrier = (int)$order->getIdOrderCarrier())
				{
					$order_carrier = new OrderCarrier((int)$id_order_carrier);
					$order_carrier->id_carrier = $order->id_carrier;
					$order_carrier->update();
				}

				if (!$order->update())
				{
					self::$errors[] = $this->l('Order could not be updated');
					return false;
				}
			}

			return true;
		}
		else
		{
			self::$errors[] = $this->l('Carrier does not exists. Order could not be updated.');
			return false;
		}

		return false;
	}

	private function addTrackingNumber(&$order, $tracking_number, $id_carrier, $id_address)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return $this->addShippingNumber($order, $tracking_number, $id_carrier, $id_address);

		$order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
		if (!Validate::isLoadedObject($order_carrier))
		{
			self::$errors[] = $this->l('The order carrier ID is invalid.');
			return false;
		}
		elseif (!Validate::isTrackingNumber($tracking_number))
		{
			self::$errors[] = $this->l('The tracking number is incorrect.');
			return false;
		}
		else
		{
			$order->id_address_delivery = (int)$id_address;
			$order->shipping_number = $tracking_number;
			$order->id_carrier = (int)$id_carrier;
			$order->update();

			$order_carrier->tracking_number = pSQL($tracking_number);
			if ($order_carrier->update())
			{
				$customer = new Customer((int)$order->id_customer);
				$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
				if (!Validate::isLoadedObject($customer))
				{
					self::$errors[] = $this->l('Can\'t load Customer object');
					return false;
				}
				if (!Validate::isLoadedObject($carrier))
					return false;
				$templateVars = array(
					'{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
					'{firstname}' => pSQL($customer->firstname),
					'{lastname}' => pSQL($customer->lastname),
					'{id_order}' => (int)$order->id,
					'{shipping_number}' => pSQL($order->shipping_number),
					'{order_name}' => pSQL($order->getUniqReference())
				);
				if (@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
					$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
					_PS_MAIL_DIR_, true, (int)$order->id_shop)
				)
				{
					Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier));
					return true;
				}
				else
				{
					self::$errors[] = $this->l('An error occurred while sending an email to the customer.');
					return false;
				}
			}
			else
			{
				self::$errors[] = $this->l('The order carrier cannot be updated.');
				return false;
			}
		}
	}

	private function addShippingNumber(&$order, $shipping_number, $id_carrier, $id_address)
	{
		$order->id_address_delivery = (int)$id_address;
		$order->shipping_number = $shipping_number;
		$order->id_carrier = (int)$id_carrier;
		$order->update();
		if ($shipping_number)
		{
			$customer = new Customer((int)($order->id_customer));
			$carrier = new Carrier((int)($order->id_carrier));
			if (!Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($carrier))
			{
				self::$errors[] = $this->l('Customer / Carrier not found');
				return false;
			}
			$templateVars = array(
				'{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
				'{firstname}' => pSQL($customer->firstname),
				'{lastname}' => pSQL($customer->lastname),
				'{order_name}' => sprintf("#%06d", (int)($order->id)),
				'{id_order}' => (int)$order->id
			);
			@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
				$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
				_PS_MAIL_DIR_, true);
		}

		return true;
	}

	private function saveParcelsLocally($data, $id_order)
	{
		DpdGeopostParcel::clearOrderParcels($id_order);

		foreach ($data as $parcel)
		{
			$products = explode(',', $parcel['products']);

			foreach ($products as $product)
			{
				list($id_product, $id_product_attribute) = explode('_', trim($product));

				$dpdParcel = new DpdGeopostParcel;
				$dpdParcel->id_order = (int)$id_order;
				$dpdParcel->parcelReferenceNumber = pSQL($parcel['parcelReferenceNumber']);
				$dpdParcel->id_product = (int)$id_product;
				$dpdParcel->id_product_attribute = (int)$id_product_attribute;

				if (!$dpdParcel->save())
					return false;
			}

		}

		return true;
	}

	private function prepareParcelsDataForWS(Order $order, $parcels)
	{
		foreach ($parcels as $parcel_number => $data)
		{
			$parcels[$parcel_number]['weight'] = pSQL($data['weight']);
			$parcels[$parcel_number]['parcelReferenceNumber'] = (int)$order->id.pSQL($parcel_number);
		}

		return array(
			'name' => 'parcels',
			'data' => $parcels
		);
	}

	private function getProductWeights($products)
	{
		$weights = array();

		foreach ($products as $product)
		{
			$this->extractAndFormatProductData($product);
			$weights[$product['id_product'].'_'.$product['id_product_attribute']] = $product['product_weight'];
		}

		return $weights;
	}

	/**
	 * Adds parcel data (description) for each product. Products are also split by quantity.
	 * Ex. Product with quantity 2 will be split into two separate products.
	 *
	 * @param	array	$products order/cart products data ($order->getProductsDetail()|$cart->getProducts())
	 * @return	array
	 * @access	public	products with parcel data
	 */

	public function getParcelsSetUp($products)
	{
		$parcels = array();

		foreach ($products as $product)
		{
			$quantity = isset($product['product_quantity']) ? (int)$product['product_quantity'] : (int)$product['quantity'];

			$this->extractAndFormatProductData($product);

			if ($this->config->packaging_method != DpdGeopostConfiguration::ONE_PRODUCT)
			{
				for($i = 0; $i < $quantity; $i++)
				{
					if (empty($parcels))
						$product['description'] = $product['id_product'].'_'.$product['id_product_attribute'];
					else
					{
						$product['description'] = '';
						$parcels[0]['description'] .= ', ' . $product['id_product'].'_'.$product['id_product_attribute'];
					}
				}
			}
			else
				$product['description'] = $product['id_product'].'_'.$product['id_product_attribute'];

			for($i = 0; $i < $quantity; $i++)
				$parcels[] = $product;
		}

		return $parcels;
	}

	public function putProductsToParcels($products)
	{
		$parcels = array();
		$all_products_in_one_parcel = ($this->config->packaging_method == DpdGeopostConfiguration::ALL_PRODUCTS) ? true : false;

		foreach ($products as &$product)
		{
			$this->extractAndFormatProductData($product);
			$parcel = array();
			$parcel['description'] = $product['id_product'].'_'.$product['id_product_attribute'];
			$parcel['weight'] = (float)$product['product_weight'];

			if ($all_products_in_one_parcel && !empty($parcels))
			{
				$parcels[0]['description'] .= ', ' . $parcel['description'];
				$parcels[0]['weight'] += $parcel['weight'];
			}
			else
				$parcels[] = $parcel;
		}

		return $parcels;
	}

	private function extractAndFormatProductData(&$product)
	{
		$id_product = isset($product['product_id']) ? (int)$product['product_id'] : (int)$product['id_product'];
		$id_product_attribute = isset($product['product_attribute_id']) ? (int)$product['product_attribute_id'] : (int)$product['id_product_attribute'];
		$product_name = isset($product['product_name']) ? $product['product_name'] : $product['name'];
		$product_weight = isset($product['product_weight']) ? self::convertWeight($product['product_weight']) : self::convertWeight($product['weight']);

		$product = array(
			'id_product' => (int)$id_product,
			'id_product_attribute' => pSQL($id_product_attribute),
			'product_name' => pSQL($product_name),
			'product_weight' => (float)$product_weight
		);
	}

	public static function convertWeight($weight)
	{
		if (!$conversation_rate = Configuration::get(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE))
			$conversation_rate = 1;

		return (float)$weight*(float)$conversation_rate;
	}

	private function getIdcarrierFromIdMethod($id_method)
	{
		switch ($id_method)
		{
			case 1:
				return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_ID);
			case 10:
				return Configuration::get(DpdGeopostConfiguration::CARRIER_10_ID);
			case 9:
				return Configuration::get(DpdGeopostConfiguration::CARRIER_12_ID);
			case 27:
				return Configuration::get(DpdGeopostConfiguration::CARRIER_SAME_DAY_ID);
			default:
				return false;
		}
	}

	public static function getShipmentData($id_order)
	{
		return Db::getInstance()->getRow('
			SELECT `id_order`, `id_shipment`, `id_manifest`, `date_pickup`, `label_printed`
			FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
			WHERE `id_order`='.(int)$id_order
		);
	}

	private static function sortParcelsByReferenceNumber($a, $b)
	{
		return $a['parcelReferenceNumber'] - $b['parcelReferenceNumber'];
	}

	public function isPickupArranged()
	{
		return (!$this->date_pickup || $this->date_pickup == '0000-00-00 00:00:00') ? false : true;
	}

	public function getShipmentList($order_by, $order_way, $filter, $start, $pagination)
	{
		$shipments = DB::getInstance()->executeS('
			SELECT
				s.`id_shipment`								AS `id_shipment`,
				s.`id_order`								AS `id_order`,
				s.`id_manifest`								AS `manifest`,
				s.`label_printed`							AS `label`,
				s.`date_pickup` 							AS `date_pickup`,
				o.`date_add` 								AS `date_add`,
				o.`shipping_number`							AS `shipping_number`,
				CONCAT(a.`firstname`, " ", a.`lastname`) 	AS `customer`,

				(SELECT MAX(oh.`date_add`)
				 FROM `'._DB_PREFIX_.'order_history` oh
				 WHERE oh.`id_order` = s.`id_order`
					AND oh.`id_order_state` = "'.pSQL(Configuration::get('PS_OS_SHIPPING')).'")	AS `date_shipped`,

				(SELECT COUNT(od.`product_quantity`)
				 FROM `'._DB_PREFIX_.'order_detail`			od
				 WHERE od.`id_order` = o.`id_order`)		AS `quantity`,

				(SELECT car.`name`
				 FROM `'._DB_PREFIX_.'carrier` car
				 WHERE car.`id_carrier` = o.`id_carrier`)	AS `carrier`
			FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'` s
			LEFT JOIN `'._DB_PREFIX_.'orders` 				o 	ON (o.`id_order` = s.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'address` 				a 	ON (a.`id_address` = o.`id_address_delivery`)'.
			(version_compare(_PS_VERSION_, '1.5', '<') ? ' ' : 'WHERE o.`id_shop` = "'.(int)Context::getContext()->shop->id.'" ').
			$filter.
			($order_by && $order_way ? ' ORDER BY '.pSQL($order_by).' '.pSQL($order_way) : '').
			($start !== null && $pagination !== null ? ' LIMIT '.(int)$start.', '.(int)$pagination : '')
		);

		if (!$shipments)
			$shipments = array();

		return $shipments;
	}

	/**
	 * Get first matching CSV rule depending on given parameters
	 *
	 * @param (float) $total_weight - Current customer cart total products weight
	 * @param (int) $id_method - Id one of described carriers IDs used in DpdGeopost module
	 * @param (object) $cart - Current customer cart object
	 * @param (bool) $is_cod_carrier - is current shipping method COD
	 *
	 * @return (array) first matching CSV rule
	 */
	public static function getPriceRule($total_weight, $id_method, $id_address_delivery, $is_cod_carrier)
	{
		if (!$id_method)
			return false;

		$id_country = (int)Tools::getValue('id_country');
		if ($id_country)
			$country_iso_code = Country::getIsoById((int)$id_country);

		$id_state = (int)Tools::getValue('id_state');
		if ($id_state)
		{
			$state = new State((int)$id_state);
			$state_iso_code = $state->iso_code;
		}

		$postcode = (int)Tools::getValue('zipcode');
		$address = new Address($id_address_delivery);

		if (!isset($country_iso_code))
			$country_iso_code = Country::getIsoById((int)$address->id_country);

		if (!isset($state_iso_code))
		{
			$state = new State((int)$address->id_state);
			$state_iso_code = $state->iso_code;
		}
		if (!$postcode)
			$postcode = $address->postcode;

		$price_rules = DB::getInstance()->executeS('
			SELECT `shipping_price`, `shipping_price_percentage`, `currency`, `cod_surcharge`, `cod_surcharge_percentage`, `cod_min_surcharge`
			FROM `'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'`
			WHERE `weight_from` <= '.pSQL($total_weight).'
				AND `weight_to` >= '.pSQL($total_weight).'
				AND (`country` = "'.pSQL($country_iso_code).'" OR `country` = "*")
				AND (`region` = "'.pSQL($state_iso_code).'" OR `region` = "*")
				AND (`zip` = "'.pSQL($postcode).'" OR `zip` = "*")
				AND (`method_id` = "'.(int)$id_method.'" OR `method_id` = "*")
				AND `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');

		if (!$price_rules)
			return false;

		self::validateCurrencies($price_rules);

		if (!$price_rules)
			return false;

		self::validateCODRules($price_rules, $is_cod_carrier);

		if (!$price_rules)
			return false;

		return reset($price_rules);
	}

	private static function validateCODRules(&$price_rules, $is_cod_carrier)
	{
		foreach ($price_rules as $key => $price_rule)
			if (($price_rule['cod_surcharge'] !== '' || $price_rule['cod_surcharge_percentage'] !== '') && !$is_cod_carrier ||
				($price_rule['cod_surcharge'] === '' && $price_rule['cod_surcharge_percentage'] === '') && $is_cod_carrier
			)
				unset($price_rules[$key]);
	}

	private static function validateCurrencies(&$price_rules)
	{
		foreach ($price_rules as $key => $price_rule)
			if (!Currency::getIdByIsoCode($price_rule['currency']))
				unset($price_rules[$key]);
	}

	private static function getCarrierIdByReference($id_reference)
	{
		$id_carrier = Db::getInstance()->getValue('
			SELECT `id_carrier`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE id_reference = '.(int)$id_reference.'
				AND deleted = 0 ORDER BY id_carrier DESC
		');

		if (!$id_carrier)
			return false;
		return (int)$id_carrier;
	}

	public static function getOrderIdByShipmentId($id_shipment)
	{
		if (!$id_shipment)
			return false;

		return DB::getInstance()->getValue('
			SELECT `id_order`
			FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
			WHERE `id_shipment` = "'.(int)$id_shipment.'"
		');
	}

	public function getTotalParcelsWeight()
	{
		if (!$this->parcels)
			return false;

		$total_weight = 0;

		foreach ($this->parcels as $parcel)
			$total_weight += (float)$parcel['weight'];

		return $total_weight;
	}
	
	private function setLabelPrinted($id_shipment)
	{
		return DB::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
			SET `label_printed` = "1"
			WHERE `id_shipment` = "'.(int)$id_shipment.'"
		');
	}
}