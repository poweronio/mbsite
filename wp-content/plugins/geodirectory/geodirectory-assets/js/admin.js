gd_infowindow = new google.maps.InfoWindow();

jQuery(window).load(function () {

    // Chosen selects
    if (jQuery("select.chosen_select").length > 0) {
        jQuery("select.chosen_select").chosen();

        jQuery("select.chosen_select_nostd").chosen({
            allow_single_deselect: 'true'
        });
    }

});

/* Check Uncheck All Related Options Start*/
jQuery(document).ready(function () {
    jQuery('#geodir_add_location_url').click(function () {

        if (jQuery(this).is(':checked')) {
            jQuery(this).closest('td').find('input').attr('checked', true).not(this).prop('disabled', false);
        } else {
            jQuery(this).closest('td').find('input').attr('checked', false).not(this).prop('disabled', true);
        }

    });


    if (jQuery('#geodir_add_location_url').is(':checked')) {
        jQuery('#geodir_add_location_url').closest('td').find('input').not(jQuery('#geodir_add_location_url')).prop('disabled', false);
    } else {
        jQuery('#geodir_add_location_url').closest('td').find('input').not(jQuery('#geodir_add_location_url')).prop('disabled', true);
    }


    /*jQuery('#geodir_add_categories_url').click(function(){

     if(jQuery(this).is(':checked')){
     jQuery('#geodir_listingurl_separator').prop('disabled', false);
     }else{
     jQuery('#geodir_listingurl_separator').prop('disabled', true);
     }

     });*/


    jQuery('#submit').click(function () {

        if (jQuery('input[name="ct_cat_icon[src]"]').hasClass('ct_cat_icon[src]')) {

            if (jQuery('input[name="ct_cat_icon[src]"]').val() == '') {

                jQuery('input[name="ct_cat_icon[src]"]').closest('tr').addClass('form-invalid');
                return false;

            } else {
                jQuery('input[name="ct_cat_icon[src]"]').closest('tr').removeClass('form-invalid');
                jQuery('input[name="ct_cat_icon[src]"]').closest('div').removeClass('form-invalid');
            }

        }

    });


    function location_validation(fields) {

        var error = false;

        if (fields.val() == '') {

            jQuery(fields).closest('.gtd-formfeild').find('.gd-location_message_error').show();
            error = true;

        } else {

            jQuery(fields).closest('.gtd-formfeild').find('.gd-location_message_error').hide();

        }

        if (error) {
            return false;
        } else {
            return true;
        }

    }

    jQuery('#location_save').click(function () {

        var is_validate = true;

        jQuery(this).closest('form').find('.required:visible').each(function () {

            var fields = jQuery(this).find('input, select');

            if (!location_validation(fields))
                is_validate = false;

        });

        if (!is_validate) {
            return false;
        }

    });

    jQuery('.default_location_form').find(".required:visible").find('input').blur(function () {
        location_validation(jQuery(this));
    });

    jQuery('.default_location_form').find(".required:visible").find('select').change(function () {
        location_validation(jQuery(this));
    });

    jQuery('.gd-cats-display-checkbox input[type="checkbox"]').click(function () {
        var isChecked = jQuery(this).is(':checked');
        var chkVal = jQuery(this).val();
        jQuery(this).closest('.gd-parent-cats-list').find('.gd-cat-row-' + chkVal + ' input[type="checkbox"]').prop("checked", isChecked);
    });

});
/* Check Uncheck All Related Options End*/



// WMPL copy function
jQuery(document).ready(function () {
    if (jQuery("#icl_cfo").length == 0) {
    }
    else { // it exists let's do stuff.

        jQuery('#icl_cfo').click(function () {
            gd_copy_translation(window.location.protocol + '//' + document.location.hostname + ajaxurl);
        });

    }
});


