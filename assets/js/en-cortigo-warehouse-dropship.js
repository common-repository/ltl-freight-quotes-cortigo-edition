/**
 * Warehouse Section Script Start
 * @package     Woocommerce Cortigo Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */

/**
 * save warehouse script
 */
function en_cortigo_save_ltl_warehouse() {
    var city = jQuery('#ltl_origin_city').val();
    var zip_err = false;
    var city_err = false;
    var state_err = false;
    var country_err = false;
    if (jQuery('#ltl_origin_zip').val() === '') {
        jQuery('.zip_invalid').hide();
        jQuery('.ltl_zip_validation_err').remove();
        jQuery('#ltl_origin_zip').after('<span class="ltl_zip_validation_err">Zip is required.</span>');
        zip_err = 1;
    }

    if (city === '') {
        jQuery('.ltl_city_validation_err').remove();
        jQuery('#ltl_origin_city').after('<span class="ltl_city_validation_err">City is required.</span>');
        city_err = 1;
    }

    if (jQuery('#ltl_origin_state').val() === '') {
        jQuery('.ltl_state_validation_err').remove();
        jQuery('#ltl_origin_state').after('<span class="ltl_state_validation_err">State is required.</span>');
        state_err = 1;
    }

    if (jQuery('#ltl_origin_country').val() === '') {
        jQuery('.ltl_country_validation_err').remove();
        jQuery('#ltl_origin_country').after('<span class="ltl_country_validation_err">Country is required.</span>');
        country_err = 1;
    }

    if (zip_err || city_err || state_err || country_err) {
        return false;
    }

    var postForm = {
        'action': 'ltl_save_warehouse',
        'origin_id': jQuery('#edit_form_id').val(),
        'origin_city': city,
        'origin_state': jQuery('#ltl_origin_state').val(),
        'origin_zip': jQuery('#ltl_origin_zip').val(),
        'origin_country': jQuery('#ltl_origin_country').val(),
        'location': jQuery('#ltl_location').val(),
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            var WarehpuseDataId = data.id;
            if (data.insert_qry == 1) {
                jQuery('.warehouse_created').show('slow').delay(5000).hide('slow');
                jQuery('.warehouse_updated').css('display', 'none');
                jQuery('.warehouse_deleted').css('display', 'none');
                jQuery('.dropship_updated').css('display', 'none');
                jQuery('.dropship_created').css('display', 'none');
                jQuery('.dropship_deleted').css('display', 'none');
                window.location.href = jQuery('.close').attr('href');
                jQuery('#append_warehouse tr').last().after('<tr class="new_warehouse_add" id="row_' + WarehpuseDataId + '" data-id="' + WarehpuseDataId + '"><td class="ltl_warehouse_list_data">' + data.origin_city + '</td><td class="ltl_warehouse_list_data">' + data.origin_state + '</td><td class="ltl_warehouse_list_data">' + data.origin_zip + '</td><td class="ltl_warehouse_list_data">' + data.origin_country + '</td><td class="ltl_warehouse_list_data"><a href="javascript(0)" title="Edit" onclick="return en_cortigo_edit_ltl_warehouse(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_cortigo_delete_ltl_current_warehouse(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"></a></td></tr>');
            } else if (data.update_qry == 1) {
                jQuery('.warehouse_updated').show('slow').delay(5000).hide('slow');
                jQuery('.warehouse_created').css('display', 'none');
                jQuery('.warehouse_deleted').css('display', 'none');
                jQuery('.dropship_updated').css('display', 'none');
                jQuery('.dropship_created').css('display', 'none');
                jQuery('.dropship_deleted').css('display', 'none');
                window.location.href = jQuery('.close').attr('href');
                jQuery('tr[id=row_' + WarehpuseDataId + ']').html('<td class="ltl_warehouse_list_data">' + data.origin_city + '</td><td class="ltl_warehouse_list_data">' + data.origin_state + '</td><td class="ltl_warehouse_list_data">' + data.origin_zip + '</td><td class="ltl_warehouse_list_data">' + data.origin_country + '</td><td class="ltl_warehouse_list_data"><a href="javascript(0)" title="Edit" onclick="return en_cortigo_edit_ltl_warehouse(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_cortigo_delete_ltl_current_warehouse(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"></a></td>');
            } else {
                jQuery('.already_exist').show('slow');
                setTimeout(function () {
                    jQuery('.already_exist').hide('slow');
                }, 5000);
            }
        },
    });
    return false;
}

/**
 * delete warehouse script
 */
