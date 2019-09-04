"use strict";
jQuery(document).ready(function ($) {

//    featured slider
    var swiper = new Swiper('.swiper-container', {
//        pagination: '.swiper-pagination',
        loop: true,
        slidesPerView: 'auto',
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        spaceBetween: 30,
        coverflow: {
            rotate: 5,
            stretch: 0,
            depth: 100,
            modifier: 3,
            slideShadows: false
        }
    });

});













