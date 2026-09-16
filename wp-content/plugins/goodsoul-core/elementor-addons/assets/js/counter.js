
(function ($) { 

    "use strict";

    var counter = function ($scope, $) {
        if ($('.time-countdown-two').length) {
            $('.time-countdown-two').each(function() {
                var Self = $(this);
                var countDate = Self.data('countdown-time'); // getting date
                Self.countdown(countDate, function(event) {
                    $(this).html('<li> <div class="box"> <span class="days">' + event.strftime('%D') + '</span> <span class="timeRef">days</span> </div> </li> <li> <div class="box"> <span class="hours">' + event.strftime('%H') + '</span> <span class="timeRef clr-1">hours</span> </div> </li> <li> <div class="box"> <span class="minutes">' + event.strftime('%M') + '</span> <span class="timeRef clr-2">minutes</span> </div> </li> <li> <div class="box"> <span class="seconds">' + event.strftime('%S') + '</span> <span class="timeRef clr-3">seconds</span> </div> </li>');
                });
            });
        }
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_events.default', counter);
    });
})(window.jQuery);