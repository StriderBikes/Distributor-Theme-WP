<?php
/**
 * Plugin Name: Strider Media Tools
 * Plugin URI:
 * Version: 1.0.0
 * Author: Strider Sports Intl., Inc.
 * Description: Provides shortcodes for sliders and popover enhancements for galleries
 * Text Domain: strider-media
 * License: BSD 3-Clause
 *
 * Copyright (c) 2016-2017, Strider Sports Intl., Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the
 * following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 * following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * 3. Neither the name of Strider Sports Intl., Inc., nor the names of its contributors may be used to endorse or
 * promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * Enqueue our css/js assets
 */
if (!function_exists('strider_media_enqueue_assets')) {
    function strider_media_enqueue_assets() {
        wp_enqueue_style('strider_media_slick', plugin_dir_url(__FILE__) . '/assets/slick/slick.css');
        wp_enqueue_script('strider_media_slick', plugin_dir_url(__FILE__) . 'assets/slick/slick.min.js', array('jquery'));

        wp_enqueue_style('strider_media_mp', plugin_dir_url(__FILE__) . 'assets/magnific-popup/magnific-popup.css');
        wp_enqueue_script('strider_media_mp', plugin_dir_url(__FILE__) . 'assets/magnific-popup/jquery.magnific-popup.min.js', array('jquery'));

        wp_enqueue_style('strider_media', plugin_dir_url(__FILE__) . 'assets/strider-media.css', array('strider_media_slick'));
        wp_enqueue_script('strider_media', plugin_dir_url(__FILE__) . 'assets/strider-media.js', array('strider_media_slick'));
    }

    add_action('wp_enqueue_scripts', 'strider_media_enqueue_assets');
}

/*
 * XXX For Progression Slider
 *
 * [striderslider ids="" instance="progression" slslidestoshow="1" slslidestoscroll="1" slarrows="false" slfade="true"
 *     sldots="false" slcentermode="false" slfocusonselect="true"]
 *
 * [striderslider instance="progression-nav" slslidestoshow="5" slslidestoscroll="1" slarrows="false" slfade="false"
 *     slasnavfor="progression" sldots="false" slcentermode="false" slfocusonselect="true" slautoplay="true"
 *     slautoplayspeed="3500"]
 */

//
// Shortcode [striderslider ids="123,456,789"]
//
// Other Options
//      instance        [0++ ...]
//
// WordPress-like Options
//      order           [ASC, DESC]
//      orderby         [menu_order, title, post_date, rand, ID]
//      id              []
//      ids             []
//
// Slick Options
// XXX asnavfor
//      sl-adaptiveHeight       [true, false]
//      sl-arrows               [true, false]
//      sl-autoplay             [true, false]
//      sl-autoplaySpeed        [true, false]
//      sl-centerMode           [true, false]
//      sl-centerPadding        [50px ...]
//      sl-dots                 [true, false]
//      sl-dotsClass            [slick-dots ...]
//      sl-draggable            [true, false]
//      sl-fade                 [true, false]
//      sl-focusOnSelect        [true, false]
//      sl-infinite             [true, false]
//      sl-pauseOnHover         [true, false]
//      sl-pauseOnFocus         [true, false]
//      sl-pauseOnDotsHover     [true, false]
//      sl-rows                 [1 ...]
//      sl-slidesPerRow         [1 ...]
//      sl-slidesToShow         [1 ...]
//      sl-slidesToScroll       [1 ...]
//      sl-speed                [500 ...]
//      sl-swipe                [true, false]
//      sl-swipeToSlide         [true, false]
//      sl-touchMove            [true, false]
//      sl-touchThreshold       [5 ...]
//      sl-useCSS               [true, false]
//      sl-useTransform         [true, false]
//      sl-variableWidth        [true, false]
//      sl-vertical             [true, false]
//      sl-verticalSwiping      [true, false]
//      sl-waitForAnimate       [true, false]
//      sl-zIndex               [1000 ...]


function strider_media_cast_boolean($val) {
    if (strtolower($val) == 'true')
        return true;
    if (strtolower($val) == 'false')
        return false;

    return false; /* default false? YYY */
}

