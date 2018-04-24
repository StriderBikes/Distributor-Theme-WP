<?php
/*
 * Custom Post Types:
 *
 *
 *
 * Taxonomies:
 *
 *
 *
 *
 *
 *
 */


class StriderLoc {

    public $module;
    private $labels;
    private $args;
    private $namespace = 'sb';

    private $meta_fields;
    private $address_fields;
    private $geo_fields;

    public function init() {
        $this->module = 'location';
        $this->label = 'Location';

        $this->labels = array(
            'name'                  => $this->label . 's',
            'singular_name'         => $this->label,
            'menu_name'             => $this->label . 's',
            'parent_item_colon'     => '',
            'all_items'             => 'All ' . $this->label . 's',
            'view_item'             => 'View ' . $this->label,
            'add_new_item'          => 'Add New ' . $this->label,
            'add_new'               => 'New ' . $this->label,
            'edit_item'             => 'Edit ' . $this->label,
            'update_item'           => 'Update ' . $this->label,
            'search_items'          => 'Search ' . strtolower($this->label) . 's',
            'not_found'             => 'No ' . strtolower($this->label) . 's found',
            'not_found_in_trash'    => 'No ' . strtolower($this->label) . 's found in Trash',
        );

        $this->args = array(
            'labels'                => $this->labels,
            'supports'              => array('thumbnail', 'revisions'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'menu_position'         => 5,
            'can_export'            => true,
            'has_archive'           => strtolower($this->label) . 's',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'taxonomies'            => array(),
            'rewrite'		        => true,
        );

        $this->meta_fields = array(
            array(
                'label'	=> 'Name',
                'desc'	=> 'Name',
                'id'         => $this->field_name('name'),
                'type'       => 'text',
                'required'   => false,
                'maxlen'     => 100
            ),
            array(
                'label'	=> 'Phone Number',
                'desc'	=> 'Phone Number',
                'id'         => $this->field_name('phone'),
                'type'       => 'text',
                'required'   => false,
                'maxlen'     => 50
            ),
            array(
                'label'	=> 'Email Address',
                'desc'	=> 'Email Address',
                'id'         => $this->field_name('email'),
                'type'       => 'text',
                'required'   => false,
                'maxlen'     => 100
            ),
            array(
                'label'	=> 'Website',
                'desc'	=> 'Website',
                'id'         => $this->field_name('website'),
                'type'       => 'text',
                'required'   => false,
                'maxlen'     => 50
            ),
        );

        $this->address_fields = array(
            array(
                'label'	=> 'Street Address',
                'desc'	=> 'Street Address of the Location',
                'id'		=> $this->field_name('address'),
                'type'	=> 'text',
                'required'	=> true
            ),
            array(
                'label'	=> 'Address Line 2',
                'desc'	=> '',
                'id'		=> $this->field_name('address2'),
                'type'	=> 'text',
                'required'	=> true
            ),
            array(
                'label'	=> 'City',
                'desc'	=> 'City',
                'id'		=> $this->field_name('city'),
                'type'	=> 'text',
                'required'	=> false
            ),
            array(
                'label'	=> 'State / Province / Region',
                'desc'	=> 'State / Province / Region',
                'id'		=> $this->field_name('region'),
                'type'	=> 'text',
                'required'	=> true
            ),
            array(
                'label'	=> 'Postal / Zip Code',
                'desc'	=> 'Postal / Zip Code',
                'id'		=> $this->field_name('postal_code'),
                'type'	=> 'text',
                'required'	=> true
            ),
            array(
                'label'	=> 'Country',
                'desc'	=> 'Country',
                'id'		=> $this->field_name('country'),
                'type'	=> 'text',
                'required'	=> true
            ),
        );

        $this->geo_fields = array(
            array(
                'label'	=> 'Latitude',
                'desc'	=> 'Latitude',
                'id'		=> $this->field_name('geo_lat'),
                'type'	=> 'text',
                'required'	=> true
            ),
            array(
                'label'	=> 'Longitude',
                'desc'	=> 'Longitude',
                'id'		=> $this->field_name('geo_lng'),
                'type'	=> 'text',
                'required'	=> true
            ),
        );

        add_action('init', array($this, 'do_init'), 0);

        $sf = new StriderLocFrontend();
        $sf->init();

        $sem = new StriderLocAdmin();
        $sem->addFields('location_meta_box', 'General Location Options', $this->meta_fields);
        $sem->addFields('location_address_box', 'Location Address', $this->address_fields);
        $sem->init();
    }

    public function do_init() {
        register_post_type($this->module, $this->args);

        // XXX should move this to admin or.. frontend needs it?
        // id, region, city, address
        add_rewrite_tag('%sb_location_id%', '([a-z0-9]+)', 'location_id=');    // in hex or dec
        add_rewrite_tag('%sb_location_region%', '([^/]+)', 'location_region=');
        add_rewrite_tag('%sb_location_city%', '([^/]+)', 'location_city=');
        add_rewrite_tag('%sb_location_address%', '([^/]+)', 'location_address=');
//        add_rewrite_tag('%location_id%','([^/]+)', 'location_id=');
        add_permastruct('location', '/locations/%sb_location_id%/%sb_location_region%/%sb_location_city%/%sb_location_address%', false);

        add_action('template_include', array($this, 'template_include'), 1, 1);

        flush_rewrite_rules();
    }

    public function template_include($template) {
        if (get_post_type() != 'location') {
            return $template;
        }

        $object = get_queried_object();
        $templates = array();
        if (!empty($object->post_type)) {
            $template = get_page_template_slug($object);
            /*
             * Verify this short-circuits for a custom template
             */
            if ($template && validate_file($template) === 0) {
                $templates[] = $template;
            }

            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "single-{$object->post_type}-{$name_decoded}.php";
            }

            $templates[] = "single-{$object->post_type}-{$object->post_name}.php";
            $templates[] = "single-{$object->post_type}.php";
        } else if (is_archive()) {
            $templates[] = 'archive-' . strtolower($this->label) . '.php';
            $templates[] = 'archive.php';
        } else {
            // XXX do what here?

            // $templates[] = strtolower($this->label);
        }



        // XXX: are we going to foot-shoot by defaulting to the passed-in template?
//        $located = '';
        $located = $template;
        foreach ((array)$templates as $template_name) {
            if (!$template_name) {
                continue;
            }

            $child_theme_template_path = trailingslashit(STYLESHEETPATH) . trailingslashit('strider-templates') . $template_name;
            $parent_theme_template_path = trailingslashit(TEMPLATEPATH) . trailingslashit('strider-templates') . $template_name;
            $plugin_template_path = plugin_dir_path(__FILE__) . trailingslashit('templates') . $template_name;

            if (file_exists($child_theme_template_path)) {
                $located = $child_theme_template_path;
                break;
            } else if (file_exists($parent_theme_template_path)) {
                $located = $parent_theme_template_path;
                break;
            }  else if (file_exists($plugin_template_path)) {
                $located = $plugin_template_path;
                break;
            }
        }

        return $located;
    }

