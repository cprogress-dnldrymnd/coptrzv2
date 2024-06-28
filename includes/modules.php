<?php
function action_update_zip_url()
{
    // Check if a post was updated (add your specific conditions here)
    if (did_action('post_updated') && is_single()) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    }
}
add_action('shutdown', 'action_update_zip_url');
