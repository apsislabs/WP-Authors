<?php

namespace Apsis;

class EditPosts
{
    private function __construct()
    {
    }

    public static function init()
    {
        add_action('add_meta_boxes', array(static::class, "addPostMetaBoxes"));
        add_action('save_post', array(static::class, "savePostMetaBoxes"));
        add_action('admin_enqueue_scripts', array(static::class, "enqueueAdmin"));
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
        if (!Utils::isValidSave($post_id, SECURITY_NONCE_KEY, SECURITY_NONCE_VALUE)) {
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

    public static function enqueueAdmin($hook)
    {
        global $post;

        // Lock down to Edit Posts screen
        if (('post.php' !== $hook && 'post-new.php' !== $hook) || $post->post_type !== 'post') {
            return;
        }

        // Vendor Scripts
        // ------------------------------------------------
        if (!wp_script_is('wp-chosen', 'registered')) {
            wp_register_script('wp-chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js', array( 'jquery' ), false, true);
        }

        if (!wp_style_is('wp-chosen', 'registered')) {
            wp_register_style('wp-chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css');
        }

        wp_enqueue_script('wp-chosen');
        wp_enqueue_style('wp-chosen');

        // Admin Scripts
        // ------------------------------------------------
        wp_register_script('wp_authors_admin', AUTHORS_PLUGIN_URL . '/assets/edit_post_admin.js', array('jquery', 'wp-chosen'), false, true);
        wp_enqueue_script('wp_authors_admin');

        // Localize Script
        wp_localize_script('wp_authors_admin', 'objectL10n', array(
            'ajaxError'  => __('There was a problem creating the author. Refresh the page and try again.', 'apsis_wp')
        ));
    }

    public static function handleAddAuthorAjax()
    {
        check_ajax_referer(AJAX_NONCE_VALUE, AJAX_NONCE_KEY);
        $first_name = trim(sanitize_text_field($_POST['first_name']));
        $last_name = trim(sanitize_text_field($_POST['last_name']));
        $full_name = trim("{$first_name} {$last_name}");

        $post = wp_insert_post(array(
            'post_type' => AUTHOR_POST_TYPE,
            'post_title' => $full_name,
            'post_status' => 'publish',
            'meta_input' => array(
                AUTHOR_FIRST_NAME_META_ID => $first_name,
                AUTHOR_LAST_NAME_META_ID => $last_name
            )
        ));

        if (is_wp_error($post)) {
            wp_die($post, 400);
        }

        wp_send_json(get_post($post));
        wp_die();
    }
}