    public function post_link($permalink, $post_id, $leavename) {

    }

    private function field_name($name) {
        return $this->namespace . '_' . $this->module . '_' . $name;
    }
}

function strider_locator_get_post_meta($post_id) {
    $temp_meta = get_post_meta($post_id);
    $meta = array();
    foreach ($temp_meta as $temp => $temp_data) {
        $temp = str_replace('sb_location_', '', $temp);
        $meta[$temp] = $temp_data[0];
    }
    return $meta;
}

function strider_locator_get_template_part($slug, $name = null) {

    $templates = array();
    $name = (string) $name;
    if ($name != '')
        $templates[] = "{$slug}-{$name}.php";

    $templates[] = "{$slug}.php";

    $located = '';
    foreach ((array)$templates as $template_name) {
        if (!$template_name) {
            continue;
        }

        $child_theme_template_path = trailingslashit(STYLESHEETPATH) . trailingslashit('strider-templates') . $template_name;
        $parent_theme_template_path = trailingslashit(TEMPLATEPATH) . trailingslashit('strider-templates') . $template_name;
        $plugin_template_path = plugin_dir_path(__FILE__) . trailingslashit('templates') . $template_name;

        if (file_exists($child_theme_template_path)) {
            $located = $child_theme_template_path;
            break;
        } else if (file_exists($parent_theme_template_path)) {
            $located = $parent_theme_template_path;
            break;
        }  else if (file_exists($plugin_template_path)) {
            $located = $plugin_template_path;
            break;
        }
    }

    if (!empty($located)) {
        load_template($located, false);
    } else {
        get_template_part($slug, $name);
    }
}

function strider_locator_activate() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'strider_locator_activate');
register_deactivation_hook(__FILE__, 'strider_locator_activate');