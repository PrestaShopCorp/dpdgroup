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

require_once(_DPDGROUP_CLASSES_DIR_.'Postcode.php');

class DpdGroupPostcodeController extends DpdGroupController
{
	private $csv_titles = array();

	const SETTINGS_SAVE_POSTCODE_ACTION 	= 'saveModulePostcodeSettings';
	const SETTINGS_DOWNLOAD_POSTCODE_ACTION	= 'downloadModulePostcodeSettings';
	const SETTINGS_RESTORE_POSTCODE_ACTION	= 'restoreModulePostcodeSettings';
	const SETTINGS_DELETE_POSTCODE_ACTION 	= 'deleteModulePostcodeSettings';
	const FILENAME 							= 'Postcode.controller';
	const DEFAULT_FIRST_LINE_INDEX			= 2;

	public function __construct()
	{
		parent::__construct();

		$this->csv_titles = array(
			'id_postcode'	=> $this->l('ID'),
			'postcode' 		=> $this->l('Postcode'),
			'region' 		=> $this->l('Region'),
			'city' 			=> $this->l('City'),
			'address' 		=> $this->l('Address')
		);
	}

	public function getPostcodePage()
	{
		$keys_array = array('id_postcode', 'postcode', 'region', 'city', 'address');

		if (Tools::isSubmit('submitFilterButtonpostcode'))
			foreach ($_POST as $key => $value)
			{
				if (strpos($key, 'postcodeFilter_') !== false && $_POST[$key] !== '') // looking for filter values in $_POST
					$this->context->cookie->$key = $value;
				else
					unset($this->context->cookie->{'postcodeFilter_'.$key});
			}

		if (Tools::isSubmit('submitResetpostcode'))
		{
			foreach ($keys_array as $key)
			{
				if ($this->context->cookie->{'postcodeFilter_'.$key} !== null)
				{
					unset($this->context->cookie->{'postcodeFilter_'.$key});
					unset($_POST['postcodeFilter_'.$key]);
				}
			}
		}

		$selected_pagination = Tools::getValue('pagination', '20');
		$page = Tools::getValue('current_page', '1');
		$order_by = Tools::getValue('postcodeOrderBy', DpdGroupPostcode::DEFAULT_ORDER_BY);
		$order_way = Tools::getValue('postcodeOrderWay', DpdGroupPostcode::DEFAULT_ORDER_WAY);

		if ($page < 1)
			$page = 1;

		$start = ($selected_pagination * $page) - $selected_pagination;

		if ($start < 0)
			$start = 0;

		$limit = ' LIMIT '.(int)$start.', '.(int)$selected_pagination.' ';
		$filter = $this->getFilterQuery($keys_array, 'postcode');

		$selected_products_data = DpdGroupPostcode::getAllData($filter, $limit, $order_by, $order_way);
		$list_total = count(DpdGroupPostcode::getAllData($filter));
		$pagination = array(20, 50, 100, 300);

		$total_pages = ceil($list_total / $selected_pagination);

		if (!$total_pages)
			$total_pages = 1;

		$this->context->smarty->assign(array(
			'saveAction' => $this->module_instance->module_url.'&menu=postcode',
			'postcode_data' => $selected_products_data,
			'page' => $page,
			'total_pages' => $total_pages,
			'pagination' => $pagination,
			'list_total' => $list_total,
			'selected_pagination' => $selected_pagination,
			'order_by' => $order_by,
			'order_way' => $order_way
		));

		$template_filename = version_compare(_PS_VERSION_, '1.6', '>=') ? 'postcode_16' : 'postcode';

		return $this->context->smarty->fetch(_DPDGROUP_TPL_DIR_.'admin/'.$template_filename.'.tpl');
	}

