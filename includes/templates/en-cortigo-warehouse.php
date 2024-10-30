<?php
/**
 * Warehouse display page in which adding, modifying and deleting
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$warehous_list = $wpdb->get_results(
    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE `location` = 'warehouse'"
);

?>
<div class="ltl_setting_section">
    <h1>Warehouses</h1><br>
    <a href="#ltl_add_warehouse_btn" title="Add Warehouse" class="ltl_add_warehouse_btn hide_val" name="avc">Add</a>
    <br>
    <div class="warehouse_text">
        <p>Warehouses that inventory all products not otherwise identified as drop shipped items. The warehouse with the
            lowest shipping cost to the destination is used for quoting purposes.</p>
    </div>
    <div id="message" class="updated inline warehouse_deleted">
        <p><strong>Success! Warehouse deleted successfully.</strong></p>
    </div>
    <div id="message" class="updated inline warehouse_created">
        <p><strong>Success! New warehouse added successfully.</strong></p>
    </div>
    <div id="message" class="updated inline warehouse_updated">
        <p><strong>Success! Warehouse updated successfully.</strong></p>
    </div>
    <table class="ltl_warehouse_list" id="append_warehouse">
        <thead>
        <tr>
            <th class="ltl_warehouse_list_heading">City</th>
            <th class="ltl_warehouse_list_heading">State</th>
            <th class="ltl_warehouse_list_heading">Zip</th>
            <th class="ltl_warehouse_list_heading">Country</th>
            <th class="ltl_warehouse_list_heading">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($warehous_list) > 0) {

            foreach ($warehous_list as $list) {
                ?>
                <tr id="row_<?php echo $list->id; ?>" data-id="<?php echo $list->id; ?>">
                    <td class="ltl_warehouse_list_data"><?php echo $list->city; ?></td>
                    <td class="ltl_warehouse_list_data"><?php echo $list->state; ?></td>
                    <td class="ltl_warehouse_list_data"><?php echo $list->zip; ?></td>
                    <td class="ltl_warehouse_list_data"><?php echo $list->country; ?></td>
                    <td class="ltl_warehouse_list_data">
                        <a href="javascript(0)"
                           onclick="return en_cortigo_edit_ltl_warehouse(<?php echo $list->id; ?>);"><img
                                    src="<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"
                                    title="Edit"></a>
                        <a href="javascript(0)"
                           onclick="return en_cortigo_delete_ltl_current_warehouse(<?php echo $list->id; ?>);"><img
                                    src="<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"
                                    title="Delete"></a></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr class="new_warehouse_add" data-id=0></tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Add Popup for new warehouse -->
    <div id="ltl_add_warehouse_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup">
            <h2 class="warehouse_heading">Warehouse</h2>
            <a class="close" href="#">&times;</a>
            <div class="content">
                <div class="already_exist">
                    <strong>Error!</strong> Zip code already exists.
                </div>
                <div class="not_allowed">
                    <p><strong>Error!</strong> Please enter US zip code.</p>
                </div>
                <div class="wrng_credential">
                    <p><strong>Error!</strong> Please verify credentials at connection settings panel.</p>
                </div>
                <form method="post">
                    <input type="hidden" name="edit_form_id" value="" id="edit_form_id">
                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_zip">Zip</label>
                        <input type="text" title="Zip" value="" name="ltl_origin_zip" placeholder="30214"
                               id="ltl_origin_zip">
                    </div>

                    <div class="ltl_add_warehouse_input city_input">
                        <label for="ltl_origin_city">City</label>
                        <input type="text" class="alphaonly" title="City" value="" name="ltl_origin_city"
                               placeholder="Fayetteville" id="ltl_origin_city">
                    </div>

                    <div class="ltl_add_warehouse_input city_select" title="City" style="display:none;">
                        <label for="ltl_origin_city">City</label>
                        <select id="actname"></select>
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_state">State</label>
                        <input type="text" class="alphaonly" maxlength="2" title="State" value=""
                               name="ltl_origin_state" placeholder="GA" id="ltl_origin_state">
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_country">Country</label>
                        <input type="text" class="alphaonly" maxlength="2" title="Country" name="ltl_origin_country"
                               value="" placeholder="US" id="ltl_origin_country">
                        <input type="hidden" name="ltl_location" value="warehouse" id="ltl_location">
                    </div>

                    <input type="submit" name="ltl_submit_warehouse" value="Save" class="save_warehouse_form"
                           onclick="return en_cortigo_save_ltl_warehouse();">
                </form>
            </div>
        </div>
    </div>
    