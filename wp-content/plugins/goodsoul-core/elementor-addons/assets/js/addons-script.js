(function ($) {
    "use strict";
        // Lazyload Images
    var lazyImage = function ($scope, $) {
        if($('.lazy-image').length){
            new LazyLoad({
                elements_selector: ".lazy-image",
                load_delay: 0,
                threshold: 300
            });
        }
    }

    var lightboxImage = function ($scope, $) {
        //LightBox / Fancybox
        if($('.lightbox-image').length) {
            $('.lightbox-image').fancybox({
                openEffect  : 'fade',
                closeEffect : 'fade',
                helpers : {
                    media : {}
                }
            });
        }
    }
    var loveCarousel = function ($scope, $) {
        if ($('.love-carousel').length) {
                $(".love-carousel").each(function (index) {
                var $owlAttr = {},
                $extraAttr = $(this).data("options");
                $.extend($owlAttr, $extraAttr);
                $(this).owlCarousel($owlAttr);
            });
        }
    }
    var protfolio = function ($scope, $) {
            //MixitUp Gallery Filters
            if($('.filter-list').length){
                $('.filter-list').mixItUp({});
        }
        
    }
    var sponsor_js = function ($scope, $) {
        //MixitUp Gallery Filters
        if ($('.sponsors-carousel').length) {
            $('.sponsors-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                smartSpeed: 1000,
                autoplay: 5000,
                navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    767: {
                        items: 3
                    },
                    1024: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            });
        }  
}


var event_js = function ($scope, $) {
    if ($('.event-carousel').length) {
		$('.event-carousel').owlCarousel({
			loop: true,
			margin: 0,
			nav: true,
			smartSpeed: 700,
			autoplay: 5000,
			navText: ['<span class="fa fa-angle-left">PREV</span>', '<span class="fa fa-angle-right">NEXT</span>'],
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
					items: 3
				}
			}
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

var five_item_js = function ($scope, $) {
if ($('.five-item-carousel').length) {
    $('.five-item-carousel').owlCarousel({
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
                items: 2
            },
            800: {
                items: 3
            },
            1024: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
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


var accordion_js = function ($scope, $) {
if ($('.accordion-box').length) {
    $(".accordion-box").on('click', '.accord-btn', function() {
        if ($(this).parents('.accordion').hasClass('active') !== true) {
            $('.accordion').removeClass('active');
        }
        if ($(this).next('.accord-content').is(':visible')) {
            $(this).removeClass('active');
            $(this).next('.accord-content').slideUp(500);
        } else {
            $(this).parents('.accordion').addClass('active');
            $('.accordion .accord-content').slideUp(500);
            $(this).next('.accord-content').slideDown(500);
        }
    });
}
}



var bannerSlider = function ($scope, $) {
    
		if ($(".banner-slider").length > 0) {
			var bannerSlider = new Swiper('.banner-slider', {
				spaceBetween: 0,
				slidesPerView: 1,
				mousewheel: false,
				height: 500,
				grabCursor: true,
				loop: true,
				speed: 1500,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false
				},
				pagination: {
					el: '.banner-slider-pagination',
					clickable: true,
				},
				navigation: {
					nextEl: '.banner-slider-button-next',
					prevEl: '.banner-slider-button-prev',
				},
			});
			bannerSlider.on('slideChange', function() {
				var csli = bannerSlider.realIndex + 1,
					curnum = $('.swiper-counter #current');
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

			function kpsc() {
				$(".slide-progress").css({
					width: "100%",
					transition: "width 4000ms"
				});
			}

			function eqwe() {
				$(".slide-progress").css({
					width: 0,
					transition: "width 0s"
				});
			}
			kpsc();
			bannerSlider.on("slideChangeTransitionStart", function() {
				eqwe();
			});
			bannerSlider.on("slideChangeTransitionEnd", function() {
				kpsc();
			});
			var totalSlides = bannerSlider.slides.length - 2;
			$('.swiper-counter #total').html('0' + totalSlides);
		}
}

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_banner_slider.default', bannerSlider);

        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_sponsors.default', sponsor_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_events.default', event_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_testimonial.default', three_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_gellery.default', four_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_gellery.default', single_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_events.default', single_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_payment.default', five_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_team_page.default', five_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_team_home.default', five_item_js);
        elementorFrontend.hooks.addAction('frontend/element_ready/goodsoul_faq.default', accordion_js);





        elementorFrontend.hooks.addAction('frontend/element_ready/banner_slider__o.default', lazyImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_populer_causes.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_populer_causes.default', lazyImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/counter_area__o.default', lazyImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/insta_gallery_one.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/insta_gallery_one.default', lazyImage);
        elementorFrontend.hooks.addAction('frontend/element_ready/insta_gallery_one.default', lightboxImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/volunteer_area__o.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/volunteer_area__o.default', lazyImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/people_slider__o.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/people_slider__o.default', lazyImage);
        elementorFrontend.hooks.addAction('frontend/element_ready/people_slider__o.default', lightboxImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/events.default', loveCarousel);

        elementorFrontend.hooks.addAction('frontend/element_ready/protfolio_area.default', protfolio);
        elementorFrontend.hooks.addAction('frontend/element_ready/protfolio_area.default', lightboxImage);

        
        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_review_are.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_review_are.default', lightboxImage);
        
        elementorFrontend.hooks.addAction('frontend/element_ready/dnors_list__o.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/dnors_list__o.default', lightboxImage);

        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_sponsors__o.default', loveCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/loveus_sponsors__o.default', lightboxImage);



    });
})(jQuery);