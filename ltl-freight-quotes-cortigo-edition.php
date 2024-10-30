<?php
/**
 * Plugin Name:  LTL Freight Quotes - Cortigo Edition
 * Plugin URI:   https://eniture.com/products/
 * Description:  Dynamically retrieves your negotiated shipping rates from cortigo and displays the results in the WooCommerce shopping cart.
 * Version:      2.1.0
 * Author:       Eniture Technology
 * Author URI:   http://eniture.com/
 * Text Domain:  eniture-technology
 * License:      GPL version 2 or later - http://www.eniture.com/
 * WC requires at least: 4.0.0
 * WC tested up to: 4.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CORTIGO_DOMAIN_HITTING_URL', 'https://ws048.eniture.com');
define('CORTIGO_MAIN_FILE', __FILE__);

/**
 * check plugin activattion
 */
if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Product Detail Check Features Enable || Disable
 *
 */
if (!function_exists('en_woo_plans_notification_PD')) {
    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($en = 1; $en <= 25; $en++) {
            $settings = get_option($eniture_plugins_id . $en);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name";
                    }

                }

            }

        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

add_action('admin_enqueue_scripts', 'en_cortigo_script');

/**
 * Load Front-end scripts for fedex
 */
function en_cortigo_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_cortigo_script', plugin_dir_url(__FILE__) . 'assets/js/en-cortigo.js', array(), '1.0.0');
    wp_localize_script('en_cortigo_script', 'en_cortigo_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
        'wc_settings_cortigo_rate_method' => get_option("wc_settings_cortigo_rate_method"),
    ));
}

/**
 * Product Store Type Old / New Messages
 *
 */
if (!function_exists('en_woo_plans_notification_message')) {
    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}
add_filter('plugin_action_links', 'en_cortigo_add_action_plugin', 10, 5);

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('getHost')) {
    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }
}

/**
 * Get Domain Name
 */
if (!function_exists('cortigo_freights_get_domain')) {
    function cortigo_freights_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return getHost($url);
    }
}

/**
 * plugin settings and support link at wp plugin.php page
 * @staticvar $plugin
 * @param $actions
 * @param $plugin_file
 * @return string
 */
function en_cortigo_add_action_plugin($actions, $plugin_file)
{
    static $plugin;
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {
        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=cortigo_freights">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://support.eniture.com/home/" target="_blank">Support</a>');
        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);
    }
    return $actions;
}

/**
 * Autoloads all classes file called.
 */

require_once plugin_dir_path(__FILE__) . 'includes/en-cortigo-autoloads.php';
require_once(__DIR__ . '/includes/en-cortigo-filter-quotes.php');
require_once(__DIR__ . '/includes/en-cortigo-compact.php');
require_once(__DIR__ . '/includes/en-cortigo-liftgate-as-option.php');
require_once(__DIR__ . '/includes/templates/en-cortigo-products-options.php');

require_once(plugin_dir_path(__FILE__) . 'includes/warehouse-dropship/wild-delivery.php');
require_once(plugin_dir_path(__FILE__) . 'includes/warehouse-dropship/get-distance-request.php');
require_once(plugin_dir_path(__FILE__) . 'includes/standard-package-addon/standard-package-addon.php');
require_once(__DIR__ . '/update-plan.php');

/**
 * LTL Freight Quotes - cortigo Edition Activation/Deactivation Hook
 */
register_activation_hook(__FILE__, array('En_Cortigo_Install_Uninstall', 'install'));
register_deactivation_hook(__FILE__, array('En_Cortigo_Install_Uninstall', 'uninstall'));
register_activation_hook(__FILE__, 'old_store_cortigo_ltl_hazmat_status');
register_activation_hook(__FILE__, 'old_store_cortigo_ltl_dropship_status');
register_activation_hook(__FILE__, 'cortigo_freights_activate_hit_to_update_plan');
register_deactivation_hook(__FILE__, 'cortigo_freights_deactivate_hit_to_update_plan');


/**
 * cortigo plugin update now
 * @param array type $upgrader_object
 * @param array type $options
 */
