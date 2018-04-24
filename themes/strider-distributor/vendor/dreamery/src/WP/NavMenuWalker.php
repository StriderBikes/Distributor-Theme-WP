<?php

namespace Dreamery\WP;

use Exception;

/* WP Global */
use Walker;

class NavMenuWalker extends Walker {
    public $tree_type = array('post_type', 'taxonomy', 'custom');

    /**
     * Database fields to use, same as the default WordPress Walker_Nav_Menu class
     *
     * YYY:
     * is this really being used?
     */
    public $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');

    /*
     * 
     * YYY:
     * This function has exceedingly gross use of array's, might be better to simply use
     * objects, even though templates incur a runtime performance hit when object support
     * is enabled
     */
    public function walk($elements, $max_depth) {

        $elementById = array();
        $elementHierKey = null;
        $elementRootIds = array();
        $elementRoots = array();

        /* Build our ById array of menu elements */
        $counter = 0;
        foreach ($elements as $element) {
            $elementById[$element->ID] = array(
                'url' => $element->url,
                'title' => $element->title,
                'target' => $element->target,
                'has_target' => (empty($element->target)) ? false : true,
                'classes' => trim(implode(' ', $element->classes)),
                'attr_title' => $element->attr_title,
                'has_attr_title' => (empty($element->attr_title)) ? false : true,
                'children' => array(),
                'children_ids' => array(),
                'has_children' => (in_array('menu-item-has-children', $element->classes)) ? true : false,
                'is_root' => ($element->menu_item_parent == 0) ? true : false,
                'parent_id' => $element->menu_item_parent,
            );
            if ($elementById[$element->ID]['has_children'] && $elementById[$element->ID]['url'] != '#') {
                $temp_element = $elementById[$element->ID];
                $temp_element['has_children'] = false;
                $temp_element['parent_id'] = $element->ID;
                $temp_element['classes'] = trim(str_replace('menu-item-has-children', '', $temp_element['classes']));
                $temp_element['is_root'] = false;
                $elementById[$element->ID . '-' . $counter] = $temp_element;
                $counter++;
                $elementById[$element->ID]['url'] = '#';
            }
        }

        /* Wire up a hierarchy */
        foreach ($elementById as $key => $element) {
            if ($element['is_root']) {
                $elementRootIds[] = $key;
                continue;
            }

            $elementById[$element['parent_id']]['children_ids'][] = $key;
        }

        foreach ($elementRootIds as $root_id) {
            $elementRoots[] = $this->assembleChildren($elementById, $elementById[$root_id], $max_depth);
        }

        /* Throw away the bits we no longer need */
        unset($elementById);

        $output = '';
//        $t = new Template();
//        foreach ($elementRoots as $element) {
//            $output .= $t->render('navigation-menu-element', $element);
//        }


        foreach ($elementRoots as $element) {
            $output .= self::render('navigation-menu-element', $element);
        }
        return $output;
    }

    /* Assemble the hierarchy */
    private function assembleChildren($elementById, $element, $max_depth, $depth = 0) {
        if ($depth >= $max_depth) {
            return $element;
        }
        $depth++;
        foreach ($element['children_ids'] as $child_id) {
            $element['children'][] = $this->assembleChildren($elementById, $elementById[$child_id], $max_depth, $depth);
        }
        return $element;
    }

