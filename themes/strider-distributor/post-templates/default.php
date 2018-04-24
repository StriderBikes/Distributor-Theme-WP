<?php
/**

 */

get_header();
get_template_part('partials/navigation-primary');

?>
    <div class="container">
        <?php

        while (have_posts()) {
            the_post();
            the_content();
        }

        ?>
    </div>
<?php

get_footer();