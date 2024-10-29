<?php
get_header('landing');
echo do_shortcode(get_post_meta(get_the_ID(), '_sections_html', true));
get_footer('landing');