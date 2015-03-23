{**
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
*}
<script type="text/javascript">
	var dpd_ajax_uri = "{$smarty.const._DPDGEOPOST_AJAX_URI_|escape:'htmlall':'UTF-8'}";
	var dpd_token = "{$dpdgeopost_token|escape:'htmlall':'UTF-8'}";
	var dpd_search_postcode_text = "{l s='DPD - search postcode' mod='dpdgeopost'}";
	var dpd_search_postcode_empty_result_alert = "{l s='There were no suggestions to the address given county. Please enter post code manually.' mod='dpdgeopost'}";
	var dpd_address_validation_length = "{l s='DPD - Address length should be less than 70 characters.' mod='dpdgeopost'}";
	var dpd_postcode_validation_error = "{l s='DPD - The zip code is wrong.' mod='dpdgeopost'}";
	var id_dpdgeopost_shop = "{Context::getContext()->shop->id|intval}";
	var id_dpdgeopost_romania_country = "{Country::getByIso(DpdGeopost::ROMANIA_COUNTRY_ISO_CODE)|intval}";
	var dpdgetopost_16 = {if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}1{else}0{/if};
</script>

{if version_compare($smarty.const._PS_VERSION_, '1.5', '<')}
	<script type="text/javascript" src="{$smarty.const._DPDGEOPOST_JS_URI_|escape:'htmlall':'UTF-8'}address_autocomplete.js"></script>
	<script type="text/javascript" src="{$smarty.const._DPDGEOPOST_JS_URI_|escape:'htmlall':'UTF-8'}jquery-ui.min.js"></script>
{/if}