<?
$post_ids = [272246, 272244, 272242];
?>

<section class="case-studies bg-accent-3 py-6 text-white">
    <div class="container">
        <div class="container-wrapper">
            <h2 class="heading-style mb-5">UK Enterprise Examples</h2>
            <div class="case-study-wrapper same-image-height">
                <div class="swiper swiper--style-v2 swiper--js-v2">
                    <div class="swiper-wrapper">
                        <?php foreach ($post_ids as $post) { ?>
                            <?php
                            ?>
                            <div class="swiper-slide">
                                <div class="swiper-slide-inner bg-accent-7 h-100">
                                    <div class="image-box">
                                        <?= get_the_post_thumbnail($post, 'large') ?>
                                    </div>
                                    <div class="content-box text-center p-4">
                                        <div class="category">Surveying & Construction</div>
                                        <h4 class="medium-text fw-medium mb-0"><?= get_the_title($post) ?></h4>
                                        <div class="subtext-1">Ashcroft Civil & Infrastructure Ltd</div>
                                        <div class="subtext-2 mb-2">42% reduction in manual site inspection visits 30% faster progress reporting</div>
                                        <a class="fw-medium" href="<?= get_the_permalink($post) ?>">
                                            <span>View Case Study</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="11" viewBox="0 0 20 11">
                                                <g id="arrow" opacity="0.5">
                                                    <g id="Dribbble-Light-Preview" transform="translate(-300 -6643)">
                                                        <g id="icons" transform="translate(56 160)">
                                                            <path id="arrow_right-_346_" data-name="arrow_right-[#346]" d="M264,6488.27l-5.657-5.27-1.414,1.22,3.243,3.01H244v1.95h16.172l-3.243,3.35,1.414,1.47Z" fill="currentColor" fill-rule="evenodd"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="button-accent col-auto button-box text-center mt-5"><a class="rounded-10px " href="/case-studies/" target="_self">View All Case Studies</a></div>
        </div>
    </div>
</section>

<script>
    var swiper_case_study = new Swiper('.swiper--js-v2', {
        loop: true,
        autoplay: false,
        spaceBetween: 25,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },

            768: {
                slidesPerView: 2
            },

            992: {
                slidesPerView: 3
            },
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });
</script>