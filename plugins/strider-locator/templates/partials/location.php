<?php

$meta = strider_locator_get_post_meta($post->ID);
//var_dump($post);
//var_dump($meta);
if (empty($meta['name'])) {
    return;
}

?>
<div class="col-xs-12 col-sm-6 col-md-4 location-panel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php echo $meta['name']; ?>
            </h3>
        </div>
        <div class="panel-body">
            <?php
            $loc = '';
            if (!empty($meta['address'])) {
                $loc .= $meta['address'] . '<br>';
            }
            if (!empty($meta['address2'])) {
                $loc .= $meta['address2'] . '<br>';
            }

            if (!empty($meta['city']) && !empty($meta['region'])) {
                $loc .= $meta['city'] . ', ' . $meta['region'] . ' ';
            } else if (!empty($meta['city'])) {
                $loc .= $meta['city'] . ' ';
            } else if (!empty($meta['region'])) {
                $loc .= $meta['region'] . ' ';
            }

            if (!empty($meta['postal_code'])) {
                $loc .= $meta['postal_code'] . '<br>';
            } else if (!empty($meta['city']) || !empty($meta['region'])) {
                $loc .= '<br>';
            }

            if (!empty($meta['country'])) {
                $loc .= $meta['country'] . '<br>';
            }

            if (!empty($loc)) {
                echo $loc . '';
            }

            echo '<hr>';

            if (!empty($meta['phone'])) {
                echo '<span class="glyphicon glyphicon-earphone"></span> <strong>' . $meta['phone'] . '</strong><br>';
            }
            if (!empty($meta['email'])) {
                echo '<a href="mailto:' . $meta['email'] . '"><span class="glyphicon glyphicon-envelope"></span> <strong>' . $meta['email'] . '</strong></a><br>';
            }
            if (!empty($meta['website'])) {
                echo '<a href="' . $meta['website'] . '" target="_blank"><span class="glyphicon glyphicon-globe"></span> <strong>' . $meta['website'] . '</strong></a><br>';
            }
            ?>
        </div>
    </div>
</div>