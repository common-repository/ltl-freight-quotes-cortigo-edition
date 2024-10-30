<?php
/**
 * WooCommerce Settings script file
 * @package     Woocommerce Cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce Settings Scripts Class | all scripts like alert,ajax call, validation etc...
 */
class Cortigo_Wc_Settings_Js
{
    /**
     * setting script js class constructor
     */
    function __construct()
    {
        add_action('admin_footer', array($this, 'en_cortigo_carrier_check_all'));
        add_action('admin_footer', array($this, 'en_cortigo_connection_ajax_calling'));
        add_action('admin_footer', array($this, 'en_cortigo_no_carrier_selected'));
        add_action('admin_footer', array($this, 'en_cortigo_admin_quote_setting_input'));
    }

    /**
     * test connection ajax calling
     */
    function en_cortigo_connection_ajax_calling()
    {
        ?>
        <script>

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

            jQuery(document).ready(function () {


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
            });

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
                        jQuery('#wc_settings_cortigo_shipper_id').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                        jQuery('#wc_settings_cortigo_username').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                        jQuery('#wc_settings_cortigo_password').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                        jQuery('#wc_settings_cortigo_authentication_key').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                        jQuery('#wc_settings_cortigo_licence_key').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
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
//                  error: function(data){
//                      alert(data);
//                  }
                });
                e.preventDefault();
            })

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
        </script>
        <?php
    }

    /**
     * carrier check list for validate to check if at least one carrier is checked or not
     */
    function en_cortigo_no_carrier_selected()
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('.cort_ltl_connection_section_class .form-table').before('<div class="warning-msg-ltl"><p> <b>Note!</b> You must have a Cortigo account to use this application. If you do not have one,  call at <a href="tel:800-333-7400">800-333-7400</a>, or click <a href="https://eniture.com/request-worldwide-express-account-number/" target="_blank">here</a> to access the new account request form. </p>');

                jQuery('.cort_carrier_section_class .button-primary').on('click', function () {
                    jQuery(".updated").hide();
                    var num_of_checkboxes = jQuery('.carrier_check:checked').size();
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
            });
        </script>
        <?php
    }

    /**
     * script for check all carriers on click all checkbox
     */
    function en_cortigo_carrier_check_all()
    {
        ?>
        <script>
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
                var int_checkboxes = jQuery('.carrier_check:checked').size();
                var int_un_checkboxes = jQuery('.carrier_check').size();
                if (int_checkboxes === int_un_checkboxes) {
                    jQuery('.include_all').attr('checked', true);
                } else {
                    jQuery('.include_all').attr('checked', false);
                }
            });

        </script>
        <?php
    }

    /**
     * admin quote settings scripts
     */
    function en_cortigo_admin_quote_setting_input()
    { ?>
        <input type="hidden" id="show_cortigo_saved_method"
               value="<?php echo get_option('wc_settings_cortigo_rate_method'); ?>"/>
        <script>
            jQuery(window).load(function () {
                var saved_mehod_value = jQuery('#show_cortigo_saved_method').val();
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

            jQuery(document).ready(function () {

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
            })


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
        </script>
        <?php
    }
}