<?php
echo ___hero_modules();
echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));

echo do_shortcode(get_post_meta(get_the_ID(), '_sections_after_main_html', true));