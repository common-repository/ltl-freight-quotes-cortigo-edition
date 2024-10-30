<?php
/**
 * LTL Freight Plugin Installation |  cortigo Edition
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * LTL Freight Plugin Installation |  cortigo Edition
 */
class En_Cortigo_Install_Uninstall
{

    /**
     * Constructor
     */
    public function __constructor()
    {

    }

    /**
     * Plugin installation script
     */
    public static function install()
    {
        global $wpdb;
        add_option('wc_cortigo_edition', '1.0', '', 'yes');
        add_option('wc_cortigo_db_version', '1.0');
        $eniture_plugins = get_option('EN_Plugins');
        if (!$eniture_plugins) {
            add_option('EN_Plugins', json_encode(array('cortigo')));
        } else {
            $plugins_array = json_decode($eniture_plugins);
            if (!in_array('cortigo', $plugins_array)) {
                array_push($plugins_array, 'cortigo');
                update_option('EN_Plugins', json_encode($plugins_array));
            }
        }
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        //carriers table
        $carriers_table = $wpdb->prefix . "carriers";
        if ($wpdb->query("SHOW TABLES LIKE '" . $carriers_table . "'") === 0) {
            $sql = "CREATE TABLE $carriers_table (
            id int(10) NOT NULL AUTO_INCREMENT,
            carrier_scac varchar(600) NOT NULL,
            carrier_name varchar(600) NOT NULL,
            carrier_logo varchar(255) NOT NULL,
            carrier_status varchar(8) NOT NULL,
            plugin_name varchar(100) NOT NULL,
            liftgate_fee varchar(255) NOT NULL,
            PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            dbDelta($sql);
        }

        //Alter Table
        $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM " . $carriers_table . " LIKE 'liftgate_fee'");
        if (!(isset($myCustomer->Field) && $myCustomer->Field == 'liftgate_fee')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN liftgate_fee VARCHAR(255) NOT NULL", $carriers_table));
        }

        //warehouse table
        $warehouse_table = $wpdb->prefix . "warehouse";
        if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
            $origin = 'CREATE TABLE IF NOT EXISTS ' . $warehouse_table . '(
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        city varchar(200) NOT NULL,
                        state varchar(200) NOT NULL,
                        zip varchar(200) NOT NULL,
                        country varchar(200) NOT NULL,
                        location varchar(200) NOT NULL,
                        nickname varchar(200) NOT NULL,
                        enable_store_pickup VARCHAR(255) NOT NULL,
                        miles_store_pickup VARCHAR(255) NOT NULL ,
                        match_postal_store_pickup VARCHAR(255) NOT NULL ,
                        checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
                        enable_local_delivery VARCHAR(255) NOT NULL ,
                        miles_local_delivery VARCHAR(255) NOT NULL ,
                        match_postal_local_delivery VARCHAR(255) NOT NULL ,
                        checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
                        fee_local_delivery VARCHAR(255) NOT NULL ,
                        suppress_local_delivery VARCHAR(255) NOT NULL,                 
                        PRIMARY KEY  (id) )';
            dbDelta($origin);
        }

        $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
        if (!(isset($myCustomer->Field) && $myCustomer->Field == 'enable_store_pickup')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));

        }
        include_once plugin_dir_path(__FILE__) . 'carriers/en-cortigo-carrier-list.php';
        new En_Cortigo_Carrier_List();
    }

    /**
     * Plugin un-installation script
     */
    public static function uninstall()
    {
        //code for uninstallation
        global $wpdb;
        $option_name = 'wc_cortigo_edition';
        delete_option($option_name);
        // for site options in Multisite
        delete_site_option($option_name);
        // delete carriers of this plugin
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "carriers WHERE plugin_name LIKE 'cortigo_ltl';");
        // Delete options.
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'wc_cortigo\_%';");
        // Clear any cached data that has been removed
        wp_cache_flush();
    }
}
