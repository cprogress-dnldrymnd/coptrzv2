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
        --accent-6: #132446;
        --accent-7: #0F192B;
        --light-1: #EFEFEF;
        --light-2: #D9D9D9;
        --light-3: #FAFAFA;
    }

    /**helpers */
    .text-accent-2 {
        color: #0E1B35;
    }

    .bg-accent-1 {
        background-color: var(--accent-1);
    }

    .bg-accent-2 {
        background-color: var(--accent-2);
    }

    .bg-accent-3 {
        background-color: var(--accent-3);
    }

    .bg-accent-4 {
        background-color: var(--accent-4);
    }

    .bg-accent-5 {
        background-color: var(--accent-5);
    }

    .bg-accent-6 {
        background-color: var(--accent-6);
    }

    .bg-accent-7 {
        background-color: var(--accent-7);
    }

    .bg-light-3 {
        background-color: var(--light-3);
    }

    .py-20 {
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .my-6 {
        margin-top: 4rem;
        margin-bottom: 4rem;
    }

    .py-6 {
        padding-top: 4rem;
        padding-bottom: 4rem;
    }

    .no-overlay:before {
        display: none;
    }

    .fs-24 {
        font-size: 24px;
    }

    .fs-22 {
        font-size: 22px;
    }

    .image-animation-zoom .image-box {
        position: relative;
        overflow: hidden;
    }

    .image-animation-zoom img {
        transition: 400ms;
    }

    .image-animation-zoom:hover .image-box img {
        transform: scale(1.1);
    }

    h2 {
        display: inline-block;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .heading-style:before {
        content: '';
        display: block;
        width: 80%;
        max-width: 400px;
        height: 4px;
        margin-bottom: 20px;
        background: transparent linear-gradient(270deg, var(--accent-4) 0%, var(--accent-5) 100%) 0% 0% no-repeat padding-box;
    }

    @media(min-width: 992px) {
        h2 {
            font-size: 38px;
        }

        .my-6 {
            margin-top: 5rem;
            margin-bottom: 5rem;
        }

        .py-6 {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }

        .w-lg-auto {
            width: auto !important;
        }
    }

    @media(min-width: 768px) {
        .w-md-auto {
            width: auto !important;
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

    /**number-box-section */
    .number-box {
        background: var(--light-1);
        border-radius: 20px;
        font-size: 28px;
        padding: 22px clamp(20px, 2vw, 40px);
        font-weight: 600;
    }



    .number-box .number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: clamp(30px, 4vw, 64px);
        height: clamp(30px, 4vw, 64px);
        flex: clamp(30px, 4vw, 64px);
        border-radius: 5px;
        font-size: 22px;
        flex: 0 0 64px;

        background-color: var(--light-2);
    }

    @media(max-width: 991px) {
        .number-box {
            padding: 22px 30px;
            font-size: 24px;
        }

        .number-box .number {
            font-size: 20px;
        }

        .number-box .number {
            width: 50px;
            height: 50px;
            flex: 0 0 50px;
        }
    }

    @media(min-width: 992px) {
        .number-box-section .number-box-wrapper {
            position: relative;
        }

        .number-box-section .number-box-wrapper>div {
            position: relative;
        }

        .number-box-section .number-box-wrapper:before {
            content: '';
            height: 1px;
            background-color: var(--accent-2);
            width: 95%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    }

    /**end number-box-section */

    /**checklist */
    .checklist .inner svg {
        width: 23px;
        height: 23px;
        flex: 0 0 23px;
    }

    /**end checklist */

    /**guides */

    .guides-wrapper .guide-item {
        color: inherit;
        text-decoration: none;

    }

    .guides-wrapper .guide-item .guide-name,
    .guides-wrapper .guide-item .guide-link {
        opacity: 0.5;

    }

    .guides-wrapper .guide-item:hover {
        text-decoration: underline;
    }

    .guides-wrapper .guide-item:hover .guide-name,
    .guides-wrapper .guide-item:hover .guide-link {
        opacity: 1;
    }

    .guides-items>.guide-item-holder {
        margin-top: 30px;
    }

    .guides-items>.guide-item-holder:not(:last-child) {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding-bottom: 30px;
    }

    .guide-item-left .image-box {
        flex: 0 0 96px;
        width: 96px;
        border-radius: 10px;
        overflow: hidden;
    }

    .guide-item-left .image-box img {
        aspect-ratio: 1/1;
    }


    @media(max-width: 767px) {
        .guide-item-right {
            text-align: right;
            width: 100%;
        }
    }

    /**end guides */

    /**chip */
    .chip .inner {
        border: 1px solid #FF0E0E8F;
        color: #FF0E0E;
        padding: 24px;
        border-radius: 20px;
    }

    /**end chip */
</style>
<?php
get_template_part('template-parts/sections/section-hero');
get_template_part('template-parts/sections/section-usp');
get_template_part('template-parts/sections/section-two-columns');
get_template_part('template-parts/sections/section-number-box');
get_template_part('template-parts/sections/section-checklist');
get_template_part('template-parts/sections/section-guides');
get_template_part('template-parts/sections/section-chip');
get_template_part('template-parts/sections/section-industries');
get_template_part('template-parts/sections/section-cta');
get_template_part('template-parts/sections/section-case-study');
?>
<?php get_footer(); ?>