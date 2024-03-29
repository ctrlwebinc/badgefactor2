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

    /* Show assertion privacy popup */
    $('body').on('click', '.has_privacy_flag', function (e) {
        e.preventDefault();
        var popupOverlay = $('.assertion_privacy_popup_overlay');
        popupOverlay.css('opacity','1');
        popupOverlay.css('display','flex');
    });

    // click on eye icon
    $('#assertion_visibility_toggle').on('click', function (e) {
        toggle_assertion_privacy()
    })

    /* Assertion privacy popup */
    $('body').on('click', '#assertion_privacy_btn_confirm', function (e) {
        var popupOverlay = $('.assertion_privacy_popup_overlay');
        var updatingMessage = $('#assertion_privacy_popup_updating_message').val();
        var actionToTake = $('input[type=radio][name=prompt_assertion_privacy]:checked').val();

        if (actionToTake == 'close') {
            popupOverlay.css('display','none');
        } else if (actionToTake == 'make_assertion_visible') {
            $('#assertion_visibility_toggle').addClass('visibility-updating');
            $('.assertion_privacy_popup_action_message').addClass('show').text(updatingMessage);
            toggle_assertion_privacy(true) 
        }
    });

    function toggle_assertion_privacy(makePublic = false) {
        var popupOverlay = $('.assertion_privacy_popup_overlay');
        var confirmingMessage = $('#assertion_privacy_popup_confirming_message').val();
        var overlayContent = $('.assertion_privacy_popup_content').text();

        $('#assertion_visibility_toggle').addClass('visibility-updating');

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
                $('#assertion_visibility_toggle').removeClass('visibility-updating');
                    if ( response.has_privacy_flag == true ) {
                        $('#assertion_visibility_toggle').addClass('visibility-private');
                        $('.bf2_social_share').addClass('has_privacy_flag');
                    } else {
                        $('#assertion_visibility_toggle').removeClass('visibility-private');
                        $('.has_privacy_flag').removeClass('has_privacy_flag');
                        if (makePublic == true) {
                            $('.assertion_privacy_popup_action_message').text(confirmingMessage);
                            setTimeout(function() {
                                $('.assertion_privacy_popup_action_message').text('');
                                $('.assertion_privacy_popup_action_message').removeClass('show');
                                popupOverlay.css('display','none');
                            }, 3000);
                        }
                    }
                },
            error: function ( error ) { console.log(error);}
        });
    }

});
