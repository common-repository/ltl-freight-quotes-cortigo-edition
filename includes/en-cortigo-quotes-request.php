<?php

/**
 * Quote Request Class | quote request for getting carriers
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quote Request Class | getting request for cart items, sending request
 */
class En_Cortigo_Quotes_Request extends En_Cortigo_Cart_To_Request
{

    public $en_wd_origin_array;
    public $InstorPickupLocalDelivery;

    /**
     * details array
     * @var array type
     */
    public $quote_settings;

    /**
     * Quote Request constructor
     */
    function __construct()
    {
    }

    /**
     * Quotes Request
     * @param $packages
     * @return array
     */
    public function en_cortigo_quotes_request($packages, $package_plugin = '')
    {
        $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : array();
       
        if (!empty($packages))

            $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");
        $domain = cortigo_freights_get_domain();
        // Version numbers
        $plugin_versions = $this->en_version_numbers();

        $post_data = array(
            // Version numbers
            'plugin_version' => $plugin_versions["en_current_plugin_version"],
            'wordpress_version' => get_bloginfo('version'),
            'woocommerce_version' => $plugin_versions["woocommerce_plugin_version"],
            'apiVersion' => $this->apiVersion,
            'plateform' => $this->plateform,
            'carrierName' => $this->carrier_name,
            'requestKey' => md5(microtime() . rand()),

            'suspend_residential' => get_option('suspend_automatic_detection_of_residential_addresses'),
            'residential_detecion_flag' => isset($residential_detecion_flag) ? $residential_detecion_flag : '',

            'carriers' => array(
                'cortigo' => array(
                    'licenseKey' => get_option('wc_settings_cortigo_licence_key'),
                    'serverName' => $domain,
                    'carrierMode' => $this->carrierMode,
                    'quotestType' => $this->quotestType,
                    'version' => $this->version(),
                    'returnQuotesOnExceedWeight' => $this->en_cortigo_quotes_on_exceed_weight(),
                    'api' => $this->en_cortigo_api_credentials($packages),
                    'originAddress' => $this->en_cortigo_origin_address($packages)
                )
            ),
            'receiverAddress' => $this->en_cortigo_reciever_address(),
            'commdityDetails' => isset($packages['items']) ? $this->en_cortigo_line_items($packages['items']) : '',
        );

        $En_Cortigo_Liftgate_As_Option = new En_Cortigo_Liftgate_As_Option();
        $post_data = $En_Cortigo_Liftgate_As_Option->cortigo_freights_update_carrier_service($post_data);
        $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request", $post_data, en_woo_plugin_cortigo_freights);

//      In-store pickup and local delivery
        $instore_pickup_local_devlivery_action = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
        if (!is_array($instore_pickup_local_devlivery_action)) {
            $post_data['carriers']['cortigo']['api']['InstorPickupLocalDelivery'] = apply_filters('en_wd_standard_plans', $post_data, $post_data['receiverAddress']['receiverZip'], $this->en_wd_origin_array, $package_plugin);
        }

//      Eniture debug mood
        do_action("eniture_debug_mood", "Plugin Features(CORTIGO) ", get_option('eniture_plugin_17'));
        do_action("eniture_debug_mood", "Quotes Request (CORTIGO)", $post_data);

        return $post_data;
    }

    /**
     * Return version numbers
     * @return int
     */
    function en_version_numbers()
    {
        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $plugin_folder = get_plugins('/' . 'woocommerce');
        $plugin_file = 'woocommerce.php';
        $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
        $get_plugin_data = get_plugin_data(CORTIGO_MAIN_FILE);
        $plugin_version = (isset($get_plugin_data['Version'])) ? $get_plugin_data['Version'] : '';

        $versions = array(
            "woocommerce_plugin_version" => $wc_plugin,
            "en_current_plugin_version" => $plugin_version
        );

        return $versions;
    }

