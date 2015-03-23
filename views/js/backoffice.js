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
	toggleWebserviceUrlDialog();

	$('#toggle_csv_info_link').click(function(){
		$('#toggle_csv_info').slideToggle();
	});

	$('#toggle_cod_info_link').click(function(){
		$('#toggle_cod_info').slideToggle();
	});

	$('input[name="downloadModuleCSVSettings"]').click(function(){
		window.location = dpd_geopost_ajax_uri+"?downloadModuleCSVSettings&token="+encodeURIComponent(dpd_geopost_token);
		return false;
	});

	$('#dpd_country_select').click(function(){
		toggleWebserviceUrlDialog();
	});

	$('#test_connection').click(function(){
		testWebserviceConnection();
	});

	$('#weight_conversion_input').keyup(function(){
		$('#dpd_weight_unit').text($(this).val());
	});

	$('#displayPickupDialog').click(function(){
		var selected_shipments = collectSelectedShipments();

		if(selected_shipments)
		{
			var available_pickup = validateShipmentsForPickup();
			if (available_pickup)
				$('#dpdgeopost_pickup_dialog').bPopup();
			else
				alert(dpdgeopost_error_puckup_not_available);
		}
		else
			alert(dpdgeopost_error_no_shipment_selected);
	});

	$('#close_dpdgeopost_pickup_dialog').click(function(){
		$('#dpdgeopost_pickup_dialog').bPopup().close();
	});

	$('#submit_dpdgeopost_pickup_dialog').click(function(){
		$('#ajax_running').slideDown();

		var ajax_request_params = 'ajax=true&arrangePickup=true';

		$('#dpdgeopost_pickup_dialog :input[name^="dpdgeopost_pickup_data"]').each(function(){
			ajax_request_params+='&'+$(this).attr('name')+'='+encodeURIComponent($(this).val());
		});

		$.ajax({
			type: "POST",
			async: true,
			url: dpd_geopost_ajax_uri,
			dataType: "json",
			global: false,
			data: ajax_request_params + collectSelectedShipments() +
				  "&token=" + encodeURIComponent(dpd_geopost_token) +
				  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
				  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang),
			success: function(resp)
			{
				if (resp.error)
					$('#dpdgeopost_pickup_dialog #dpdgeopost_pickup_dialog_mssg').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
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

	$('#price_calculation_webservices').change(function(){
		if ($(this).is(':checked')) {
			$('#address_validation_block').slideDown('slow');
		}
	});

	$('#price_calculation_prestashop').change(function(){
		if ($(this).is(':checked')) {
			$('#address_validation_block').slideUp('slow');
		}
	});

	$('#price_calculation_csv').change(function(){
		if ($(this).is(':checked')) {
			$('#address_validation_block').slideUp('slow');
		}
	});

	if ($('#price_calculation_webservices').is(':checked')) {
		$('#address_validation_block').slideDown('slow');
	}

	$('#cod_payment_methods_container').find('input').change(function(){
		selectMaxOneMethod($(this));
		checkCODMethods(true);
	});

	$('#active_services .carriers_cod_block').find('input').change(function(){
		checkCODMethods(true);
	});

	checkCODMethods(false);
});

function validateShipmentsForPickup()
{
	var ok = true;
	$('input[name="ShipmentsBox[]"]:checked').each(function(){
		if ($(this).next().val() == 0)
			ok = false;
	});
	return ok;
}

function checkCODMethods(move_to_warning)
{
	if (isCODShippingSelected() && !isCODPaymentSelected())
		toggleCODSelectionWarning(true, move_to_warning);
	else
		toggleCODSelectionWarning(false, move_to_warning);
}

function toggleCODSelectionWarning(display, move_to_warning)
{
	if (display)
	{
		$('.cod_selection_warning').slideDown();
		if (move_to_warning)
			window.location.href = '#cod_selection_warning';
	}
	else
		$('.cod_selection_warning').slideUp();
}

function isCODShippingSelected()
{
	if ($('#active_services .carriers_cod_block').find('input[type="checkbox"]:checked').length > 0)
		return true;
	return false;
}

function isCODPaymentSelected()
{
	if ($('#cod_payment_methods_container').find('input[type="checkbox"]:checked').length > 0)
		return true;
	return false;
}

function selectMaxOneMethod(selected_method)
{
	var is_checked = false;
	if (selected_method.prop('checked'))
		is_checked = true;

	$('#cod_payment_methods_container').find('input').removeAttr('checked');

	if (is_checked)
	{
		selected_method.attr('checked', 'checked');
	}
}

function collectSelectedShipments()
{
	selected_shipments = '';
	$('input[name="ShipmentsBox[]"]:checked').each(function(){
		selected_shipments+='&shipmentIds[]='+$(this).val();
	});
	return selected_shipments;
}

function toggleWebserviceUrlDialog() {
	if ($('#dpd_country_select').val() == 'other') {
		$('#custom_web_service_container').slideDown('slow');
	}
	else
	{
		$('#custom_web_service_container').slideUp('slow');
	}
}

function testWebserviceConnection() {
	$('p.connection_message').slideUp('slow');

	var ws_country		  = $('#dpd_country_select').val();
	var production_ws_url   = $('#production_ws_url').val();
	var test_ws_url		 = $('#test_ws_url').val();
	var other_country	   = 0;
	if ($('#dpd_country_select').val() == dpd_geopost_other_country) {
		other_country = 1;
	}
	var production_mode = 0;
	if ($('#production_mode_yes').is(':checked'))
		production_mode = 1;

	var params = "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) + "&id_shop=" + dpd_geopost_id_shop + "&testConnectivity=true" +
	"&ws_country=" + ws_country + "&production_ws_url=" + production_ws_url + "&test_ws_url=" + test_ws_url + '&other_country=' + other_country +
	"&production_mode=" + production_mode;

	$.ajax({
		type: "POST",
		async: false,
		url: dpd_geopost_ajax_uri,
		data: params,
		success: function(response)
		{
			if (response == true) {
				$('p.connection_message.conf').slideDown('slow');
			}
			else
			{
				$('p.connection_message.error .error_message').text(response);
				$('p.connection_message.error').slideDown('slow');
			}
		}
	});
}