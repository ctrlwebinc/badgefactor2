/*
 * Badge Factor 2
 * Copyright (C) 2019-2022 ctrlweb
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
jQuery(document).ready(function ($) {

    console.log('Privacy js loaded');

    $('#assertion_visibility_toggle').on('click', function (e) {
        $.ajax({
            type : "post",
            dataType : "json",
            url : bf2_privacy_ajax.callback_parameters.ajax_endpoint,
            data : {
                action: bf2_privacy_ajax.callback_parameters.ajax_action,
                badge_slug: bf2_privacy_ajax.callback_parameters.badge_slug,
                nonce: bf2_privacy_ajax.callback_parameters.nonce
            },
            success: function (response) {
                 console.log(response);
                 if ( response.has_privacy_flag == true ) {
                    $('#assertion_visibility_toggle').removeClass('visibility-public');
                 } else {
                    $('#assertion_visibility_toggle').addClass('visibility-public');
                 }
                },
            error: function ( error ) { console.log(error);}
        });
    })

/*     jQuery.ajax({
        type : "post",
        dataType : "json",
        url : bf2_privacy_ajax.callback_parameters.ajax_endpoint,
        data : {
            action: bf2_privacy_ajax.callback_parameters.ajax_action,
            badge_slug: bf2_privacy_ajax.callback_parameters.badge_slug,
            nonce: bf2_privacy_ajax.callback_parameters.nonce
        },
        success: function (response) { console.log(response);},
        error: function ( error ) { console.log(error);}
    }); */
});
