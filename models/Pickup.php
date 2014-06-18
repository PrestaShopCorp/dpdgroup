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
	
class DpdGeopostPickup extends DpdGeopostWs
{
	protected $targetNamespace = 'http://it4em.yurticikargo.com.tr/eshop/pickuporder';
	protected $serviceName = 'PickupOrderServiceImpl';
	
	public $id_shipment;
	
	public $date;
	
	public $fromTime;
	
	public $toTime;
	
	public $contactEmail;
	
	public $contactName;
	
	public $contactPhone;
	
	public $specialInstruction = null;
	
	public $referenceNumber;
	
	public function arrange()
	{
		$pieces_sorted_by_country = $this->formatPieces();
		
		if (self::$errors)
			return false;
		
		foreach ($pieces_sorted_by_country as $pieces)
		{
			$params = array(
				'date' => pSQL(str_replace('-', '', $this->date)),
				'fromTime' => pSQL(str_replace(':', '', $this->fromTime)),
				'toTime' => pSQL(str_replace(':', '', $this->toTime)),
				'contactName' => pSQL($this->contactName),
				'contactPhone' => pSQL($this->contactPhone),
				'contactEmail' => pSQL($this->contactEmail),
				'specialInstruction' => pSQL($this->specialInstruction),
				'referenceNumber' => pSQL($this->referenceNumber)
			);
			
			
			$params[] = array(
				'name' => 'pieces',
				'data' => $pieces
			);
	
			$this->createPickupOrder('pickupOrderList', $params);
			
			if (!self::$errors)
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
				SET `date_pickup`="'.pSQL($this->date).' '.pSQL($this->fromTime).'"
				WHERE `id_shipment`='.(int)$piece['id_shipment'])
			)
				self::$errors[] = sprintf($this->l('Pickup was successfully created, but could not be recorded locally for shipment #%d'));
		
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
				'destinationCountryCode' => pSQL($shipment->receiverCountryCode),
				'id_shipment' => (int)$id_shipment
			);
		}
		
		return $pieces;
	}
}