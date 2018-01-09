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
define('AUTHORS_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!class_exists("AuthorsPlugin")) {
    class AuthorsPlugin
    {
        public static function init()
        {
            require_once('lib/constants.php');
            require_once('lib/utils.php');
            require_once('lib/edit_posts.php');
            require_once('lib/authors.php');

            EditPosts::init();
            Authors::init();
        }
    }
}

AuthorsPlugin::init();