	public static function init()
	{
		$controller = new DpdGroupPostcodeController;

		if (Tools::isSubmit(DpdGroupPostcodeController::SETTINGS_SAVE_POSTCODE_ACTION))
		{
			$csv_data = $controller->readCSVData();

			if ($csv_data === false)
			{
				DpdGroup::addFlashError($controller->l('Wrong CSV file'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}

			$message = $controller->validatePostcodeData($csv_data);
			if ($message !== true)
				return $controller->module_instance->outputHTML($controller->module_instance->displayError(implode('<br />', $message)));

			if ($controller->saveCSVData($csv_data))
			{
				DpdGroup::addFlashMessage($controller->l('CSV data was successfully saved'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
			else
			{
				DpdGroup::addFlashError($controller->l('CSV data could not be saved'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
		}

		if (Tools::isSubmit(DpdGroupPostcodeController::SETTINGS_DOWNLOAD_POSTCODE_ACTION))
			$controller->generateCSV();

		if (Tools::isSubmit('submitBulkdeletepostcode'))
		{
			$postcodes_ids = Tools::getValue('postcodeBox');

			if (empty($postcodes_ids))
			{
				DpdGroup::addFlashError($controller->l('No postcodes selected'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}

			$errors = array();

			foreach ($postcodes_ids as $id_postcode)
			{
				$postcode = new DpdGroupPostcode((int)$id_postcode);

				if (!$postcode->delete())
					$errors[] = $controller->l('Could not delete postcode, #').(int)$id_postcode;
			}

			if (empty($errors))
			{
				DpdGroup::addFlashMessage($controller->l('Selected postcodes deleted successfully'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
			else
			{
				DpdGroup::addFlashError(implode('<br />', $errors));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
		}

		if (Tools::isSubmit('delete_postcode'))
		{
			$postcode = new DpdGroupPostcode((int)Tools::getValue('id_postcode'));

			if ($postcode->delete())
			{
				DpdGroup::addFlashMessage($controller->l('Postcode was deleted successfully'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
			else
			{
				DpdGroup::addFlashError($controller->l('Could not delete postcode'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
		}

		if (Tools::isSubmit(self::SETTINGS_DELETE_POSTCODE_ACTION))
		{
			$postcode = new DpdGroupPostcode();

			if ($postcode->deleteAllData())
			{
				DpdGroup::addFlashMessage($controller->l('All postcodes deleted successfully'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
			else
			{
				DpdGroup::addFlashError($controller->l('Could not delete all postcodes'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}
		}

		if (Tools::isSubmit(self::SETTINGS_RESTORE_POSTCODE_ACTION))
		{
			$postcode = new DpdGroupPostcode();

			if (!$postcode->deleteAllData())
			{
				DpdGroup::addFlashError($controller->l('Could not delete all postcodes'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
			}

			$sql = array();
			$postcodes = Tools::file_get_contents(_DPDGROUP_SQL_DIR_.'data.sql');
			$postcodes = str_replace('zitec_dpd_postcodes', _DB_PREFIX_._DPDGROUP_POSTCODE_DB_, $postcodes);
			$postcodes = explode(';', $postcodes);

			foreach ($postcodes as $query)
				if ($query)
					$sql[] = $query.';';

			foreach ($sql as $query)
				if (Db::getInstance()->execute($query) == false)
				{
					DpdGroup::addFlashError($controller->l('Could not restore default postcodes data'));
					Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
				}

			DpdGroup::addFlashMessage($controller->l('Default postcodes data restored successfully'));
			Tools::redirectAdmin($controller->module_instance->module_url.'&menu=postcode');
		}

		return null;
	}

	public function generateCSV()
	{
		$csv_data = array($this->csv_titles);
		$csv_data = array_merge($csv_data, DpdGroupPostcode::getCSVData());
		$this->arrayToCSV($csv_data, _DPDGROUP_CSV_FILENAME_.'.csv', _DPDGROUP_CSV_DELIMITER_);
	}

	private function arrayToCSV($array, $filename, $delimiter)
	{
		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w');
		// loop over the input array
		foreach ($array as $line)
			fputcsv($f, $line, $delimiter); // generate csv lines from the inner arrays
		// rewrind the "file" with the csv lines
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv; charset=utf-8');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachement; filename="'.$filename.'"');
		// make php send the generated csv lines to the browser
		fpassthru($f);

		exit;
	}

	private function validatePostcodeData($postcode_data)
	{
		$errors = array();
		$postcode_data_count = count($postcode_data);

		if (!$this->validateCSVStructure($postcode_data, $postcode_data_count))
		{
			$errors[] = $this->l('Wrong CSV file structure or empty lines');
			return $errors;
		}

		$id_postcode_validation = $this->validatePostcodeCSVColumn($postcode_data, $postcode_data_count, DpdGroupPostcode::COLUMN_ID_POSTCODE);

		if ($id_postcode_validation !== true)
			$errors[] = sprintf($this->l('ID is not valid - invalid lines: %s'), $id_postcode_validation);

		$region_validation = $this->validatePostcodeCSVColumn($postcode_data, $postcode_data_count, DpdGroupPostcode::COLUMN_REGION);

		if ($region_validation !== true)
			$errors[] = sprintf($this->l('Region is not valid - invalid lines: %s'), $region_validation);

		$postcode_validation = $this->validatePostcodeCSVColumn($postcode_data, $postcode_data_count, DpdGroupPostcode::COLUMN_POSTCODE);

		if ($postcode_validation !== true)
			$errors[] = sprintf($this->l('Postcode is not valid - invalid lines: %s'), $postcode_validation);

		$city_validation = $this->validatePostcodeCSVColumn($postcode_data, $postcode_data_count, DpdGroupPostcode::COLUMN_CITY);

		if ($city_validation !== true)
			$errors[] = sprintf($this->l('City is not valid - invalid lines: %s'), $city_validation);

		$address_validation = $this->validatePostcodeCSVColumn($postcode_data, $postcode_data_count, DpdGroupPostcode::COLUMN_ADDRESS);

		if ($address_validation !== true)
			$errors[] = sprintf($this->l('Address is not valid - invalid lines: %s'), $address_validation);

		if (!empty($errors))
			return $errors;

		return true;
	}

	private function validatePostcodeCSVColumn($postcodes_data, $postcodes_data_count, $column)
	{
		$wrong_lines = array();

		for ($i = 0; $i < $postcodes_data_count; $i++)
		{
			switch ($column)
			{
				case DpdGroupPostcode::COLUMN_ID_POSTCODE:
					if (!Validate::isUnsignedId($postcodes_data[$i][DpdGroupPostcode::COLUMN_ID_POSTCODE]))
						$wrong_lines[] = ($i + self::DEFAULT_FIRST_LINE_INDEX);
					break;
				case DpdGroupPostcode::COLUMN_REGION:
					if (!Validate::isCityName($postcodes_data[$i][DpdGroupPostcode::COLUMN_REGION]))
						$wrong_lines[] = ($i + self::DEFAULT_FIRST_LINE_INDEX);
					break;
				case DpdGroupPostcode::COLUMN_POSTCODE:
					if (!Validate::isPostCode($postcodes_data[$i][DpdGroupPostcode::COLUMN_POSTCODE]))
						$wrong_lines[] = ($i + self::DEFAULT_FIRST_LINE_INDEX);
					break;
				case DpdGroupPostcode::COLUMN_CITY:
					if (!Validate::isCityName($postcodes_data[$i][DpdGroupPostcode::COLUMN_CITY]))
						$wrong_lines[] = ($i + self::DEFAULT_FIRST_LINE_INDEX);
					break;
				case DpdGroupPostcode::COLUMN_ADDRESS:
					if (!Validate::isCityName($postcodes_data[$i][DpdGroupPostcode::COLUMN_ADDRESS]))
						$wrong_lines[] = ($i + self::DEFAULT_FIRST_LINE_INDEX);
					break;
			}
		}

		return empty($wrong_lines) ? true : implode(', ', $wrong_lines);
	}

	private function validateCSVStructure($csv_data, $csv_data_count)
	{
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!isset($csv_data[$i][DpdGroupPostcode::COLUMN_ROUTE]))
				return false;

		return true;
	}

	private function readCSVData()
	{
		if ($_FILES[DpdGroupPostcode::CSV_POSTCODE_FILE]['error'] || !preg_match('/.*\.csv$/i', $_FILES[DpdGroupPostcode::CSV_POSTCODE_FILE]['name']))
			return false;

		$csv_data = array();
		$row = 0;

		if (($handle = fopen($_FILES[DpdGroupPostcode::CSV_POSTCODE_FILE]['tmp_name'], 'r')) !== false)
		{
			while (($data = fgetcsv($handle, 1000, _DPDGROUP_CSV_DELIMITER_)) !== false)
			{
				if (!$data) continue;
				$csv_data_line = array();
				$row++;
				if ($row == 1)
					continue;
				$num = count($data);
				$row++;
				for ($i = 0; $i < $num; $i++)
					$csv_data_line[] = $data[$i];
				$csv_data[] = $csv_data_line;
			}
			fclose($handle);
		}

		return $csv_data;
	}

	private function saveCSVData($csv_data)
	{
		$success = true;

		foreach ($csv_data as $data)
		{
			$csv = new DpdGroupPostcode((int)$data[DpdGroupPostcode::COLUMN_ID_POSTCODE]);
			$csv->id_postcode	= $data[DpdGroupPostcode::COLUMN_ID_POSTCODE];
			$csv->postcode 		= $data[DpdGroupPostcode::COLUMN_POSTCODE];
			$csv->region 		= $data[DpdGroupPostcode::COLUMN_REGION];
			$csv->city 			= $data[DpdGroupPostcode::COLUMN_CITY];
			$csv->address 		= $data[DpdGroupPostcode::COLUMN_ADDRESS];

			$success &= $csv->save();
		}

		return $success;
	}
}