function en_cortigo_delete_ltl_current_warehouse(e) {
    var postForm = {
        'action': 'ltl_delete_warehouse',
        'delete_id': e,
    };
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            if (data == 1) {
                jQuery('#row_' + e).remove();
                jQuery('.warehouse_deleted').show('slow').delay(5000).hide('slow');
                jQuery('.warehouse_updated').css('display', 'none');
                jQuery('.warehouse_created').css('display', 'none');
                jQuery('.dropship_updated').css('display', 'none');
                jQuery('.dropship_created').css('display', 'none');
                jQuery('.dropship_deleted').css('display', 'none');
            }
        },
    });
    return false;
}

/**
 * update warehouse script
 */
function en_cortigo_edit_ltl_warehouse(e) {
    var postForm = {
        'action': 'ltl_edit_warehouse',
        'edit_id': e,
    };
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            if (data[0]) {
                jQuery('#edit_form_id').val(data[0].id);
                jQuery('#ltl_origin_zip').val(data[0].zip);
                jQuery('.city_select').hide();
                jQuery('#ltl_origin_city').val(data[0].city);
                jQuery('.city_input').show();
                jQuery('#ltl_origin_city').css('background', 'none');
                jQuery('#ltl_origin_state').val(data[0].state);
                jQuery('#ltl_origin_country').val(data[0].country);
                jQuery('.ltl_zip_validation_err').hide();
                jQuery('.ltl_city_validation_err').hide();
                jQuery('.ltl_state_validation_err').hide();
                jQuery('.ltl_country_validation_err').hide();
                window.location.href = jQuery('.ltl_add_warehouse_btn').attr('href');
                jQuery('.already_exist').hide();
                setTimeout(function () {
                    if (jQuery('.ltl_add_warehouse_popup').is(':visible')) {
                        jQuery('.ltl_add_warehouse_input > input').eq(0).focus();
                    }
                }, 500);
            }
        },
    });
    return false;
}

// Dropship Section Script Start
/**
 * save dropship script
 */
function en_cortigo_save_ltl_dropship() {
    var city = jQuery('#ltl_dropship_city').val();
    var zip_err = false;
    var city_err = false;
    var state_err = false;
    var country_err = false;

    if (jQuery('#ltl_dropship_zip').val() === '') {
        jQuery('.zip_invalid').hide();
        jQuery('.ltl_zip_validation_err').remove();
        jQuery('#ltl_dropship_zip').after('<span class="ltl_zip_validation_err">Zip is required.</span>');
        zip_err = 1;
    }

    if (city === '') {
        jQuery('.ltl_city_validation_err').remove();
        jQuery('#ltl_dropship_city').after('<span class="ltl_city_validation_err">City is required.</span>');
        city_err = 1;
    }

    if (jQuery('#ltl_dropship_state').val() === '') {
        jQuery('.ltl_state_validation_err').remove();
        jQuery('#ltl_dropship_state').after('<span class="ltl_state_validation_err">State is required.</span>');
        state_err = 1;
    }

    if (jQuery('#ltl_dropship_country').val() === '') {
        jQuery('.ltl_country_validation_err').remove();
        jQuery('#ltl_dropship_country').after('<span class="ltl_country_validation_err">Country is required.</span>');
        country_err = 1;
    }

    if (zip_err || city_err || state_err || country_err) {
        return false;
    }

    var postForm = {
        'action': 'ltl_save_dropship',
        'dropship_id': jQuery('#edit_dropship_form_id').val(),
        'dropship_city': city,
        'nickname': jQuery('#ltl_dropship_nickname').val(),
        'dropship_state': jQuery('#ltl_dropship_state').val(),
        'dropship_zip': jQuery('#ltl_dropship_zip').val(),
        'dropship_country': jQuery('#ltl_dropship_country').val(),
        'location': jQuery('#ltl_dropship_location').val(),
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            var WarehpuseDataId = data.id;
            if (data.insert_qry == 1) {
                jQuery('.dropship_created').show('slow').delay(5000).hide('slow');
                jQuery('.dropship_updated').css('display', 'none');
                jQuery('.dropship_deleted').css('display', 'none');
                jQuery('.warehouse_created').css('display', 'none');
                jQuery('.warehouse_updated').css('display', 'none');
                jQuery('.warehouse_deleted').css('display', 'none');
                window.location.href = jQuery('.close').attr('href');
                jQuery('#append_dropship tr').last().after('<tr class="new_dropship_add" id="row_' + WarehpuseDataId + '" data-id="' + WarehpuseDataId + '"><td class="ltl_dropship_list_data">' + data.nickname + '</td><td class="ltl_dropship_list_data">' + data.origin_city + '</td><td class="ltl_dropship_list_data">' + data.origin_state + '</td><td class="ltl_dropship_list_data">' + data.origin_zip + '</td><td class="ltl_dropship_list_data">' + data.origin_country + '</td><td class="ltl_dropship_list_data"><a href="javascript(0)" title="Edit" onclick="return en_cortigo_edit_ltl_dropship(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_cortigo_delete_ltl_current_dropship(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"></a></td></tr>');
            } else if (data.update_qry == 1) {
                jQuery('.dropship_updated').show('slow').delay(5000).hide('slow');
                jQuery('.dropship_created').css('display', 'none');
                jQuery('.dropship_deleted').css('display', 'none');
                jQuery('.warehouse_created').css('display', 'none');
                jQuery('.warehouse_updated').css('display', 'none');
                jQuery('.warehouse_deleted').css('display', 'none');
                window.location.href = jQuery('.close').attr('href');
                jQuery('tr[id=row_' + WarehpuseDataId + ']').html('<td class="ltl_dropship_list_data">' + data.nickname + '</td><td class="ltl_dropship_list_data">' + data.origin_city + '</td><td class="ltl_dropship_list_data">' + data.origin_state + '</td><td class="ltl_dropship_list_data">' + data.origin_zip + '</td><td class="ltl_dropship_list_data">' + data.origin_country + '</td><td class="ltl_dropship_list_data"><a href="javascript(0)" title="Edit" onclick="return en_cortigo_edit_ltl_dropship(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_cortigo_delete_ltl_current_dropship(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-cortigo-edition/assets/icons/delete.png"></a></td>');
            } else {
                jQuery('.already_exist').show('slow');
                setTimeout(function () {
                    jQuery('.already_exist').hide('slow');
                }, 5000);
            }
        },
    });
    return false;
}

