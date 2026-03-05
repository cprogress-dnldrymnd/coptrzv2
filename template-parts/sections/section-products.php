<?php
$post_ids = [372804,372813,372840,372842,373370];
?>

<section class="products-section my-padding" id="Related-Products">
    <h2 class="heading-style">Tools That Enable These Outcomes</h2>
    <div class="container extend-right">
        <div class="swiper-holder">z
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
</section>