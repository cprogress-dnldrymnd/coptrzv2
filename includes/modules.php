<?php
function action_module_content()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated') && is_single()) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Update post 37
        $my_post = array(
            'ID'           => get_the_ID(),
            'post_content' => 'This is the updated content.',
        );

        // Update the post into the database
        wp_update_post($my_post);
    }
}
add_action('shutdown', 'action_module_content');
