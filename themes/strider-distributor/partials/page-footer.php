<style>
    #striderslider-lifestyle { margin-top: 75px; }
    #striderslider-lifestyle img { height: 275px; width: auto; }
</style>
<div>
<?php
    echo do_shortcode('[striderslider ids="191,192,193,178,179,180,181,182,183,184,185,186,187,188,189,190" instance="lifestyle" showtitle="false" link="file" sladaptiveheight="true" slslidestoshow="8" slslidestoscroll="1" slvariablewidth="true" slarrows="false" slfade="false" sldots="false" slcentermode="false" slfocusonselect="true" slautoplay="true" slautoplayspeed="3500" slinfinite="true"]');
?>
</div>
<?php
    get_template_part('partials/strider-difference-container');
?>
<div id="trade-footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <?php
                $url = get_option('siteurl');
                $parsed_url = parse_url($url);
                ?>
                <h2><?php echo $parsed_url['host']; ?></h2>
            </div>
        </div>
    </div>
</div>
<?php
$social_links_enabled = get_theme_mod('strider_social_links_enabled', 'disabled');
?>
<div id="sub-footer">
    <div class="container">
        <?php if ($social_links_enabled == 'enabled') { ?>
        <div class="col-sm-4 col-md-5 copy">
        <?php } else { ?>
        <div class="col-sm-9 col-md-10 copy">
        <?php } ?>
            &copy; <?php echo date('Y'); ?> Strider Sports International, Inc.<br>All Rights Reserved.
            <ul>
                <li><a href="https://www.striderbikes.com/trademarks" target="_blank">Trademarks</a></li>
            </ul>
        </div>
        <?php if ($social_links_enabled == 'enabled') { ?>
        <div class="col-sm-5 col-md-5 text-center">
            <div class="row">
            <?php
            $social_link_facebook = get_theme_mod('strider_social_link_facebook', '');
            $social_link_instagram = get_theme_mod('strider_social_link_instagram', '');
            $social_link_twitter = get_theme_mod('strider_social_link_twitter', '');
            $social_link_pinterest = get_theme_mod('strider_social_link_pinterest', '');
            $social_link_tumblr = get_theme_mod('strider_social_link_tumblr', '');
            $social_link_flickr = get_theme_mod('strider_social_link_flickr', '');
            $social_link_youtube = get_theme_mod('strider_social_link_youtube', '');
            ?>
            <?php if (!empty($social_link_facebook)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_facebook; ?>" target="_blank" class="symbol facebook">&#xe427;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_instagram)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_instagram; ?>" target="_blank" class="symbol instagram">&#xe500;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_twitter)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_twitter; ?>" target="_blank" class="symbol twitter">&#xe487;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_pinterest)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_pinterest; ?>" target="_blank" class="symbol pinterest">&#xe464;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_tumblr)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_tumblr; ?>" target="_blank" class="symbol tumblr">&#xe485;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_flickr)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_flickr; ?>" target="_blank" class="symbol flickr">&#xe429;</a></div>
            <?php } ?>
            <?php if (!empty($social_link_youtube)) { ?>
                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2"><a href="<?php echo $social_link_youtube; ?>" target="_blank" class="symbol youtube">&#xe499;</a></div>
            <?php } ?>
            </div>
        </div>
        <?php } ?>
        <div class="col-sm-3 col-md-2">
            <img src="<?php echo get_template_directory_uri() . '/assets/img/strider-balance-bike-side-profile-left.png'; ?>" class="img-responsive">
        </div>
    </div>
</div>