<?php
get_header('landing');
?>
<div class="modules">
    <?php
    echo ___hero_modules();
    echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
    ?>
</div>
<?php
get_footer('landing');
