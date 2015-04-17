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
{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
	<br />
{/if}
<div id="help_container"{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')} class="panel"{/if}>
	<img src="{$smarty.const._DPDGROUP_IMG_URI_|escape:'htmlall':'UTF-8'}pdf.gif" />
	<a href="{$module_link|escape:'htmlall':'UTF-8'}&menu=help&print_pdf">{l s='User guide in English' mod='dpdgroup'}</a>
</div>