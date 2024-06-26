<?php
$DisplayData = new DisplayData;
$featured_video_text = get__post_meta_by_id($product_id, 'featured_video_text');
$featured_video_description = get__post_meta_by_id($product_id, 'featured_video_description');
$featured_video_file = get__post_meta_by_id($product_id, 'featured_video_file');
$featured_video_type = get__post_meta_by_id($product_id, 'featured_video_type');
?>
<?php if ($featured_video_text || $featured_video_description || $featured_video_file) { ?>
    <section class="cta sm-padding pb-0">

        <div class="container">

            <div class="cta-inner position-relative background-black <?= $featured_video_type ?>">
                <?php if ($featured_video_file) { ?>
                    <div class="cta-background-box background-box overlay">

                        <video autoplay muted loop preload="metadata" playsinline>

                            <source src="<?= wp_get_attachment_url($featured_video_file) ?>">

                        </video>

                    </div>

                <?php } ?>

                <div class="row align-items-end gy-4 position-relative">

                    <div class="col-12">

                        <div class="column-holder content-holder medium-width">
                            <?php
                            if ($featured_video_text) {
                                $DisplayData->heading(

                                    array(

                                        'heading' => $featured_video_text,
                                        'tag'     => 'h3'

                                    ),
                                );
                            }

                            if ($featured_video_description) {
                                $DisplayData->description(
                                    array(

                                        'description' => $featured_video_description,
                                    ),
                                );
                            }
                            ?>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

<?php } ?>
