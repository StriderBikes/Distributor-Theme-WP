<?php
/*
 * Support short codes for all locations, individual locations by id
 *
 * [locations]
 * [location id=""]
 */

class StriderLocFrontend
{
    public $module;

    private $namespace = 'sb';

    public function __construct()
    {
        $this->module = 'location';




        add_action('pre_get_posts', array($this, 'custom_posts_per_page'));
    }

    public function custom_posts_per_page($query) {
        if ($query->is_archive() && $query->is_main_query() && $query->query['post_type'] == $this->module) {
            $query->set('posts_per_page', -1);
        }
    }

    public function init()
    {
        add_shortcode($this->namespace . '_' . $this->module . 's', array($this, 'sc_locations'));
    }

    public function sc_locations() {
        $query = new WP_Query(array('post_type' => $this->module));
        while ($query->have_posts()) {
            $query->the_post();
            strider_locator_get_template_part('partials/location');
        }
        //wp_reset_query();
    }

    /*
    public function sc_location_by_id() {

    }
    */
}
