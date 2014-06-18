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

class DpdGeopostMessagesController extends DpdGeopostController
{
	const DPD_GEOPOST_SUCCESS_MESSAGE 		= 'dpd_geopost_success_message';
	const DPD_GEOPOST_ERROR_MESSAGE 		= 'dpd_geopost_error_message';

	private $cookie;
	
	public function __construct()
	{
		parent::__construct();
		$this->cookie = new Cookie(_DPDGEOPOST_COOKIE_);
	}
	
	public function setSuccessMessage($message)
	{
		if (!is_array($message))
			$this->cookie->{self::DPD_GEOPOST_SUCCESS_MESSAGE} = $message;
	}

	public function setErrorMessage($message)
	{
		$old_message = $this->cookie->{self::DPD_GEOPOST_ERROR_MESSAGE};
		if ($old_message && Validate::isSerializedArray($old_message))
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
				$old_message = unserialize($old_message);
			else
				$old_message = Tools::unSerialize($old_message);
			
			$message = array_merge($message, $old_message);
		}
		
		if (is_array($message))
			$this->cookie->{self::DPD_GEOPOST_ERROR_MESSAGE} = serialize($message);
		else
			$this->cookie->{self::DPD_GEOPOST_ERROR_MESSAGE} = $message;
	}

	public function getSuccessMessage()
	{
		$message = $this->cookie->{self::DPD_GEOPOST_SUCCESS_MESSAGE};
		unset($this->cookie->{self::DPD_GEOPOST_SUCCESS_MESSAGE});
		return $message ? $message : '';
	}

	public function getErrorMessage()
	{
		$message = $this->cookie->{self::DPD_GEOPOST_ERROR_MESSAGE};
		if (Validate::isSerializedArray($message))
			if (version_compare(_PS_VERSION_, '1.5', '<'))
				$message = unserialize($message);
			else
				$message = Tools::unSerialize($message);
			
		unset($this->cookie->{self::DPD_GEOPOST_ERROR_MESSAGE});
		if (is_array($message))
			return array_unique($message);
		return $message ? array($message) : '';
	}
}