    /**
     * Getting Line Items
     * @param $packages
     * @return array
     */
    function en_cortigo_line_items($packages)
    {
        $line_item = array();

        $hazmat_flage = false;
        if (get_option('cortigo_freights_store_type') == "1") {
            $hazardous_material = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'hazardous_material');
            if (!is_array($hazardous_material)) {
                $hazmat_flage = true;
            }
        } else {
            $hazmat_status = get_option('en_old_user_hazmat_status');
            isset($hazmat_status) && ($hazmat_status == 1) ? $hazmat_flage = false : $hazmat_flage = true;
        }

        foreach ($packages as $item) {
            $line_item[] = array(
                'freightClass' => $item['freightClass'],
                'lineItemHeight' => $item['productHeight'],
                'lineItemLength' => $item['productLength'],
                'lineItemWidth' => $item['productWidth'],
                'lineItemClass' => $item['productClass'],
                'lineItemWeight' => $item['productWeight'],
                'piecesOfLineItem' => $item['productQty'],
                'isHazmatLineItem' => isset($hazmat_flage) && ($hazmat_flage == true) ? $item['isHazmatLineItem'] : 'N'
            );
        }
        return $line_item;
    }

    /**
     * Checking item is hazmet or not
     * @param $packages
     * @return string
     */
    function en_cortigo_item_hazmet($packages)
    {

        $hazmet = '';
       
        if (is_array($packages)) {
           foreach ($packages['items'] as $item):
                $items_id[] = array(
                    'id' => $item['productId']
                );
            endforeach;
            foreach ($items_id as $pid):
                $enable_hazmet[] = get_post_meta($pid['id'], '_hazardousmaterials', true);
            endforeach;
            //      Hazardous Material
            if (get_option('cortigo_freights_store_type') == "1") {
                $hazardous_material = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'hazardous_material');
                if (!is_array($hazardous_material)) {
                    $hazmet = (in_array("yes", $enable_hazmet)) ? 'Y' : 'N';
                }
            } else {
                $hazmet = (in_array("yes", $enable_hazmet)) ? 'Y' : 'N';
            }
        }
            return $hazmet;
        
    }

    /**
     * Checking item delivery is Residential/Liftgate
     * @return array
     */
    function en_cortigo_item_accessorial()
    {
        $accessorials = array();
        $wc_liftgate = get_option('wc_settings_cortigo_lift_gate_delivery');
        $wc_residential = get_option('wc_settings_cortigo_residential_delivery');
        ($wc_liftgate == 'yes') ? $accessorials[] = 'LFTGATORIG' : "";
        ($wc_residential == 'yes') ? $accessorials[] = 'RESFURDEL' : "";
        return $accessorials;
    }

    /**
     * Getting origin address
     * @param $packages
     * @return array
     */
    function en_cortigo_origin_address($packages)
    {
        $origin_address = [];
        if (is_array($packages)) {
            foreach ($packages['origin'] as $k => $origin):
                $origin['senderZip'] = preg_replace('/\s+/', '', $origin['zip']);
                if (trim($origin['country']) == 'USA') {
                    $origin['country'] = 'US';
                }
                $origin_address[] = array(
                    'location_id' => $origin['locationId'],
                    'senderCity' => $origin['city'],
                    'senderState' => $origin['state'],
                    'senderZip' => $origin['senderZip'],
                    'location' => $origin['location'],
                    'senderCountryCode' => trim($origin['country'])
                );
            endforeach;
        }
            return $origin_address;
    }

    /**
     * Country Code
     * @param $country
     */
    function en_cortigo_origin_country($country)
    {
        if (isset($country)) {
            if ($country = 'US' || $country = 'USA') {
                $sender_country = "US";
            } else if ($country = 'CA' || $country = 'CN' || $country = 'CAN') {
                $sender_country = 'CA';
            }
        }
        return $sender_country;
    }

    /**
     * Getting customer address
     * @return array
     */
    function en_cortigo_reciever_address()
    {
        $billing_obj = new En_Cortigo_Wc_Billing_Details();
        $billing_details = $billing_obj->en_cortigo_billing_details();
        $freight_zipcode = "";
        $freight_state = "";
        $freight_city = "N";
        (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = $billing_details['postcode'];
        (strlen(WC()->customer->get_shipping_state()) > 0) ? $freight_state = WC()->customer->get_shipping_state() : $freight_state = $billing_details['state'];
        (strlen(WC()->customer->get_shipping_country()) > 0) ? $freight_country = WC()->customer->get_shipping_country() : $freight_country = $billing_details['country'];
        (strlen(WC()->customer->get_shipping_city()) > 0) ? $freight_city = WC()->customer->get_shipping_city() : $freight_city = $billing_details['city'];
        (strlen(WC()->customer->get_shipping_address_1()) > 0) ? $freight_addressline = WC()->customer->get_shipping_address_1() : $freight_addressline = $billing_details['s_address'];

        $freight_zipcode = preg_replace('/\s+/', '', $freight_zipcode);
        if (trim($freight_country) == 'USA') {
            $freight_country = 'US';
        }
        $address = array(
            'receiverCity' => $freight_city,
            'receiverState' => $freight_state,
            'receiverZip' => $freight_zipcode,
            'receiverCountryCode' => trim($freight_country),//$this->en_cortigo_origin_country($freight_country),
            'addressLine' => (isset($_POST['s_addres'])) ? trim($_POST['s_addres']) : $freight_addressline
        );
        return $address;
    }

    /**
     * destinationAddressUps
     * @return array type
     */
    function destinationAddressCortigo()
    {
        $reciever_address = $this->en_cortigo_reciever_address();
        return array(
            'city' => $reciever_address['receiverCity'],
            'state' => $reciever_address['receiverState'],
            'zip' => $reciever_address['receiverZip'],
            'country' => $reciever_address['receiverCountryCode'],
        );
    }

    /**
     * API Credentials
     * @param $packages
     * @return array/string
     */
    function en_cortigo_api_credentials($packages)
    {
        $credentials = array(
            'shipperID' => get_option('wc_settings_cortigo_shipper_id'),
            'username' => get_option('wc_settings_cortigo_username'),
            'password' => get_option('wc_settings_cortigo_password'),
            'accessKey' => get_option('wc_settings_cortigo_authentication_key'),
            'direction' => 'Dropship',
            'billingType' => 'Prepaid',
            'hazmat' => $this->en_cortigo_item_hazmet($packages),
            'accessorial' => $this->en_cortigo_item_accessorial(),
        );
        return $credentials;
    }

    /**
     * Quotes On Exceed Weight
     * @return int
     */
    function en_cortigo_quotes_on_exceed_weight()
    {
        if (get_option('en_plugins_return_LTL_quotes') == 'yes') {
            $quotes_on_exceed_weight = "1";
        } else {
            $quotes_on_exceed_weight = "0";
        }
        return $quotes_on_exceed_weight;
    }

    /**
     * getting quotes via curl class
     * @param $request_data
     * @return string
     */
    function en_cortigo_get_quotes($request_data)
    {

//      check response from session 
        $srequest_data = $request_data;
        $srequest_data['requestKey'] = "";
        $currentData = md5(json_encode($srequest_data));

        $requestFromSession = WC()->session->get('previousRequestData');

        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {
            $instorepickup = json_decode($requestFromSession[$currentData]);
            $instorepickup = reset($instorepickup);
            $instorepickup = reset($instorepickup);

            $this->InstorPickupLocalDelivery = (isset($instorepickup->InstorPickupLocalDelivery) ? $instorepickup->InstorPickupLocalDelivery : NULL);

//          Eniture debug mood
            do_action("eniture_debug_mood", "Plugin Features(CORTIGO) ", get_option('eniture_plugin_17'));
            do_action("eniture_debug_mood", "Quotes Response (CORTIGO)", json_decode($requestFromSession[$currentData]));
            $quote_response = json_decode($requestFromSession[$currentData]);
            if (isset($quote_response->cortigo) && !empty($quote_response->cortigo)) {
                return $quote_response->cortigo;
            }
        }

        if (is_array($request_data) && count($request_data) > 0) {
            $curl_obj = new En_Cortigo_Wc_Curl_Class();
            $output = $curl_obj->en_cortigo_get_curl_response($this->end_point_url_pro, $request_data);

            $instorepickup = json_decode($output);
            $instorepickup = isset($instorepickup) ? reset($instorepickup) : '';
            $instorepickup =  !empty($instorepickup) ? reset($instorepickup) : '';
//          set response in session
            $this->InstorPickupLocalDelivery = (isset($instorepickup->InstorPickupLocalDelivery) ? $instorepickup->InstorPickupLocalDelivery : NULL);
            $response = json_decode($output, TRUE);


            if (isset($response['cortigo']) && (!empty($response['cortigo']))) {
                if (isset($response['autoResidentialSubscriptionExpired']) &&
                    ($response['autoResidentialSubscriptionExpired'] == 1)) {
                    $flag_api_response = "no";
                    $srequest_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($srequest_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }

            $response = json_decode($output);

            $this->InstorPickupLocalDelivery = (isset($response->InstorPickupLocalDelivery) ? $response->InstorPickupLocalDelivery : NULL);

//          Eniture debug mood
            do_action("eniture_debug_mood", "Plugin Features(CORTIGO) ", get_option('eniture_plugin_17'));
            do_action("eniture_debug_mood", "Quotes Response (CORTIGO)", json_decode($output));

            $quote_response = json_decode($output);
            if (isset($quote_response->cortigo) && !empty($quote_response->cortigo)) {
                return $quote_response->cortigo;
            }
            return FALSE;
        }
    }

    /**
     * check "R" in array
     * @param array type $label_sufex
     * @return array type
     */
    public function label_R_cortigo($label_sufex)
    {
        if (get_option('wc_settings_cortigo_residential_delivery') == 'yes' && (in_array("R", $label_sufex))) {
            $label_sufex = array_flip($label_sufex);
            unset($label_sufex['R']);
            $label_sufex = array_keys($label_sufex);

        }

        return $label_sufex;
    }


    /**
     * passing quotes result to display
     * @param $quotes
     * @param $cart_obj
     * @param $handlng_fee
     * @return string/array
     */
    function en_cortigo_pass_quotes($quotes, $cart_obj, $handlng_fee)
    {

        $carr = $this->en_cortigo_get_active_carriers();

        $allServices = array();
        if (isset($quotes)) {
            $En_Cortigo_Liftgate_As_Option = new En_Cortigo_Liftgate_As_Option();
            $label_sufex = $En_Cortigo_Liftgate_As_Option->filter_label_sufex_array_cortigo_freights($quotes);

            $count = 0;
            $price_sorted_key = array();
            $simple_quotes = array();
            $quotes = (isset($quotes['q'])) ? $quotes['q'] : array();

            foreach ($quotes as $quote) {
                $service_type = (isset($quote['serviceType'])) ? $quote['serviceType'] : "";
                if (strpos($service_type, '-') !== false) {
                    $service_type_exp = explode('-', $service_type);
                    $service_type = (isset($service_type_exp['0'])) ? $service_type_exp['0'] : "";
                }

                if (isset($carr[$service_type])) {
                    $allServices[$count] = array(
                        'id' => $quote['serviceType'],
                        'carrier_scac' => $quote['serviceType'],
                        'carrier_name' => $quote['serviceDesc'],
                        'label' => $quote['serviceDesc'],
                        'label_sufex' => $label_sufex,
                        'cost' => $quote['totalNetCharge'],
                        'transit_days' => $quote['deliveryDayOfWeek']
                    );

                    $allServices[$count] = apply_filters("en_woo_addons_web_quotes", $allServices[$count], en_woo_plugin_cortigo_freights);

                    $label_sufex = (isset($allServices[$count]['label_sufex'])) ? $allServices[$count]['label_sufex'] : array();

                    $label_sufex = $this->label_R_cortigo($label_sufex);
                    $allServices[$count]['label_sufex'] = $label_sufex;

                    $liftgate_charge = (isset($carr[$service_type])) ? $carr[$service_type] : 0;

                    if (($this->quote_settings['liftgate_delivery_option'] == "yes") && array_filter($carr) &&
                        (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                            ($this->quote_settings['liftgate_resid_delivery'] != "yes"))) {
                        if ($liftgate_charge > 0) {
                            $service = $allServices[$count];
                            (isset($allServices[$count]['id'])) ? $allServices[$count]['id'] .= "WL" : $allServices[$count]['id'] = "WL";

                            (isset($allServices[$count]['label_sufex']) &&
                                (!empty($allServices[$count]['label_sufex']))) ?
                                array_push($allServices[$count]['label_sufex'], "L") :  // IF
                                $allServices[$count]['label_sufex'] = array("L");       // ELSE

                            $allServices[$count]['append_label'] = " with lift gate delivery ";

                            $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                            (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = array();

                            $simple_quotes[$count] = $service;

                            $price_sorted_key[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;
                        } else {
                            if (isset($allServices[$count])) unset ($allServices[$count]);
                        }
                    }

                    $count++;
                }
            }
        }

//           array multisort 
        (!empty($simple_quotes)) ? array_multisort($price_sorted_key, SORT_ASC, $simple_quotes) : "";

        (!empty($simple_quotes)) ? $allServices['simple_quotes'] = $simple_quotes : "";

        return $allServices;

    }

    /**
     * getting warehouse address
     * @param $warehous_list
     * @param $receiver_zip_code
     * @return array
     */
    public function en_cortigo_get_warehouse($warehous_list, $receiver_zip_code)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->en_cortigo_origin_array($warehous_list);
        }

        $cortigo_distance_request = new Get_cortigo_freights_distance();
        $accessLevel = "MultiDistance";
        $response_json = $cortigo_distance_request->cortigo_freights_get_distance($warehous_list, $accessLevel, $this->destinationAddressCortigo());

        $response_obj = json_decode($response_json);
        return isset($response_obj->origin_with_min_dist) ? $this->en_cortigo_origin_array($response_obj->origin_with_min_dist) : '';
    }

    /**
     * getting plugin origin
     * @param $origin
     * @return array
     */
    function en_cortigo_origin_array($origin)
    {

//      In-store pickup and local delivery
        if (has_filter("en_wd_origin_array_set")) {
            return apply_filters("en_wd_origin_array_set", $origin);
        }

        $origin_array = array(
            'location_id' => $origin->id,
            'senderZip' => $origin->zip,
            'senderCity' => $origin->city,
            'senderState' => $origin->state,
            'location' => $origin->location,
            'senderCountryCode' => $origin->country
        );
        return $origin_array;
    }

    /**
     * All Enabled Carriers List
     * @return array
     * @global $wpdb
     */
    function en_cortigo_get_active_carriers()
    {
        global $wpdb;
        $all_carriers = $wpdb->get_results(
            "SELECT `id`, `carrier_scac`, `carrier_status`, `liftgate_fee` FROM " . $wpdb->prefix . "carriers WHERE `plugin_name`='cortigo_ltl' AND `carrier_status`='1'"
        );
        if ($all_carriers) {
            foreach ($all_carriers as $key => $value) {
                $carriers[$value->carrier_scac] = $value->liftgate_fee;
            }
            return $carriers;
        } else {
            return $carriers = array('Error' => 'Not active carriers found!');
        }
    }

    /**
     * Return Cortigo LTL In-store Pickup Array
     */
    function cortigo_ltl_return_local_delivery_store_pickup()
    {
        return $this->InstorPickupLocalDelivery;
    }

}
