<?php

$DisplayData = new DisplayData;

$SVG = new SVG;

?>
<section class="product-slider blog-section xl-padding-bottom archive-section search-section no-overflow">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="column-holder position-sticky">
                    <div class="blog-sidebar">
                        <div class="blog-filter">
                            <div class="widget-inner">
                                <div class="search">
                                    <div class="search-inner">
                                        <div class="filter-header d-flex justify-content-between align-items-center">
                                            <label for="search"><strong>Search</strong></label>
                                            <button class="reset-all">
                                                Reset All
                                            </button>
                                        </div>
                                        <input type="text" id="search" name="s" placeholder="Search by name" value="<?= $_GET['s'] ?>">
                                        <div class="result-count">
                                            <p>Showing <span class="result-post">0</span> results of <span class="total-post">0</span></p>
                                        </div>
                                    </div>
                                </div>
                                <form class="archive-form-filter">
                                    <div class="filter-header d-flex justify-content-between align-items-center">
                                        <label for="search"><strong>Filter by</strong></label>
                                        <button class="clear-category">
                                            Clear
                                        </button>
                                    </div>

                                    <input type="hidden" name="post-type" value="<?= $post_type ?>">

                                    <input type="hidden" name="taxonomy" value="<?= $taxonomy ?>">

                                    <input type="hidden" name="is_search" value="1">

                                    <?php
                                    $post_types = array(
                                        'page'    => 'Page',
                                        'post'     => 'Blogs',
                                        'product' => 'Products',
                                        'events'  => 'Events',
                                        'webinars'  => 'Webinars',
                                    );
                                    ?>

                                    <div class="category-box flex-wrap d-flex">
                                        <?php foreach ($post_types as $key => $post_type) { ?>
                                            <div class="cat-filter-holder ">
                                                <input type="checkbox" value="<?= $key ?>" id="term-<?= $key ?>" name="post_types[]">
                                                <label class="inner" for="term-<?= $key ?>">
                                                    <?= $post_type ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-8">
                <div class="column-holder">
                    <div class="product-slider-box">

                        <div class="mySwiper-productSwiperPost">

                            <div id="results">

                                <div class="results-holder">

                                </div>

                                <div id="pagination">

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>



    </div>

</section>



<script>
    jQuery(document).ready(function($) {

        jQuery('.archive-form-filter').change();

    });
</script>