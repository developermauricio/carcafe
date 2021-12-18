var AWSM_TEAM_P = (function($) {
    'use strict';
    var frame,

        init = function() {
            reset();
            bind_events();
            $(window).resize(tb_position);
        },
        bind_events = function() {
            var $awsm_team_popup = $('#awsm-block-popup');
            $('body').on('click', '.awsm-team-btn', awsm_team_popup);
            $awsm_team_popup.on('change', '#cmb-teams', awsm_change_shortcode);
            $awsm_team_popup.on('click', '#awsm-insert-team', awsm_shortcode);
            $awsm_team_popup.on('click', '.cancel-awsm-block,.atp-close', remove_atppop);
        },
        awsm_team_popup = function(e) {
            reset();
            e.preventDefault();
            $('body').addClass('atp-popup-on');
            tb_show(team_settings.pluginname, "#TB_inline?inlineId=awsm-block-popup-wrap&amp;width=1030&amp;modal=true", null);
            tb_position();
            return;
        },
        tb_position = function() {
            var tbWindow = $('#TB_window');
            var width = $(window).width();
            var H = $(window).height();
            var W = (1080 < width) ? 1080 : width;

            if (tbWindow.size()) {
                tbWindow.width(W - 50).height(H - 45);
                $('#TB_ajaxContent').css({ 'width': '100%', 'height': '100%', 'padding': '0' });
                tbWindow.css({ 'margin-left': '-' + parseInt(((W - 50) / 2), 10) + 'px' });
                if (typeof document.body.style.maxWidth != 'undefined')
                    tbWindow.css({ 'top': '20px', 'margin-top': '0' });
                $('#TB_title').css({ 'background-color': '#fff', 'color': '#cfcfcf' });
            };
        },
        awsm_change_shortcode = function() {
            var team = $('#cmb-teams').val();
            if (team !== '') {
                $('#atp-shortcode').val(team);
                $('#awsm-insert-team').removeAttr('disabled');
                $('#awsm-insert-team').removeProp('disabled');
                $("#awsm-insert-team").attr("disabled", false);
            } else{
                $("#awsm-insert-team").attr("disabled", true);
            }
        },
        showmsg = function(msg) {
            $('#awsm-block-message').fadeIn();
            $('#awsm-block-message p').text(msg);
        },
        awsm_shortcode = function(event) {
            if ($('#atp-shortcode').val()) {
                var ins_shortcode = true;
                if(typeof wp.blocks !== 'undefined') {
                    var team_block = wp.blocks.getBlockType('gutenberg-awsm/awsm-team-dynamic');
                    if(typeof team_block !== 'undefined') {
                        ins_shortcode = false;
                    }
                }
                if(ins_shortcode) {
                    wp.media.editor.insert($('#atp-shortcode').val());
                }
                remove_atppop(event);
            } else {
                showmsg(team_settings.nocontent);
            }
        },
        remove_atppop = function(event) {
            event.preventDefault();
            tb_remove();
            setTimeout(function() {
                $('body').removeClass('atp-popup-on');
            }, 800);
        },
        
        reset = function() {
            $('.atp-container').show();
            $('#awsm-block-message').hide();
            $('#atp-boxtheme').hide();
        }
    return {
        init: init
    };
})(jQuery);

jQuery(AWSM_TEAM_P.init);