function gd_copy_translation(url) {
    //alert(url);return;
    //post_id = jQuery("input[name=icl_trid]").val();
    post_id = jQuery("#icl_translation_of_hidden").val();
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: {action: 'gd_copy_original_translation', post_id: post_id},
        beforeSend: function () {
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            data = JSON.parse(data);
            for (var key in data) {
                jQuery('#' + key).val(data[key]);
            }
            if (data.post_images) {
                plu_show_thumbs('post_images');
            }
            //jQuery('#post_address').val(data.post_address);

            if (data.categories) {
                var a = ["a", "b", "c"];
                data.categories.forEach(function (cat) {
                    show_subcatlist(cat);
                });
            }


            /////////////////

            if(data.post_latitude && data.post_longitude){
                latlon = new google.maps.LatLng(data.post_latitude,data.post_longitude);
                jQuery.goMap.map.setCenter(latlon);
                updateMarkerPosition(latlon);
                centerMarker();
                if(!data.post_address){google.maps.event.trigger(baseMarker, 'dragend');}// geocode address only if no street name present

            }

            if(data.post_country && jQuery('#post_country').length){jQuery('#post_country').val(data.post_country);jQuery("#post_country").trigger("chosen:updated");}
            if(data.post_region && jQuery('#post_region').length){jQuery('#post_region').val(data.post_region);jQuery("#post_region").trigger("chosen:updated");}
            if(data.post_city && jQuery('#post_city').length){jQuery('#post_city').val(data.post_city);jQuery("#post_city").trigger("chosen:updated");}
            if(data.post_zip && jQuery('#post_zip').length){jQuery('#post_zip').val(data.post_zip);}


            if(data.post_country && data.post_region && data.post_city && data.post_zip){
                gdfi_codeaddress = true;
                gdfi_city = data.post_city;
                gdfi_street = data.post_address;
                gdfi_zip= data.post_zip;
                geodir_codeAddress(true);
                setTimeout(function(){ google.maps.event.trigger(baseMarker, 'dragend');}, 600);

                //incase the drag marker changes the street and post code we should fix it.
                setTimeout(function(){
                    if(data.post_address && jQuery('#post_address').length){jQuery('#post_address').val(data.post_address);}
                    if(data.post_zip && jQuery('#post_zip').length){jQuery('#post_zip').val(data.post_zip);}
                }, 1000);



            }
            //////////////////////

        },
        error: function (xhr, textStatus, errorThrown) {
            alert(textStatus);
            //jQuery('#' + id + ' .contentarea').html(textStatus);
        }
    });
}

// Diagnosis related js starts here
/* Check Uncheck All Related Options Start*/
jQuery(document).ready(function () {
    jQuery('.geodir_diagnosis_button').click(function () {
        var diagnose = (jQuery(this).data('diagnose'))
        //var result_container = jQuery(this).parents('td').find("div")
        jQuery('.tool-' + diagnose).remove();
        var result_container = jQuery('.geodir_diagnostic_result-' + diagnose);
        if (!result_container.length) {
            jQuery('<tr class="gd-tool-results tool-' + diagnose + '" ><td colspan="3"><span class="gd-tool-results-remove" onclick="jQuery(this).closest(\'tr\').remove();"><i class="fa fa-spinner fa-spin"></i></span><div class="geodir_diagnostic_result-' + diagnose + '"></div></td></tr>').insertAfter(jQuery(this).parents('tr'));
            var result_container = jQuery('.geodir_diagnostic_result-' + diagnose);
        }
        jQuery.ajax({
            url: geodir_all_js_msg.geodir_admin_ajax_url,
            type: 'POST',
            dataType: 'html',
            data: {action: 'geodir_admin_ajax', geodir_admin_ajax_action: 'diagnosis', diagnose_this: diagnose},
            beforeSend: function () {
            },
            success: function (data, textStatus, xhr) {
                jQuery('.tool-' + diagnose + ' .gd-tool-results-remove').html('<i class="fa fa-times"></i>');

                result_container.html(data);
                geodir_enable_fix_buttons();//enable new fix buttons
            },
            error: function (xhr, textStatus, errorThrown) {
                alert(textStatus);

            }
        }); // end of ajax

    });


    geodir_enable_fix_buttons();// enabel fix buttons


});

function geodir_enable_fix_buttons() {
    jQuery('.geodir_fix_diagnostic_issue').click(function () {
        var diagnose = (jQuery(this).data('diagnostic-issue'))
        var result_container = jQuery(this).parents('td').find("div")
        jQuery.ajax({
            url: geodir_all_js_msg.geodir_admin_ajax_url,
            type: 'POST',
            dataType: 'html',
            data: {
                action: 'geodir_admin_ajax',
                geodir_admin_ajax_action: 'diagnosis-fix',
                diagnose_this: diagnose,
                fix: 1
            },
            beforeSend: function () {
            },
            success: function (data, textStatus, xhr) {
                result_container.html(data);
                geodir_enable_fix_buttons();//enable new fix buttons
            },
            error: function (xhr, textStatus, errorThrown) {
                alert(textStatus);

            }
        }); // end of ajax

    });

}

	
	

