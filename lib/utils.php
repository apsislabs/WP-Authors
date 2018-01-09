<?php

namespace Apsis;

class Utils
{
    public static function isValidSave($post_id, $nonce_key, $nonce_value)
    {
        if (
            !isset($_POST[$nonce_key]) ||
            !wp_verify_nonce( $_POST[$nonce_key], $nonce_value ) ||
            !current_user_can( 'edit_post', $post_id ) ||
            (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
        ) {
            return false;
        }

        return true;
    }

    public static function getPostAuthor($post_id)
    {
        return get_post_meta($post_id, AUTHOR_META_ID, true);
    }
}
