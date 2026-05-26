<?php
get_header('landing-v2');
?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(___sections());
    echo do_shortcode(___sections('sections_after_main'));
    ?>
</div>
<?php
get_footer('landing-v2');
