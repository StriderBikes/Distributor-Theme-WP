<?php

if (!defined('WPINC'))
    die();

require get_template_directory() . '/vendor/dreamery/autoload.php';

if (!function_exists('strider_theme_support_html5')) {
    function strider_theme_support_html5() {
        /*
         * Add support for HTML5 Semantic Markup
         *
         * XXX
         * Do we need extra CSS for this? <figure> ? <figcaption> ?
         */
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    }
    add_action('after_setup_theme', 'strider_theme_support_html5');
}

if (!function_exists('strider_theme_support_title_tag')) {
    function strider_theme_support_title_tag() {
        add_theme_support('title-tag');
    }
    add_action('after_setup_theme', 'strider_theme_support_title_tag');
}

/**
 * Customize gallery output
 *
 * This is based on the core WordPress function
 */
function strider_post_gallery($string, $attr){
    $post = get_post();

    if (!empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    $defaults = array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post ? $post->ID : 0,
        'itemtag'    => 'figure',
        'icontag'    => 'div',
        'captiontag' => 'figcaption',
        'columns'    => 3,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => '',
        'link'       => ''
    );
    $atts = shortcode_atts($defaults, $attr, 'gallery');

    $column_class = 'col-xs-4';
    switch ($atts['columns']) {
        /* Standard column widths */
        case 1:
            $column_class = 'col-xs-12';
            break;
        case 2:
            $column_class = 'col-xs-6';
            break;
        case 3:
            $column_class = 'col-xs-4';
            break;
        case 4:
            $column_class = 'col-xs-3';
            break;
        case 6:
            $column_class = 'col-xs-2';
            break;

        /* Non-standard widths */
        case 5:
            $column_class = 'origin-col-one-fifth';
            break;
        case 7:
            $column_class = 'origin-col-one-seventh';
            break;
        case 8:
            $column_class = 'origin-col-one-eighth';
            break;
        case 9:
            $column_class = 'origin-col-one-ninth';
            break;

        default:
            break;
    }

    $id = intval($atts['id']);

    if (!empty($atts['include'])) {
        $_attachments = get_posts(array('include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( ! empty( $atts['exclude'] ) ) {
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
    }

    if (empty($attachments)) {
        return '';
    }

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment) {
            $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
        }
        return $output;
    }

    $itemtag = tag_escape($atts['itemtag']);
    $captiontag = tag_escape($atts['captiontag']);
    $icontag = tag_escape($atts['icontag']);
    $valid_tags = wp_kses_allowed_html('post');
    if (!isset($valid_tags[$itemtag])) { $itemtag = $defaults['itemtag']; }
    if (!isset($valid_tags[$captiontag])) { $captiontag = $defaults['captiontag']; }
    if (!isset($valid_tags[$icontag])) { $icontag = $defaults['icontag']; }

    $columns = intval($atts['columns']);

    $size_class = sanitize_html_class($atts['size']);
    $gallery_div = "<div class='strider-gallery gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

    /**
     * Filters the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters('gallery_style', $gallery_div);

    foreach ($attachments as $id => $attachment) {
        $attr = array();
        $attr['class'] = 'img-responsive attachment-' . $atts['size'] . ' size-' . $atts['size'];
        if (!empty($atts['link']) && 'file' === $atts['link']) {
            $image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
        } elseif (!empty($atts['link']) && 'none' === $atts['link']) {
            $image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
        } else {
            $image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
        }
        $image_meta  = wp_get_attachment_metadata($id);

        $orientation = '';
        if (isset($image_meta['height'], $image_meta['width'])) {
            $orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
        }
        $output .= '<' . $itemtag . ' class="figure gallery-item ' . $column_class . '">';
        $output .= "
			<{$icontag} class='figure-img gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
        if ($captiontag && trim($attachment->post_excerpt)) {
            $output .= "
				<{$captiontag} class='figure-caption wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
    }

    $output .= "
		</div>\n";

    return $output;
}
add_filter('post_gallery', 'strider_post_gallery', 10, 2);


/*
 * Enqueue our css/js assets
 */
if (!function_exists('strider_enqueue_assets')) {
    function strider_enqueue_assets() {
        wp_enqueue_style('bootstrap3', get_template_directory_uri() . '/assets/bootstrap3/css/bootstrap.css');
        wp_enqueue_style('strider', get_template_directory_uri() . '/assets/css/screen.css', array('bootstrap3'));

        wp_enqueue_script('bootstrap3', get_template_directory_uri() . '/assets/bootstrap3/js/bootstrap.js', array('jquery'));
    }

    add_action('wp_enqueue_scripts', 'strider_enqueue_assets');
}
if (!function_exists('strider_admin_enqueue_assets')) {
    function strider_admin_enqueue_assets() {
        add_editor_style(get_template_directory_uri() . '/assets/bootstrap3/css/bootstrap.css');
        add_editor_style(get_template_directory_uri() . '/assets/css/screen.css');
    }
    add_action('admin_init', 'strider_admin_enqueue_assets');
}

/*
 * Add img-responsive to all WP-managed images
 */
if (!function_exists('strider_image_tag_class')) {
    function strider_image_tag_class($class) {
        return $class . ' img-responsive';
    }

    add_filter('get_image_tag_class', 'strider_image_tag_class');
}


if (!function_exists('strider_register_navigation')) {
    function strider_register_navigation() {
        $locations = array(
            'strider_navigation_menu_primary' =>     __('Primary Menu', 'strider-distributor'),
        );
        register_nav_menus($locations);
    }

    add_action('init', 'strider_register_navigation');
}


if (!function_exists('strider_widgets_init')) {
    function strider_widgets_init() {
        register_sidebar(array(
            'name'          => 'Strider Difference',
            'id'            => 'strider_difference',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
        ));
        register_sidebar(array(
            'name'          => 'Strider Comparison Chart',
            'id'            => 'strider_comparison_chart',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
        ));
    }
    add_action('widgets_init', 'strider_widgets_init');
}

if (!function_exists('strider_shortcode_comparison_chart')) {
    function strider_shortcode_comparison_chart($atts) {
        ob_start();
        if (is_active_sidebar('strider_comparison_chart')) {
            dynamic_sidebar('strider_comparison_chart');
        } else {
            get_template_part('partials/comparison-chart');
        }
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }
    add_shortcode('strider_comparison_chart', 'strider_shortcode_comparison_chart');
}

/*
 * Rename "link", add another "img"
 * Take attributes for class(es), target, label, url
 */
if (!function_exists('strider_shortcode_baseurl')) {
    function strider_shortcode_baseurl($atts) {
        return get_bloginfo('wpurl');
    }
    add_shortcode('strider_baseurl', 'strider_shortcode_baseurl');
}

if (!function_exists('strider_shortcode_img')) {
    function strider_shortcode_baseurl_img($atts) {
        $atts = shortcode_atts(array(
            'src' =>    '#',
            'class' =>  '',
            'alt' =>    '',
            'style' =>  '',
        ), $atts);

        $atts['class'] .= ' img-responsive';

        $tpl = '<img src="%s/wp-content/uploads/%s" class="%s" alt="%s" style="%s" />';
        $ret = sprintf($tpl, get_bloginfo('wpurl'), $atts['src'], $atts['class'], $atts['alt'], $atts['style']);
        return $ret;
    }
    add_shortcode('strider_baseurl_img', 'strider_shortcode_baseurl_img');
}

if (!function_exists('strider_shortcode_link')) {
    function strider_shortcode_baseurl_link($atts) {
        $atts = shortcode_atts(array(
            'href' =>   '#',
            'class' =>  '',
            'target' => '_self',
            'label' =>  '',
        ), $atts);

        $tpl = '<a href="%s%s" class="%s" target="%s">%s</a>';
        $ret = sprintf($tpl, get_bloginfo('wpurl'), $atts['href'], $atts['class'], $atts['target'], $atts['label']);
        return $ret;
    }
    add_shortcode('strider_baseurl_link', 'strider_shortcode_baseurl_link');
}

if (!function_exists('strider_shortcode_imglink')) {
    function strider_shortcode_baseurl_imglink($atts) {
        $atts = shortcode_atts(array(
            'href' =>   '#',
            'class' =>  '',
            'target' => '_self',
            'src' =>    '',
            'alt' =>    '',
        ), $atts);

        $tpl = '<a href="%s%s" class="%s" target="%s"><img src="%s/wp-content/uploads/%s" alt="%s" class="img-responsive" /></a>';
        $ret = sprintf($tpl, get_bloginfo('wpurl'), $atts['href'], $atts['class'], $atts['target'], get_bloginfo('wpurl'), $atts['src'], $atts['alt']);
        return $ret;
    }
    add_shortcode('strider_baseurl_imglink', 'strider_shortcode_baseurl_imglink');
}

/*
 * Ensure the above short codes run in widgets
 */
add_filter('widget_text','do_shortcode');


/*
 *
 *
 * Admin
 *
 *
 */

if (!function_exists('strider_admin_register_customizations')) {
    function strider_admin_register_customizations($wp_customize) {
        $wp_customize->add_section('strider_social_links', array(
            'title'         => 'Social Networks',
            'description'   => 'Full Links to Social Networks',
        ));

        /*
         * Enable/Disable
         */
        $wp_customize->add_setting('strider_social_links_enabled', array(
            'default' => 'disabled',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_links_enabled',
                array(
                    'label'          => 'Enable Social Links?',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_links_enabled',
                    'type'           => 'radio',
                    'choices'        => array(
                        'enabled'       => 'Enabled',
                        'disabled'      => 'Disabled',
                    )
                )
            )
        );

        /*
         * Facebook
         */
        $wp_customize->add_setting('strider_social_link_facebook', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_facebook',
                array(
                    'label'          => 'Facebook URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_facebook',
                    'type'           => 'text',
                )
            )
        );

        /*
         * Instagram
         */
        $wp_customize->add_setting('strider_social_link_instagram', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_instagram',
                array(
                    'label'          => 'Instagram URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_instagram',
                    'type'           => 'text',
                )
            )
        );

        /*
         * Twitter
         */
        $wp_customize->add_setting('strider_social_link_twitter', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_twitter',
                array(
                    'label'          => 'Twitter URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_twitter',
                    'type'           => 'text',
                )
            )
        );

        /*
         * Pinterest
         */
        $wp_customize->add_setting('strider_social_link_pinterest', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_pinterest',
                array(
                    'label'          => 'Pinterest URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_pinterest',
                    'type'           => 'text',
                )
            )
        );

        /*
         * Tumblr
         */
        $wp_customize->add_setting('strider_social_link_tumblr', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_tumblr',
                array(
                    'label'          => 'Tumblr URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_tumblr',
                    'type'           => 'text',
                )
            )
        );

        /*
         * Flickr
         */
        $wp_customize->add_setting('strider_social_link_flickr', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_flickr',
                array(
                    'label'          => 'Flickr URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_flickr',
                    'type'           => 'text',
                )
            )
        );

        /*
         * YouTube
         */
        $wp_customize->add_setting('strider_social_link_youtube', array(
            'default' => '',
        ));
        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'strider_social_link_youtube',
                array(
                    'label'          => 'YouTube URL',
                    'section'        => 'strider_social_links',
                    'settings'       => 'strider_social_link_youtube',
                    'type'           => 'text',
                )
            )
        );
    }

    add_action('customize_register', 'strider_admin_register_customizations');
}
