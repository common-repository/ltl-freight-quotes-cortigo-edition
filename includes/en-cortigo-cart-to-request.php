<?php
/**
 * Cart Request Class | cart items requests
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cart to Request Class | request for carriers, receiver address
 */
class En_Cortigo_Cart_To_Request extends En_Cortigo_Ltl
{
    public $hasLTLShipment = 0;
    public $ValidShipmentsArr = array();

    /**
     * variable to display errors
     * @var svariabletring
     */
    public $errors = array();

    /**
     * cart to request class constructor
     */
    function __construct()
    {
    }

    /**
     * Cart To Request
     * @param $package
     * @param $freight_quotes
     * @param $freight_zipcode
     * @return array
     */
    function en_cortigo_cart_to_request($package, $freight_quotes, $freight_zipcode)
    {
        if (empty($freight_zipcode) || count((array)$package) < 0) {
            return FALSE;
        }
        $cortigo_package = [];
        $weight = 0;
        $dimensions = 0;
        $freight_enable = false;
        $exceedWeight = get_option('en_plugins_return_LTL_quotes');
        foreach ($package['contents'] as $item_id => $values) {
            $_product = $values['data'];
            $height = wc_get_dimension($_product->get_height(), 'in');
            $width = wc_get_dimension($_product->get_width(), 'in');
            $length = wc_get_dimension($_product->get_length(), 'in');
            $product_weight = wc_get_weight($_product->get_weight(), 'lbs');
            $weight = ($values['quantity'] == 1) ? $product_weight : $product_weight * $values['quantity'];
            $dimensions = (($length * $values['quantity']) * $width * $height);

            $shipping_class_id = $_product->get_shipping_class_id();

            $freight_class = ($shipping_class_id > 0) ? 'ltl' : '';

            $shipping_class = $_product->get_shipping_class();
            $freight_class = ($shipping_class == 'ltl_freight') ? 'ltl' : '';

            $location_id = 0;
            $origin_address = $this->en_cortigo_get_origin($_product, $values, $freight_quotes, $freight_zipcode);
            $origin_address = $freight_quotes->en_cortigo_get_warehouse($origin_address, $freight_zipcode);

            $freight_class_ltl_gross = $this->en_cortigo_get_freight_class($_product, $values['variation_id'], $values['product_id']);
            ($freight_class_ltl_gross == 'Null') ? $freight_class_ltl_gross = "" : "";

            if (!empty($origin_address)) {
                $location_id = $origin_address['locationId'];
//                if(isset($cortigo_package['origin'])) {
                $cortigo_package['origin'][] = isset($origin_address) ? $origin_address : '';
//                }
                if (!$_product->is_virtual()) {
//                    if(isset($cortigo_package['items'])) {
                    $cortigo_package['items'][] = $this->en_cortigo_item_dimensions($_product, $values, $freight_class_ltl_gross, $freight_class, $height, $width, $length, $product_weight);
//                    }
                }
            }

            $freight_enable = $this->en_cortigo_get_cortigo_enable($_product);
            $weight = ($_product->get_weight() * $values['quantity']);
            $small_plugin_exist = 0;
            $called_method = array();
            $eniture_pluigns = json_decode(get_option('EN_Plugins'));
            if (!empty($eniture_pluigns)) {
                foreach ($eniture_pluigns as $en_index => $en_plugin) {
                    $freight_small_class_name = 'WC_' . $en_plugin;

                    if (!in_array($freight_small_class_name, $called_method)) {
                        if (class_exists($freight_small_class_name)) {
                            $small_plugin_exist = 1;
                        }
                        $called_method[] = $freight_small_class_name;
                    }
                }
            }

            ($freight_enable == true || ($weight > 150 && $exceedWeight == 'yes')) ? $this->ValidShipmentsArr[] = "ltl_freight" : $this->ValidShipmentsArr[] = "no_shipment";
        }

//      Eniture debug mood
        do_action("eniture_debug_mood", "Product Detail (CORTIGO)", $cortigo_package);
        return $cortigo_package;
    }

    /**
     * Check hazmat item
     */
    function item_hazmet($product_id)
    {

        $enable_hazmet = get_post_meta($product_id, '_hazardousmaterials', true);

        if ($enable_hazmet == "yes") {
            $hazmet = 'Y';
        } else {
            $hazmet = 'N';
        }

        return $hazmet;
    }


