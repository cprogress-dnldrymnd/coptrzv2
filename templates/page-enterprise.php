<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Page Enterprise
/*-----------------------------------------------------------------------------------*/
?>
<style>
    /**
 * USP Bar Block
 * Defines the outer wrapper, background color, and base typography.
 */
    .usp-bar {
        background-color: #242936;
        /* Dark slate matching the image */
        color: #e2e8f0;
        padding: 12px 0;
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
        border-top: 2px solid #b829a3;
        /* Magenta accent line from image */
        border-bottom: 2px solid #b829a3;
    }

    /**
 * USP Container
 * Constrains maximum width and handles flexbox distribution.
 */
    .usp-bar__container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        flex-wrap: wrap;
        /* Allows wrapping on smaller viewports */
        justify-content: space-between;
        /* Evenly distributes items horizontally */
        align-items: center;
        gap: 24px;
    }

    /**
 * USP Item Element
 * Controls individual item alignment and establishes context for absolute positioning.
 */
    .usp-bar__item {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    /**
 * Blue Dot Separator Modifier
 * Automatically injects a blue dot before every item EXCEPT the first one.
 * Relies on the adjacent sibling combinator (+).
 */
    .usp-bar__item+.usp-bar__item::before {
        content: "•";
        color: #3b82f6;
        /* Bright blue */
        font-size: 18px;
        position: absolute;
        left: -16px;
        /* Positions the dot perfectly between the flex gap */
        line-height: 1;
    }

    /**
 * Star Icon Modifier
 * Specific color injection for the review star.
 */
    .usp-bar__icon--star {
        color: #10b981;
        /* Emerald green */
        font-size: 16px;
        line-height: 1;
    }

    /**
 * Responsive Media Query
 * Adjusts layout for tablet/mobile devices by switching to a stacked/grid approach if needed.
 */
    @media (max-width: 1024px) {
        .usp-bar__container {
            justify-content: center;
        }

        /* Hides the separators on stacked mobile layouts */
        .usp-bar__item+.usp-bar__item::before {
            display: none;
        }
    }
</style>
<?php get_header(); ?>
<section class="text-white hero pb-50px pt-50px rounded-10px bg-primary overflow-hidden d-flex align-items-center mx-20px position-relative" id="hero">
    <div class="background-image background-overlay"><img width="1920" height="768" src="https://coptrz.com/wp-content/uploads/2026/03/NoPath-Copy-18.jpg" /></div>
    <div class="container">
        <div class="hero-left-content position-relative overflow-hidden hero-bg-mobile small-width">
            <h1 class="large-heading mb-3">DJI Enterprise UK: Buy Mission-Ready</h1>
            <div class="description-box fw-light  mx-auto mb-4">
                <p>Reduce operational risk, deployment friction and future regret. Buy DJI Enterprise drones that are configured, supported and defensible from day one, backed by Coptrz.</p>
            </div>
            <div>
                <div class="button-group-box ">
                    <div class="row g-3 justify-content-center d-inline-flex">
                        <div class="button-accent col-auto button-box"><a class="rounded-10px " href="#Benefits" target="_self">Speak to Our Enterprise Team</a></div>
                        <div class="button-bordered col-auto button-box"><a class="rounded-10px " href="#Use-Cases" target="_self">See Solutions by Industry</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="usp-bar" aria-label="Company Trust Signals">
    <div class="usp-bar__container">
        <div class="usp-bar__item">
            <span class="usp-bar__icon usp-bar__icon--star">★</span>
            <span><strong>4.9 average rating</strong> from verified customers</span>
        </div>

        <div class="usp-bar__item">
            <span><strong>DJI Certified Service Centre</strong> for warranty-safe repairs</span>
        </div>

        <div class="usp-bar__item">
            <span><strong>UK stock, fulfilment,</strong> and predictable dispatch</span>
        </div>

        <div class="usp-bar__item">
            <span><strong>Trusted by enterprise</strong> and public sector organisations</span>
        </div>
    </div>
</section>
<?php get_footer(); ?>