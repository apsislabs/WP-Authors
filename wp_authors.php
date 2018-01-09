<?php

/**
 * Plugin Name:     Authors
 * Description:     Add authors to your Wordpress Site
 * Author:          Apsis Labs
 * Author URI:      http://apsis.io
 * Text Domain:     apsis_wp
 * Version:         20171010
 *
 * @package         wp_authors
 */

namespace Apsis;

define('AUTHORS_PLUGIN_DIR', plugin_dir_path(__FILE__));

class AuthorsPlugin
{
    public static function init()
    {
        require_once('lib/constants.php');
        require_once('lib/utils.php');
        require_once('lib/edit_posts.php');

        EditPosts::init();

        // add_action('add_meta_boxes', array(static::class, "addMetaBoxes"));
        add_action('init', array(static::class, 'registerAuthorPostType'));
        // add_action('save_post', array(static::class, 'saveAuthorMetabox'));

        // add_action('admin_footer', array(static::class, 'addAuthorJavascript'));
        // add_action('wp_ajax_handleAddAuthorAjax', array(static::class, 'handleAddAuthorAjax'));
    }

    public static function registerAuthorPostType()
    {
        register_post_type(AUTHOR_POST_TYPE, array(
            "label" => __("Author", "apsis_wp"),
            "public" => true,
            "menu_icon" => "dashicons-groups",
            // "register_meta_box_cb" => array(static::class, 'addAuthorInfoMetabox'),
            "supports" => array("title")
        ));
    }

    public static function addAuthorInfoMetabox()
    {
        add_meta_box(
            'apsis_author_info_metabox',
            __('Author Info', 'apsis_wp'),
            array(static::class, 'renderAuthorPostTypeMetabox'),
            static::AUTHOR_POST_TYPE
        );
    }

    public static function renderAuthorPostTypeMetabox($post)
    {
        $meta_key = 'second_featured_img';
        ?>
        <div class="wp-author-info">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="first_name"><?= __('First Name', 'apsis_wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="first_name" name="first_name" class="regular-text">
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label for="last_name"><?= __('Last Name', 'apsis_wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="last_name" name="last_name" class="regular-text">
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label><?= __('Avatar', 'apsis_wp'); ?></label>
                        </th>
                        <td>
                            <?= misha_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label><?= __('Bio', 'apsis_wp'); ?></label>
                        </th>
                        <td>
                            <?= wp_editor("", "wp_author_bio") ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <script>
                jQuery(function($){
                    /*
                     * Select/Upload image(s) event
                     */
                    $('body').on('click', '.misha_upload_image_button', function(e){
                        e.preventDefault();

                            var button = $(this),
                                custom_uploader = wp.media({
                            title: 'Insert image',
                            library : {
                                // uncomment the next line if you want to attach image to the current post
                                // uploadedTo : wp.media.view.settings.post.id,
                                type : 'image'
                            },
                            button: {
                                text: 'Use this image' // button label text
                            },
                            multiple: false // for multiple image selection set to true
                        }).on('select', function() { // it also has "open" and "close" events
                            var attachment = custom_uploader.state().get('selection').first().toJSON();
                            $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
                            /* if you sen multiple to true, here is some code for getting the image IDs
                            var attachments = frame.state().get('selection'),
                                attachment_ids = new Array(),
                                i = 0;
                            attachments.each(function(attachment) {
                                attachment_ids[i] = attachment['id'];
                                console.log( attachment );
                                i++;
                            });
                            */
                        })
                        .open();
                    });

                    /*
                     * Remove image event
                     */
                    $('body').on('click', '.misha_remove_image_button', function(){
                        $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
                        return false;
                    });

                });
            </script>
        </div>
        <?php
    }

    public static function addAuthorJavascript() {
        ?>
        <script type="text/javascript" >
            (function($) {
                $('#wp_authors').chosen({ width: "100%" });

                $('#add_author').on('click', function() {
                    var formData = $('#new_author :input').serializeArray();
                    formData.push({name: "action", value: "handleAddAuthorAjax"});

                    $.post(ajaxurl, formData)
                        .done(function(res) {
                            // Append Value
                            $('#wp_authors').append($("<option>", {
                                text: res.post_title,
                                value: res.ID
                            })).val(res.ID).trigger("chosen:updated");

                            // Reset Inputs
                            $('#new_author :input').val("");
                        })
                        .fail(function(res) {
                            alert('There was a problem creating the author. Refresh the page and try again.');
                            console.error(res);
                        });
                });
            })(jQuery);
        </script>
        <?php
    }
}

function misha_image_uploader_field( $name, $value = '') {
    $image = ' button">Upload image';
    $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
    $display = 'none'; // display state ot the "Remove image" button

    if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

        // $image_attributes[0] - image URL
        // $image_attributes[1] - image width
        // $image_attributes[2] - image height

        $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
        $display = 'inline-block';

    }

    return '
    <div>
        <a href="#" class="misha_upload_image_button' . $image . '</a>
        <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
        <a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
    </div>';
}

AuthorsPlugin::init();
