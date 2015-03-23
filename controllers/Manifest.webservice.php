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

class DpdGeopostManifest extends DpdGeopostWs
{
	const REFERENCE_LENGTH = 9;

	protected $service_name = 'ManifestServiceImpl';

	public $id_manifest;
	private $reference;
	public $manifest_reference_number = null; /* generated random string later on if not defined */
	public $manifest_notes = null; /* comment */
	public $shipments = array(); /* array of Shipment ID's */

	private $manifest_print_option = 'PrintManifestWithUnprintedParcels'; /* 'PrintOnlyManifest' */
	private $print_option = 'Pdf';
	private $shipment_reference_list = array();
	private $action = 'closeAndPrint';

	public function printManifest()
	{
		foreach ($this->shipments as $id_shipment)
			if (Db::getInstance()->getValue('
				SELECT `id_manifest`
				FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				WHERE `id_shipment` = '.(int)$id_shipment))
				$this->action = 'reprint';

		$params = $this->formatRequestParams();

		$print_options = array(
			'manifestPrintOption' => $this->manifest_print_option,
			'printOption' => $this->print_option
		);

		return call_user_func(array($this, $this->action), $params, $print_options);
	}

	private function closeAndPrint($params, $print_options)
	{
		$result = $this->closeManifest('manifest', $params, $print_options);

		if (!reset(self::$errors))
		{
			if (isset($result['pdfManifestFile']))
			{
				$this->id_manifest = (int)$result['manifestId'];
				$this->reference = $result['manifestReferenceNumber'];

				if (!$this->updateManifestStatus())
				{
					self::$errors[] = $this->l('Could not update manifest status locally');
					return false;
				}

				return $result['pdfManifestFile'];
			}
			else
			{
				self::$errors[] = $this->l('PDF file cannot be generated');
				return false;
			}
		}
		else
			return false;
	}

	private function reprint($params, $print_options)
	{
		$result = $this->reprintManifest('manifestReference', $params, $print_options);

		if (!reset(self::$errors))
		{
			if (isset($result['pdfManifestFile']))
				return $result['pdfManifestFile'];
			else
			{
				self::$errors[] = $this->l('PDF file cannot be generated');
				return false;
			}
		}
		else
			return false;
	}

	private function formatRequestParams()
	{
		if (!$this->manifest_reference_number)
			$this->manifest_reference_number = $this->generateReference();

		$params = array(
			'manifestReferenceNumber' => $this->manifest_reference_number,
			'manifestNotes' => $this->manifest_notes
		);

		foreach ($this->shipments as $id_shipment)
			$this->shipment_reference_list[] = array('id' => (int)$id_shipment);

		$name = ($this->action == 'closeAndPrint') ? 'shipmentReferenceList' : 'manifestReference';
		$params[$name] = $this->shipment_reference_list;

		if ($this->action == 'reprint')
		{
			$params = array();

			foreach ($this->shipments as $id_shipment)
			{
				$params['referenceNumber'] = DB::getInstance()->getValue('
					SELECT `reference`
					FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
					WHERE `id_shipment` = "'.(int)$id_shipment.'"
				');
				$params['id'] = (int)$this->getIdManifestByIdShipment($id_shipment);
			}
		}

		return $params;
	}

	private function getIdManifestByIdShipment($id_shipment)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_manifest`
			FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
			WHERE `id_shipment`='.(int)$id_shipment
		);
	}

	private function generateReference()
	{
		return Tools::strtoupper(Tools::passwdGen(self::REFERENCE_LENGTH));
	}

	private function updateManifestStatus()
	{
		if (!$this->id_manifest)
			return false;

		foreach ($this->shipments as $id_shipment)
			if (!Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				SET
					`id_manifest` = "'.(int)$this->id_manifest.'",
					`reference` = "'.pSQL($this->reference).'"
				WHERE `id_shipment`='.(int)$id_shipment))
				return false;

		return true;
	}
}