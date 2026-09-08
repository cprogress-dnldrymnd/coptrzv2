<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: HTML Page
/* Template Post Type: page, guides, producttaxonomypages
/*-----------------------------------------------------------------------------------*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="html">

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="">
    <meta name="format-detection" content="telephone=no">
    <?php if (!is_404() && !is_search()) { ?>
        <link rel="canonical" href="<?= canonical() ?>" />
    <?php } ?>
    <title>
        <?php wp_title('') ?>
    </title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>

    <div id="main">
        <?php the_content() ?>
    </div>

    <?php wp_footer(); ?>
</body>

</html>