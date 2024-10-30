<?php

/**
 * Woocommerce cortigo Curl Class
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Curl Request Class | getting curl response
 */
class En_Cortigo_Wc_Curl_Class {

    /**
     * Get Curl Response 
     * @param $url
     * @param $post_data
     * @return json/array
     */
    
    function en_cortigo_get_curl_response($url, $postData) 
    {
        if ( !empty( $url ) && !empty( $postData ) )
        {
            $field_string = http_build_query($postData);

//          Eniture debug mood
            do_action("eniture_debug_mood" , "Build Query (CORTIGO)" , $field_string);
            
            $response = wp_remote_post($url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );

            $output = wp_remote_retrieve_body($response);

            return $output;
        }    
    }

}
