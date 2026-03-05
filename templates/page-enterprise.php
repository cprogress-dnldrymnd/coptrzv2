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
        --light-1: #EFEFEF;
        --light-2: #D9D9D9;
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

    .my-6 {
        margin-top: 3rem;
        margin-bottom: 3rem;
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

        .my-6 {
            margin-top: 5rem;
            margin-bottom: 5rem;
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
</style>
<?php
get_template_part('template-parts/sections/section-hero');
get_template_part('template-parts/sections/section-usp');
get_template_part('template-parts/sections/section-two-columns');
get_template_part('template-parts/sections/section-number-box');
?>
<?php get_footer(); ?>