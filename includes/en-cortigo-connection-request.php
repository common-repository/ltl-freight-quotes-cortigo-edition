<?php
/**
 * Connection Request Class | getting connection
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Connection Request Class | getting connection with server
 */
class En_Cortigo_Connection_Request
{
    /**
     * cortigo connection request class constructor
     */
    function __construct()
    {
        add_action('wp_ajax_nopriv_cort_test_connection_call', array($this, 'en_cortigo_cortigo_test_connection'));
        add_action('wp_ajax_cort_test_connection_call', array($this, 'en_cortigo_cortigo_test_connection'));
    }

    /**
     * cortigo test connection function
     * @param none
     * @return array
     */
    function en_cortigo_cortigo_test_connection()
    {
        if (isset($_POST)) {

            foreach ($_POST as $key => $post) {
                $data[$key] = sanitize_text_field($post);
            }

            $shippingID = $data['wc_cortigo_shipper_id'];
            $username = $data['wc_cortigo_username'];
            $password = $data['wc_cortigo_password'];
            $accessKey = $data['authentication_key'];
            $license_key = $data['wc_cortigo_licence_key'];
            $domain = cortigo_freights_get_domain();

            $data = array(
                'license_key' => $license_key,
                'server_name' => $domain,
                'carrierName' => 'cortigo',
                'platform' => 'WordPress',
                'carrier_mode' => 'test',
                //Carrier Credentials
                'shipperID' => $shippingID,
                'username' => $username,
                'password' => $password,
                'accessKey' => $accessKey,
            );
        }

        $En_Cortigo_Wc_Curl_Class = new En_Cortigo_Wc_Curl_Class();
        $finalResponse = $En_Cortigo_Wc_Curl_Class->en_cortigo_get_curl_response(CORTIGO_DOMAIN_HITTING_URL . "/index.php", $data);
        $output_decoded = json_decode($finalResponse);
        if (empty($output_decoded)) {
            $re['error'] = 'We are unable to test connection. Please try again later.';
        }
        if (isset($output_decoded->severity) && $output_decoded->severity == 'SUCCESS') {

            $re['success'] = $output_decoded->Message;
        } else if (isset($output_decoded->severity) && $output_decoded->severity == 'ERROR') {
            $re['error'] = $output_decoded->Message;
        } else {
            $re['error'] = $output_decoded->error;
        }
        echo json_encode($re);
        exit;
    }
}
