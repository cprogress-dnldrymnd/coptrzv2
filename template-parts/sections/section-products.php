<?php
$post_ids = [372804, 372813, 372840, 372842, 373370];
?>

<section class="products-section my-6" id="Related-Products">
    <div class="container ">
        <div class="container-wrapper">
            <h2 class="heading-style">Tools That Enable These Outcomes</h2>
            <div class="desc-box mb-5">
                <p>DJI Enterprise products support different outcomes depending on how and where they are deployed. Selecting the right aircraft, payload and software is not a catalogue exercise. It is an operational decision that affects uptime, data quality and long-term resilience.</p>
            </div>
            <h3 class="text-accent mb-4">Featured DJI Enterprise Products</h3>
            <div class="swiper-holder">
                <div class="swiper swiper-linked-products" id="swiper---products">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">

                            <?php foreach ($post_ids as $post_id) {
                                echo _product_grid_display($post_id);
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>