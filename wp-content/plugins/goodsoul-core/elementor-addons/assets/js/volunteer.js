(function($) {

    "use strict";

    var volunteerjs = function() {
        if ($('.volunteer-carousel').length) {
            var vImg = new Swiper(".volunteer-image", {
                preloadImages: false,
                loop: true,
                speed: 600,
                spaceBetween: 0,
                direction: "vertical",
                effect: "slide",
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            var vCont = new Swiper(".volunteer-content", {
                preloadImages: false,
                loop: true,
                speed: 600,
                spaceBetween: 0,
                effect: "slide",
                pagination: {
                    el: '.volunteer-carousel-pagination',
                    clickable: true,
                }
            });
            vImg.controller.control = vCont;
            vCont.controller.control = vImg;
            vCont.on('slideChange', function() {
                var csli = vCont.realIndex + 1,
                    curnum = $('.swiper-counter-two #current');
                TweenMax.to(curnum, 0.2, {
                    force3D: true,
                    y: -10,
                    opacity: 0,
                    ease: Power2.easeOut,
                    onComplete: function() {
                        TweenMax.to(curnum, 0.1, {
                            force3D: true,
                            y: 10
                        });
                        curnum.html('0' + csli);
                    }
                });
                TweenMax.to(curnum, 0.2, {
                    force3D: true,
                    y: 0,
                    delay: 0.3,
                    opacity: 1,
                    ease: Power2.easeOut
                });
            });
            var totalSlides = vCont.slides.length - 2;
            $('.swiper-counter-two #total').html('0' + totalSlides);
        }
    }
    volunteerjs();


    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_volunteer.default', volunteerjs);
    });


})(window.jQuery);