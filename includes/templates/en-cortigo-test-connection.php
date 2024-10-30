<?php
/**
 * WC Cortigo Connection Settings Tab Class
 * @package     Woocommerce Cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
       
/**
 * Cortigo Test Connection Settings Form Class
 */
class En_Cortigo_Test_Connection
{
    /**
     * test connection setting form
     * @return array
     */
    function en_cortigo_connection_setting_tab()
    {
        $settings = array(
            'section_title_wc_cortigo' => array(
                'name' => __('', 'wc-settings-cortigo_quotes'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'wc_settings_cortigo_title_section_connection'
            ),

            'wc_cortigo_shipper_id' => array(
                'name' => __('Shipper ID ', 'wc-settings-cortigo_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_shipper_id',
                'placeholder' => 'Shipper ID'
            ),

            'wc_cortigo_username' => array(
                'name' => __('Username ', 'wc-settings-cortigo_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_username',
                'placeholder' => 'Username'
            ),

            'wc_cortigo_password' => array(
                'name' => __('Password ', 'wc-settings-cortigo_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_password',
                'placeholder' => 'Password'
            ),

            'wc_cortigo_authentication_key' => array(
                'name' => __('Authentication Key ', 'wc-settings-cortigo_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_authentication_key',
                'placeholder' => 'Authentication Key'
            ),

            'wc_cortigo_plugin_licence_key' => array(
                'name' => __('Plugin License Key ', 'wc-settings-cortigo_quotes'),
                'type' => 'text',
                'desc' => __('Obtain a License Key from <a href="https://eniture.com/products/" target="_blank" >eniture.com </a>', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_licence_key',
                'placeholder' => 'Plugin License Key'
            ),

            'wc_cortigo_save_buuton' => array(
                'name' => __('Save Button ', 'wc-settings-cortigo_quotes'),
                'type' => 'button',
                'desc' => __('', 'wc-settings-cortigo_quotes'),
                'id' => 'wc_settings_cortigo_button'
            ),

            'wc_cortigo_section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_cortigo_end-section_connection'
            ),
        );
        return $settings;
    }

}

