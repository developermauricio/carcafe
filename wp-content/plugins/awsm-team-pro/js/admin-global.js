jQuery(document).ready(function($) {
    var notice_wrapper_sel = '.awsm-pro-activate-notice';
    $(notice_wrapper_sel).on('click', '.notice-dismiss', function(e) {
        e.preventDefault();
        var $dismiss_elem = $(this);
        var $wrapper = $dismiss_elem.parents(notice_wrapper_sel);
        var nonce = $dismiss_elem.parents(notice_wrapper_sel).data('nonce');
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                nonce: nonce,
                action: 'awsm_team_pro_admin_notice'
            },
            dataType: "json"
        }).done(function(response) {
            if (response && response.dismiss) {
                $wrapper.fadeTo(400, 0, function() {
                    $wrapper.slideUp(100, function() {
                        $wrapper.remove();
                    });
                });
            }
        })
        .fail(function(xhr) {
            console.log(xhr.responseText);
        })
    });

    $('#awsm_team_pro_enable_crop').on('click', function(e) {
        if ($(this).is(":checked")) {
            $('.awsm-team-pro-thumbnail-options').addClass('show');
        } else{
            $('.awsm-team-pro-thumbnail-options').removeClass('show');
        }
    });

    $('#awsm_team_pro_enable_deep_linking').on('click', function(e) {
		var $wrapper = $('.awsm-team-pro-deep-linking-options');
        if ($(this).is(":checked")) {
            $wrapper.addClass('show').find('.awsm-team-req-field').prop('required', true);
        } else{
            $wrapper.removeClass('show').find('.awsm-team-req-field').prop('required', false);
        }
    });

    var deepLinkRegEx = new RegExp('^([a-z0-9]+(-|_))*[a-z0-9]+$');

    $('#awsm-team-pro-settings-general-form').on('submit', function(e) {
        var isValid = true;
        $('.awsm-team-pro-error-container').remove();
        $('.awsm-team-deep-link-field').each(function() {
			var $elem = $(this);
			var fieldVal = $elem.val().trim();
			var isReqField = $elem.hasClass('awsm-team-req-field');
            if ((isReqField && fieldVal.length === 0) || (fieldVal.length > 0 && ! deepLinkRegEx.test(fieldVal))) {
                isValid = false;
            }
        });
        if (! isValid) {
            e.preventDefault();
            var errorTemplate = wp.template('awsm-team-pro-settings-error');
            var templateData = {isInvalidKey: true};
            $('#awsm-team-pro-settings-general-form').find('.awsm-form-section-main').append(errorTemplate(templateData));
        }
    });
});
