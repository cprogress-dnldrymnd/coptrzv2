<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Page Enterprise
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header(); ?>
<style>
  
    :root {
        --accent-1: #0E1729;
        --accent-2: #0E1B35;
        --accent-3: #071020;
        --accent-4: #2DA1FF;
        --accent-5: #175180;
    }

    /**helpers */

    .bg-accent-1 {
        background-color: var(--accent-1);
    }

    .bg-accent-2 {
        background-color: var(--accent-2);
    }

    .bg-accent-3 {
        background-color: var(--accent-3);
    }

    .py-20 {
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .no-overlay:before {
        display: none;
    }

    .heading-style {
        display: inline-block;
    }
    .heading-style:before {
        content: '';
        display: block;
        width: 80%;
        height: 4px;
        margin-bottom: 20px;
        background: transparent linear-gradient(270deg, var(--accent-4) 0%, var(--accent-5) 100%) 0% 0% no-repeat padding-box;
    }

    @media(min-width: 992px) {
        .heading-style {
            font-size: 38px;
        }
    }

    /**end helpers */

    /**usp bar */

    .usp-bar__icon--star {
        color: #25C560;
    }

    .usp-bar__icon--dot {
        color: var(--accent-4);
    }

    /**end usp bar */
</style>
<?php
get_template_part('template-parts/sections/section-hero');
get_template_part('template-parts/sections/section-usp');
get_template_part('template-parts/sections/section-two-columns');
?>
<?php get_footer(); ?>