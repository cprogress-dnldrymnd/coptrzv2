<?
$post_ids = [291423, 272246, 272244, 272242];
?>

<section class="guides bg-accent-3 py-6 text-white">
    <div class="container">
        <div class="container-wrapper">
            <h2 class="heading-style mb-5">UK Enterprise Examples</h2>
            <div class="case-study-wrapper same-image-height">
                <div class="swiper swiper-case-study">
                    <div class="swiper-wrapper">
                        <?php foreach ($post_ids as $post) { ?>
                            <?php
                            ?>
                            <div class="swiper-slide">
                                <div class="swiper-slide-inner bg-accent-7 h-100">
                                    <div class="image-box">
                                        <?= get_the_post_thumbnail($post, 'large') ?>
                                    </div>
                                    <div class="content-box">
                                        <h4><?= get_the_title($post) ?></h4>
                                        <a href="<?= get_the_permalink($post) ?>">
                                            View Case Study
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var swiper_case_study = new Swiper('.swiper-case-study', {
        loop: true,
        autoplay: false,
        spaceBetween: 25,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 3
            },
        },
        pagination: {
            el: ".swiper-pagination",
        },
    });
</script>