/**
 * update dropship script
 */

function en_cortigo_edit_ltl_dropship(e) {
    var postForm = {
        'action': 'ltl_edit_dropship',
        'dropship_edit_id': e,
    };
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            if (data[0]) {
                jQuery('#edit_dropship_form_id').val(data[0].id);
                jQuery('#ltl_dropship_zip').val(data[0].zip);
                jQuery('.city_select').hide();
                jQuery('#ltl_dropship_nickname').val(data[0].nickname);
                jQuery('#ltl_dropship_city').val(data[0].city);
                jQuery('.city_input').show();
                jQuery('#ltl_dropship_city').css('background', 'none');
                jQuery('#ltl_dropship_state').val(data[0].state);
                jQuery('#ltl_dropship_country').val(data[0].country);
                jQuery('.ltl_zip_validation_err').hide();
                jQuery('.ltl_city_validation_err').hide();
                jQuery('.ltl_state_validation_err').hide();
                jQuery('.ltl_country_validation_err').hide();
                window.location.href = jQuery('.ltl_add_dropship_btn').attr('href');
                jQuery('.already_exist').hide();
                setTimeout(function () {
                    if (jQuery('.ltl_add_warehouse_popup').is(':visible')) {
                        jQuery('.ds_input > input').eq(0).focus();
                    }
                }, 500);
            }
        },
    });
    return false;
}

/**
 * delete dropship script
 */
function en_cortigo_delete_ltl_current_dropship(e) {
    var id = e;
    window.location.href = jQuery('.delete_ltl_dropship_btn').attr('href');
    jQuery('.cancel_delete').on('click', function () {
        window.location.href = jQuery('.cancel_delete').attr('href');
    });
    jQuery('.confirm_delete').on('click', function () {
        window.location.href = jQuery('.confirm_delete').attr('href');
        return en_cortigo_confirm_ltl_delete_dropship(id);
    });
    return false;
}

/**
 *
 * @param e
 * @returns Boolean
 */
function en_cortigo_confirm_ltl_delete_dropship(e) {
    var postForm = {
        'action': 'ltl_delete_dropship',
        'dropship_delete_id': e,
    };
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',

        success: function (data) {
            if (data == 1) {
                jQuery('#row_' + e).remove();
                jQuery('.dropship_deleted').show('slow').delay(5000).hide('slow');
                jQuery('.dropship_created').css('display', 'none');
                jQuery('.dropship_updated').css('display', 'none');
                jQuery('.warehouse_created').css('display', 'none');
                jQuery('.warehouse_updated').css('display', 'none');
                jQuery('.warehouse_deleted').css('display', 'none');
            }
        },
    });
    return false;
}
