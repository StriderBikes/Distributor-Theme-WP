<div id="primary-navigation">
    <nav class="navbar navbar-inverse" role="navigation">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- <a class="navbar-brand" href="#">Brand</a> -->
                    <?php
                    /*
            <a class="navbar-brand" href="<?php echo home_url(); ?>">
                <?php bloginfo('name'); ?>
            </a>
            */
                    ?>
                </div>
                <div class="collapse navbar-collapse" id="navbar-header">
                <?php
                $a = wp_nav_menu(array(
                        'menu'              => 'primary',
                        'theme_location'    => 'strider_navigation_menu_primary',
                        'depth'             => 2,
                        'container'         => '',
                        'container_class'   => '',
                        'menu_class'        => 'nav navbar-nav',
                        'menu_id'           => 'strider-primary-navigation',
                        'fallback_cb'       => 'Dreamery\WP\NavMenuWalker::fallback',
                        'walker'            => new Dreamery\WP\NavMenuWalker()
                    )
                );
                ?>
                </div>
            </div>
        </div>
    </nav>
</div>