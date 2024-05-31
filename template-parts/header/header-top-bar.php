<div class="top-bar background-black">
   
    <div class="container large-container">

        <div class="row align-items-center d-none d-lg-flex">
            <div class="col-lg-4 col-sm-6">
                <div class="column-holder top-bar-left">
                    <?php dynamic_sidebar('top_bar_left') ?>
                </div>
            </div>
            <div class="col-sm-4 text-center">
                <div class="column-holder top-bar-middle">
                    <?php dynamic_sidebar('top_bar_middle') ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 text-end">
                <div class="column-holder top-bar-right">
                    <?php dynamic_sidebar('top_bar_right') ?> </div>
            </div>
        </div>
        <div class="row d-flex d-lg-none g-0">
            <div class="col-6">
                <div class="column-holder top-bar-left-mobile top-bar-mobile">
                    <?php dynamic_sidebar('top_bar_left_mobile') ?>
                </div>
            </div>
            <div class="col-6 text-center">
                <div class="column-holder top-bar-right-mobile top-bar-mobile">
                    <?php dynamic_sidebar('top_bar_right_mobile') ?>
                </div>
            </div>
        </div>
    </div>
</div>