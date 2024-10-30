<?php
/**
 * cortigo Carriers List Class | saving carrier list to database when plugin called for activation
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Carriers List Class | saving carriers list to database when plugin called for activation
 */
class En_Cortigo_Carrier_List
{
    /**
     * carrier list class constructor
     */
    function __construct()
    {
        $this->en_cortigo_carriers();
    }

    /**
     * carriers names, code, and logo
     * @global $wpdb
     */
    function en_cortigo_carriers()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $table_name = $wpdb->prefix . "carriers";
        $installed_carriers = $wpdb->get_results("SELECT COUNT(*) AS carrier FROM " . $table_name . " WHERE `plugin_name` = 'cortigo_ltl'");
        if ($installed_carriers[0]->carrier < 1) {
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'AACT',
                'carrier_name' => 'AAA Cooper Transportation',
                'carrier_logo' => 'aact.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'PYLE',
                'carrier_name' => 'A. Duie Pyle, Inc.',
                'carrier_logo' => 'pyle.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'CENF',
                'carrier_name' => 'Central Freight Lines, Inc',
                'carrier_logo' => 'cenf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'CHRW-DEMO',
                'carrier_name' => 'C.H. Robinson Worldwide - TRUCKLOAD',
                'carrier_logo' => 'chrobinson.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'CMMS-DEMO',
                'carrier_name' => 'Command Transportation LLC - TRUCKLOAD',
                'carrier_logo' => 'commandllc.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'DAFG',
                'carrier_name' => 'Dayton Freight Lines',
                'carrier_logo' => 'dafg.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'DHRN',
                'carrier_name' => 'Dohrn Transfer Company',
                'carrier_logo' => 'dhrn.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'EXLA',
                'carrier_name' => 'Estes Express Lines',
                'carrier_logo' => 'exla.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'FXNL',
                'carrier_name' => 'FedExFreightEconomy',
                'carrier_logo' => 'fedex.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'HJBT-DEMO',
                'carrier_name' => 'J.B. Hunt - TRUCKLOAD',
                'carrier_logo' => 'jbh.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'KNIT-DEMO',
                'carrier_name' => 'Knight Transportation - TRUCKLOAD',
                'carrier_logo' => 'knight.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'LKVL',
                'carrier_name' => 'Lakeville Motor Express Inc',
                'carrier_logo' => 'lkvl.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'MIDW',
                'carrier_name' => 'Midwest Motor Express',
                'carrier_logo' => 'midw.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'NEMF',
                'carrier_name' => 'New England Motor Freight',
                'carrier_logo' => 'nemf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'NEMF-CN',
                'carrier_name' => 'New England Motor Freight-Canada',
                'carrier_logo' => 'nemf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'NMTF',
                'carrier_name' => 'N & M Transfer Co. Inc.',
                'carrier_logo' => 'nopk.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'NPME',
                'carrier_name' => 'New Penn Motor Express',
                'carrier_logo' => 'nm.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'PITD',
                'carrier_name' => 'Pitt Ohio Express, LLC',
                'carrier_logo' => 'pitd.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'RDFS',
                'carrier_name' => 'Roadrunner Transportation Services',
                'carrier_logo' => 'rdfs.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'RETL',
                'carrier_name' => 'USF Reddaway',
                'carrier_logo' => 'retl.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'RLCA',
                'carrier_name' => 'R&L Carriers Inc',
                'carrier_logo' => 'rlca.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'SAIA',
                'carrier_name' => 'SAIA',
                'carrier_logo' => 'saia.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'SEFL',
                'carrier_name' => 'Southeastern Freight Lines',
                'carrier_logo' => 'sefl.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'UPGF',
                'carrier_name' => 'UPS Freight',
                'carrier_logo' => 'upgf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'UPGF-CN',
                'carrier_name' => 'UPS Freight-Canada',
                'carrier_logo' => 'upgf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'UPGF-CAUS',
                'carrier_name' => 'UPS Freight-Canada to US',
                'carrier_logo' => 'upgf.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'HMES',
                'carrier_name' => 'USF Holland LLC',
                'carrier_logo' => 'hmes.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'WTVA',
                'carrier_name' => 'Wilson Trucking Corporation',
                'carrier_logo' => 'wtva.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'RDWY',
                'carrier_name' => 'YRC Freight',
                'carrier_logo' => 'rdwy.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'RDWY-CN',
                'carrier_name' => 'YRC Freight - Canada',
                'carrier_logo' => 'rdwy.png',
                'plugin_name' => 'cortigo_ltl'
            ));
            $wpdb->insert(
                $table_name, array(
                'carrier_scac' => 'YRCA',
                'carrier_name' => 'YRC Freight Accelerated',
                'carrier_logo' => 'rdwy.png',
                'plugin_name' => 'cortigo_ltl'
            ));
        }
    }
}