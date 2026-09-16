
(function ($) { 

    "use strict";

    var causes = function ($scope, $) {
        if ($('.cause-carousel').length) {
            $('.cause-carousel').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                smartSpeed: 1000,
                autoplay: 5000,
                navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    800: {
                        items: 2
                    },
                    1024: {
                        items: 3
                    },
                    1200: {
                        items: 3
                    },
                }
            });
        }
    }
    var single_item_js = function ($scope, $) {
        if ($('.single-item-carousel').length) {
            $('.single-item-carousel').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                smartSpeed: 2000,
                autoplay: 5000,
                navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    1200: {
                        items: 1
                    }
                }
            });
        }
        }
    var counter = function ($scope, $) {
        if ($('.count-box').length) {
            $('.count-box').appear(function() {
                var $t = $(this),
                    n = $t.find(".count-text").attr("data-stop"),
                    r = parseInt($t.find(".count-text").attr("data-speed"), 10);
                if (!$t.hasClass("counted")) {
                    $t.addClass("counted");
                    $({
                        countNum: $t.find(".count-text").text()
                    }).animate({
                        countNum: n
                    }, {
                        duration: r,
                        easing: "linear",
                        step: function() {
                            $t.find(".count-text").text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $t.find(".count-text").text(this.countNum);
                        }
                    });
                }
            }, {
                accY: 0
            });
        }
        }
        var four_item_js = function ($scope, $) {
            if ($('.four-item-carousel').length) {
                $('.four-item-carousel').owlCarousel({
                    loop: false,
                    margin: 0,
                    nav: true,
                    smartSpeed: 2000,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 2
                        },
                        800: {
                            items: 2
                        },
                        1024: {
                            items: 3
                        },
                        1200: {
                            items: 4
                        }
                    }
                });
            }
            }
    var showdial = function ($scope, $) {
    	if ($('.dial').length) {
            $('.dial').appear(function() {
                var elm = $(this);
                var color = elm.attr('data-fgColor');
                var perc = elm.attr('value');
                elm.knob({
                    'value': 0,
                    'min': 0,
                    'max': 100,
                    'skin': 'tron',
                    'readOnly': true,
                    'thickness': 0.15,
                    'dynamicDraw': true,
                    'displayInput': false
                });
                $({
                    value: 0
                }).animate({
                    value: perc
                }, {
                    duration: 2000,
                    easing: 'swing',
                    progress: function() {
                        elm.val(Math.ceil(this.value)).trigger('change');
                    }
                });
                //circular progress bar color
                $(this).append(function() {
                    // elm.parent().parent().find('.circular-bar-content').css('color',color);
                    //elm.parent().parent().find('.circular-bar-content .txt').text(perc);
                });
            }, {
                accY: 20
            });
        }
    }

    var three_item_js = function ($scope, $) {
        if ($('.three-item-carousel').length) {
            $('.three-item-carousel').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                smartSpeed: 1000,
                autoplay: 5000,
                navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    800: {
                        items: 2
                    },
                    1024: {
                        items: 3
                    },
                    1200: {
                        items: 3
                    },
                }
            });
        }
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', causes);
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', single_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', four_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', counter);
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', showdial);
        elementorFrontend.hooks.addAction('frontend/element_ready/chariti_area__o.default', three_item_js);
    });
})(window.jQuery);