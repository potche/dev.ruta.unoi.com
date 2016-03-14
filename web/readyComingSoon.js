/*
 *  Document   : readyComingSoon.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Coming Soon page
 */

var ComingSoon = function() {

    return {
        init: function() {
            // With Countdown.js, for extra usage examples you can check out https://github.com/hilios/jQuery.countdown
            $('.js-countdown').countdown('2016/01/15', function(event) {
                $(this).html(event.strftime('<div class="row">'
                        + '<div class="col-xs-6 col-sm-4 countdown-con"><div class="countdown-num">%H</div><div class="countdown-info">HORAS</div></div>'
                        + '<div class="col-xs-6 col-sm-4 countdown-con"><div class="countdown-num">%M</div><div class="countdown-info">MINUTOS</div></div>'
                        + '<div class="col-xs-6 col-sm-4 countdown-con"><div class="countdown-num">%S</div><div class="countdown-info">SEGUNDOS</div></div>'
                        + '</div>'
                ));
            });
        }
    };
}();