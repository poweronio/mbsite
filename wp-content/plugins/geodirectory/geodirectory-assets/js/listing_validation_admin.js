function validate_field(field) {

    var is_error = true;
    switch (jQuery(field).attr('field_type')) {
        case 'radio':
        case 'checkbox':

            if (jQuery(field).closest('.required_field').find('#cat_limit').length) {

                var cat_limit = jQuery(field).closest('.required_field').find('#cat_limit').attr('cat_limit');
                var cat_msg = jQuery(field).closest('.required_field').find('#cat_limit').val();

                if (jQuery(field).closest('.required_field').find(":checked").length > cat_limit && cat_limit > 0) {

                    jQuery(field).closest('.required_field').find('.geodir_message_error').show();
                    jQuery(field).closest('.required_field').find('.geodir_message_error').html(cat_msg);
                    return false;

                }

            }

            if (jQuery(field).closest('.required_field').find(":checked").length > 0) {
                is_error = false;
            }
            break;

        case 'select':
            if (jQuery(field).closest('.geodir_form_row').find(".geodir_taxonomy_field").length > 0 && jQuery(field).closest('.geodir_form_row').find("#post_category").length > 0) {
                if (jQuery(field).closest('.geodir_form_row').find("#post_category").val() != '') {
                    is_error = false;
                }
            } else {
                if (jQuery(field).find("option:selected").length > 0 && jQuery(field).find("option:selected").val() != '') {
                    is_error = false;
                }
            }
            break;

        case 'multiselect':
            if (jQuery(field).closest('.required_field').find('#cat_limit').length) {

                var cat_limit = jQuery(field).closest('.required_field').find('#cat_limit').attr('cat_limit');
                var cat_msg = jQuery(field).closest('.required_field').find('#cat_limit').val();

                if (jQuery(field).find("option:selected").length > cat_limit && cat_limit > 0) {
                    jQuery(field).closest('.required_field').find('.geodir_message_error').show();
                    jQuery(field).closest('.required_field').find('.geodir_message_error').html(cat_msg);
                    return false;

                }

            }

            if (jQuery(field).find("option:selected").length > 0) {
                is_error = false;
            }


            break;

        case 'email':
            //var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            /* fix for email with domain @me.com */
            var filter = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (field.value != '' && filter.test(field.value)) {
                is_error = false;
            }
            break;

        case 'url':
            var filter = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
            if (field.value != '' && filter.test(field.value)) {
                is_error = false;
            }
            break;

        case 'editor':
            if (jQuery('#' + jQuery(field).attr('field_id')).val() != '') {
                is_error = false;
            }
            break;

        case 'datepicker':
        case 'time':
        case 'text':
        case 'textarea':
            if (field.value != '') {
                is_error = false;
            }
            break;

        case 'address':

            if (jQuery(field).attr('id') == 'post_latitude' || jQuery(field).attr('id') == 'post_longitude') {

                if (/^[0-90\-.]*$/.test(field.value) == true && field.value != '') {
                    is_error = false;
                } else {

                    var error_msg = geodir_all_js_msg.geodir_latitude_error_msg;
                    if (jQuery(field).attr('id') == 'post_longitude')
                        error_msg = geodir_all_js_msg.geodir_longgitude_error_msg;

                    jQuery(field).closest('.required_field').find('.geodir_message_error').show();
                    jQuery(field).closest('.required_field').find('.geodir_message_error').html(error_msg);

                }

            } else {

                if (field.value != '')
                    is_error = false;
            }

            break;

        default:
            if (field.value != '') {
                is_error = false;
            }
            break;

    }


    if (is_error) {
        if (jQuery(field).closest('.required_field').find('span.geodir_message_error').html() == '') {
            jQuery(field).closest('.required_field').find('span.geodir_message_error').html(geodir_all_js_msg.geodir_field_id_required)
        }

        jQuery(field).closest('.required_field').find('span.geodir_message_error').fadeIn();

        return false;
    } else {

        jQuery(field).closest('.required_field').find('span.geodir_message_error').html('');
        jQuery(field).closest('.required_field').find('span.geodir_message_error').fadeOut();

        return true;
    }
}

jQuery(document).ready(function() {
	/// check validation on blur
	jQuery('#post').find(".required_field:visible").find("[field_type]:visible, .editor textarea").blur(function() {
		validate_field(this);
	});
	
	jQuery('#post').find(".required_field:visible").find("input[type='checkbox'],input[type='radio']").click(function() {
		validate_field(this);
	});
	
	jQuery(document).delegate("#post", "submit", function(ele) {
		var is_validate = true;
		
		jQuery(this).find(".required_field:visible").each(function() {
			jQuery(this).find("[field_type]:visible, .chosen_select, .geodir_location_add_listing_chosen, .editor, .event_recurring_dates, .geodir-custom-file-upload").each(function() {
				if (jQuery(this).is('.chosen_select, .geodir_location_add_listing_chosen')) {
					var chosen_ele = jQuery(this);
					jQuery('#' + jQuery(this).attr('id') + '_chzn').mouseleave(function() {
						validate_field(chosen_ele);
					});
				}
				if (!validate_field(this)) {
					is_validate = validate_field(this);
				}
			});
		});
		
		if (is_validate) {
			return true;
		} else {
			jQuery(window).scrollTop(jQuery(".geodir_message_error:visible:first").closest('.required_field').offset().top);
			jQuery('#save-action .spinner').removeClass('is-active');
			jQuery('#save-action #save-post').removeClass('disabled');
			jQuery('#publishing-action .spinner').removeClass('is-active');
			jQuery('#publishing-action #publish').removeClass('disabled');
			return false;
		}
	});
});