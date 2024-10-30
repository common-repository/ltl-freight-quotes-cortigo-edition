<?php
/**
 * cortigo Carrier List Class
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
       
/**
 * cortigo Carrier List Class | all carriers display into a table in quote settings 
 */

class En_Cortigo_Carriers
{
    /**
     * Carrier List Page
     */
    function __construct() {
        error_reporting(0);
    }

    /**
     * carriers table to show the carriers
     * @global $wpdb
     * @return string
     */
    function en_cortigo_carrier_list_tab()
    {
        ?>
        <div class="cort_carrier_section_class wrap woocommerce">
            <p>
                Identifies which carriers are included in the quote response, not what is displayed in the shopping cart. Identify what displays in the shopping cart in the Quote Settings. For example, you may include quote responses from all carriers, but elect to only show the cheapest three in the shopping cart. <br> <br> 
                Not all carriers service all origin and destination points. If a carrier doesn`t service the ship to address, it is automatically omitted from the quote response. Consider conferring with your cortigo Edition if you`d like to narrow the number of carrier responses. <br> <br>
            </p>
            <table class="table table-bordered">
                <thead>
                    <tr class="even_odd_class">
                        <th>Sr#</th>
                        <th>Carrier Name</th>
                        <th>Logo</th>
                        <th>Liftgate Fee</th>
                        <th><input type="checkbox" name="include_all" class="include_all" /></th>
                    </tr>
                </thead> 
                <tbody>
                <?php    
                    global $wpdb;
                    $count_carrier = 1;
                    $carriers_table = $wpdb->prefix . "carriers";
                    $cortigo_cerrier_all = $wpdb->get_results("SELECT `id`, `carrier_scac`, `carrier_name`, `carrier_logo`, `carrier_status`, `liftgate_fee` FROM ".$carriers_table." WHERE `plugin_name`='cortigo_ltl' GROUP BY `carrier_scac` ORDER BY `id` ASC");
                    foreach ($cortigo_cerrier_all as $cortigo_cerrier):
                        ?>
                        <tr <?php
                        if ($count_carrier % 2 == 0) {

                            echo 'class="even_odd_class"';
                        }
                        ?> >
                            <td>
                                <?php echo $count_carrier; ?>
                            </td>

                            <td>
                                <?php echo $cortigo_cerrier->carrier_name; ?>
                            </td>
                            <td>
                                <img  src="<?php echo plugin_dir_url(dirname(__FILE__)).'../assets/carrier-logos/' . $cortigo_cerrier->carrier_logo ?> " >
                            </td>
                            
                            <td>
                                <input name="<?php echo $cortigo_cerrier->carrier_scac . $cortigo_cerrier->id . "liftgate_fee"; ?>" class="liftgate_fee" id="<?php echo $cortigo_cerrier->carrier_scac . $cortigo_cerrier->id . "liftgate_fee"; ?>" value="<?php echo (isset($cortigo_cerrier->liftgate_fee) && (strlen($cortigo_cerrier->liftgate_fee) > 0)) ? $cortigo_cerrier->liftgate_fee : '' ?>" type="text" >
                            </td>
                            
                            <td>
                                <input <?php
                                if ($cortigo_cerrier->carrier_status == '1') {
                                    echo 'checked="checked"';
                                }
                                ?>
                                    name="<?php echo $cortigo_cerrier->carrier_scac . $cortigo_cerrier->id; ?>" class="carrier_check" id="<?php echo $cortigo_cerrier->carrier_scac . $cortigo_cerrier->id; ?>" type="checkbox" >
                            </td>
                        </tr>
                        <?php
                        $count_carrier ++;
                    endforeach;
                    ?>                  
                    <input name="action" value="en_cortigo_save_carrier_status"  type="hidden" />
                </tbody>
            </table>
        </div>
    <?php
    }

}