function strider_media_cast_int($val) {
    return (int)$val;
}


function strider_media($attr)
{
    $post = get_post();

    static $instance = 0;
    $instance++;

    if (!empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    $atts = shortcode_atts(array(
        /*
         * WordPress Gallery-like options
         */
        'id' => '',
        'ids' => '',
        'orderby' => 'menu_order',  // [title, post_date, rand, ID]
        'order' => 'ASC',

        'size' => 'large',
        'include' => '',
        'exclude' => '',
        'link' => '',

        'instance' => '',
        'showtitle' => 'false',

        'hasclass' => 'true',

        /*
         * Slick Slider options
         *
         * Types: boolean, numeric, string
         */
        'sladaptiveheight' => '',
        'slarrows' => '',
        'slautoplay' => '',
        'slautoplayspeed' => '',
        'slcentermode' => '',
        'slcenterpadding' => '',
        'sldots' => '',
        'sldotsclass' => '',
        'sldraggable' => '',
        'slfade' => '',
        'slfocusonselect' => '',
        'slinfinite' => '',
        'slpauseonhover' => '',
        'slpauseonfocus' => '',
        'slpauseondotshover' => '',
        'slrows' => '',
        'slslidesperrow' => '',
        'slslidestoshow' => '',
        'slslidestoscroll' => '',
        'slspeed' => '',
        'slswipe' => '',
        'slswipetoslide' => '',
        'sltouchmove' => '',
        'sltouchthreshold' => '',
        'slusecss' => '',
        'slusetransform' => '',
        'slvariablewidth' => '',
        'slvertical' => '',
        'slverticalswiping' => '',
        'slwaitforanimate' => '',
        'slzindex' => '',
        'slasnavfor' => '',
    ), $attr);

    $slickOpts = array();
    if (!empty($attr['sladaptiveheight'])) {
        $slickOpts['adaptiveHeight'] = strider_media_cast_boolean($attr['sladaptiveheight']);
    }
    if (!empty($attr['slarrows'])) {
        $slickOpts['arrows'] = strider_media_cast_boolean($attr['slarrows']);
    }
    if (!empty($attr['slautoplay'])) {
        $slickOpts['autoplay'] = strider_media_cast_boolean($attr['slautoplay']);
    }
    if (!empty($attr['slautoplayspeed'])) {
        $slickOpts['autoplaySpeed'] = strider_media_cast_int($attr['slautoplayspeed']);
    }
//    if (!empty($attr['slcentermode'])) { $slickOpts['centerMode'] = $attr['slcentermode']; }
//    if (!empty($attr['slcenterpadding'])) { $slickOpts['centerPadding'] = $attr['slcenterpadding']; }
    if (!empty($attr['sldots'])) {
        $slickOpts['dots'] = strider_media_cast_boolean($attr['sldots']);
    }
    if (!empty($attr['sldotsclass'])) {
        $slickOpts['dotsClass'] = $attr['sldotsclass'];
    }
    if (!empty($attr['sldraggable'])) {
        $slickOpts['draggable'] = strider_media_cast_boolean($attr['sldraggable']);
    }
    if (!empty($attr['slfade'])) {
        $slickOpts['fade'] = strider_media_cast_boolean($attr['slfade']);
    }
//    if (!empty($attr['slfocusonselect'])) { $slickOpts[''] = $attr['slfocusonselect']; }
    if (!empty($attr['slinfinite'])) {
        $slickOpts['infinite'] = strider_media_cast_boolean($attr['slinfinite']);
    }
    if (!empty($attr['slpauseonhover'])) {
        $slickOpts['pauseOnHover'] = strider_media_cast_boolean($attr['slpauseonhover']);
    }
    if (!empty($attr['slpauseonfocus'])) {
        $slickOpts['pauseOnFocus'] = strider_media_cast_boolean($attr['slpauseonfocus']);
    }
    if (!empty($attr['slpauseondotshover'])) {
        $slickOpts['pauseOnDotsHover'] = strider_media_cast_boolean($attr['slpauseondotshover']);
    }
//    if (!empty($attr['slrows'])) { $slickOpts['rows'] = $attr['slrows']; }
    if (!empty($attr['slslidesperrow'])) {
        $slickOpts['slidesPerRow'] = strider_media_cast_int($attr['slslidesperrow']);
    }
    if (!empty($attr['slslidestoshow'])) {
        $slickOpts['slidesToShow'] = strider_media_cast_int($attr['slslidestoshow']);
    }
    if (!empty($attr['slslidestoscroll'])) {
        $slickOpts['slidesToScroll'] = strider_media_cast_int($attr['slslidestoscroll']);
    }
    if (!empty($attr['slspeed'])) {
        $slickOpts['speed'] = strider_media_cast_int($attr['slspeed']);
    }
    if (!empty($attr['slswipe'])) {
        $slickOpts['swipe'] = strider_media_cast_boolean($attr['slswipe']);
    }
    if (!empty($attr['slswipetoslide'])) {
        $slickOpts['swipeToSlide'] = strider_media_cast_boolean($attr['slswipetoslide']);
    }
    if (!empty($attr['sltouchmove'])) {
        $slickOpts['touchMove'] = strider_media_cast_boolean($attr['sltouchmove']);
    }
    if (!empty($attr['sltouchthreshold'])) {
        $slickOpts['touchThreshold'] = strider_media_cast_int($attr['sltouchthreshold']);
    }
    if (!empty($attr['slusecss'])) {
        $slickOpts['useCSS'] = strider_media_cast_boolean($attr['slusecss']);
    }
    if (!empty($attr['slusetransform'])) {
        $slickOpts['useTransform'] = strider_media_cast_boolean($attr['slusetransform']);
    }
    if (!empty($attr['slvariablewidth'])) {
        $slickOpts['variableWidth'] = strider_media_cast_boolean($attr['slvariablewidth']);
    }
    if (!empty($attr['slvertical'])) {
        $slickOpts['vertical'] = strider_media_cast_boolean($attr['slvertical']);
    }
    if (!empty($attr['slverticalswiping'])) {
        $slickOpts['verticalSwiping'] = strider_media_cast_boolean($attr['slverticalswiping']);
    }
    if (!empty($attr['slwaitforanimate'])) {
        $slickOpts['waitForAnimate'] = strider_media_cast_boolean($attr['slwaitforanimate']);
    }
    if (!empty($attr['slzindex'])) {
        $slickOpts['zIndex'] = strider_media_cast_int($attr['slzindex']);
    }

    if (!empty($attr['slasnavfor'])) {
        $slickOpts['asNavFor'] = "striderslider-" . $attr['slasnavfor'];
    }


    $id = intval($atts['id']);

    if (!empty($atts['include'])) {
        $_attachments = get_posts(array('include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($atts['exclude'])) {
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
    }

    if (empty($attachments)) {
        return '';
    }

    if (!empty($atts['instance'])) {
        $selector = "striderslider-{$atts['instance']}";
    } else {
        $selector = "striderslider-{$instance}";
    }

    if ($atts['hasclass'] == 'false') {
        $output = '<div id="' . $selector . '" data-slick=\'' . json_encode($slickOpts) . '\'>';
    } else {
        $output = '<div id="' . $selector . '" class="strider-slider" data-slick=\'' . json_encode($slickOpts) . '\'>';
    }
    $i = 0;
    foreach ( $attachments as $id => $attachment ) {

	    $attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
        $attr['class'] = 'img-responsive'; // xxx
	    if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
            $image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
        } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
            $image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
	    } else {
            $image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
	    }
	    //$image_meta  = wp_get_attachment_metadata( $id );
                $output .= '<div class="striderslide-item">';
                $output .= $image_output;
                if ($atts['showtitle'] == 'true') {
                    $output .= '<h4>' . get_the_excerpt($id) . '</h4>';
                }
                $output .= '</div>';
    }

    $output .= "
          </div>\n";

    return $output;
}
add_shortcode('striderslider', 'strider_media');