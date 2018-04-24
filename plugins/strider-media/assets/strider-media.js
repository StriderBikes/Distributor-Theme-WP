jQuery(document).ready(function($){

    $('#striderslider-progression').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '#striderslider-progression-nav',
        dots: false,
        centerMode: false,
        focusOnSelect: true,

        autoplay: true,
        autoplaySpeed: 3500,

    }).on('beforeChange', function(event, slick, i){
        var $all = $('#striderslider-progression-nav .slick-slide');
        if (i+1 >= $all.length) {
            i = -1;
        }
        $all.removeClass('slick-current');
        $all.eq(i+1).addClass('slick-current');
    });

    $('#striderslider-progression-nav').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        asNavFor: '#striderslider-progression',
        dots: false,
        centerMode: false,
        focusOnSelect: true,

    });

    $('.strider-slider a').magnificPopup({
        type:'image',
        gallery: {
            enabled: false,
        }
    });
    $('.strider-slider').slick();

    $('.strider-gallery a').magnificPopup({
        type:'image',
        gallery: {
            enabled: true,
        }
    });
});