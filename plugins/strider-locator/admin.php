<?php
/*
 *
 */

class StriderLocAdmin
{
    public $module;

    private $namespace = 'sb';
    private $fields;

    public function __construct() {
        $this->module = 'location';
    }

    public function init() {
        if (!is_admin()) {
            return;
        }

        add_filter('manage_edit-' . $this->module . '_columns', array($this, 'edit_columns'), 10, 1);
        add_action('manage_' . $this->module . '_posts_custom_column', array($this, 'show_columns'), 10, 2);
        add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'), 10, 1);
        add_action('save_post', array($this, 'save_custom_meta'), 10, 1);

        add_filter('post_link', array($this, 'permalink'), 10, 3);
        add_filter('post_type_link', array($this, 'permalink'), 10, 3);

//        add_filter('name_save_pre', array($this, 'pre'));
    }

    public function pre($post_name) {
        // only works if AJAX not working?

        // check if its a location save?  YES!
        // check that we're dealing with a product, and editing the slug
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
        $new_title = isset($_POST['post_title']) ? $_POST['post_title'] : 0;

        if ($post_id && $post_name === '') {
            $post = get_post($post_id);
            if ($post->post_type == $this->module && $post->post_status != 'auto-draft') {
                // generate new slug
                $post_name = 'zzzzzzzzzzzzz';
//                $post_name = generateProductSlug($post, $new_title);
            }
        }

        return $post_name;
    }

    public function addFields($id, $title, $fields) {
        $callback = function() use ($fields) {
            global $post;
            $this->render_admin_options($fields, $post);
        };

        $field = array(
            'id' => $id,
            'title' => $title,
            'callback' => $callback,
            'page' => $this->module,
            'context' => 'normal',
            'priority' => 'high',
            'fields' => $fields,
        );
        $this->fields[] = $field;
    }

    public function edit_columns($columns)
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'date' => 'Date'
        );

        return ($columns);
    }

    public function show_columns($column, $post_id)
    {
        global $post;

        switch($column) {
            case 'address':
                $field = get_post_meta($post_id, $this->field_name($column), true);
                if (empty($field))
                    $field = 'Unknown';
                printf('<a href="' . get_admin_url() . 'post.php?post=' . $post->ID . '&amp;action=edit">%s</a>', $field);
                break;
            case 'city':
            case 'state':
            case 'zip':
                $field = get_post_meta($post_id, $this->field_name($column), true);
                if (empty($field))
                    print 'Unknown';
                else
                    printf('%s', $field);
                break;
            default:
                break;
        }
    }

    public function render_admin_options($data, $post)
    {
        // Begin the field table and loop
        echo '<table class="form-table">';
        foreach ($data as $field) {
            // get value of this field if it exists for this post
            $meta = get_post_meta($post->ID, $field['id'], true);
            // begin a table row with
            echo '<tr>
				<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
				<td>';
            switch($field['type']) {
                // case items will go here
// text
                case 'text':
                    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
		<br /><span class="description">'.$field['desc'].'</span>';
                    break;
// textarea
                case 'textarea':
                    echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
		<br /><span class="description">'.$field['desc'].'</span>';
                    break;
// select
                case 'select':
                    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                    echo '<option value="">Select a Value</option>';
                    if ($field['id'] == 'realestate_county') {
                        // YYY: Hack to handle counties, which are unpopulated by default (cannot be set from admin)
                        echo '<option selected="selected" value="' . $meta . '">' . $meta . '</option>';
                    } else {
                        foreach ($field['options'] as $option) {
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                    }
                    echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
            } //end switch
            echo '</td></tr>';
        } // end foreach
        echo '</table>'; // end table
    }

    public function add_custom_meta_boxes()
    {
        foreach ($this->fields as $field) {
            add_meta_box(
                $field['id'],
                $field['title'],
                $field['callback'],
                $field['page'],
                $field['context'],
                $field['priority']
            );
        }
    }



// Save the Data
    public function save_custom_meta($post_id)
    {

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return ($post_id);
        }

        // check permissions
        // XXX: under what conditions is post_type set? OK to check it here?
        if (!empty($_POST['post_type']) && $_POST['post_type'] == 'page') {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

//        return;

        // XXX
        // loop through fields and save the data
        foreach ($this->fields as $fields) {
            $cur_fields = $fields['fields'];
            foreach ($cur_fields as $field) {
                if (empty($_POST[$field['id']])) {
                    continue;
                }
                $old = get_post_meta($post_id, $field['id'], true);
                // strip underscores for permalink goodness
                $new = str_replace('_', '', $_POST[$field['id']]);
                if ($new && $new != $old) {
                    update_post_meta($post_id, $field['id'], $new);
                } elseif ($new == '' && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                }
            }
        }

    }

    /*
     * /HEX ID/region/city/address1
     *
     * HEX ID: 6-digit hexadecimal identifier, supporting 16M locations
     *
     * %field% not in passed-in permalink? XXX
     */
    public function permalink($permalink, $post_id, $leavename)
    {
        $post = get_post($post_id);
        if (!$post) {
//            echo '<pre> XXX Returning early notpost </pre>';
            return ($permalink);
        }

//        echo '<pre> pre-permalink: ' . $permalink . ' </pre>';

        foreach (array($this->field_name('id'),
                       $this->field_name('region'),
                       $this->field_name('city'),
                       $this->field_name('address')) as $field)
        {
//            echo 'FIELD: ' . $field;
            $field_value = get_post_meta($post->ID, $field, true);
//            echo 'FIELD VALUE: ' . $field_value;
            if (!$field_value) {
                $field_value = '';
            }
            if ($field_value == '' && $field == $this->field_name('id')) {
                $field_value = 'xyxyxy';
            }
            $field_value = urlencode(str_replace(' ', '_', substr($field_value, 0, 50)));
            $permalink = str_replace('%' . $field . '%', $field_value, $permalink);
        }

        /*
         * Replace any duplicate //'s with a single / without affecting
         * the // in the protocol specification
         */
        $permalink = str_replace('://', ':::', $permalink);
        $permalink = str_replace('//', '/', $permalink);
        $permalink = str_replace(':::', '://', $permalink);

//        echo '<pre> permalink: ' . $permalink . ' </pre>';

        return ($permalink);
    }

    private function field_name($name) {
        return $this->namespace . '_' . $this->module . '_' . $name;
    }
}



/*

function realestate_permalink($permalink, $post_id, $leavename)
{
    global $realestate_field_prefix;
    $field_prefix = $realestate_field_prefix;

    $post = get_post($post_id);
    if (!$post) return ($permalink);

    foreach (array('address', 'state', 'city', 'id') as $field) {
        $field_value = get_post_meta($post->ID, $field_prefix . $field, true);
        if (!$field_value)
            $field_value = '';
        $field_value = urlencode(str_replace(' ', '_', substr($field_value, 0, 50)));
        $permalink = str_replace('%' . $field_prefix . $field . '%', $field_value, $permalink);
    }

    $permalink = str_replace('://', ':::', $permalink);
    $permalink = str_replace('//', '/', $permalink);
    $permalink = str_replace(':::', '://', $permalink);

    return ($permalink);
}
add_filter('post_link', 'realestate_permalink', 10, 3);
add_filter('post_type_link', 'realestate_permalink', 10, 3);



*/