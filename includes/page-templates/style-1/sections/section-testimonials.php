<?php $s=$GLOBALS['ep_section']; ?>
<section class="testimonial-v2 my-6">
    <div class="container"><div class="container-wrapper">
        <div class="row g-3 justify-content-between mb-5">
            <div class="col-lg-7">
                <?php if($h=pts1_field($s,'heading')): ?><h2 class="heading-style"><?=esc_html($h)?></h2><?php endif; ?>
            </div>
            <?php if(($bt=pts1_field($s,'btn_text'))&&($bu=pts1_field($s,'btn_url'))): ?>
                <div class="col-auto"><div class="button-accent col-auto button-box"><a class="rounded-10px" href="<?=esc_url($bu)?>"><?=esc_html($bt)?></a></div></div>
            <?php endif; ?>
        </div>
        <?php echo ____post_grid_module(['id'=>'testimonial-slider','is_slider'=>true,'number_of_slides'=>4,'number_of_slides_tablet'=>2,'number_of_slides_mobile'=>1,'post_grid_id'=>'testimonial-slider','post_elements'=>[['_type'=>'icon','icon'=>'417802','icon_color'=>'text-accent','icon_color_custom'=>'','icon_width'=>'','icon_height'=>''],['_type'=>'custom_field_1','custom_field_key'=>'_testimonial_content','custom_field_type'=>'p','custom_field_class'=>'testimonial-content'],['_type'=>'post_title','text_before'=>'-','text_after'=>'','tag'=>'p','text_color'=>'','text_color_custom'=>'']],'post_type'=>[['_type'=>'testimonials']]]); ?>
    </div></div>
</section>