    /*
     *
     * YYY:
     * Does something need to be done with before, after, link_before, link_after, or items_wrap
     * as passed to us in $args?
     */
    public static function fallback($args) {
        if (current_user_can('manage_options')) {
            $options = array_intersect_key($args, array('container' => true, 'container_id' => true,
                'container_class' => true, 'menu_id' => true, 'menu_class' => true));

            $options['has_container'] = (empty($options['container'])) ? false : true;

            $options['has_container_id_class'] = false;
            $options['has_container_id'] = false;
            $options['has_container_class'] = false;
            if (!empty($options['container_id']) && !empty($options['container_class']))
                $options['has_container_id_class'] = true;
            if (!empty($options['container_id']))
                $options['has_container_id'] = true;
            if (!empty($options['container_class']))
                $options['has_container_class'] = true;

            $options['has_menu_id_class'] = false;
            $options['has_menu_id'] = false;
            $options['has_menu_class'] = false;
            if (!empty($options['menu_id']) && !empty($options['menu_class']))
                $options['has_menu_id_class'] = true;
            if (!empty($options['menu_id']))
                $options['has_menu_id'] = true;
            if (!empty($options['menu_class']))
                $options['has_menu_class'] = true;

            $menus = get_registered_nav_menus();
            $options['menu_theme_location_ident'] = $args['theme_location'];
            $options['menu_theme_location'] = $menus[$options['menu_theme_location_ident']];

            // get compiler
        //    $t = new Template();
        //    echo $t->render('navigation-menu-nomenu', $options);

//            var_dump($options);
//            self::render($options);
            echo self::renderMenuNoMenu();
        }
    }

    public static function render($name, $options) {


        $menu = '<ul class="nav navbar-nav">' . self::renderMenuElement($name, $options) . '</ul>';
        return $menu;
    }

    public static function renderMenuElement($name, $options) {
$tpl = <<<EOL
<li class="%li_class%">
    <a class="%a_class%" href="{{url}}" %attr_target% %attr_title%>{{title}}</a>
</li>
EOL;
$tpl_dropdown = <<<EOL
<li class="%li_class%">
    {{level}}
</li>
EOL;

        if ($options['has_children']) {
            $ret = $tpl_dropdown;
            $children = self::renderMenuLevel($options['title'], $options['children']);
            $ret = str_replace('{{level}}', $children, $ret);
        } else {
            $ret = $tpl;
            $ret = str_replace('{{url}}', $options['url'], $ret);
            $ret = str_replace('{{title}}', $options['title'], $ret);
        }

        return $ret;
    }

    public static function renderMenuDropdown($children) {
$tpl = <<<EOL
<li class="{{classes}}">
    <a class="nav-link" href="{{url}}">{{title}}</a>
</li>
EOL;

        $ret = '';
        foreach ($children as $child) {
            $ret_child = $tpl;
            $ret_child = str_replace('{{url}}', $child['url'], $ret_child);
            $ret_child = str_replace('{{title}}', $child['title'], $ret_child);
            $ret .= $ret_child;
/*
            echo '<pre>';
            var_dump($child);
            echo "-----------\n";
            echo $ret_child;
            echo '</pre>';
*/
        }

        return $ret;
    }

    public static function renderMenuLevel($title, $children) {
$tpl = <<<EOL
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{title}} <span class="caret"></span></a>
    <ul class="dropdown-menu">
        {{children}}
    </ul>
</li>
EOL;

        $ret = str_replace('{{title}}', $title, $tpl);
        return str_replace('{{children}}', self::renderMenuDropDown($children), $ret);
    }

    public static function renderMenuNoMenu() {
        /*
{{#has_container}}
  {{#if has_container_id_class}}
<{{container}} id="{{container_id}}" class="{{container_class}}">
  {{else if has_container_id}}
<{{container}} id="{{container_id}}">
  {{else if has_container_class}}
<{{container}} class="{{container_class}}">
  {{else}}
<{{container}}>
  {{/if}}
{{/has_container}}
{{#if has_menu_id_class}}
<ul id="{{menu_id}}" class="{{menu_class}}">
{{else if has_menu_id}}
<ul id="{{menu_id}}">
{{else if has_menu_class}}
<ul class="{{menu_class}}">
{{else}}
<ul>
{{/if}}
  <li class="nav-item"><a class="nav-link btn btn-lg btn-error" href="{{wp_admin_url path='nav-menus.php'}}">Create or Assign <strong><em>{{menu_theme_location}}</em></strong></a></li>
</ul>
{{#has_container}}
</{{container}}>
{{/has_container}}
         */
$tpl = <<<EOL

EOL;

    }
}