    /**
     * Product dimensions from cart
     * @param $_product
     * @param $values
     * @param $freight_class_value
     * @param $freight_class
     * @return array
     */
    function en_cortigo_item_dimensions($_product, $values, $freight_class_value, $freight_class, $height, $width, $length, $product_weight)
    {
        $hazmatItemStatus = $this->item_hazmet($_product->get_id());
        $dimensions = array(
            'productId' => $_product->get_id(),
            'productName' => $_product->get_title(),
            'productQty' => $values['quantity'],
            'productPrice' => $_product->get_price(),
            'productWeight' => $product_weight,
            'productLength' => $length,
            'productWidth' => $width,
            'productHeight' => $height,
            'freightClass' => $freight_class,
            'productClass' => $freight_class_value,
            'isHazmatLineItem' => $hazmatItemStatus
        );
        return $dimensions;
    }

    /**
     * get locations list
     * @param $_product
     * @param $values
     * @param $freight_quotes
     * @param $freight_zipcode
     * @return string
     * @global $wpdb
     */
    function en_cortigo_get_origin($_product, $values, $freight_quotes, $freight_zipcode)
    {
        global $wpdb;

//      UPDATE QUERY In-store pick up                           
        $en_wd_update_query_string = apply_filters("en_wd_update_query_string", "");

        (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();

        $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
        if ($enable_dropship == 'yes') {
            $get_loc = get_post_meta($post_id, '_dropship_location', true);
            if ($get_loc == '') {
                return array('error' => 'SEFL LTL dp location not found!');
            }

//          Multi Dropship
            $multi_dropship = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'multi_dropship');

            if (is_array($multi_dropship)) {
                $locations_list = $wpdb->get_results(
                    "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship' LIMIT 1"
                );
            } else {
                $get_loc = ($get_loc !== '') ? maybe_unserialize($get_loc) : $get_loc;
                $get_loc = is_array($get_loc) ? implode(" ', '", $get_loc) : $get_loc;
                $locations_list = $wpdb->get_results(
                    "SELECT id, city, state, zip, country, location, nickname " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE id IN ('" . $get_loc . "')"
                );
            }

            $eniture_debug_name = "Dropships";
        } else {

//          Multi Warehouse
            $multi_warehouse = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'multi_warehouse');
            if (is_array($multi_warehouse)) {
                $locations_list = $wpdb->get_results(
                    "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse' LIMIT 1"
                );
            } else {
                $locations_list = $wpdb->get_results(
                    "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
                );
            }

            $eniture_debug_name = "Warehouses";

        }

        do_action("eniture_debug_mood", "Quotes $eniture_debug_name (CORTIGO)", $locations_list);

        return $locations_list;
    }

    /**
     * get freight class
     * @param $_product
     * @param $variation_id
     * @param $product_id
     * @return string
     */
    function en_cortigo_get_freight_class($_product, $variation_id, $product_id)
    {
        if ($_product->get_type() == 'variation') {
            $variation_class = get_post_meta($variation_id, '_ltl_freight_variation', true);

            if ($variation_class == 'get_parent' || $variation_class == 0) {
                $variation_class = get_post_meta($product_id, '_ltl_freight', true);
                $freight_class_ltl_gross = $variation_class;
            } else {
                if ($variation_class > 0) {
                    $freight_class_ltl_gross = get_post_meta($variation_id, '_ltl_freight_variation', true);
                } else {
                    $freight_class_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
                }
            }

        } else {
            $freight_class_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
        }
        return $freight_class_ltl_gross;
    }

    /**
     * Getting cortigo Enable/Disable
     * @param $_product
     * @return string
     */
    function en_cortigo_get_cortigo_enable($_product)
    {
        if ($_product->get_type() == 'variation') {
            $ship_class_id = $_product->get_shipping_class_id();
            if ($ship_class_id == 0) {
                $parent_data = $_product->get_parent_data();
                $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                $get_shipping_result = (isset($get_parent_term->slug)) ? $get_parent_term->slug : '';
            } else {
                $get_shipping_result = $_product->get_shipping_class();
            }

            $freight_enable = ($get_shipping_result && $get_shipping_result == 'ltl_freight') ? true : false;
        } else {
            $get_shipping_result = $_product->get_shipping_class();
            $freight_enable = ($get_shipping_result == 'ltl_freight') ? true : false;
        }
        return $freight_enable;
    }

    /**
     * freight parse handling fee
     * @param $handlng_fee
     * @param $cost
     * @return int
     */
    function en_cortigo_parse_handeling_fee($handlng_fee, $cost)
    {
        $pos = strpos($handlng_fee, '%');
        if ($pos > 0) {
            $exp = explode(substr($handlng_fee, $pos), $handlng_fee);
            $get = $exp[0];
            $percnt = $get / 100 * $cost;
            $grand_total = $cost + $percnt;
        } else {
            $grand_total = $cost + $handlng_fee;
        }
        return $grand_total;
    }
}

new En_Cortigo_Cart_To_Request();
