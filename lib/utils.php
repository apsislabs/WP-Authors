<?php

namespace Apsis;

class Utils
{
    public static function isValidSave($post_id, $nonce_key, $nonce_value)
    {
        if (
            !isset($_POST[$nonce_key]) ||
            !wp_verify_nonce($_POST[$nonce_key], $nonce_value) ||
            !current_user_can('edit_post', $post_id) ||
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        ) {
            return false;
        }

        return true;
    }

    public static function safeSaveMeta($post_id, $meta_key)
    {
        if (array_key_exists($meta_key, $_POST)) {
            update_post_meta($post_id, $meta_key, sanitize_text_field(htmlentities($_POST[$meta_key])));
        }
    }

    public static function safeSaveRichTextMeta($post_id, $meta_key)
    {
        if (array_key_exists($meta_key, $_POST)) {
            update_post_meta($post_id, $meta_key, wp_kses_post($_POST[$meta_key]));
        }
    }

    public static function getPostAuthor($post_id)
    {
        return get_post_meta($post_id, AUTHOR_META_ID, true);
    }
}
