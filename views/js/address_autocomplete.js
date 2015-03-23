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

var address_form;
var allowZipCodeErrorAlert = false;

$(document).ready(function()
{
	address_form = $('#address_form');

	if (!address_form.length)
		address_form = $('input[name="address2"]').closest('form');

	addRomaniaPostcodeSearchLink();
    postcodeValidation();

	$('select#id_country').change(function(){
		addRomaniaPostcodeSearchLink();
	});

	address_form.submit(function(event)
	{
		if (dpdCheckAddressLength() === false)
		{
			event.preventDefault();
			dpdAlertErrorProblems();
			$('body').scrollTop($('#address2').offset().top - 150);
		}
	});

	$('#address1, #address2').blur(function()
	{
		dpdAlertErrorProblems();
	});

	$('input[name="postcode"]').change(function(){
        postcodeValidation();
	});

    $('#id_country').change(function(){
        postcodeValidation();
    });
});

function displayPostcodeValidationError()
{
    if (dpdgetopost_16)
        $('input[name="postcode"]').after('<div style="color: red; padding: 10px;" id="dpdAlertErrorPostcode">'+dpd_postcode_validation_error+'</div>');
    else
        $('input[name="postcode"]').next().after('<div style="color: red; padding: 10px; width: 230px; display: inline;" id="dpdAlertErrorPostcode">'+dpd_postcode_validation_error+'</div>');
}

function hidePostcodeValidationError()
{
    $('#dpdAlertErrorPostcode').remove();
}

function postcodeValidation()
{
    hidePostcodeValidationError();

    if ($('select#id_country').val() == id_dpdgeopost_romania_country && !validatePostcode())
        displayPostcodeValidationError();
}

function validatePostcode()
{
    var result = true;
    var postcode = $('input[name="postcode"]').val();

    if (!postcode)
        return result;

    $.ajax({
        type: 'POST',
        async: false,
        url: dpd_ajax_uri,
        data: 'dpdpostcode='+postcode+'&action=validate_postcode'+'&token='+dpd_token,
        dataType: 'json',
        success: function(resp)
        {
            result = resp.is_valid;
        }
    });

    return result;
}

function addRomaniaPostcodeSearchLink()
{
	if ($('select#id_country').val() != id_dpdgeopost_romania_country)
	{
		$('#refresh-postcode').parent().remove();
		return false;
	}

	if (address_form)
	{
		var postcodeField = $('input[name="postcode"]');

		if (!postcodeField.length)
			postcodeField = $('input[name="postcode"]');

		if (postcodeField)
		{
			if (dpdgetopost_16)
				postcodeField.parent().after('<div class="col-lg-2 "><a class="btn btn-link" id="refresh-postcode">'+
				'<i class="icon-refresh"></i> '+dpd_search_postcode_text+'</a></div>');
			else
				postcodeField.next().after('<div class="postcode_refresh_button"><a class="button" id="refresh-postcode">'
				+dpd_search_postcode_text+'</a></div>');

			$('#refresh-postcode').click(function(){
				allowZipCodeErrorAlert = true;
				postcodeField.autocomplete('search');
			});

			postcodeField.autocomplete({
				source: function(request, response)
				{
					$.ajax({
						type: 'POST',
						async: false,
						url: dpd_ajax_uri,
						data: address_form.serialize()+'&action=postcode-recommendation'+'&token='+dpd_token,
						success: function(data)
						{
							data = jQuery.parseJSON(data);

							if (data.length == 0 && allowZipCodeErrorAlert)
								alert(dpd_search_postcode_empty_result_alert);

							response(data);
							allowZipCodeErrorAlert = false;
						}
					});
				},
				minLength: 0,
				select: function(event, ui) {
					postcodeField.val(ui.item.postcode);
                    postcodeValidation();
					return false;
				},
				focus: function(event, ui) {
					return false;
				}
			}).autocomplete('instance')._renderItem = function(ul, item){
				return $('<li>').append('<a>'+item.label+'<br />'+item.postcode+'</a>').appendTo(ul);
			};
		}
	}
}

function dpdAlertErrorProblems()
{
	var dpdAlertErrorProblems = $('#dpdAlertErrorProblems');

	dpdAlertErrorProblems.remove();

	if (dpdCheckAddressLength() === false)
	{
		$('#address2').after('<div style="color: red; padding: 10px;" id="dpdAlertErrorProblems">'+dpd_address_validation_length+'</div>');

		dpdAlertErrorProblems.fadeOut('slow');
		dpdAlertErrorProblems.fadeIn('slow');
	}
}

function dpdCheckAddressLength()
{
	var $addressLength = $('#address1').val().length + $('#address2').val().length;

	return $addressLength < 70;
}