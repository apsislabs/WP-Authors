<?php

namespace Apsis;

class Authors
{
    public static function init()
    {
        add_action('init', array(static::class, 'registerAuthorPostType'));
        add_action('admin_enqueue_scripts', array(static::class, "enqueueAdmin"));
        add_action('save_post', array(static::class, "saveAuthorInfoMetabox"));
    }

    public static function registerAuthorPostType()
    {
        register_post_type(AUTHOR_POST_TYPE, array(
            "label" => __("Author", "apsis_wp"),
            "public" => true,
            "menu_icon" => "dashicons-groups",
            "register_meta_box_cb" => array(static::class, 'addAuthorInfoMetabox'),
            "supports" => array('title')
        ));
    }

    public static function addAuthorInfoMetabox()
    {
        add_meta_box(
            'apsis_author_info_metabox',
            __('Author Info', 'apsis_wp'),
            array(static::class, 'renderAuthorInfoMetabox'),
            AUTHOR_POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function renderAuthorInfoMetabox($post)
    {
        require_once(AUTHORS_PLUGIN_DIR . '/templates/author_meta_box.php');
    }

    public static function saveAuthorInfoMetabox($post_id)
    {
        if (get_post_type($post_id) !== AUTHOR_POST_TYPE) {
            return $post_id;
        }

        if (!Utils::isValidSave($post_id, SECURITY_NONCE_KEY, SECURITY_NONCE_VALUE)) {
            return $post_id;
        }

        // Sanitize & Save
        Utils::safeSaveMeta($post_id, AUTHOR_FIRST_NAME_META_ID);
        Utils::safeSaveMeta($post_id, AUTHOR_LAST_NAME_META_ID);
        Utils::safeSaveMeta($post_id, AUTHOR_AVATAR_META_ID);

        Utils::safeSaveRichTextMeta($post_id, AUTHOR_BIO_META_ID);
    }

    public static function enqueueAdmin($hook)
    {
        global $post;
        if ( !static::shouldEnqueueScripts($hook, $post) ) { return; }

        // Vendor Scripts
        // ------------------------------------------------
        wp_register_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-validate');

        // Admin Scripts
        // ------------------------------------------------
        wp_register_script('wp_authors_admin', AUTHORS_PLUGIN_URL . '/assets/author_post_admin.js', array('jquery', 'jquery-validate'), false, true);
        wp_enqueue_script('wp_authors_admin');
    }

    private static function shouldEnqueueScripts($hook, $post)
    {
        $is_edit_page = ('post.php' === $hook || 'post-new.php' === $hook);
        $is_author_post_type = $post->post_type === AUTHOR_POST_TYPE;
        return ($is_edit_page && $is_author_post_type);
    }
}
