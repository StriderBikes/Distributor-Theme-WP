<?php

get_header();

?>
    <div class="container">
<h2>YYY: Single Location</h2>
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
<?php

get_footer();