function en_cortigo_update_now()
{
    $index = 'ltl-freight-quotes-cortigo-edition/ltl-freight-quotes-cortigo-edition.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
    $update_now = get_option('en_cortigo_update_now');

    if ($update_now != $plugin_version) {
        if (!function_exists('cortigo_freights_activate_hit_to_update_plan')) {
            require_once(__DIR__ . '/update-plan.php');
        }

        old_store_cortigo_ltl_hazmat_status();
        old_store_cortigo_ltl_dropship_status();
        cortigo_freights_activate_hit_to_update_plan();

        update_option('en_cortigo_update_now', $plugin_version);
    }
}

add_action('init', 'en_cortigo_update_now');


define("en_woo_plugin_cortigo_freights", "cortigo_freights");

add_action('wp_enqueue_scripts', 'en_ltl_cortigo_frontend_checkout_script');
/**
 * Load Frontend scripts for ODFL
 */
function en_ltl_cortigo_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_ltl_cortigo_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-cortigo-checkout.js', array(), '1.0.0');
    wp_localize_script('en_ltl_cortigo_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

/**
 * Plans Common Hooks
 */
add_filter('cortigo_freights_quotes_plans_suscription_and_features', 'cortigo_freights_quotes_plans_suscription_and_features', 1);

function cortigo_freights_quotes_plans_suscription_and_features($feature)
{
    $package = get_option('cortigo_freights_packages_quotes_package');

    $features = array
    (
        'instore_pickup_local_devlivery' => array('3'),
    );

    if (get_option('cortigo_freights_store_type') == "1") {
        $features['multi_warehouse'] = array('2', '3');
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
        $features['hazardous_material'] = array('2', '3');
    }

    if (get_option('en_old_user_dropship_status') == "0" && get_option('cortigo_freights_store_type') == "0") {
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
    }
    if (get_option('en_old_user_warehouse_status') == "0" && get_option('cortigo_freights_store_type') == "0") {
        $features['multi_warehouse'] = array('2', '3');
    }
    if (get_option('en_old_user_hazmat_status') == "1" && get_option('cortigo_freights_store_type') == "0") {
        $features['hazardous_material'] = array('2', '3');
    }

    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
}

add_filter('cortigo_freights_plans_notification_link', 'cortigo_freights_plans_notification_link', 1);

function cortigo_freights_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan) {
        case 2:
            $plan_to_upgrade = "<a target='_blank' href='http://eniture.com/plan/woocommerce-cortigo-ltl-freight/'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a target='_blank' href='http://eniture.com/plan/woocommerce-cortigo-ltl-freight/'>Advanced Plan required</a>";
            break;
    }

    return $plan_to_upgrade;
}

/**
 *
 * old customer check dropship / warehouse status on plugin update
 */
function old_store_cortigo_ltl_dropship_status()
{
    global $wpdb;

//  Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);

    if (get_option('en_old_user_dropship_status') == "0" && get_option('cortigo_freights_store_type') == "0") {

        $dropship_status = ($num > 1) ? 1 : 0;

        update_option('en_old_user_dropship_status', "$dropship_status");
    } elseif (get_option('en_old_user_dropship_status') == "" && get_option('cortigo_freights_store_type') == "0") {
        $dropship_status = ($num == 1) ? 0 : 1;

        update_option('en_old_user_dropship_status', "$dropship_status");
    }

//  Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);

    if (get_option('en_old_user_warehouse_status') == "0" && get_option('cortigo_freights_store_type') == "0") {

        $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('cortigo_freights_store_type') == "0") {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    }
}

/**
 *
 * old customer check hazmat status on plugin update
 */
function old_store_cortigo_ltl_hazmat_status()
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_hazardousmaterials%' AND meta_value = 'yes'
            "
    );

    if (get_option('en_old_user_hazmat_status') == "0" && get_option('cortigo_freights_store_type') == "0") {
        $hazmat_status = (count($results) > 0) ? 0 : 1;
        update_option('en_old_user_hazmat_status', "$hazmat_status");
    } elseif (get_option('en_old_user_hazmat_status') == "" && get_option('cortigo_freights_store_type') == "0") {
        $hazmat_status = (count($results) == 0) ? 1 : 0;

        update_option('en_old_user_hazmat_status', "$hazmat_status");
    }

}
