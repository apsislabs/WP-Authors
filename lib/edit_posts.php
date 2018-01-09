<?php

namespace Apsis;

class EditPosts
{
    private function __construct() {}

    public static function init()
    {
        add_action('add_meta_boxes', array(static::class, "addPostMetaBoxes"));
        add_action('save_post', array(static::class, "savePostMetaBoxes"));
        add_action('admin_footer', array(static::class, "enqueueAdmin"));
        add_action('wp_ajax_handleAddAuthorAjax', array(static::class, "handleAddAuthorAjax"));
    }

    public static function addPostMetaBoxes()
    {
        add_meta_box(
            'apsis_author_metabox',
            __('Authors', 'apsis_wp'),
            array(static::class, 'renderPostMetaBoxes'),
            'post',
            'side',
            'core'
        );
    }

    public static function renderPostMetaBoxes($post)
    {
        $authors = get_posts(array('post_type' => AUTHOR_POST_TYPE, 'posts_per_page' => -1));
        require_once(AUTHORS_PLUGIN_DIR . '/templates/posts_meta_box.php');
    }

    public static function savePostMetaBoxes($post_id)
    {
        if ( !Utils::isValidSave($post_id, SECURITY_NONCE_KEY, SECURITY_NONCE_VALUE) )
        {
            return $post_id;
        }

        // Sanitize & Save
        if (array_key_exists('ap_authors', $_POST)) {
            update_post_meta(
                $post_id,
                AUTHOR_META_ID,
                sanitize_text_field($_POST['ap_authors'])
            );
        }
    }

    public static function enqueueAdmin()
    {

    }

    public static function handleAddAuthorAjax() {
        check_ajax_referer( AJAX_NONCE_VALUE, AJAX_NONCE_KEY );

        $full_name = join(' ', array($_POST['first_name'], $_POST['last_name']));

        $post = wp_insert_post(array(
            'post_type' => AUTHOR_POST_TYPE,
            'post_title' => $full_name,
            'post_status' => 'publish'
        ));

        if (is_wp_error($post)) {
            wp_die($post, 400);
        }

        wp_send_json(get_post($post));
        wp_die();
    }
}
