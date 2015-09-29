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
{if version_compare($smarty.const._PS_VERSION_, '1.6', '>=')}
	<nav class="navbar navbar-default" role="navigation">
		<div>
			<ul class="nav navbar-nav">
				{foreach from=$menutabs item=tab}
					<li class="{if $tab.active}active{/if}">
						<a id="{$tab.short|escape:'htmlall':'UTF-8'}" href="{$tab.href|escape:'htmlall':'UTF-8'}">
							<span class="{$tab.imgclass|escape:'htmlall':'UTF-8'}" style="margin-right: 5px;"></span>
							{$tab.desc|escape:'htmlall':'UTF-8'}
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
	</nav>
{else}
	<div class="toolbar-placeholder">
		<div class="toolbarBox toolbarHead">
			<ul class="cc_button">
				<li>
					<a id="shipment_list_page" href="{$module_link|escape:'htmlall':'UTF-8'}&menu=shipment_list" class="toolbar_btn">
						<span class="process-icon-shipment_list shipment_list"></span>
						<div>{l s='Shipment list' mod='dpdgroup'}</div>
					</a>
				</li>
				<li>
					<a id="csv_page" href="{$module_link|escape:'htmlall':'UTF-8'}&menu=csv" class="toolbar_btn">
						<span class="process-icon-csv csv"></span>
						<div>{l s='Price rules' mod='dpdgroup'}</div>
					</a>
				</li>
				<li>
					<a id="postcode_page" href="{$module_link|escape:'htmlall':'UTF-8'}&menu=postcode" class="toolbar_btn">
						<span class="process-icon-envelope envelope"></span>
						<div>{l s='Postcodes' mod='dpdgroup'}</div>
					</a>
				</li>
				<li>
					<a id="settings_page" href="{$module_link|escape:'htmlall':'UTF-8'}&menu=configuration" class="toolbar_btn">
						<span class="process-icon-settings settings"></span>
						<div>{l s='Settings' mod='dpdgroup'}</div>
					</a>
				</li>
				<li>
					<a id="help_page" href="{$module_link|escape:'htmlall':'UTF-8'}&menu=help" class="toolbar_btn">
						<span class="process-icon-help help"></span>
						<div>{l s='Help' mod='dpdgroup'}</div>
					</a>
				</li>
			</ul>
			<div class="pageTitle">
				<h3>
					<span id="current_obj" style="font-weight: normal;">
						<span class="breadcrumb item-0 ">
							{section name=breadcrumb_iteration loop=$path}
								{if $smarty.section.breadcrumb_iteration.index != 0}
									<img src="{$smarty.const._DPDGROUP_IMG_URI_|escape:'htmlall':'UTF-8'}/separator_breadcrumb.png" style="margin-right:5px" alt=">">
								{/if}
								<span class="breadcrumb item-1">
									{$path[breadcrumb_iteration]|escape:'htmlall':'UTF-8'}
								</span>
							{/section}
						</span>
					</span>
				</h3>
			</div>
		</div>
	</div>
{/if}