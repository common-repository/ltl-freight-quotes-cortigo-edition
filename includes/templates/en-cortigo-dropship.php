<?php
/**
 * Drop ships adding, modifying and deleting page
 * @package     Woocommerce cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$dropship_list = $wpdb->get_results(
    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE `location` = 'dropship'"
);
?>
<div class="ltl_setting_section">
    <a href="#delete_ltl_dropship_btn" class="delete_ltl_dropship_btn hide_drop_val"></a>
    <div id="delete_ltl_dropship_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup">
            <h2 class="del_hdng">
                Warning!
            </h2>
            <p class="delete_p">
                Warning! If you delete this location, Drop ship location settings will be disable against products if
                any.
            </p>
            <div class="del_btns">
                <a href="#" class="cancel_delete">Cancel</a>
                <a href="#" class="confirm_delete">OK</a>
            </div>
        </div>
    </div>

    <h1>Drop ships</h1><br>
    <a href="#add_ltl_dropship_btn" title="Add Drop Ship" class="ltl_add_dropship_btn hide_drop_val">Add</a>
    <br>
    <div class="warehouse_text">
        <p>Locations that inventory specific items that are drop shipped to the destination. Use the product's settings
            page to identify it as a drop shipped item and its associated drop ship location. Orders that include drop
            shipped items will display a single figure for the shipping rate estimate that is equal to the sum of the
            cheapest option of each shipment required to fulfill the order.</p>
    </div>
    <div id="message" class="updated inline dropship_created">
        <p><strong>Success! New drop ship added successfully.</strong></p>
    </div>
    <div id="message" class="updated inline dropship_updated">
        <p><strong>Success! Drop ship updated successfully.</strong></p>
    </div>
    <div id="message" class="updated inline dropship_deleted">
        <p><strong>Success! Drop ship deleted successfully.</strong></p>
    </div>
    <table class="ltl_dropship_list" id="append_dropship">
        <thead>
        <tr>
            <th class="ltl_dropship_list_heading">Nickname</th>
            <th class="ltl_dropship_list_heading">City</th>
            <th class="ltl_dropship_list_heading">State</th>
            <th class="ltl_dropship_list_heading">Zip</th>
            <th class="ltl_dropship_list_heading">Country</th>
            <th class="ltl_dropship_list_heading">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($dropship_list) > 0) {
            foreach ($dropship_list as $list) {
                ?>
                <tr id="row_<?php echo $list->id; ?>">
                    <td class="ltl_dropship_list_data"><?php echo $list->nickname; ?></td>
                    <td class="ltl_dropship_list_data"><?php echo $list->city; ?></td>
                    <td class="ltl_dropship_list_data"><?php echo $list->state; ?></td>
                    <td class="ltl_dropship_list_data"><?php echo $list->zip; ?></td>
                    <td class="ltl_dropship_list_data"><?php echo $list->country; ?></td>
                    <td class="ltl_dropship_list_data">
                        <a href="javascript(0)"
                           onclick="return en_cortigo_edit_ltl_dropship(<?php echo $list->id; ?>);"><img
                                    src="<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"
                                    title="Edit"></a>
                        <a href="javascript(0)"
                           onclick="return en_cortigo_delete_ltl_current_dropship(<?php echo $list->id; ?>);"><img
                                    src="<?php echo plugins_url(); ?>/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"
                                    title="Delete"></a></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr class="new_dropship_add" data-id=0></tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Add Popup for new dropship -->
    <div id="add_ltl_dropship_btn" class="ltl_warehouse_overlay">
        <div class="ltl_add_warehouse_popup ds_popup">
            <h2 class="dropship_heading">Drop Ship</h2>
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
                    <input type="hidden" name="edit_dropship_form_id" value="" id="edit_dropship_form_id">
                    <div class="ltl_add_warehouse_input ds_input">
                        <label for="ltl_dropship_nickname">Nickname</label>
                        <input type="text" title="Nickname" value="" name="ltl_dropship_nickname" placeholder="Nickname"
                               id="ltl_dropship_nickname">
                    </div>
                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_zip">Zip</label>
                        <input title="Zip" type="text" value="" name="ltl_dropship_zip" placeholder="30214"
                               id="ltl_dropship_zip">
                    </div>

                    <div class="ltl_add_warehouse_input city_input">
                        <label for="ltl_origin_city">City</label>
                        <input type="text" class="alphaonly" title="City" value="" name="ltl_dropship_city"
                               placeholder="Fayetteville" id="ltl_dropship_city">
                    </div>

                    <div class="ltl_add_warehouse_input city_select">
                        <label for="ltl_origin_city">City</label>
                        <select id="dropship_actname"></select>
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_state">State</label>
                        <input type="text" class="alphaonly" maxlength="2" title="State" value=""
                               name="ltl_dropship_state" placeholder="GA" id="ltl_dropship_state">
                    </div>

                    <div class="ltl_add_warehouse_input">
                        <label for="ltl_origin_country">Country</label>
                        <input type="text" class="alphaonly" maxlength="2" title="Country" name="ltl_dropship_country"
                               value="" placeholder="US" id="ltl_dropship_country">
                        <input type="hidden" name="ltl_dropship_location" value="dropship" id="ltl_dropship_location">
                    </div>

                    <input type="submit" name="ltl_submit_dropship" value="Save" class="save_warehouse_form"
                           onclick="return en_cortigo_save_ltl_dropship();">
                </form>
            </div>
        </div>
    </div>