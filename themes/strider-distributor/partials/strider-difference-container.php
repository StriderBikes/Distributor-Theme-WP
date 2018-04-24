<div style="background-color: #ffcd03; color: #000; border-top: solid 3px #000;">
    <div class="container">
        <h2 style="margin: 20px 20px;">The Strider<sup><small> &reg;</small></sup> Difference</h2>
    </div>
</div>
<div class="container widget-area">
<?php
    if (is_active_sidebar('strider_difference')) {
        dynamic_sidebar('strider_difference');
    } else {
        get_template_part('partials/strider-difference');
    }
?>
</div>
