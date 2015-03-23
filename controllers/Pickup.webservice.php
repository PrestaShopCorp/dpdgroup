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

class DpdGeopostPickup extends DpdGeopostWs
{
	protected $service_name = 'PickupOrderServiceImpl';

	public $id_shipment;

	public $date;

	public $from_time;

	public $to_time;

	public $contact_email;

	public $contact_name;

	public $contact_phone;

	public $special_instruction = null;

	public $reference_number;

	public function arrange()
	{
		$pieces_sorted_by_country = $this->formatPieces();

		if (self::$errors)
			return false;

		foreach ($pieces_sorted_by_country as $pieces)
		{
			$params = array(
				'date' => str_replace('-', '', $this->date),
				'fromTime' => str_replace(':', '', $this->from_time),
				'toTime' => str_replace(':', '', $this->to_time),
				'contactName' => $this->contact_name,
				'contactPhone' => $this->contact_phone,
				'contactEmail' => $this->contact_email,
				'specialInstruction' => $this->special_instruction,
				'referenceNumber' => $this->reference_number,
				'payerId' => $this->config->payer_id,
				'senderAddressId' => $this->config->sender_id,
				'pieces' => $pieces
			);

			$this->createPickupOrder('pickupOrderList', $params);

			if (!self::$errors && !self::$notices)
				$this->recordPickUpDate($pieces);
			else
				return false;
		}

		if (self::$errors)
			return false;

		return true;
	}

	private function recordPickUpDate($pieces)
	{
		foreach ($pieces as $piece)
			if (!Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				SET `date_pickup`="'.pSQL($this->date).' '.pSQL($this->from_time).'"
				WHERE `id_shipment`='.(int)$piece['id_shipment']))
				self::$errors[] = sprintf($this->l('Pickup was successfully created, but could not be recorded locally for shipment #%d'),
					(int)$piece['id_shipment']);

		return true;
	}

	private function formatPieces()
	{
		if (!$this->id_shipment)
		{
			self::$errors[] = $this->l('Shipment ID is missing');
			return false;
		}

		$pieces = array();

		if (!is_array($this->id_shipment))
			$this->id_shipment = array($this->id_shipment);

		foreach ($this->id_shipment as $id_shipment)
		{
			$id_order = (int)DpdGeopostShipment::getOrderIdByShipmentId($id_shipment);
			$shipment = new DpdGeopostShipment($id_order);

			if (!(int)$shipment->id_order)
			{
				self::$errors[] = sprintf($this->l('Order #%d does not exists'), (int)$shipment->id_order);
				return false;
			}

			if (!isset($pieces[$shipment->receiverCountryCode]))
				$pieces[$shipment->receiverCountryCode] = array();

			$pieces[$shipment->receiverCountryCode][] = array(
				'serviceCode' => (int)$shipment->mainServiceCode,
				'quantity' => count($shipment->parcels),
				'weight' => (float)$shipment->getTotalParcelsWeight(),
				'destinationCountryCode' => $shipment->receiverCountryCode,
				'id_shipment' => (int)$id_shipment
			);
		}

		return $pieces;
	}
}