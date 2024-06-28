<?php
function action_module_content()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated')) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $post_content = '<!-- wp:html -->';
        
        $hero = ___hero();
   
        $post_content .= '<!-- /wp:html -->';

        $my_post = array(
            'ID'           => get_the_ID(),
            'post_content' => $post_content,
        );

        // Update the post into the database
        wp_update_post($my_post);
    }
}
add_action('shutdown', 'action_module_content');

function ___hero() {
    $heading = 
}