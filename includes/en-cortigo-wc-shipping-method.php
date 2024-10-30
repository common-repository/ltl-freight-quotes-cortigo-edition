<?php
/**
 * Shipping Method Class
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('woocommerce_shipping_init', 'en_cortigo_shipping_method_init');
/**
 * shipping method function to initiate the shipping calculation
 */
function en_cortigo_shipping_method_init()
{

    if (!class_exists('Cortigo_WC_Shipping_Method')) {

        /**
         * Shipping Method Class | shipping method function to initiate the shipping calculation
         */
        class Cortigo_WC_Shipping_Method extends WC_Shipping_Method
        {
            public $forceAllowShipMethodCortigo = array();
            public $getPkgObjCortigo;
            public $cortigo_res_inst;

            public $instore_pickup_and_local_delivery;
            public $web_service_inst;
            public $package_plugin;
            public $InstorPickupLocalDelivery;
            public $shipment_type;

            /**
             * Shipping method class constructor
             * @param $instance_id
             * @global $woocommerce
             */
            public function __construct($instance_id = 0)
            {
                error_reporting(0);
                $this->allow_arrangements = get_option('wc_settings_cortigo_allow_for_own_arrangment');
                $this->rate_method = get_option('wc_settings_cortigo_rate_method');
                $this->estimate_delivery = get_option('wc_settings_cortigo_delivery_estimate');
                $this->label_as = get_option('wc_settings_cortigo_label_as');
                $this->option_number = get_option('wc_settings_cortigo_Number_of_options');
                $this->arrangement_text = get_option('wc_settings_cortigo_text_for_own_arrangment');
                $this->id = 'en_cortigo_shipping_method';
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Cortigo Freight');
                $this->method_description = __('Shipping rates from Cortigo freight.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = "LTL Freight Quotes - Cortigo Edition";
                $this->init();

            }

            /**
             * shipping method initiate the form fields
             */
            function init()
            {
                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }

            /**
             * Third party quotes
             * @param type $forceShowMethods
             * @return type
             */
            public function forceAllowShipMethodCortigo($forceShowMethods)
            {
                if (!empty($this->getPkgObjCortigo->ValidShipmentsArr) && (!in_array("ltl_freight", $this->getPkgObjCortigo->ValidShipmentsArr))) {
                    $this->forceAllowShipMethodCortigo[] = "free_shipping";
                    $this->forceAllowShipMethodCortigo[] = "valid_third_party";

                } else {

                    $this->forceAllowShipMethodCortigo[] = "ltl_shipment";
                }

                $forceShowMethods = array_merge($forceShowMethods, $this->forceAllowShipMethodCortigo);

                return $forceShowMethods;
            }

            /**
             * shipping method enable/disable checkbox for shipping service
             */
            public function init_form_fields()
            {
                $this->instance_form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable / Disable', 'woocommerce'),
                        'type' => 'checkbox',
                        'label' => __('Enable This Shipping Service', 'woocommerce'),
                        'default' => 'yes',
                        'id' => 'cortigo_enable_disable_shipping'
                    )
                );
            }

            /**
             * shipping method rate calculation
             * @param $package
             * @return boolean
             */
            public function calculate_shipping($package = array())
            {

                $action_arr = array("woocommerce_add_to_cart", "woocommerce_cart_item_removed", "wp_loaded");
                $post_action = (isset($_POST['action'])) ? $_POST['action'] : "";
                if (($post_action != "en_cortigo_admin_order_quotes" && is_admin()) || (in_array(current_action(), $action_arr) && is_admin())) return FALSE;

//              Eniture debug mood
                do_action("eniture_error_messages", "Errors");

                $this->package_plugin = get_option('cortigo_freights_packages_quotes_package');

                $this->instore_pickup_and_local_delivery = FALSE;

                $coupon = WC()->cart->get_coupons();
                if (isset($coupon) && !empty($coupon)) {
                    $free_shipping = $this->en_cortigo_shipping_rate_coupon($coupon);
                    if ($free_shipping == 'y') return FALSE;
                }
                $billing_obj = new En_Cortigo_Wc_Billing_Details();
                $billing_details = $billing_obj->en_cortigo_billing_details();
                $freight_quotes = new En_Cortigo_Quotes_Request();

                $this->web_service_inst = $freight_quotes;

                $cart_obj = new En_Cortigo_Cart_To_Request();

                $this->getPkgObjCortigo = $cart_obj;
                add_filter('force_show_methods', array($this, 'forceAllowShipMethodCortigo'));
                $this->cortigo_res_inst = $freight_quotes;
                $this->ltl_shipping_quote_settings();

                if (isset($this->cortigo_res_inst->quote_settings['handling_fee']) &&
                    ($this->cortigo_res_inst->quote_settings['handling_fee'] == "-100%")) {
                    return FALSE;
                }

                $admin_settings = new En_Cortigo_Admin_Settings();
                $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = $billing_details['postcode'];
                $freight_package = $cart_obj->en_cortigo_cart_to_request($package, $freight_quotes, $freight_zipcode);
                $handlng_fee = $admin_settings->en_cortigo_get_handling_fee();
                $quotes = array();
                $web_service_array = $freight_quotes->en_cortigo_quotes_request($freight_package, $this->package_plugin);
                $quotes = $freight_quotes->en_cortigo_get_quotes($web_service_array);

                $this->quote_settings = $this->cortigo_res_inst->quote_settings;
                $this->quote_settings = json_decode(json_encode($this->quote_settings), true);
                $quotes = json_decode(json_encode($quotes), true);

                if (isset($quotes['stagin'])) {
                    unset($quotes['stagin']);
                }

                $handling_fee = $this->quote_settings['handling_fee'];

                $Cortigo_Quotes = new Cortigo_Quotes();
                if (count((array)$quotes) > 1) {

                    $multi_cost = 0;
                    $s_multi_cost = 0;
                    $_label = array();
                    $s_label = array();

//                       Custom client work "ltl_remove_small_minimum_value_By_zero_when_coupon_add" 
                    if (has_filter('small_min_remove_zero_type_params')) {
                        $smpkgCost = apply_filters('small_min_remove_zero_type_params', $package, $smpkgCost);
                    }

                    $this->quote_settings['shipment'] = "multi_shipment";

                    foreach ($quotes as $key => $quote) {
                        $quote = $freight_quotes->en_cortigo_pass_quotes($quote, $cart_obj, $handlng_fee);
                        $simple_quotes = (isset($quote['simple_quotes'])) ? $quote['simple_quotes'] : array();
                        $quote = $this->remove_array($quote, 'simple_quotes');

                        $rates = $Cortigo_Quotes->calculate_quotes($quote, $this->quote_settings);
                        $rates = reset($rates);

                        $_cost = (isset($rates['cost'])) ? $rates['cost'] : 0;
                        (isset($rates['label_sufex']) && (!empty($rates['label_sufex']))) ? $_label = array_merge($_label, $rates['label_sufex']) : "";
                        $append_label = (isset($rates['append_label'])) ? $rates['append_label'] : "";
                        $handling_fee = (isset($rates['markup']) && (strlen($rates['markup']) > 0)) ? $rates['markup'] : $handling_fee;

//                      Offer lift gate delivery as an option is enabled
                        if (isset($this->quote_settings['liftgate_delivery_option']) &&
                            ($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                            (!empty($simple_quotes))) {
                            $s_rates = $Cortigo_Quotes->calculate_quotes($simple_quotes, $this->quote_settings);
                            $s_rates = reset($s_rates);
                            $s_cost = (isset($s_rates['cost'])) ? $s_rates['cost'] : 0;
                            (isset($s_rates['label_sufex']) && (!empty($s_rates['label_sufex']))) ? $s_label = array_merge($s_label, $s_rates['label_sufex']) : "";
                            $s_append_label = (isset($s_rates['append_label'])) ? $s_rates['append_label'] : "";
                            $s_multi_cost += $this->add_handling_fee($s_cost, $handling_fee);
                        }

                        $multi_cost += $this->add_handling_fee($_cost, $handling_fee);
                    }

                    ($s_multi_cost > 0) ? $rate[] = $this->arrange_multiship_freight(($s_multi_cost), 's_multiple_shipment', $s_label, $s_append_label) : "";
                    ($multi_cost > 0) ? $rate[] = $this->arrange_multiship_freight(($multi_cost), '_multiple_shipment', $_label, $append_label) : "";

                    $this->shipment_type = "multiple";

                    isset($rate) && !empty($rate) ? $this->cortigo_add_rate_arr($rate) : '';

                } else {

//                  Dispaly Local and In-store PickUp Delivery 
                    $this->InstorPickupLocalDelivery = $freight_quotes->cortigo_ltl_return_local_delivery_store_pickup();
                    $this->web_service_inst->en_wd_origin_array = isset($this->web_service_inst->en_wd_origin_array) && !empty($this->web_service_inst->en_wd_origin_array) ? reset($this->web_service_inst->en_wd_origin_array) : array();
                    (isset($this->InstorPickupLocalDelivery->localDelivery) && ($this->InstorPickupLocalDelivery->localDelivery->status == 1)) ? $this->local_delivery($this->web_service_inst->en_wd_origin_array['fee_local_delivery'], $this->web_service_inst->en_wd_origin_array['checkout_desc_local_delivery']) : "";
                    (isset($this->InstorPickupLocalDelivery->inStorePickup) && ($this->InstorPickupLocalDelivery->inStorePickup->status == 1)) ? $this->pickup_delivery($this->web_service_inst->en_wd_origin_array['checkout_desc_store_pickup']) : "";

                    if (isset($quotes) && !empty($quotes)) {
                        $quote = $freight_quotes->en_cortigo_pass_quotes(reset($quotes), $cart_obj, $handlng_fee);

                        $simple_quotes = (isset($quote['simple_quotes'])) ? $quote['simple_quotes'] : array();
                        $quote = $this->remove_array($quote, 'simple_quotes');

                        $rates = $Cortigo_Quotes->calculate_quotes($quote, $this->quote_settings);

//                      Offer lift gate delivery as an option is enabled
                        if (isset($this->quote_settings['liftgate_delivery_option']) &&
                            ($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                            (!empty($simple_quotes))) {
                            $simple_rates = $Cortigo_Quotes->calculate_quotes($simple_quotes, $this->quote_settings);
                            $rates = array_merge($rates, $simple_rates);
                        }

                        $cost_sorted_key = array();

                        $this->quote_settings['shipment'] = "single_shipment";

                        foreach ($rates as $key => $quote) {
                            $handling_fee = (isset($rates['markup']) && (strlen($rates['markup']) > 0)) ? $rates['markup'] : $handling_fee;
                            $_cost = (isset($quote['cost'])) ? $quote['cost'] : 0;
                            $rates[$key]['cost'] = $this->add_handling_fee($_cost, $handling_fee);
                            $cost_sorted_key[$key] = (isset($quote['cost'])) ? $quote['cost'] : 0;
                            $rates[$key]['shipment'] = "single_shipment";

                            $this->quote_settings['transit_days'] == "yes" && strlen($quote['transit_days']) > 0 ? $rates[$key]['transit_label'] = ' ( Estimated transit time of ' . $quote['transit_days'] . ' business days. )' : "";

                        }

//                       array multisort 
                        array_multisort($cost_sorted_key, SORT_ASC, $rates);

                        $this->shipment_type = "single";

                        $this->cortigo_add_rate_arr($rates);
                    }
                }

            }

            /**
             * Multishipment
             * @return array
             */
            function arrange_multiship_freight($cost, $id, $label_sufex, $append_label)
            {

                return array(
                    'id' => $id,
                    'label' => "Freight",
                    'cost' => $cost,
                    'label_sufex' => $label_sufex,
                    'append_label' => $append_label,
                );
            }

            /**
             *
             * @param string type $price
             * @param string type $handling_fee
             * @return float type
             */
            function add_handling_fee($price, $handling_fee)
            {
                $handling_fee = !$price > 0 ? 0 : $handling_fee;
                $handelingFee = 0;
                if ($handling_fee != '' && $handling_fee != 0) {
                    if (strrchr($handling_fee, "%")) {

                        $prcnt = (float)$handling_fee;
                        $handelingFee = (float)$price / 100 * $prcnt;
                    } else {
                        $handelingFee = (float)$handling_fee;
                    }
                }

                $handelingFee = $this->smooth_round($handelingFee);
                $price = (float)$price + $handelingFee;
                return $price;
            }

            /**
             * Remove array
             * @return array
             */
            function remove_array($quote, $remove_index)
            {
                unset($quote[$remove_index]);

                return $quote;
            }

            /**
             * filter label new update
             * @param type $label_sufex
             * @return string
             */
            public function filter_from_label_sufex($label_sufex)
            {
                $append_label = "";
                switch (TRUE) {
                    case (in_array("R", $label_sufex) && in_array("L", $label_sufex)):
                        $append_label = " with lift gate and residential delivery ";
                        break;

                    case (in_array("L", $label_sufex)):
                        $append_label = " with lift gate delivery ";
                        break;

                    case (in_array("R", $label_sufex)):
                        $append_label = " with residential delivery ";
                        break;
                }

                return $append_label;
            }

            /**
             *
             * @param float type $val
             * @param int type $min
             * @param int type $max
             * @return float type
             */
            function smooth_round($val, $min = 2, $max = 4)
            {
                $result = round($val, $min);
                if ($result == 0 && $min < $max) {
                    return $this->smooth_round($val, ++$min, $max);
                } else {
                    return $result;
                }
            }

            /**
             * Label from quote settings tab
             * @return string type
             */
            public function wwe_label_as()
            {
                return (strlen($this->quote_settings['wwe_label']) > 0) ? $this->quote_settings['wwe_label'] : "Freight";
            }

            /**
             * Append label in quote
             * @param array type $rate
             * @return string type
             */
            public function set_label_in_quote($rate)
            {
                $rate_label = "";
                $label_sufex = (isset($rate['label_sufex'])) ? array_unique($rate['label_sufex']) : array();
                $rate_label = (!isset($rate['label']) ||
                    ($this->quote_settings['shipment'] == "single_shipment" &&
                        strlen($this->quote_settings['wwe_label']) > 0)) ?
                    $this->wwe_label_as() : $rate['label'];

                $rate_label .= (isset($this->quote_settings['sandbox'])) ? ' (Sandbox) ' : '';
                $rate_label .= (isset($rate['transit_label'])) ? $rate['transit_label'] : "";
                $rate_label .= $this->filter_from_label_sufex($label_sufex);
                return $rate_label;
            }

            /**
             * rates to add_rate woocommerce
             * @param array type $add_rate_arr
             */
            public function cortigo_add_rate_arr($add_rate_arr)
            {

                if (isset($add_rate_arr) && (!empty($add_rate_arr)) && (is_array($add_rate_arr))) {
                    add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                    $instore_pickup_local_devlivery_action = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');

                    foreach ($add_rate_arr as $key => $rate) {

                        if (isset($this->web_service_inst->en_wd_origin_array['suppress_local_delivery']) && $this->web_service_inst->en_wd_origin_array['suppress_local_delivery'] == "1" && (!is_array($instore_pickup_local_devlivery_action)) && $this->shipment_type != "multiple") {

                            $rate = apply_filters('suppress_local_delivery', $rate, $this->web_service_inst->en_wd_origin_array, $this->package_plugin, $this->InstorPickupLocalDelivery);

                            if (!empty($rate)) {
                                $this->add_rate($rate);
                            }

                        } else if (isset($rate['cost']) && $rate['cost'] > 0) {
                            $rate['label'] = $this->set_label_in_quote($rate);
                            $this->add_rate($rate);
                        }
                    }

                    (isset($this->quote_settings['own_freight']) && ($this->quote_settings['own_freight'] == "yes")) ? $this->add_rate($this->arrange_own_freight()) : "";
                }

            }

            /**
             * Free Shipping rate
             * @param $coupon
             * @return string/array
             */
            function arrange_own_freight()
            {
                return array(
                    'id' => 'free',
                    'label' => $this->arrangement_text,
                    'cost' => 0
                );
            }

            /**
             * final rates sorting
             * @param array type $rates
             * @param array type $package
             * @return array type
             */
            function en_sort_woocommerce_available_shipping_methods($rates, $package)
            {
                //  if there are no rates don't do anything
                if (!$rates) {
                    return;
                }

                // get an array of prices
                $prices = array();
                foreach ($rates as $rate) {
                    $prices[] = $rate->cost;
                }

                // use the prices to sort the rates
                array_multisort($prices, $rates);

                // return the rates
                return $rates;
            }

            /**
             * quote settings array
             * @global $wpdb $wpdb
             */
            function ltl_shipping_quote_settings()
            {
                global $wpdb;
                $rating_method = get_option('wc_settings_cortigo_rate_method');
                $wwe_label = get_option('wc_settings_cortigo_label_as');
                $this->cortigo_res_inst->quote_settings['transit_days'] = get_option('wc_settings_cortigo_delivery_estimate');
                $this->cortigo_res_inst->quote_settings['own_freight'] = get_option('wc_settings_cortigo_allow_for_own_arrangment');
                $this->cortigo_res_inst->quote_settings['total_carriers'] = get_option('wc_settings_cortigo_Number_of_options');
                $this->cortigo_res_inst->quote_settings['rating_method'] = (isset($rating_method) && (strlen($rating_method)) > 0) ? $rating_method : "Cheapest";
                $this->cortigo_res_inst->quote_settings['wwe_label'] = ($rating_method == "average_rate" || $rating_method == "Cheapest") ? $wwe_label : "";
                $this->cortigo_res_inst->quote_settings['handling_fee'] = get_option('wc_settings_cortigo_hand_free_mark_up');
                $this->cortigo_res_inst->quote_settings['liftgate_delivery'] = get_option('wc_settings_cortigo_lift_gate_delivery');
                $this->cortigo_res_inst->quote_settings['liftgate_delivery_option'] = get_option('cortigo_freights_liftgate_delivery_as_option');
                $this->cortigo_res_inst->quote_settings['residential_delivery'] = get_option('wc_settings_cortigo_residential_delivery');
                $this->cortigo_res_inst->quote_settings['liftgate_resid_delivery'] = get_option('en_woo_addons_liftgate_with_auto_residential');
            }

            /**
             * Free Shipping rate
             * @param $coupon
             * @return string/array
             */
            function en_cortigo_shipping_rate_coupon($coupon)
            {
                foreach ($coupon as $key => $value) {
                    if ($value->get_free_shipping() == 1) {
                        $rates = array(
                            'id' => 'free',
                            'label' => 'Free Shipping',
                            'cost' => 0
                        );
                        $this->add_rate($rates);
                        return 'y';
                    }
                }
                return 'n';
            }

            /**
             * Pickup delivery quote
             * @return array type
             */
            function pickup_delivery($label)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;

                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'In-store pick up';

//              check woocommerce version for displying instore pickup cost $0.00
                $woocommerce_version = get_option('woocommerce_version');
                $label = ($woocommerce_version < '3.5.4') ? $label : $label . ': $0.00';

                $pickup_delivery = array(
                    'id' => 'in-store-pick-up',
                    'cost' => 0,
                    'label' => $label,
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($pickup_delivery);
            }


            /**
             * Local delivery quote
             * @param string type $cost
             * @return array type
             */
            function local_delivery($cost, $label)
            {

                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;
                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'Local Delivery';
                if ($cost == 0) {
//              check woocommerce version for displying instore pickup cost $0.00
                    $woocommerce_version = get_option('woocommerce_version');
                    $label = ($woocommerce_version < '3.5.4') ? $label : $label . ': $0.00';
                }

                $local_delivery = array(
                    'id' => 'local-delivery',
                    'cost' => $cost,
                    'label' => $label,
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($local_delivery);
            }

            /**
             * Final Rate Array
             * @param $grand_total
             * @param $code
             * @param $label
             * @return array
             */
            function en_cortigo_final_rate_array($grand_total, $code, $label)
            {
                if ($grand_total > 0) {
                    $rates = array(
                        'id' => $code,
                        'label' => ($label == '') ? 'Freight' : $label,
                        'cost' => $grand_total
                    );
                }
                return $rates;
            }
        }
    }
}
