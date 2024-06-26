<?php
get_template_part('template-parts/single/single-'.get_post_type().'/service', 'contents');
$Modules = new Modules();
$Modules->modules_section(get_the_ID());
