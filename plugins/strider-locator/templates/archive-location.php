<?php

get_header();

?>
    <style>
    .location-panel:nth-child(3n+1) {
        clear: left;
    }
    </style>
    <div class="container">
        <br>
        <div class="row">
            <?php

            while (have_posts()) {
                the_post();
                strider_locator_get_template_part('partials/location');
            }
            ?>
        </div>
    </div>

<br><br>
<?php

get_footer();