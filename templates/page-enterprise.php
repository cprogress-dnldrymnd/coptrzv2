<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Page Enterprise
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
        <section class="text-center text-white hero pb-50px pt-50px rounded-10px bg-primary overflow-hidden d-flex align-items-end mx-20px position-relative" id="hero">
            <div class="background-image background-overlay"><img width="1920" height="768" src="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg" class="attachment-full size-full lazyautosizes lazyloaded" alt="" decoding="async" fetchpriority="high" data-src="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg" data-srcset="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg 1920w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-500x200.jpg 500w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-600x240.jpg 600w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-300x120.jpg 300w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1024x410.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-768x307.jpg 768w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1536x614.jpg 1536w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-2048x819.jpg 2048w" data-sizes="auto" data-eio-rwidth="1920" data-eio-rheight="768" sizes="1960px" srcset="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg 1920w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-500x200.jpg 500w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-600x240.jpg 600w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-300x120.jpg 300w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1024x410.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-768x307.jpg 768w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1536x614.jpg 1536w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-2048x819.jpg 2048w"><noscript><img width="1920" height="768" src="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg" class="attachment-full size-full" alt="" decoding="async" fetchpriority="high" srcset="https://coptrz.com/wp-content/uploads/2024/07/photogrammetry.jpg 1920w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-500x200.jpg 500w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-600x240.jpg 600w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-300x120.jpg 300w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1024x410.jpg 1024w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-768x307.jpg 768w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-1536x614.jpg 1536w, https://coptrz.com/wp-content/uploads/2024/07/photogrammetry-2048x819.jpg 2048w" sizes="(max-width: 1920px) 100vw, 1920px" data-eio="l" /></noscript></div>
            <div class="container">
                <div class="hero-left-content position-relative overflow-hidden hero-bg-mobile">
                    <div class="breadcrumbs mb-3 medium-text fw-light">
                        <ul class="list-inline m-0 p-0 t">
                            <li><a class="item text-white" href="https://coptrz.com">Home</a></li>
                            <li><a class="item text-white" href="https://coptrz.com/capabilities/">Capabilities</a></li>
                            <li><span class="item text-white">Photogrammetry</span></li>
                        </ul>
                    </div>
                    <h1 class="large-heading mb-3">Photogrammetry</h1>
                    <div class="description-box fw-light medium-text small-width mx-auto mb-4">
                        <p>Photogrammetry is the process of capturing high-resolution imagery and stitching them together to create an accurate 3D model of a particular area in the real world. Sophisticated software is used to stitch the collated imagery to create a realistic, geo-referenced 3D model. </p>
                    </div>
                    <div>
                        <div class="button-group-box ">
                            <div class="row g-3 justify-content-center d-inline-flex">
                                <div class="button-bordered col-auto button-box"><a class="rounded-10px " href="#Benefits" target="_self">Benefits</a></div>
                                <div class="button-bordered col-auto button-box"><a class="rounded-10px " href="#Use-Cases" target="_self">Use Cases</a></div>
                                <div class="button-bordered col-auto button-box"><a class="rounded-10px " href="#Enquire" target="_self">Enquire</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php wp_footer(); ?>
</body>

</html>