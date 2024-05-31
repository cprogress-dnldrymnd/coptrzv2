<?php

$DisplayData = new DisplayData;

$SVG = new SVG;
$taxonomy = 'category';
if (is_home()) {
    $post_type = 'post';
    $taxonomy = 'category';
} else if (is_archive()) {
    if (is_post_type_archive('casestudies')) {
        $post_type = 'casestudies';
        $taxonomy = 'case_study_category';
    } else if (is_post_type_archive('guides')) {
        $post_type = 'guides';
        $taxonomy = 'Guide_category';
    } else if (is_post_type_archive('webinars')) {
        $post_type = 'webinars';
        $taxonomy = 'webinars_category';
    } else if (is_post_type_archive('events')) {
        $post_type = 'events';
        $taxonomy = 'events_category';
    }
} else {
    $term = get_queried_object();
}


?>



<section class="product-slider blog-section xl-padding-bottom archive-section no-overflow">

    <div class="container mb-7">

        <div class="row row-blog">
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

                                        <input type="text" id="search" name="s" placeholder="Search by name">
                                        <div class="result-count">
                                            <p>Showing <span class="result-post">0</span> results of <span class="total-post">0</span></p>
                                        </div>
                                    </div>
                                </div>
                                <form class="archive-form-filter">
                                    <div class="filter-header d-flex justify-content-between align-items-center">
                                        <label for="search"><strong>Category</strong></label>
                                        <button class="clear-category">
                                            Clear
                                        </button>
                                    </div>
                                    <input type="hidden" name="post-type" value="<?= $post_type ?>">

                                    <input type="hidden" name="taxonomy" value="<?= $taxonomy ?>">

                                    <input type="hidden" name="is_search" value="0">


                                    <?php if ($taxonomy) { ?>
                                        <div class="category-box flex-wrap d-flex">

                                            <?php

                                            $terms = get_terms(

                                                array(

                                                    'taxonomy'   => $taxonomy,

                                                    'hide_empty' => false,

                                                )

                                            );
                                            ?>
                                            <?php foreach ($terms as $term) { ?>
                                                <div class="cat-filter-holder ">
                                                    <input type="checkbox" value="<?= $term->term_id ?>" id="term-<?= $term->term_id ?>" name="terms[]">
                                                    <label class="inner" for="term-<?= $term->term_id ?>">
                                                        <?= $term->name ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <input type="hidden" value="<?= $term->term_id ?>" name="terms-category">
                                    <?php } ?>

                                </form>
                            </div>
                        </div>

                        <?php if (is_active_sidebar('blog_sidebar')) { ?>
                            <?php dynamic_sidebar('blog_sidebar') ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="column-holder">

                    <div class="blog-filter">
                        <div class="widget-inner">
                            <div class="filter-header filter-sort d-flex justify-content-between align-items-center mb-0">
                                <label for="search"><strong>Filtering by :</strong></label>
                                <div class="d-inline-flex">
                                    <button class="reset-all me-3">
                                        Reset All
                                    </button>
                                    <select name="sortby" id="sortpostby">
                                        <option value="">Sort by</option>
                                        <option value="name-a-z">Sort by name (a-z)</option>
                                        <option value="name-z-a">Sort by name (z-a)</option>
                                        <option value="date-desc">Sort by date (latest)</option>
                                        <option value="date-asc">Sort by date (oldest)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-slider-box">

                        <div class="mySwiper-productSwiperPost">

                            <div id="results" page="1">

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
</section>