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


<?php
get_template_part('template-parts/sections/section-hero');
get_template_part('template-parts/sections/section-usp');

?>
<?php get_footer(); ?>