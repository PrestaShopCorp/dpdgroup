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


class DpdGroupShipmentController extends DpdGroupController
{
	const DEFAULT_ORDER_BY 	= 'id_shipment';
	const DEFAULT_ORDER_WAY = 'desc';
	const FILENAME 			= 'ShipmentsList.controller';

	public function __construct()
	{
		parent::__construct();
		$this->init();
	}

	private function init()
	{
		if (Tools::isSubmit('printManifest'))
		{
			if ($shipment_ids = Tools::getValue('ShipmentsBox'))
			{
				$manifest = new DpdGroupManifest;
				$manifest->shipments = $shipment_ids;

				if ($pdf_content = $manifest->printManifest())
				{
					foreach ($shipment_ids as $id_shipment)
					{
						$shipment = new DpdGroupShipment;
						$shipment->getAndSaveTrackingInfo((int)$id_shipment);
					}

					ob_end_clean();
					header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="manifest_'.time().'.pdf"');
					echo $pdf_content;
					exit;
				}
				else
				{
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(DpdGroupManifest::$errors)
						)
					);
				}
			}
			else
				$this->module_instance->outputHTML($this->module_instance->displayError($this->l('No selected shipments')));
		}

		if (Tools::isSubmit('printLabels'))
		{
			if ($shipment_ids = Tools::getValue('ShipmentsBox'))
			{
				$shipment = new DpdGroupShipment;

				if ($pdf_content = $shipment->getLabelsPdf($shipment_ids))
				{
					ob_end_clean();
					header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="shipment_labels_'.time().'.pdf"');
					echo $pdf_content;
				}
				else
				{
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(DpdGroupManifest::$errors)
						)
					);
				}
			}
			else
			{
				$this->module_instance->outputHTML(
					$this->module_instance->displayError(
						$this->l('Select at least one shipment')
					)
				);
			}
		}

		if (Tools::isSubmit('changeOrderStatus'))
		{
			if ($shipment_ids = Tools::getValue('ShipmentsBox'))
			{
				foreach ($shipment_ids as $id_shipment)
				{
					$id_order = DpdGroupShipment::getOrderIdByShipmentId((int)$id_shipment);

					if (!self::changeOrderStatusToShipped($id_order))
					{
						self::$errors[] = sprintf($this->l('Can not continue: shipment #%d order status could not be updated'), $id_shipment);
						break;
					}
				}

				if (self::$errors)
				{
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(self::$errors)
						)
					);
				}
				else
				{
					DpdGroup::addFlashMessage($this->l('Selected orders statuses were successfully updated'));
					Tools::redirectAdmin($this->module_instance->module_url.'&menu=shipment_list');
				}
			}
			else
			{
				$this->module_instance->outputHTML(
					$this->module_instance->displayError(
						$this->l('Select at least one shipment')
					)
				);
			}
		}
	}

	public function getShipmentList()
	{
		$keys_array = array('id_shipment', 'date_shipped', 'id_order', 'date_add', 'carrier', 'customer',
			'quantity', 'manifest', 'label', 'date_pickup');

		if (Tools::isSubmit('submitFilterButtonShipments'))
			foreach ($_POST as $key => $value)
			{
				if (strpos($key, 'ShipmentsFilter_') !== false) // looking for filter values in $_POST
				{
					if (is_array($value))
						$this->context->cookie->$key = serialize($value);
					else
						$this->context->cookie->$key = $value;
				}
			}

		if (Tools::isSubmit('submitResetShipments'))
		{
			foreach ($keys_array as $key)
			{
				if ($this->context->cookie->__isset('ShipmentsFilter_'.$key))
				{
					$this->context->cookie->__unset('ShipmentsFilter_'.$key);
					unset($_POST['ShipmentsFilter_'.$key]);
				}
			}
		}

		$page = (int)Tools::getValue('submitFilterShipments');

		if (!$page)
			$page = 1;

		$selected_pagination = (int)Tools::getValue('pagination', $this->pagination[0]);
		$start = ($selected_pagination * $page) - $selected_pagination;
		$order_by = Tools::getValue('ShipmentOrderBy', self::DEFAULT_ORDER_BY);
		$order_way = Tools::getValue('ShipmentOrderWay', self::DEFAULT_ORDER_WAY);
		$filter = $this->getFilterQuery($keys_array, 'Shipments');
		$shipment = new DpdGroupShipment();
		$shipments = $shipment->getShipmentList($order_by, $order_way, $filter, $start, $selected_pagination);
		$list_total = count($shipment->getShipmentList($order_by, $order_way, $filter, null, null));
		$total_pages = ceil($list_total / $selected_pagination);

		if (!$total_pages)
			$total_pages = 1;

		$shipments_count = count($shipments);

		for ($i = 0; $i < $shipments_count; $i++)
		{
			$order = new Order((int)$shipments[$i]['id_order']);
			$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
			$shipments[$i]['carrier_url'] = $carrier->url;
		}

		$this->context->smarty->assign(array(
			'full_url' 				=> $this->module_instance->module_url.'&menu=shipment_list&ShipmentOrderBy='.$order_by.'&ShipmentOrderWay='.$order_way,
			'employee' 				=> $this->context->employee,
			'shipments'			  	=> $shipments,
			'page'				  	=> $page,
			'selected_pagination'   => $selected_pagination,
			'pagination'			=> $this->pagination,
			'total_pages'			=> $total_pages,
			'list_total'			=> $list_total,
			'order_by'	   			=> $order_by,
			'order_way'	  			=> $order_way,
			'order_link'			=> DpdGroup::getAdminOrderLink()
		));

		$template_filename = version_compare(_PS_VERSION_, '1.6', '>=') ? 'shipment_list_16' : 'shipment_list';

		return $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/'.$template_filename.'.tpl');
	}

	public static function changeOrderStatusToShipped($id_order)
	{
		if (!$id_order)
			return false;

		$order = new Order((int)$id_order);

		if (!Validate::isLoadedObject($order))
			return false;

		if ($order->current_state == Configuration::get('PS_OS_SHIPPING'))
			return true;

		if ($order->setCurrentState((int)Configuration::get('PS_OS_SHIPPING'), (int)Context::getContext()->employee->id) === false)
			return false;

		return true;
	}
}