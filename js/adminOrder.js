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

$(document).ready(function(){
	$('#dpd_shipping_method, #dpdgeopost_id_address').live('change', function(){
		$('#ajax_running').slideDown();
		
		var method_id = $('#dpd_shipping_method').val();
		
		$.ajax({
			type: "POST",
			async: true,
			url: dpd_geopost_ajax_uri,
			dataType: "json",
			global: false,
			data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
				  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
				  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
				  "&calculatePrice=true" +
				  "&method_id=" + encodeURIComponent(method_id) +
				  "&id_address=" + encodeURIComponent($('#dpdgeopost_id_address').val()) +
				  "&id_order=" + encodeURIComponent(id_order),
			success: function(resp)
			{
				if (resp.error || resp.notice)
				{
					if (resp.notice)
					{
						$('#dpdgeopost_notice_container').hide().html('<p class="warn">'+resp.notice+'</p>').slideDown();
						changeShipmentCreationButtonAccessibility(true);
					}
					else
					{
						$('#dpdgeopost_notice_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
						changeShipmentCreationButtonAccessibility(false);
					}
				}
				else
				{
					$('#dpdgeopost_notice_container').slideUp().html('');
					changeShipmentCreationButtonAccessibility(true);
				}
				
				if (resp.price == '---')
					changeShipmentCreationButtonAccessibility(false);
				
				if ($('#dpdgeopost_service_price').text() != resp.price)
				{
					$('#dpdgeopost_service_price').fadeOut('slow', function(){
						$('#dpdgeopost_service_price').text(resp.price);
						$(this).fadeIn('slow', function(){});
					}); 
				}
				
				if (stringToNumber($('#dpdgeopost_paid_price').text()) < stringToNumber(resp.price))
					$('#dpdgeopost_paid_price, #dpdgeopost_service_price').css('color', 'red');
				else
					$('#dpdgeopost_paid_price, #dpdgeopost_service_price').css('color', 'inherit');
					
				$('#ajax_running').slideUp();
			},
			error: function()
			{
				changeShipmentCreationButtonAccessibility(false);
				$('#ajax_running').slideUp();
			}
		});
	});
	
	
	
	$('#dpdgeopost_shipment_creation_save').live('click', function(){
		$('#ajax_running').slideDown();
		
		var method_id = $('#dpd_shipping_method').val();
		var parcels = collectParcels();
		
		$.ajax({
			type: "POST",
			async: true,
			url: dpd_geopost_ajax_uri,
			dataType: "json",
			global: false,
			data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
				  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
				  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
				  "&saveShipment=true" +
				  "&method_id=" + encodeURIComponent(method_id) +
				  "&id_address=" + encodeURIComponent($('#dpdgeopost_id_address').val()) +
				  "&id_order=" + encodeURIComponent(id_order) +
				  "&_PS_ADMIN_DIR_=" + encodeURIComponent(presta_admin_dir_path) +
				  parcels,
			success: function(resp)
			{
				if (resp.error)
					$('#dpdgeopost_shipment_creation .message_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
				else
				   window.location.reload();
				
				$('#ajax_running').slideUp();
			},
			error: function()
			{
				$('#ajax_running').slideUp();
			}
		});
	});
	
	$('.parcel_selection').live('change', function(){
		updatePercalDescriptions();
		updatePercalTotals();
	});
	
	$('#dpdgeopost_create_shipment').live('click', function(){
		$('#dpdgeopost_shipment_creation').bPopup();
	});
	
	$('#dpdgeopost_shipment_creation_cancel, #dpdgeopost_shipment_creation_close').live('click', function(){
		$('div.message_container p.error').css('display', 'none');
		$('#dpdgeopost_shipment_creation').bPopup().close();
	});
	
	$('#dpdgeopost_edit_shipment').live('click', function(){
		$("#dpdgeopost_shipment_creation :input[value!='']").removeAttr("disabled");
		$('.buttons_container').show();
		$('#dpdgeopost_shipment_creation_close').hide();
		$('#dpdgeopost_shipment_creation').bPopup();
	});
	
	$('#dpdgeopost_preview_shipment').live('click', function(){
		$('#dpdgeopost_shipment_creation .message_container').slideUp().html('');
		$('.buttons_container').hide();
		$("#dpdgeopost_shipment_creation :input").attr("disabled", true);
		$('#dpdgeopost_shipment_creation_close').removeAttr('disabled').show();
		$('#dpdgeopost_shipment_creation').bPopup();
	});
	
	$('#parcel_selection_table .parcel_weight').live("change keyup paste", function(){
		updatePercalTotals();
	});
});

function stringToNumber(string) {
	if (typeof(string) == "number")
		return string;

	return Number(string.replace(/[,]/g, '.').replace(/[^0-9.]/g,''));
}

function updatePercalDescriptions()
{
	$('#parcel_descriptions_table td.parcel_description input[type="text"]').attr('value', '').removeAttr('disabled');
	$('#parcel_descriptions_table td.parcel_description input[type="hidden"]').attr('value', '');
	
	$('#parcel_selection_table .product_id').each(function(){
		var product_id = $(this).text();
		var parcel_id = $(this).siblings().find('select').val();
		var description = '';
		var $parcel_description_field = $('#parcel_descriptions_table td.parcel_id_'+parcel_id).siblings().find('input[type="text"]');
		var $parcel_description_safe = $parcel_description_field.siblings('input[type="hidden"]:first');
		
		if ($parcel_description_safe.attr('value') == '')
			description = product_id;
		else
			description = $parcel_description_safe.attr('value') + ', ' + product_id;
			
		$parcel_description_field.attr('value', description);
		$parcel_description_safe.attr('value', description);
	});
	
	$('#parcel_descriptions_table td.parcel_description input[type="text"][value=""]').attr('disabled', 'disabled');
}

function updatePercalTotals()
{
	$('.parcel_total_weight').each(function(){
		var parcel_number = $(this).siblings().find('select').val();
		//var total_parcel_weight = Number($(this).siblings('td.parcel_weight').attr('rel'));
		var total_parcel_weight = Number($(this).siblings('td.parcel_weight').find('input').val());
		
		$(this).parents('tr:first').siblings('tr').find('select option[value="'+parcel_number+'"]:selected').each(function(){
			//total_parcel_weight += Number($(this).parents('td:first').siblings('td.parcel_weight').attr('rel'));
			total_parcel_weight += Number($(this).parents('td:first').siblings('td.parcel_weight').find('input').val());
		});
		if (total_parcel_weight.toFixed(3) == '' || total_parcel_weight.toFixed(3) == 'NaN')
			total_parcel_weight = 0;
		
		$(this).attr('rel', total_parcel_weight.toFixed(3)).find('input').val(total_parcel_weight.toFixed(3));
	});
}

function collectParcels()
{
	var parcels = '';
	
	$('#parcel_descriptions_table td.parcel_description input[type="text"]:enabled').each(function(){
		var parcel_number = $(this).parent().siblings('td:first').text();
		parcels +=  '&parcels['+parcel_number+'][products]='+   encodeURIComponent($(this).siblings('input[type="hidden"]:first').val())+
					'&parcels['+parcel_number+'][description]='+encodeURIComponent($(this).val())+
					'&parcels['+parcel_number+'][weight]='+	 encodeURIComponent($('#parcel_selection_table tbody tr:nth-child('+parcel_number+') td.parcel_total_weight input').val());
	});
	
	return parcels;
}

function changeShipmentCreationButtonAccessibility(enabled)
{
	if (enabled)
		$('#dpdgeopost_create_shipment').removeAttr('disabled');
	else
		$('#dpdgeopost_create_shipment').attr('disabled', 'disabled'); 
}

function deleteShipment()
{
	$('#ajax_running').slideDown();

	$.ajax({
		type: "POST",
		async: true,
		url: dpd_geopost_ajax_uri,
		dataType: "json",
		global: false,
		data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
			  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
			  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
			  "&id_order=" + encodeURIComponent(id_order) +
			  "&_PS_ADMIN_DIR_=" + encodeURIComponent(presta_admin_dir_path) +
			  "&deleteShipment=true",
		success: function(resp)
		{
			if (resp.error)
				$('#dpdgeopost_notice_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
			else
				window.location.reload();
		}
	});
	
	$('#ajax_running').slideUp();
}