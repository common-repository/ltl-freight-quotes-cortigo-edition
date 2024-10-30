<?php
/**
 * Admin Settings | all admin settings defined
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Settings | all admin settings defined for plugin usage
 */
class En_Cortigo_Admin_Settings
{

    /**
     * admin settings constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'en_cortigo_save_carrier_status'));
        add_action('admin_enqueue_scripts', array($this, 'en_cortigo_admin_validation_styles_scripts'));
        add_filter('woocommerce_package_rates', array($this, 'en_cortigo_hide_shipping_based_on_class'));
        if (!function_exists('en_cortigo_create_ltl_class')) {
            $this->en_cortigo_create_ltl_class();
        }
        add_filter('woocommerce_no_shipping_available_html', array($this, 'en_cortigo_cortigo_shipping_message'));
        add_filter('woocommerce_cart_no_shipping_available_html', array($this, 'en_cortigo_cortigo_shipping_message'));
    }

    /**
     * Load CSS And JS Scripts
     */
    public function en_cortigo_admin_validation_styles_scripts()
    {
        wp_enqueue_script('cortigo_custom_script', plugin_dir_url(dirname(__FILE__)) . 'assets/js/en-cortigo-warehouse-dropship.js', array(), '1.5', true);
        wp_localize_script('cortigo_custom_script', 'script', array('pluginsUrl' => plugins_url(),));
        wp_register_style('cortigo_custom_style', plugin_dir_url(dirname(__FILE__)) . 'assets/css/en-cortigo-custom-style.css', array(), '1.0.6', 'screen');
        wp_enqueue_style('cortigo_custom_style');
    }

    /**
     * Save Freight Carriers
     * @param $post_id
     */
    public function en_cortigo_save_carrier_status($post_id)
    {
        global $wpdb;
        $postData = $_POST;
        $carriers_table = $wpdb->prefix . "carriers";
        $actionStatus = (isset($postData['action'])) ? sanitize_text_field($postData['action']) : "";
        if (isset($actionStatus) && $actionStatus == 'en_cortigo_save_carrier_status') {
            global $wpdb;
            $ltl_carriers = $wpdb->get_results("SELECT `id`, `carrier_scac`, `carrier_name`, `carrier_status`,`plugin_name` FROM " . $carriers_table . " WHERE `plugin_name` = 'cortigo_ltl' ORDER BY `carrier_name` ASC");
            foreach ($ltl_carriers as $carriers_value):
                $carrier_scac = (isset($postData[$carriers_value->carrier_scac . $carriers_value->id])) ? sanitize_text_field($postData[$carriers_value->carrier_scac . $carriers_value->id]) : "";
                $liftgate_fee = (isset($postData[$carriers_value->carrier_scac . $carriers_value->id . "liftgate_fee"])) ? sanitize_text_field($postData[$carriers_value->carrier_scac . $carriers_value->id . "liftgate_fee"]) : "";

                if (isset($carrier_scac) && $carrier_scac == 'on') {
                    $wpdb->query($wpdb->prepare("UPDATE " . $carriers_table . " SET `carrier_status` = '%s' , `liftgate_fee` = '$liftgate_fee' WHERE `carrier_scac` = '$carriers_value->carrier_scac' AND `plugin_name` Like 'cortigo_ltl'", '1'));
                } else {

                    $wpdb->query($wpdb->prepare("UPDATE " . $carriers_table . " SET `carrier_status` = '%s' , `liftgate_fee` = '$liftgate_fee' WHERE `carrier_scac` = '$carriers_value->carrier_scac' AND `plugin_name` Like 'cortigo_ltl'", '0'));
                }
            endforeach;
        }
    }

    /**
     * Hide Shipping Methods If Not From Eniture
     * @param $available_methods
     */
    function en_cortigo_hide_shipping_based_on_class($available_methods)
    {
        $forceShowMethods = apply_filters('force_show_methods', array());

        if (get_option('wc_settings_cortigo_allow_other_plugins') == 'no' && (!empty($forceShowMethods)) && (!in_array("valid_third_party", $forceShowMethods))) {
            if (count($available_methods) > 0) {
                $plugins_array = array();
                $eniture_plugins = get_option('EN_Plugins');
                if ($eniture_plugins) {
                    $plugins_array = json_decode($eniture_plugins);
                }
                foreach ($available_methods as $index => $method) {
                    if (!($method->method_id == 'speedship' || $method->method_id == 'en_cortigo_shipping_method' || in_array($method->method_id, $plugins_array))) {
                        unset($available_methods[$index]);
                    }
                }
            }
        }
        return $available_methods;
    }

    /**
     * getting handling fee
     */
    public function en_cortigo_get_handling_fee()
    {
        return $handling_fee = get_option('wc_settings_cortigo_hand_free_mark_up');
    }

    /**
     * check status for other plugins
     */
    public function en_cortigo_other_plugins_status()
    {
        return $other_plugin_status = get_option('wc_settings_cortigo_allow_other_plugins');
    }

    /**
     * create LTL class function
     */
    function en_cortigo_create_ltl_class()
    {
        wp_insert_term('LTL Freight', 'product_shipping_class', array(
                'description' => 'The plugin is triggered to provide LTL freight quote when the shopping cart contains an item that has a designated shipping class. Shipping class? is a standard WooCommerce parameter not to be confused with freight class? or the NMFC classification system.',
                'slug' => 'ltl_freight'
            )
        );
    }

    /**
     * No Shipping Available Message
     * @param $message
     * @return string
     */
    function en_cortigo_cortigo_shipping_message($message)
    {
        return __('There are no carriers available for this shipment please contact with store owner');
    }
}