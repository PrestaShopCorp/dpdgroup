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

class DpdGroupMessagesController extends DpdGroupController
{
	const DPD_GROUP_SUCCESS_MESSAGE = 'dpd_geopost_success_message';
	const DPD_GROUP_ERROR_MESSAGE = 'dpd_geopost_error_message';

	private $cookie;

	public function __construct()
	{
		parent::__construct();

		$this->cookie = new Cookie(_DPDGROUP_COOKIE_);
	}

	public function setSuccessMessage($message)
	{
		if (!is_array($message))
			$this->cookie->{self::DPD_GROUP_SUCCESS_MESSAGE} = $message;
	}

	public function setErrorMessage($message)
	{
		$old_message = $this->cookie->{self::DPD_GROUP_ERROR_MESSAGE};

		if ($old_message && Validate::isSerializedArray($old_message))
		{
			$old_message = Tools::jsonDecode($old_message);
			$message = array_merge($message, $old_message);
		}

		$this->cookie->{self::DPD_GROUP_ERROR_MESSAGE} = is_array($message) ? Tools::jsonEncode($message) : $message;
	}

	public function getSuccessMessage()
	{
		$message = $this->cookie->{self::DPD_GROUP_SUCCESS_MESSAGE};

		unset($this->cookie->{self::DPD_GROUP_SUCCESS_MESSAGE});

		return $message ? $message : '';
	}

	public function getErrorMessage()
	{
		$message = $this->cookie->{self::DPD_GROUP_ERROR_MESSAGE};

		if (Validate::isSerializedArray($message))
			$message = Tools::jsonDecode($message);

		unset($this->cookie->{self::DPD_GROUP_ERROR_MESSAGE});

		if (is_array($message))
			return array_unique($message);

		return $message ? array($message) : '';
	}
}