<?php

/**
 * Cortigo LTL Plan
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_cortigo_freights_activate_hit_to_update_plan', 'cortigo_freights_activate_hit_to_update_plan');
add_action('wp_ajax_nopriv_cortigo_freights_activate_hit_to_update_plan', 'cortigo_freights_activate_hit_to_update_plan');

/**
 * Activate Cortigo LTL
 */
function cortigo_freights_activate_hit_to_update_plan()
{
    $domain = cortigo_freights_get_domain();

    $index = 'ltl-freight-quotes-cortigo-edition/ltl-freight-quotes-cortigo-edition.php';
    $plugin_info = get_plugins();
    $plugin_version = $plugin_info[$index]['Version'];

    $plugin_dir_url = plugin_dir_url(__FILE__) . 'en-hit-to-update-plan.php';
    $post_data = array(
        'platform' => 'wordpress',
        'carrier' => '48',
        'store_url' => $domain,
        'webhook_url' => $plugin_dir_url,
        'plugin_version' => $plugin_version,
    );
    $url = CORTIGO_DOMAIN_HITTING_URL . "/web-hooks/subscription-plans/create-plugin-webhook.php?";
    $response = wp_remote_get($url,
        array(
            'method' => 'GET',
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'body' => $post_data,
        )
    );
    $output = wp_remote_retrieve_body($response);
    $response = json_decode($output, TRUE);

    $plan = isset($response['pakg_group']) ? $response['pakg_group'] : '';
    $expire_day = isset($response['pakg_duration']) ? $response['pakg_duration'] : '';
    $expiry_date = isset($response['expiry_date']) ? $response['expiry_date'] : '';
    $plan_type = isset($response['plan_type']) ? $response['plan_type'] : '';

    if (isset($response['pakg_price']) && $response['pakg_price'] == '0') {
        $plan = '0';
    }

    update_option('cortigo_freights_packages_expire_days', "$expire_day");
    update_option('cortigo_freights_packages_expire_date', "$expiry_date");
    update_option('cortigo_freights_packages_quotes_package', "$plan");
    update_option('cortigo_freights_store_type', "$plan_type");

    en_check_cortigo_freight_plan_on_product_detail();

}

/**
 * Product Detail Features
 */
function en_check_cortigo_freight_plan_on_product_detail()
{

    $hazardous_feature_PD = 1;
    $dropship_feature_PD = 1;

//  Hazardous Material
    if (get_option('cortigo_freights_store_type') == "1") {
        $hazardous_material = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'hazardous_material');
        if (!is_array($hazardous_material)) {
            $hazardous_feature_PD = 1;
        } else {
            $hazardous_feature_PD = 0;
        }
    }

//  Dropship
    if (get_option('cortigo_freights_store_type') == "1") {
        $action_dropship = apply_filters('cortigo_freights_quotes_plans_suscription_and_features', 'multi_dropship');
        if (!is_array($action_dropship)) {
            $dropship_feature_PD = 1;
        } else {
            $dropship_feature_PD = 0;
        }
    }

    if (get_option('en_old_user_hazmat_status') == "1" && get_option('cortigo_freights_store_type') == "0") {
        $hazardous_feature_PD = 0;
    }

    update_option('eniture_plugin_17', array('cortigo_freights_packages_quotes_package' => array('plugin_name' => 'LTL Freight Quotes - Cortigo Edition', 'multi_dropship' => $dropship_feature_PD, 'hazardous_material' => $hazardous_feature_PD)));
}

/**
 * Deactivate Cortigo LTL
 */
function cortigo_freights_deactivate_hit_to_update_plan()
{
    delete_option('eniture_plugin_17');
    delete_option('cortigo_freights_packages_quotes_package');
    delete_option('cortigo_freights_packages_expire_days');
    delete_option('cortigo_freights_packages_expire_date');
    delete_option('cortigo_freights_store_type');
}

/**
 * Get Cortigo LTL Plan
 * @return string
 */
function cortigo_freights_plan_name()
{

    $plan = get_option('cortigo_freights_packages_quotes_package');
    $expire_days = get_option('cortigo_freights_packages_expire_days');
    $expiry_date = get_option('cortigo_freights_packages_expire_date');
    $plan_name = "";

    switch ($plan) {
        case 3:
            $plan_name = "Advanced Plan";
            break;
        case 2:
            $plan_name = "Standard Plan";
            break;
        case 1:
            $plan_name = "Basic Plan";
            break;
        default:
            $plan_name = "Trial Plan";
    }

    $package_array = array(
        'plan_number' => $plan,
        'plan_name' => $plan_name,
        'expire_days' => $expire_days,
        'expiry_date' => $expiry_date
    );
    return $package_array;
}

/**
 * Show YRC LTL Plan Notice
 * @return string
 */
function cortigo_freights_plan_notice()
{

    if (isset($_GET['tab']) && ($_GET['tab'] == "cortigo_freights")) {
        $plan_number = get_option('cortigo_freights_packages_quotes_package');
        $store_type = get_option('cortigo_freights_store_type');

        $plan_package = cortigo_freights_plan_name();

        if ($store_type == "1" || $store_type == "0" && ($plan_number == "0" || $plan_number == "1" || $plan_number == "2" || $plan_number == "3")) {

            $click_here_to_update_plan = ' <a href="javascript:void(0)" data-action="cortigo_freights_activate_hit_to_update_plan" onclick="en_update_plan(this);">Click here</a> to refresh the plan';

            if ($plan_package['plan_number'] == '0') {
                echo '<div class="notice notice-success is-dismissible">
                         <p> You are currently on the ' . $plan_package['plan_name'] . '. Your plan will be expire within ' . $plan_package['expire_days'] . ' days and plan renews on ' . $plan_package['expiry_date'] . $click_here_to_update_plan . '.</p>
                     </div>';
            } else if ($plan_package['plan_number'] == '1' || $plan_package['plan_number'] == '2' || $plan_package['plan_number'] == '3') {

                echo '<div class="notice notice-success is-dismissible">
                        <p>You are currently on the ' . $plan_package['plan_name'] . '. The plan renews on ' . $plan_package['expiry_date'] . $click_here_to_update_plan . '</p>
                    </div>';
            } else {
                echo '<div class="notice notice-warning is-dismissible">
                        <p>Your currently plan subscription is inactive. ' . $click_here_to_update_plan . ' to check the subscription status. If the subscription status remains inactive, log into eniture.com and update your license.</p>
                    </div>';
            }
        }
    }
}

add_action('admin_notices', 'cortigo_freights_plan_notice');
