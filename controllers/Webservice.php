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

class DpdGeopostWS extends DpdGeopostController
{
	private $client; /* instance of SoapClient class */
	protected $config;
	private $params = array();
	private $last_called_function_payload;
	private $last_called_function_name;
	private $last_called_function_args = array();

	protected $service_name;
	public static $parcel_weight_warning_message = false;
	public static $notices = array();

	const APPLICATION_TYPE = 9;
	const FILENAME = 'dpdgeopost.ws';
	const ID_WEIGHT_ERROR_MESSAGE = 123;

	const DEBUG_FILENAME = 'DPDGEOPOST_DEBUG_FILENAME';
	const DEBUG_FILENAME_LENGTH = 16;

	public function __construct()
	{
		parent::__construct();

		$this->config = new DpdGeopostConfiguration;

		if ($this->config->dpd_country_select == DpdGeopostConfiguration::OTHER_COUNTRY)
			$url = $this->config->production_mode ? $this->config->ws_production_url : $this->config->ws_test_url;
		else
		{
			require_once(_DPDGEOPOST_CONTROLLERS_DIR_.'Configuration.controller.php');

			$configuration_controller = new DpdGeopostConfigurationController();
			$configuration_controller->setAvailableCountries();
			$mode = $this->config->production_mode ? 'ws_uri_prod' : 'ws_uri_test';
			$url = $configuration_controller->countries[$this->config->dpd_country_select][$mode];
		}

		if (!$url)
		{
			self::$errors[] = $this->l('Wrong WebServices URL');
			return null;
		}

		$this->params = array(
			'wsUserName' => $this->config->ws_username,
			'wsPassword' => $this->config->ws_password,
			'wsLang' => $this->context->language->iso_code,
			'applicationType' => self::APPLICATION_TYPE
		);

		try
		{
			$url .= $this->service_name.'?wsdl';
			$this->client = new SoapClient($url, array('connection_timeout' => (int)$this->config->ws_timeout, 'trace' => true));

			return $this->client;
		}
		catch (Exception $e)
		{
			self::$errors[] = $e->getMessage();
		}

		return null;
	}

	public function __call($function_name, $arguments)
	{
		self::$errors = array();
		self::$notices = array();

		if (!$this->client)
		{
			self::$errors[] = $this->l('Wrong WebServices URL');
			return false;
		}

		$result = null;
		$this->last_called_function_name = $function_name;

		if (isset($arguments[0]) && !is_array($arguments[0]) && isset($arguments[1]) && is_array($arguments[1]))
		{
			$this->params[$arguments[0]] = $arguments[1];

			if (isset($arguments[2]) && is_array($arguments[2]))
				foreach ($arguments[2] as $key => $argument)
					$this->params[$key] = $argument;

			$this->last_called_function_args = $this->params;

			try
			{
				if (!$result = $this->client->$function_name($this->params))
					self::$errors[] = $this->l('Could not connect to webservice server. Please check webservice URL');
			}
			catch (Exception $e)
			{
				self::$errors[] = $e->getMessage();
			}

			if (isset($result->return))
				$result = $result->return;

			if (isset($result->faultstring))
				self::$errors[] = $result->faultstring;

			if (isset($result->result))
				$this->getError($result->result);

			if (isset($result->error))
			{
				$result_response = $this->objectToArray($result);
				$error_code = '';
				$error_text = '';

				$transaction_id = isset($result_response['transactionId']) ? '; '.$this->l('Transaction Id:').' '.$result_response['transactionId']: '';

				if ($result_response['error']['text'])
				{
					$error_text = $result_response['error']['text'];

					if (isset($result_response['error']['code']))
						$error_code = $result_response['error']['code'];
				}

				$message = $this->getTranslatableMessage($error_code);

				if ($error_text)
					self::$errors[] = $error_text.$transaction_id;

				if ($message)
					self::$errors[] = $message;
			}

			if ($this->config->debug_mode)
				$this->debug($result);

			return $this->objectToArray($result);
		}

		return false;
	}

	private function objectToArray($response)
	{
		if (!is_object($response) && !is_array($response))
			return $response;

		return array_map(array($this, 'objectToArray'), (array)$response);
	}

