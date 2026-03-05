<?php
$post_ids = [372804, 372813, 372840, 372842, 373370];
?>

<section class="products-section my-6" id="Related-Products">
    <div class="container ">
        <div class="container-wrapper">
            <h2 class="heading-style text-accent-2">Tools That Enable These Outcomes</h2>
            <div class="desc-box mb-5">
                <p>DJI Enterprise products support different outcomes depending on how and where they are deployed. Selecting the right aircraft, payload and software is not a catalogue exercise. It is an operational decision that affects uptime, data quality and long-term resilience.</p>
            </div>
            <h3 class="text-accent mb-4">Featured DJI Enterprise Products</h3>
            <div class="swiper-holder">
                <div class="swiper swiper-linked-products-v2 swiper--style-v2 swiper--style-v2-dark" id="swiper---products">
                    <div class="swiper-wrapper">

                        <?php foreach ($post_ids as $post_id) {
                            echo '<div class="swiper-slide">';
                            echo _product_grid_display($post_id);
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<script>
    var swiper_linked_products = new Swiper('.swiper-linked-products-v2', {
        loop: true,
        spaceBetween: 20,
        autoplay: false,
        breakpoints: {
            0: {
                slidesPerView: 2,
            },

            768: {
                slidesPerView: 3,
            },


            992: {
                slidesPerView: 4,
            },

         
        },
        pagination: {
            el: ".swiper-pagination",
        },
    });
</script>