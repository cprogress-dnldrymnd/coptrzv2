<?php

$DisplayData = new DisplayData;
if (is_home()) {
    $title = 'Blog';
} else if (is_category()) {
    $term = get_queried_object();
    $title = $term->name;
} else if (is_archive()) {
    $title = get_the_archive_title();
} else if (is_search()) {

    $title = 'Search results for ' . $_GET['s'];
} else {

    $title = get_the_title();
}
if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
    $title = 'Order Received';
}

if (is_page() || is_single()) {
    $description = get__post_meta('description');
}
?>

<section class="breadcrumbs-title xxs-padding md-padding">

    <div class="container">

        <div class="breadcrumbs-holder">

            <ul class="breadcrumbs list-unstyled d-flex ">

                <li>

                    <a href="<?= get_site_url() ?>"> Home </a>

                </li>



                <?php if (is_single()) { ?>

                    <li>
                        <?php if (get_post_type() == 'post') { ?>

                            <a href="<?= get_permalink(get_option('page_for_posts')) ?>"> Blog </a>
                        <?php } else if (get_post_type() == 'guides') { ?>
                            <a href="<?= get_post_type_archive_link('guides') ?>"> Guides </a>

                        <?php } ?>
                    </li>

                <?php } ?>

                <li>

                    <span><?= $title ?></span>

                </li>

            </ul>

        </div>

        <?= do_shortcode('[_heading heading="' . $title . '" class="big-heading"]') ?>
        <?= do_shortcode('[_description description="' . _format_text($description) . '" ]') ?>
        <?php if (is_single()) { ?>

            <div class="meta-box d-flex mt-3">

                <span class="date">

                    <?= get_the_date('d/m/Y', get_the_ID()) ?>

                </span>

                <div class="bull">&bull;</div>

                <span class="author">

                    <?php

                    $author_id = get_post_field('post_author', get_the_ID());

                    $author_name = get_the_author_meta('display_name', $author_id);

                    ?>

                    <?= $author_name ?>

                </span>

            </div>

        <?php } ?>

    </div>

</section>