	protected function getError($result)
	{
		if (is_object($result))
			$result = $this->objectToArray($result);

		$transaction_id = isset($result['transactionId']) ? '; '.$this->l('Transaction Id:').' '.$result['transactionId'] : '';
		$error_text = '';
		$error_code = '';

		if (isset($result['detail']['EShopException']['error']['text']))
		{
			$error_text = $result['detail']['EShopException']['error']['text'];

			if (isset($result['detail']['EShopException']['error']['code']))
				$error_code = $result['detail']['EShopException']['error']['code'];
		}
		elseif (isset($result['priceList']['error']['text']))
		{
			$error_text = $result['priceList']['error']['text'];

			if (isset($result['priceList']['error']['code']))
				$error_code = $result['priceList']['error']['code'];
		}
		elseif (isset($result['resultList']['error']['text']))
		{
			$error_text = $result['resultList']['error']['text'];

			if (isset($result['resultList']['error']['code']))
				$error_code = $result['resultList']['error']['code'];
		}
		elseif (isset($result['error']['text']))
		{
			$error_text = $result['error']['text'];

			if (isset($result['error']['code']))
				$error_code = $result['error']['code'];
		}
		elseif (isset($result['prestashop_message']))
			self::$errors[] = $result['prestashop_message'].$transaction_id;

		$message = $this->getTranslatableMessage($error_code);

		if ($error_code == self::ID_WEIGHT_ERROR_MESSAGE)
		{
			self::$parcel_weight_warning_message = true;
			self::$notices[] = $error_text.$transaction_id;

			if ($message)
				self::$notices[] = $message;
		}
		else
		{
			if ($error_text)
				self::$errors[] = $error_text.$transaction_id;

			if ($message)
				self::$errors[] = $message;
		}
	}

	private function getTranslatableMessage($error_code)
	{
		require_once(_DPDGEOPOST_MODULE_DIR_.'dpdgeopost.lang.php');

		$language = new DpdGeopostLanguage();

		return $language->getTranslation($error_code);
	}

	public static function createDebugFileIfNotExists()
	{
		if ((!$debug_filename = Configuration::get(self::DEBUG_FILENAME)) || !self::isDebugFileName($debug_filename))
		{
			$debug_filename = Tools::passwdGen(self::DEBUG_FILENAME_LENGTH).'.html';
			Configuration::updateValue(self::DEBUG_FILENAME, $debug_filename);
		}

		if (!file_exists(_DPDGEOPOST_MODULE_DIR_.$debug_filename))
		{
			$file = fopen(_DPDGEOPOST_MODULE_DIR_.$debug_filename, 'w');
			fclose($file);
		}

		return $debug_filename;
	}

	private static function isDebugFileName($debug_filename)
	{
		return Tools::strlen($debug_filename) == (int)self::DEBUG_FILENAME_LENGTH + 5 && preg_match('#^[a-zA-Z0-9]+\.html$#', $debug_filename);
	}

	private function debug($result = null)
	{
		$debug_html = '';
		$url = $this->config->production_mode ? $this->config->ws_production_url : $this->config->ws_test_url;
		$url .= $this->service_name.'?wsdl';

		if ($this->last_called_function_name)
		{
			$debug_html .= '<h2 style="padding: 10px 0 10px 0; display: block; border-top: solid 2px #000000; border-bottom: solid 2px #000000;">
			'.$url.'<br />
			['.date('Y-m-d H:i:s').']</h2><h2>Function \''.$this->last_called_function_name.'\' params
			</h2><pre>';
			$debug_html .= print_r($this->last_called_function_args, true);
			$debug_html .= '</pre>';
		}

		if ($this->last_called_function_payload = (string)$this->client->__getLastRequest())
			$debug_html .= '<h2>Request</h2><pre>'.$this->displayPayload().'</pre>';

		if ($result)
		{
			$result = print_r($result, true);
			$debug_html .= '<h2>Response</h2><pre>';
			$debug_html .= strip_tags($result);
			$debug_html .= '</pre>';

			if (self::$errors)
				$debug_html .= '<h2>Response errors</h2><pre>'.implode('<br />', array_unique(self::$errors)).'</pre>';

			if (self::$notices)
				$debug_html .= '<h2>Response notices</h2><pre>'.implode('<br />', array_unique(self::$notices)).'</pre>';
		}
		else
			$debug_html .= '<h2>Errors</h2><pre>'.print_r(self::$errors, true).'</pre>';

		if ($debug_html)
		{
			$debug_filename = $this->createDebugFileIfNotExists();

			$current_content = Tools::file_get_contents(_DPDGEOPOST_MODULE_DIR_.$debug_filename);
			file_put_contents(_DPDGEOPOST_MODULE_DIR_.$debug_filename, $debug_html.$current_content, LOCK_EX);
		}
	}

	/* only for debugging purposes */
	private function displayPayload()
	{
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $this->last_called_function_payload);
		$token = strtok($xml, "\n");
		$result = '';
		$pad = 0;
		$matches = array();
		while ($token !== false)
		{
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches))
				$indent = 0;
			elseif (preg_match('/^<\/\w/', $token, $matches))
			{
				$pad -= 4;
				$indent = 0;
			}
			elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches))
				$indent = 4;
			else
				$indent = 0;

			$line    = str_pad($token, Tools::strlen($token) + $pad, ' ', STR_PAD_LEFT);
			$result .= $line."\n";
			$token   = strtok("\n");
			$pad    += $indent;
		}

		return htmlentities($result);
	}
}