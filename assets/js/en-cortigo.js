jQuery(window).on("load", function () {
    var saved_mehod_value = en_cortigo_admin_script.wc_settings_cortigo_rate_method;
    if (saved_mehod_value == 'Cheapest') {
        jQuery(".cortigo_delivery_estimate").removeAttr('style');
        jQuery(".cortigo_Number_of_label_as").removeAttr('style');
        jQuery(".cortigo_Number_of_options_class").removeAttr('style');

        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').css("display", "none");
        jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
        jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
        jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

        jQuery('.cortigo_rate_mehod td span').html('Displays only the cheapest returned Rate.');
        jQuery('.cortigo_Number_of_label_as td span').html('What the user sees during checkout, e.g. Freight. Leave blank to display the carrier name.');
    }
    if (saved_mehod_value == 'cheapest_options') {

        jQuery(".cortigo_delivery_estimate").removeAttr('style');
        jQuery(".cortigo_Number_of_label_as").removeAttr('style');
        jQuery(".cortigo_Number_of_options_class").removeAttr('style');

        jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
        jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
        jQuery("#wc_settings_cortigo_label_as").closest('tr').css("display", "none");
        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
        jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

        jQuery('.cortigo_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
        jQuery('.cortigo_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
    }
    if (saved_mehod_value == 'average_rate') {

        jQuery(".cortigo_delivery_estimate").removeAttr('style');
        jQuery(".cortigo_Number_of_label_as").removeAttr('style');
        jQuery(".cortigo_Number_of_options_class").removeAttr('style');

        jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
        jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').css("display", "none");
        jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
        jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

        jQuery('.cortigo_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
        jQuery('.cortigo_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
        jQuery('.cortigo_Number_of_label_as td span').html('What the user sees during checkout, e.g. Freight. If left blank will default to Freight.');
    }
});

jQuery(document).ready(function () {
    jQuery('.hide_drop_val').click(function () {
        jQuery('#edit_dropship_form_id').val('');
        jQuery("#ltl_dropship_zip").val('');
        jQuery('.city_select').hide();
        jQuery('.city_input').show();
        jQuery('#ltl_dropship_city').css('background', 'none');
        jQuery("#ltl_dropship_nickname").val('');
        jQuery("#ltl_dropship_city").val('');
        jQuery('.ltl_multi_state').empty();
        jQuery("#ltl_dropship_state").val('');
        jQuery("#ltl_dropship_country").val('');
        jQuery('.ltl_zip_validation_err').hide();
        jQuery('.ltl_city_validation_err').hide();
        jQuery('.ltl_state_validation_err').hide();
        jQuery('.ltl_country_validation_err').hide();
        jQuery('.not_allowed').hide();
        jQuery('.already_exist').hide();
        jQuery('.wrng_credential').hide();
    });

    jQuery('.ltl_add_dropship_btn').click(function () {

        setTimeout(function () {

            if (jQuery('.ds_popup').is(':visible')) {
                jQuery('.ds_input > input').eq(0).focus();
            }
        }, 500);
    });

    jQuery("#ltl_dropship_zip").keypress(function (e) {

        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    jQuery("#ltl_dropship_zip").on('change', function () {

        if (jQuery("#ltl_dropship_zip").val() == '') {
            return false;
        }
        jQuery('#ltl_dropship_city').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('#ltl_dropship_state').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('.city_select_css').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('#ltl_dropship_country').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');

        var postForm = {
            'action': 'get_address',
            'origin_zip': jQuery('#ltl_dropship_zip').val(),
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {
                jQuery('.ltl_zip_validation_err').hide();
                jQuery('.ltl_city_validation_err').hide();
                jQuery('.ltl_state_validation_err').hide();
                jQuery('.ltl_country_validation_err').hide();
            },
            success: function (data) {

                if (data) {

                    if (data.country === 'US' || data.country === 'CA') {

                        if (data.postcode_localities == 1) {

                            jQuery('.city_select').show();
                            jQuery('#dropship_actname').replaceWith(data.city_option);
                            jQuery('#ltl_dropship_state').val(data.state);
                            jQuery('#ltl_dropship_country').val(data.country);
                            jQuery('.city-multiselect').change(function () {
                                en_cortigo_setLtlDsCity(this);
                            });
                            jQuery('#ltl_dropship_city').val(data.first_city);
                            jQuery('#ltl_dropship_state').css('background', 'none');
                            jQuery('.city_select_css').css('background', 'none');
                            jQuery('#ltl_dropship_country').css('background', 'none');
                            jQuery('.city_input').hide();

                        } else {

                            jQuery('.city_input').show();
                            jQuery('#_city').removeAttr('value');
                            jQuery('.city_select').hide();
                            jQuery('#ltl_dropship_city').val(data.city);
                            jQuery('#ltl_dropship_state').val(data.state);
                            jQuery('#ltl_dropship_country').val(data.country);
                            jQuery('#ltl_dropship_city').css('background', 'none');
                            jQuery('#ltl_dropship_state').css('background', 'none');
                            jQuery('#ltl_dropship_country').css('background', 'none');
                        }
                    } else if (data.result === 'false') {

                        jQuery('#ltl_dropship_city').css('background', 'none');
                        jQuery('#ltl_dropship_state').css('background', 'none');
                        jQuery('#ltl_dropship_country').css('background', 'none');

                    } else if (data.apiResp === 'apiErr') {
                        jQuery('.wrng_credential').show('slow');
                        jQuery('#ltl_dropship_city').css('background', 'none');
                        jQuery('#ltl_dropship_state').css('background', 'none');
                        jQuery('#ltl_dropship_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.wrng_credential').hide('slow');
                        }, 5000);
                    } else {

                        jQuery('.not_allowed').show('slow');
                        jQuery('#ltl_dropship_city').css('background', 'none');
                        jQuery('#ltl_dropship_state').css('background', 'none');
                        jQuery('#ltl_dropship_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.not_allowed').hide('slow');
                        }, 5000);
                    }
                }
            },
        });
        return false;
    });

    jQuery('input.alphaonly').keyup(function () {

        if (this.value.match(/[^a-zA-Z ]/g)) {
            this.value = this.value.replace(/[^a-zA-Z ]/g, '');
        }
    });

    jQuery('.hide_val').click(function () {

        jQuery('#edit_form_id').val('');
        jQuery("#ltl_origin_zip").val('');
        jQuery('.city_select').hide();
        jQuery('.city_input').show();
        jQuery('#ltl_origin_city').css('background', 'none');
        jQuery("#ltl_origin_city").val('');
        jQuery("#ltl_origin_state").val('');
        jQuery("#ltl_origin_country").val('');
        jQuery('.ltl_zip_validation_err').hide();
        jQuery('.ltl_city_validation_err').hide();
        jQuery('.ltl_state_validation_err').hide();
        jQuery('.ltl_country_validation_err').hide();
        jQuery('.not_allowed').hide();
        jQuery('.wrng_credential').hide();
    });

    jQuery('.ltl_add_warehouse_btn').click(function () {

        setTimeout(function () {

            if (jQuery('.ltl_add_warehouse_popup').is(':visible')) {
                jQuery('.ltl_add_warehouse_input > input').eq(0).focus();
            }
        }, 500);
    });

    jQuery("#ltl_origin_zip").keypress(function (e) {

        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {

            return false;
        }
    });

    jQuery("#ltl_origin_zip").on('change', function () {

        if (jQuery("#ltl_origin_zip").val() == '') {

            return false;
        }

        jQuery('#ltl_origin_city').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('#ltl_origin_state').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('.city_select_css').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
        jQuery('#ltl_origin_country').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');

        var postForm = {
            'action': 'get_address',
            'origin_zip': jQuery('#ltl_origin_zip').val(),
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {
                jQuery('.ltl_zip_validation_err').hide();
                jQuery('.ltl_city_validation_err').hide();
                jQuery('.ltl_state_validation_err').hide();
                jQuery('.ltl_country_validation_err').hide();
            },
            success: function (data) {

                if (data) {

                    if (data.country === 'US' || data.country === 'CA') {
                        if (data.postcode_localities == 1) {
                            jQuery('.city_select').show();
                            jQuery('#actname').replaceWith(data.city_option);
                            jQuery('.ltl_multi_state').replaceWith(data.city_option);
                            jQuery('.city-multiselect').change(function () {
                                en_cortigo_setLTLCity(this);
                            });
                            jQuery('#ltl_origin_city').val(data.first_city);
                            jQuery('#ltl_origin_state').val(data.state);
                            jQuery('#ltl_origin_country').val(data.country);
                            jQuery('#ltl_origin_state').css('background', 'none');
                            jQuery('.city_select_css').css('background', 'none');
                            jQuery('#ltl_origin_country').css('background', 'none');
                            jQuery('.city_input').hide();
                        } else {
                            jQuery('.city_input').show();
                            jQuery('#_city').removeAttr('value');
                            jQuery('.city_select').hide();
                            jQuery('#ltl_origin_city').val(data.city);
                            jQuery('#ltl_origin_state').val(data.state);
                            jQuery('#ltl_origin_country').val(data.country);
                            jQuery('#ltl_origin_city').css('background', 'none');
                            jQuery('#ltl_origin_state').css('background', 'none');
                            jQuery('#ltl_origin_country').css('background', 'none');
                        }
                    } else if (data.result === 'false') {
                        jQuery('#ltl_origin_city').css('background', 'none');
                        jQuery('#ltl_origin_state').css('background', 'none');
                        jQuery('#ltl_origin_country').css('background', 'none');
                    } else if (data.apiResp === 'apiErr') {
                        jQuery('.wrng_credential').show('slow');
                        jQuery('#ltl_origin_city').css('background', 'none');
                        jQuery('#ltl_origin_state').css('background', 'none');
                        jQuery('#ltl_origin_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.wrng_credential').hide('slow');
                        }, 5000);
                    } else {
                        jQuery('.not_allowed').show('slow');
                        jQuery('#ltl_origin_city').css('background', 'none');
                        jQuery('#ltl_origin_state').css('background', 'none');
                        jQuery('#ltl_origin_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.not_allowed').hide('slow');
                        }, 5000);
                    }
                }
            },
        });
        return false;
    });

    jQuery('input.alphaonly').keyup(function () {
        if (this.value.match(/[^a-zA-Z ]/g)) {

            this.value = this.value.replace(/[^a-zA-Z ]/g, '');
        }
    });

    jQuery(".cort_carrier_section_class .liftgate_fee").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    jQuery("#wc_settings_cortigo_residential_delivery").closest('tr').addClass("wc_settings_cortigo_residential_delivery");
    jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");
    jQuery("#avaibility_lift_gate").closest('tr').addClass("avaibility_lift_gate");
    jQuery("#wc_settings_cortigo_lift_gate_delivery").closest('tr').addClass("wc_settings_cortigo_lift_gate_delivery");
    jQuery("#cortigo_freights_liftgate_delivery_as_option").closest('tr').addClass("cortigo_freights_liftgate_delivery_as_option");


    /**
     * Offer lift gate delivery as an option and Always include residential delivery fee
     * @returns {undefined}
     */

    jQuery(".checkbox_fr_add").on("click", function () {
        var id = jQuery(this).attr("id");
        if (id == "wc_settings_cortigo_lift_gate_delivery") {
            jQuery("#cortigo_freights_liftgate_delivery_as_option").prop({checked: false});
            jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false});

        } else if (id == "cortigo_freights_liftgate_delivery_as_option" ||
            id == "en_woo_addons_liftgate_with_auto_residential") {
            jQuery("#wc_settings_cortigo_lift_gate_delivery").prop({checked: false});
        }
    });

    var url = en_cortigo_getUrlVarsCortigoFreight()["tab"];
    if (url === 'cortigo_freights') {
        jQuery('#footer-left').attr('id', 'wc-footer-left');
    }
    //Restrict Handling Fee with 8 digits limit
    jQuery("#wc_settings_cortigo_hand_free_mark_up").attr('maxlength', '8');

    jQuery(".cort_ltl_connection_section_class .button-primary").click(function () {
        var input = en_cortigo_validateInput('.cort_ltl_connection_section_class');
        if (input === false) {
            return false;
        }
    });
    jQuery(".cort_ltl_connection_section_class .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary cort_ltl_test_connection">Test Connection</a>');
    jQuery('.cort_ltl_test_connection').click(function (e) {
        var input = en_cortigo_validateInput('.cort_ltl_connection_section_class');
        if (input === false) {
            return false;
        }

        var postForm = {
            'wc_cortigo_shipper_id': jQuery('#wc_settings_cortigo_shipper_id').val(),
            'wc_cortigo_username': jQuery('#wc_settings_cortigo_username').val(),
            'wc_cortigo_password': jQuery('#wc_settings_cortigo_password').val(),
            'wc_cortigo_licence_key': jQuery('#wc_settings_cortigo_licence_key').val(),
            'authentication_key': jQuery('#wc_settings_cortigo_authentication_key').val(),
            'action': 'cort_test_connection_call'
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {

                jQuery(".cort_ltl_test_connection").css("color", "#fff");
                jQuery(".cort_ltl_connection_section_class .button-primary").css("cursor", "pointer");
                jQuery('#wc_settings_cortigo_shipper_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_cortigo_username').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_cortigo_password').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_cortigo_authentication_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_cortigo_licence_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_cortigo_admin_script.plugins_url + '/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data) {

                console.log(data);
                if (data.success) {
                    jQuery(".updated").hide();
                    jQuery('#wc_settings_cortigo_shipper_id').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_username').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_password').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_authentication_key').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_licence_key').css('background', '#fff');
                    jQuery(".class_success_message").remove();
                    jQuery(".class_error_message").remove();
                    jQuery(".cort_ltl_connection_section_class .button-primary").attr("disabled", false);
                    jQuery('.warning-msg-ltl').before('<p class="class_success_message" ><b> Success! The test resulted in a successful connection. </b></p>');
                } else {
                    jQuery(".updated").hide();
                    jQuery(".class_error_message").remove();
                    jQuery('#wc_settings_cortigo_shipper_id').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_username').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_password').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_authentication_key').css('background', '#fff');
                    jQuery('#wc_settings_cortigo_licence_key').css('background', '#fff');
                    jQuery(".class_success_message").remove();
                    jQuery(".cort_ltl_connection_section_class .button-primary").attr("disabled", false);
                    if (data.error_desc) {
                        jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! ' + data.error_desc + ' </b></p>');
                    } else {
                        jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! Your test connection failed. ' + data.error + ' </b></p>');
                    }
                }
            }
        });
        e.preventDefault();
    });

    jQuery('.cort_ltl_connection_section_class .form-table').before('<div class="warning-msg-ltl"><p> <b>Note!</b> You must have a Cortigo account to use this application. If you do not have one,  call at <a href="tel:800-333-7400">800-333-7400</a>, or click <a href="https://eniture.com/request-worldwide-express-account-number/" target="_blank">here</a> to access the new account request form. </p>');

    jQuery('.cort_carrier_section_class .button-primary').on('click', function () {
        jQuery(".updated").hide();
        var num_of_checkboxes = jQuery('.carrier_check:checked').length;
        if (num_of_checkboxes < 1) {
            jQuery(".cort_carrier_section_class:first-child").before('<div id="message" class="error inline no_srvc_select"><p><strong>Please select at least one carrier service.</strong></p></div>');

            jQuery('html, body').animate({
                'scrollTop': jQuery('.no_srvc_select').position().top
            });
            return false;
        }
    });
    jQuery('.cort_quote_section_class_ltl .button-primary').on('click', function () {
        jQuery(".updated").hide();
        jQuery('.error').remove();
        var handling_fee = jQuery('#wc_settings_cortigo_hand_free_mark_up').val();
        if (handling_fee.slice(handling_fee.length - 1) == '%') {
            handling_fee = handling_fee.slice(0, handling_fee.length - 1)
        }
        if (handling_fee === "") {
            return true;
        } else {
            if (en_cortigo_isValidNumber(handling_fee) === false) {

                jQuery("#mainform .cort_quote_section_class_ltl").prepend('<div id="message" class="error inline handlng_fee_error"><p><strong>Handling fee format should be 100.20 or 10%.</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.handlng_fee_error').position().top
                });
                return false;
            } else if (en_cortigo_isValidNumber(handling_fee) === 'decimal_point_err') {
                jQuery("#mainform .cort_quote_section_class_ltl").prepend('<div id="message" class="error inline handlng_fee_error"><p><strong>Handling fee format should be 100.2000 or 10% and only 4 digits are allowed after decimal</strong></p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.handlng_fee_error').position().top
                });
                return false;
            } else {
                return true;
            }
        }
    });

    var all_checkboxes = jQuery('.carrier_check');
    if (all_checkboxes.length === all_checkboxes.filter(":checked").length) {
        jQuery('.include_all').prop('checked', true);
    }

    jQuery(".include_all").change(function () {
        if (this.checked) {
            jQuery(".carrier_check").each(function () {
                this.checked = true;
            })
        } else {
            jQuery(".carrier_check").each(function () {
                this.checked = false;
            })
        }
    });

    /*
    * Uncheck Select All Checkbox
    */

    jQuery(".carrier_check").on('change load', function () {
        var int_checkboxes = jQuery('.carrier_check:checked').length;
        var int_un_checkboxes = jQuery('.carrier_check').length;
        if (int_checkboxes === int_un_checkboxes) {
            jQuery('.include_all').prop('checked', true);
        } else {
            jQuery('.include_all').prop('checked', false);
        }
    });

    //      changed
    var wc_settings_cortigo_rate_method = jQuery("#wc_settings_cortigo_rate_method").val();
    if (wc_settings_cortigo_rate_method == 'Cheapest') {
        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
        jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').css("display", "none");
    }

    jQuery("#wc_settings_cortigo_rate_method").change(function () {
        var rating_method = jQuery(this).val();
        if (rating_method == 'Cheapest') {

            jQuery(".cortigo_delivery_estimate").removeAttr('style');
            jQuery(".cortigo_Number_of_label_as").removeAttr('style');
            jQuery(".cortigo_Number_of_options_class").removeAttr('style');

            jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
            jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').css("display", "none");
            jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
            jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
            jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

            jQuery('.cortigo_rate_mehod td span').html('Displays only the cheapest returned Rate.');
            jQuery('.cortigo_Number_of_label_as td span').html('What the user sees during checkout, e.g. Freight. Leave blank to display the carrier name.');

        }
        if (rating_method == 'cheapest_options') {

            jQuery(".cortigo_delivery_estimate").removeAttr('style');
            jQuery(".cortigo_Number_of_label_as").removeAttr('style');
            jQuery(".cortigo_Number_of_options_class").removeAttr('style');

            jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
            jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
            jQuery("#wc_settings_cortigo_label_as").closest('tr').css("display", "none");
            jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
            jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

            jQuery('.cortigo_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
            jQuery('.cortigo_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
        }
        if (rating_method == 'average_rate') {

            jQuery(".cortigo_delivery_estimate").removeAttr('style');
            jQuery(".cortigo_Number_of_label_as").removeAttr('style');
            jQuery(".cortigo_Number_of_options_class").removeAttr('style');

            jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').addClass("cortigo_delivery_estimate");
            jQuery("#wc_settings_cortigo_delivery_estimate").closest('tr').css("display", "none");
            jQuery("#wc_settings_cortigo_label_as").closest('tr').addClass("cortigo_Number_of_label_as");
            jQuery("#wc_settings_cortigo_Number_of_options").closest('tr').addClass("cortigo_Number_of_options_class");
            jQuery("#wc_settings_cortigo_rate_method").closest('tr').addClass("cortigo_rate_mehod");

            jQuery('.cortigo_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
            jQuery('.cortigo_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
            jQuery('.cortigo_Number_of_label_as td span').html('What the user sees during checkout, e.g. Freight. If left blank will default to Freight.');
        }
    });

    jQuery('.cort_ltl_connection_section_class input[type="text"]').each(function () {
        if (jQuery(this).parent().find('.err').length < 1) {
            jQuery(this).after('<span class="err"></span>');
        }
    });

    jQuery('#wc_settings_cortigo_shipper_id').attr('title', 'Shipper ID');
    jQuery('#wc_settings_cortigo_username').attr('title', 'Username');
    jQuery('#wc_settings_cortigo_password').attr('title', 'Password');
    jQuery('#wc_settings_cortigo_authentication_key').attr('title', 'Authentication Key');
    jQuery('#wc_settings_cortigo_licence_key').attr('title', 'Plugin License Key');
    jQuery('#wc_settings_cortigo_allow_for_own_arrangment').attr('title', 'Text For Own Arrangement');
    jQuery('#wc_settings_cortigo_hand_free_mark_up').attr('title', 'Handling Fee / Markup');
    jQuery('#wc_settings_cortigo_label_as').attr('title', 'Label As');

});

// Update plan
if (typeof en_update_plan != 'function') {
    function en_update_plan(input) {
        let action = jQuery(input).attr('data-action');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: action},
            success: function (data_response) {
                window.location.reload(true);
            }
        });
    }
}

