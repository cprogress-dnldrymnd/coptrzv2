<?php

$DisplayData = new DisplayData;

$Helpers = new Helpers;

$GetData = new GetData;


if ($post_is_global) {
    $featured_posts = get__theme_option('featured_posts');
}
else {
    $featured_posts = $module['featured_posts'];
}

?>

<?php if ($featured_posts) { ?>

    <?php
    if ($post_is_global) {
        echo '<section class="scrolling-section background-secondary has-edit">';

        if (current_user_can('administrator')) {
            echo $Helpers->get_edit_url('/edit.php?page=crb_carbon_fields_container_settings1.php', 'Edit Global Featured Posts');
        }
    }
    ?>
    <div class="container-fluid gx-0">

        <div class="row gx-0">

            <div class="col-lg-6 col-left">

                <div class="col-inner background-secondary">

                    <div class="column-holder ms-auto content-margin">

                        <div class="line-title-bottom fw-medium d-flex flex-column white-color">

                            <span class="text">

                                FEATURED POSTS

                            </span>

                            <span class="line"></span>

                        </div>



                        <div class="featured-posts-box">

                            <ul class="list-unstyled">



                                <?php foreach ($featured_posts as $key => $featured_post) { ?>

                                    <li class="<?= $key == 0 ? 'active' : '' ?> transition-300 featured-post"
                                        id="featured-post-<?= $key ?>">

                                        <a href="<?= get_permalink($featured_post) ?>"
                                            class="content-margin d-flex d-lg-block align-items-center">

                                            <div class="image-box d-block d-lg-none">

                                                <img decoding="async" class="jetpack-lazy-image" src="<?= get_the_post_thumbnail_url($featured_post, 'large') ?>" alt="<?= get_the_title($featured_post) ?>">

                                            </div>

                                            <div class="content-box ms-4 ms-lg-0">

                                                <?php

                                                $DisplayData->heading(

                                                    array(

                                                        'heading' => get_the_title($featured_post),

                                                        'tag'     => 'h3'

                                                    )

                                                );

                                                $DisplayData->description(

                                                    array(

                                                        'description' => get_the_excerpt($featured_post)

                                                    ),

                                                );

                                                ?>

                                            </div>

                                        </a>

                                    </li>



                                <?php } ?>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-6 col-right d-none d-lg-block">

                <div class="column-holder h-100">

                    <div class="images-box h-100">

                        <?php foreach ($featured_posts as $key => $featured_post) { ?>

                            <div class="image-box featured-post-image" data-target="#featured-post-<?= $key ?>">

                                <img decoding="async" class="jetpack-lazy-image" src="<?= get_the_post_thumbnail_url($featured_post, 'large') ?>"
                                    alt="<?= get_the_title($featured_post) ?>">

                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <?php
    if ($post_is_global) {
        echo '</section>';
    }
?>
<?php } ?>