<!DOCTYPE html>
<html <?php language_attributes(); ?> class="html">

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="">
    <meta name="format-detection" content="telephone=no">
    <title>
        <?php bloginfo('name'); // show the blog name, from settings 
        ?> |
        <?php is_front_page() ? bloginfo('description') : wp_title(''); // if we're on the home page, show the description, from the site's settings - otherwise, show the title of the post or page 
        ?>
    </title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <link rel="stylesheet" id="table-addons-for-elementor-css"
        href="https://old.coptrz.com/wp-content/plugins/table-addons-for-elementor/public/css/table-addons-for-elementor-public.css?ver=1.4.0"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-icons-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min.css?ver=5.31.0"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-frontend-css"
        href="https://old.coptrz.com/wp-content/uploads/elementor/css/custom-frontend.min.css?ver=1733219349"
        type="text/css" media="all">
    <link rel="stylesheet" id="swiper-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5"
        type="text/css" media="all">
    <link rel="stylesheet" id="e-swiper-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-pro-css"
        href="https://old.coptrz.com/wp-content/uploads/elementor/css/custom-pro-frontend.min.css?ver=1733219349"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-spacer-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/widget-spacer.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-heading-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-text-editor-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/widget-text-editor.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-image-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-image-carousel-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/css/widget-image-carousel.min.css?ver=3.24.4"
        type="text/css" media="all">
    <link rel="stylesheet" id="widget-icon-list-css"
        href="https://old.coptrz.com/wp-content/uploads/elementor/css/custom-widget-icon-list.min.css?ver=1733219349"
        type="text/css" media="all">

    <link rel="stylesheet" id="coptz-style-css" href="https://old.coptrz.com/wp-content/themes/coptrz/style.css?ver=5.5"
        type="text/css" media="all">
    <link rel="stylesheet" id="intl-tel-css"
        href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/css/intlTelInput.css?ver=5.5" type="text/css"
        media="all">
    <link rel="stylesheet" id="google-fonts-1-css"
        href="https://fonts.googleapis.com/css?family=Poppins%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRubik%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;ver=6.7.1"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-icons-shared-0-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/fontawesome.min.css?ver=5.15.3"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-icons-fa-solid-css"
        href="https://old.coptrz.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/solid.min.css?ver=5.15.3"
        type="text/css" media="all">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.7/build/js/intlTelInput.min.js?ver=5.5"
        id="intl-tel-js"></script>
    <script type="text/javascript"
        src="https://old.coptrz.com/wp-content/themes/coptrz/assets/coptrz_vendors/swiper/swiper-bundle.min.js?ver=6.7.1"
        id="coptz-swiper-js"></script>
    <script type="text/javascript"
        src="https://old.coptrz.com/wp-content/themes/coptrz/assets/javascripts/main.js?ver=5.5" id="coptz-js"></script>
    <?php wp_head(); ?>
</head>


<body
    class="guides-template guides-template-elementor_canvas single single-guides postid-255051 wp-custom-logo theme-coptrz woocommerce-js has-annoucement e-wc-error-notice e-wc-message-notice e-wc-info-notice elementor-default elementor-template-canvas elementor-kit-60957 elementor-page elementor-page-255051 e--ua-blink e--ua-chrome e--ua-webkit"
    data-elementor-device-mode="desktop">
    <?php wp_body_open(); ?>
    <main class="mt-20px">