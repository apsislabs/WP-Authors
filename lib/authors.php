<?php

namespace Apsis;

class Authors
{
    private function __construct()
    {
    }

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
            return false;
        }

        if (!Utils::isValidSave($post_id, SECURITY_NONCE_KEY, SECURITY_NONCE_VALUE)) {
            return false;
        }

        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            return false;
        }

        // Sanitize & Save
        if (array_key_exists('first_name', $_POST)) {
            update_post_meta($post_id, AUTHOR_FIRST_NAME_META_ID, sanitize_text_field($_POST['first_name']));
        }

        if (array_key_exists('last_name', $_POST)) {
            update_post_meta($post_id, AUTHOR_LAST_NAME_META_ID, sanitize_text_field($_POST['last_name']));
        }

        if (array_key_exists('avatar_id', $_POST)) {
            update_post_meta($post_id, AUTHOR_AVATAR_META_ID, sanitize_text_field($_POST['avatar_id']));
        }

        if (array_key_exists('bio', $_POST)) {
            update_post_meta($post_id, AUTHOR_BIO_META_ID, sanitize_text_field(htmlentities($_POST['bio'])));
        }
    }

    public static function enqueueAdmin($hook)
    {
        global $post;

        // Lock down to Edit Posts screen
        $is_edit_page = ('post.php' === $hook || 'post-new.php' === $hook);
        $is_author_post_type = $post->post_type === AUTHOR_POST_TYPE;

        if (!$is_edit_page || !$is_author_post_type) {
            return;
        }

        // Vendor Scripts
        // ------------------------------------------------
        wp_register_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-validate');

        // Admin Scripts
        // ------------------------------------------------
        wp_register_script('wp_authors_admin', AUTHORS_PLUGIN_URL . '/assets/author_post_admin.js', array('jquery', 'jquery-validate'), false, true);
        wp_enqueue_script('wp_authors_admin');
    }
}