function en_cortigo_isValidNumber(value, noNegative) {
    if (typeof (noNegative) === 'undefined') noNegative = false;
    var isValidNumber = false;
    var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;
    if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
        if (value.indexOf(".") >= 0) {
            var n = value.split(".");
            if (n[n.length - 1].length <= 4) {
                isValidNumber = true;
            } else {
                isValidNumber = 'decimal_point_err';
            }
        } else {
            isValidNumber = true;
        }
    }
    return isValidNumber;
}

function en_cortigo_validateInput(form_id) {
    var has_err = true;
    jQuery(form_id + " input[type='text']").each(function () {
        var input = jQuery(this).val();
        var response = en_cortigo_validateString(input);

        var errorElement = jQuery(this).parent().find('.err');
        jQuery(errorElement).html('');
        var errorText = jQuery(this).attr('title');
        var optional = jQuery(this).data('optional');
        optional = (optional === undefined) ? 0 : 1;
        errorText = (errorText != undefined) ? errorText : '';
        if ((optional == 0) && (response == false || response == 'empty')) {
            errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';
            jQuery(errorElement).html(errorText);
        }
        has_err = (response != true && optional == 0) ? false : has_err;
    });
    return has_err;
}

function en_cortigo_validateString(string) {
    if (string == '') {
        return 'empty';
    } else {
        return true;
    }
}

function en_cortigo_getUrlVarsCortigoFreight() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function en_cortigo_setLtlDsCity($this) {
    var city = jQuery($this).val();
    jQuery('#ltl_dropship_city').val(city);
}

function en_cortigo_setLTLCity($this) {
    var city = jQuery($this).val();
    jQuery('#ltl_origin_city').val(city);
}