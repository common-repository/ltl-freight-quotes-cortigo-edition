<?php
/**
 * WWE LTL Distance Get
 *
 * @package     Cortigo LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Get_cortigo_freights_distance
 */
class Get_cortigo_freights_distance
{
    /**
     * Get Distance Function
     * @param $map_address
     * @param $accessLevel
     * @return json
     */
    function cortigo_freights_get_distance($map_address, $accessLevel, $destinationZip = array())
    {

        $domain = cortigo_freights_get_domain();
        $post = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('wc_settings_cortigo_licence_key'),
            'ServerName' => $domain,
        );

        if (is_array($post) && count($post) > 0) {
            $cortigo_curl_obj = new En_Cortigo_Wc_Curl_Class();
            $output = $cortigo_curl_obj->en_cortigo_get_curl_response(CORTIGO_DOMAIN_HITTING_URL . '/addon/google-location.php', $post);
            return $output;
        }
    }
}
