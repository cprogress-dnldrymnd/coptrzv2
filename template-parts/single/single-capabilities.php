<?php


echo ___hero_modules();
the_content();


echo do_shortcode(___sections('sections_after_main', get_the_ID()));